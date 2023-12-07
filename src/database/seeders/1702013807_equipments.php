<?php
namespace app\database\seeders;

use app\models\Equipment;
use Faker\Factory;

return new class {
    public function up()
    {
        $factory = Factory::create(config('fakerLocale'));

        $datas = [
            'Wi-Fi',
            'Air conditioner',
            'Heating',
            'Washing machine',
            'Dryer',
            'Television',
            'Iron/Ironing board',
            'Nintendo Switch',
            'PS5',
            'Terrace',
            'Balcony',
            'Swimming pool',
            'Garden',
        ];

        foreach ($datas as $name) {
            Equipment::create([
                "name" => $name,
            ]);
        }
    }
};
