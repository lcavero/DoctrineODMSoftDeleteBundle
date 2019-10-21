<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Interfaces;


interface SoftDeleteable
{
    function getDeletedAt();
    function setDeletedAt($date);

    function setDeleteOn($date);
    function getDeleteOn();

    function getId();
}
