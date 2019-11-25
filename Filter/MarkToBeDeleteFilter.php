<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Filter;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\SoftDeleteable;

class MarkToBeDeleteFilter extends BsonFilter
{
    public function addFilterCriteria(ClassMetadata $class): array
    {
        if(!(class_implements($class->reflClass, SoftDeleteable::class))){
            return [];
        }
        return ['deleteOn' => null];
    }
}
