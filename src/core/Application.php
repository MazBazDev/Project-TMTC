<?php

namespace app\core;

use app\core\commands\MakeAdminCommand;
use app\core\commands\MakeMigrationCommand;
use app\core\commands\MakeSeederCommand;
use app\core\commands\MigrateCommand;
use app\core\commands\SeedCommand;
use app\core\commands\StorageLinkCommand;
use app\core\database\Database;
use app\core\routing\Router;
use Dotenv\Dotenv;
use Symfony\Component\Console\Application as SymfonyConsole;

// Fuck deprecated errors
error_reporting(E_ALL ^ E_DEPRECATED);

class Application
{
    public static string $ROOT_DIR;
    public array $config;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;

    public static Application $app;

    public Auth $auth;


    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $dotenv = Dotenv::createImmutable(self::$ROOT_DIR);
        $dotenv->load();

        date_default_timezone_set(env("APP_TIMEZONE"));

        $this->config = require_once(self::$ROOT_DIR."/config/app.php");
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->db = new Database();
        $this->auth = new Auth();
    }

    public function run()
    {
        // Si la demande est faite via la console
        if (php_sapi_name() === 'cli') {
            $this->handleConsoleCommand();
        } else {
            // Sinon, continuez avec le routage Web normal
            $this->router->resolve();
        }
    }

    private function handleConsoleCommand()
    {
        $console = new SymfonyConsole('Chef Framework by MazBaz', '1.0.0');
        $console->add(new MakeMigrationCommand());
        $console->add(new MigrateCommand());


        $console->add(new MakeSeederCommand());
        $console->add(new SeedCommand());

        $console->add(new MakeAdminCommand());

        $console->add(new StorageLinkCommand());
        $console->run();
    }
}