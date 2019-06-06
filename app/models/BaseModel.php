<?php

namespace App\models;

use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
    public function onConstruct()
    {
        $this->setSource($this->getPluralClassName());
    }

    protected function getPluralClassName() : string
    {
        $name = get_class($this);

        $parts = explode('\\', $name);

        $className = array_pop($parts);

        if (substr($className, -1) == "s") {
            return strtolower($className);
        }

        return strtolower($className) . "s";
    }
}