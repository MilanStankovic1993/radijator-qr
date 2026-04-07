<!doctype html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pregled servisnih QR kodova</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Arial, Helvetica, sans-serif;
            background: #0f1115;
            color: #f8fafc;
        }

        .page {
            max-width: 1800px;
            margin: 0 auto;
            padding: 20px 24px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .title {
            font-size: 42px;
            font-weight: 700;
            line-height: 1.1;
        }

        .subtitle {
            color: #9ca3af;
            font-size: 15px;
            margin-top: 4px;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            border: 0;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-print {
            background: #f59e0b;
            color: #fff;
        }

        .btn-back {
            background: #2a2f3a;
            color: #fff;
        }

        .table-wrap {
            background: #161a22;
            border: 1px solid #2b3240;
            border-radius: 16px;
            overflow: auto;
        }

        table {
            width: max-content;
            min-width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #1c212b;
        }

        th,
        td {
            padding: 14px 14px;
            border-bottom: 1px solid #2b3240;
            vertical-align: middle;
            text-align: left;
            font-size: 14px;
            white-space: nowrap;
        }

        th {
            color: #f8fafc;
            font-size: 16px;
            font-weight: 700;
        }

        td {
            color: #e5e7eb;
            font-size: 30px;
            font-weight: 600;
        }

        .thumb {
            width: 84px;
            height: 84px;
            object-fit: cover;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #3a4252;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
        }

        .dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            display: inline-block;
        }

        .green {
            background: #22c55e;
        }

        .red {
            background: #ef4444;
        }

        .muted {
            color: #9ca3af;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            body {
                background: #fff;
                color: #000;
            }

            .page {
                max-width: 100%;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .table-wrap {
                border: none;
                border-radius: 0;
                background: #fff;
                overflow: visible;
            }

            thead {
                background: #f3f4f6 !important;
            }

            table {
                width: 100%;
                table-layout: fixed;
            }

            th,
            td {
                color: #000;
                border: 1px solid #d1d5db;
                font-size: 11px;
                padding: 6px;
                white-space: normal;
                word-break: break-word;
            }

            .thumb {
                width: 42px;
                height: 42px;
                border: 1px solid #ccc;
            }

            .status-badge {
                gap: 4px;
                font-size: 10px;
            }

            .dot {
                width: 8px;
                height: 8px;
            }

            .print-hide {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <div>
                <div class="title">Servis QR Kodovi</div>
                <div class="subtitle">Pregled označenih stavki: {{ $labels->count() }}</div>
            </div>

            <div class="actions">
                <a href="{{ url()->previous() }}" class="btn btn-back">Nazad</a>
                <button onclick="window.print()" class="btn btn-print">Print</button>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Slika</th>
                        <th>Status</th>
                        <th>Štampano</th>
                        <th>Datum</th>
                        <th class="print-hide">Br. narudžbenice dobavljača</th>
                        <th>Naziv</th>
                        <th>Tip kotla</th>
                        <th>Dimenzija</th>
                        <th>CODE PDM</th>
                        <th class="print-hide">Težina (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labels as $label)
                        <tr>
                            <td>{{ $label->id }}</td>
                            <td>
                                @if(!empty($label->picture_path))
                                    <img src="{{ asset('storage/' . $label->picture_path) }}" alt="Slika dela" class="thumb">
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge">
                                    <span class="dot {{ $label->disabled_at ? 'red' : 'green' }}"></span>
                                    {{ $label->disabled_at ? 'Neaktivan' : 'Aktivan' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge">
                                    <span class="dot {{ $label->printed_at ? 'green' : 'red' }}"></span>
                                    {{ $label->printed_at ? 'Da' : 'Ne' }}
                                </span>
                            </td>
                            <td>{{ optional($label->date)->format('d.m.Y') ?? '-' }}</td>
                            <td class="print-hide">{{ $label->supplier_order_number ?: '-' }}</td>
                            <td>{{ $label->name ?: '-' }}</td>
                            <td>{{ $label->boiler_type ?: '-' }}</td>
                            <td>{{ $label->dimension ?: '-' }}</td>
                            <td>{{ $label->code_pdm ?: '-' }}</td>
                            <td class="print-hide">{{ filled($label->weight) ? number_format((float) $label->weight, 2, ',', '.') . ' kg' : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
