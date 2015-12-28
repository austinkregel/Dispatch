<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;

class Comments extends Model
{
	use Wardenable;

	protected $fillable = [
		'body',
		'post_date',
		'user_id',
		'ticket_id'
	];

	protected $table = 'dispatch_jurisdiction';

	public function users()
	{
		return $this->belongsToMany(\App\Models\User::class, 'dispatch_jurisdiction_user', 'user_id', 'jurisdiction_id');
	}

	public function tickets()
	{
		return $this->belongsTo(Ticket::class);
	}
}
