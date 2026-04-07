<?php

namespace App\Http\Controllers;

use App\Models\ServiceQrLabel;
use Illuminate\Http\Request;

class ServiceQrLabelPreviewPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('ids')))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values();

        abort_if($ids->isEmpty(), 404, 'Nema odabranih stavki za prikaz.');

        $labelsQuery = ServiceQrLabel::query()->whereIn('id', $ids);

        $labels = $labelsQuery
            ->orderByRaw('FIELD(id, ' . $ids->implode(',') . ')')
            ->get();

        abort_if($labels->isEmpty(), 404, 'Nema odabranih stavki za prikaz.');

        return view('service-qr-labels.preview-print', [
            'labels' => $labels,
        ]);
    }
}
