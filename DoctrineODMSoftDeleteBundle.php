<?php

namespace LCV\DoctrineODMSoftDeleteBundle;

use LCV\DoctrineODMSoftDeleteBundle\DependencyInjection\DoctrineODMSoftDeleteExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DoctrineODMSoftDeleteBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DoctrineODMSoftDeleteExtension();
    }

}
