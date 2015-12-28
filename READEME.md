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