<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;

class Jurisdiction extends Model
{
    use Wardenable;

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
