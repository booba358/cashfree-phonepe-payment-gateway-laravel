<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashfreeController extends Controller
{
    /**
     * initiate
     * @return Redirect
     *
     */
    public function initiate()
    {
        try {
            // Cashfree Payment Gateway

            $url = "https://sandbox.cashfree.com/pg/orders";

            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: TEST10030635a875ebca16ebdbed20cd53603001",
                "x-client-secret: TESTd1f5027f84f9fabdbc923e115c25be3ce556683a"
            );
            $data = json_encode([
                'order_id' =>  'order_'.rand(1111111111,9999999999),
                'order_amount' => 1000,
                "order_currency" => 'INR',
                "customer_details" => [
                    "customer_id" => '1',
                    "customer_name" => 'Test',
                    "customer_email" => 'test@mailinator.com',
                    "customer_phone" => '9856748798',
                ],
                "order_meta" => [
                    "return_url" => 'http://127.0.0.1:8000/cashfree/success-payment/?order_id={order_id}&order_token={order_token}'
                ]
            ]);
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);
            // dd($resp);
            curl_close($curl);
            return redirect()->to(json_decode($resp)->payment_link);
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
            $client = new \GuzzleHttp\Client();
            $status = $client->get('https://sandbox.cashfree.com/pg/orders/'.$request->get('order_id'), [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-01-01',
                    'x-client-id' => 'TEST10030635a875ebca16ebdbed20cd53603001',
                    'x-client-secret' => 'TESTd1f5027f84f9fabdbc923e115c25be3ce556683a',
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
            // Cashfree Refund

            $url = "https://sandbox.cashfree.com/pg/orders/order_4630046927/refunds";

            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: TEST10030635a875ebca16ebdbed20cd53603001",
                "x-client-secret: TESTd1f5027f84f9fabdbc923e115c25be3ce556683a"
            );
            $data = json_encode([
                'order_id' =>  'order_4630046927',
                'refund_amount' => 10,
                "refund_id" => 'refund'.rand(1111111111,9999999999),
                "refund_note" => 'test',
            ]);
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);
            curl_close($curl);
        } catch (Exception $e) {
            Log::error("Error in refundPayment function", [$e->getMessage()]);
        }
    }
}
