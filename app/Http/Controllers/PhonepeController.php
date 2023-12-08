<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class PhonepeController extends Controller
{
    /**
     * initiate
     * @return Redirect
     *
     */
    public function initiate()
    {
        try {
            // PhonePe Payment Gateway
            $data = [
                "merchantId" => "PGTESTPAYUAT",
                "merchantTransactionId" => (string) Str::uuid(),
                "merchantUserId" => (string) Str::uuid(),
                "amount" => 100 * 100,
                "redirectUrl" => 'http://127.0.0.1:8000/phonepe/success-payment',
                "callbackUrl" => 'http://127.0.0.1:8000/phonepe/success-payment',
                "redirectMode" => "REDIRECT",
                "mobileNumber" => '9999999999',
                "paymentInstrument" => [
                    "type" => "PAY_PAGE",
                ],
            ];
            $encode = base64_encode(json_encode($data));
            $saltkey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
            $saltIndex = 1;
            $string = $encode . '/pg/v1/pay' . $saltkey;
            $sha256 = hash('sha256', $string);
            $finalXHeader = $sha256 . '###' . $saltIndex;

            $client = new Client();
            $response = $client->post('https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $finalXHeader,
                ],
                'json' => ['request' => $encode],
            ]);

            $rData = json_decode($response->getBody()->getContents(), true);
            $url = $rData['data']['instrumentResponse']['redirectInfo']['url'];

            return redirect()->to($url);
        } catch (Exception $e) {
            Log::error("Error in initiate function", [$e->getMessage()]);
        }
    }

    /**
     * initiate
     * @param Request 
     * @return Redirect
     *
     */
    public function successPayment(Request $request)
    {
        try {
            $client = new Client();
            $saltkey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
            $saltIndex = 1;
 
            $finalXHeader1 = hash('sha256', '/pg/v1/status/'. 'PGTESTPAYUAT'.'/'.'2c6f28ba-da40-4d90-b109-452acc06477c'.$saltkey) .'###' . $saltIndex;
            $status = $client->get('https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/status/'. 'PGTESTPAYUAT'.'/'.'2c6f28ba-da40-4d90-b109-452acc06477c', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $finalXHeader1,
                    'X-MERCHANT-ID' => '2c6f28ba-da40-4d90-b109-452acc06477c',
                    'accept' => 'application/json',
                ]
            ]);
            dd(json_decode($status->getBody()->getContents(), true));
 
        } catch (Exception $e) {
            Log::error("Error in successPayment function", [$e->getMessage()]);
        }
    }


    /**
     * initiate
     * @return Redirect
     *
     */
    public function refundPayment()
    {
        try {
            // Phonepe Refund

            $data = [
                "merchantId" => "PGTESTPAYUAT",
                "merchantTransactionId" => (string) Str::uuid(),
                "originalTransactionId" => '9ebd7d07-b622-4f1e-a0a9-84de4b2ba105',
                "amount" => 100,
            ];
            $encode = base64_encode(json_encode($data));
            $saltkey = '099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
            $saltIndex = 1;
            $string = $encode . '/pg/v1/refund' . $saltkey;
            $sha256 = hash('sha256', $string);
            $finalXHeader = $sha256 . '###' . $saltIndex;
    
            $client = new Client();
            $response = $client->post('https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/refund', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $finalXHeader,
                ],
                'json' => ['request' => $encode],
            ]);
    
            $rData = json_decode($response->getBody()->getContents(), true);
 
            $finalXHeader1 = hash('sha256', '/pg/v1/status/'. $rData['data']['merchantId'].'/'.$rData['data']['merchantTransactionId'].$saltkey) .'###' . $saltIndex;
            $status = $client->get('https://api-preprod.phonepe.com/apis/merchant-simulator/pg/v1/status/'.$rData['data']['merchantId'].'/'.$rData['data']['merchantTransactionId'], [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $finalXHeader1,
                    'X-MERCHANT-ID' => $rData['data']['merchantTransactionId'],
                    'accept' => 'application/json',
                ]
            ]);
            dd(json_decode($status->getBody()->getContents(), true));
        } catch (Exception $e) {
            Log::error("Error in refundPayment function", [$e->getMessage()]);
        }
    }
}
