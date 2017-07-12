<extends:vault:layout title="[[View transaction]]" class="wide-content"/>
<dark:use path="pages:/*" prefix="pages:"/>

<?php #compile
/**
 * @var \Spiral\Transactions\Database\Transaction           $transaction
 * @var \Spiral\Transactions\GatewayInterface               $gateway
 * @var \Spiral\Transactions\VaultServices\Currencies       $currencies
 * @var \Spiral\Transactions\VaultServices\Statuses         $statuses
 * @var \Spiral\Transactions\Database\Transaction\Item      $item
 * @var \Spiral\Transactions\Database\Transaction\Attribute $attribute
 * @var \Spiral\Transactions\Database\Transaction\Refund    $refund
 */
?>

<define:actions>
    <vault:uri target="transactions" class="btn-flat  waves-effect" post-icon="trending_flat">
        [[Back]]
    </vault:uri>
</define:actions>

<define:styles>
    <define:styles/>
    <style>
        dl dt {
            width: 200px;
        }
    </style>
</define:styles>

<define:content>
    <div class="row">
        <div class="col s12 m6">
            <vault:block title="[[Transaction:]]">
                <dl>
                    <dt>[[ID:]]</dt>
                    <dd><?= $transaction->primaryKey() ?></dd>

                    <dt>[[Time Created:]]</dt>
                    <dd><?= $transaction->time_created ?></dd>

                    <dt>[[Gateway:]]</dt>
                    <dd><?= $transaction->getGateway() ?>
                        [<a href="<?= $gateway->transactionGatewayURI($transaction->getGatewayID()) ?>"><?= $transaction->getGatewayID() ?></a>]
                    </dd>

                    <dt>[[Status:]]</dt>
                    <dd><i class="material-icons tiny"><?= $statuses->icon($transaction->status) ?></i>
                        <?= $statuses->label($transaction->status) ?></dd>
                </dl>
            </vault:block>

            <vault:block title="[[Amounts:]]">
                <dl>
                    <dt>[[Currency:]]</dt>
                    <dd><?= strtoupper($transaction->getCurrency()) ?></dd>

                    <dt>[[Paid amount:]]</dt>
                    <dd><?= number_format($transaction->getPaidAmount(), 2) ?></dd>

                    <dt>[[Refunded amount:]]</dt>
                    <dd><?= number_format($transaction->getRefundedAmount(), 2) ?></dd>

                    <dt>[[Fee:]]</dt>
                    <dd>$<?= number_format($transaction->getFeeAmount(), 2) ?></dd>
                </dl>
            </vault:block>

            <vault:block title="[[Payment Source:]]">
                <dl>
                    <dt>[[Card Holder:]]</dt>
                    <dd><?= $transaction->source->getCardHolder() ?></dd>

                    <dt>[[Card Type:]]</dt>
                    <dd><?= $transaction->source->getCardType() ?></dd>

                    <dt>[[Card Expiration:]]</dt>
                    <dd><?= $transaction->source->getExpMonth() ?> / <?= $transaction->source->getExpYear() ?></dd>

                    <dt>[[Card Number Ending:]]</dt>
                    <dd>*** <?= $transaction->source->getNumberEnding() ?></dd>
                </dl>
            </vault:block>
        </div>
        <div class="col s12 m6">
            <vault:block title="[[Paid Items:]]">
                <spiral:grid source="<?= $transaction->items ?>" as="item" color="" class="table responsive-table">
                    <grid:cell label="[[Title:]]" title="<?= $item->getTitle() ?>">
                        <?= \Spiral\Support\Strings::shorter($item->getTitle(), 100) ?>
                    </grid:cell>
                    <grid:cell label="[[Type:]]"><?= $item->getType() ?></grid:cell>
                    <grid:cell label="[[Quantity:]]"><?= $item->getQuantity() ?></grid:cell>
                    <grid:cell label="[[Price:]]"><?= number_format($item->getAmount(), 2) ?></grid:cell>
                </spiral:grid>
            </vault:block>

            <?php if ($transaction->attributes->count()) { ?>
                <vault:block title="[[Attributes:]]">
                    <spiral:grid source="<?= $transaction->attributes ?>" as="attribute" color="" class="table responsive-table">
                        <grid:cell label="[[Name:]]"><?= $attribute->getName() ?></grid:cell>
                        <grid:cell label="[[Value:]]"><?= $attribute->getValue() ?></grid:cell>
                    </spiral:grid>
                </vault:block>
            <?php } ?>

            <vault:block title="[[Refunds:]]">
                <?php if ($transaction->refunds->count()) { ?>
                    <spiral:grid source="<?= $transaction->refunds ?>" as="refund" color="" class="table responsive-table">
                        <grid:cell label="[[Gateway ID:]]"><?= $refund->getGatewayID() ?></grid:cell>
                        <grid:cell label="[[Amount:]]"><?= number_format($item->getAmount(), 2) ?></grid:cell>
                    </spiral:grid>
                <?php } else { ?>
                    <vault:card>
                        <p>[[No refunds... Yet.]]</p>
                    </vault:card>
                <?php } ?>
            </vault:block>

            <?php if ($transaction->isRefundable()) { ?>
                <vault:allowed permission="vault.transactions.refund">
                    <vault:block title="[[Refund Payment:]]">
                        <spiral:form action="<?= vault()->uri('pages:transactions:fullRefund', ['id' => $transaction->primaryKey()]) ?>"
                                     style="padding-bottom: 0;">
                            <?php if ($transaction->isPartiallyRefundable()) { ?>
                                <div class="row">
                                    <div class="col s12 m12">
                                        <div class="right-left">
                                            <input type="submit" value="[[Remaining amount]]" class="btn waves-effect waves-light light-green"/>
                                        </div>

                                        <p class="grey-text">
                                            <?php
                                            $refundNotice1 = l(
                                                'Refund the remaining {amount}',
                                                [
                                                    'amount' => $currencies->formatValue(
                                                        $transaction->getPaidAmount() - $transaction->getRefundedAmount(),
                                                        $transaction->getCurrency()
                                                    )
                                                ]
                                            );
                                            $refundNotice2 = l(
                                                '{amount} has already been refunded.',
                                                [
                                                    'amount' => $currencies->formatValue(
                                                        $transaction->getRefundedAmount(),
                                                        $transaction->getCurrency()
                                                    )
                                                ]
                                            );
                                            ?>
                                            <?= $refundNotice1 ?> (<?= $refundNotice2 ?>)</p>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col s12 m12">
                                        <div class="right-left">
                                            <input type="submit" value="[[Full refund]]" class="btn waves-effect waves-light light-green"/>
                                        </div>
                                        <p class="grey-text">
                                            <?= l(
                                                'Refund the full amount ({amount})',
                                                [
                                                    'amount' => $currencies->formatValue($transaction->getPaidAmount(), $transaction->getCurrency())
                                                ]
                                            ) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php } ?>
                        </spiral:form>

                        <div class="col m12">
                            <hr/>
                            <br/>
                        </div>

                        <spiral:form action="<?= vault()->uri('pages:transactions:refund', ['id' => $transaction->primaryKey()]) ?>"
                                     style="padding-bottom: 5px;">
                            <div class="row">
                                <div class="col s12 m4">
                                    <form.input name="amount" placeholder="[[Refund amount...]]"/>
                                    <p class="grey-text">[[Refund a partial amount.]]</p>
                                </div>

                                <div class="col s12 m3">
                                    <div class="right-left">
                                        <input type="submit" value="[[Refund]]" class="btn waves-effect waves-light light-green"/>
                                    </div>
                                </div>
                            </div>
                        </spiral:form>
                    </vault:block>
                </vault:allowed>
            <?php } ?>
        </div>
    </div>
</define:content>