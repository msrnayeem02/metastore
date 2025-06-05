<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        // Retrieve the latest policy data
        $policy = Policy::latest()->first();

        return view('frontend.privacy-policy.index', compact('policy'));
    }

    public function termsConditions()
    {
        $policy = Policy::latest()->first();
        return view('frontend.terms.index', compact('policy'));
    }
}
