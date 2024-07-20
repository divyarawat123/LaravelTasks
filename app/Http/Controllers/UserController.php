<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $roles = Roles::all();
        return view('index', compact('roles'));
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|digits:10',
            'description' => 'required',
            'user_role' => 'required',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            // Create a new User instance
            $user = new User();
            $user->name = $request->post('name');
            $user->email = $request->post('email');
            $user->phone = $request->post('phone_number');
            $user->description = $request->post('description');
            $user->role_id = $request->post('user_role');

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $path = $file->storeAs('images', $filename, 'public');
                $user->profile_image = $path;
            }
            
            $user->save();

            return response()->json(['message' => 'Form submitted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function view()
    {
        $users = User::with('roles')->get();
        return response()->json(['user' =>$users]);
    }

}
