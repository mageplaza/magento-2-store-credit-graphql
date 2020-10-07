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

use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\StoreCredit\Api\StoreCreditCustomerRepositoryInterface;
use Mageplaza\StoreCredit\Helper\Data;
use Mageplaza\StoreCreditGraphQl\Model\Resolver\AbstractStoreCredit;

/**
 * Class UpdateBalanceNotification
 * @package Mageplaza\StoreCreditGraphQl\Model\Resolver\Customer
 */
class UpdateBalanceNotification extends AbstractStoreCredit
{
    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var StoreCreditCustomerRepositoryInterface
     */
    private $storeCreditCustomerRepository;

    /**
     * UpdateBalanceNotification constructor.
     *
     * @param Data $helperData
     * @param GetCustomer $getCustomer
     * @param StoreCreditCustomerRepositoryInterface $storeCreditCustomerRepository
     */
    public function __construct(
        Data $helperData,
        GetCustomer $getCustomer,
        StoreCreditCustomerRepositoryInterface $storeCreditCustomerRepository
    ) {
        $this->getCustomer                   = $getCustomer;
        $this->storeCreditCustomerRepository = $storeCreditCustomerRepository;
        $this->helperData                    = $helperData;

        parent::__construct($helperData);
    }

    /**
     * @inheritdoc
     */
    public
    function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        parent::resolve($field, $context, $info, $value, $args);

        $customer = $this->getCustomer->execute($context);

        if (!isset($args['mp_credit_notification'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }
        $this->storeCreditCustomerRepository->updateNotification($customer->getId(), $args['mp_credit_notification']);

        return true;
    }
}
