<?php

namespace App\Http\Middleware;

use App\Models\JobApplication;
use App\Models\Reminder;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureReminderOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $job_application_id = $request->route('job_application');

        $job_application = JobApplication::find($job_application_id);

        if (!$job_application) {
            return response()->json([
                'message' => "This job application does not exist."
            ], status: 400);
        }

        if ($request->user()->id !== $job_application->user_id) {
            return response()->json([
                'message' => "Unauthorized to access this job application."
            ], 403);
        }

        $reminder_id = $request->route('reminder');

        if ($reminder_id) {
            $reminder = Reminder::find($reminder_id);

            if (!$reminder) {
                return response()->json([
                    'message' => "This reminder does not exist."
                ], status: 400);
            }

            if ($job_application->id !== $reminder->job_application_id) {
                return response()->json([
                    'message' => "Unauthorized access this reminder with this job application"
                ], 403);
            }
        }

        return $next($request);
    }
}
