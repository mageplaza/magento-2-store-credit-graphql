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

namespace Mageplaza\StoreCreditGraphQl\Model\Resolver;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Message\AbstractMessage;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Mageplaza\StoreCredit\Helper\Data;

/**
 * Class AddStoreCreditProductsToCart
 * @package Mageplaza\StoreCreditGraphQl\Model\Resolver
 */
class AddStoreCreditProductsToCart implements ResolverInterface
{
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * AddStoreCreditProductsToCart constructor.
     *
     * @param GetCartForUser $getCartForUser
     * @param Data $helperData
     * @param ProductRepositoryInterface $productRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        Data $helperData,
        ProductRepositoryInterface $productRepository,
        CartRepositoryInterface $cartRepository
    ) {
        $this->getCartForUser    = $getCartForUser;
        $this->helperData        = $helperData;
        $this->productRepository = $productRepository;
        $this->cartRepository    = $cartRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new GraphQlNoSuchEntityException(__('Store Credit is disabled.'));
        }

        if (empty($args['input']['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }
        $maskedCartId = $args['input']['cart_id'];

        if (empty($args['input']['store_credit_input'])
            || !is_array($args['input']['store_credit_input'])
        ) {
            throw new GraphQlInputException(__('Required parameter "store_credit_input" is missing'));
        }

        if ($this->helperData->versionCompare('2.3.3')) {
            $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
            $cart    = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);
        } else {
            $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId());
        }
        $storeCreditInput = $args['input']['store_credit_input'];
        $sku              = $storeCreditInput['sku'];
        $creditAmount     = isset($storeCreditInput['credit_amount']) ? $storeCreditInput['credit_amount'] : '';

        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__('Could not find a product with SKU "%sku"', ['sku' => $sku]));
        }

        try {
            $buyRequest = [
                'qty'           => 1,
                'sku'           => $sku,
                'quote_id'      => $cart->getId(),
                'credit_amount' => $creditAmount
            ];
            $result     = $cart->addProduct($product, new DataObject($buyRequest));
        } catch (Exception $e) {
            throw new GraphQlInputException(
                __(
                    'Could not add the product with SKU %sku to the shopping cart: %message',
                    ['sku' => $sku, 'message' => $e->getMessage()]
                )
            );
        }

        if (is_string($result)) {
            throw new GraphQlInputException(__($result));
        }

        if ($cart->getData('has_error')) {
            throw new GraphQlInputException(
                __('Shopping cart error: %message', ['message' => $this->getCartErrors($cart)])
            );
        }

        $this->cartRepository->save($cart);

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }

    /**
     * Collecting cart errors
     *
     * @param Quote $cart
     *
     * @return string
     */
    private function getCartErrors(Quote $cart): string
    {
        $errorMessages = [];

        /** @var AbstractMessage $error */
        foreach ($cart->getErrors() as $error) {
            $errorMessages[] = $error->getText();
        }

        return implode(PHP_EOL, $errorMessages);
    }
}
