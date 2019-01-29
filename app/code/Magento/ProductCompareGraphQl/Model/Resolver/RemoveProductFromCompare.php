<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\Product\Compare\ItemFactory;
use Magento\ProductCompareGraphQl\Model\FilterManagement;

/**
 * Resolver for Remove Product(products) from Compare List
 */
class RemoveProductFromCompare implements ResolverInterface
{
    /**
     * @var ItemFactory
     */
    private $compareItemFactory;

    /**
     * @var FilterManagement
     */
    private $filterManagement;

    /**
     * @param FilterManagement $filterManagement
     * @param ItemFactory $compareItemFactory
     */
    public function __construct(
        FilterManagement $filterManagement,
        ItemFactory $compareItemFactory
    ) {
        $this->filterManagement = $filterManagement;
        $this->compareItemFactory = $compareItemFactory;
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
    ) {
        if (!isset($args['input']) || !is_array($args['input']) || empty($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        if (!isset($args['customerToken']) && !isset($args['hashed_id'])) {
            throw new GraphQlInputException(__('"customerToken" or "hashed_id" value should be specified'));
        } elseif (isset($args['customerToken']) && isset($args['hashed_id'])) {
            throw new GraphQlInputException(__('Only "customerToken" or only "hashed_id" value should be specified'));
        }

        $result = ['result' => false, "compareProducts" => []];
        if (!empty($args['input']['ids']) && is_array($args['input']['ids'])) {
            foreach ($args['input']['ids'] as $id) {
                $item = $this->compareItemFactory->create();
                if (isset($args['customerToken'])) {
                    $item = $this->filterManagement->addCustomerToItem($item, $args['customerToken']);
                } else {
                    $item = $this->filterManagement->addVisitorToItem($item, $args['hashed_id']);
                }
                $item->loadByProduct($id);
                if ($item->getId()) {
                    $item->delete();
                }
            }
            $result = ['result' => true, "compareProducts" => []];
        }

        return $result;
    }
}
