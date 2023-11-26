<?php

namespace app\core;

use Symfony\Component\Console\Application as SymfonyConsole;
use app\core\Commands\MakeMigrationCommand;
class Application
{
    public static string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Database $db;

    public static Application $app;
    
    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();

        $this->router = new Router($this->request, $this->response);

        $this->db = new Database();
    }

    public function run()
    {
        // Si la demande est faite via la console
        if (php_sapi_name() === 'cli') {
            $this->handleConsoleCommand();
        } else {
            // Sinon, continuez avec le routage Web normal
            return $this->router->resolve();
        }
    }

    private function handleConsoleCommand()
    {
        $console = new SymfonyConsole('Chef Framework by MazBaz', '1.0.0');
        $console->add(new MakeMigrationCommand());
        $console->run();
    }
}