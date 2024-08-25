<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * @OA\Post(
 *     path="/api/v1/users",
 *     tags={"Authentication"},
 *     summary="Registro de un nuevo usuario",
 *     description="Permite a un usuario registrarse en la aplicación.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password"},
 *             @OA\Property(property="name", type="string", example="John Doe New"),
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="strongpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Usuario creado exitosamente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-25T15:03:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-25T15:03:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Los datos proporcionados no son válidos."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="El campo name es obligatorio.")
 *                 ),
 *                 @OA\Property(property="email", type="array",
 *                     @OA\Items(type="string", example="El campo email es obligatorio."),
 *                     @OA\Items(type="string", example="El campo email debe ser una dirección de correo válida."),
 *                     @OA\Items(type="string", example="El email ya ha sido registrado.")
 *                 ),
 *                 @OA\Property(property="password", type="array",
 *                     @OA\Items(type="string", example="El campo password es obligatorio."),
 *                     @OA\Items(type="string", example="El campo password debe tener al menos 8 caracteres.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Tag(name="Authentication")
 * )
 */
class SignUpHandler
{
    public function __invoke(SignUpRequest $request): Response
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        return response([
            'data' => UserResource::make($user)
        ], 201);
    }
}
