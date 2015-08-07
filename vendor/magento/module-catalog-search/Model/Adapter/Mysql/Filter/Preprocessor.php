<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSearch\Model\Adapter\Mysql\Filter;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Resource;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\Search\Adapter\Mysql\Filter\PreprocessorInterface;
use Magento\Framework\Search\Adapter\Mysql\Query\QueryContainer;
use Magento\Framework\Search\Request\FilterInterface;

class Preprocessor implements PreprocessorInterface
{
    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var string
     */
    private $attributePrefix;

    /**
     * @param ConditionManager $conditionManager
     * @param ScopeResolverInterface $scopeResolver
     * @param Config $config
     * @param Resource $resource
     * @param string $attributePrefix
     */
    public function __construct(
        ConditionManager $conditionManager,
        ScopeResolverInterface $scopeResolver,
        Config $config,
        Resource $resource,
        $attributePrefix
    ) {
        $this->conditionManager = $conditionManager;
        $this->scopeResolver = $scopeResolver;
        $this->config = $config;
        $this->resource = $resource;
        $this->attributePrefix = $attributePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function process(FilterInterface $filter, $isNegation, $query, QueryContainer $queryContainer)
    {
        return $this->processQueryWithField($filter, $isNegation, $query, $queryContainer);
    }

    /**
     * @param FilterInterface $filter
     * @param bool $isNegation
     * @param string $query
     * @param QueryContainer $queryContainer
     * @return string
     */
    private function processQueryWithField(FilterInterface $filter, $isNegation, $query, QueryContainer $queryContainer)
    {
        $currentStoreId = $this->scopeResolver->getScope()->getId();

        $attribute = $this->config->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $filter->getField());
        $select = $this->getConnection()->select();
        $table = $attribute->getBackendTable();
        if ($filter->getField() == 'price') {
            $query = str_replace('price', 'min_price', $query);
            $select->from(['main_table' => $this->resource->getTableName('catalog_product_index_price')], 'entity_id')
                ->where($query);
        } elseif ($filter->getField() == 'category_ids') {
            return 'category_index.category_id = ' . $filter->getValue();
        } else {
            if ($attribute->isStatic()) {
                $select->from(['main_table' => $table], 'entity_id')
                    ->where($query);
            } else {
                if ($filter->getType() == FilterInterface::TYPE_TERM) {
                    if (is_array($filter->getValue())) {
                        $value = sprintf(
                            '%s IN (%s)',
                            ($isNegation ? 'NOT' : ''),
                            implode(',', $filter->getValue())
                        );
                    } else {
                        $value = ($isNegation ? '!' : '') . '= ' . $filter->getValue();
                    }
                    $filterQuery = sprintf(
                        'cpie.store_id = %d AND cpie.attribute_id = %d AND cpie.value %s',
                        $this->scopeResolver->getScope()->getId(),
                        $attribute->getId(),
                        $value
                    );
                    $queryContainer->addFilter($filterQuery);
                    return '';
                }
                $ifNullCondition = $this->getConnection()->getIfNullSql('current_store.value', 'main_table.value');

                $select->from(['main_table' => $table], 'entity_id')
                    ->joinLeft(
                        ['current_store' => $table],
                        'current_store.attribute_id = main_table.attribute_id AND current_store.store_id = '
                        . $currentStoreId,
                        null
                    )
                    ->columns([$filter->getField() => $ifNullCondition])
                    ->where(
                        'main_table.attribute_id = ?',
                        $attribute->getAttributeId()
                    )
                    ->where('main_table.store_id = ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                    ->having($query);
            }
        }

        return 'search_index.entity_id IN (
            select entity_id from  ' . $this->conditionManager->wrapBrackets($select) . ' as filter
            )';
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }
}
