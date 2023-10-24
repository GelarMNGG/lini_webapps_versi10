<?php

namespace App\Http\Controllers\Api\Tech\Login;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Tech;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    
    public function __construct()
    {
        $this->middleware('auth:tech-api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->guard('tech-api')->attempt($credentials)) {
            return response()->json(['error' => 'Nama atau password yang Anda masukkan salah.'], 401);
        }

        /*$cookie = cookie('token',$token,60*12); // 12 hours

        return $this->respondWithToken($token)->withCookie($cookie);
        */
        return response()->json(['token' => $token]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = $this->authUser();

        if (!isset($user->id)) {
            return response()->json(['error' => 'Maaf, data tidak tersedia atau Anda tidak diijinkan mengakses halaman ini.']);
        }
        $data['user'] = Tech::select([
            'id',
            'firstname',
            'lastname',
            'title',
            'mobile',
            'address',
            'image',
            'norek',
            'bank_name',
            'branch',
        ])->where('id',$user->id)->first();

        return response()->json($data);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = auth()->guard('tech-api')->user();
        if (!$user) {
            return response()->json(['error' => 'Anda sudah logout.']);
        }
        auth()->guard('tech-api')->logout();

        return response()->json(['message' => 'Successfully logged out.']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->guard('tech-api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('tech-api')->factory()->getTTL() * 60
        ]);
    }
}
