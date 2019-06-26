<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $user = User::create([
             'email'    => $request->email,
             'password' => $request->password,
         ]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // return $this->respondWithToken($token);
        return $this->getTokenFromUserObject();
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            // 'token_type'   => 'bearer',
            // 'expires_in'   => auth('api')->factory()->getTTL() * 60
            'name' => User::first()->name 
        ]);
    }




    public function getTokenFromUserObject(){
        $user = User::first();
        $token = JWTAuth::fromUser($user);
        return $this->respondWithToken($token);
    }

    public function getTokenFromOtherAttributes(){

        $customClaims = ['foo' => User::first()->email, 'baz' => User::first()->id];
 
        $payload = JWTFactory::make($customClaims);
         
        $token = JWTAuth::encode($payload);

        return $this->respondWithToken($token->get());

        }
}
