<?php

namespace App\Observers;

use App\Models\QrLabel;
use App\Models\QrLabelAudit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class QrLabelObserver
{
    public function created(QrLabel $label): void
    {
        $this->log($label, 'create', null, $label->getAttributes());
    }

    public function updated(QrLabel $label): void
    {
        $changes = $label->getChanges();
        if (empty($changes)) {
            return;
        }

        // ne loguj updated_at samo
        if (count($changes) === 1 && array_key_exists('updated_at', $changes)) {
            return;
        }

        $before = [];
        foreach ($changes as $key => $val) {
            $before[$key] = $label->getOriginal($key);
        }

        // specijalno: disable/enable
        if (array_key_exists('disabled_at', $changes)) {
            $action = $changes['disabled_at'] ? 'disable' : 'enable';
        } else {
            $action = 'update';
        }

        $this->log($label, $action, $before, $changes);
    }

    public function deleted(QrLabel $label): void
    {
        // soft delete
        $this->log($label, 'delete', null, null);
    }

    public function restored(QrLabel $label): void
    {
        $this->log($label, 'restore', null, null);
    }

    protected function log(QrLabel $label, string $action, ?array $before, ?array $after): void
    {
        QrLabelAudit::create([
            'qr_label_id' => $label->id,
            'user_id'     => Auth::id(),
            'action'      => $action,
            'before'      => $before,
            'after'       => $after,
            'ip_address'  => Request::ip(),
            'user_agent'  => (string) Request::userAgent(),
        ]);
    }
}