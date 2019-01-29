<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model\Resolver;

use Magento\Catalog\Model\CompareListFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Math\Random;

/**
 * CompareProducts field resolver, used for GraphQL request processing.
 */
class CreateCompareList implements ResolverInterface
{
    /**
     * @var CompareListFactory 
     */
    private $compareListFactory;

    /**
     * @var Random
     */
    private $randomDataGenerator;

    /**
     * @param CompareListFactory $compareListFactory
     * @param Random $randomDataGenerator
     */
    public function __construct(
        CompareListFactory $compareListFactory,
        Random $randomDataGenerator
    ) {
        $this->compareListFactory = $compareListFactory;
        $this->randomDataGenerator = $randomDataGenerator;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $compareList = $this->compareListFactory->create();
        $compareList->setHashedId($this->randomDataGenerator->getUniqueHash())->save();

        return ['hashed_id' => $compareList->getHashedId()];
    }
}
