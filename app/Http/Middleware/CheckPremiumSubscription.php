<?php

namespace App\Http\Middleware;

use Closure;

use Stripe\Stripe;

use Stripe\Subscription;

class CheckPremiumSubscription
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (

            $user &&

            $user->stripe_subscription_id

        ) {

            Stripe::setApiKey(
                config('services.stripe.secret')
            );

            try {

                $subscription =
                    Subscription::retrieve(

                        $user
                            ->stripe_subscription_id
                    );

                if (
                    $subscription->status === 'active'
                ) {

                    $user->update([
                        'plan' => 'premium'
                    ]);

                } else {

                    $user->update([
                        'plan' => 'free'
                    ]);
                }

            } catch (\Exception $e) {

                $user->update([
                    'plan' => 'free'
                ]);
            }
        }

        return $next($request);
    }
}