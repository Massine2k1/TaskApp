<?php

namespace model;

abstract class AbstractMapping
{
    public function __construct(array $datas)
    {
        $this->hydrate($datas);
    }

    protected function hydrate(array $datas)
    {
        foreach ($datas as $setter=>$value) {
            
            $setterName = 'set'.str_replace("_","",ucwords($setter,'_'));
            if (method_exists($this,$setterName)) {
                $this->$setterName($value);
            }
        }
    }
}