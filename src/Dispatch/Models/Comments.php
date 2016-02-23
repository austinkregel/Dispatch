<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\Warden\Traits\Wardenable;

class Comments extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'body',
        'user_id',
        'ticket_id',
    ];
    protected $dates = ['deleted_at'];

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
