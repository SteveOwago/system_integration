<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(){
        abort_unless(Gate::allows('admin_management'),403);
        $students = User::role('Student')->get();
        return view('users.index',compact('students'));
    }
}
