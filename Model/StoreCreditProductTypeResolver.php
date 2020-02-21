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

namespace Mageplaza\StoreCreditGraphQl\Model;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;
use Mageplaza\StoreCredit\Model\Product\Type\StoreCredit;

/**
 * Class StoreCreditProductTypeResolver
 * @package Mageplaza\StoreCreditGraphQl\Model
 */
class StoreCreditProductTypeResolver implements TypeResolverInterface
{
    const BUNDLE_PRODUCT = 'StoreCreditProduct';

    /**
     * @inheritdoc
     */
    public function resolveType(array $data): string
    {
        if (isset($data['type_id']) && $data['type_id'] == StoreCredit::TYPE_STORE_CREDIT) {
            return self::BUNDLE_PRODUCT;
        }

        return '';
    }
}
