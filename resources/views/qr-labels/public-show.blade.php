<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Purchase order {{ $label->token }}</title>

  <style>
    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; background:#fff; color:#111; font-family: Arial, Helvetica, sans-serif; }

    @page { size: A4; margin: 10mm; }

    body{
      background:#f2f4f7;
      padding: 18px;
      display:flex;
      justify-content:center;
      align-items:flex-start;
    }

    .sheet{
      width: 190mm;
      background:#fff;
      border:1px solid #d1d5db;
      padding: 10mm 10mm 8mm;
    }

    @media print{
      body{ background:#fff; padding:0; }
      .sheet{ border:none; width:auto; padding:0; }
      .no-print{ display:none !important; }
    }

    /* ===== Header (full-width like PO) ===== */
    .po-header{
      padding-bottom: 4mm;
      margin-bottom: 4mm;
      border-bottom: 1px solid #9ca3af;
    }
    .po-header .row{
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap: 8mm;
    }
    .po-header .leftTitle{
      font-size: 18pt;
      font-weight: 800;
      margin: 0;
      line-height: 1.05;
    }
    .po-header .rightNumber{
      font-size: 18pt;
      font-weight: 800;
      margin: 0;
      line-height: 1.05;
      text-align:right;
      word-break: break-word;
    }

    /* ===== Subheader: LOGO LEFT + bigger ===== */
    .subhead{
      display:flex;
      align-items:center;
      gap: 8mm;
      margin-bottom: 6mm;
    }

    .subhead .logo{
      flex: 0 0 auto;
      display:flex;
      align-items:center;
      justify-content:flex-start;
    }
    .subhead img{
      width: 48mm; /* bigger */
      height:auto;
      object-fit:contain;
      display:block;
    }

    .subhead .info{
      flex:1;
      min-width:0;
      display:flex;
      align-items:baseline;
      gap: 6mm;
      flex-wrap:wrap;
    }

    .subhead .info .company{
      font-size: 12pt;
      font-weight: 800;
      margin:0;
      line-height:1.15;
      white-space: nowrap;
    }
    .subhead .info .city{
      font-size: 10pt;
      font-weight: 700;
      margin:0;
      color:#111;
      white-space: nowrap;
    }

    /* ===== Boxes grid ===== */
    .grid2{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 8mm;
      margin-top: 4mm;
      margin-bottom: 6mm;
    }

    .box{
      border:1px solid #9ca3af;
      padding: 4.5mm;
      min-height: 36mm;
    }
    .box .box-title{
      font-size: 10pt;
      font-weight: 800;
      margin: 0 0 3mm 0;
    }

    table{ width:100%; border-collapse:collapse; }

    .kv td{
      padding: 1.1mm 0;
      font-size: 9.5pt;
      vertical-align: top;
    }
    .kv td.k{
      width: 46%;
      color:#111;
      font-weight: 700;
      padding-right: 4mm;
      white-space: nowrap;
    }
    .kv td.v{
      color:#111;
      font-weight: 400;
      word-break: break-word;
    }

    .section-line{
      margin: 4mm 0 2.5mm;
      font-size: 10pt;
      font-weight: 700;
    }

    /* Items table */
    .items{
      width:100%;
      border:1px solid #111;
    }
    .items thead th{
      font-size: 9pt;
      font-weight: 800;
      text-align:left;
      padding: 2mm 2mm;
      border-bottom:1px solid #111;
      background:#fff;
    }
    .items td{
      font-size: 9.5pt;
      padding: 2mm 2mm;
      border-top:1px solid #9ca3af;
      vertical-align: top;
    }
    .right{ text-align:right; }
    .center{ text-align:center; }

    /* Totals */
    .totals{
      display:flex;
      justify-content:flex-end;
      margin-top: 5mm;
    }
    .totals table{ width: 82mm; }
    .totals td{
      font-size: 10pt;
      padding: 2mm 0;
    }
    .totals td.k{
      font-weight: 700;
      padding-right: 6mm;
    }
    .totals td.v{
      font-weight: 700;
      text-align:right;
    }
    .totals tr + tr td{
      border-top:1px solid #9ca3af;
    }

    /* Footer note */
    .footer{
      margin-top: 8mm;
      border:1px solid #9ca3af;
      padding: 4.5mm;
      font-size: 9pt;
      text-align:center;
      line-height: 1.35;
      color:#111;
    }

    /* Actions */
    .actions{
      margin-top: 8mm;
      display:flex;
      gap:10px;
      justify-content:flex-end;
      flex-wrap:wrap;
    }
    .btn{
      display:inline-block;
      padding: 10px 14px;
      border:1px solid #111;
      background:#111;
      color:#fff;
      text-decoration:none;
      font-weight: 800;
      border-radius: 10px;
      font-size: 14px;
    }
    .btn.secondary{
      background:#fff;
      color:#111;
    }

    .alert{
      margin-top: 6mm;
      border:1px solid #f59e0b;
      background:#fffbeb;
      padding: 10px 12px;
      font-weight: 700;
      color:#92400e;
    }
  </style>
</head>
<body>

@php
  $docNumber = $label->po_number ?: $label->token;

  $createdAtDate = $label->created_at ? $label->created_at->format('d.m.Y') : '—';
  $createdAtTime = $label->created_at ? $label->created_at->format('H:i') : '—';

  $deliveryDate = $label->load_date ? $label->load_date->format('d.m.Y') : '—';

  $qty = $label->quantity ?? null;
  $unitPrice = $label->price ?? null;

  $unitPriceDisplay = ($unitPrice === null || $unitPrice === '') ? '—' : (string) $unitPrice;

  $netAmountDisplay = '—';
  if ($qty !== null && $qty !== '' && is_numeric($qty) && $unitPrice !== null && $unitPrice !== '' && is_numeric($unitPrice)) {
    $netAmountDisplay = number_format(((float)$qty) * ((float)$unitPrice), 2, '.', ',');
  } elseif ($unitPriceDisplay !== '—') {
    $netAmountDisplay = $unitPriceDisplay;
  }

  $infoLeft = [
    'Document Number' => $docNumber,
    'Date' => $createdAtDate,
    'Time' => $createdAtTime,
    'Vendor No.' => $label->vendor_no ?: '—',
    'Buyer' => $label->buyer ?: '—',
    'Delivery Date' => $deliveryDate,
    'Receiving / Storage Location' => $label->storage_location ?: '—',
  ];

  $companyRight = [
    'Company' => 'Radijator-Inženjering d.o.o.',
    'Address' => 'Ul. Zivojina Lazica-Solunca Br.6',
    'City' => '36000 Kraljevo, Serbia',
  ];

  $billing = [
    'Billing Address' => $label->billing_address ?: '—',
    'Email' => $label->billing_email ?: '—',
  ];

  $shipping = [
    'Shipping Address' => $label->shipping_address ?: '—',
    'Terms of payment' => $label->terms_payment ?: '—',
    'Terms of delivery' => $label->terms_delivery ?: '—',
  ];

  $descLines = array_filter([
    $label->ga_code ? ($label->ga_code . ' — ' . ($label->ga_name ?: '')) : ($label->ga_name ?: null),
    $label->ga_item_number ? ('GA Item No.: ' . $label->ga_item_number) : null,
    $label->ga_internal_number ? ('GA Internal No.: ' . $label->ga_internal_number) : null,
    $label->ri_code ? ('RI Code: ' . $label->ri_code . ' — ' . ($label->ri_name ?: '')) : ($label->ri_name ? ('RI: ' . $label->ri_name) : null),
    $label->ri_item_number ? ('RI Item No.: ' . $label->ri_item_number) : null,
    $label->ri_doc_number ? ('Goods receipt / Delivery note: ' . $label->ri_doc_number) : null,
  ]);

  $um = $label->um ?: 'PC';
@endphp

<div class="sheet">

  <div class="po-header">
    <div class="row">
      <p class="leftTitle">Purchase order</p>
      <p class="rightNumber">{{ $docNumber }}</p>
    </div>
  </div>

  <div class="subhead">
    <div class="logo">
      <img src="{{ asset('images/logo.png') }}?v=1" alt="Radijator">
    </div>

    <div class="info">
      <p class="company">Radijator Inženjering</p>
      <p class="city">36000 Kraljevo, Serbia</p>
    </div>
  </div>

  <div class="grid2">
    <div class="box">
      <div class="box-title">Information</div>
      <table class="kv">
        @foreach($infoLeft as $k => $v)
          <tr>
            <td class="k">{{ $k }}</td>
            <td class="v">{{ ($v === '' || $v === null) ? '—' : $v }}</td>
          </tr>
        @endforeach
      </table>
    </div>

    <div class="box">
      <div class="box-title">Company</div>
      <table class="kv">
        @foreach($companyRight as $k => $v)
          <tr>
            <td class="k">{{ $k }}</td>
            <td class="v">{{ ($v === '' || $v === null) ? '—' : $v }}</td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>

  <div class="grid2">
    <div class="box">
      <div class="box-title">Billing Address</div>
      <table class="kv">
        @foreach($billing as $k => $v)
          <tr>
            <td class="k">{{ $k }}</td>
            <td class="v">{{ ($v === '' || $v === null) ? '—' : $v }}</td>
          </tr>
        @endforeach
      </table>
    </div>

    <div class="box">
      <div class="box-title">Shipping Address</div>
      <table class="kv">
        @foreach($shipping as $k => $v)
          <tr>
            <td class="k">{{ $k }}</td>
            <td class="v">{{ ($v === '' || $v === null) ? '—' : $v }}</td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>

  <div class="section-line">We require an order acknowledgment for the following items:</div>

  <table class="items">
    <thead>
      <tr>
        <th style="width:10mm;">Item</th>
        <th>Material / Description</th>
        <th class="right" style="width:22mm;">Quantity</th>
        <th class="center" style="width:16mm;">UM</th>
        <th class="right" style="width:34mm;">Unit Price</th>
        <th class="right" style="width:34mm;">Net Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="center">10</td>
        <td>
          @foreach($descLines as $line)
            <div>{{ $line }}</div>
          @endforeach

          @if($label->note)
            <div style="margin-top:2mm;"><strong>Note:</strong> {!! nl2br(e($label->note)) !!}</div>
          @endif
        </td>
        <td class="right">{{ ($qty === null || $qty === '') ? '—' : $qty }}</td>
        <td class="center">{{ $um }}</td>
        <td class="right">{{ $unitPriceDisplay }}</td>
        <td class="right">{{ $netAmountDisplay }}</td>
      </tr>
    </tbody>
  </table>

  <div class="totals">
    <table>
      <tr>
        <td class="k">Total net value excl. tax</td>
        <td class="v">{{ $netAmountDisplay }}</td>
      </tr>
      <tr>
        <td class="k">Total value</td>
        <td class="v">{{ $netAmountDisplay }}</td>
      </tr>
    </table>
  </div>

  <div class="footer">
    PLEASE NOTE: Please include our document number and item references on all your documents
    (order confirmation, delivery note, invoice, etc.).<br>
    Loading / unloading hours: Monday–Friday 07:00–12:00 and 12:30–14:30.
  </div>

  <div class="actions no-print">
    <a class="btn" href="{{ route('qr-labels.public.print', $label->token) }}" target="_blank">Print (A4)</a>
    <button class="btn secondary" type="button" onclick="window.print()">Print this page</button>
    <a class="btn secondary" href="{{ url('/') }}">Home</a>
  </div>

  @if($label->disabled_at)
    <div class="alert">
      This label is disabled (scan view is informational only).
    </div>
  @endif

</div>

</body>
</html>