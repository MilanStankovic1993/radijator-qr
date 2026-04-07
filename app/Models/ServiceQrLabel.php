<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServiceQrLabel extends Model
{
    protected $table = 'service_qr_labels';

    protected $fillable = [
        'token',
        'picture_path',
        'date',
        'supplier_order_number',
        'name',
        'boiler_type',
        'dimension',
        'code_pdm',
        'weight',
        'price',
        'buyer',
        'quantity',
        'note',
        'printed_at',
        'printed_by',
        'created_by',
        'updated_by',
        'disabled_at',
    ];

    protected $casts = [
        'date' => 'date',
        'weight' => 'decimal:2',
        'price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'printed_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (blank($model->token)) {
                $model->token = Str::upper(Str::random(10));
            }

            if (Auth::check() && blank($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function (self $model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

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