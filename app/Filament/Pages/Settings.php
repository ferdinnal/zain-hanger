<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class Settings extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Website';
    protected static string  $view            = 'filament.pages.settings';
    protected static ?int    $navigationSort  = 99;

    public static function getNavigationGroup(): string
    {
        return 'Pengaturan';
    }

    public array $data = [];

    public function mount(): void
    {
        $all = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($all);
    }

    // Helper: buat FileUpload standar dengan fix "Waiting for size"
    // - Paksa state null saat hydrate (hindari bug Windows symlink)
    // - Tampilkan preview gambar existing via helperText
    // - Setelah upload baru, sync ke key setting yang sesuai
    protected static function makeImageUpload(string $field, string $label, string $directory = 'settings'): FileUpload
    {
        return FileUpload::make($field)
            ->label($label . ' (kosongkan jika tidak ingin ganti)')
            ->image()
            ->disk('public')
            ->directory($directory)
            ->imageEditor()
            ->afterStateHydrated(function (FileUpload $component) {
                // Paksa kosong — hindari stuck "Waiting for size" di Windows
                $component->state(null);
            })
            ->helperText(function () use ($field, $directory) {
                $value = Setting::get($field);
                if (! $value) {
                    return 'Belum ada foto';
                }
                // Jika sudah URL penuh (http/https)
                if (str_starts_with($value, 'http')) {
                    $url = $value;
                } else {
                    // Path storage lokal
                    $url = asset('storage/' . ltrim($value, '/'));
                }
                return new HtmlString(
                    '<img src="' . e($url) . '" style="max-height:100px;margin-top:8px;border-radius:6px;border:1px solid #444">'
                );
            });
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Tabs::make('Settings')->tabs([

                Tabs\Tab::make('Identitas Toko')->schema([
                    TextInput::make('site_name')->label('Nama Website')->required(),
                    TextInput::make('site_name_short')->label('Nama Pendek (Logo Teks)'),
                    TextInput::make('site_name_sub')->label('Sub Nama (Logo)'),
                    static::makeImageUpload('site_logo', 'Logo'),
                    static::makeImageUpload('site_favicon', 'Favicon'),
                    Textarea::make('site_description')->label('Deskripsi Meta')->rows(2),
                    TextInput::make('site_copyright')->label('Copyright Footer'),
                ]),

                Tabs\Tab::make('Hero Section')->schema([
                    static::makeImageUpload('hero_image', 'Gambar Hero'),
                    TextInput::make('hero_subtitle')->label('Subjudul'),
                    Textarea::make('hero_title')->label('Judul (boleh HTML)')->rows(2),
                    Textarea::make('hero_description')->label('Deskripsi')->rows(2),
                    TextInput::make('hero_cta_primary')->label('Tombol Utama'),
                    TextInput::make('hero_cta_secondary')->label('Tombol Sekunder'),
                ]),

                Tabs\Tab::make('Tentang Kami')->schema([
                    TextInput::make('about_title')->label('Judul'),
                    TextInput::make('about_subtitle')->label('Subtitle'),
                    static::makeImageUpload('about_image', 'Foto About'),
                ]),

                Tabs\Tab::make('Kontak & Sosmed')->schema([
                    TextInput::make('contact_whatsapp')
                        ->label('Nomor WhatsApp Admin')
                        ->helperText('Format: 628xxx tanpa + dan spasi')
                        ->required(),
                    TextInput::make('contact_email')->label('Email'),
                    Textarea::make('contact_address')->label('Alamat Lengkap')->rows(2),
                    TextInput::make('social_instagram')->label('Instagram URL'),
                    TextInput::make('social_facebook')->label('Facebook URL'),
                    TextInput::make('social_tiktok')->label('TikTok URL'),
                    TextInput::make('social_shopee')->label('Shopee URL'),
                ]),

                Tabs\Tab::make('Template WA')->schema([
                    Textarea::make('wa_order_template')
                        ->label('Template Pesan WhatsApp')
                        ->rows(8)
                        ->helperText('Variabel: {site_name} {customer_name} {product_name} {kepala} {jenis} {qty} {price_per_unit} {total} {order_code}'),
                ]),

            ])->columnSpanFull(),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Jika FileUpload menghasilkan array (nama file baru), ambil filename-nya
            if (is_array($value)) {
                $filename = array_key_first($value);
                $value = $filename ? 'settings/' . $filename : null;
            }

            if ($value !== null) {
                Setting::set($key, $value);
            }
        }

        Notification::make()->title('Pengaturan berhasil disimpan!')->success()->send();
    }
}
