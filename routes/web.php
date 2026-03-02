<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrLabelController;
use App\Http\Controllers\QrLabelPublicController;

Route::get('/', function () {
    // Može welcome, ili redirect na admin ako hoćeš:
    return redirect('/admin');
});

/**
 * Public (QR scan)
 * - ovo je URL koji upisuješ u QR kod: /doc/{token}
 */
Route::get('/doc/{token}', [QrLabelPublicController::class, 'show'])
    ->name('qr-labels.public.show');

Route::get('/doc/{token}/print', [QrLabelPublicController::class, 'print'])
    ->name('qr-labels.public.print');

/**
 * Opciono: ručni unos mimo Filamenta
 * (Ako ti ne treba, možeš obrisati ovaj blok.)
 */
Route::get('/qr-labels/create', [QrLabelController::class, 'create'])
    ->name('qr-labels.create');

Route::post('/qr-labels', [QrLabelController::class, 'store'])
    ->name('qr-labels.store');