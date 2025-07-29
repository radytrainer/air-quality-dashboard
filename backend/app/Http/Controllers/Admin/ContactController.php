<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // List all contacts, paginated
    public function index()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($contacts);
    }

    // Optional: show a single contact
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json($contact);
    }

    // Optional: mark as replied or add admin reply, etc.
}
