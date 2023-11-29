<?php

namespace app\core\commands;

use app\core\Application;
use app\models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Command
{
    protected static $defaultName = 'test';

    protected function configure()
    {
        $this->setDescription('test');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $user = User::create([
//            "email" => "hello@keqllo.com",
//            "firstname" => "mazbaz",
//            "lastname" => "labaz",
//            "profile_picture" => "https://feeee.com",
//            "admin" => true,
//            "password" => "passsss",
//        ]);

        var_dump(User::where("id", 1)->first());
        return Command::SUCCESS;
    }
}
