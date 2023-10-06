<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function checkServer()
    {
        return response()->json(['status' => 'Server is online']);
    }
}
