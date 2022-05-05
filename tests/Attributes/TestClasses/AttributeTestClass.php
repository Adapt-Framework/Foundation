<?php

namespace Tests\Adapt\Foundation\Attributes\TestClasses;

use Adapt\Foundation\Attributes\AttributesTrait;

class AttributeTestClass
{
    use AttributesTrait;

    public function __construct(
        array $attributes,
        array|null $fillable = null,
        array|null $guardedAttributes = null,
        array|null $dateAttributes = [],
        array|null $dateTimeAttributes = [],
        array|null $timeAttributes = []
    ) {
        $this->fillable = $fillable;
        $this->guardedAttributes = $guardedAttributes;
        $this->dateAttributes = $dateAttributes;
        $this->dateTimeAttributes = $dateTimeAttributes;
        $this->timeAttributes = $timeAttributes;

        foreach($attributes as $attribute => $value) {
            $this->__set($attribute, $value);
        }
    }

    public function performResetChangeLog(): void
    {
        $this->resetChangeLog();
    }

    public function getChanges(): array
    {
        return $this->changes;
    }
}
