<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        return 'register';
    }
    public function login(Request $request)
    {
        return 'login';
    }
    public function logout(Request $request)
    {
        return 'logout';
    }
}
