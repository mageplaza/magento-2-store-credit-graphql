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

namespace Mageplaza\StoreCreditGraphQl\Model\Resolver\StoreCreditCustomer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Mageplaza\StoreCredit\Helper\Data;
use Mageplaza\StoreCredit\Model\CustomerFactory;

/**
 * Class Account
 * @package Mageplaza\StoreCreditGraphQl\Model\Resolver\StoreCreditCustomer
 */
class Account implements ResolverInterface
{
    /**
     * @var CustomerFactory
     */
    protected $storeCreditCustomerFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Account constructor.
     *
     * @param CustomerFactory $storeCreditCustomerFactory
     * @param Data $helperData
     */
    public function __construct(
        CustomerFactory $storeCreditCustomerFactory,
        Data $helperData
    ) {
        $this->helperData                 = $helperData;
        $this->storeCreditCustomerFactory = $storeCreditCustomerFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {

        if (!$this->helperData->isEnabled()) {
            return [];
        }

        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        $customer = $value['model'];

        $storeCreditCustomer            = $this->storeCreditCustomerFactory->create()->loadByCustomerId($customer->getId());
        $data                           = $storeCreditCustomer->toArray();
        $data['mp_credit_balance']      = $storeCreditCustomer->getMpCreditBalance();
        $data['mp_credit_notification'] = $storeCreditCustomer->getMpCreditNotification();

        $data['customer'] = $customer;

        return $data;
    }
}
