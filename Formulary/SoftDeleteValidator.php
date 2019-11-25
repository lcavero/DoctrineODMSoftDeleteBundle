<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Formulary;

use Exception;
use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;
use MongoDB\BSON\Regex;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SoftDeleteValidator
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
     * @param $uniqueKey
     * @param string $uniqueKeyInUseTranslation
     * @param bool $excludeArchived
     * @throws Exception
     */
    public function validateUniqueKey(FormInterface $form, $uniqueKey, $uniqueKeyInUseTranslation = 'keyName_in_use', $excludeArchived = true)
    {
        $data = $form->getData();
        if($data){
            $document = $form->getRoot()->getData();
            $documentName = $this->am->getDocumentManager()->getClassMetadata(get_class($document))->getName();
            $existingDocument = $this->am->findOneBy(
                $documentName,
                [$uniqueKey => new Regex('^' . trim($document->$uniqueKey) . '$', 'i')],
                $excludeArchived
            );

            if($existingDocument != null && ($existingDocument->getId() != $document->getId())){
                $form->addError(
                    new FormError($this->translator->trans(
                       $uniqueKeyInUseTranslation, ['key' => $uniqueKey, 'value' => $document->$uniqueKey], 'validators')
                    )
                );
            }
        }
    }
}
