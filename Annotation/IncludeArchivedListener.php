<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Annotation;


use Doctrine\Common\Annotations\Reader;
use Doctrine\ODM\MongoDB\DocumentManager;
use ReflectionObject;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class IncludeArchivedListener
{
    private $reader;
    private $dm;

    public function __construct(Reader $reader, DocumentManager $dm)
    {
        $this->reader = $reader;
        $this->dm = $dm;
    }

    public function onKernelController(ControllerEvent $event)
    {
        if(!is_array($controller = $event->getController())){
            return;
        }

        list($controller, $method) = $controller;

        $this->ignoreSoftDeleteAnnotation($controller, $method);
    }

    private function readAnnotation($controller, $method, $annotation) {
        $objectReflection = new ReflectionObject($controller);
        $methodReflection = $objectReflection->getMethod($method);
        $methodAnnotation = $this->reader->getMethodAnnotation($methodReflection, $annotation);

        if (!$methodAnnotation) {
            return false;
        }

        return [$methodAnnotation, $methodReflection];
    }

    private function ignoreSoftDeleteAnnotation($controller, $method) {
        static $class = IncludeArchived::class;

        if ($this->readAnnotation($controller, $method, $class)) {
            $this->dm->getFilterCollection()->disable('archivable');
        }
    }

}
