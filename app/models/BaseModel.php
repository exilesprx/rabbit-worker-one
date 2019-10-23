<?php

namespace App\Models;

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

        $className = self::camelCaseClassName($parts);

        if (substr($className, -1) == "s") {
            return strtolower($className);
        }

        return strtolower($className) . "s";
    }

    private static function camelCaseClassName(array $parts)
    {
        $className = array_pop($parts);

        preg_match_all("/[A-Z][a-z]+/", $className, $matches, PREG_PATTERN_ORDER);

        return implode("_", $matches[0]);
    }
}