<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;

class UserController extends Controller
{

    public function register(Request $request){
        // Validate the request data here
        
        // $user = new User([
        //     'first_name' => $request->input('first_name'),
        //     'last_name' => $request->input('last_name'),
        //     'image' => $request->input('image'),
        //     'Rc' => $request->input('Rc'),
        //     'email' => $request->input('email'),
        //     'phone' => $request->input('phone'),
        //     'password' => Hash::make($request->input('password')),
        //     'status' => 'active', // Set the default status
        //     'role' => $request->input('role'), // Assign the role from the request
        // ]);
        

        // $user->save();
        

        // // Generate and return JWT token
        // $token = Auth::login($user);
        // dd($token);
        // return $this->respondWithToken($token);
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'image' => 'string|nullable',
            'Rc'=>'string|nullable',
            'location' => 'string|nullable|max:255',
            'phone' => 'required|string|unique:users', // Unique phone number
            'email' => 'sometimes|string||nullable|email|unique:users', 
            'password' => 'required|string|confirmed|min:8',
            'created_by' => 'sometimes',
            // 'status' => 'required|in:pending, active, inactive', 
            'role'=>'required|string'
        ]);


        $last_name = $request->input('last_name', null);
        $image = $request->input('image', null);
        $Rc = $request->input('Rc', null);
        $location = $request->input('location', null);
        $email = $request->input('email', null);
        $created_by = $request->input('created_by', null);

        // dd(Auth::user());

        // if(Auth::user()){
        //     $created_by['created_by'] = Auth::user()->id;
        //     dd(Auth::user());
        // }


        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $last_name,
            'image' => $image,
            'Rc' => $Rc,
            'location' => $location,
            'phone' => $validatedData['phone'],
            'email' => $email,
            'role' => $validatedData['role'],
            'created_by' => $created_by,
            'password' => Hash::make($validatedData['password']),
        ]);
        
        
        // $supplier->save();

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
            $validatedData = $request->validate([
                'phone' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('phone', $validatedData['phone'])->first();

            if (!$user) {
                return $this->respondWithError('User not found', 404);
            }

            if (!Hash::check($validatedData['password'], $user->password)) {
                return $this->respondWithError('Wrong password', 401);
            }

            $token = $user->createToken('authToken')->accessToken;

            return response()->json(['user' => $user, 'access_token' => $token]);
    }

    public function someAction(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Logic for admin role
        } elseif ($user->role === 'supplier') {
            // Logic for supplier role
        } elseif ($user->role === 'store') {
            // Logic for store role
        } elseif ($user->role === 'client') {
            // Logic for client role
        } elseif ($user->role === 'courier') {
            // Logic for couriers role
        } elseif ($user->role === 'delivery_man') {
            // Logic for delivery_man role
        } else {
            // Handle other roles or unauthorized access
        }

        // Continue with your action logic
    }


    public function logout() 
    {
        try{
            
            $user = Auth::guard('user')->user();
            // dd($user);
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage(), 500);
        }
    }

    public function respondWithError($message, $statusCode = 400)
    {
        return response()->json([
            'error' => $message,
        ], $statusCode);
    }

}
