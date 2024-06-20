@extends('front.layouts.app')

@section('content')

    <main>
        <section class="section-5 pt-3 pb-3 mb-3 bg-white">
            <div class="container">
                <div class="light-font">
                    <ol class="breadcrumb primary-color mb-0">
                        <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                        <li class="breadcrumb-item">Checkout</li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="section-9 pt-4">
            <div class="container">
                <form role="form" method="POST" id="orderForm" name="orderForm"
                    data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" data-cc-on-file="false" action="{{route('front.processCheckout')}}">
                    @csrf
                    <input type="hidden" name="stripeToken" id="stripeToken" value="">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="sub-title">
                                <h2>Shipping Address</h2>
                            </div>
                            <div class="card shadow-lg border-0">
                                <div class="card-body checkout-form">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="first_name" id="first_name" class="form-control"
                                                    placeholder="First Name"
                                                    value="{{ !empty($customerAddress) ? $customerAddress->first_name : '' }}">
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="last_name" id="last_name" class="form-control"
                                                    placeholder="Last Name"
                                                    value="{{ !empty($customerAddress) ? $customerAddress->last_name : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="email" id="email" class="form-control"
                                                    placeholder="Email"
                                                    value="{{ !empty($customerAddress) ? $customerAddress->email : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <select name="country" id="country" class="form-control">
                                                    <option value="">Select a Country</option>
                                                    @if ($countries->isNotEmpty())
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}"
                                                                {{ !empty($customerAddress) && $customerAddress->country_id == $country->id ? 'selected' : '' }}>
                                                                {{ $country->name }} </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control">{{ !empty($customerAddress) ? $customerAddress->address : '' }}</textarea>
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="apartment" id="apartment" class="form-control"
                                                    placeholder="Apartment, suite, unit, etc. (optional)"
                                                    value="{{ !empty($customerAddress) ? $customerAddress->apartment : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" name="city" id="city" class="form-control"
                                                    placeholder="City"
                                                    value="{{ !empty($customerAddress) ? $customerAddress->city : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" name="state" id="state" class="form-control"
                                                    placeholder="State"
                                                    value="{{ !empty($customerAddress) ? $customerAddress->state : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <input type="text" name="zip" id="zip" class="form-control"
                                                    placeholder="Zip"
                                                    value="{{ !empty($customerAddress) ? $customerAddress->zip : '' }}">
                                                <p></p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" name="mobile" id="mobile" class="form-control"
                                                    placeholder="Mobile No."
                                                    value="{{ !empty($customerAddress) ? $customerAddress->mobile : '' }}">
                                                <p></p>
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <textarea name="notes" id="notes" cols="30" rows="2" placeholder="Order Notes (optional)"
                                                    class="form-control"></textarea>
                                                <p></p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sub-title">
                                <h2>Order Summery</h3>
                            </div>
                            <div class="card cart-summery">
                                <div class="card-body">
                                    @foreach (Cart::content() as $item)
                                        <div class="d-flex justify-content-between pb-2">
                                            <div class="h6">{{ $item->name }} X {{ $item->qty }}</div>
                                            <div class="h6">{{ $item->price * $item->qty }}</div>
                                        </div>
                                    @endforeach
                                    <div class="d-flex justify-content-between summery-end">
                                        <div class="h6"><strong>Subtotal</strong></div>
                                        <div class="h6"><strong> {{ Cart::subtotal() }} </strong></div>
                                    </div>
                                    <div class="d-flex justify-content-between summery-end">
                                        <div class="h6"><strong>Discount</strong></div>
                                        <div class="h6"><strong id="discount"> {{ number_format($discount, 2) }}
                                            </strong></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <div class="h6"><strong>Shipping</strong></div>
                                        <div class="h6"><strong
                                                id="shippingAmount">{{ number_format($totalShipping, 2) }}</strong></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 summery-end">
                                        <div class="h5"><strong>Total</strong></div>
                                        <div class="h5"><strong
                                                id="grandTotal">{{ number_format($grandTotal, 2) }}</strong></div>
                                        <input type="hidden" value="{{ $grandTotal }}" name="grandTotal">
                                    </div>
                                </div>
                            </div>

                            <div class="card payment-form ">
                                <h3 class="card-title h5 mb-3">Split Payments</h3>
                                <div class="card-body p-0" id="split-payment_div">
                                    <div class="col-md-12">
                                        <div class="mb-3">  
								            <label for="name">No of People</label>
                                            <input type="number" name="no_of_people" id="no_of_people" class="form-control"
                                                placeholder="No of People">
                                            <p></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name">No of People you paid for</label>
                                            <input type="number" name="no_of_people_paid_for" id="no_of_people_paid_for" class="form-control"
                                                placeholder="No of People you paid for">
                                            <p></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card payment-form ">
                                <h3 class="card-title h5 mb-3">Payment Details</h3>
                                <div class="">
                                    <input type="radio" name="payment_method" id="payment_method_one" value="cod"
                                        checked>
                                    <label for="payment_method_one" class="form-check-label">COD</label>
                                </div>
                                <div class="mb-3">
                                    <input type="radio" name="payment_method" id="payment_method_two" value="stripe">
                                    <label for="payment_method_two" class="form-check-label">Stripe</label>
                                </div>
                                <div class="card-body p-0 d-none" id="payment_div">
                                    <div class='form-row row'>
                                        <div class='col-xs-12 form-group required'>
                                            <label class='control-label'>Name on Card</label> <input class='form-control'
                                                size='4' type='text' name="name_on_card">
                                        </div>
                                    </div>

                                    <div class='form-row row'>
                                        <div class='col-xs-12 form-group card required'>
                                            <label class='control-label'>Card Number</label> <input autocomplete='off'
                                                class='form-control card-number' size='20' type='text' name="card_number" value="4242424242424242">
                                        </div>
                                    </div>

                                    <div class='form-row row'>
                                        <div class='col-xs-12 col-md-4 form-group cvc required'>
                                            <label class='control-label'>CVC</label> <input autocomplete='off'
                                                class='form-control card-cvc' placeholder='ex. 311' size='4'
                                                type='text' name="cvc" value="123">
                                        </div>
                                        <div class='col-xs-12 col-md-4 form-group expiration required'>
                                            <label class='control-label'>Expiration Month</label> <input
                                                class='form-control card-expiry-month' placeholder='MM' size='2'
                                                type='text' name="exp_month" value="11">
                                        </div>
                                        <div class='col-xs-12 col-md-4 form-group expiration required'>
                                            <label class='control-label'>Expiration Year</label> <input
                                                class='form-control card-expiry-year' placeholder='YYYY' size='4'
                                                type='text' name="exp_year" value="2025">
                                        </div>
                                    </div>

                                </div>
                                <div class="input-group apply-coupan mt-4">
                                    <input type="text" placeholder="Coupon Code" class="form-control"
                                        id="discountCoupon" name="discountCoupon">
                                    <button class="btn btn-dark" type="button" name="applyDiscount"
                                        id="applyDiscount">Apply Coupon</button>
                                </div>
                                <div id="removeDiscountDiv-wrapper">
                                    @if (Session::has('code'))
                                        <div class="input-group mt-4" id="removeDiscountDiv">
                                            <strong>{{ Session::get('code')->code }}</strong>
                                            <a href="#" class="btn btn-danger btn-sm" id="removeDiscount"><i
                                                    class="fa fa-times"></i></a>
                                        </div>
                                    @endif
                                </div>
                                <div class="pt-4">
                                    <button type="submit" class="btn-dark btn btn-block w-100">Pay Now</button>
                                </div>
                            </div>


                            <!-- CREDIT CARD FORM ENDS HERE -->

                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

