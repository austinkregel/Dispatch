<?php

namespace Kregel\Dispatch\Http\Controllers;

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
                'method' => 'post',
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
                'form' => $form,
            ]);
        }
        $jurisdiction = Jurisdiction::whereName($jurisdiction)->first();

        return view('dispatch::create.ticket')->with([
            'jurisdiction' => $jurisdiction,
            'form' => $form,
        ]);
    }

    public function viewAll()
    {
        return view('dispatch::view.ticket')->withJurisdictions(\Auth::user()->jurisdiction);
    }
}
