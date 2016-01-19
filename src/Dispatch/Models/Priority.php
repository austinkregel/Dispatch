<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kregel\Warden\Traits\Wardenable;

class Priority extends Model
{
    use Wardenable;

    protected $fillable = [
        'name',
        'deadline',
    ];

    protected $table = 'dispatch_priority';

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
