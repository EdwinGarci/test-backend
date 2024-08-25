<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Post(
 *     path="/api/v1/login",
 *     tags={"Authentication"},
 *     summary="Login de usuario",
 *     description="Permite a un usuario autenticarse y obtener un token JWT.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Operación exitosa. Retorna el token JWT y los datos del usuario.",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *                 @OA\Property(property="user", ref="#/components/schemas/UserResource")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciales inválidos. El email o la contraseña son incorrectos."
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación. Falta el email o la contraseña.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="El campo email es obligatorio."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="El campo email es obligatorio.")),
 *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="El campo password es obligatorio."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor. No se pudo crear el token."
 *     ),
 *     @OA\Tag(name="Authentication")
 * )
 */
class LoginHandler
{
    public function __invoke(LoginRequest $request): Response
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response([
                    'message' => 'Credenciales inválidos'
                ], 401);
            }
        } catch (JWTException $exception) {
            return response([
                'message' => 'No se pudo crear el token'
            ], 500);
        }

        $user = JWTAuth::user();

        return response([
            'data' => [
                'token' => $token,
                'user' => UserResource::make($user),
            ],
        ], 200);
    }
}
