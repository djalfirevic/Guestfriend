<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TicketLog
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $ticket_id
 * @property string $action
 * @property string $details
 * @property Carbon $requested_at
 */
class TicketLog extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'action',
        'details',
        'ticket_id',
        'requested_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'ticket_id',
    ];

    /**
     * Get the ticket that is owner of a log.
     *
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
