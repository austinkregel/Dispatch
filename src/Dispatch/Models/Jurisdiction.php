<?php

namespace Kregel\Dispatch\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\Warden\Traits\Wardenable;

class Jurisdiction extends Model
{
    use Wardenable;
    public static function boot()
    {
        parent::boot();
        self::creating(function ($jurisdiction) {
            $perm = config('entrust.permission');
            $perm = $perm::create([
                'name' => 'view-'.str_slug($jurisdiction->name),
                'display_name' => 'View '.$jurisdiction->name,
                'description' => 'This permission will let the user view '.strtolower($jurisdiction->name),
            ]);
        });
        self::created(function ($jurisdiction) {
            if (\Auth::check()) {
                if (!auth()->user()->jurisdiction->contains($jurisdiction->id)) {
                    auth()->user()->jurisdiction()->attach($jurisdiction->id);
                }
            } else {
                $user = config('auth.model');
                $admin_user = $user::find(1);
                if(!!$admin_user){
                    $admin_user->jurisdiction()->attach($jurisdiction->id);
                }
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
        'user_id',
    ];

    protected $table = 'dispatch_jurisdiction';

    public function users()
    {
        return $this->belongsToMany(config('auth.model'), 'dispatch_jurisdiction_user', 'jurisdiction_id','user_id');
    }

    public function user(){
        return $this->belongsTo(config('auth.model'));
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
