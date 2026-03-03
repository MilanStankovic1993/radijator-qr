<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Receiving document {{ $label->token }}</title>

  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

  <style>
    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; background:#fff; color:#000; font-family: Arial, Helvetica, sans-serif; }

    @page { size: A4; margin: 10mm; }

    body{
      background:#f2f4f7;
      padding: 18px;
      display:flex;
      justify-content:center;
      align-items:flex-start;
    }

    .sheet{
      background:#fff;
      border:1px solid #e5e7eb;
      border-radius:14px;
      box-shadow: 0 10px 25px rgba(0,0,0,.08);
      padding: 18px;
    }

    .page{
      width: 190mm;
      min-height: 277mm;
      background:#fff;
    }

    .muted{ color:#444; }
    .mono{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }

    .doc-header{
      display:flex;
      align-items:flex-end;
      justify-content:space-between;
      margin-top: 1mm;
      margin-bottom: 3mm;
      gap: 10px;
    }
    .doc-header .left{
      font-size: 13px;
      font-weight: 900;
      letter-spacing: .2px;
    }
    .doc-header .right{
      font-size: 10.5px;
      font-weight: 700;
      color:#333;
    }

    .line-strong{
      border-top: 2px solid #111;
      margin: 6mm 0 4mm;
    }
    .line-thin{
      border-top: 1px solid #111;
      margin: 3.5mm 0 3.5mm;
    }

    .head{
      display:grid;
      grid-template-columns: 26mm 1fr 62mm;
      gap: 8mm;
      align-items:start;
    }

    .qr-s{
      width:26mm; height:26mm;
      border:1px solid #111;
      padding:2mm;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .qr-s canvas{
      width:100% !important;
      height:100% !important;
      display:block;
      image-rendering: pixelated;
    }

    .h-title{
      font-size: 15px;
      font-weight: 800;
      margin: 0 0 2mm 0;
    }

    .top-meta{
      display:grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 6mm;
      margin-top: 1mm;
    }

    .kv .k{
      font-size:10px;
      color:#111;
      margin-bottom: 0.5mm;
      font-weight:700;
    }
    .kv .v{
      font-size:12px;
      font-weight:800;
      word-break: break-word;
    }

    .brand{
      display:flex;
      justify-content:flex-end;
      align-items:flex-start;
    }
    .brand .wrap{
      text-align:right;
      display:flex;
      flex-direction:column;
      align-items:flex-end;
      gap: 2mm;
    }
    .brand img{
      width: 52mm;
      height:auto;
      object-fit:contain;
      display:block;
    }
    .brand .created{
      font-size: 10.5px;
      line-height: 1.2;
      color:#111;
    }
    .brand .created strong{
      font-weight:900;
    }

    .mid-row{
      display:grid;
      grid-template-columns: 56mm 56mm 1fr; /* 3 cols now (removed token col) */
      gap: 6mm;
      align-items:start;
      font-size: 11px;
    }

    .mid-cell .k{
      font-weight:700;
      margin-bottom: 0.6mm;
    }
    .mid-cell .v{
      font-weight:800;
      font-size: 12px;
      word-break: break-word;
    }
    .mid-cell .v.smallv{
      font-weight:700;
      font-size: 11px;
      line-height: 1.35;
    }

    .footer{
      margin-top: 14mm;
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 22mm;
      font-size: 12px;
      align-items:end;
    }
    .sig{
      display:flex;
      align-items:flex-end;
      gap: 6mm;
    }
    .sig .line{
      flex: 1;
      border-bottom: 2px solid #111;
      height: 0;
      margin-bottom: 2px;
    }

    .no-print{
      margin-top: 12px;
      display:flex;
      gap:10px;
      justify-content:flex-end;
      flex-wrap: wrap;
    }
    .btn{
      border:1px solid #e5e7eb;
      background:#fff;
      border-radius:10px;
      padding:10px 14px;
      font-weight:700;
      cursor:pointer;
    }
    .btn-primary{
      background:#111827;
      color:#fff;
      border-color:#111827;
    }

    /* ✅ SAMO ZA TELEFON (SCREEN). Ne dira print. */
    @media screen and (max-width: 720px){
      body{ padding: 12px; }

      /* da stane lepo na ekran */
      .sheet{
        width: calc(100vw - 24px);
        padding: 14px;
      }

      /* na telefonu ne forsiramo mm A4, nego fluid */
      .page{
        width: auto;
        min-height: auto;
      }

      .doc-header{
        flex-direction: column;
        align-items:flex-start;
        gap: 6px;
        margin-bottom: 10px;
      }
      .doc-header .left{
        font-size: 14px;
      }
      .doc-header .right{
        font-size: 12px;
      }

      .head{
        grid-template-columns: 1fr;
        gap: 12px;
      }

      .qr-s{
        width: 96px;
        height: 96px;
        padding: 8px;
      }

      .brand{
        justify-content:flex-start;
      }
      .brand .wrap{
        text-align:left;
        align-items:flex-start;
      }
      .brand img{
        width: min(240px, 70vw);
      }

      .top-meta{
        grid-template-columns: 1fr;
        gap: 10px;
      }

      .mid-row{
        grid-template-columns: 1fr;
        gap: 10px;
      }

      .footer{
        margin-top: 18px;
        grid-template-columns: 1fr;
        gap: 14px;
      }

      .no-print{
        justify-content: stretch;
      }
      .btn{
        width: 100%;
        text-align:center;
      }
    }

    /* ✅ PRINT OSTAVLJAMO KAKO JE BILO (tvoj dobar print) */
    @media print {
      body{ background:#fff; padding:0; display:block; }

      /* bitno: ako je .sheet dobio width na mobilnom, ovde ga “resetujemo” */
      .sheet{ width:auto; border:0; box-shadow:none; padding:0; border-radius:0; }

      .page{ width:auto; min-height:auto; }
      .no-print{ display:none !important; }
      @page { size: A4; margin: 10mm; }
    }
  </style>
</head>
<body>

@php
  $scanUrl = route('qr-labels.public.show', $label->token);

  // Common
  $storage   = trim((string) ($label->storage_location ?? ''));
  $orderType = trim((string) ($label->order_type ?? ''));
  $loadDate  = $label->load_date ? $label->load_date->format('d.m.Y') : '';
  $po        = trim((string) ($label->po_number ?? ''));
  $qty       = $label->quantity !== null ? (string) $label->quantity : '';
  $price     = $label->price !== null ? (string) $label->price : '';
  $note      = trim((string) ($label->note ?? ''));

  // Codes (their / ours)
  $codeTheir = trim((string) ($label->ga_code ?? ''));
  $codeOurs  = trim((string) ($label->ri_code ?? ''));

  // Names (their / ours)
  $nameTheir = trim((string) ($label->ga_name ?? ''));
  $nameOurs  = trim((string) ($label->ri_name ?? ''));

  // Item numbers (their / ours)
  $itemTheir = trim((string) ($label->ga_item_number ?? ''));
  $itemOurs  = trim((string) ($label->ri_item_number ?? ''));

  // Header title = prefer "their name / our name"
  $headerTitle = trim($nameTheir . ($nameTheir !== '' && $nameOurs !== '' ? ' / ' : '') . $nameOurs);

  $created = $label->created_at ? $label->created_at->format('d.m.y') : '';
@endphp

<div class="sheet">
  <div class="page">

    <div class="doc-header">
      <div class="left">Radijator Engineering • Kraljevo</div>
      <div class="right muted">Receiving document</div>
    </div>

    <div class="head">
      <div class="qr-s"><div id="qrTop"></div></div>

      <div>
        <div class="h-title">
          {{ $headerTitle !== '' ? $headerTitle : 'Receiving note 1 / 1' }}
        </div>

        <div class="top-meta">
          <div class="kv">
            <div class="k">Purchase Order</div>
            <div class="v">{{ $po !== '' ? $po : '—' }}</div>
          </div>
          <div class="kv">
            <div class="k">Loading date</div>
            <div class="v">{{ $loadDate !== '' ? $loadDate : '—' }}</div>
          </div>
          <div class="kv">
            <div class="k">Quantity</div>
            <div class="v">{{ $qty !== '' ? $qty : '—' }}</div>
          </div>
        </div>

        <div class="top-meta" style="margin-top:2.2mm;">
          <div class="kv">
            <div class="k">Receiving / storage location</div>
            <div class="v">{{ $storage !== '' ? $storage : '—' }}</div>
          </div>
          <div class="kv">
            <div class="k">Order type</div>
            <div class="v">{{ $orderType !== '' ? $orderType : '—' }}</div>
          </div>
          <div class="kv">
            <div class="k">Price</div>
            <div class="v">{{ $price !== '' ? $price : '—' }}</div>
          </div>
        </div>
      </div>

      <div class="brand">
        <div class="wrap">
          <img src="{{ asset('images/logo.png') }}?v=1" alt="Radijator">
          <div class="created">Created: <strong>{{ $created !== '' ? $created : '—' }}</strong></div>
        </div>
      </div>
    </div>

    <div class="line-strong"></div>

    <div class="mid-row">
      <div class="mid-cell">
        <div class="k">Item No. (Group Atlantic / Radijator)</div>
        <div class="v">
          {{ $itemTheir !== '' ? $itemTheir : '—' }}
          @if($itemOurs !== '')
            <span class="muted"> / </span>{{ $itemOurs }}
          @endif
        </div>
      </div>

      <div class="mid-cell">
        <div class="k">Code (Group Atlantic / Radijator)</div>
        <div class="v">
          {{ $codeTheir !== '' ? $codeTheir : '—' }}
          @if($codeOurs !== '')
            <span class="muted"> / </span>{{ $codeOurs }}
          @endif
        </div>
      </div>

      <div class="mid-cell">
        <div class="k">Note</div>
        <div class="v smallv">{{ $note !== '' ? \Illuminate\Support\Str::limit($note, 120) : '—' }}</div>
      </div>
    </div>

    <div class="line-thin"></div>

    <div class="footer">
      <div class="sig">
        <div><em>Received / handed over:</em></div>
        <div class="line"></div>
      </div>
      <div class="sig">
        <div><em>Date:</em></div>
        <div class="line"></div>
      </div>
    </div>

  </div>

  <div class="no-print">
    <button class="btn" type="button" onclick="history.back()">Back</button>
    <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
  </div>
</div>

<script>
  (function () {
    const scanUrl = @json($scanUrl);

    function render(elId, text, minPx) {
      const el = document.getElementById(elId);
      if (!el) return;
      const parent = el.parentElement;
      const innerPx = Math.max(minPx || 180, Math.floor(parent.clientWidth - 6));
      const canvas = document.createElement('canvas');
      el.innerHTML = '';
      el.appendChild(canvas);
      QRCode.toCanvas(canvas, text || '-', {
        errorCorrectionLevel: 'L',
        width: innerPx,
        margin: 0,
      });
    }

    render('qrTop', scanUrl, 180);
  })();
</script>

</body>
</html>