<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
         public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        return response()->json($user, 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $user = User::find($id);
        // $user = new ShowUserResource($user);
        // return response()->json([
        //     "message" => "Get User Successfully",
        //     "data" => $user,
        // ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
                $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,'.$id,
        ]);

        $user->update($data);

        return response()->json($user);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $user = User::findOrFail($id);  
    $user->delete();
    return response()->json([
        'message' => 'User deleted successfully',
    ]);
}

}
