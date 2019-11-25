<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Interfaces;


use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;

interface SoftDeleteable
{
    function getArchivedAt();
    function setArchivedAt($date);

    function setDeleteOn($date);
    function getDeleteOn();

    function getId();

    function onArchive(ArchiveManager $archiveManager);
    function onDelete(ArchiveManager $archiveManager);
    function onRestore(ArchiveManager $archiveManager);
}
