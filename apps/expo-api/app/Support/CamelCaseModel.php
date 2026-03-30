<?php

namespace App\Support;

trait CamelCaseModel
{
    public $incrementing = true;
    protected $keyType = 'int';

    public function getAttribute($key)
    {
        // Try snake_case first (Laravel default), then camelCase
        $value = parent::getAttribute($key);
        if ($value === null && $key !== $camel = $this->toCamelCase($key)) {
            $value = parent::getAttribute($camel);
        }
        return $value;
    }

    public function setAttribute($key, $value)
    {
        // Convert snake_case to camelCase for DB storage
        $camel = $this->toCamelCase($key);
        if ($camel !== $key && in_array($camel, $this->getCamelColumns())) {
            return parent::setAttribute($camel, $value);
        }
        return parent::setAttribute($key, $value);
    }

    protected function toCamelCase($str)
    {
        return lcfirst(str_replace('_', '', ucwords($str, '_')));
    }

    protected function getCamelColumns()
    {
        return [];
    }
}
