<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Interfaces;

use DateTime;
use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;

/**
 * Class Archivable
 */
interface Archivable
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return DateTime|null
     */
    public function getArchivedAt(): ?DateTime;

    /**
     * @param DateTime|null $archivedAt
     */
    public function setArchivedAt(?DateTime $archivedAt): void;

    /**
     * @param ArchiveManager $archiveManager
     */
    public function onArchive(ArchiveManager $archiveManager): void;

    /**
     * @param ArchiveManager $archiveManager
     */
    public function onRestore(ArchiveManager $archiveManager): void;
}
