<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Filter;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;
use LCV\DoctrineODMSoftDeleteBundle\Document\SoftDeleteableDocument;


class MarkToBeDeleteFilter extends BsonFilter
{
    public function addFilterCriteria(ClassMetadata $class): array
    {
        if(!is_a($class->getReflectionClass(), SoftDeleteableDocument::class)){
            return [];
        }
        return ['deleteOn' => null];
    }
}
