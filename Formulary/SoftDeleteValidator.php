<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Formulary;

use Exception;
use LCV\DoctrineODMSoftDeleteBundle\Interfaces\UniqueSoftDeleteable;
use LCV\DoctrineODMSoftDeleteBundle\Manager\ArchiveManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
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
     * @param FormEvent $event
     * @param bool $excludeArchived
     * @throws Exception
     */
    public function validateUniqueKey(FormEvent $event, $excludeArchived = true)
    {
        $document = $event->getData();
        if($document instanceof UniqueSoftDeleteable){
            if($document->getUniqueKeyValue()){
                /** @var UniqueSoftDeleteable $existingDocument */
                $existingDocument = $this->am->findOneBy(
                    $this->am->getDocumentManager()->getClassMetadata(get_class($document))->getName(),
                    [$document->getUniqueKeyName() => $document->getUniqueKeyValue()],
                    $excludeArchived
                );
                if($existingDocument != null && ($existingDocument->getId() != $document->getId())){
                    $event->getForm()->addError(
                        new FormError($this->translator->trans(
                            $document->getUniqueKeyInUseTranslation(), [], 'validators')
                        )
                    );
                }
            }
        }
    }
}
