<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary\NewProducts;

use Ramsey\Uuid\UuidInterface;

class OldProductDescription
{
    /**
     * @var UuidInterface
     */
    private $serialNumber;

    /**
     * @var string
     */
    private $desc;

    /**
     * @var string
     */
    private $longDesc;

    public function __construct(UuidInterface $serialNumber, string $desc, string $longDesc)
    {
        $this->serialNumber = $serialNumber;
        $this->desc = $desc;
        $this->longDesc = $longDesc;
    }

    public function formatted(): string
    {
        if (empty($this->desc) || empty($this->longDesc)) {
            return "";
        }

        return $this->desc . " *** " . $this->longDesc;
    }

    public function replace(string $charToReplace, string $replaceWith): OldProductDescription
    {
        return new OldProductDescription(
            $this->serialNumber,
            str_replace($charToReplace, $replaceWith, $this->desc),
            str_replace($charToReplace, $replaceWith, $this->longDesc)
        );
    }

    /**
     * @return UuidInterface
     */
    public function serialNumber(): UuidInterface
    {
        return $this->serialNumber;
    }
}
