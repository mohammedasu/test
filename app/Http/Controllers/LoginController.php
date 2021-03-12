<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function authenticate(Request $request)
	{
		$credentials = $request->only('email', 'password');

		try {
			
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => true, 'message' => 'invalid credentials'], 400);
			}
			
		} catch (JWTException $e) {
			
			return response()->json(['error' => true, 'message' => 'Could not create token'], 500);
			
		}

		return response()->json(compact('token'),200);
	}

	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'firstname' => 'required|string|max:255',
			'lastname'	=> 'required|string|max:255',
			'mobile' 	=> 'required|digits:10|unique:users,mobile',
			'email' 	=> 'required|string|email|max:255|unique:users',
			'age'		=> 'required|integer',
			'gender'	=> 'in:m,f,o',
			'city'		=> 'required',
			'password'	=> 'required|string|min:6|confirmed',
		]);

		if($validator->fails()){
			return response()->json(['error' => true, 'message' => $validator->errors()->all()], 400);
		}

		$user = User::create([
			'firstname' => $request->get('firstname'),
			'lastname' 	=> $request->get('lastname'),
			'mobile' 	=> $request->get('mobile'),
			'email' 	=> $request->get('email'),
			'age' 		=> $request->get('age'),
			'gender' 	=> $request->get('gender'),
			'city'		=> $request->get('city'),
			'password' 	=> Hash::make($request->get('password')),
		]);

		$token = JWTAuth::fromUser($user);

		return response()->json(compact('user','token'),200);
	}

	public function getAuthenticatedUser()
	{
		try {
			
			if (! $user = JWTAuth::parseToken()->authenticate()) {
					return response()->json(['error' => true, 'message' => 'User not found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

				return response()->json(['error' => true, 'message' => 'Token expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

				return response()->json(['error' => true, 'message' => 'Token invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

				return response()->json(['error' => true, 'message' => 'Token absent'], $e->getStatusCode());

		}

		return response()->json(compact('user'),200);
	}

}
