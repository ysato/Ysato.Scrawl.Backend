<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ThreadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    /** @use HasFactory<ThreadFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'is_closed',
        'created_at',
        'last_scratch_created_at',
        'last_closed_at',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'created_at' => 'datetime',
        'last_scratch_created_at' => 'datetime',
        'last_closed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Scratch, $this>
     */
    public function scratches(): HasMany
    {
        return $this->hasMany(Scratch::class);
    }
}
