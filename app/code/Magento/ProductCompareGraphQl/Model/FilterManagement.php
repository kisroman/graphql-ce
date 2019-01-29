<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model;

use Magento\Catalog\Model\CompareList\HashedListIdToListIdInterface;
use Magento\Catalog\Model\Product\Compare\Item;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Integration\Model\Oauth\TokenFactory;

class FilterManagement
{
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var HashedListIdToListIdInterface
     */
    private $hashedListIdToListId;

    /**
     * @param TokenFactory $tokenFactory
     * @param HashedListIdToListIdInterface $hashedListIdToListId
     */
    public function __construct(
        TokenFactory $tokenFactory,
        HashedListIdToListIdInterface $hashedListIdToListId
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->hashedListIdToListId = $hashedListIdToListId;
    }

    /**
     * @param Item $item
     * @param string $token
     *
     * @return Item
     */
    public function addCustomerIdToItem(Item $item, string $token)
    {
        $customerId = $this->getCustomerIdByToken($token);

        if ($customerId) {
            $item->setCustomerId((int)$customerId);
        }

        return $item;
    }

    public function addHashIdToItem(Item $item, string $hashedId)
    {
        $listId = $this->hashedListIdToListId->execute($hashedId);
        $item->setCatalogCompareListId($listId);

        return $item;
    }


    public function addHashIdFilterToCollection(Collection $collection, string $hashedId)
    {
    }

    /**
     * @param string $token
     *
     * @return int
     */
    private function getCustomerIdByToken(string $token)
    {
        $oathToken = $this->tokenFactory->create()->loadByToken($token);

        return $oathToken->getCustomerId();
    }


    /**
     * @param Collection $collection
     * @param string $token
     *
     * @throws GraphQlAuthorizationException
     */
    public function addCustomerIdFilterToCollection(Collection $collection, string $token)
    {
        $customerId = $this->getCustomerIdByToken($token);

        if ($customerId) {
            $collection->setCustomerId($customerId);
        } else {
            throw new GraphQlAuthorizationException(__('Invalid "customerToken" provided.'));
        }
    }
}
