<?php

namespace PHPixieTests\ORM\Relationships\Type\Embeds\Type\One;

/**
 * @coversDefaultClass \PHPixie\ORM\Relationships\Type\Embeds\Type\One\Handler
 */
class HandlerTest extends \PHPixieTests\ORM\Relationships\Type\Embeds\HandlerTest
{
    protected $ownerPropertyName = 'ownerItemProperty';
    protected $configOwnerProperty = 'flower';

    /**
     * @covers ::setItem
     * @covers ::<protected>
     */
    public function testSetItem()
    {
        $this->setItemTest();
        $this->setItemTest(true);
        $this->setItemTest(true, true);
    }

    /**
     * @covers ::offsetSet
     * @covers ::<protected>
     */
    public function testOffsetSetWrongModel() {
        $owner = $this->getOwner();
        $item = $this->prepareWrongItem();
        $this->handler->setItem($owner['entity'], $this->propertyConfig, $item);
    }

    /**
     * @covers ::setItem
     * @covers ::<protected>
     */
    public function testRemoveItem()
    {
        $this->removeItemTest();
        $this->removeItemTest(true);
    }

    /**
     * @covers ::createItem
     * @covers ::<protected>
     */
    public function testCreateItem()
    {
        $this->createItemTest();
        $this->createItemTest(true);
        $this->createItemTest(true, true);
    }

    /**
     * @covers ::loadProperty
     * @covers ::<protected>
     */
    public function testLoadProperty()
    {
        $owner = $this->getOwner();
        $item = $this->getItem();

        $document = $this->prepareGetDocument($owner['document'], $this->configData['path']);
        $this->method($this->modelMocks['embedded'], 'loadEntity', $item['entity'], array($this->configData['itemModel'], $document), 0);
        $this->method($item['entity'], 'setOwnerRelationship',null , array($owner['entity'], $this->configData['ownerItemProperty']), 0);
        $this->handler->loadProperty($this->propertyConfig, $owner['entity']);
    }

    protected function setItemTest($ownerIsLoaded = false, $ownerItemIsNull = false)
    {
        $owner = $this->getOwner($ownerIsLoaded, $ownerItemIsNull);
        $this->prepareUnsetCurrentItemOwner($owner, $ownerIsLoaded, $ownerItemIsNull);
        
        $item = $this->getItem();
        $this->prepareSetItemModel($owner, $item);
        $this->handler->setItem($owner['entity'], $this->propertyConfig, $item['entity']);
    }

    protected function removeItemTest($ownerIsLoaded = false, $ownerItemIsNull = false)
    {
        $owner = $this->getOwner($ownerIsLoaded, $ownerItemIsNull);
        $this->prepareUnsetCurrentItemOwner($owner, $ownerIsLoaded, $ownerItemIsNull);
        $this->prepareRemoveItem($owner);
        $this->handler->removeItem($owner['entity'], $this->propertyConfig);
    }

    protected function createItemTest($ownerIsLoaded = false, $ownerItemIsNull = false)
    {

        $item = $this->getItem();
        $owner = $this->getOwner($ownerIsLoaded, $ownerItemIsNull);
        $this->prepareUnsetCurrentItemOwner($owner, $ownerIsLoaded, $ownerItemIsNull);
        
        $data = array('name' => 'pixie');

        $this->method($this->modelMocks['embedded'], 'loadEntityFromData', $item['entity'], array($this->configData['itemModel'], $data), 0);
        $this->prepareSetItemModel($owner, $item, 1);
        $this->handler->createItem($owner['entity'], $this->propertyConfig, $data);
    }


    protected function prepareSetItemModel($owner, $item, $itemOffset = 2)
    {
        list($document, $key) = $this->prepareGetParentDocumentAndKey($owner['document'], $this->configData['path']);
        $this->method($document, 'set', null, array($key, $item['document']), 0);
        $this->method($item['entity'], 'setOwnerRelationship', null, array($owner['entity'], $this->configData['ownerItemProperty']), $itemOffset);
        $this->preparePopertySetValue($owner['property'], $item['entity']);
    }

    protected function prepareRemoveItem($owner)
    {
        list($document, $key) = $this->prepareGetParentDocumentAndKey($owner['document'], $this->configData['path']);
        $this->method($document, 'remove', null, array($key), 0);
        $this->preparePopertySetValue($owner['property'], null);
    }

    protected function preparePopertySetValue($property, $value)
    {
        $property
            ->expects($this->once())
            ->method('setValue')
            ->with($value);
    }
    
    protected function prepareUnsetCurrentItemOwner($owner, $ownerIsLoaded = true, $ownerItemIsNull =  false)
    {
        if($ownerIsLoaded && !$ownerItemIsNull) {
            $this->method($owner['item']['entity'], 'unsetOwnerRelationship', null, array(), 0);
        }
    }
    
    protected function getOwner($loaded = false, $itemIsNull = false, $propertyName = null)
    {
        if($propertyName == null) {
            $propertyName = $this->configOwnerProperty;
        }
        
        $owner = $this->getRelationshipEntity('owner');
        $property = $this->getProperty();
        $itemModel = null;
        if($loaded && !$itemIsNull) {
            $owner['item'] = $this->getItem($owner);
            $itemModel = $owner['item']['entity'];
        }
        $this->method($property, 'isLoaded', $loaded, array());
        $this->method($property, 'value', $itemModel, array());

        $this->method($owner['entity'], 'getRelationshipProperty', $property, array($propertyName), null, true);
        $owner['property'] = $property;
        return $owner;
    }

    protected function prepareMapConditionBuilder($builder, $side, $collection, $plan)
    {
        $container = $this->quickMock('\PHPixie\Database\Type\Document\Conditions\Builder\Container');
        
        $this->method($builder, 'addSubdocumentPlaceholder', $container, array(
            $this->configData['path'],
            $collection->logic(),
            $collection->isNegated(),
        ), 0);
        
        $this->method($this->mapperMocks['group'], 'map', null, array(
            $container,
            $this->configData['itemModel'],
            $collection->conditions(),
            $plan
        ), 0);
        
    }

    protected function getProperty()
    {
        return $this->quickMock('\PHPixie\ORM\Relationships\Type\Embeds\Type\One\Property\Entity\Item');
    }

    protected function getPreloader() {
        return $this->quickMock('\PHPixie\ORM\Relationships\Type\Embeds\Type\One\Preloader');
    }
    
    protected function getPreloadResult()
    {
        return $this->quickMock('\PHPixie\ORM\Relationships\Type\Embeds\Type\One\Preload\Result');
    }

    protected function getConfig()
    {
        return $this->quickMock('\PHPixie\ORM\Relationships\Type\Embeds\Type\One\Config');
    }

    protected function getSide()
    {
        return $this->quickMock('\PHPixie\ORM\Relationships\Type\Embeds\Type\One\Side');
    }

    protected function getRelationship()
    {
        return $this->quickMock('\PHPixie\ORM\Relationships\Type\EmbedsOne');
    }

    protected function getHandler()
    {
        return new \PHPixie\ORM\Relationships\Type\Embeds\Type\One\Handler(
            $this->models,
            $this->planners,
            $this->plans,
            $this->steps,
            $this->loaders,
            $this->mappers,
            $this->relationship
        );
    }
}
