<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrLabelController;
use App\Http\Controllers\QrLabelPublicController;
use App\Http\Controllers\ServiceQrLabelPublicController;

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

Route::get('/__debug/panel-provider', function () {
    $class = \App\Providers\Filament\AdminPanelProvider::class;

    return response()->json([
        'environment' => app()->environment(),
        'class' => $class,
        'class_exists' => class_exists($class),
        'file' => class_exists($class) ? (new \ReflectionClass($class))->getFileName() : null,
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
 * Public (QR scan) - standardne QR nalepnice
 * URL u QR kodu: /doc/{token}
 */
Route::prefix('doc')->group(function () {
    Route::get('/{token}', [QrLabelPublicController::class, 'show'])
        ->name('qr-labels.public.show');

    Route::get('/{token}/print', [QrLabelPublicController::class, 'print'])
        ->name('qr-labels.public.print');

    Route::get('/{token}/print-direct', [QrLabelPublicController::class, 'printDirect'])
        ->name('qr-labels.public.print-direct');
});

/**
 * Public (QR scan) - servisni / part QR kodovi
 * URL u QR kodu: /service-doc/{token}
 */
Route::prefix('service-doc')->group(function () {
    Route::get('/{token}', [ServiceQrLabelPublicController::class, 'show'])
        ->name('service-qr-labels.public.show');

    Route::get('/{token}/print', [ServiceQrLabelPublicController::class, 'print'])
        ->name('service-qr-labels.public.print');

    Route::get('/{token}/zpl', [ServiceQrLabelPublicController::class, 'zpl'])
        ->name('service-qr-labels.public.zpl');

    Route::get('/{token}/print-direct', [ServiceQrLabelPublicController::class, 'printDirect'])
        ->name('service-qr-labels.public.print-direct');
});

/**
 * Opciono: ručni unos mimo Filamenta
 */
Route::get('/qr-labels/create', [QrLabelController::class, 'create'])
    ->name('qr-labels.create');

Route::post('/qr-labels', [QrLabelController::class, 'store'])
    ->name('qr-labels.store');
