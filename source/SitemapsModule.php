<?php

namespace Spiral;

use Spiral\Core\DirectoriesInterface;
use Spiral\Modules\ModuleInterface;
use Spiral\Modules\PublisherInterface;
use Spiral\Modules\RegistratorInterface;
use Spiral\Transactions\TransactionsConfig;

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
    }
}