<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    // List all favourites for the logged-in user
    public function index()
    {
        $user = Auth::user();
        return response()->json(Favourite::where('user_id', $user->id)->get());
    }

    // Add a favourite city
    public function store(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string',
            'country_code' => 'required|string|max:2',
        ]);

        $user = Auth::user();

        $favourite = Favourite::firstOrCreate(
            [
                'user_id' => $user->id,
                'city_name' => $request->city_name
            ],
            [
                'country_code' => $request->country_code
            ]
        );

        return response()->json($favourite, 201);
    }

    // Remove a favourite city
    public function destroy($city_name)
    {
        $user = Auth::user();

        $deleted = Favourite::where('user_id', $user->id)
            ->where('city_name', $city_name)
            ->delete();

        return response()->json(['deleted' => $deleted]);
    }
}
