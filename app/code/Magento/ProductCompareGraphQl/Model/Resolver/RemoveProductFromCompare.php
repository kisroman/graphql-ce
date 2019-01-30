<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model\Resolver;

use Magento\Catalog\Model\CompareList\HashedListIdToListIdInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\Product\Compare\ItemFactory;

/**
 * Resolver for Remove Product(products) from Compare List.
 */
class RemoveProductFromCompare implements ResolverInterface
{
    /**
     * @var ItemFactory
     */
    private $compareItemFactory;

    /**
     * @var HashedListIdToListIdInterface
     */
    private $hashedListIdToListId;

    /**
     * @param ItemFactory $compareItemFactory
     * @param HashedListIdToListIdInterface $hashedListIdToListId
     */
    public function __construct(
        ItemFactory $compareItemFactory,
        HashedListIdToListIdInterface $hashedListIdToListId
    ) {
        $this->compareItemFactory = $compareItemFactory;
        $this->hashedListIdToListId = $hashedListIdToListId;
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

        if (!isset($args['hashed_id']) || !is_string($args['hashed_id'])) {
            throw new GraphQlInputException(__('"hashed_id" value should be specified'));
        }

        $result = ['result' => false, "compareProducts" => []];

        if (!empty($args['input']['ids']) && is_array($args['input']['ids'])) {
            $customerId = $context->getUserId();

            foreach ($args['input']['ids'] as $id) {
                $item = $this->compareItemFactory->create();
                if (0 !== $customerId && null !== $customerId) {
                    $item->setCustomerId($customerId);
                }

                $listId = $this->hashedListIdToListId->execute($args['hashed_id']);
                $item->setCatalogCompareListId($listId);
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
