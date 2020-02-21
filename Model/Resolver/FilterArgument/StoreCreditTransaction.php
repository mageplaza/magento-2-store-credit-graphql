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

namespace Mageplaza\StoreCreditGraphQl\Model\Resolver\FilterArgument;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;

/**
 * Class StoreCreditTransaction
 * @package Mageplaza\StoreCreditGraphQl\Model\Resolver\FilterArgument
 */
class StoreCreditTransaction implements FieldEntityAttributesInterface
{
    /**
     * @var string
     */
    protected $type = 'MpStoreCreditCustomerTransaction';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * FilterArgument constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getEntityAttributes(): array
    {
        $fields = [];

        /** @var Field $field */
        foreach ($this->config->getConfigElement($this->type)->getFields() as $field) {
            $fields[$field->getName()] = '';
        }

        return array_keys($fields);
    }
}
