<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Scopes\UserScope;
use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'date' => 'date'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'user_id' => 1,
        'status' => TaskStatus::TODO,
        'priority' => TaskPriority::NORMAL
    ];

    /**
     * Get the user that owns the Todo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::addGlobalScope(new UserScope());
    }
}
