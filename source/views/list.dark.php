<extends:vault.layout title="[[Transactions]]" class="wide-content"/>

<dark:use path="spiral:listing/*" prefix="listing:"/>

<?php
/**
 * @var \Spiral\Listing\Listing                     $listing
 * @var \Spiral\Transactions\Database\Transaction   $entity
 * @var \Spiral\Transactions\VaultServices\Statuses $statuses
 */
?>

<define:content>
    <vault:card>
        <listing:form listing="<?= $listing ?>">
            <div class="row">
                <div class="col s4">
                    <listing:filter>
                        <form:input name="search" placeholder="[[Find by transactions gateway id, currency, amounts...]]"/>
                    </listing:filter>
                </div>
                <div class="col s4">
                    <listing:filter>
                        <form:input name="metadata" placeholder="[[Find by items title and amount, cardholder, attribute values...]]"/>
                    </listing:filter>
                </div>
                <div class="col s2">
                    <listing:filter>
                        <form:select name="status" values="<?= $statuses->labels(true) ?>"/>
                    </listing:filter>
                </div>
                <div class="col s2">
                    <div class="right-align">
                        <listing:reset/>
                    </div>
                </div>
            </div>
        </listing:form>
    </vault:card>

    <?php
    /** @var \Spiral\Transactions\VaultServices\Currencies $currencies */
    $currencies = spiral(\Spiral\Transactions\VaultServices\Currencies::class);
    ?>
    <vault:listing listing="<?= $listing ?>" as="entity" color="" class="striped">
        <grid:cell sorter="id" label="[[ID:]]" value="<?= $entity->primaryKey() ?>"/>
        <grid:cell sorter="created" label="[[Created:]]" value="<?= $entity->time_created ?>"/>
        <grid:cell label="[[Gateway:]]"><?= $entity->getGateway() ?>
            <span title="<?= $entity->getGatewayID() ?>"><strong>*</strong></span>
        </grid:cell>
        <grid:cell sorter="currency" label="[[Currency:]]" value="<?= $entity->getCurrency() ?>"/>
        <grid:cell sorter="amount" label="[[Amount:]]" value="<?= $currencies->formatValue($entity->getCurrency(), $entity->getPaidAmount()) ?>"/>
        <grid:cell label="[[Card:]]"><?= $entity->source->getCardType() ?> / <?= $entity->source->getNumberEnding() ?></grid:cell>
        <grid:cell label="[[Card Holder:]]"><?= $entity->source->getCardHolder() ?></grid:cell>
        <grid:cell label="[[Status:]]">
            <i class="material-icons tiny"><?= $statuses->icon($entity->status) ?></i>
            <?= $statuses->label($entity->status) ?>
        </grid:cell>
        <grid:cell class="right-align">
            <vault:allowed permission="vault.transactions.view">
                <vault:uri target="transactions:view" icon="visibility" options="<?= ['id' => $entity->primaryKey()] ?>"
                           class="btn waves-effect teal"> [[View]]
                </vault:uri>
            </vault:allowed>
        </grid:cell>
    </vault:listing>
</define:content>