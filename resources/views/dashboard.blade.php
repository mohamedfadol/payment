<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" >

    <title>Aurages Payment</title>
  </head>
  <body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center m-5">Aurages Payment</h1>
                @if(session()->has('success_message'))
                    <p class="text-success">{{session()->get('success_message')}}</p>
                @endif
                <form id="payment-form" action="{{route('confirm-pay')}}" method="POST">
                    {{ csrf_field() }}
                    <div id="dropin-container"></div>
                    <input type="hidden" name="price" value="1" id="price">
                    <input type="hidden" id="nonce" name="payment_method_nonce">
                    @php
                        $gateway = new \Braintree\Gateway([
                        'environment' => config('services.braintree.environment'),
                        'merchantId' => env('BT_MERCHANT_ID'),
                        'publicKey' => env('BT_PUBLIC_KEY'),
                        'privateKey' => env('BT_PRIVATE_KEY')
                        ]);
                        $token = $gateway->ClientToken()->generate();
                    @endphp
                    <button type="button" class="btn btn-primary btn-block"><i class="fa fa-money mr-2" aria-hidden="true"></i>Payment Now</button>
                </form>
                <script src="https://js.braintreegateway.com/web/dropin/1.33.1/js/dropin.min.js"></script>
                <script>
                    var form = document.querySelector('#payment-form');
                    var nonceInput = document.querySelector('#nonce');
                    var price = document.getElementById('price').value;
                    braintree.dropin.create({
                    authorization: '{{$token}}',
                    container: '#dropin-container',
                    applePay: {
                        displayName: 'Merchant Name',
                        paymentRequest: {
                        total: {
                            label: 'Localized Name',
                            amount: price
                        }
                        }
                    },
                    paypal: {
                        flow: 'checkout',
                        amount: price,
                        currency: 'USD'
                    },
                    paypalCredit: {
                    flow: 'checkout',
                    amount: price,
                    currency: 'USD'
                    },
                    venmo: true
                    }, function (createErr, dropinInstance) {
                    // Set up a handler to request a payment method and
                    // submit the payment method nonce to your server
                    if (createErr) {
                            console.log('Create Error', createErr);
                            return;
                        }
                        form.addEventListener('submit', function (event) {
                            event.preventDefault();
                            dropinInstance.requestPaymentMethod(function (err, payload) {
                                if (err) {
                                console.log('Request Payment Method Error', err);
                                return;
                                }
                                // Add the nonce to the form and submit
                                // document.querySelector('#nonce').value = payload.nonce;
                                // Send payload.nonce to your server
                                nonceInput.value = payload.nonce;
                                form.submit();
                            });
                        });
                    } );

                </script>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" ></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>

</body>
</html>