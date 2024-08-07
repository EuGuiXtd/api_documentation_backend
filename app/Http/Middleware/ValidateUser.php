<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $body = $request->all();
        $senha = $body['password'] ?? '';
        $email = $body['email'] ?? '';
        $name = $body['name'] ?? '';

        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            return response()->json(['error' => 'Email inválido'], 400);
        }
        if (strlen($senha) < 6) {
            return response()->json(['error' => 'Senha deve ter no mínimo 6 caracteres'], 400);
        }
        if (empty($name) || empty($senha) || empty($email)) {
            return response()->json(['error' => 'Todos os campos devem ser preenchidos'], 400);
        }
        $user_exists = \App\Models\User::where('email', $email)->first();
        if ($user_exists) {
            return response()->json(['error' => 'Usuário já cadastrado'], 400);
        }

        $request->merge(['password' => bcrypt($senha)]);
        return $next($request);
    }
}