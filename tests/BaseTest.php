<?php

namespace Spiral\Tests;

use Monolog\Handler\NullHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Spiral\Core\Traits\SharedTrait;
use Spiral\Debug\Snapshot;
use Zend\Diactoros\ServerRequest;

/**
 * @property \Spiral\Core\MemoryInterface             $memory
 * @property \Spiral\Core\ContainerInterface          $container
 * @property \Spiral\Debug\LogsInterface              $logs
 * @property \Spiral\Http\HttpDispatcher              $http
 * @property \Spiral\Console\ConsoleDispatcher        $console
 * @property \Spiral\Console\ConsoleDispatcher        $commands
 * @property \Spiral\Files\FilesInterface             $files
 * @property \Spiral\Tokenizer\TokenizerInterface     $tokenizer
 * @property \Spiral\Tokenizer\ClassesInterface       $locator
 * @property \Spiral\Tokenizer\InvocationsInterface   $invocationLocator
 * @property \Spiral\Views\ViewManager                $views
 * @property \Spiral\Translator\Translator            $translator
 * @property \Spiral\Database\DatabaseManager         $dbal
 * @property \Spiral\ORM\ORM                          $orm
 * @property \Spiral\Encrypter\EncrypterInterface     $encrypter
 * @property \Spiral\Database\Entities\Database       $db
 * @property \Spiral\Http\Cookies\CookieQueue         $cookies
 * @property \Spiral\Http\Routing\RouterInterface     $router
 * @property \Spiral\Pagination\PaginatorsInterface   $paginators
 * @property \Psr\Http\Message\ServerRequestInterface $request
 * @property \Spiral\Http\Request\InputManager        $input
 * @property \Spiral\Http\Response\ResponseWrapper    $response
 * @property \Spiral\Http\Routing\RouteInterface      $route
 * @property \Spiral\Security\PermissionsInterface    $permissions
 * @property \Spiral\Security\RulesInterface          $rules
 * @property \Spiral\Security\ActorInterface          $actor
 * @property \Spiral\Session\SessionInterface         $session
 */
abstract class BaseTest extends TestCase
{
    use SharedTrait;

    /**
     * @var TestApplication
     */
    protected $app;

    public function setUp()
    {
        $root = __DIR__ . '/-app-/';
        $this->app = TestApplication::init(
            [
                'root'        => $root,
                'libraries'   => dirname(__DIR__) . '/vendor/',
                'application' => $root,
                'framework'   => dirname(__DIR__) . '/source/',
                'runtime'     => $root . 'runtime/',
                'cache'       => $root . 'runtime/cache/',
            ],
            null,
            null,
            false
        );

        //Monolog love to write to CLI when no handler set
        $this->app->logs->debugHandler(new NullHandler());
        $this->app->container->bind('factory', $this->app->container);

        $files = $this->app->files;

        //Ensure runtime is clean
        foreach ($files->getFiles($this->app->directory('runtime')) as $filename) {
            //If exception is thrown here this will mean that application wasn't correctly
            //destructed and there is open resources kept
            $files->delete($filename);
        }

        $builder = $this->orm->schemaBuilder(true);
        $builder->renderSchema();
        $builder->pushSchema();
        $this->orm->setSchema($builder);

        if ($this->app->getEnvironment()->get('DEBUG')) {
            $this->app->db->getDriver()->setLogger(new class implements LoggerInterface
            {
                use LoggerTrait;

                public function log($level, $message, array $context = [])
                {
                    if ($level == LogLevel::ERROR) {
                        echo " \n! \033[31m" . $message . "\033[0m";
                    } elseif ($level == LogLevel::ALERT) {
                        echo " \n! \033[35m" . $message . "\033[0m";
                    } elseif (strpos($message, 'PRAGMA') === 0) {
                        echo " \n> \033[34m" . $message . "\033[0m";
                    } else {
                        if (strpos($message, 'SELECT') === 0) {
                            echo " \n> \033[32m" . $message . "\033[0m";
                        } else {
                            echo " \n> \033[33m" . $message . "\033[0m";
                        }
                    }
                }
            });
        }

        clearstatcache();

        //Open application scope
        TestApplication::shareContainer($this->app->container);
    }

    /**
     * This method performs full destroy of spiral environment.
     */
    public function tearDown()
    {
        \Mockery::close();

        TestApplication::shareContainer(null);

        //Forcing destruction
        $this->app = null;

        gc_collect_cycles();
        clearstatcache();
    }

    /**
     * @return \Spiral\Core\ContainerInterface
     */
    protected function iocContainer()
    {
        return $this->app->container;
    }


    /**
     * @param string $message
     * @param int    $code
     * @return Snapshot
     */
    protected function makeSnapshot(string $message, int $code): Snapshot
    {
        return $this->factory->make(Snapshot::class, [
            'exception' => new \Error($message, $code)
        ]);
    }
}