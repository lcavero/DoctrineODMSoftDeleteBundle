<?php


namespace LCV\DoctrineODMSoftDeleteBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\Groups({"COMMON.created"})
     */
    protected $created;

    /**
     * @var DateTime
     * @MongoDB\Field(type="date")
     * @Serializer\Exclude()
     */
    protected $updated;

    /**
     * @return string
     */
    public function getId(): string
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
