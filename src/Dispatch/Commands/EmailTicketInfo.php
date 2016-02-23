<?php

namespace Kregel\Dispatch\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kregel\Dispatch\Models\Priority;
use Kregel\Dispatch\Models\Ticket;

class EmailTicketInfo extends Command implements SelfHandling
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dispatch:ticket-stats' . ' {--fake= : Whether emails should be sent updating the user on the ticket statuses}';

    /**
     * This value is the difference between emailing all your clients at a random
     * time and just checking if the command works.
     * @var bool
     */
    protected $is_fake;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for checking all the tickets involved with our databases';

    protected $ticket;
    protected $users;

    public function __construct()
    {
        parent::__construct();
        $this->ticket = new Ticket;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->users = $this->getUsersInvolved();
        dd($this->users);
    }


    public function getUsersInvolved()
    {
        $users = [ ];
        if($this->ticket->assign_to->toArray() !== null) {
            $users = $users + $this->ticket->assign_to->toArray();
        }
        $users[] = $this->ticket->owner;
        foreach ($this->ticket->comments as $comment) {
            $users[] = $comment->user;
        }
        return collect($users);
    }

    public function mailUsersInvolved(){
        $users = $this->getUsersInvolved();
        if($users->isEmpty()){
            
            return false;
        }
    }
}
