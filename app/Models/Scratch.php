<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ScratchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $thread_id
 * @property string $content
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 * @property-read \App\Models\Thread $thread
 * @method static \Database\Factories\ScratchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scratch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Scratch extends Model
{
    /** @use HasFactory<ScratchFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<Thread, $this>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
