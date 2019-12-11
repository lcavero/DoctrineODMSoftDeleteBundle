<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Interfaces;


interface Unique
{

    /**
     * @return string
     */
    public function getUniqueKeyInUseTranslation(): string;

    /**
     * @return string
     */
    public function getUniqueKeyName(): string;

    /**
     * @return mixed
     */
    public function getUniqueKeyValue();

    /**
     * @param $value
     */
    public function setUniqueKeyValue($value): void;
}
