<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // login
    public function login(Request $request){
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);


        // (TEMPORARY ASSUMPTION)
        // sepertinya tidak akan dieksekusi karena validasi biasanya
        // diatur di aplikasi yang mengakses API (FORM VALIDATION)
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



    // register
    public function register(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
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

    public function update(Request $request, $id){
        $user = User::where('id', $id)->first();
        if($user){
            $user->update($request->all());
            return $this->success($user);
        }
        return $this->error("User tidak ditemukan");
    }

    public function uploadImage(Request $request, $id){
        $user = User::where('id', $id)->first();
        if($user){
            $fileName = "";
            if($request->image){
                // dapatkan nama file
                $image = $request->image->getClientOriginalName();  

                // format nama dengan menghilangkan spasi
                $image = str_replace(' ', '', $image);

                // membuat agar nama file tidak ada yang sama saat terupload
                $image = date('Hs').rand(1,999) . "_" . $image;

                $fileName = $image;
                $request->image->storeAs('public/user', $image);
            } else {
                return $this->error('Image wajib diberikan');
            }

            $user->update([
                'image' => $fileName
            ]);
            return $this->success($user);
        }
        return $this->error("User tidak ditemukan");
    }

    // success response
    public function success($data, $message = 'Success'){
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    // error response
    public function error($message = 'Terjadi kesalahan'){
        return response()->json([
            'code' => 400,
            'message' => $message
        ], 400); 
    }

}
