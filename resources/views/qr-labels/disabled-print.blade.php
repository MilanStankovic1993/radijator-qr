<!doctype html>
<html lang="sr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Etiketa deaktivirana</title>

  <style>
    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; color:#111; }
    @page { size: A4; margin: 10mm; }

    .sheet{
      width: 190mm;
      min-height: 277mm;
      padding: 10mm;
    }

    .head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      border-bottom: 2px solid #111;
      padding-bottom: 6mm;
      margin-bottom: 10mm;
    }
    .head img{ width: 55mm; height:auto; }

    h1{ font-size: 18pt; margin:0 0 4mm; font-weight:900; }
    p{ font-size: 12pt; margin:0; line-height:1.45; }

    .box{
      border:2px solid #111;
      padding: 10mm;
      margin-top: 12mm;
    }

    .muted{ color:#444; font-size: 10.5pt; margin-top: 6mm; }
    .no-print{ margin-top: 14px; }
    @media print{ .no-print{ display:none !important; } }
    .btn{
      border:1px solid #111;
      background:#111;
      color:#fff;
      padding:10px 14px;
      border-radius:10px;
      font-weight:800;
      cursor:pointer;
    }
  </style>
</head>
<body>
  <div class="sheet">
    <div class="head">
      <div>
        <strong>Radijator Inženjering</strong><br>
        36000 Kraljevo, Serbia
      </div>
      <img src="{{ asset('images/logo.png') }}?v=1" alt="Radijator">
    </div>

    <div class="box">
      <h1>Ova etiketa je deaktivirana.</h1>
      <p>Dokument nije dostupan za pregled i štampu.</p>

      <div class="muted">
        Token: <strong>{{ $label->token }}</strong>
        @if($label->disabled_at)
          • Deaktivirano: <strong>{{ $label->disabled_at->format('d.m.Y H:i') }}</strong>
        @endif
      </div>
    </div>

    <div class="no-print">
      <button class="btn" type="button" onclick="window.print()">Print</button>
    </div>
  </div>
</body>
</html>