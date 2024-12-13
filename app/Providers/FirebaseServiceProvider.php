<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Database::class, function ($app) {
 
            $factory = (new Factory)
                    ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
                ->withServiceAccount(base_path(config('firebase.FIREBASE_CREDENTIALS')));

            return $factory->createDatabase();
        });
    }
}
