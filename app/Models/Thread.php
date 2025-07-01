<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ThreadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property bool $is_closed
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable|null $last_scratch_created_at
 * @property \Carbon\CarbonImmutable|null $last_closed_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Scratch> $scratches
 * @property-read int|null $scratches_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ThreadFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereIsClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereLastClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereLastScratchCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Thread whereUserId($value)
 * @mixin \Eloquent
 */
class Thread extends Model
{
    /** @use HasFactory<ThreadFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'is_closed',
    ];

    protected $attributes = [
        'is_closed' => false,
        'last_scratch_created_at' => null,
        'last_closed_at' => null,
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'created_at' => 'datetime',
        'last_scratch_created_at' => 'datetime',
        'last_closed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Thread $thread): void {
            $thread->created_at = now();
        });
    }

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
