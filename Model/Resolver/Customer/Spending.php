<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_StoreCreditGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\StoreCreditGraphQl\Model\Resolver\Customer;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Mageplaza\StoreCredit\Model\Api\SpendingManagement;
use Magento\Checkout\Model\TotalsInformation;
use Mageplaza\StoreCredit\Helper\Data;
use Mageplaza\StoreCreditGraphQl\Model\Resolver\AbstractStoreCredit;

/**
 * Class Spending
 * @package Mageplaza\StoreCreditGraphQl\Model\Resolver\Customer
 */
class Spending extends AbstractStoreCredit
{
    /**
     * @var SpendingManagement
     */
    protected $spendingManagement;

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var TotalsInformation
     */
    protected $totalInformation;

    /**
     * SpendingPoint constructor.
     *
     * @param SpendingManagement $spendingManagement
     * @param GetCartForUser $getCartForUser
     * @param TotalsInformation $totalInformation
     * @param Data $helperData
     */
    public function __construct(
        SpendingManagement $spendingManagement,
        GetCartForUser $getCartForUser,
        TotalsInformation $totalInformation,
        Data $helperData
    ) {
        $this->spendingManagement = $spendingManagement;
        $this->getCartForUser     = $getCartForUser;
        $this->totalInformation   = $totalInformation;
        parent::__construct($helperData);
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        parent::resolve($field, $context, $info, $value, $args);

        if ($this->helperData->versionCompare('2.3.3')) {
            $store = $context->getExtensionAttributes()->getStore();
            $quote = $this->getCartForUser->execute($args['cart_id'], $context->getUserId(), (int) $store->getId());
        } else {
            $quote = $this->getCartForUser->execute($args['cart_id'], $context->getUserId());
        }

        $totals = $this->spendingManagement->spend(
            $quote->getId(),
            $args['amount']
        );

        return $totals->getTotalSegments();
    }
}
