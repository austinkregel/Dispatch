<?php

namespace Kregel\Dispatch\Http\Controllers;

use Kregel\Dispatch\Models\Jurisdiction as JurisdictionModel;
use Kregel\FormModel\FormModel;

class JurisdictionController extends Controller
{
    public function __construct(FormModel $form)
    {
        $this->form = $form;
    }

    public function create($jurisdiction = null)
    {
        $form = $this->form->using(config('kregel.formmodel.using.framework'))
                ->withModel(new JurisdictionModel())
                ->submitTo(route('warden::api.create-model', 'jurisdiction'))
                ->form([
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ]);
        if (empty($jurisdiction)) {
            return $this->checkJurisdiction($form);
        }
        $jurisdiction = JurisdictionModel::whereName($jurisdiction)->first();

        return view('dispatch::create.jurisdiction')->with([
                'jurisdiction' => $jurisdiction,
                'form' => $form,
            ]);
    }

    private function checkJurisdiction($form)
    {
        $jurisdictions = auth()->user()->jurisdiction;
        if ($jurisdictions->isEmpty()) {
            return view('dispatch::home')->withErrors([
                'I can\'t seem to find your jurisdiction... Please contact your administrator.',
            ]);
        }

        return view('dispatch::create.jurisdiction')->with([
            'jurisdiction' => $jurisdictions->first(),
            'form' => $form,
        ]);
    }

    public function viewAll()
    {
        return view('dispatch::view.jurisdiction')->withJurisdiction(auth()->user()->jurisdiction);
    }
}
