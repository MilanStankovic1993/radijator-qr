<!doctype html>
<html lang="sr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">

  <title>{{ $label->name ?: 'Servisni dokument' }} - {{ $label->token }}</title>

  <style>
    :root{
      --bg: #e8edf4;
      --paper: #ffffff;
      --ink: #0f172a;
      --muted: #475569;
      --line: #dbe2ea;
      --line-strong: #bac6d4;
      --accent: #0b3a6b;
      --accent-soft: #edf4fb;
      --warn-bg: #fffbeb;
      --warn-line: #f59e0b;
      --warn-ink: #92400e;
    }

    *{ box-sizing:border-box; }

    html, body{
      margin:0;
      padding:0;
      color:var(--ink);
      font-family:"Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background:
        radial-gradient(circle at 0% 0%, #f6f9fc 0, #eef3f8 42%, #e8edf4 100%);
    }

    body{
      min-height:100vh;
    }

    .sheet{
      width:min(1120px, calc(100vw - 32px));
      margin:18px auto;
      background:var(--paper);
      border:1px solid var(--line);
      border-radius:16px;
      padding:22px;
      box-shadow:0 14px 36px rgba(15, 23, 42, 0.10);
    }

    .header{
      display:grid;
      grid-template-columns:auto 1fr auto;
      gap:14px;
      align-items:center;
      border:1px solid var(--line);
      background:linear-gradient(180deg, #f9fbfd 0%, #f3f7fb 100%);
      border-radius:12px;
      padding:12px 14px;
      margin-bottom:16px;
    }

    .logo{
      width:54px;
      height:54px;
      border:1px solid var(--line);
      border-radius:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#fff;
      overflow:hidden;
      flex-shrink:0;
    }

    .logo img{
      width:42px;
      height:auto;
      object-fit:contain;
      display:block;
    }

    .title-wrap p{
      margin:0;
      line-height:1.2;
    }

    .title{
      font-size:24px;
      font-weight:900;
      letter-spacing:.2px;
    }

    .subtitle{
      margin-top:2px;
      font-size:12px;
      color:var(--muted);
      font-weight:700;
    }

    .doc-chip{
      text-align:right;
      border:1px solid var(--line-strong);
      border-radius:10px;
      padding:8px 10px;
      background:#fff;
      min-width:190px;
    }

    .doc-chip .k{
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:.45px;
      font-weight:800;
      color:var(--muted);
    }

    .doc-chip .v{
      margin-top:2px;
      font-size:20px;
      font-weight:900;
      overflow-wrap:anywhere;
    }

    .hero{
      display:grid;
      grid-template-columns:260px 1fr;
      gap:16px;
      margin-bottom:16px;
      align-items:stretch;
    }

    .image-card,
    .box{
      border:1px solid var(--line);
      border-radius:12px;
      padding:14px;
      background:#fff;
    }

    .image-wrap{
      width:100%;
      aspect-ratio:1 / 1;
      border:1px dashed var(--line-strong);
      border-radius:10px;
      overflow:hidden;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#f8fafc;
      color:var(--muted);
      font-size:13px;
      font-weight:700;
      text-align:center;
      padding:10px;
    }

    .image-wrap img{
      width:100%;
      height:100%;
      object-fit:contain;
      display:block;
    }

    .name{
      margin:0 0 10px;
      font-size:30px;
      font-weight:900;
      line-height:1.1;
      overflow-wrap:anywhere;
    }

    .meta-row{
      display:grid;
      grid-template-columns:repeat(3, 1fr);
      gap:10px;
    }

    .meta{
      border:1px solid var(--line);
      border-radius:10px;
      padding:10px;
      background:var(--accent-soft);
    }

    .meta .k{
      font-size:11px;
      font-weight:800;
      text-transform:uppercase;
      letter-spacing:.3px;
      color:#27476a;
      margin-bottom:4px;
    }

    .meta .v{
      font-size:15px;
      font-weight:900;
      overflow-wrap:anywhere;
    }

    .grid2{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:16px;
      margin-bottom:16px;
    }

    .box-title{
      margin:0 0 10px;
      font-size:13px;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.35px;
      color:#1e3a5f;
    }

    table{
      width:100%;
      border-collapse:collapse;
    }

    .kv td{
      padding:8px 0;
      border-bottom:1px solid #edf1f5;
      font-size:13px;
      vertical-align:top;
    }

    .kv tr:last-child td{
      border-bottom:none;
    }

    .kv td.k{
      width:40%;
      color:#334155;
      font-weight:800;
      padding-right:12px;
      white-space:nowrap;
    }

    .kv td.v{
      font-weight:600;
      overflow-wrap:anywhere;
    }

    .box-wide .kv td.k{
      width:30%;
    }

    .footer-note{
      margin-top:16px;
      border:1px solid var(--line-strong);
      border-radius:12px;
      padding:13px;
      white-space:pre-wrap;
      line-height:1.55;
      background:#f8fafc;
      font-size:14px;
    }

    .footer-note strong{
      display:block;
      margin-bottom:8px;
      text-transform:uppercase;
      letter-spacing:.35px;
      font-size:12px;
      color:#1e3a5f;
    }

    .actions{
      margin-top:16px;
      display:flex;
      gap:10px;
      justify-content:flex-end;
      flex-wrap:wrap;
    }

    .btn{
      display:inline-block;
      text-decoration:none;
      border:1px solid var(--accent);
      background:var(--accent);
      color:#fff;
      border-radius:10px;
      padding:10px 14px;
      font-weight:800;
      font-size:13px;
      cursor:pointer;
    }

    .btn.secondary{
      background:#fff;
      color:var(--accent);
    }

    .alert{
      margin-top:16px;
      border:1px solid var(--warn-line);
      background:var(--warn-bg);
      border-radius:10px;
      padding:10px 12px;
      font-weight:800;
      color:var(--warn-ink);
      font-size:13px;
    }

    @media (max-width: 980px){
      .header{
        grid-template-columns:auto 1fr;
      }

      .doc-chip{
        grid-column:1 / -1;
        text-align:left;
      }

      .hero{
        grid-template-columns:1fr;
      }

      .meta-row{
        grid-template-columns:1fr;
      }

      .grid2{
        grid-template-columns:1fr;
      }

      .box-wide .kv td.k{
        width:40%;
      }
    }

    @media (max-width: 620px){
      .sheet{
        width:calc(100vw - 18px);
        margin:9px auto;
        padding:12px;
        border-radius:12px;
      }

      .name{
        font-size:24px;
      }

      .actions .btn{
        width:100%;
        text-align:center;
      }
    }

    @media print{
      @page{
        size:A4;
        margin:10mm;
      }

      html, body{
        background:#fff !important;
        width:auto !important;
        height:auto !important;
        overflow:visible !important;
      }

      body{
        display:block !important;
        min-height:auto !important;
      }

      .sheet{
        width:auto !important;
        max-width:none !important;
        margin:0 !important;
        padding:0 !important;
        border:none !important;
        border-radius:0 !important;
        box-shadow:none !important;
      }

      .no-print{
        display:none !important;
      }

      .header,
      .image-card,
      .box,
      .footer-note,
      .alert,
      .meta,
      .doc-chip{
        border-radius:0 !important;
        box-shadow:none !important;
      }

      .header{
        padding:3mm !important;
        margin:0 0 4mm 0 !important;
      }

      .logo{
        width:14mm !important;
        height:14mm !important;
      }

      .logo img{
        width:10mm !important;
      }

      .title{
        font-size:16pt !important;
      }

      .subtitle{
        font-size:9pt !important;
      }

      .doc-chip .k{
        font-size:8pt !important;
      }

      .doc-chip .v{
        font-size:14pt !important;
      }

      .hero{
        display:grid !important;
        grid-template-columns:48mm 1fr !important;
        gap:4mm !important;
        margin-bottom:4mm !important;
      }

      .grid2{
        display:grid !important;
        grid-template-columns:1fr 1fr !important;
        gap:4mm !important;
        margin-bottom:4mm !important;
      }

      .image-card,
      .box{
        padding:3mm !important;
        break-inside:avoid;
        page-break-inside:avoid;
      }

      .image-wrap{
        aspect-ratio:auto !important;
        min-height:42mm !important;
        height:42mm !important;
        padding:2mm !important;
      }

      .name{
        font-size:16pt !important;
        margin-bottom:3mm !important;
      }

      .meta-row{
        display:grid !important;
        grid-template-columns:repeat(3, 1fr) !important;
        gap:2.5mm !important;
      }

      .meta{
        padding:2.5mm !important;
      }

      .meta .k{
        font-size:7.5pt !important;
      }

      .meta .v{
        font-size:10pt !important;
      }

      .box-title{
        font-size:9pt !important;
        margin:0 0 2mm 0 !important;
      }

      .kv td{
        font-size:9pt !important;
        padding:1.5mm 0 !important;
      }

      .kv td.k{
        width:38% !important;
        white-space:normal !important;
      }

      .box-wide .kv td.k{
        width:28% !important;
      }

      .footer-note{
        margin-top:4mm !important;
        padding:3mm !important;
        font-size:9pt !important;
        line-height:1.35 !important;
        break-inside:avoid;
        page-break-inside:avoid;
      }

      .footer-note strong{
        font-size:8pt !important;
        margin-bottom:2mm !important;
      }

      .alert{
        margin-top:4mm !important;
        padding:2.5mm 3mm !important;
        font-size:8.5pt !important;
      }
    }
  </style>
</head>
<body>

@php
  $dash = '-';
  $docNumber = $label->code_pdm ?: $label->token;

  $date = $label->date ? $label->date->format('d.m.Y') : $dash;
  $createdAt = $label->created_at ? $label->created_at->format('d.m.Y H:i') : $dash;
  $printedAt = $label->printed_at ? $label->printed_at->format('d.m.Y H:i') : $dash;

  $productInfo = [
    'Datum / Date' => $date,
    'Broj narudzbenice dobavljaca' => $label->supplier_order_number ?: $dash,
    'Naziv / Name' => $label->name ?: $dash,
    'Tip kotla / Type of boiler' => $label->boiler_type ?: $dash,
    'Dimenzija / Dimension' => $label->dimension ?: $dash,
    'CODE PDM' => $label->code_pdm ?: $dash,
    'Kupac / Buyer' => $label->buyer ?: $dash,
  ];

  $orderInfo = [
    'Tezina / Weight (kg)' => filled($label->weight) ? number_format((float) $label->weight, 2, ',', '.') . ' kg' : $dash,
    'Cena / Price (kom)' => filled($label->price) ? number_format((float) $label->price, 2, ',', '.') : $dash,
    'Kolicina / Quantity' => filled($label->quantity) ? number_format((float) $label->quantity, 2, ',', '.') : $dash,
    'Kreirano' => $createdAt,
    'Stampano' => $printedAt,
  ];

  $docInfo = [
    'Datum stampanja' => $printedAt,
    'Kreirao dokument' => optional($label->creator)->name ?: $dash,
    'Azurirao dokument' => optional($label->editor)->name ?: $dash,
  ];
@endphp

<div class="sheet">
  <div class="header">
    <div class="logo">
      <img src="{{ url('images/logo.png') }}" alt="Radijator">
    </div>

    <div class="title-wrap">
      <p class="title">Servisni QR dokument</p>
      <p class="subtitle">Radijator Inzenjering | 36000 Kraljevo, Serbia</p>
    </div>

    <div class="doc-chip">
      <div class="k">Broj dokumenta</div>
      <div class="v">{{ $docNumber }}</div>
    </div>
  </div>

  <div class="hero">
    <div class="image-card">
      <div class="image-wrap">
        @if($label->picture_path)
          <img src="{{ asset('storage/' . $label->picture_path) }}" alt="{{ $label->name }}">
        @else
          <div>Nema slike dela</div>
        @endif
      </div>
    </div>

    <div class="box">
      <h1 class="name">{{ $label->name ?: $dash }}</h1>

      <div class="meta-row">
        <div class="meta">
          <div class="k">CODE PDM</div>
          <div class="v">{{ $label->code_pdm ?: $dash }}</div>
        </div>

        <div class="meta">
          <div class="k">Kupac / Buyer</div>
          <div class="v">{{ $label->buyer ?: $dash }}</div>
        </div>

        <div class="meta">
          <div class="k">Token</div>
          <div class="v">{{ $label->token ?: $dash }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid2">
    <div class="box">
      <div class="box-title">Podaci o delu</div>
      <table class="kv">
        @foreach($productInfo as $k => $v)
          <tr>
            <td class="k">{{ $k }}</td>
            <td class="v">{{ ($v === '' || $v === null) ? $dash : $v }}</td>
          </tr>
        @endforeach
      </table>
    </div>

    <div class="box">
      <div class="box-title">Komercijalni podaci</div>
      <table class="kv">
        @foreach($orderInfo as $k => $v)
          <tr>
            <td class="k">{{ $k }}</td>
            <td class="v">{{ ($v === '' || $v === null) ? $dash : $v }}</td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>

  <div class="box box-wide">
    <div class="box-title">Informacije o dokumentu</div>
    <table class="kv">
      @foreach($docInfo as $k => $v)
        <tr>
          <td class="k">{{ $k }}</td>
          <td class="v">{{ ($v === '' || $v === null) ? $dash : $v }}</td>
        </tr>
      @endforeach
    </table>
  </div>

  <div class="footer-note">
    <strong>Napomena / Disc</strong>
    {{ $label->note ?: $dash }}
  </div>

  <div class="actions no-print">
    <button class="btn" type="button" onclick="window.print()">Štampaj dokument</button>
    <a class="btn secondary" href="{{ url('/') }}">Home</a>
  </div>

  @if($label->disabled_at)
    <div class="alert">
      Ova etiketa je deaktivirana i prikaz je samo informativan.
    </div>
  @endif
</div>

</body>
</html>