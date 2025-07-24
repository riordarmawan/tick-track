<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // kita akan login menggunakan Auth
            // ->attempt($request->only('email', 'password')) - kita mengirimkan request email dan password

            // jika gagal
            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'data' => null
                ], 401);
            }

            // jika berhasil
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login Berhasil',
                'data' => [
                    'token' => $token,
                    'user' => new UserResource($user)
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // get profile yang sedang login
    public function me()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'message' => 'Profile User Berhasil Diambil',
                'data' => new UserResource($user)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout Berhasil',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(RegisterStoreRequest $request)
    {
        $data = $request->validated(); // untuk validasi

        // kemudian setelah tervalidasi, kita tambahkan
        DB::beginTransaction(); // agar saat terjadi kesalahan ga langsung ke input ke db

        try {
            $user = new User;
            // kemudian kita simpan name, email, password
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->save();

            // setelah berhasil regist maka kita berikan tokennya
            $token = $user->createToken('auth_token')->plainTextToken;

            // ini untuk kita masukkan ke db
            DB::commit();

            return response()->json([
                'message' => 'Registrasi Berhasil',
                'data' => [
                    'token' => $token,
                    'user' => new UserResource($user) // data user kita panggil dari new UserResource($user)
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
