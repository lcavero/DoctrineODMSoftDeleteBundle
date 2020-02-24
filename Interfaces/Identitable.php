<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Interfaces;


interface Identitable
{
    /**
     * @return string
     */
    public function getId(): ?string;
}
