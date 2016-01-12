<?php

namespace Kregel\Dispatch\Models;

 use Illuminate\Database\Eloquent\Model;
use Kregel\Warden\Traits\Wardenable;
use Kregel\Dispatch\Models\Comments;

class Ticket extends Model
{

	public static function boot(){
		parent::boot();
	}

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
	public function adjustments()
	{
		return $this->belongsToMany(config('auth.model'), 'dispatch_ticket_edits')
			->withTimestamps()
			->withPivot(['before','after','hash'])
			->latest('pivot_updated_at');
	}

	public function adjust($userId = null, $diff = null){
		$userId = $userId ?:auth()->user()->id;
		$diff = $diff?:$this->getDiff();
		return $this->adjustments()->attach($userId, $diff);
	}

	public function getDiff()
	{
		$changed = $this->getDirty();
		$before = json_encode(array_intersect($this->fresh()->toArray(), $changed));
		$after = json_encode($changed);
		$hash = sha1($this);
		return compact ('before', 'after', 'hash');
	}
}
