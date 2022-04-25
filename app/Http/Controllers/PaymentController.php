<?php

namespace App\Http\Controllers;

use Braintree\Gateway;
use Braintree;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $package = 1;
        $user['email'] = "mohamed@test.com";
        return view('dashboard',compact('package','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {

        $data = [];
        $data['items'] = [
                [
                    'name' => $request->name,
                    'price' => (float)$request->price,
                    'qty' => 1
                ]
            ];
           
        $data['total'] = (float)$request->price;
        $data['invoice_id'] = Str::random(5);
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $gateway = new Braintree\Gateway([
                'environment' => config('services.braintree.environment'),
                'merchantId' => env('BT_MERCHANT_ID'),
                'publicKey' => env('BT_PUBLIC_KEY'),
                'privateKey' => env('BT_PRIVATE_KEY')
            ]);

            $amount = $request->price;
            $nonce = $request->payment_method_nonce;

            $result = $gateway->transaction()->sale([
                'amount' => $amount,
                'paymentMethodNonce' => $nonce,
                'customer' => [
                    'firstName' => 'Tony',
                    'lastName' => 'Stark',
                    'email' => 'tony@avengers.com',
                ],
                'options' => [
                    'submitForSettlement' => true
                ]
            ]);

            // dd($result);
            if ($result->success) {
                $transaction = $result->transaction;
                // header("Location: transaction.php?id=" . $transaction->id);

                return back()->with('success_message', 'Transaction successful. The ID is:'. $transaction->id);
            } else {
                $errorString = "";

                foreach ($result->errors->deepAll() as $error) {
                    $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
                }

                // $_SESSION["errors"] = $errorString;
                // header("Location: index.php");
                return back()->withErrors('An error occurred with the message: '.$result->message);
            }


        // // redirect to paypal
        // // after payment is done paypal
        // // will redirect us back to $this->expressCheckoutSuccess
        // return redirect($response['paypal_link']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
