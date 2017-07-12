<?php

namespace Spiral\Transactions\VaultServices;

use Spiral\Core\FactoryInterface;
use Spiral\Listing\Dependency;
use Spiral\Listing\Filters\SearchFilter;
use Spiral\Listing\Filters\ValueFilter;
use Spiral\Listing\Listing;
use Spiral\Listing\SorterInterface;
use Spiral\Listing\Sorters\BinarySorter;
use Spiral\Listing\StaticState;
use Spiral\ORM\Entities\RecordSelector;
use Spiral\Transactions\Database\Types\TransactionStatus;

class Listings
{
    /** @var FactoryInterface */
    protected $factory;

    /**
     * ListingService constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function getTransactions(RecordSelector $selector)
    {
        /** @var Listing $listing */
        $listing = $this->factory->make(Listing::class, [
            'selector' => $selector->distinct(),
        ]);

        $listing->addSorter('id', new BinarySorter('transaction.id'));
        $listing->addSorter('created', new BinarySorter('time_created'));
        $listing->addSorter('amount', new BinarySorter('paid_amount'));
        $listing->addSorter('currency', new BinarySorter('currency'));

        $listing->addFilter(
            'status',
            new ValueFilter('status')
        );

        $listing->addFilter(
            'search',
            new SearchFilter([
                'transaction.gateway_id' => SearchFilter::EQUALS_STRING,
                'transaction.id'         => SearchFilter::EQUALS_STRING,
                'currency'               => SearchFilter::EQUALS_STRING,
                'paid_amount'            => SearchFilter::EQUALS_FLOAT,
                'refunded_amount'        => SearchFilter::EQUALS_FLOAT,
            ])
        );

        $listing->addFilter(
            'metadata',
            new SearchFilter([
                'items.title'       => SearchFilter::LIKE_STRING,
                'items.amount'      => SearchFilter::EQUALS_FLOAT,
                'attributes.value'  => SearchFilter::LIKE_STRING,
                'source.cardHolder' => SearchFilter::LIKE_STRING,
            ], new Dependency('items'), new Dependency('attributes'), new Dependency('source'))
        );

        $defaultState = new StaticState('id', ['status' => TransactionStatus::COMPLETED], SorterInterface::DESC);
        $listing = $listing->setDefaultState($defaultState)->setNamespace('transactions');

        return $listing;
    }
}