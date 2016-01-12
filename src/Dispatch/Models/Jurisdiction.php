<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;

class Jurisdiction extends Model
{
    use Wardenable;

    public static function boot()
    {
        parent::boot();
        Jurisdiction::creating(function ($jurisdiction) {
            $perm = Permission::create([
                'name' => 'view-'.str_slug($jurisdiction->name),
                'display_name' => 'View '.$jurisdiction->name,
                'description' => 'This permission will let the user view '.strtolower($jurisdiction->name),
            ]);
        });
        Jurisdiction::created(function ($jurisdiction) {
            if (\Auth::check()) {
                if(!\Auth::user()->jurisdiction->contains($jurisdiction->id))
                    \Auth::user()->jurisdiction()->attach($jurisdiction->id);
            }
        });
    }
    protected $warden = [
        'name' => 'name',
        'phone_number' => 'phone',
    ];

    protected $fillable = [
        'name',
        'phone_number',
    ];

    protected $table = 'dispatch_jurisdiction';

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'dispatch_jurisdiction_user', 'user_id', 'jurisdiction_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
