<?php

namespace Kregel\Dispatch\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;
use Kregel\Dispatch\Models\Comments;

class Ticket extends Model
{

	use Wardenable;

	protected $fillable = [
		'title',
		'body',
		'priority',
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


	public function assignedTo()
	{
		return $this->hasMany(config('auth.model'));
	}


	public function jurisdiction()
	{
		return $this->belongsTo(Jurisdiction::class);
	}


	public function comments()
	{
		return $this->hasMany();
	}

}
