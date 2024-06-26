<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CourseInvite extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'name',
        'email',
        'token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token',
    ];

    /**
     * Generate the token that identifies the invite.
     *
     * @param  int  $course_id
     * @return string
     */
    public function generateToken($course_id)
    {
        if ($course_id) {
            return Crypt::encryptString($course_id.'-'.Str::uuid());
        }
    }

    /**
     * Validate and decrypt the identification token invite.
     *
     * @param  string  $token
     * @return array
     */
    public function identifyToken($token)
    {
        if ($token) {
            try {
                $invite = Crypt::decryptString($token);
                $components = explode('-', $invite);

                return [
                    'course_id' => array_shift($components),
                ];
            } catch (DecryptException $e) {
                return [
                    'error' => $e,
                ];
            }
        }
    }
}
