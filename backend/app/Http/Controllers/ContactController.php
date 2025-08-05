<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'          => 'required|string|max:255',
            'email'              => 'required|email',
            'phone_number'       => 'required|string|max:20',
            'purpose_of_contact' => 'nullable|string|max:255',
            'organisation'       => 'nullable|string|max:255',
            'message'            => 'required|string',
        ]);

        Contact::create($validated);

        return response()->json(['message' => 'Contact saved successfully'], 201);
    }
}
