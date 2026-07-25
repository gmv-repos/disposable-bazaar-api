<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SendSMSCommand extends Command
{
    protected $signature = 'sms:send-sms';
    protected $description = 'Send SMS to customers';

    public function handle()
    {
        $this->sendSMS();
    }

    private function sendSMS()
    {
        // Define the API endpoint and parameters

        $smsDetails = DB::table('sms_details as sd')->select('sd.*')->where('sd.status', 1)->get();

        foreach ($smsDetails as $sdRow) {
            $username = 'DisposableSMS';
            $apiId = 'ehM9X4vL';
            $source = 'Disp Bazaar';
            $campaignName = 'SMS API';
            $accessToken = 'L[coo$I:Z9x:hspP+D3Pg#/5VA2qsHRv';

            $destination = $sdRow->phone_number; // Destination number

            // Validate the phone number format (e.g., 923000000000)
            if (!preg_match('/^92[0-9]{10}$/', $destination)) {
                $this->error("Invalid phone number format: {$destination}");
                // Optionally, update the status to indicate invalid number
                DB::table('sms_details')
                    ->where('id', $sdRow->id)
                    ->update(['status' => 4]); // 4 for invalid number
                continue; // Skip to the next SMS
            }

            $text = $sdRow->message;

            // Build the complete URL with query parameters
            $url = sprintf(
                'https://sms.montymobile.com/API/SendSMS?username=%s&apiId=%s&json=True&destination=%s&source=%s&campaignname=%s&text=%s',
                urlencode($username),
                urlencode($apiId),
                urlencode($destination),
                urlencode($source),
                urlencode($campaignName),
                urlencode($text),
            );

            // Send the request
            $response = Http::withHeaders([
                'X-Access-Token' => $accessToken,
            ])->post($url);

            // Log the response and update status
            if ($response->successful()) {
                DB::table('sms_details')
                    ->where('id', $sdRow->id)
                    ->update(['status' => 2]);
                $this->info("SMS sent  {$destination}");
            } else {
                $this->error("Failed to send SMS at {$destination}. Error: " . $response->body());
                // Optionally, you can update the status to indicate failure
                DB::table('sms_details')
                    ->where('id', $sdRow->id)
                    ->update(['status' => 3]); // 3 for failed
            }
        }
    }
}
