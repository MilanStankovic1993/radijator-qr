<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrLabelAudit extends Model
{
    protected $table = 'qr_label_audits';

    protected $fillable = [
        'qr_label_id',
        'user_id',
        'action',
        'before',
        'after',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function label(): BelongsTo
    {
        return $this->belongsTo(QrLabel::class, 'qr_label_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}