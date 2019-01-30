<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model\Resolver;

use Magento\Catalog\Model\CompareList\HashedListIdToListIdInterface;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;

/**
 * CompareProducts field resolver, used for GraphQL request processing.
 */
class CompareProducts implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HashedListIdToListIdInterface
     */
    private $hashedListIdToListId;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Config $catalogConfig
     * @param StoreManagerInterface $storeManager
     * @param HashedListIdToListIdInterface $hashedListIdToListId
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Config $catalogConfig,
        StoreManagerInterface $storeManager,
        HashedListIdToListIdInterface $hashedListIdToListId
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->storeManager = $storeManager;
        $this->hashedListIdToListId = $hashedListIdToListId;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['hashed_id']) || !is_string($args['hashed_id'])) {
            throw new GraphQlInputException(__('"hashed_id" value should be specified'));
        }

        $context->setData('hashed_id', $args['hashed_id']);

        return [];
    }
}
