<?php

namespace App\Http\Controllers;

use App\Models\QrLabel;
use Illuminate\Http\Response;

class QrLabelPublicController extends Controller
{
    public function show(string $token)
    {
        $label = QrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        // ✅ Disabled: prikaži poruku (bez detalja/bez QR-a)
        if ($label->disabled_at) {
            return response()
                ->view('qr-labels.disabled', compact('label'))
                ->setStatusCode(Response::HTTP_OK);
        }

        // ✅ Active: postojeći public view (tvoj fajl: resources/views/qr-labels/public-show.blade.php)
        return view('qr-labels.public-show', compact('label'));
    }

    public function print(string $token)
    {
        $label = QrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        // ✅ Disabled: print-friendly poruka (bez valid QR-a)
        if ($label->disabled_at) {
            return response()
                ->view('qr-labels.disabled-print', compact('label'))
                ->setStatusCode(Response::HTTP_OK);
        }

        // ✅ Active: tvoj print view (tvoj fajl: resources/views/qr-labels/public-print-zebra.blade.php)
        return view('qr-labels.public-print-zebra', compact('label'));
    }
}