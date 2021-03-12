<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
	{
		$user = User::all();
		
		return response()->json(['success' => true, 'data' => $user],200);
	}
	
	public function show($id)
	{
		$user = User::find($id);
		
		return response()->json(['success' => true, 'data' => $user],200);
	}
	
	public function store(Request $request)
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

		return response()->json(compact('user'),200);
	}
	
	public function update(Request $request, $id)
    {
		
        $validator = Validator::make($request->all(), [
			'firstname' => 'required|string|max:255',
			'lastname'	=> 'required|string|max:255',
			'age'		=> 'required|integer',
			'gender'	=> 'in:m,f,o',
			'city'		=> 'required',
		]);

		if($validator->fails()){
			return response()->json(['error' => true, 'message' => $validator->errors()->all()], 400);
		}
		
		$checkMobileNo = User::where('mobile',$request->get('mobile'))->where('id','!=',$id)->first();
		if( !is_null($checkMobileNo) )
        {
            return response()->json(['error' => true, 'message' => 'Mobile no already taken by someone!'], 404);
        }
		
		$checkRecord = User::where('id',$id)->first();
		if( is_null($checkRecord) )
        {
            return response()->json(['error' => true, 'message' => 'Record not exist!'], 404);
        }
		
		$data = $checkRecord->update([
			'firstname' => $request->get('firstname'),
			'lastname' 	=> $request->get('lastname'),
			'mobile' 	=> $request->get('mobile'),
			'age' 		=> $request->get('age'),
			'gender' 	=> $request->get('gender'),
			'city'		=> $request->get('city'),
		]);
		
        return response()->json(['success' => true, 'message' => 'User updated successfully', 'data' => $data],200);
    }
	
	public function destroy($id)
    {
        $checkRecord = User::where('id',$id)->first();
		if( is_null($checkRecord) )
        {
            return response()->json(['error' => true, 'message' => 'Record not exist!'], 404);
        }
		
		$data = $checkRecord->delete();
		
        return response()->json(['success' => true, 'message' => 'User deleted successfully', 'data' => $data],200);
    }
}
