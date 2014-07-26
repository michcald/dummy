<?php

namespace Michcald\Dummy;

abstract class Bootstrap
{
    public static function init()
    {
        date_default_timezone_set('europe/london');
        
        self::initConfig();
        self::initMonolog();
        self::initDb();
        self::initDbSchema();
        self::initRoutes();
        self::initRequest();
    }

    private static function initConfig()
    {
        $dir = realpath(__DIR__ . '/../../../app/config');

        $config = \Michcald\Dummy\Config::getInstance();
        $config->loadDir($dir);
    }

    private static function initMonolog()
    {
        $logdir = __DIR__ . '/../../../app/logs';
        
        if (!is_dir($logdir)) {
            mkdir($logdir, 0777);
        }

        $logdir = realpath($logdir);
        
        $logger = new \Monolog\Logger('monolog');

        $logger->pushHandler(
            new \Monolog\Handler\RotatingFileHandler(
                $logdir . '/access.log',
                10,
                \Monolog\Logger::INFO,
                false
            )
        );

        $logger->pushHandler(
            new \Monolog\Handler\RotatingFileHandler(
                $logdir . '/error.log',
                10,
                \Monolog\Logger::WARNING
            )
        );

        \Michcald\Mvc\Container::add('dummy.monolog', $logger);
    }

    private static function initDb()
    {
        $config = \Michcald\Dummy\Config::getInstance();

        $dsn = sprintf(
            '%s:host=%s;dbname=%s',
            strtolower($config->database['adapter']),
            $config->database['host'],
            $config->database['dbname']
        );

        $db = new \PDO($dsn, $config->database['user'], $config->database['password']);

        \Michcald\Mvc\Container::add('dummy.db', $db);
    }

    private static function initDbSchema()
    {
        /* @var \PDO $db */
        $db = \Michcald\Mvc\Container::get('dummy.db');

        $file = realpath(__DIR__ . '/../../../app/install.sql');
        $installSql = file_get_contents($file);

        $db->query($installSql);

        // create default application
        $sql = 'SELECT COUNT(id) count FROM meta_app';
        $stm = $db->prepare($sql);
        $stm->execute();
        $row = $stm->fetch(\PDO::FETCH_ASSOC);

        if ($row['count'] == 0) {
            $sql = sprintf(
                'INSERT INTO meta_app (`name`,`description`,`password`) VALUES ("%s","%s","%s");',
                'dummy',
                'Default application',
                sha1('dummy')
            );
            $db->query($sql);
        }
    }

    private static function initRoutes()
    {
        $mvc = new \Michcald\Mvc\Mvc();

        $listener = new Event\Listener\Monolog();
        $mvc->addEventSubscriber($listener);

        $config = \Michcald\Dummy\Config::getInstance();

        foreach ($config->routes as $routeConfig) {

            $uri = new \Michcald\Mvc\Router\Route\Uri();
            $uri->setPattern($routeConfig['uri']['pattern']);

            foreach ($routeConfig['uri']['requirements'] as $requirement) {
                $uri->setRequirement($requirement['param'], $requirement['value']);
            }

            $route = new \Michcald\Mvc\Router\Route();
            $route->setMethods($routeConfig['methods'])
                ->setUri($uri)
                ->setId($routeConfig['name'])
                ->setControllerClass($routeConfig['controller'])
                ->setActionName($routeConfig['action']);

            $mvc->addRoute($route);
        }

        \Michcald\Mvc\Container::add('dummy.mvc', $mvc);
    }

    private static function initRequest()
    {
        $request = new \Michcald\Dummy\Request();

        \Michcald\Mvc\Container::add('dummy.mvc.request', $request);
    }
}