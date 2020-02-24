<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class TimestampableDocument
 * @MongoDB\MappedSuperclass()
 */
abstract class TimestampableDocument
{
    /**
     * @var string
     * @MongoDB\Id
     */
    public $id;

    /**
     * @var DateTime
     * @MongoDB\Field(type="date")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Groups({"COMMON.created"})
     */
    protected $created;

    /**
     * @var DateTime
     * @MongoDB\Field(type="date")
     * @Gedmo\Timestampable(on="update")
     * @Serializer\Exclude()
     */
    protected $updated;

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }
}
