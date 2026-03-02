<?php

namespace App\Http\Controllers;

use App\Models\QrLabel;

class QrLabelPublicController extends Controller
{
    public function show(string $token)
    {
        $label = QrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        if ($label->isDisabled()) {
            abort(410, 'Ova etiketa je deaktivirana.');
        }

        return view('qr-labels.public-show', compact('label'));
    }

    public function print(string $token)
    {
        $label = QrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        if ($label->isDisabled()) {
            abort(410, 'Ova etiketa je deaktivirana.');
        }

        // print view (A4 / Zebra – kako već imaš)
        return view('qr-labels.public-print-zebra', compact('label'));
    }
}