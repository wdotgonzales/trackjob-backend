<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\SubscriptionController;

class EnsureValidSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subscriptionController = new SubscriptionController();
        
        $subscription = $subscriptionController->checkIfUserHasExistingValidSubscription($request->user()->id);

        if (!$subscription) {
            return response()->json([
                'message' => 'User has no existing valid subscription'
            ], 403);
        }

        if ($subscriptionController->isSubscriptionExpired($subscription)) {
            return response()->json([
                'message' => "User's subscription has already expired"
            ], 403);
        }

        return $next($request);
    }
}
