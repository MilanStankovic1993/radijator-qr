<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrItemMapping extends Model
{
    protected $fillable = [
        'ri_item_number',
        'ri_name',
        'ga_item_number',
        'ga_code',
        'ga_name',
    ];

    public function qrLabels(): HasMany
    {
        return $this->hasMany(QrLabel::class);
    }

    public function displayName(): string
    {
        $ri = trim(($this->ri_item_number ?: '-') . ' / ' . ($this->ri_name ?: '-'));
        $ga = trim(($this->ga_item_number ?: '-') . ' / ' . ($this->ga_code ?: '-') . ' / ' . ($this->ga_name ?: '-'));

        return "RI: {$ri} | GA: {$ga}";
    }
}