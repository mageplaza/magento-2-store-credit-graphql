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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Mageplaza\StoreCredit\Helper\Data;

/**
 * Class StoreCreditDiscount
 * @package Mageplaza\StoreCreditGraphQl\Model\Resolver\Customer
 */
class StoreCreditDiscount implements ResolverInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * AbstractStoreCredit constructor.
     *
     * @param Data $helperData
     */
    public function __construct(Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return array[]
     * @throws GraphQlNoSuchEntityException
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new GraphQlNoSuchEntityException(__('Store Credit is disabled.'));
        }

        if ($this->helperData->versionCompare('2.3.3')) {
            if ($context->getExtensionAttributes()->getIsCustomer() === false) {
                return null;
            }
        }

        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        return [
            [
                'code'  => 'mp_store_credit_spent',
                'title' => __('Store Credit'),
                'value' => $value['model']->getMpStoreCreditDiscount()
            ]
        ];
    }
}
