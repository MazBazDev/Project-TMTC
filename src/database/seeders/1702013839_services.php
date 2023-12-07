<?php
namespace app\database\seeders;

use app\models\Services;
use Faker\Factory;

return new class {
    public function up()
    {
        $factory = Factory::create(config('fakerLocale'));

        $datas = [
            'Airport transfers',
            'Breakfast',
            'Housekeeping service',
            'Car rental',
            'Guided tours',
            'Cooking classes',
            'Recreational activities',
        ];

        foreach ($datas as $name) {
            Services::create([
                "name" => $name,
                "description" => $factory->text(150),
            ]);
        }
    }
};
