<?php

namespace App\Models;

use App\Contracts\SearchableModelInterface;
use App\Events\TicketDeleted;
use App\Events\TicketSaved;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Ticket
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $priority
 * @property int $user_id
 * @property int $lane_id
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property Lane $lane
 */
class Ticket extends Model implements SearchableModelInterface
{
    use SoftDeletes;

    const CREATE_ACTION = 'created';
    const UPDATE_ACTION = 'updated';
    const DELETE_ACTION = 'deleted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'priority',
        'user_id',
        'lane_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'lane_id',
        'user',
        'lane',
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'user_assigned',
        'status',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved'   => TicketSaved::class,
        'deleted' => TicketDeleted::class,
    ];

    protected $loggable = [
        'title',
        'description',
        'priority',
        'user_id',
        'lane_id',
    ];

    /**
     * The attributes available for searching.
     *
     * @var array
     */
    protected $searchable = [
        'id',
        'title',
        'description',
        'priority',
        'user_id',
        'lane_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return array
     */
    public function getSearchableAttributes(): array
    {
        return $this->searchable;
    }

    /**
     * Get the user that is assigned / owns the ticket.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lane that owns the ticket.
     *
     * @return BelongsTo
     */
    public function lane(): BelongsTo
    {
        return $this->belongsTo(Lane::class);
    }

    /**
     * Get the logs for the ticket.
     *
     * @return HasMany
     */
    public function ticketLogs(): HasMany
    {
        return $this->hasMany(TicketLog::class);
    }

    /**
     * @return string
     */
    public function getUserAssignedAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * @return string
     */
    public function getStatusAttribute(): string
    {
        return $this->lane->name;
    }

    /**
     * @return array
     */
    public function prepareForLog(): array
    {
        $data = $this->getDirty();
        $loggable = $this->loggable;

        $details = array_filter($data, function ($key) use ($loggable) {
            return in_array($key, $loggable);
        }, ARRAY_FILTER_USE_KEY);

        return $details;
    }
}
