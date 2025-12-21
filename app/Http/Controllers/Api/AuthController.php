<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPSupabase\Service;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication Endpoints (Supabase Proxy)"
 * )
 */
class AuthController extends Controller
{
    private Service $supabase;

    public function __construct()
    {
        $this->supabase = new Service(
            env('SUPABASE_KEY'),
            env('SUPABASE_URL')
        );
    }

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="Login user",
     *      description="Login with email and password via Supabase",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string", format="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful login",
     *          @OA\JsonContent(
     *              @OA\Property(property="access_token", type="string"),
     *              @OA\Property(property="refresh_token", type="string"),
     *              @OA\Property(property="user", type="object")
     *          )
     *      ),
     *      @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $auth = $this->supabase->createAuth();
            $auth->signInWithEmailAndPassword($request->email, $request->password);
            $data = $auth->data();

            return response()->json($data);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (isset($auth) && method_exists($auth, 'getError')) {
                $libError = $auth->getError();
                if ($libError) $errorMessage = $libError;
            }

            return response()->json(['error' => $errorMessage], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      operationId="register",
     *      tags={"Auth"},
     *      summary="Register user",
     *      description="Register new user via Supabase",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string", format="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully"
     *      ),
     *      @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        try {
            $auth = $this->supabase->createAuth();
            $auth->createUserWithEmailAndPassword($validated['email'], $validated['password']);
            $data = $auth->data();
            return response()->json($data, 201);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (isset($auth) && method_exists($auth, 'getError')) {
                $libError = $auth->getError();
                if ($libError) $errorMessage = $libError;
            }
            return response()->json(['error' => $errorMessage], 400);
        }
    }

    /**
     * @OA\Put(
     *      path="/auth/user",
     *      operationId="updateUser",
     *      tags={"Auth"},
     *      summary="Update user data",
     *      description="Update user attributes in Supabase",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="password", type="string", format="password"),
     *              @OA\Property(property="data", type="object", description="User metadata")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User updated successfully"
     *      ),
     *      @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $auth = $this->supabase->createAuth();

            $email = $request->input('email');
            $password = $request->input('password');
            $metaData = $request->input('data', []);

            // updateUser($accessToken, $email, $password, $data)
            $auth->updateUser($token, $email, $password, $metaData);
            $data = $auth->data();

            return response()->json($data);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (isset($auth) && method_exists($auth, 'getError')) {
                $libError = $auth->getError();
                if ($libError) $errorMessage = $libError;
            }
            return response()->json(['error' => $errorMessage], 400);
        }
    }
}
