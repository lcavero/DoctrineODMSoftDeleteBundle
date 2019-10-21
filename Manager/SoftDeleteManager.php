<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\SoftDeleteable;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\UniqueSoftDeleteable;

class SoftDeleteManager
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

    public function getClassMetadata($className)
    {

        return $this->dm->getClassMetadata($className);
    }

    public function find($documentName, $id)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
        try{
            $this->enableFilter();
            return $this->dm->getRepository($documentName)->find($id);
        } catch (\Exception $e){
            throw $e;
        } finally {
            $this->restoreFilter($enabled);
        }
    }

    public function findBy($documentName, array $criteria)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
        try{
            $this->enableFilter();
            return $this->dm->getRepository($documentName)->findBy($criteria);
        } catch (\Exception $e){
            throw $e;
        } finally {
            $this->restoreFilter($enabled);
        }
    }

    public function findAll($documentName)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
        try{
            $this->enableFilter();
            return $this->dm->getRepository($documentName)->findAll();
        } catch (\Exception $e){
            throw $e;
        } finally {
            $this->restoreFilter($enabled);
        }
    }

    public function findOneBy($documentName, array $criteria)
    {
        $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
        try{
            $this->enableFilter();
            return $this->dm->getRepository($documentName)->findOneBy($criteria);
        } catch (\Exception $e){
            throw $e;
        } finally {
            $this->restoreFilter($enabled);
        }
    }

    public function markToBeDeleted(SoftDeleteable $document)
    {
        if(!$document->getDeleteOn()){
            $document->setDeleteOn(new \DateTime("+1day"));
        }
    }

    public function remove(SoftDeleteable $document, $soft = true)
    {
        if($soft){
            $document->setDeletedAt(new \DateTime());
            $this->markToBeDeleted($document);
        }else{
            $this->dm->remove($document);
        }
    }

    public function restore(SoftDeleteable $document)
    {
        $document->setDeletedAt(null);
        $document->setDeleteOn(null);
        if($document instanceof UniqueSoftDeleteable){
            $enabled = $this->dm->getFilterCollection()->isEnabled('soft_delete');
            try{
                $this->enableFilter();
                $existent = $this->dm->getRepository($this->getClassMetadata(get_class($document))->getName())
                    ->findOneBy([$document->getUniqueKeyName() => $document->getUniqueKeyValue()]);
                if($existent && $existent->getId() != $document->getId()){
                    $document->setUniqueKeyValue($document->getUniqueKeyValue() . ' RESTORED-' . date('Y-m-d H:i:s'));
                }
            } catch (\Exception $e){
                throw $e;
            } finally {
                $this->restoreFilter($enabled);
            }
        }
    }

    public function flush($options = [])
    {
        $this->dm->flush($options);
    }

    private function enableFilter()
    {
        $fc = $this->dm->getFilterCollection();
        if(!$fc->isEnabled('soft_delete'))  $fc->enable('soft_delete');
    }

    private function restoreFilter($enabled)
    {
        $fc = $this->dm->getFilterCollection();
        (!$enabled) ? $fc->disable('soft_delete') : $fc->enable('soft_delete');
    }
}
