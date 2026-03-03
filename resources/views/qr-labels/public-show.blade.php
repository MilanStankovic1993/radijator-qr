<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <!-- bitno: viewport ostaje -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ TAB ICON (favicon) - stavi fajlove u /public -->
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">

  <style>
    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; background:#fff; color:#111; font-family: Arial, Helvetica, sans-serif; }

    /* PRINT defaults (A4) */
    @page { size: A4; margin: 10mm; }

    body{
      background:#f2f4f7;
      padding: 18px;
      display:flex;
      justify-content:center;
      align-items:flex-start;
    }

    /* ===== SCREEN (mobile/desktop) container ===== */
    .sheet{
      width: min(980px, calc(100vw - 24px));
      background:#fff;
      border:1px solid #d1d5db;
      border-radius: 14px;
      padding: 18px;
    }

    /* ===== PRINT overrides (A4 layout ALWAYS) ===== */
    @media print{
      html, body{ background:#fff !important; }
      body{
        padding:0 !important;
        display:block !important;   /* ne flex u printu */
      }
      .sheet{
        border:none !important;
        border-radius:0 !important;
        width: 190mm !important;
        padding: 10mm 10mm 8mm !important;
      }
      .no-print{ display:none !important; }

      /* bitno: na printu nema horizontalnog scroll-a */
      .table-scroll{ overflow: visible !important; }
      .items{ min-width: 0 !important; }

      /* spreči random page-break */
      .items, .totals, .footer, .grid2, .box { page-break-inside: avoid; break-inside: avoid; }
      .items tr { page-break-inside: avoid; break-inside: avoid; }

      @page { size: A4; margin: 10mm; }
    }

    /* ===== Header (screen) ===== */
    .po-header{
      padding-bottom: 14px;
      margin-bottom: 14px;
      border-bottom: 1px solid #9ca3af;
    }
    .po-header .row{
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap: 18px;
    }
    .po-header .leftTitle,
    .po-header .rightNumber{
      font-size: 26px;
      font-weight: 900;
      margin: 0;
      line-height: 1.05;
    }
    .po-header .rightNumber{
      text-align:right;
      word-break: normal;
      overflow-wrap: anywhere;
    }

    /* ===== Print typography ===== */
    @media print{
      .po-header{ padding-bottom: 4mm; margin-bottom: 4mm; }
      .po-header .row{ gap: 8mm; }
      .po-header .leftTitle,
      .po-header .rightNumber{ font-size: 18pt; }
    }

    /* ===== Subheader ===== */
    .subhead{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 18px;
      margin-bottom: 18px;
      flex-wrap:wrap;
    }

    .subhead .logo{
      flex: 0 0 auto;
      display:flex;
      align-items:center;
      justify-content:flex-start;
    }

    .subhead img{
      width: 220px;
      max-width: 60vw;
      height:auto;
      object-fit:contain;
      display:block;
    }

    .subhead .info{
      flex: 1 1 auto;
      min-width: 240px;
      text-align:right;
    }
    .subhead .info .company{
      font-size: 18px;
      font-weight: 900;
      margin:0;
      line-height:1.15;
      white-space: normal;
      overflow-wrap:anywhere;
    }
    .subhead .info .city{
      font-size: 14px;
      font-weight: 700;
      margin:6px 0 0;
      color:#111;
      white-space: normal;
      overflow-wrap:anywhere;
    }

    @media print{
      .subhead{ gap: 8mm; margin-bottom: 6mm; }
      .subhead img{ width: 48mm; max-width:none; }
      .subhead .info{ text-align:left; display:flex; align-items:baseline; gap:6mm; flex-wrap:wrap; }
      .subhead .info .company{ font-size: 12pt; white-space:nowrap; }
      .subhead .info .city{ font-size: 10pt; margin:0; white-space:nowrap; }
    }

    /* ===== Boxes grid ===== */
    .grid2{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      margin-top: 10px;
      margin-bottom: 18px;
    }

    .box{
      border:1px solid #9ca3af;
      padding: 14px;
      min-height: 0;
      border-radius: 12px;
    }
    .box .box-title{
      font-size: 14px;
      font-weight: 900;
      margin: 0 0 12px 0;
    }

    table{ width:100%; border-collapse:collapse; }

    .kv td{
      padding: 7px 0;
      font-size: 14px;
      vertical-align: top;
    }
    .kv td.k{
      width: 44%;
      color:#111;
      font-weight: 800;
      padding-right: 14px;
      white-space: nowrap;
    }
    .kv td.v{
      color:#111;
      font-weight: 400;
      overflow-wrap:anywhere;
      word-break: normal;
    }

    @media print{
      .grid2{ gap: 8mm; margin-top: 4mm; margin-bottom: 6mm; }
      .box{ padding: 4.5mm; border-radius:0; min-height: 36mm; }
      .box .box-title{ font-size: 10pt; margin: 0 0 3mm 0; }
      .kv td{ padding: 1.1mm 0; font-size: 9.5pt; }
      .kv td.k{ width: 46%; padding-right: 4mm; }
    }

    /* ✅ Mobile: SAMO SCREEN. Ne utiče na print preview */
    @media screen and (max-width: 720px){
      body{ padding: 12px; }
      .sheet{ padding: 14px; border-radius: 14px; }

      .po-header .row{ flex-direction:column; align-items:flex-start; }
      .po-header .rightNumber{ text-align:left; }

      .subhead{ flex-direction:column; align-items:flex-start; }
      .subhead .info{ text-align:left; min-width:0; width:100%; }

      .grid2{ grid-template-columns: 1fr; }
      .kv td.k{ width: 42%; }
    }

    .section-line{
      margin: 16px 0 10px;
      font-size: 14px;
      font-weight: 800;
    }
    @media print{
      .section-line{ margin: 4mm 0 2.5mm; font-size: 10pt; }
    }

    /* Items table */
    .items{
      width:100%;
      border:1px solid #111;
      border-radius: 12px;
      overflow:hidden;
      table-layout: fixed; /* ✅ stabilne kolone */
    }

    /* ✅ kolone (SCREEN default) */
    .items col.col-item{ width:70px; }
    .items col.col-desc{ width:auto; }
    .items col.col-qty{ width:110px; }
    .items col.col-um{ width:80px; }
    .items col.col-unit{ width:140px; }
    .items col.col-net{ width:140px; }

    .items thead th{
      font-size: 13px;
      font-weight: 900;
      text-align:left;
      padding: 10px 10px;
      border-bottom:1px solid #111;
      background:#fff;
    }
    .items td{
      font-size: 14px;
      padding: 10px 10px;
      border-top:1px solid #e5e7eb;
      vertical-align: top;
      overflow-wrap:anywhere;
      word-break: normal;
    }

    .right{ text-align:right; }
    .center{ text-align:center; }

    /* ✅ opis: default (screen) kao pre */
    .desc-line{ display:block; }
    .desc-note{ margin-top:10px; }

    @media print{
      .items{ border-radius:0; }
      .items thead th{ font-size: 9pt; padding: 2mm 2mm; }
      .items td{ font-size: 9pt; padding: 2mm 2mm; border-top:1px solid #9ca3af; line-height:1.15; }

      /* ✅ print: SUZI ostale kolone da Material dobije više prostora */
      .items col.col-item{ width:12mm; }
      .items col.col-qty{ width:16mm; }
      .items col.col-um{ width:10mm; }
      .items col.col-unit{ width:18mm; }
      .items col.col-net{ width:20mm; }
      /* desc ostaje auto = maksimalno */

      /* ✅ print: SPoji linije opisa u “tekući” tekst (da ne bude duguljasto) */
      .desc-line{
        display:inline;
        white-space: normal;
      }
      .desc-line:after{
        content: " • ";
      }
      .desc-line:last-child:after{
        content: "";
      }

      /* note neka ide u novi red i malo manjim */
      .desc-note{
        margin-top: 1.5mm;
        font-size: 8.5pt;
        line-height: 1.15;
      }
    }

    /* Mobile: table scroll on SCREEN only */
    .table-scroll{
      width:100%;
      overflow-x:auto;
      -webkit-overflow-scrolling: touch;
    }
    @media screen and (max-width: 720px){
      .items{
        min-width: 720px; /* na telefonu skrol, umesto sabijanja */
      }
    }

    /* Totals */
    .totals{
      display:flex;
      justify-content:flex-end;
      margin-top: 18px;
    }
    .totals table{ width: min(420px, 100%); }
    .totals td{
      font-size: 14px;
      padding: 10px 0;
    }
    .totals td.k{
      font-weight: 800;
      padding-right: 18px;
    }
    .totals td.v{
      font-weight: 900;
      text-align:right;
    }
    .totals tr + tr td{
      border-top:1px solid #e5e7eb;
    }
    @media print{
      .totals{ margin-top: 4mm; }
      .totals table{ width: 82mm; }
      .totals td{ font-size: 10pt; padding: 2mm 0; }
      .totals tr + tr td{ border-top:1px solid #9ca3af; }
      .totals td.k{ padding-right: 6mm; }
    }

    /* Footer note */
    .footer{
      margin-top: 18px;
      border:1px solid #9ca3af;
      padding: 14px;
      font-size: 13px;
      text-align:center;
      line-height: 1.35;
      color:#111;
      border-radius: 12px;
    }
    @media print{
      .footer{ margin-top: 6mm; padding: 4mm; font-size: 9pt; border-radius:0; }
    }

    /* Actions */
    .actions{
      margin-top: 18px;
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
      font-weight: 900;
      border-radius: 10px;
      font-size: 14px;
    }
    .btn.secondary{
      background:#fff;
      color:#111;
    }

    .alert{
      margin-top: 16px;
      border:1px solid #f59e0b;
      background:#fffbeb;
      padding: 10px 12px;
      font-weight: 800;
      color:#92400e;
      border-radius: 12px;
    }
    @media print{ .alert{ border-radius:0; margin-top:6mm; } }
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

  $descLines = array_values(array_filter([
    $label->ga_code ? ($label->ga_code . ' — ' . ($label->ga_name ?: '')) : ($label->ga_name ?: null),
    $label->ga_item_number ? ('GA Item No.: ' . $label->ga_item_number) : null,
    $label->ga_internal_number ? ('GA Internal No.: ' . $label->ga_internal_number) : null,
    $label->ri_code ? ('RI Code: ' . $label->ri_code . ' — ' . ($label->ri_name ?: '')) : ($label->ri_name ? ('RI: ' . $label->ri_name) : null),
    $label->ri_item_number ? ('RI Item No.: ' . $label->ri_item_number) : null,
    $label->ri_doc_number ? ('Goods receipt / Delivery note: ' . $label->ri_doc_number) : null,
  ]));

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

  <div class="table-scroll">
    <table class="items">
      <colgroup>
        <col class="col-item">
        <col class="col-desc">
        <col class="col-qty">
        <col class="col-um">
        <col class="col-unit">
        <col class="col-net">
      </colgroup>
      <thead>
        <tr>
          <th class="center">Item</th>
          <th>Material / Description</th>
          <th class="right">Quantity</th>
          <th class="center">UM</th>
          <th class="right">Unit Price</th>
          <th class="right">Net Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="center">10</td>
          <td>
            @foreach($descLines as $line)
              <span class="desc-line">{{ $line }}</span>
            @endforeach

            @if($label->note)
              <div class="desc-note"><strong>Note:</strong> {!! nl2br(e($label->note)) !!}</div>
            @endif
          </td>
          <td class="right">{{ ($qty === null || $qty === '') ? '—' : $qty }}</td>
          <td class="center">{{ $um }}</td>
          <td class="right">{{ $unitPriceDisplay }}</td>
          <td class="right">{{ $netAmountDisplay }}</td>
        </tr>
      </tbody>
    </table>
  </div>

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