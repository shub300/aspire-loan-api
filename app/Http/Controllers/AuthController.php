<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User as UserResource;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5'
        ];

        $response = $this->validateWithJson($request->all(), $rules);

        if ($response === true) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            return $this->respondWithSuccess('User Registered Successfully.', UserResource::make($user));
        }

        return $this->respondWithError('Validation Error', $response);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $response = $this->validateWithJson($request->all(), $rules);

        // if validation passes, authenticate the user
        if ($response === true) {
            $credentials = $request->only(['email', 'password']);

            if (!$token = auth('api')->attempt($credentials)) {
                return $this->respondWithError('Invalid Login Details.');
            }

            return $this->respondWithSuccess('User Logged In Successfully!', $this->prepareTokenData($token));
        }

        return $this->respondWithError('Validation Error', $response);
    }

    /**
     * Log out user (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $data = auth("api")->user();

        if ($data) {
            auth('api')->logout();

            return $this->respondWithSuccess('User logged out successfully!');
        }
        return $this->respondWithError('Unauthorized access', [], 403);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return Array
     */
    protected function prepareTokenData($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
    }
}
