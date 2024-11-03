<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WizardController extends Controller {
    /**
     * Show the wizard to the user
     * 
     * @param Request $request
     */
    public function show(Request $request) {
        $user = $request->user();

        if ($user->hasDSP())
            return redirect('/');

        return view('wizard.show', ['user' => $user]);
    }
}
