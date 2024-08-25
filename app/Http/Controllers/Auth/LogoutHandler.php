<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Post(
 *     path="/api/v1/logout",
 *     tags={"Authentication"},
 *     summary="Cierre de sesión del usuario",
 *     description="Invalida el token JWT y cierra la sesión del usuario.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Cierre de sesión exitoso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Cierre de sesión exitoso")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Error interno del servidor")
 *         )
 *     ),
 *     @OA\Tag(name="Authentication")
 * )
 */
class LogoutHandler
{
    public function __invoke(Request $request): Response
    {
        try {
            $token = $request->bearerToken();
            $token = JWTAuth::setToken($token)->getToken();
            JWTAuth::manager()->invalidate($token, true);
            return response([
                'message' => 'Cierre de sesión exitoso'
            ], 200);
        } catch (JWTException $exception) {
            return response([
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
