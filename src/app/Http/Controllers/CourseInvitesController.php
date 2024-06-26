<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseInvite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CourseInvitesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $identify = (new CourseInvite)->identifyToken($token);

        if (isset($identify['error'])) {
            return response()->json([
                'error' => trans('auth.unauthorized'),
            ], 401);
        }

        $courseInvite = CourseInvite::where('course_id', $identify['course_id'])
            ->where('token', $token)
            ->first();

        if (! $courseInvite) {
            return response()->json([
                'error' => trans('auth.invalid_verification_token'),
            ], 401);
        }

        $req = $request->all();
        $user = User::create([
            'name' => $courseInvite->name,
            'email' => $courseInvite->email,
            'password' => bcrypt($req['password']),
            'email_verified_at' => Carbon::now(),
        ]);

        if ($user) {
            $courseInvite->delete();
            $course = Course::findOrFail($identify['course_id']);
            $course->students()->syncWithoutDetaching($user->id);
            $user['token'] = $user->createToken($request->header('User-Agent'))->plainTextToken;

            return response()->json($user);
        }

        return response()->json([
            'error' => trans('messages.general_error'),
        ], 422);
    }
}
