<?php
namespace App\Http\Controllers;

use App\Http\Resources\User as UserResource;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        return $this->respondWithSuccess('User profile found.', ['user' => UserResource::make($user)]);
    }
}