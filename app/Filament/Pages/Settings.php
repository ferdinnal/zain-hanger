<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Website';
    protected static string  $view
            = 'filament.pages.settings';
    protected static ?int $navigationSort = 99;
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

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Tabs::make('Settings')->tabs([

                Tabs\Tab::make('Identitas Toko')->schema([
                    TextInput::make('site_name')->label('Nama Website')->required(),
                    TextInput::make('site_name_short')->label('Nama Pendek (Logo Teks)'),
                    TextInput::make('site_name_sub')->label('Sub Nama (Logo)'),
                    FileUpload::make('site_logo')->label('Logo')->image()->directory('settings'),
                    FileUpload::make('site_favicon')->label('Favicon')->image()->directory('settings'),
                    Textarea::make('site_description')->label('Deskripsi Meta')->rows(2),
                    TextInput::make('site_copyright')->label('Copyright Footer'),
                ]),

                Tabs\Tab::make('Hero Section')->schema([
                    FileUpload::make('hero_image')->label('Gambar Hero')->image()->directory('settings'),
                    TextInput::make('hero_subtitle')->label('Subjudul'),
                    Textarea::make('hero_title')->label('Judul (boleh HTML)')->rows(2),
                    Textarea::make('hero_description')->label('Deskripsi')->rows(2),
                    TextInput::make('hero_cta_primary')->label('Tombol Utama'),
                    TextInput::make('hero_cta_secondary')->label('Tombol Sekunder'),
                ]),

                Tabs\Tab::make('Tentang Kami')->schema([
                    TextInput::make('about_title')->label('Judul'),
                    TextInput::make('about_subtitle')->label('Subtitle'),
                    FileUpload::make('about_image')->label('Foto About')->image()->directory('settings'),
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
            Setting::set($key, $value);
        }
        Notification::make()->title('Pengaturan berhasil disimpan!')->success()->send();
    }
}
