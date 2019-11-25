<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Document;

use DateTime;
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
     * @Serializer\Groups({"COMMON.created"})
     */
    protected $created;

    /**
     * @MongoDB\Field(type="date")
     * @Serializer\Exclude()
     */
    protected $updated;

    /**
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     * @Serializer\Groups({"COMMON.archived"})
     */
    protected $archivedAt;

    /**
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     * @Serializer\Exclude()
     */
    protected $deleteOn;


    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    public function setArchivedAt($date)
    {
        $this->archivedAt = $date;
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
