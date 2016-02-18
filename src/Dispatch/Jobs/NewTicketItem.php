<?php

namespace Kregel\Dispatch\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class NewTicketItem
{

    use InteractsWithQueue, SerializesModels;

    protected $user;

    protected $ticket;


    public function __construct($user, $ticket)
    {
        $this->user   = $user;
        $this->ticket = $ticket;
    }


    public function handle()
    {
        $view = empty( config('kregel.dispatch.mail.template.new.user') ) ? 'dispatch::mail.new.user' : config('kregel.dispatch.mail.template.new.user');
        Mail::send($view, [ 'user' => $this->user, 'ticket' => $this->ticket ], function ($message) {
            list( $from_addr, $from_name, $subject, $reply_to, $user_email, $user_name ) = $this->getConfigurationInfo();
            $message->from($from_addr, $from_name);
            $message->to($this->user->{$user_email}, $this->fixUserName($user_name)); // Fix the config based username, yea, it's messing but it's nice :P

            if ( ! empty( $subject )) {
                $message->subject($subject);
            }
            if ( ! empty( $reply_to )) {
                $message->replyTo($reply_to);
            }
        });
    }


    private function getConfigurationInfo()
    {
        return [
            empty( config('mail.from.address') ) ? config('kregel.dispatch.mail.from.address') : config('mail.from.address'),
            empty( config('mail.from.name') ) ? config('kregel.dispatch.mail.from.name') : config('mail.from.address'),
            empty( config('kregel.dispatch.mail.subject.new.user') ) ? 'Thank you for joining!' : config('kregel.dispatch.mail.subject.new.user'),
            empty( config('kregel.dispatch.mail.reply_to') ) ? null : config('kregel.dispatch.mail.reply_to'),
            empty( config('kregel.dispatch.user.email') ) ? 'email' : config('kregel.dispatch.user.email'),
            empty( config('kregel.dispatch.user.name') ) ? 'name' : config('kregel.dispatch.user.email'),
        ];
    }


    private function fixUserName($username)
    {
        $fixed_username = '';
        if (is_array($username)) {
            foreach ($username as $attribute) {
                $fixed_username .= $this->user->{$attribute};
            }
        } elseif (is_object($username)) {
            return $this->fixUserName(json_decode(json_encode($username), true));
        } else {
            $fixed_username = $this->user->$username;
        }

        return $fixed_username;
    }

}