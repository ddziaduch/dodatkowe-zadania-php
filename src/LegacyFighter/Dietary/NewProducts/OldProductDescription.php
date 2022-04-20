<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary\NewProducts;

class OldProductDescription
{
    /**
     * @var string
     */
    private $desc;

    /**
     * @var string
     */
    private $longDesc;

    public function __construct(string $desc, string $longDesc)
    {
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
            str_replace($charToReplace, $replaceWith, $this->desc),
            str_replace($charToReplace, $replaceWith, $this->longDesc)
        );
    }
}
