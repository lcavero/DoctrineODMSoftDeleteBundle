<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Formulary;

use Exception;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\Unique;
use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;
use MongoDB\BSON\Regex;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArchivableValidator
{
    private $am;
    private $translator;

    public function __construct(ArchiveManager $am, TranslatorInterface $translator)
    {
        $this->am = $am;
        $this->translator = $translator;
    }

    /**
     * @param FormInterface $form
     * @param string $uniqueKeyInUseTranslation
     * @param bool $excludeArchived
     * @throws Exception
     */
    public function validateOwnedUniqueKey(FormInterface $form, $uniqueKeyInUseTranslation = "", $excludeArchived = true)
    {
        $data = $form->getData();
        if($data){
            /** @var Unique $document */
            $document = $form->getRoot()->getData();
            $documentName = $this->am->getDocumentManager()->getClassMetadata(get_class($document))->getName();
            $uniqueKey = $document->getUniqueKeyName();
            $uniqueKeyValue = $document->getUniqueKeyValue();
            $uniqueKeyInUseTranslation = $uniqueKeyInUseTranslation ?: $document->getUniqueKeyInUseTranslation();

            $this->validateUniqueKey($form, $documentName, $uniqueKey, $uniqueKeyValue, $uniqueKeyInUseTranslation, $excludeArchived, $document->getId());
        }
    }

    /**
     * @param FormInterface $form
     * @param $documentName
     * @param $uniqueKey
     * @param string $uniqueKeyInUseTranslation
     * @param bool $excludeArchived
     * @throws Exception
     */
    public function validateForeignUniqueKey(FormInterface $form, $documentName, $uniqueKey, $uniqueKeyInUseTranslation = "", $excludeArchived = true)
    {
        $data = $form->getData();
        if($data){
            $this->validateUniqueKey($form, $documentName, $uniqueKey, $data, $uniqueKeyInUseTranslation, $excludeArchived);
        }
    }

    /**
     * @param FormInterface $form
     * @param $documentName
     * @param $uniqueKey
     * @param $uniqueKeyValue
     * @param $uniqueKeyInUseTranslation
     * @param $excludeArchived
     * @param $ownedId
     * @throws Exception
     */
    private function validateUniqueKey(FormInterface $form, $documentName, $uniqueKey, $uniqueKeyValue, $uniqueKeyInUseTranslation, $excludeArchived, $ownedId = null)
    {
        $existingDocument = $this->am->findOneBy(
            $documentName,
            [$uniqueKey => new Regex('^' . trim($uniqueKeyValue) . '$', 'i')],
            $excludeArchived
        );


        if($existingDocument != null && (!$ownedId || ($existingDocument->getId() != $ownedId))){
            $translation = $uniqueKeyInUseTranslation;
            if(!$translation){
                $translation = 'keyName_in_use';
            }
            $form->addError(
                new FormError($this->translator->trans(
                    $translation, ['key' => $uniqueKey, 'value' => $uniqueKeyValue], 'validators')
                )
            );
        }
    }
}
