<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Entity\Person;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Type
{
    const PERSON = 'person';
    const EXPERT   = 'expert';

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $type;

    /**
     * Type constructor.
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isPerson(): bool
    {
        return $this->type == self::PERSON;
    }

    /**
     * @return bool
     */
    public function isExpert(): bool
    {
        return $this->type == self::EXPERT;
    }

    public function getType()
    {
        return $this->type;
    }

    public function __toString() : string
    {
        return $this->type;
    }
}