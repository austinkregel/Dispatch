<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;

class Comments extends Model
{
    use Wardenable;

    protected $fillable = [
        'body',
        'user_id',
        'ticket_id',
    ];

    protected $table = 'dispatch_ticket_comments';

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function tickets()
    {
        return $this->belongsTo(Ticket::class);
    }
}
