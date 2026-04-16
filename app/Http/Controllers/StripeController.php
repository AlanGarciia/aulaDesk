<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function premium()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price' => config('services.stripe.price_id'),
                'quantity' => 1,
            ]],
            'success_url' => route('stripe.success'),
            'cancel_url' => route('stripe.cancel'),
        ]);


        return redirect($session->url);
    }

    public function success()
    {
        return "Pagament Premium completat correctament (TEST)";
    }

    public function cancel()
    {
        return "Pagament cancel·lat (TEST)";
    }
}
