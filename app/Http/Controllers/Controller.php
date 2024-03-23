<?php

namespace App\Http\Controllers;

use App\Helpers\K;
use App\Models\User;
use App\Helpers\Lists;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController {
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get the dashboard page.
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard() {
        return view('dashboard');
    }

    /**
     * Get the map test page.
     * 
     * @return \Illuminate\View\View
     */
    public function map() {
        return view('map');
    }

    /**
     * Get the privacy policy page.
     * 
     * @return \Illuminate\View\View
     */
    public function privacyPolicy() {
        return view('legal.privacy-policy');
    }

    /**
     * Get the terms and conditions page.
     * 
     * @return \Illuminate\View\View
     */
    public function termsAndConditions() {
        return view('legal.terms-and-conditions');
    }
}
