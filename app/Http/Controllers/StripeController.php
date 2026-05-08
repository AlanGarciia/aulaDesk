<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Stripe\Stripe;

use Stripe\Checkout\Session;

use Stripe\Subscription;

class StripeController extends Controller
{
    public function premium()
    {
        Stripe::setApiKey(
            config('services.stripe.secret')
        );

        $session = Session::create([

            'customer_email' =>
                auth()->user()->email,

            'payment_method_types' => ['card'],

            'mode' => 'subscription',

            'line_items' => [[

                'price' =>
                    config('services.stripe.price_id'),

                'quantity' => 1,
            ]],

            'success_url' =>

                route('stripe.success')

                . '?session_id={CHECKOUT_SESSION_ID}',

            'cancel_url' =>
                route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(
            config('services.stripe.secret')
        );

        $session = Session::retrieve(
            $request->session_id
        );

        $subscription = Subscription::retrieve(
            $session->subscription
        );

        auth()->user()->update([

            'plan' => 'premium',

            'stripe_customer_id' =>
                $session->customer,

            'stripe_subscription_id' =>
                $subscription->id,
        ]);

        return redirect()

            ->route('dashboard')

            ->with(
                'success',
                'Premium activat'
            );
    }

    public function cancel()
    {
        return redirect()

            ->route('dashboard')

            ->with(
                'error',
                'Pagament cancel·lat'
            );
    }

    public function portal()
    {
        Stripe::setApiKey(
            config('services.stripe.secret')
        );

        $session =
            \Stripe\BillingPortal\Session::create([

                'customer' =>
                    auth()->user()
                    ->stripe_customer_id,

                'return_url' =>
                    route('dashboard'),
            ]);

        return redirect($session->url);
    }

    public function cancelSubscription()
    {
        Stripe::setApiKey(
            config('services.stripe.secret')
        );

        Subscription::update(

            auth()->user()
                ->stripe_subscription_id,

            [
                'cancel_at_period_end' => true
            ]
        );

        return back()->with(
            'success',
            'Subscripció cancel·lada'
        );
    }
}