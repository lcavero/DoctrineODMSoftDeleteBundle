<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Interfaces;


interface UniqueSoftDeleteable extends SoftDeleteable
{
    function getUniqueKeyName();
    function getUniqueKeyValue();
    function setUniqueKeyValue($value);
    function getUniqueKeyInUseTranslation();
}
