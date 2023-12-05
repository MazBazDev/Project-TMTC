<?php
namespace app\database\seeders;

use app\models\User;
use Faker\Factory;

return new class {
    public function up()
    {
        $factory = Factory::create(config('fakerLocale'));

        for ($i = 0; $i < 1; $i++) {
            User::create([
                "firstname" => $factory->firstName,
                "lastname" => $factory->lastName,
                "email" => $factory->email,
                "password" => password_hash($factory->password, PASSWORD_ARGON2ID),
                "admin" => $factory->boolean,
            ]);
        }
    }
};

