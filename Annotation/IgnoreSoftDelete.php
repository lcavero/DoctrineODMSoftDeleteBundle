<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class IgnoreSoftDelete
 * @package LCV\DoctrineODMSoftDeleteBundle\Annotation
 *
 * @Annotation
 * @Target("METHOD")
 */
class IgnoreSoftDelete extends Annotation { }