<?php

namespace PHPixieTests\ORM\Relationships\Type\Embedded\Type\Embeds\Type\Many;

/**
 * @coversDefaultClass \PHPixie\ORM\Relationships\Type\Embedded\Type\Embeds\Type\Many\Side
 */
class SideTest extends \PHPixieTests\ORM\Relationships\Type\Embedded\Type\Embeds\SideTest
{
    protected $ownerProperty = 'flowers';
    protected $relationshipType = 'embedsMany';

    protected function getConfig()
    {
        return $this->quickMock('\PHPixie\ORM\Relationships\Type\Embedded\Type\Embeds\Type\Many\Side\Config');
    }

    protected function getSide($type)
    {
        return new \PHPixie\ORM\Relationships\Type\Embedded\Type\Embeds\Type\Many\Side($type, $this->config);
    }
}
