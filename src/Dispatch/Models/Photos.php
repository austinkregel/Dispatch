<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\Warden\Traits\Wardenable;

class Photos extends Model
{
    use SoftDeletes, Wardenable;
    protected $fillable = [
        'uuid',
        'path',
        'type',
        'ticket_id',
        'user_id'
    ];

    protected $table = 'dispatch_ticket_media';

    protected $touches = ['ticket'];

    public static function boot()
    {

    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(config('kregel.dispatch.user'));
    }
}