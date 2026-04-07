<!doctype html>
<html lang="sr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>QR etiketa deaktivirana</title>

  <style>
    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background:#f3f4f6; color:#111; }
    .wrap{ min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
    .card{
      width: min(680px, 100%);
      background:#fff;
      border:1px solid #e5e7eb;
      border-radius:16px;
      padding:24px;
      box-shadow: 0 10px 25px rgba(0,0,0,.08);
      text-align:center;
    }
    .logo{ margin-bottom:14px; }
    .logo img{ width:220px; max-width:70vw; height:auto; }
    h1{ margin:0 0 10px; font-size:20px; font-weight:900; }
    p{ margin:0; font-size:14px; color:#374151; line-height:1.5; }
    .meta{ margin-top:14px; font-size:12px; color:#6b7280; }
    .btns{ margin-top:18px; display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
    .btn{
      display:inline-block;
      padding:10px 14px;
      border-radius:10px;
      border:1px solid #111;
      font-weight:800;
      text-decoration:none;
      color:#111;
      background:#fff;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="logo">
        <img src="{{ asset('images/logo.png') }}?v=1" alt="Radijator">
      </div>

      <h1>Ova QR etiketa je deaktivirana.</h1>
      <p>Dokument više nije dostupan za pregled i štampu.</p>

      <div class="meta">
        Token: <strong>{{ $label->token }}</strong>
        @if($label->disabled_at)
          • Deaktivirano: <strong>{{ $label->disabled_at->format('d.m.Y H:i') }}</strong>
        @endif
      </div>

      <div class="btns">
        <a class="btn" href="{{ url('/') }}">Početna</a>
      </div>
    </div>
  </div>
</body>
</html>