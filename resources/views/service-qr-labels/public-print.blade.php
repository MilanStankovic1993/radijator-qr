<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $label->name ?: 'QR nalepnica' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        @page {
            size: 75mm 75mm;
            margin: 0;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 75mm !important;
            height: 75mm !important;
            min-width: 75mm !important;
            min-height: 75mm !important;
            background: #fff;
            overflow: hidden;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
        }

        body {
            position: relative;
        }

        .label {
            position: fixed;
            left: 0;
            top: 0;
            width: 75mm;
            height: 75mm;
            padding: 3mm;
            background: #fff;
        }

        .inner {
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-rows: 12mm 1fr 10mm;
            align-items: center;
            justify-items: center;
            padding: 2.5mm 2.5mm 2mm;
            border-radius: 2.5mm;
            border: 0.4mm solid #111;
            background: #fff;
        }

        .logo {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo img {
            display: block;
            max-width: 42mm;
            max-height: 10mm;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .qr-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-box {
            width: 42mm;
            height: 42mm;
            padding: 1mm;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .qr-box canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
            image-rendering: pixelated;
        }

        .name {
            width: 100%;
            text-align: center;
            font-size: 10pt;
            line-height: 1.15;
            font-weight: 700;
            padding: 0 2mm;
            word-break: break-word;
            overflow-wrap: anywhere;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .screen-print {
            display: none;
        }

        @media screen {
            html, body {
                width: auto !important;
                height: auto !important;
                min-width: 100vw !important;
                min-height: 100vh !important;
                background: #e5e7eb;
            }

            body {
                display: flex;
                align-items: flex-start;
                justify-content: center;
                padding: 20px !important;
            }

            .label {
                position: relative;
                left: auto;
                top: auto;
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            }

            .screen-print {
                display: block;
                margin-top: calc(75mm + 16px);
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
            }

            .screen-print button {
                border: 1px solid #111827;
                background: #111827;
                color: #fff;
                border-radius: 10px;
                padding: 10px 16px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
            }
        }

        @media print {
            .screen-print {
                display: none !important;
            }

            .label {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

@php
    $scanUrl = route('service-qr-labels.public.show', $label->token);
@endphp

<div class="label">
    <div class="inner">
        <div class="logo">
            <img
                src="{{ asset('images/logo-black.png') }}?v=1"
                alt="Radijator"
                onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}?v=1';"
            >
        </div>

        <div class="qr-wrap">
            <div class="qr-box">
                <div id="qrTop"></div>
            </div>
        </div>

        <div class="name">
            {{ $label->name ?: '—' }}
        </div>
    </div>
</div>

<div class="screen-print">
    <button type="button" onclick="window.print()">Print</button>
</div>

<script>
    (function () {
        const el = document.getElementById('qrTop');
        if (!el) return;

        const canvas = document.createElement('canvas');
        el.appendChild(canvas);

        QRCode.toCanvas(canvas, @json($scanUrl), {
            errorCorrectionLevel: 'H',
            width: 340,
            margin: 0,
        });
    })();
</script>

</body>
</html>