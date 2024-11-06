<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScoreController extends Controller {
    /**
     * Display a listing of the scores with the given ID.
     * 
     * @param Request $request The request object
     * @return \Illuminate\View\View
     */
    public function show(Request $request) {
        return view('scores.show');
    }

    /**
     * Show the import page for scores.
     * 
     * @return \Illuminate\View\View
     */
    public function import() {
        return view('scores.import');
    }
}
