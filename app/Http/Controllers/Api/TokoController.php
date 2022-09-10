<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

// CATATAN
// php artisan make:controller Api\TokoController --resource
// membuat controller yang langsung da functions crudnya oto

class TokoController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'userId' => 'required',
            'name' => 'required',
            'kota' => 'required',
        ]);

        if ($validasi->fails()) {
            return $this->error($validasi->errors()->first());
        }

        $toko = Toko::create($request->all());
        return $this->success($toko);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
        //
    }

    public function cekToko($id) {
        $user = User::where('id', $id)->with('toko')->first();
        if ($user) {
            return $this->success($user);
        } else {
            return $this->error("User tidak ditemukan");
        }
    }

    public function success($data, $message = "success") {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function error($message) {
        return response()->json([
            'code' => 400,
            'message' => $message
        ], 400);
    }
}
