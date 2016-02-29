<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\Dispatch\Commands\SendEmails;
use Kregel\Warden\Traits\Wardenable;
use Mail;

class Ticket extends Model
{

    use SoftDeletes, Wardenable;
    protected $fillable = [
        'title',
        'body',
        'priority_id',
        'owner_id',
        'jurisdiction_id',
        'finish_by',
        'closer_id',
    ];

    protected $table = 'dispatch_tickets';
    protected $dates = ['deleted_at', 'finish_by'];
    protected $hidden = [
        'owner_id', 'closer_id'
    ];

    public static function boot()
    {
        self::updated(function (Ticket $ticket) {
            $ticket->sendEmail('update');

        });
        self::created(function (Ticket $ticket) {
            $ticket->sendEmail('new');
        });
        self::deleting(function(Ticket $ticket) {
            $ticket->closer_id = auth()->user()->id;
            $ticket->save();
        });
    }

    public function sendEmail($type){
        \Artisan::queue('dispatch:send-mail', [
            '--ticket' => $this->id, '--type' => $type
        ]);
    }


    public function owner()
    {
        return $this->belongsTo(config('auth.model'));
    }


    public function closer()
    {
        return $this->belongsTo(config('auth.model'));
    }


    public function assign_to()
    {
        return $this->belongsToMany(config('auth.model'), 'dispatch_ticket_user');
    }


    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }


    public function comments()
    {
        return $this->hasMany(Comments::class);
    }


    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }


    public function adjust($userId = null, $diff = null)
    {
        $userId = $userId ?: auth()->user()->id;
        $diff = $diff ?: $this->getDiff();

        return $this->adjustments()->attach($userId, $diff);
    }


    public function getDiff()
    {
        $changed = $this->getDirty();
        $before = json_encode(array_intersect($this->fresh()->toArray(), $changed));
        $after = json_encode($changed);
        $hash = sha1($this);

        return compact('before', 'after', 'hash');
    }


    public function adjustments()
    {
        return $this->belongsToMany(config('auth.model'), 'dispatch_ticket_edits')->withTimestamps()->withPivot([
            'before',
            'after',
            'hash'
        ])->latest('pivot_updated_at');
    }

    public function mailUsers()
    {
        $assigned_users = $this->getEmailList();
    }

    private function getEmailList()
    {
        $emails = $this->assign_to()->select('email')->get()->toArray();
    }
}
