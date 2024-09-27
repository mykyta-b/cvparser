<?php

declare(strict_types=1);

namespace App\DTO\Helper;

use ReflectionClass;

trait DTOTrait
{
    public function fromArray(array $options): self
    {
        if (empty($options)) {
            return $this;
        }

        $props = $this->getProperties();

        if (empty($props)) {
            return $this;
        }

        foreach ($props as $property) {
            $name = $property->getName();
            if (isset($options[$name])) {
                $this->$name = $options[$name];
            }
        }

        return $this;
    }

    public function toArray(): array
    {
        $props = $this->getProperties();

        if (empty($props)) {
            return [];
        }

        $props2Array = [];
        foreach ($props as $property) {
            $name = $property->getName();
            if (isset($this->$name)) {
                $props2Array[$name] = $this->$name;
            }
        }

        return $props2Array;
    }

    /**
     * @return \ReflectionProperty[]
     */
    private function getProperties(): array
    {
        $ref = new ReflectionClass(self::class);
        return array_filter(
            $ref->getProperties(),
            function ($property) {
                return $property->class == self::class;
            }
        );
    }
}
