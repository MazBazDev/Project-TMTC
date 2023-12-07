<?php
namespace app\database\seeders;

use app\models\HousingsType;
use Faker\Factory;

return new class {
    public function up()
    {
        $factory = Factory::create(config('fakerLocale'));

        $datas = [
            'Apartments',
            'Houses',
            'Chalets',
            'Villas',
            'Houseboats',
            'Yurts',
            'Cabins',
            'Igloos',
            'Tents',
            'Campervans',
        ];

        foreach ($datas as $name) {
            HousingsType::create([
                "name" => $name,
            ]);
        }
    }
};
