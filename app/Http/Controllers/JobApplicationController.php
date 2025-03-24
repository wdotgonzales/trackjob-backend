<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user, Request $request)
    {
        $query = $user->jobApplications()->with([
            'employmentType',
            'workArrangement',
            'jobApplicationStatus',
            'reminder'
        ]);

        if ($request->filled('job_application_status')) {
            $query->where('job_application_status_id', $request->job_application_status);
        }

        if ($request->has('employment_type')) {
            $query->where('employment_type_id', $request->employment_type);
        }

        if ($request->has('work_arrangement')) {
            $query->where('work_arrangement_id', $request->work_arrangement);
        }

        $jobApplications = $query->orderBy('created_at', 'desc')->simplePaginate(5);

        return JobApplicationResource::collection($jobApplications);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'position_title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'employment_type_id' => 'required|integer',
            'work_arrangement_id' => 'required|integer',
            'job_application_status_id' => 'required|integer',
            'job_posting_link' => 'nullable|string|max:255', // Added nullable
            'date_applied' => 'required|date',
            'company_logo_url' => 'string|max:255',
            'job_location' => 'required|string|max:255'
        ]);

        $jobApplication = JobApplication::create([
            ...$validatedData,
            'user_id' => 108 //temporary
        ]);

        return new JobApplicationResource($jobApplication);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, JobApplication $jobApplication)
    {
        // Ensure the job application belongs to the given user
        if ($jobApplication->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this job application.');
        }

        return new JobApplicationResource(
            $jobApplication->load(['employmentType', 'workArrangement', 'jobApplicationStatus', 'reminder'])
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, JobApplication $jobApplication)
    {
        // Ensure the job application belongs to the given user
        if ($jobApplication->user_id !== $user->id) {
            abort(403, 'Unauthorized access to update this job application.');
        }

        $validatedData = $request->validate([
            'position_title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'employment_type_id' => 'required|integer',
            'work_arrangement_id' => 'required|integer',
            'job_application_status_id' => 'required|integer',
            'job_posting_link' => 'nullable|string|max:255',
            'date_applied' => 'required|date',
            'company_logo_url' => 'string|max:255',
            'job_location' => 'required|string|max:255'
        ]);

        $jobApplication->update($validatedData);

        return new JobApplicationResource($jobApplication);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, JobApplication $jobApplication)
    {
        // Ensure the job application belongs to the given user
        if ($jobApplication->user_id !== $user->id) {
            abort(403, 'Unauthorized access to delete this job application.');
        }

        $jobApplication->delete();

        return response()->json(['message' => 'Job application deleted successfully.'], 200);
    }

}
