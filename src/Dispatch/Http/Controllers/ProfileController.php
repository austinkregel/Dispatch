<?php

namespace Kregel\Dispatch\Http\Controllers;

class ProfileController extends Controller
{
    public function viewProfile($id,$name){
        $user_model = config('auth.model');
        $user = $user_model::find($id);
        if (empty($user))
        {
            $user = $user_model::whereName('%'.implode('%', explode($name, ' ')).'%')->get();
            if($user->count() > 1)
            {
                return 'There are too many users with your name. Your user id is incorrect....';
            }
        }
        //This line should be limited to admins+ not include contacts / maintence.
        $tickets = $user->tickets()->orderBy('created_at')->limit(5)->get();
        //grab the user's assigned tickets.
        $tickets_ = $user->assigned_tickets()->orderBy('created_at')->limit(5)->get();
        $sum_tickets = $tickets->merge($tickets_)->sortBy('created_at');

        return view('dispatch::profile.profile', ['user' => $user, 'tickets'=>$sum_tickets]);
    }
}