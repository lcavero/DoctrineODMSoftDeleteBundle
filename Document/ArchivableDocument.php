<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;

/**
 * Class ArchivableDocument
 * @MongoDB\MappedSuperclass()
 */
abstract class ArchivableDocument extends SoftDeleteableDocument
{
    /**
     * @var DateTime|null
     * @MongoDB\Field(type="date")
     * @MongoDB\Index
     * @Serializer\Groups({"COMMON.archived"})
     */
    protected $archivedAt;

    /**
     * @return DateTime|null
     */
    public function getArchivedAt(): ?DateTime
    {
        return $this->archivedAt;
    }

    /**
     * @param DateTime|null $archivedAt
     */
    public function setArchivedAt(?DateTime $archivedAt): void
    {
        $this->archivedAt = $archivedAt;
    }

    /**
     * @param ArchiveManager $archiveManager
     */
    public function onArchive(ArchiveManager $archiveManager): void
    {
        return;
    }

    /**
     * @param ArchiveManager $archiveManager
     */
    public function onRestore(ArchiveManager $archiveManager): void
    {
        return;
    }
}
