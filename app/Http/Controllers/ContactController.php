<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        try {
            // Contact::create([
            //     'shop_name' => $shop_name,
            //     'name' => $validated['name'],
            //     'email' => $validated['email'],
            //     'subject' => $validated['subject'],
            //     'message' => $validated['message']
            // ]);

            return back()->with('success', 'Thank you for your message. We will get back to you soon!');
        } catch (\Exception $e) {
            return back()->with('error', 'Sorry, something went wrong. Please try again later.')
                ->withInput();
        }
    }
}
