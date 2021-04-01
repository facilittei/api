<?php

namespace App\Models;

use DOMDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chapter_id',
        'title',
        'description',
        'duration',
        'video',
        'audio',
        'doc',
        'position',
        'is_published',
    ];

    /**
     * The chapter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * The comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Format lesson with a chapter on top of a query result.
     *
     * @param array $result
     * @return array
     */
    public static function formatResultWithChapter($result)
    {
        $lessons = [];
        foreach ($result as $res) {
            $lessons[] = [
                'id' => $res->lesson_id,
                'title' => $res->lesson_title,
                'chapter' => [
                    'id' => $res->chapter_id,
                    'title' => $res->chapter_title,
                ],
            ];
        }
        return $lessons;
    }
}
