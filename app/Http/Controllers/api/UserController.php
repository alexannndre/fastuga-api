<?php

namespace App\Http\Controllers\api;

use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserPostRequest;
use App\Http\Requests\User\UserPutRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allUsers()
    {
        return UserResource::collection(User::all());
    }

    public function showMe(Request $request)
    {
        return new UserResource($request->user());
    }

    public function store(UserPostRequest $request)
    {
        $newUser = $request->validated();

        $regUser = UserHelper::registerUser($request, $newUser);

        return response(["message" => "User created", "user" => new UserResource($regUser)]);
    }

    public function update(UserPutRequest $request, User $user)
    {
        $newUser = $request->validated();

        UserHelper::updateUser($request, $newUser, $user);

        return response(['message' => 'User updated']);
    }

    public function updateMe(UserPutRequest $request, User $user)
    {
        if ($user->type == 'C')
            return response(['message' => 'To update your account as a customer please use the ' . route('update-customer-profile') . ' route'], 403);
        if ($user->id !== $request->user()->id)
            return response(['message' => 'You can only update your own account'], 403);
        return $this->update($request, $user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response(['message' => 'User deleted']);
    }

    public function block(User $user)
    {
        if ($user->blocked)
            return response(['message' => 'That user is already blocked'], 400);

        $user->blocked = true;

        foreach ($user->tokens as $token)
            $token->revoke();

        $user->save();
        return response(['message' => 'User blocked']);
    }

    public function unblock(User $user)
    {
        if (!$user->blocked)
            return response(['message' => 'That user is not blocked'], 400);
        $user->blocked = false;
        $user->save();
        return response(['message' => 'User unblocked']);
    }

    /*public function isMyEmailVerified(Request $request)
    {
        if ($request->user()->email_verified_at)
            return response(['status' => true, 'message' => 'User\'s email is verified', 'email_verified_at' => $request->user()->email_verified_at]);
        return response(['status' => false, 'message' => 'User\'s email is not verified']);
    }*/

    public function verifyMyEmail(Request $request)
    {
        if ($request->user()->email_verified_at)
            return response(['message' => 'User\'s email is already verified'], 400);
        $request->user()->sendEmailVerificationNotification();
        return response(['message' => 'Verification email sent']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|between:6,128',
            'new_password' => 'required|string|confirmed|between:6,128',
        ]);

        //Check if old password is correct
        if (!Hash::check($request->old_password, $request->user()->password))
            return response(['message' => 'Current password is incorrect'], 401);

        $user = $request->user();
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response(['message' => 'Password changed']);
    }

    public function changeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $user = $request->user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();

        //$user->sendEmailVerificationNotification();

        return response(['message' => 'Email changed! Verification email sent']);
    }
}
