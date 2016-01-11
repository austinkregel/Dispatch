<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;
use Kregel\Dispatch\Models\Comments;

class Ticket extends Model
{

	protected $fillable = [
		'title',
		'body',
		'priority_id',
		'owner_id',
		'jurisdiction_id',
		'closer_id',
	];

	protected $table = 'dispatch_tickets';


	public function owner()
	{
		return $this->belongsTo(config('auth.model'));
	}

	public function closer()
	{
		return $this->belongsTo(config('auth.model'));
	}


	public function assigned_to()
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
}
