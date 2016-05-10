<?php

namespace Kregel\Dispatch\Http\Controllers;

use App\Models\User;
use Kregel\Dispatch\Models\Jurisdiction;
use Kregel\Dispatch\Models\Ticket;
use Illuminate\Http\Request;
class ProfileController extends Controller
{
    private $user;

    public function viewProfile($id, $name = null, Request $request)
    {
        $user_model = config('auth.model');
        $this->user = $user_model::find($id);
        if (empty($this->user)) {
            $user = $user_model::whereName('%' . implode('%', explode(' ', $id)) . '%')->get();
            if ($user->count() > 1) {
                return 'There are too many users with your name. Your user id is incorrect....';
            }
            $user = $user->first();
            dd($user, "User model error: ");
            return redirect(route('dispatch::' . config('kregel.auth-login.profile.route') . '.user', [$user->id, str_slug($user->name)]));
        }
        if (empty($name))
            return redirect(route('dispatch::' . config('kregel.auth-login.profile.route') . '.user', [$this->user->id, str_slug($this->user->name)]));

        //This line should be limited to admins+ not include contacts / maintenance.
        $options = [];

        $tickets = $this->getOpenTickets($options);
        $closed_tickets = $this->getClosedTickets($options);

        $tickets = $tickets->orderBy('priority_id')->paginate(10);
        $jurisdictions = auth()->user()->jurisdiction->intersect($this->user->jurisdiction);
        $closed_tickets = $closed_tickets->orderBy('priority_id')->paginate(10);
        $user = $this->user;
        return view('dispatch::profile.profile', compact('user', 'tickets', 'closed_tickets', 'jurisdictions'));
    }

    private function getOpenTickets($options)
    {
        $location = \Input::get('location');
        if(isset($location)){
            return Ticket::whereNull('deleted_at')
                ->whereNull('closer_id')
                ->whereIn('id', function ($query){
                    $query->from('dispatch_ticket_user')
                        ->select('ticket_id')
                        ->where('user_id', $this->user->id)
                        ->whereIn('jurisdiction_id', function($q) {
                            $q->from('dispatch_jurisdiction')
                                ->select('id')
                                ->where('name', 'like', '%' . implode('%', explode('-', \Input::get('location'))) . '%');
                        });

                })->whereIn('jurisdiction_id', function ($query) {
                    $query->from('dispatch_jurisdiction_user')
                        ->select('jurisdiction_id')
                        ->where('user_id', auth()->user()->id);
                })->orWhere('owner_id', $this->user->id)
                ->whereNull('deleted_at')
                ->whereNull('closer_id')
                ->whereIn('id', function ($query) {
                    $query->from('dispatch_ticket_user')
                        ->select('ticket_id')
                        ->where('user_id', $this->user->id);
                })->whereIn('jurisdiction_id', function ($query) use ($options) {
                    $query->from('dispatch_jurisdiction_user')
                        ->select('jurisdiction_id')
                        ->where('user_id', auth()->user()->id)
                        ->whereIn('jurisdiction_id', function($q) {
                            $q->from('dispatch_jurisdiction')
                                ->select('id')
                                ->where('name', 'like', '%' . implode('%', explode('-', \Input::get('location'))) . '%');
                        });
                });
        }
        return Ticket::whereNull('deleted_at')
            ->whereNull('closer_id')
            ->whereIn('id', function ($query){
                $query->from('dispatch_ticket_user')
                    ->select('ticket_id')
                    ->where('user_id', $this->user->id);
            })->whereIn('jurisdiction_id', function ($query) {
                $query->from('dispatch_jurisdiction_user')
                    ->select('jurisdiction_id')
                    ->where('user_id', auth()->user()->id);
            })->orWhere('owner_id', $this->user->id)
            ->whereNull('deleted_at')
            ->whereNull('closer_id')
            ->whereIn('id', function ($query) {
                $query->from('dispatch_ticket_user')
                    ->select('ticket_id')
                    ->where('user_id', $this->user->id);
            })->whereIn('jurisdiction_id', function ($query) {
                $query->from('dispatch_jurisdiction_user')
                    ->select('jurisdiction_id')
                    ->where('user_id', auth()->user()->id);
            });
    }

    private function getClosedTickets($options)
    {
        $location = \Input::get('location');
        if(isset($location)){
            return Ticket::whereNotNull('deleted_at')
                ->whereIn('id', function ($query) {
                    $query->from('dispatch_ticket_user')
                        ->select('ticket_id')
                        ->where('user_id', $this->user->id);
                })->whereIn('jurisdiction_id', function ($query) {
                    $query->from('dispatch_jurisdiction_user')
                        ->select('jurisdiction_id')
                        ->where('user_id', auth()->user()->id)
                        ->whereIn('jurisdiction_id', function($q) {
                            $q->from('dispatch_jurisdiction')
                                ->select('id')
                                ->where('name', 'like', '%' . implode('%', explode('-', \Input::get('location'))) . '%');
                        });
                })->orWhere('owner_id', $this->user->id)
                ->whereNotNull('deleted_at')
                ->whereIn('id', function ($query) {
                    $query->from('dispatch_ticket_user')
                        ->select('ticket_id')
                        ->where('user_id', $this->user->id);
                })->whereIn('jurisdiction_id', function ($query) {
                    $query->from('dispatch_jurisdiction_user')
                        ->select('jurisdiction_id')
                        ->where('user_id', auth()->user()->id)
                        ->whereIn('jurisdiction_id', function($q) {
                            $q->from('dispatch_jurisdiction')
                                ->select('id')
                                ->where('name', 'like', '%' . implode('%', explode('-', \Input::get('location'))) . '%');
                        });
                });
        }
        return Ticket::whereNotNull('deleted_at')
            ->whereIn('id', function ($query) {
                $query->from('dispatch_ticket_user')
                    ->select('ticket_id')
                    ->where('user_id', $this->user->id);
            })->whereIn('jurisdiction_id', function ($query) {
                $query->from('dispatch_jurisdiction_user')
                    ->select('jurisdiction_id')
                    ->where('user_id', auth()->user()->id);
            })->orWhere('owner_id', $this->user->id)
            ->whereNotNull('deleted_at')
            ->whereIn('id', function ($query) {
                $query->from('dispatch_ticket_user')
                    ->select('ticket_id')
                    ->where('user_id', $this->user->id);
            })->whereIn('jurisdiction_id', function ($query) {
                $query->from('dispatch_jurisdiction_user')
                    ->select('jurisdiction_id')
                    ->where('user_id', auth()->user()->id);
            });
    }
}
