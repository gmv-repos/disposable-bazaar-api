<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    const ORDER_PLACED = 'place_order';
    const ORDER_DELIVERED = 'order_has_been_delivered';
    const ORDER_CANCELLED = 'order_is_cancelled';

    protected $apiUrl;
    protected $token;

    protected $to;
    protected $templateName;
    protected $parameters = [];

    public function __construct()
    {
        $version = config('services.whatsapp.version');
        $phoneId = config('services.whatsapp.phone_id');

        $this->token = config('services.whatsapp.token');
        $this->apiUrl = "https://graph.facebook.com/{$version}/{$phoneId}/messages";
    }

    public function to($phone)
    {
        $this->to = $phone;
        return $this;
    }

    public function template($templateName)
    {
        $this->templateName = $templateName;
        return $this;
    }

    public function params(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function send()
    {
        try {

            $code = ($this->templateName == self::ORDER_CANCELLED)
                ? 'en' : 'en_US';

            $bodyParams = array_map(fn($param) => [
                'type' => 'text',
                'text' => $param
            ], $this->parameters);

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $this->to,
                'type' => 'template',
                'template' => [
                    'name' => $this->templateName,
                    'language' => ['code' => $code],
                    'components' => [[
                        'type' => 'body',
                        'parameters' => $bodyParams,
                    ]]
                ]
            ];

            $response = Http::withToken($this->token)->post($this->apiUrl, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'exception' => $e->getMessage()
            ];
        }
    }
}