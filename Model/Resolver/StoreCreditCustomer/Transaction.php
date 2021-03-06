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

use Magento\Customer\Model\Customer;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\StoreCredit\Api\Data\TransactionSearchResultInterface;
use Mageplaza\StoreCredit\Model\TransactionRepository;
use Mageplaza\StoreCreditGraphQl\Model\Resolver\AbstractStoreCredit;
use Mageplaza\StoreCredit\Helper\Data;

/**
 * Class Transaction
 * @package Mageplaza\StoreCreditGraphQl\Model\Resolver\StoreCreditCustomer
 */
class Transaction extends AbstractStoreCredit
{
    /**
     * @var string
     */
    protected $fieldName = 'mp_store_credit_transactions';

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Transaction constructor.
     *
     * @param Data $helperData
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(
        Data $helperData,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransactionRepository $transactionRepository
    ) {
        parent::__construct($helperData);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        parent::resolve($field, $context, $info, $value, $args);

        if (!isset($value['customer'])) {
            return [];
        }

        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }

        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        $searchCriteria = $this->searchCriteriaBuilder->build($this->fieldName, $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);
        $searchResult = $this->getSearchResult($value['customer'], $searchCriteria);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItems(),
        ];
    }

    /**
     * @param Customer $customer
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return TransactionSearchResultInterface|mixed
     */
    public function getSearchResult($customer, $searchCriteria)
    {
        $result = $this->transactionRepository->getTransactionByCustomerId($customer->getId(), $searchCriteria);
        foreach ($result->getItems() as $item) {
            $item->setComment($item->getTitle());
        }

        return $result;
    }
}
