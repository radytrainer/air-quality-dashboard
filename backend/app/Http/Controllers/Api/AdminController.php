<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
       
        $totalUsers = User::count();
        $admins = User::where('role', 'admin')->count();
        $users = User::where('role', 'user')->count();

        return response()->json([
            'dashboard' => 'Admin Dashboard',
            'total_users' => $totalUsers,
            'total_admins' => $admins,
            'total_users_only' => $users
        ]);
    }
}
