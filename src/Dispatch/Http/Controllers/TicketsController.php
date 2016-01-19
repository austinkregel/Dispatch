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
        $form        = $this->form->using(config('kregel.formmodel.using.framework'))->withModel(new $this->ticket())->submitTo(route('warden::api.create-model',
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

            return view('dispatch::create.ticket')->with([
                'jurisdiction' => $jurisdictions->first(),
                'form'         => $form,
                'form_'        => $form_submit
            ]);
        }
        $jurisdiction = Jurisdiction::whereName($jurisdiction)->first();

        return view('dispatch::create.ticket')->with([
            'jurisdiction' => $jurisdiction,
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
            $jurisdiction->id)->orderBy('created_at')->orderBy('priority_id')->get();

        //grab the user's assigned tickets.
        $tickets_    = auth()->user()->assigned_tickets()->where('jurisdiction_id',
            $jurisdiction->id)->orderBy('created_at')->orderBy('priority_id')->get();
        $sum_tickets = $tickets->merge($tickets_)->sortBy('created_at')->sortBy('priority_id');

        return view('dispatch::view.ticket')->with(compact('jurisdiction'))->withTickets($sum_tickets);
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
        $ticket   = $this->getUsersTickets($jurisdiction, $id);
        if (empty($ticket->comments)){
            dd($ticket);
            return view('dispatch::view.ticket-single')->with(compact('jurisdiction'))->withTicket($ticket)->withComments([]);
        }
        $comments = $ticket->comments()->orderBy('created_at', 'desc')->get();

        return view('dispatch::view.ticket-single')->with(compact('jurisdiction'))->withTicket($ticket)->withComments($comments);
    }


    private function getUsersTickets($returnAsQueryBuilder)
    {

        return auth()->user()->tickets;
    }
    private function getUserTicket($jurisdiction, $id){
        return $this->getUsersTickets();
    }


    public function getTicketFromJurisdictionForEdit($jurisdiction, $id)
    {
        $jurisdiction = $this->searchJurisdiction($jurisdiction);
        $form         = $this->form
                                ->using(config('kregel.formmodel.using.framework'))
                                ->withModel(new $this->ticket())
                                ->submitTo(route('warden::api.create-model', [ 'ticket' ]));
        return view('dispatch::create.ticket')->with([
            'jurisdiction' => $jurisdiction,
            'form'         => $form,
            'form_'        => $form->submit()
        ]);
    }


    private function getUserTicket($jurisdiction, $id)
    {
        return auth()->user()->tickets()->where('jurisdiction_id', $jurisdiction->id)->whereId($id)->first();
    }
}
