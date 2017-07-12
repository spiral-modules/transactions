<?php

namespace Spiral\Transactions\Controllers;

use Spiral\Core\Controller;
use Spiral\Core\Traits\AuthorizesTrait;
use Spiral\Http\Exceptions\ClientExceptions\NotFoundException;
use Spiral\Transactions\Database\Sources\TransactionSource;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\VaultServices\Currencies;
use Spiral\Transactions\VaultServices\Listings;
use Spiral\Transactions\VaultServices\Statuses;
use Spiral\Translator\Traits\TranslatorTrait;

/**
 * Class TransactionsController
 *
 * @property \Spiral\Views\ViewManager $views
 * @package Controllers\Keeper
 */
class TransactionsController extends Controller
{
    use AuthorizesTrait, TranslatorTrait;

    const GUARD_NAMESPACE = 'vault.transactions';

    /**
     * Pages list.
     *
     * @param Listings          $listings
     * @param TransactionSource $source
     * @param Statuses          $statuses
     *
     * @return string
     */
    public function indexAction(
        Listings $listings,
        TransactionSource $source,
        Statuses $statuses
    ) {
        return $this->views->render('transactions:list', [
            'listing'  => $listings->getTransactions($source->find()->load('source')),
            'statuses' => $statuses
        ]);
    }

    /**
     * View transaction.
     *
     * @param string            $id
     * @param TransactionSource $source
     * @param Statuses          $statuses
     * @param GatewayInterface  $gateway
     * @param Currencies        $currencies
     *
     * @return string
     */
    public function viewAction(
        $id,
        TransactionSource $source,
        Statuses $statuses,
        GatewayInterface $gateway,
        Currencies $currencies
    ) {
        $transaction = $source->findByPK($id);
        if (empty($transaction)) {
            throw new NotFoundException();
        }

        $this->allows('view', ['entity' => $transaction]);

        return $this->views->render('transactions:view', [
            'transaction' => $transaction,
            'statuses'    => $statuses,
            'gateway'     => $gateway,
            'currencies'  => $currencies
        ]);
    }
}