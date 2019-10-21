<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Formulary;

use LCV\DoctrineODMSoftDeleteBundle\Interfaces\UniqueSoftDeleteable;
use LCV\DoctrineODMSoftDeleteBundle\Manager\SoftDeleteManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class SoftDeleteValidator
{
    private $sdm;
    private $translator;

    public function __construct(SoftDeleteManager $sdm, TranslatorInterface $translator)
    {
        $this->sdm = $sdm;
        $this->translator = $translator;
    }

    public function validateSoftUniqueKey(FormEvent $event, $restrict = true)
    {
        $document = $event->getData();
        if($document instanceof UniqueSoftDeleteable){
            if($document->getUniqueKeyValue()){
                /** @var UniqueSoftDeleteable $existing_entity */
                $existing_entity = $this->sdm->findOneBy(
                    $this->sdm->getClassMetadata(get_class($document))->getName(),
                    [$document->getUniqueKeyName() => $document->getUniqueKeyValue()]
                );
                if($existing_entity != null && ($existing_entity->getId() != $document->getId())){
                    if(!$existing_entity->getDeletedAt() || $restrict){
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
}
