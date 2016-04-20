<?php

namespace Fazb\RedBean\Model;

use RedBeanPHP\SimpleModel;

/**
 * BaseModel
 */
abstract class BaseModel extends SimpleModel
{
    public function getType()
    {
        return $this->bean->getMeta('type');
    }
}
