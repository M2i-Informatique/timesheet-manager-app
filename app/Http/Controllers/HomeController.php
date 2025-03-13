<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $assignedProjects = $user->projects()
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        return view('pages.home', compact('assignedProjects'));
    }
}
