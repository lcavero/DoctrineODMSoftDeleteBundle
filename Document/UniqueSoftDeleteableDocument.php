<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\UniqueSoftDeleteable;

/**
 * Class UniqueSoftDeleteableDocument
 * @MongoDB\MappedSuperclass()
 */
abstract class UniqueSoftDeleteableDocument extends SoftDeleteableDocument implements UniqueSoftDeleteable
{
    public function getUniqueKeyInUseTranslation()
    {
        return "keyName_in_use";
    }

    public function getUniqueKeyValue()
    {
        return $this->keyName;
    }

    public function setUniqueKeyValue($value)
    {
        $this->keyName = $value;
    }

    public function getUniqueKeyName()
    {
        return 'keyName';
    }
}