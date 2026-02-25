<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JwtAuth
{
    private $key;

    public function __construct()
    {
        $this->key = env('JWT_SECRET', 'la-diabla-secret-key-pizzeria-2026');
    }

    /**
     * Generate JWT token for authenticated user
     */
    public function generateToken(User $user, $getUser = null)
    {
        $token = [
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60) // 7 days
        ];

        $jwt = JWT::encode($token, $this->key, 'HS256');
        $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));

        if (!empty($getUser)) {
            return $decoded;
        } else {
            return $jwt;
        }
    }

    /**
     * Verify user credentials and generate token
     */
    public function authenticate($email, $password, $getUser = null)
    {
        $user = User::where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            return $this->generateToken($user, $getUser);
        }

        return [
            'status' => 'error',
            'message' => 'Invalid credentials'
        ];
    }

    /**
     * Validate JWT token
     */
    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;
        $decoded = null;

        try {
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        } catch (\Exception $e) {
            $auth = false;
        }

        if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin($jwt): bool
    {
        $identity = $this->checkToken($jwt, true);
        return $identity && isset($identity->role) && $identity->role === 'admin';
    }
}
