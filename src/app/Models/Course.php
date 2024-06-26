<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'price',
        'slug',
        'description',
        'cover',
        'is_published',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'float',
    ];

    /**
     * The user (teacher).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The students.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * The chapters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('position');
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
     * The lessons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    /**
     * The sales.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany(OrderItem::class)
            ->join('order_histories', 'order_histories.order_id', '=', 'order_items.order_id')
            ->where('status', '=', OrderStatus::SUCCEED->value);
    }

    /**
     * Set the slug by title.
     *
     * @param  string  $value
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Get the cover image URL.
     *
     * @return string
     */
    public function getCoverAttribute()
    {
        if ($this->attributes['cover']) {
            return config('app.assets_url').'/'.$this->attributes['cover'];
        }
    }
}
