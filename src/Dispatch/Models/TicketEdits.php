<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;
use Kregel\Dispatch\Models\Comments;

class TicketEdits extends Model
{



    protected $fillable = [
        'id',
        'user_id',
        'ticket_id',
        'before',
        'after',
        'hash',
        'created_at',
        'updated_at',
    ];

    protected $table = 'dispatch_ticket_edits';


}
