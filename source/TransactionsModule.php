<?php

namespace Spiral;

use Spiral\Core\DirectoriesInterface;
use Spiral\Modules\ModuleInterface;
use Spiral\Modules\PublisherInterface;
use Spiral\Modules\RegistratorInterface;
use Spiral\Transactions\Configs\CurrencyConfig;
use Spiral\Transactions\Configs\TransactionsConfig;

/**
 * Class TransactionsModule
 *
 * @package Spiral
 */
class TransactionsModule implements ModuleInterface
{
    /**
     * @inheritDoc
     */
    public function register(RegistratorInterface $registrator)
    {        //Register tokenizer directory
        $registrator->configure('tokenizer', 'directories', 'spiral/transactions', [
            "directory('libraries') . 'spiral/transactions/source/Transactions/Database/',",
        ]);

        //Register database settings
        $registrator->configure('databases', 'aliases', 'spiral/transactions', [
            "'transactions' => 'default',",
        ]);

        //Register view namespace
        $registrator->configure('views', 'namespaces', 'spiral/transactions', [
            "'transactions' => [",
            "directory('libraries') . 'spiral/transactions/source/views/',",
            "/*{{namespaces.transactions}}*/",
            "],",
        ]);

        //Register controller in vault config
        $registrator->configure('modules/vault', 'controllers', 'spiral/transactions', [
            "'transactions' => \\Spiral\\Transactions\\Controllers\\TransactionsController::class,",
        ]);
    }

    /**
     * @inheritDoc
     */
    public function publish(PublisherInterface $publisher, DirectoriesInterface $directories)
    {
        //Publish config
        $publisher->publish(
            __DIR__ . '/config/config.php',
            $directories->directory('config') . TransactionsConfig::CONFIG . '.php',
            PublisherInterface::FOLLOW
        );
        $publisher->publish(
            __DIR__ . '/config/currencies.php',
            $directories->directory('config') . CurrencyConfig::CONFIG . '.php',
            PublisherInterface::FOLLOW
        );
    }
}