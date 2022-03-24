<?php

declare(strict_types=1);

namespace LegacyFighter\Dietary;

class CountryCode
{
    /**
     * @var string
     */
    private $code;

    /**
     * @throws \Exception
     */
    public function __construct(string $code)
    {
        if ($code === "" || strlen($code) === 1) {
            throw new \Exception("Invalid country code");
        }

        $this->code = $code;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }

    public function isEqual(string $other): bool
    {
        return $this->code === $other;
    }
}