@endsection

@section('customJs')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <script>
        $(function() {
            // Setup form and Stripe publishable key
            var $form = $("#orderForm");
            var stripePublishableKey = $form.data('stripe-publishable-key');

            // Add submit event handler to the form
            $form.on('submit', function(event) {
                event.preventDefault();

                // Check which payment method is selected
                if ($("input[name='payment_method']:checked").val() === "stripe") {
                    // Stripe payment selected
                    Stripe.setPublishableKey(stripePublishableKey);
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                } else {
                    // COD payment selected
                    submitOrderForm();
                }
            });

            // Stripe response handler
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    // Show error message
                    alert(response.error.message);
                } else {
                    // Token was created
                    var token = response.id;
                    $("#stripeToken").val(token);
                    submitOrderForm();
                }
            }

            // Function to submit the order form
            function submitOrderForm() {
                $("button[type=submit]").prop('disabled', true);
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serializeArray(),
                    dataType: 'json',
                    success: function(response) {
                        $("button[type=submit]").prop('disabled', false);
                        if (response.status == false) {
                            handleErrors(response.errors);
                        } else {
                            window.location.href = "{{ url('/thanks/') }}/" + response.orderId;
                        }
                    }
                });
            }

            // Function to handle form errors
            function handleErrors(errors) {
                if (errors.first_name) {
                    $('#first_name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors
                        .first_name);
                } else {
                    $('#first_name').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.last_name) {
                    $('#last_name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors
                        .last_name);
                } else {
                    $('#last_name').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.email) {
                    $('#email').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors
                    .email);
                } else {
                    $('#email').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.country) {
                    $('#country').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors
                        .country);
                } else {
                    $('#country').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.address) {
                    $('#address').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors
                        .address);
                } else {
                    $('#address').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.city) {
                    $('#city').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.city);
                } else {
                    $('#city').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.state) {
                    $('#state').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors
                    .state);
                } else {
                    $('#state').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.zip) {
                    $('#zip').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.zip);
                } else {
                    $('#zip').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }

                if (errors.mobile) {
                    $('#mobile').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors
                        .mobile);
                } else {
                    $('#mobile').removeClass('is-invalid').siblings('p.invalid-feedback').html('');
                }
            }

            // Payment method selection handling
            $("#payment_method_two").click(function() {
                if ($(this).is(':checked')) {
                    $("#payment_div").removeClass('d-none');
                }
            });

            $("#payment_method_one").click(function() {
                if ($(this).is(':checked')) {
                    $("#payment_div").addClass('d-none');
                }
            });

            // Country selection handling
            $("#country").change(function() {
                let country = $(this).val();
                $.ajax({
                    url: '{{ route('front.getOrderSummary') }}',
                    method: 'POST',
                    data: {
                        country: country
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == true) {
                            $("#grandTotal").html(response.grand_total);
                            $("#shippingAmount").html(response.totalShipping);
                        }
                    }
                });
            });

            // Apply discount coupon
            $("#applyDiscount").click(function() {
                $.ajax({
                    url: '{{ route('cart.discountCoupon') }}',
                    method: 'POST',
                    data: {
                        code: $("#discountCoupon").val(),
                        country: $("#country").val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == true) {
                            $("#grandTotal").html(response.grand_total);
                            $("#discount").html(response.discount);
                            $("#shippingAmount").html(response.totalShipping);
                            $("#removeDiscountDiv-wrapper").html(response.discountString);
                        } else {
                            $("#removeDiscountDiv-wrapper").html("<span class='text-danger'>" +
                                response.message + "</span>");
                        }
                    }
                });
            });

            // Remove discount
            $("body").on('click', "#removeDiscount", function() {
                $.ajax({
                    url: "{{ route('cart.removeDiscount') }}",
                    method: 'POST',
                    data: {
                        country: $("#country").val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == true) {
                            $("#grandTotal").html(response.grand_total);
                            $("#discount").html(response.discount);
                            $("#shippingAmount").html(response.totalShipping);
                            $("#removeDiscountDiv-wrapper").html("");
                            $("#discountCoupon").val("");
                        }
                    }
                });
            });
        });
    </script>
@endsection
