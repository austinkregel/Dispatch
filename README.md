#What is this?
This currently is a support ticketing system that is under development. 

#What's with the commit comments?

Like I just said, it's currently under rapid development, and I am using git/github as a way to backup my code. I'm not using git/github to help document my changes (even though I should be.)

Your user model must have 

```php
    public function closed_tickets(){
        return $this->hasMany(Ticket::class, 'closer_id');
    }
    
    public function tickets(){
        return $this->hasMany(Ticket::class, 'owner_id');
    }
    
    public function assigned_tickets(){
        return $this->belongsToMany(Ticket::class, 'dispatch_ticket_user', 'user_id', 'ticket_id');
    }
```
