<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrLabelController;
use App\Http\Controllers\QrLabelPublicController;

/*
|--------------------------------------------------------------------------
| DEBUG – privremeno za Railway
|--------------------------------------------------------------------------
| OBRIŠI kada završimo debug!
*/

Route::get('/__debug/routes', function () {
    $out = [];

    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        $uri = $route->uri();

        if (str_starts_with($uri, 'admin') || str_contains($uri, 'filament')) {
            $out[] = [
                'methods' => implode('|', $route->methods()),
                'uri'     => $uri,
                'name'    => $route->getName(),
                'action'  => is_string($route->getActionName())
                    ? $route->getActionName()
                    : '',
            ];
        }
    }

    return response()->json([
        'environment' => app()->environment(),
        'route_count' => count($out),
        'routes'      => $out,
    ]);
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/admin');
});

/**
 * Public (QR scan)
 * - URL u QR kodu: /doc/{token}
 */
Route::get('/doc/{token}', [QrLabelPublicController::class, 'show'])
    ->name('qr-labels.public.show');

Route::get('/doc/{token}/print', [QrLabelPublicController::class, 'print'])
    ->name('qr-labels.public.print');

/**
 * Opciono: ručni unos mimo Filamenta
 */
Route::get('/qr-labels/create', [QrLabelController::class, 'create'])
    ->name('qr-labels.create');

Route::post('/qr-labels', [QrLabelController::class, 'store'])
    ->name('qr-labels.store');