<?php

namespace Kregel\Dispatch\Http\Controllers;

use Illuminate\Http\Request;
use Kregel\Dispatch\Models\Jurisdiction;
use Kregel\Dispatch\Models\Photos;
use Kregel\Dispatch\Models\Ticket;
use Kregel\FormModel\FormModel;
use Kregel\Warden\Http\Controllers\Controller as WController;

class TicketsController extends WController
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
        $this->form = $form;
        $this->ticket = config('kregel.dispatch.models.ticket');
        $this->jurisdiction = config('kregel.dispatch.models.jurisdiction');
    }


    public function create($jurisdiction = null)
    {
        if (!auth()->user()->can('create-ticket')) {
            return response(view('errors.403')->withMessage('Sorry, but it looks like you don\'t have permission to view this'), 403);
        }
        $form = $this->form->using(config('kregel.formmodel.using.framework'))->withModel(new $this->ticket)->submitTo(route('warden::api.create-model',
            ['ticket']));
        $form_submit = $form->form([
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);
        if (empty($jurisdiction)) {
            if (auth()->user()->can_assign())
                $jurisdictions = Jurisdiction::all();
            else
                $jurisdictions = auth()->user()->jurisdiction;
            if ($jurisdictions->isEmpty()) {
                return view('dispatch::home')->withErrors([
                    'I can\'t seem to find your jurisdiction... Please contact your administrator.',
                ]);
            }

            return view('dispatch::create.ticket')->with([
                'jurisdictions' => $jurisdictions,
                'form' => $form,
                'form_' => $form_submit
            ]);

        }
        $jurisdiction = $this->searchJurisdiction($jurisdiction);
        if(auth()->user()->jurisdiction->contains('id',$jurisdiction->id) || auth()->user()->hasRole('developer')){
            return view('dispatch::create.ticket')->with([
                'jurisdiction' => $jurisdiction,
                'form' => $form,
                'form_' => $form_submit
            ]);
        }
        return abort(404, 'This is not the page you are looking for...');
    }

    private function searchJurisdiction($jur)
    {
        $jur = str_replace('-', '%', '%' . $jur . '%');
        if (auth()->user()->can_assign())
            return Jurisdiction::where('name', 'LIKE', $jur)->first();
        $jurisdiction = auth()->user()->jurisdiction()->where('name', 'LIKE', $jur)->first();
        if($jurisdiction)
            return $jurisdiction;
        abort(404, 'I can not find that location for you...');
    }

    public function viewAll()
    {
        if(auth()->user()->jurisdiction->count() === 1){
            return redirect(route('dispatch::view.ticket', str_slug(auth()->user()->jurisdiction->first()->name)),302);
        }
        return view('dispatch::view.ticket')->withJurisdictions(auth()->user()->jurisdiction);
    }

    public function getTicketsForJurisdiction($jurisdiction)
    {
        $jurisdiction = $this->searchJurisdiction($jurisdiction);
        if (empty($jurisdiction)) {
            return abort(404, 'This is not the page you are looking for...');
        }
        if(auth()->user()->jurisdiction->contains('id',$jurisdiction->id) || auth()->user()->hasRole('developer')){

            //This line should be limited to admins+ not include contacts / maintence.
            $tickets = Ticket::where('jurisdiction_id', $jurisdiction->id)
                ->where('deleted_at', null)
                ->orderBy('priority_id')->orderBy('created_at')->paginate(25);
            return view('dispatch::view.ticket')->with(compact('jurisdiction'))->withTickets($tickets);
        }
        return abort(404, 'This is not the page you are looking for...');
    }

    public function getTicketFromJurisdiction($jurisdiction, $id)
    {
        $jurisdiction = $this->searchJurisdiction($jurisdiction);
        if(auth()->user()->jurisdiction->contains('id',$jurisdiction->id) || auth()->user()->hasRole('developer')){

            //This line should be limited to admins+ not include contacts / maintence.
            $ticket = $this->getUsersTicket($jurisdiction, $id);

            if (empty($ticket->comments)) {
                return view('dispatch::view.ticket-single-new')->with(compact('jurisdiction'))->withTicket($ticket)->withComments([]);
            }
            $comments = $ticket->comments()->orderBy('created_at', 'asc')->get();
            return view('dispatch::view.ticket-single-new')->with(compact('jurisdiction'))->withTicket($ticket->orderBy('priority_id'))->withComments($comments);
        }
        return abort(404, 'This is not the page you are looking for...');
    }


    private function getUsersTicket($jurisdiction, $id)
    {
        if(auth()->user()->jurisdiction->contains('id',$jurisdiction->id) || auth()->user()->hasRole('developer')){
            return Ticket::whereJurisdictionId($jurisdiction->id)->whereId($id)->first();
        }
        return abort(404, 'This is not the page you are looking for...');
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
        $ticket = $this->getUsersTicket($jurisdiction, $id);
        $form = $this->form
            ->using(config('kregel.formmodel.using.framework'))
            ->withModel($ticket)
            ->submitTo(route('warden::api.update-model', ['ticket', $ticket->id]));
        $form_submit = $form->form([
            'method' => 'put',
            'enctype' => 'multipart/form-data',
        ]);

        return view('dispatch::edit.ticket')->with([
            'jurisdiction' => $jurisdiction,
            'ticket' => $ticket,
            'form' => $form,
            'form_' => $form_submit
        ]);
    }

    public function getClosedTicketsFromJurisdiction($jurisdiction)
    {
        $jurisdiction = $this->searchJurisdiction($jurisdiction);
        $tickets = $jurisdiction->tickets()->whereRaw('deleted_at is not null')->orderBy('priority_id')->orderBy('created_at')->get();
        return view('dispatch::view.ticket', compact('tickets', 'jurisdiction'));
    }

    public function postTicketCreate($id, Request $request)
    {
        $this->validate($request, [
            'photo' => 'mimes:jpg,jpeg,png,pdf,gif'
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $ext = strtolower($file->getClientOriginalExtension());
            $uuid = uuid(openssl_random_pseudo_bytes(16));
            $name = $uuid . '.' . $ext;
            $file->move(storage_path(config('kregel.dispatch.storage_path')), $name);

            $file_path = config('kregel.dispatch.storage_path') . $name;
            switch ($ext) {
                case 'png':
                case 'jpg':
                case 'jpeg':
                    $type = 'image';
                    break;
                case 'pdf':
                    $type = 'doc';
                    break;
                default:
                    $type = 'unknown';
            }
            Photos::create([
                'path' => $file_path,
                'uuid' => $uuid,
                'ticket_id' => $id,
                'user_id' => auth()->user()->id,
                'type' => $type
            ]);

            return response()->json([
                'message' => 'Upload was successful',
                'code' => 202
            ]);
        }

        return response()->json([
            'message' => 'No file was found',
            'code' => 422
        ], 422);
    }

    public function post($route, $postData = [])
    {
        list($params, $data) = $postData;
        $route = $this->parseRoute($route, $params);
        $request = Request::create($route, 'POST', $data);
        $response = Route::dispatch($request);

        return $response;
    }
}
