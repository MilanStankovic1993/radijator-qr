<?php

namespace App\Http\Controllers;

use App\Models\ServiceQrLabel;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ServiceQrLabelPublicController extends Controller
{
    private const ZEBRA_IP = '10.0.6.123';
    private const ZEBRA_PORT = 9100;
    private const SOCKET_TIMEOUT = 5;

    public function show(string $token)
    {
        $label = ServiceQrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        if ($label->isDisabled()) {
            return response()
                ->view('service-qr-labels.disabled', compact('label'))
                ->setStatusCode(Response::HTTP_OK);
        }

        return view('service-qr-labels.public-show', compact('label'));
    }

    public function print(string $token)
    {
        $label = ServiceQrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        if ($label->isDisabled()) {
            return response()
                ->view('service-qr-labels.disabled-print', compact('label'))
                ->setStatusCode(Response::HTTP_OK);
        }

        if (! $label->isPrinted()) {
            $label->markAsPrinted();
        }

        return view('service-qr-labels.public-print', compact('label'));
    }

    public function zpl(string $token)
    {
        $label = ServiceQrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_if($label->isDisabled(), Response::HTTP_NOT_FOUND);

        if (! $label->isPrinted()) {
            $label->markAsPrinted();
        }

        $zpl = $this->buildZpl($label);

        return response($zpl, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'inline; filename="service-label-' . $label->token . '.zpl"',
        ]);
    }

    public function printDirect(string $token)
    {
        $label = ServiceQrLabel::query()
            ->where('token', $token)
            ->firstOrFail();

        abort_if($label->isDisabled(), Response::HTTP_NOT_FOUND);

        $result = $this->sendToPrinter($label);

        return response()->json($result, $result['ok'] ? 200 : 500);
    }

    public function sendToPrinter(ServiceQrLabel $label): array
    {
        $zpl = $this->buildZpl($label);

        $errorNumber = 0;
        $errorMessage = '';

        $socket = @fsockopen(
            self::ZEBRA_IP,
            self::ZEBRA_PORT,
            $errorNumber,
            $errorMessage,
            self::SOCKET_TIMEOUT
        );

        if (! $socket) {
            return [
                'ok' => false,
                'message' => 'Ne mogu da se povezem na Zebra stampac.',
                'printer_ip' => self::ZEBRA_IP,
                'printer_port' => self::ZEBRA_PORT,
                'error' => trim($errorMessage) !== '' ? $errorMessage : ('Socket error #' . $errorNumber),
            ];
        }

        stream_set_timeout($socket, self::SOCKET_TIMEOUT);

        $written = fwrite($socket, $zpl);
        fflush($socket);
        fclose($socket);

        if ($written === false || $written <= 0) {
            return [
                'ok' => false,
                'message' => 'ZPL nije uspesno poslat stampacu.',
                'printer_ip' => self::ZEBRA_IP,
                'printer_port' => self::ZEBRA_PORT,
            ];
        }

        if (! $label->isPrinted()) {
            $label->markAsPrinted();
        }

        return [
            'ok' => true,
            'message' => 'Etiketa je poslata na Zebra stampac.',
            'printer_ip' => self::ZEBRA_IP,
            'printer_port' => self::ZEBRA_PORT,
            'bytes_sent' => $written,
            'token' => $label->token,
        ];
    }

    private function buildZpl(ServiceQrLabel $label): string
    {
        $scanUrl = route('service-qr-labels.public.show', $label->token);

        $companyLine = $this->zplSafeText('RADIJATOR INZENJERING DOO');
        $nameLine = $this->zplSafeText(
            Str::limit(
                trim((string) ($label->name ?: 'DEO')),
                64,
                ''
            )
        );
        $codeLine = $this->zplSafeText('CODE PDM: ' . ($label->code_pdm ?: '-'));
        $metaLine = $this->zplSafeText(collect([
            filled($label->dimension) ? ('DIM: ' . $label->dimension) : null,
            filled($label->boiler_type) ? ('TIP: ' . $label->boiler_type) : null,
        ])->filter()->implode(' | '));
        $buyerLine = $this->zplSafeText('KUPAC: ' . ($label->buyer ?: '-'));
        $tokenLine = $this->zplSafeText($label->token);
        $urlLine = $this->zplSafeText($scanUrl);

        // 75mm x 75mm @ 203 dpi is approximately 600 x 600 dots.
        return implode("\n", [
            '^XA',
            '^CI28',
            '^PW600',
            '^LL600',
            '^LH0,0',
            '^LS0',

            '^FO8,8^GB584,584,2^FS',

            '^FO26,22^A0N,24,24^FB548,1,0,C,0^FD' . $companyLine . '^FS',
            '^FO26,52^A0N,22,22^FB548,1,0,C,0^FD' . $codeLine . '^FS',
            '^FO26,80^A0N,20,20^FB548,1,0,C,0^FD' . $metaLine . '^FS',

            '^FO152,118^BQN,2,7^FDLA,' . $urlLine . '^FS',

            '^FO34,422^A0N,34,34^FB532,2,6,C,0^FD' . $nameLine . '^FS',
            '^FO34,510^A0N,22,22^FB532,1,0,C,0^FD' . $buyerLine . '^FS',
            // '^FO34,540^A0N,18,18^FB532,1,0,C,0^FDTKN: ' . $tokenLine . '^FS',

            '^XZ',
        ]);
    }

    private function zplSafeText(?string $value): string
    {
        $text = trim((string) $value);

        if ($text === '') {
            return '-';
        }

        $text = Str::ascii($text);

        return str_replace(
            ['\\', '^', '~', "\r", "\n"],
            ['/', '-', '-', ' ', ' '],
            $text
        );
    }
}