<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'user_id', 'customer_name', 'customer_email',
        'customer_phone', 'shipping_address', 'notes',
        'total_amount', 'status', 'source',
        'wa_sent_at', 'wa_message_preview',
    ];

    protected $casts = [
        'wa_sent_at'   => 'datetime',
        'total_amount' => 'decimal:0',
    ];

    const STATUS_LABELS = [
        'pending'    => ['label' => 'Menunggu',     'color' => 'amber'],
        'confirmed'  => ['label' => 'Dikonfirmasi', 'color' => 'blue'],
        'processing' => ['label' => 'Diproses',     'color' => 'purple'],
        'shipped'    => ['label' => 'Dikirim',      'color' => 'indigo'],
        'done'       => ['label' => 'Selesai',      'color' => 'green'],
        'cancelled'  => ['label' => 'Dibatalkan',   'color' => 'red'],
    ];

    protected static function booted(): void
    {
        static::creating(function ($order) {
            $order->order_code = static::generateCode();
        });
    }

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;
        return 'ZH-' . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    // ── Relations ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Accessors ───────────────────────────────────────────────

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_LABELS[$this->status]['color'] ?? 'gray';
    }

    // ── WhatsApp ────────────────────────────────────────────────

    public function buildWaMessage(): string
    {
        $template  = Setting::get('wa_order_template', '');
        $firstItem = $this->items->first();
        $snapshot  = $firstItem?->product_snapshot ?? [];

        // Multi-item: buat list semua produk
        if ($this->items->count() > 1) {
            $itemList = $this->items->map(fn ($item) =>
                "📦 *{$item->product_snapshot['name']}*\n" .
                "   Qty: {$item->qty} pcs × Rp " .
                number_format($item->price_per_unit, 0, ',', '.') .
                ' = Rp ' . number_format($item->subtotal, 0, ',', '.')
            )->join("\n");

            return
                "Halo " . Setting::get('site_name', 'Zain Hanger') .
                ", saya *{$this->customer_name}* ingin memesan:\n\n" .
                $itemList .
                "\n\n*Total: {$this->total_formatted}*\n" .
                "Kode Order: #{$this->order_code}\n" .
                "Mohon konfirmasinya 🙏";
        }

        $replacements = [
            '{site_name}'      => Setting::get('site_name', 'Zain Hanger'),
            '{customer_name}'  => $this->customer_name,
            '{product_name}'   => $snapshot['name'] ?? '-',
            '{kepala}'         => $snapshot['kepala_label'] ?? '-',
            '{jenis}'          => $snapshot['jenis_label'] ?? '-',
            '{qty}'            => (string) ($firstItem?->qty ?? 0),
            '{price_per_unit}' => 'Rp ' . number_format($firstItem?->price_per_unit ?? 0, 0, ',', '.'),
            '{total}'          => $this->total_formatted,
            '{order_code}'     => $this->order_code,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }

    public function getWaUrl(): string
    {
        $phone   = preg_replace('/[^0-9]/', '', Setting::get('contact_whatsapp', '6282291409209'));
        $message = $this->buildWaMessage();
        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }
}
