<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Interfaces;

use DateTime;
use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;

/**
 * Interface PostDeletableDocument
 */
interface PostDeletable
{
    /**
     * @return DateTime|null
     */
    public function getDeleteOn(): ?DateTime;

    /**
     * @param DateTime|null $deleteOn
     */
    public function setDeleteOn(?DateTime $deleteOn): void;

    /**
     * @param ArchiveManager $archiveManager
     */
    public function onDelete(ArchiveManager $archiveManager): void;
}
