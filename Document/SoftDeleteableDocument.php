<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;

/**
 * Class SoftDeleteableDocument
 * @MongoDB\MappedSuperclass()
 */
abstract class SoftDeleteableDocument extends TimestampableDocument
{
    /**
     * @var DateTime|null
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     * @Serializer\Exclude()
     */
    protected $deleteOn;

    /**
     * @return DateTime|null
     */
    public function getDeleteOn(): ?DateTime
    {
        return $this->deleteOn;
    }

    /**
     * @param DateTime|null $deleteOn
     */
    public function setDeleteOn(?DateTime $deleteOn): void
    {
        $this->deleteOn = $deleteOn;
    }

    /**
     * @param ArchiveManager $archiveManager
     */
    public function onDelete(ArchiveManager $archiveManager): void
    {
        return;
    }
}
