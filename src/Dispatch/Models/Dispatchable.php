<?php

namespace Kregel\Dispatch\Models;

trait Dispatchable
{

    public function closed_tickets(){
        return $this->hasMany(config('kregel.dispatch.models.ticket'), 'closer_id');
    }

    public function tickets(){
        return $this->hasMany(config('kregel.dispatch.models.ticket'), 'owner_id');
    }

    public function assigned_tickets(){
        return $this->belongsToMany(config('kregel.dispatch.models.ticket'), 'dispatch_ticket_user', 'user_id', 'ticket_id');
    }

    public function jurisdiction(){
        return $this->belongsToMany(config('kregel.dispatch.models.jurisdiction'), 'dispatch_jurisdiction_user', 'user_id', 'jurisdiction_id');
    }

    public function can_assign(){
        return $this->hasRole(['developer', 'saycomputer-admin']);
    }
    public function all_tickets($int = true){
        $locations = $this->jurisdiction;
        if($int)
            $tickets = 0;
        else
            $tickets = [];
        foreach($locations as $loc){
            if($int)
                $tickets += ($loc->tickets->count());
            else
                $tickets[] = $loc->tickets();
        }
        return $tickets;
    }
    public function all_open_tickets(){
        $locations = $this->jurisdiction;
        $tickets = 0;
        foreach($locations as $loc){
            $tickets += ($loc->tickets()->where('deleted_at', null)->get()->count());
        }
        return $tickets;
    }
}