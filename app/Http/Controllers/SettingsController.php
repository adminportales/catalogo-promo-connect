<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function setRoles()
    {
        $users = User::all();
        $userRole = Role::where('display_name', 'usuario')->first();
        foreach ($users as $user) {
            $user->attachRole($userRole);
        }
    }
}
