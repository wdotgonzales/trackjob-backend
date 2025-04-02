<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\JobApplication;
use App\Models\Subscription;
use Carbon\Carbon;
use App\Models\JobApplicationStatus;

class StatisticsController extends Controller
{
    public function handleStatistics(Request $request)
    {
        $user_id = $request->user()->id;

        return response()->json([
            'data' => [
                'user' => $this->fetchUserInformation($user_id),
                'subscriptionLifeSpanDays' => $this->fetchSubscriptionLifeSpan($user_id),
                'applicationStatuses' => $this->fetchApplicationStatuses(),
                'jobApplicationStatusCount' => $this->fetchjobApplicationStatusCount($user_id)
            ]
        ]);
    }

    private function fetchUserInformation(int $user_id)
    {
        $user = User::find($user_id);
        return new UserResource($user);
    }

    private function fetchSubscriptionLifeSpan(int $user_id)
    {
        $subscription = Subscription::where('user_id', $user_id)
            ->where('isActive', 1)
            ->first();

        $current_date = Carbon::now('Asia/Manila');
        $expiration_date = Carbon::parse($subscription->end_date);

        if ($current_date->greaterThanOrEqualTo($expiration_date)) {
            return 0;
        }

        return (int) $current_date->diffInDays($expiration_date);
    }

    private function fetchApplicationStatuses()
    {
        return JobApplicationStatus::all();
    }

    private function fetchjobApplicationStatusCount(int $user_id)
    {
        $jobApplicationStatuses = $this->fetchApplicationStatuses(); // Fetch all statuses

        $statusCounts = [];

        foreach ($jobApplicationStatuses as $status) {
            $statusCounts[$status->title] = JobApplication::where('user_id', $user_id)
                ->where('job_application_status_id', $status->id)
                ->count();
        }

        $statusCounts['total_count'] = JobApplication::where('user_id', $user_id)->count();

        return $statusCounts;
    }
}
