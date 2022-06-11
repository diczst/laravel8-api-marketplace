<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return $this->error($validation->errors()->first());
        }

        $user = User::where('email', $request->email)->first();
        if($user){
            if(password_verify($request->password, $user->password)){
                return $this->success($user);
            } else {
                return $this->error('Password salah');
            }

            return $this->success($user, "Selamat datang $user->name");
        }

        return $this->error("Email atau password salah");
    }

    public function register(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => '400',
                'message' => $validation->errors()->first()
            ], 400); 
            // return $validation->errors()->all();
        }

        $user =  User::create(array_merge($request->all(), [
            'password' => bcrypt($request->password)
            ])
         );

        if($user){
            return $this->success($user, "Selamat datang $user->name");
        } else {
            return $this->error();
        }
    }

    public function success($data, $message = 'Success'){
        return response()->json([
            'code' => '200',
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function error($message = 'Terjadi kesalahan'){
        return response()->json([
            'code' => '400',
            'message' => $message
        ], 400); 
    }

}
