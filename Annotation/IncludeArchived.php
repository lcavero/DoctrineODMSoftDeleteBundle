<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class IncludeArchived
 * @package LCV\DoctrineODMSoftDeleteBundle\Annotation
 *
 * @Annotation
 * @Target("METHOD")
 */
class IncludeArchived extends Annotation { }
