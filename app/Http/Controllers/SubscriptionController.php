<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function handlePurchaseSubscription(Request $request)
    {
        $validatedData = $request->validate([
            'subscription_plan_id' => 'required|integer',
        ]);

        $subscription = $this->checkIfUserHasExistingValidSubscription($request->user()->id);

        if (!$subscription) {
            return $this->createSubscription($request->user()->id, $validatedData['subscription_plan_id']);
        }

        if ($this->isSubscriptionExpired($subscription)) {
            $this->changeSubscriptionStatusToExpired($subscription);
            return $this->createSubscription($request->user()->id, $validatedData['subscription_plan_id']);
        }

        return $this->extendSubscription($subscription, $validatedData['subscription_plan_id']);
    }

    public function checkIfUserHasExistingValidSubscription(int $user_id)
    {
        $subscription = Subscription::where('user_id', $user_id)
            ->where('isActive', 1)
            ->first();

        return $subscription;
    }

    public function isSubscriptionExpired($subscription)
    {
        $current_date = Carbon::now('Asia/Manila');
        $end_date = Carbon::parse($subscription->end_date);

        if ($current_date->greaterThanOrEqualTo($end_date)) {
            return true;
        }
        return false;
    }

    public function createSubscription(int $user_id, int $subscription_plan_id)
    {
        $subscription_plan = SubscriptionPlan::findOrFail($subscription_plan_id);

        $start_date = Carbon::now('Asia/Manila');
        $end_date = $start_date->copy()->addDays($subscription_plan->duration_days);

        $subscription = Subscription::create([
            'user_id' => $user_id,
            'subscription_plan_id' => $subscription_plan->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'isActive' => 1
        ]);

        return response()->json([
            'message' => 'Subscription created successfully.',
            'data' => $subscription
        ], 201);
    }

    public function changeSubscriptionStatusToExpired($subscription)
    {
        $subscription->update([
            'isActive' => 2
        ]);
    }

    public function extendSubscription($subscription, $subscription_plan_id)
    {
        $subscription_plan = SubscriptionPlan::findOrFail($subscription_plan_id);

        $end_date = Carbon::parse($subscription->end_date)->addDays($subscription_plan->duration_days);

        $subscription->update([
            'subscription_plan_id' => $subscription_plan->id,
            'end_date' => $end_date
        ]);

        return response()->json([
            'message' => "Subscription's expiration date extended successfully.",
            'data' => $subscription
        ], 201);
    }

    public function subscription(Request $request)
    {
        return [
            'data' => $this->checkIfUserHasExistingValidSubscription($request->user()->id)
        ];
    }   
}
    