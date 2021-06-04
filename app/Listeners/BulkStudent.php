<?php

namespace App\Listeners;

use App\Events\EnrollMany;
use App\Mail\CourseEnrollManyMail;
use App\Mail\CourseInviteMail;
use App\Models\User;
use App\Models\CourseInvite;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BulkStudent implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  EnrollMany  $event
     * @return void
     */
    public function handle(EnrollMany $event)
    {
        $students = [];
        for ($i = 0; $i < count($event->records); $i++) {
            $user = explode(',', $event->records[$i]);
            if (count($user) == 2) {
                list($name, $email) = $user;
                $isUser = User::where('email', $email)->select('id', 'name', 'email')->first();
                if ($isUser) {
                    $students[] = $isUser->id;
                    Mail::to($isUser->email)->queue(new CourseEnrollManyMail($event->course, $isUser));
                } else {
                    $invite = CourseInvite::firstOrCreate([
                        'course_id' => $event->course->id,
                        'name' => $name,
                        'email' => $email,
                        'token' => Str::uuid(),
                    ]);
                    Mail::to($invite->email)->queue(new CourseInviteMail($event->course, $invite));
                }
            }
        }
        $event->course->students()->syncWithoutDetaching($students);
    }
}
