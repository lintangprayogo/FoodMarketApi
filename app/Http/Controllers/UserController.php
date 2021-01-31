<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    public function index()
    {
        $users = User::paginate(10);

        return view('user.index', [
            'users' => $users
        ]);
    }


    public function create()
    {
        return view('user.create');
    }


    public function store(UserRequest $request)
    {
        $data = $request->all();
        if ($request->file('picturePath')) {
            $data['picturePath'] = $request->file('picturePath')->store('assets/user', 'public');
        }
        $data['password']=Hash::make($data['password']);
        $user = User::create($data);
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => $user->name . " Teams",
            'personal_team' => false
        ]));
        return redirect()->route('user.index');
    }


    public function show($id)
    {
    }


    public function edit($id)
    {
        $user = User::find($id);
        return view('user.edit', ['user' => $user]);
    }

    public function update(Request $request, $id)
    {
    }


    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('user.index');
    }
}
