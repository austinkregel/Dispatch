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
    protected $signature = 'dispatch:send-mail {--ticket= : Ticket ID} {--type= : Valid types are [new, assigned, and updates]}';

    /*
     * Have a class wide instance of an array of users. make them unique so no one gets
     * double spammed.
     *
     */
    private $messages = [];

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->info("Firing up");
        if (is_numeric($this->option('ticket'))) {
            $this->info("Finding ticket");
            $this->ticket = Ticket::whereId($this->option('ticket'))->first();
            $this->info("Found ticket");
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
    private function setAssigned($subject, $msg = 'Hey $owner,EOL Just wanted to let you know you have a new ticket.')
    {
        $users = $this->ticket->assign_to->unique();

        if (!empty($users)) {
            foreach ($users as $user) {
                $msg  = [
                    $subject,
                    str_replace('$owner', $user->name, str_replace('EOL', '<br/>', $msg )),
                    config('kregel.dispatch.mail.template.assign.ticket'),
                    [
                        'user' => $user,
                        'ticket' => $this->ticket
                    ]
                ];
                $this->messages[$user->id] = $msg;
            }
        }
    }

    /**
     * Mail the owner of the ticket the information related to this ticket.
     * @param $subject
     * @param string $message
     */
    private function setOwner($subject, $message = 'Hey $owner,EOLJust wanted to let you know you have a new ticket.')
    {
        $user = $this->ticket->owner;
        $message = str_replace('$owner', htmlentities($user->name), str_replace('EOL', '<br/>', $message));
        $msg  = [
            $subject,
            str_replace('$owner', 'Assigned user', str_replace('EOL', '<br/>', $message )),
            config('kregel.dispatch.mail.template.new.ticket'),
            [
                'user' => $user,
                'ticket' => $this->ticket
            ]
        ];
        $this->messages[$user->id] = $msg;
    }

    private function setCommented($subject, $message = ''){
        $users = $this->ticket->assign_to->unique();

        if (!empty($users)) {
            foreach ($users as $user) {

                $msg = [
                    $subject,
                    str_replace('$owner', 'Assigned user', str_replace('EOL', '<br/>', $message )),
                    config('kregel.dispatch.mail.template.new.comment'),
                    [
                        'user' => $user,
                        'ticket' => $this->ticket
                    ],
                ];
                $this->messages[$user->id] = $msg;

            }
        }

    }

    private function sendDahEmails(){
        foreach($this->messages as $message_){
            list($subject, $message, $view, $data) = ($message_);
            $user = $data['user'];
            Mail::queue($view, ['msg' => $message, 'user' => $user], function ($message) use ($subject, $user) {
                $message->subject($subject);
                $message->to($user->email, $user->name);
                $message->from(config('kregel.dispatch.mail.from.address'), config('kregel.dispatch.mail.from.name'));
            });
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
            default:
                $this->error("No type selected {new, assign, update, comment}");
        }
    }

    private function newComment(){
        $this->setOwner('New comment on your ticket!');
        $this->setAssigned('New comment on a ticket you are assigned to!' );
        $this->setCommented('New comment on a ticket you are subscribed to!');
        $this->sendDahEmails();
    }

    private function newTicket()
    {
        $this->setOwner('Ticket affirmation!');
        $this->sendDahEmails();
    }

    private function assignedATicket()
    {
        $this->setOwner('Your Ticket has been reassigned');
        $this->setAssigned('A ticket you are assigned to has been reassigned');
//        $this->oldAssigned('You have been removed from the ticket');
        $this->setCommented('A ticket you are subscribed to has been reassigned');
        $this->sendDahEmails();
    }

    private function updatedTicket()
    {
        $this->setOwner('Your ticket has been updated!');
        $this->setAssigned('A ticket you are assigned to has been updated');
        $this->setCommented('A ticket you are subscribed to has been updated');
//        $this->oldAssigned('You have been removed from the ticket');
        $this->sendDahEmails();
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
