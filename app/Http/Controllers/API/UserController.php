<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response([ 'users' => UserResource::collection($users), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|required|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'password' => 'required'
        ]);

        $validatedData['password'] = bcrypt($request->password);
        $user = User::create($validatedData);
        return response([ 'user' => new UserResource($user), 'message' => 'New user created successfully'], 200);
  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
       
        return response([ 'user' => new UserResource($user), 'message' => 'Show successfully'], 200);
      

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'email' => 'email|unique:users,email,' . $user->id,
            'phone' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
        ]);
        $user->update($request->all());
        return response([ 'user' => new UserResource($user), 'message' => 'Update successfully'], 200);
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user) {
            $user->delete();
            return response(['message' => 'Deleted user id '.$user->id]);
        }
        return response(['message' => 'User not found',400]);
    }
}
