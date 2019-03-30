<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() 
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {

            $token = $request->header('token');
            if(!$token) return null;

            try {
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            } catch(ExpiredException $e) {
                return null;
            } catch(Exception $e) {
                return null;
            }

            $user = User::find($credentials->sub)->first();
            if (!$user || !$user->status) return null;

            return $user; 
        });
    }
}