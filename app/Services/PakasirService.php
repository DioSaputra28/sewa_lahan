<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PakasirService
{
    public function paymentUrl(Invoice $invoice): string
    {
        $redirectUrl = $this->paymentRedirectUrl();

        return $this->baseUrl()
            .'/pay/'.rawurlencode($this->projectSlug())
            .'/'.$invoice->total_amount
            .'?'.http_build_query([
                'order_id' => $invoice->invoice_number,
                'redirect' => $redirectUrl,
            ]);
    }

    public function paymentRedirectUrl(): string
    {
        return route('filament.user.pages.dashboard');
    }

    public function createTransaction(Invoice $invoice, string $method): array
    {
        $response = $this->request()
            ->post($this->baseUrl().'/api/transactioncreate/'.$method, $this->transactionPayload($invoice));

        return $this->parseJsonResponse($response, 'Gagal membuat transaksi Pakasir.');
    }

    public function getTransactionDetail(Invoice $invoice): array
    {
        $response = $this->request()
            ->get($this->baseUrl().'/api/transactiondetail', $this->transactionPayload($invoice));

        return $this->parseJsonResponse($response, 'Gagal mengambil detail transaksi Pakasir.');
    }

    public function cancelTransaction(Invoice $invoice): array
    {
        $response = $this->request()
            ->post($this->baseUrl().'/api/transactioncancel', $this->transactionPayload($invoice));

        return $this->parseJsonResponse($response, 'Gagal membatalkan transaksi Pakasir.');
    }

    public function simulatePayment(Invoice $invoice): array
    {
        $response = $this->request()
            ->post($this->baseUrl().'/api/paymentsimulation', $this->transactionPayload($invoice));

        return $this->parseJsonResponse($response, 'Gagal menjalankan simulasi pembayaran Pakasir.');
    }

    public function methods(): array
    {
        return config('services.pakasir.methods', []);
    }

    public function projectSlug(): string
    {
        return (string) config('services.pakasir.project_slug');
    }

    public function isSandbox(): bool
    {
        return (bool) config('services.pakasir.sandbox', true);
    }

    protected function request()
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout(10)
            ->connectTimeout(5)
            ->retry(2, 200, throw: false);
    }

    protected function transactionPayload(Invoice $invoice): array
    {
        return [
            'project' => $this->projectSlug(),
            'order_id' => $invoice->invoice_number,
            'amount' => $invoice->total_amount,
            'api_key' => (string) config('services.pakasir.api_key'),
        ];
    }

    protected function parseJsonResponse(Response $response, string $message): array
    {
        if ($response->failed()) {
            throw new RuntimeException($message);
        }

        $json = $response->json();

        if (! is_array($json)) {
            throw new RuntimeException($message);
        }

        return $json;
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('services.pakasir.base_url', 'https://app.pakasir.com'), '/');
    }
}
