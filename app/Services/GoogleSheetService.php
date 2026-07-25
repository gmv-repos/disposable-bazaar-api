<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    protected Sheets $service;
    protected string $spreadsheetId;
    protected string $sheetName = 'Sheet1';

    protected array $headers = [];
    protected array $rows = [];

    protected ?int $rowNumber = null;

    protected array $newOrderData = [];
    protected array $rowUpdates = [];

    public function __construct()
    {
        $this->spreadsheetId = env('ORDERS_GOOGLE_SHEET_ID');

        if (!$this->spreadsheetId) {
            throw new \Exception('Google Sheet ID is not set.');
        }

        $client = new Client();
        $client->setAuthConfig(storage_path(env('GOOGLE_SERVICE_ACCOUNT_CREDENTIALS')));
        $client->addScope(Sheets::SPREADSHEETS);

        $this->service = new Sheets($client);
    }

    /* ===================== ADD ORDER ===================== */

    public static function addOrder($order): self
    {
        $instance = new self();
        $instance->loadSheet();

        $nextSNo = count($instance->rows) + 1;

        $instance->newOrderData = [
            'S. No'        => $nextSNo,
            'Date'         => (string) ($order->order_date ?? ''),
            'Order No.'    => (string) ($order->order_no ?? ''),
            'Name'         => (string) ($order->name ?? ''),
            'Shipping'     => (string) ($order->shipping_charges ?? ''),
            'Subtotal'     => (string) ($order->total_amount ?? ''),
            'Total'        => (string) ($order->grand_total ?? ''),
            'Order Status' => 'Pending',
            'Area'         => '',
            'Rider'        => '',
            'Delivery Day' => '',
            'Payment'      => '',
        ];

        return $instance;
    }

    /* ===================== UPDATE ORDER ===================== */

    public static function updateOrder($orderNo, string $orderNoHeader = 'Order No.'): self
    {
        $instance = new self();
        $instance->loadSheet();

        $colIndex = array_search($orderNoHeader, $instance->headers);

        if ($colIndex === false) {
            throw new \Exception("Header '{$orderNoHeader}' not found.");
        }

        foreach ($instance->rows as $index => $row) {
            if (($row[$colIndex] ?? null) == $orderNo) {
                $instance->rowNumber = $index + 2; // Header is row 1
                return $instance;
            }
        }

        throw new \Exception("Order '{$orderNo}' not found in Google Sheet.");
    }

    /* ===================== SET FIELD ===================== */

    public function set(string $header, $value): self
    {
        if (!in_array($header, $this->headers)) {
            throw new \Exception("Header '{$header}' does not exist.");
        }

        if (!empty($this->newOrderData)) {
            $this->newOrderData[$header] = $value;
        } elseif ($this->rowNumber !== null) {
            $this->rowUpdates[$header] = $value;
        } else {
            throw new \Exception('No order selected.');
        }

        return $this;
    }

    /* ===================== SAVE ===================== */

    public function save(): void
    {
        // ADD ORDER
        if (!empty($this->newOrderData)) {
            $row = [];

            foreach ($this->headers as $header) {
                $row[] = $this->newOrderData[$header] ?? '';
            }

            $body = new ValueRange([
                'values' => [$row]
            ]);

            $this->service->spreadsheets_values->append(
                $this->spreadsheetId,
                "{$this->sheetName}!A1",
                $body,
                [
                    'valueInputOption' => 'RAW',
                    'insertDataOption' => 'INSERT_ROWS',
                ]
            );

            return;
        }

        if ($this->rowNumber !== null && !empty($this->rowUpdates)) {

            $existingRow = $this->rows[$this->rowNumber - 2] ?? [];

            foreach ($this->headers as $index => $header) {
                if (array_key_exists($header, $this->rowUpdates)) {
                    $existingRow[$index] = $this->rowUpdates[$header];
                }
            }

            $range = "{$this->sheetName}!A{$this->rowNumber}";
            $body = new ValueRange(['values' => [$existingRow]]);

            $this->service->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }
    }

    /* ===================== LOAD SHEET ===================== */

    protected function loadSheet(): void
    {
        $response = $this->service->spreadsheets_values->get(
            $this->spreadsheetId,
            "{$this->sheetName}!A1:Z"
        );

        $values = $response->getValues() ?? [];

        if (empty($values)) {
            throw new \Exception('Sheet is empty.');
        }

        $this->headers = $values[0];
        $this->rows = array_slice($values, 1);
    }
}
