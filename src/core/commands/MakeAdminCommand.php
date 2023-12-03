<?php

namespace app\core\commands;

use app\core\Application;
use app\models\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MakeAdminCommand extends Command
{
    protected static $defaultName = 'make:admin';

    protected function configure()
    {
        $this->setDescription('Create an admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating an admin user...</info>');

        // Create a Question Helper
        $helper = $this->getHelper('question');

        // Ask for First Name
        $firstNameQuestion = new Question('Enter First Name: ');
        $firstName = $helper->ask($input, $output, $firstNameQuestion);

        // Ask for Last Name
        $lastNameQuestion = new Question('Enter Last Name: ');
        $lastName = $helper->ask($input, $output, $lastNameQuestion);

        // Ask for Email
        $emailQuestion = new Question('Enter Email: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        // Ask for Password
        $passwordQuestion = new Question('Enter Password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false); // Show input in non-hidden mode if hidden is not supported
        $password = $helper->ask($input, $output, $passwordQuestion);

        // Ask for Password Confirmation
        $passwordConfirmQuestion = new Question('Confirm Password: ');
        $passwordConfirmQuestion->setHidden(true);
        $passwordConfirmQuestion->setHiddenFallback(false);
        $passwordConfirm = $helper->ask($input, $output, $passwordConfirmQuestion);

        // Validate Password Confirmation
        if ($password !== $passwordConfirm) {
            $output->writeln('<error>Passwords do not match. Please try again.</error>');
            return Command::FAILURE;
        }

        $user = User::create([
            "email" => strtolower($email),
            "firstname" => $firstName,
            "lastname" => $lastName,
            "admin" => true,
            "password" => password_hash($password, PASSWORD_ARGON2ID)
        ]);

        $output->writeln('<info>Admin user created successfully!</info>');
        $output->writeln("<info>Email : {$user->email}</info>");
        $output->writeln("<info>Password : {$password}</info>");

        return Command::SUCCESS;
    }
}
