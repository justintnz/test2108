<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class UserController extends Controller
{
    const PAGE_SIZE = 20;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate(['page'=>'integer|gt:0']);
        $offset = (isset($request->page))?(intval($request->page-1)* self::PAGE_SIZE):0;
        $users = User::offset($offset)->limit(self::PAGE_SIZE)->get(); 
        return response(['users' => UserResource::collection($users), 'message' => 'Retrieved successfully'], 200);
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

        $validatedData = $request->only(['first_name', 'last_name', 'email', 'phone', 'password', 'is_admin']);
        $validatedData['password'] = bcrypt($request->password);

        // only admin can create new admin
        if (!Auth::user()->is_admin && !empty($validatedData['is_admin'])) {
            return response(['message' => 'Only admin can create new admin'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::create($validatedData);
        return response(['user' => new UserResource($user), 'message' => 'New user created successfully'], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response(['user' => new UserResource($user), 'message' => 'Show successfully'], Response::HTTP_OK);
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
        $request->validate([
            'email' => 'email|unique:users,email,' . $user->id,
            'phone' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'is_admin' => 'boolean',
        ]);


        $validatedData =   $request->only(['first_name', 'last_name', 'email', 'phone', 'password', 'is_admin']);
        // Check if none Admin try to update Admin only fields
        if (!Auth::user()->is_admin) {
            // trying to update other password ?
            if (isset($validatedData['password']) && (Auth::user()->id != $user->id)) {
                return response(['message' => 'Only admin can update password'],  Response::HTTP_UNAUTHORIZED);
            }

            // trying to change admin/none admin role of any user
            if (isset($validatedData['is_admin']) && ($validatedData['is_admin'] != $user->is_admin)) {
                return response(['message' => 'Only admin can update user role'], Response::HTTP_UNAUTHORIZED);
            }
        }

        // encrypt password if needed
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($$validatedData['password']);
        }

        if (count($validatedData) > 0) {
            $user->update($validatedData);
            return response(['user' => new UserResource($user), 'message' => 'Update successfully'], Response::HTTP_ACCEPTED);
        }
        return response(['message' => 'Nothing updated'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (!Auth::user()->is_admin) {
           
            return response(['message' => 'Only admin can delete user'],  Response::HTTP_UNAUTHORIZED);
        }
        $user->delete();
        return response(['message' => 'Deleted user id ' . $user->id], Response::HTTP_ACCEPTED);
    }
}
