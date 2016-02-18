<?php

namespace Kregel\Dispatch\Http\Controllers;

use Kregel\Dispatch\Models\Jurisdiction;
use Kregel\FormModel\FormModel;
use Auth;

class TicketsController extends Controller
{

    protected $form;

    protected $ticket;

    protected $jurisdiction;


    /**
     * Mike wants to be able to edit who is assigned to the ticket, and still
     * have them kept in the loop for updates. More or less to let them be able
     * to re assign themselves to another business.
     *
     *
     * If person is jurisdiction  owner they can who in a department gets the
     * ticket to work on. If the person is not the owner, they can assign it
     * to a department, the owner can specify who is in which department, byt
     * it can vary between businesses. Each business needs to be able to have
     * their own departments to throw more things under. If you're department
     * is assign to a ticket you can replace your department with another if
     * you believe your department won't fit the goal of the task desired.
     *
     */
    public function __construct(FormModel $form)
    {
        $this->form         = $form;
        $this->ticket       = config('kregel.dispatch.models.ticket');
        $this->jurisdiction = config('kregel.dispatch.models.jurisdiction');
    }


    public function create($jurisdiction = null)
    {
        if ( ! auth()->user()->can('create-ticket')) {
            return response(view('errors.403')->withMessage('Sorry, but it looks like'), 403);
        }
        $form        = $this->form->using(config('kregel.formmodel.using.framework'))->withModel(new $this->ticket)->submitTo(route('warden::api.create-model',
            [ 'ticket' ]));
        $form_submit = $form->form([
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);
        if (empty( $jurisdiction )) {
            $jurisdictions = auth()->user()->jurisdiction;
            if ($jurisdictions->isEmpty()) {
                return view('dispatch::home')->withErrors([
                    'I can\'t seem to find your jurisdiction... Please contact your administrator.',
                ]);
            }

            return view('dispatch::create.ticket-multilocation')->with([
                'jurisdiction' => $jurisdictions,
                'form'         => $form,
                'form_'        => $form_submit
            ]);
        }
        $jurisdiction = Jurisdiction::whereName($jurisdiction)->get();

        return view('dispatch::create.ticket')->with([
            'jurisdiction' => $jurisdiction->first(),
            'form'         => $form,
            'form_'        => $form_submit
        ]);
    }


    public function viewAll()
    {
        return view('dispatch::view.ticket')->withJurisdictions(\auth()->user()->jurisdiction);
    }


    public function getTicketsForJurisdiction($jurisdiction)
    {
        $jurisdiction = $this->searchJurisdiction($jurisdiction);

        //This line should be limited to admins+ not include contacts / maintence.
        $tickets = auth()->user()->tickets()->where('jurisdiction_id',
            $jurisdiction->id)->orderBy('created_at')->orderBy('priority_id')->paginate(25);

        //grab the user's assigned tickets.
        $tickets_    = auth()->user()->assigned_tickets()->where('jurisdiction_id',
            $jurisdiction->id)->orderBy('created_at')->orderBy('priority_id')->paginate(25);
        $sum_tickets = $tickets->merge($tickets_)->sortBy('created_at')->sortBy('priority_id');

        return view('dispatch::view.ticket')->with(compact('jurisdiction'))->withTickets($tickets);
    }


    private function searchJurisdiction($jur)
    {
        $jur = str_replace('-', '%', '%' . $jur . '%');

        return auth()->user()->jurisdiction()->where('name', 'LIKE', $jur)->first();
    }


    public function getTicketFromJurisdiction($jurisdiction, $id)
    {
        $jurisdiction = $this->searchJurisdiction($jurisdiction);

        //This line should be limited to admins+ not include contacts / maintence.
        $ticket = $this->getUsersTicket($jurisdiction, $id);
        if (empty( $ticket->comments )) {
            return view('dispatch::view.ticket-single-new')->with(compact('jurisdiction'))->withTicket($ticket)->withComments([ ]);
        }
        $comments = $ticket->comments()->orderBy('created_at', 'desc')->get();

        return view('dispatch::view.ticket-single-new')->with(compact('jurisdiction'))->withTicket($ticket)->withComments($comments);
    }


    private function getUsersTicket($jurisdiction, $id)
    {
        return $this->getTickets(true)->whereJurisdictionId($jurisdiction->id)->whereId($id)->first();
    }


    private function getTickets($returnAsQueryBuilder = false)
    {
        if ($returnAsQueryBuilder) {
            return auth()->user()->tickets();
        }

        return auth()->user()->tickets;
    }


    public function getTicketFromJurisdictionForEdit($jurisdiction, $id)
    {
        $jurisdiction = $this->searchJurisdiction($jurisdiction);
        $ticket       = $this->getUsersTicket($jurisdiction, $id);
        $form         = $this->form
                ->using(config('kregel.formmodel.using.framework'))
                ->withModel($ticket)
                ->submitTo(route('warden::api.update-model', [ 'ticket', $ticket->id ]));
        $form_submit  = $form->form([
            'method'  => 'put',
            'enctype' => 'multipart/form-data',
        ]);

        return view('dispatch::edit.ticket')->with([
            'jurisdiction' => $jurisdiction,
            'ticket'       => $ticket,
            'form'         => $form,
            'form_'        => $form_submit
        ]);
    }


    private function getUserTicket($jurisdiction, $id)
    {
        return auth()->user()->tickets()->where('jurisdiction_id', $jurisdiction->id)->whereId($id)->first();
    }
}
