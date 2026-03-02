<?php

namespace App\Http\Controllers;

use App\Models\QrLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrLabelController extends Controller
{
    public function create()
    {
        return view('qr-labels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // -----------------------------
            // ZAJEDNIČKI PODACI
            // -----------------------------
            'po_number'         => ['nullable', 'string', 'max:255'],
            'vendor_no'         => ['nullable', 'string', 'max:255'],
            'buyer'             => ['nullable', 'string', 'max:255'],

            'storage_location'  => ['nullable', 'string', 'max:255'],
            'load_date'         => ['nullable', 'date'],
            'order_type'        => ['nullable', 'string', 'max:255'],

            'quantity'          => ['nullable', 'numeric'],
            'um'                => ['nullable', 'string', 'max:20'],
            'price'             => ['nullable', 'numeric'],

            // -----------------------------
            // BILLING / SHIPPING & TERMS
            // -----------------------------
            'billing_address'   => ['nullable', 'string', 'max:5000'],
            'billing_email'     => ['nullable', 'email', 'max:255'],

            'shipping_address'  => ['nullable', 'string', 'max:5000'],
            'terms_payment'     => ['nullable', 'string', 'max:255'],
            'terms_delivery'    => ['nullable', 'string', 'max:255'],

            // -----------------------------
            // INTERNI – RADIJATOR INŽ
            // -----------------------------
            'ri_item_number'    => ['nullable', 'string', 'max:255'],
            'ri_code'           => ['nullable', 'string', 'max:255'],
            'ri_name'           => ['nullable', 'string', 'max:255'],
            'ri_doc_number'     => ['nullable', 'string', 'max:255'],

            // -----------------------------
            // GROUP ATLANTIC
            // -----------------------------
            'ga_item_number'      => ['nullable', 'string', 'max:255'],
            'ga_internal_number'  => ['nullable', 'string', 'max:255'],
            'ga_code'             => ['nullable', 'string', 'max:255'],
            'ga_name'             => ['nullable', 'string', 'max:255'],

            // ostalo
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        // fallback za UM ako nije poslato
        if (! array_key_exists('um', $data) || $data['um'] === null || $data['um'] === '') {
            $data['um'] = 'PC';
        }

        $token = Str::upper(Str::random(10));

        $label = QrLabel::create([
            'token'      => $token,
            'created_by' => optional($request->user())->id,
            ...$data,
        ]);

        return redirect()
            ->route('qr-labels.create')
            ->with('created_label_url', route('qr-labels.public.show', $label->token));
    }
}