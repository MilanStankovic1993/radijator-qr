<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QrLabel extends Model
{
    use SoftDeletes;

    protected $table = 'qr_labels';

    protected $fillable = [
        'qr_item_mapping_id',
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

        // print
        'printed_at',
        'printed_by',

        // audit
        'created_by',
        'updated_by',
        'deleted_by',

        // status
        'disabled_at',
    ];

    protected $casts = [
        'load_date'   => 'date',
        'quantity'    => 'decimal:3',
        'price'       => 'decimal:2',
        'printed_at'  => 'datetime',
        'disabled_at' => 'datetime',
        'deleted_at'  => 'datetime',
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

            if (Auth::check() && blank($m->created_by)) {
                $m->created_by = Auth::id();
            }
        });

        static::updating(function (self $m) {
            if (Auth::check()) {
                $m->updated_by = Auth::id();
            }
        });

        static::deleting(function (self $m) {
            // samo na soft delete (ne forceDelete)
            if (! $m->isForceDeleting() && Auth::check()) {
                $m->deleted_by = Auth::id();

                // upiši deleted_by pre nego što se postavi deleted_at
                $m->saveQuietly();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function itemMapping(): BelongsTo
    {
        return $this->belongsTo(QrItemMapping::class, 'qr_item_mapping_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
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

    public function isPrinted(): bool
    {
        return ! is_null($this->printed_at);
    }

    public function markAsPrinted(): void
    {
        $this->update([
            'printed_at' => now(),
            'printed_by' => Auth::id(),
        ]);
    }

    public function unmarkPrinted(): void
    {
        $this->update([
            'printed_at' => null,
            'printed_by' => null,
        ]);
    }
}