<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VotingContest;

class HomeController extends Controller
{
    public function index()
    {


        return view('welcome', [

            'title' => 'EventSphere - Your Event Universe'
        ]);
    }
}
