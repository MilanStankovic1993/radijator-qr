<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrLabel extends Model
{
    protected $table = 'qr_labels';

    protected $fillable = [
        'token',

        // zajedničko
        'po_number',
        'vendor_no',
        'buyer',
        'storage_location',
        'load_date',
        'order_type',
        'quantity',
        'um',
        'price',

        // radijator
        'ri_item_number',
        'ri_code',
        'ri_name',
        'ri_doc_number',

        // group atlantic
        'ga_item_number',
        'ga_internal_number',
        'ga_code',
        'ga_name',

        // billing / shipping
        'billing_address',
        'billing_email',
        'shipping_address',
        'terms_payment',
        'terms_delivery',

        'note',
        'created_by',
        'disabled_at',
    ];

    protected $casts = [
        'load_date'   => 'date',
        'quantity'    => 'decimal:3',
        'price'       => 'decimal:2',
        'disabled_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (blank($m->token)) {
                $m->token = Str::upper(Str::random(10));
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isDisabled(): bool
    {
        return ! is_null($this->disabled_at);
    }

    public function disable(): void
    {
        $this->update([
            'disabled_at' => now(),
        ]);
    }

    public function enable(): void
    {
        $this->update([
            'disabled_at' => null,
        ]);
    }
}