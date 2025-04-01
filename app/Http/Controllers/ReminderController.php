<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\Reminder;
use App\Http\Resources\ReminderResource;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, int $job_application_id)
    {
        $reminders = Reminder::where('job_application_id', $job_application_id)->get();
        return ReminderResource::collection($reminders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $job_application_id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'reminder_date' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $reminder = Reminder::create([
            ...$validatedData,
            'job_application_id' => $job_application_id
        ]);

        return new ReminderResource($reminder);
    }


    /**
     * Display the specified resource.
     */
    public function show(int $job_application_id, int $reminder_id)
    {
        $reminder = Reminder::where('id', $reminder_id)->first();
        return new ReminderResource($reminder);
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
    public function update(Request $request, int $job_application_id, int $reminder_id)
    {
        $reminder = Reminder::where('id', $reminder_id)->first();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'reminder_date' => 'required|date_format:Y-m-d H:i:s',
            'isReminderUsed' => 'required|boolean',
        ]);

        $reminder->update($validatedData);

        return new ReminderResource($reminder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $job_application_id, int $reminder_id)
    {
        $reminder = Reminder::where('id', $reminder_id)->first();
        $reminder->delete();
        return response()->json(['message' => 'Reminder deleted successfully.'], 200);
    }
}
