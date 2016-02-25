<?php

namespace Kregel\Dispatch\Commands;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kregel\Dispatch\Models\Priority;
use Kregel\Dispatch\Models\Ticket;
use Mail;

class SendEmails extends Command implements SelfHandling
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'dispatch:send-mail' . ' {--ticket= : Ticket ID} {--type= : Valid types are [new, assigned, and updates]}';

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
    protected $description = 'Command for sending emails about a ticket';

    /*
     * Custom Vars
     */
    private $ticket;
    private $type;

    /**
     * @param Collection $tickets All applicable tickets.
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if (is_numeric($this->option('ticket'))) {
            $this->ticket = Ticket::find($this->option('ticket'));
            $this->jumpThroughTickets();
        } else {
            $this->error('You didn\'t declare a valid ticket id');
        }
    }

    /**
     * Mail those assigned to the ticket the information related to this ticket.
     * @param $subject
     * @param string $message
     */
    private function assigned($subject, $msg = 'Hey $owner,EOL Just wanted to let you know you have a new ticket.')
    {
        $users = $this->ticket->assign_to->unique();
        $msg  = [
            'msg' => str_replace('$owner', 'Assigned user', str_replace('EOL', '<br/>', $msg ))
        ];
        if (!empty($users)) {
            foreach ($users as $user) {
                Mail::queue('dispatch::email.new-ticket', $msg + ['user' => $user] , function ($message) use ($subject, $user) {
                    $message->subject($subject);
                    $message->to($user->email, $user->name);
                });
            }
        }
    }

    /**
     * Mail the owner of the ticket the information related to this ticket.
     * @param $subject
     * @param string $message
     */
    private function owner($subject, $message = 'Hey $owner,EOL Just wanted to let you know you have a new ticket.')
    {
        $user = $this->ticket->owner;
        $message = str_replace('$owner', htmlentities($user->name), str_replace('EOL', '<br/>', $message));
        $msg  = [
            'msg' => str_replace('$owner', 'Assigned user', str_replace('EOL', '<br/>', $message )),
            'user' => $user
        ];

        Mail::queue('dispatch::email.new-ticket', $msg, function ($message) use ($subject, $user) {
            $message->subject($subject);
            $message->to($user->email, $user->name);
        });
    }

    private function commented($subject, $msg = ''){
        $users = $this->ticket->assign_to->unique();
        $msg  = [
            'msg' => str_replace('$owner', 'Assigned user', str_replace('EOL', '<br/>', $msg ))
        ];
        if (!empty($users)) {
            foreach ($users as $user) {
                Mail::queue('dispatch::email.new-ticket', $msg + ['user' => $user] , function ($message) use ($subject, $user) {
                    $message->subject($subject);
                    $message->to($user->email, $user->name);
                });
            }
        }

    }
    /**
     * This will do the needed matching for the type of ticket creation and the
     * proper function to execute that type of ticket.
     */
    private function jumpThroughTickets()
    {
        switch (strtolower($this->option('type'))) {
            case 'new':
                $this->newTicket();
                break;
            case 'assign':
                $this->assignedATicket();
                break;
            case 'update':
                $this->updatedTicket();
                break;
            case 'comment':
                $this->newComment();
                break;
        }
    }

    private function newComment(){
        $this->owner('New comment on your ticket!');
        $this->assigned('New comment on a ticket you are assigned to!' );
        $this->commented('New comment on a ticket you are subscribed to!');
    }

    private function newTicket()
    {
        $this->owner('Ticket affirmation!');
        $this->assigned('You have been assigned a ticket');
    }

    private function assignedATicket()
    {
        $this->owner('Your Ticket has been reassigned');
        $this->assigned('A ticket you are assigned to has been reassigned');
//        $this->oldAssigned('You have been removed from the ticket');
        $this->commented('A ticket you are subscribed to has been reassigned');
    }

    private function updatedTicket()
    {
        $this->owner('Your ticket has been updated!');
        $this->assigned('A ticket you are assigned to has been updated');
        $this->commented('A ticket you are subscribed to has been updated');
//        $this->oldAssigned('You have been removed from the ticket');

    }

    /**
     * @param array $for
     * @param Model $in
     * @param bool $returnQuery
     *
     * @return Collection|QueryBuilder
     */
    private function search(array $for, Model $in, $returnQuery = false)
    {
        $results = [];
        $queryBuilders = [];
        $query = null;
        foreach ($for as $fields) {
            if ($query === null) {
                $query = $this->where($in, $fields);
            } else {
                $query = $this->where($query, $fields);
            }
        }
        if ($returnQuery) {
            return $query;
        }
        foreach ($queryBuilders as $query) {
            $results[] = $query->get();
        }

        return collect($results);
    }


    /**
     * @param $query
     * @param $where
     *
     * @return QueryBuilder
     */
    private function where($query, $where)
    {
        list($field, $relation, $value) = $where;

        return $query->where($field, $relation, $value);
    }


    /**
     * @param       $where
     * @param array $dontStrip
     *
     * @return string
     */
    private function searchableWhere($where, $dontStrip = [])
    {
        return '%' . (str_replace(' ', '%', preg_replace('/[^a-z0-9' . implode('', $dontStrip) . ']+/', ' ', $where))) . '%';
    }
}