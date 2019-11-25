<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\SoftDeleteable;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\UniqueSoftDeleteable;
use MongoDB\BSON\Regex;

class ArchiveManager
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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');

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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
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
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
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
     * @param SoftDeleteable $document
     * @throws Exception
     *
     * Marca un documento listo para su eliminación
     */
    public function delete(SoftDeleteable $document)
    {
        $document->onDelete($this);
        if(!$document->getDeleteOn()){
            $document->setDeleteOn(new \DateTime("+1day"));
        }
    }

    /**
     * @param SoftDeleteable $document
     * @throws Exception
     *
     * Archiva un documento
     */
    public function archive(SoftDeleteable $document)
    {
        $document->onArchive($this);
        $document->setArchivedAt(new \DateTime());
    }

    /**
     * @param SoftDeleteable $document
     * @param null $newUniqueKeyValue
     * @throws Exception
     *
     * Restaura un documento archivado
     */
    public function restore(SoftDeleteable $document, $newUniqueKeyValue = null)
    {
        $document->onRestore($this);
        $document->setArchivedAt(null);

        if($document instanceof UniqueSoftDeleteable){
            if(!$newUniqueKeyValue){
                $newUniqueKeyValue = $document->getUniqueKeyValue();
            }
            $existentDocument = $this->findOneBy(
                $this->dm->getClassMetadata(get_class($document))->getName(),
                [$document->getUniqueKeyName() => new Regex('^' . $newUniqueKeyValue . '$', 'i')],
                true
            );

            if($existentDocument && ($existentDocument->getId() != $document->getId())){
                $document->setUniqueKeyValue('RESTORED - ' . $newUniqueKeyValue . ' - ' . $document->getId() . (new \DateTime())->getTimestamp());
            }
        }
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
     * Habilita el filtro Soft delete
     */
    private function enableSoftDeleteFilter()
    {
        $fc = $this->dm->getFilterCollection();
        if(!$fc->isEnabled('soft_delete'))  $fc->enable('soft_delete');
    }

    /**
     * Deshabilita el filtro Soft delete
     */
    private function disableSoftDeleteFilter()
    {
        $fc = $this->dm->getFilterCollection();
        if($fc->isEnabled('soft_delete'))  $fc->disable('soft_delete');
    }

    /**
     * @param $enabled
     * Restaura el filtro Soft delete a su estado original
     */
    private function restoreFilter($enabled)
    {
        $fc = $this->dm->getFilterCollection();
        (!$enabled) ? $fc->disable('soft_delete') : $fc->enable('soft_delete');
    }
}
