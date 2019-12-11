<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Filter;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Filter\BsonFilter;
use LCV\DoctrineODMSoftDeleteBundle\Document\ArchivableDocument;

class ArchivableFilter extends BsonFilter
{
    public function addFilterCriteria(ClassMetadata $class): array
    {
        if(!is_a($class->getReflectionClass(), ArchivableDocument::class)){
            return [];
        }
        return ['archivedAt' => null];
    }
}
