<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\SoftDeleteable;

/**
 * Class SoftDeleteableDocument
 * @MongoDB\MappedSuperclass()
 */
abstract class SoftDeleteableDocument implements SoftDeleteable
{
    /**
     * @MongoDB\Field(type="date")
     */
    protected $created;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $updated;

    /**
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     * @Serializer\Groups({"COMMON.deletion"})
     */
    protected $deletedAt;

    /**
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     * @Serializer\Groups({"COMMON.deletion"})
     */
    protected $deleteOn;


    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($date)
    {
        $this->deletedAt = $date;
    }

    public function getDeleteOn()
    {
        return $this->deleteOn;
    }

    public function setDeleteOn($date)
    {
        $this->deleteOn = $date;
    }
}