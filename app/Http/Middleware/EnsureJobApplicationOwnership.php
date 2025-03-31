<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\JobApplication;


class EnsureJobApplicationOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $job_application = $request->route('job_application');

        if ($request->user()->id !== $job_application->user_id) {
            return response()->json([
                'message' => 'Unauthorized access to this job application.'
            ], 403);
        }

        return $next($request);
    }
}
