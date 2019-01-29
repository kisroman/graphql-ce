<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model\DataProvider;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\ProductCompareGraphQl\Model\FilterManagement;
use Magento\Store\Model\StoreManagerInterface;

class CompareProducts
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
     * @var FilterManagement
     */
    private $filterManagement;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Config $catalogConfig
     * @param StoreManagerInterface $storeManager
     * @param FilterManagement $filterManagement
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Config $catalogConfig,
        StoreManagerInterface $storeManager,
        FilterManagement $filterManagement
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->storeManager = $storeManager;
        $this->filterManagement = $filterManagement;
    }

    /**
     * @param string $customerToken
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException
     */
    public function getFilteredByCustomer(string $customerToken)
    {
        $collection = $this->loadCollection();
        $this->filterManagement->addCustomerToCollection($collection, $customerToken);
        $items = $this->getItemsFromCollection($collection);

        return $items;
    }

    /**
     * @param string $hashedId
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFilteredByVisitor(string $hashedId)
    {
        $collection = $this->loadCollection();
        $this->filterManagement->addVisitorToCollectionItem($collection, $hashedId);
        $items = $this->getItemsFromCollection($collection);

        return $items;
    }

    /**
     * @return Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function loadCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->useProductItem(true)->setStoreId($this->storeManager->getStore()->getId());
        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes());
        $collection->loadComparableAttributes();

        return $collection;
    }

    /**
     * @param Collection $collection
     *
     * @return array
     */
    private function getItemsFromCollection(Collection $collection)
    {
        $items = [];

        foreach ($collection as $item) {
            $productData = $item->getData();
            $productData['model'] = $item;
            $items[] = [
                'item_id' => $item->getData('catalog_compare_item_id'),
                'product' => $productData
            ];
        }

        return $items;
    }
}
