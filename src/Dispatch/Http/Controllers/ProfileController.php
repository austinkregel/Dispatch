<?php

namespace Kregel\Dispatch\Http\Controllers;

class ProfileController extends Controller
{
    public function viewProfile($id, $name = null)
    {
        $user_model = config('auth.model');
        $user = $user_model::find($id);
        if (empty($user)) {
            $user = $user_model::whereName('%'.implode('%', explode(' ',$id)).'%')->get();
            if ($user->count() > 1) {
                return 'There are too many users with your name. Your user id is incorrect....';
            }
            $user = $user->first();
            dd($user);
            return redirect(route('dispatch::'.config('kregel.auth-login.profile.route').'.user', [$user->id, str_slug($user->name)]));
        }
        if(empty($name))
            return redirect( route('dispatch::'.config('kregel.auth-login.profile.route').'.user', [$user->id, str_slug($user->name)]));

        //This line should be limited to admins+ not include contacts / maintence.
        $tickets = $user->tickets()->orderBy('created_at')->limit(10)->get();
        //grab the user's assigned tickets.
        $tickets_ = $user->assigned_tickets()->orderBy('created_at')->limit(10)->get();
        $sum_tickets = $tickets->merge($tickets_)->sortBy('created_at');
        return view('dispatch::profile.profile', ['user' => $user, 'tickets' => $sum_tickets]);
    }
}
