<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getAllUserData()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function saveUserData(Request $request)
    {

        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email:rfc,dns',
                'phone' => ['required', 'regex:/^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)?[789]\d{9}$/'],
                'description' => 'required',
                'role_id' => 'required',
            ]
        );
    
        if ($validateUser->fails()) {
            return response()->json(['status' => false, 'message' => 'Validation Error', 'errors' => $validateUser->errors()->all()], 401);
        }
        
        if ($request->has('profile_image')) {
            $image = $request->profile_image;
            $name =  time().'.'.$image->getClientOriginalExtension();
            $path = public_path('images');
            $path = $image->storeAs('images', $name, 'public');
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'description' => $request->description,
                'role_id' => $request->role_id,
                'profile_image' => $path,
            ]);
    
            return response()->json(['status' => true, 'message' => 'User Created Successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while creating the user', 'error' => $e->getMessage()], 500);
        }
    }
    
}
