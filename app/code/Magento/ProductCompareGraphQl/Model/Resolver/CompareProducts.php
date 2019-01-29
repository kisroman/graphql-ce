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
use Magento\ProductCompareGraphQl\Model\DataProvider\CompareProducts as CompareProductsDataProvider;

/**
 * CompareProducts field resolver, used for GraphQL request processing.
 */
class CompareProducts implements ResolverInterface
{
    /**
     * @var string
     */
    private $customerToken;

    /**
     * @var string
     */
    private $hashedId;

    /**
     * @var CompareProductsDataProvider
     */
    private $compareProducts;

    /**
     * @param CompareProductsDataProvider $compareProducts
     */
    public function __construct(
        CompareProductsDataProvider $compareProducts
    ) {
        $this->compareProducts = $compareProducts;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if ($field->getName() == 'compareProducts') {
            if (!isset($args['customerToken']) && !isset($args['hashed_id'])) {
                throw new GraphQlInputException(__('"customerToken" or "hashed_id" value should be specified'));
            } elseif (isset($args['customerToken']) && isset($args['hashed_id'])) {
                throw new GraphQlInputException(__('Only "customerToken" or only "hashed_id" value should be specified'));
            }

            if (isset($args['customerToken'])) {
                $this->customerToken = $args['customerToken'];
            } else {
                $this->hashedId = $args['hashed_id'];
            }

            return [];
        }

        $items = [];
        if ($this->customerToken !== null) {
            $items = $this->compareProducts->getFilteredByCustomer($this->customerToken);
        } elseif ($this->hashedId !== null) {
            $items = $this->compareProducts->getFilteredByVisitor($this->hashedId);
        }

        return $items;
    }
}
