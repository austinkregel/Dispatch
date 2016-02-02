<?php

namespace Kregel\Dispatch\Http\Controllers;

use Auth;
use Kregel\Dispatch\Models\Jurisdiction;
use Kregel\FormModel\FormModel;

class TicketsController extends Controller
{
    protected $form;
    protected $ticket;
    protected $jurisdiction;

    public function __construct(FormModel $form)
    {
        $this->form = $form;
        $this->ticket = config('kregel.dispatch.models.ticket');
        $this->jurisdiction = config('kregel.dispatch.models.jurisdiction');
    }

    public function create($jurisdiction = null)
    {
        if (!auth()->user()->can('create-ticket')) {
            return response(view('errors.403')->withMessage('Sorry, but it looks like'), 403);
        }
        $form = $this->form->using(config('kregel.formmodel.using.framework'))
            ->withModel(new $this->ticket())
            ->submitTo(route('warden::api.create-model', ['ticket']))
            ->form([
                'method'  => 'post',
                'enctype' => 'multipart/form-data',
            ]);
        if (empty($jurisdiction)) {
            $jurisdictions = auth()->user()->jurisdiction;
            if ($jurisdictions->isEmpty()) {
                return view('dispatch::home')->withErrors([
                    'I can\'t seem to find your jurisdiction... Please contact your administrator.',
                ]);
            }

            return view('dispatch::create.ticket')->with([
                'jurisdiction' => $jurisdictions->first(),
                'form'         => $form,
            ]);
        }
        $jurisdiction = Jurisdiction::whereName($jurisdiction)->first();

        return view('dispatch::create.ticket')->with([
            'jurisdiction' => $jurisdiction,
            'form'         => $form,
        ]);
    }

    public function viewAll()
    {
        return view('dispatch::view.ticket')
                    ->withJurisdictions(\auth()->user()->jurisdiction);
    }

    public function getTicketsForJurisdiction($jurisdiction)
    {
        $jur = str_replace('-', '%', '%'.$jurisdiction.'%');
        $jurisdiction = auth()->user()->jurisdiction()->where('name', 'LIKE', $jur)->first();

        //This line should be limited to admins+ not include contacts / maintence.
        $tickets = auth()->user()->tickets()->where('jurisdiction_id', $jurisdiction->id)->orderBy('created_at')->orderBy('priority_id')->get();

        //grab the user's assigned tickets.
        $tickets_ = auth()->user()->assigned_tickets()->where('jurisdiction_id', $jurisdiction->id)->orderBy('created_at')->orderBy('priority_id')->get();
        $sum_tickets = $tickets->merge($tickets_)->sortBy('created_at')->sortBy('priority_id');

        return view('dispatch::view.ticket')->with(compact('jurisdiction'))->withTickets($sum_tickets);
    }

    public function getTicketFromJurisdiction($jurisdiction, $id)
    {
        $jur = str_replace('-', '%', '%'.$jurisdiction.'%');
        $jurisdiction = auth()->user()->jurisdiction()->where('name', 'LIKE', $jur)->first();

        //This line should be limited to admins+ not include contacts / maintence.
        $ticket = auth()->user()->tickets()->where('jurisdiction_id', $jurisdiction->id)->whereId($id)->first();
        $comments = $ticket->comments()->orderBy('created_at', 'desc')->get();

        return view('dispatch::view.ticket-single')->with(compact('jurisdiction'))->withTicket($ticket)->withComments($comments);
    }
}
