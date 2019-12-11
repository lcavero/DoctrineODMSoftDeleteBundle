<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Manager;

use DateTime;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use LCV\CommonExceptions\Exception\ApiException;
use LCV\DoctrineODMSoftDeleteBundle\Document\ArchivableDocument;
use LCV\DoctrineODMSoftDeleteBundle\Document\SoftDeleteableDocument;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\Unique;
use MongoDB\BSON\Regex;

class ArchiveManager implements ObjectManager
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function getDocumentManager()
    {
        return $this->dm;
    }

    /**
     * @param $documentName
     * @param $id
     * @return object|null
     * @throws Exception
     *
     * Busca un documento archivado por su id
     */
    public function findArchived($documentName, $id)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');
        try{
            $this->disableSoftDeleteFilter();
            return $this->dm->getRepository($documentName)->findOneBy(
                ['id' => $id, 'archivedAt' => new Regex('\.+')]
            );
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }


    /**
     * @param $documentName
     * @param $id
     * @param bool $excludeArchived
     * @return object|null
     * @throws Exception
     *
     * Busca un documento archivado o no por su id
     */
    public function find($documentName, $id, $excludeArchived = false)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');

        try{
            if($excludeArchived){
                $this->enableSoftDeleteFilter();
            }else{
                $this->disableSoftDeleteFilter();
            }
            return $this->dm->getRepository($documentName)->findOneBy(['id' => $id]);
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }

    /**
     * @param $documentName
     * @param array $criteria
     * @return object[]
     * @throws Exception
     *
     * Busca documentos archivados según un criterio
     */
    public function findArchivedBy($documentName, array $criteria)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');
        try{
            $this->disableSoftDeleteFilter();
            $criteria['archivedAt'] = new Regex('\.+');
            return $this->dm->getRepository($documentName)->findBy($criteria);
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }

    /**
     * @param $documentName
     * @param array $criteria
     * @param bool $excludeArchived
     * @return object[]
     * @throws Exception
     *
     * Busca documentos archivados o no según un criterio
     */
    public function findBy($documentName, array $criteria, $excludeArchived = false)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');
        try{
            if($excludeArchived){
                $this->enableSoftDeleteFilter();
            }else{
                $this->disableSoftDeleteFilter();
            }
            return $this->dm->getRepository($documentName)->findBy($criteria);
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }

    /**
     * @param $documentName
     * @return object[]
     * @throws Exception
     *
     * Busca todos los documentos archivados
     */
    public function findAllArchived($documentName)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');
        try{
            $this->disableSoftDeleteFilter();
            return $this->dm->getRepository($documentName)->findBy(['archivedAt' => new Regex('\.+')]);
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }

    /**
     * @param $documentName
     * @param bool $excludeArchived
     * @return object[]
     * @throws Exception
     *
     * Busca todos los documentos archivados o no
     */
    public function findAll($documentName, $excludeArchived = false)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');
        try{
            if($excludeArchived){
                $this->enableSoftDeleteFilter();
            }else{
                $this->disableSoftDeleteFilter();
            }
            return $this->dm->getRepository($documentName)->findAll();
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }

    /**
     * @param $documentName
     * @param array $criteria
     * @return object|null
     * @throws Exception
     *
     * Busca un documento archivado según un criterio
     */
    public function findOneArchivedBy($documentName, array $criteria)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');
        try{
            $this->disableSoftDeleteFilter();
            $criteria['archivedAt'] = new Regex('\.+');
            return $this->dm->getRepository($documentName)->findOneBy($criteria);
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }

    /**
     * @param $documentName
     * @param array $criteria
     * @param bool $excludeArchived
     * @return object|null
     * @throws Exception
     *
     * Busca un documento archivado o no según un criterio
     */
    public function findOneBy($documentName, array $criteria, $excludeArchived = false)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('archivable');
        try{
            if($excludeArchived){
                $this->enableSoftDeleteFilter();
            }else{
                $this->disableSoftDeleteFilter();
            }
            return $this->dm->getRepository($documentName)->findOneBy($criteria);
        }catch (Exception $e){
            throw $e;
        }finally{
            $this->restoreFilter($enabled);
        }
    }

    /**
     * @param SoftDeleteableDocument $document
     * @throws Exception
     *
     * Marca un documento listo para su eliminación
     */
    public function remove($document)
    {
        if($document instanceof SoftDeleteableDocument){
            if(!$document->getDeleteOn()){
                $document->setDeleteOn(new DateTime("+1day"));
            }
            $document->onDelete($this);
        }else{
            throw new ApiException(500, 'Document should implements Softdeleteable');
        }

    }

    /**
     * @param ArchivableDocument $document
     * @throws Exception
     *
     * Archiva un documento
     */
    public function archive(ArchivableDocument $document)
    {
        $document->setArchivedAt(new DateTime());
        $document->onArchive($this);
    }

    /**
     * @param ArchivableDocument $document
     * @param null $newUniqueKeyValue
     * @throws Exception
     *
     * Restaura un documento archivado
     */
    public function restore(ArchivableDocument $document, $newUniqueKeyValue = null)
    {
        $document->setArchivedAt(null);
        $document->onRestore($this);

        if($document instanceof Unique){
            if(!$newUniqueKeyValue){
                $newUniqueKeyValue = $document->getUniqueKeyValue();
            }
            $existentDocument = $this->findOneBy(
                $this->dm->getClassMetadata(get_class($document))->getName(),
                [$document->getUniqueKeyName() => new Regex('^' . trim($newUniqueKeyValue) . '$', 'i')],
                true
            );

            if($existentDocument && ($existentDocument->getId() != $document->getId())){
                $document->setUniqueKeyValue('RESTORED - ' . $newUniqueKeyValue . ' - ' . $document->getId() . (new DateTime())->getTimestamp());
            }
        }
    }

    /**
     * Habilita el filtro Soft delete
     */
    private function enableSoftDeleteFilter()
    {
        $fc = $this->dm->getFilterCollection();
        if(!$fc->isEnabled('archivable'))  $fc->enable('archivable');
    }

    /**
     * Deshabilita el filtro Soft delete
     */
    private function disableSoftDeleteFilter()
    {
        $fc = $this->dm->getFilterCollection();
        if($fc->isEnabled('archivable'))  $fc->disable('archivable');
    }

    /**
     * @param $enabled
     * Restaura el filtro Soft delete a su estado original
     */
    private function restoreFilter($enabled)
    {
        $fc = $this->dm->getFilterCollection();
        (!$enabled) ? $fc->disable('archivable') : $fc->enable('archivable');
    }

    /**
     * Tells the ObjectManager to make an instance managed and persistent.
     *
     * The object will be entered into the database as a result of the flush operation.
     *
     * NOTE: The persist operation always considers objects that are not yet known to
     * this ObjectManager as NEW. Do not pass detached objects to the persist operation.
     *
     * @param object $object The instance to make managed and persistent.
     *
     * @return void
     */
    public function persist($object)
    {
        $this->dm->persist($object);
    }

    /**
     * @param array $options
     * @throws MongoDBException
     */
    public function flush($options = [])
    {
        $this->dm->flush($options);
    }

    /**
     * Merges the state of a detached object into the persistence context
     * of this ObjectManager and returns the managed copy of the object.
     * The object passed to merge will not become associated/managed with this ObjectManager.
     *
     * @param object $object
     *
     * @return object
     * @throws LockException
     * @deprecated Merge operation is deprecated and will be removed in Persistence 2.0.
     *             Merging should be part of the business domain of an application rather than
     *             a generic operation of ObjectManager.
     */
    public function merge($object)
    {
        return $this->dm->merge($object);
    }

    /**
     * Clears the ObjectManager. All objects that are currently managed
     * by this ObjectManager become detached.
     *
     * @param string|null $objectName if given, only objects of this type will get detached.
     *
     * @return void
     */
    public function clear($objectName = null)
    {
        $this->dm->clear($objectName);
    }

    /**
     * Detaches an object from the ObjectManager, causing a managed object to
     * become detached. Unflushed changes made to the object if any
     * (including removal of the object), will not be synchronized to the database.
     * Objects which previously referenced the detached object will continue to
     * reference it.
     *
     * @param object $object The object to detach.
     *
     * @return void
     * @deprecated Detach operation is deprecated and will be removed in Persistence 2.0. Please use
     *             {@see ObjectManager::clear()} instead.
     *
     */
    public function detach($object)
    {
        $this->dm->detach($object);
    }

    /**
     * Refreshes the persistent state of an object from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param object $object The object to refresh.
     *
     * @return void
     */
    public function refresh($object)
    {
        $this->dm->refresh($object);
    }

    /**
     * Gets the repository for a class.
     *
     * @param string $className
     *
     * @return ObjectRepository
     */
    public function getRepository($className)
    {
        return $this->dm->getRepository($className);
    }

    /**
     * Returns the ClassMetadata descriptor for a class.
     *
     * The class name must be the fully-qualified class name without a leading backslash
     * (as it is returned by get_class($obj)).
     *
     * @param string $className
     *
     * @return ClassMetadata
     */
    public function getClassMetadata($className)
    {
        return $this->dm->getClassMetadata($className);
    }

    /**
     * Gets the metadata factory used to gather the metadata of classes.
     *
     * @return ClassMetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->dm->getMetadataFactory();
    }

    /**
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * This method is a no-op for other objects.
     *
     * @param object $obj
     *
     * @return void
     */
    public function initializeObject($obj)
    {
        $this->dm->initializeObject($obj);
    }

    /**
     * Checks if the object is part of the current UnitOfWork and therefore managed.
     *
     * @param object $object
     *
     * @return bool
     */
    public function contains($object)
    {
        return $this->dm->contains($object);
    }
}
