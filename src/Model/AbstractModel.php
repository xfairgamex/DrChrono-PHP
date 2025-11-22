<?php

declare(strict_types=1);

namespace DrChrono\Model;

use JsonSerializable;

/**
 * Base class for all model/DTO classes
 */
abstract class AbstractModel implements JsonSerializable
{
    /**
     * Create model from array
     */
    public static function fromArray(array $data): static
    {
        $model = new static();
        $model->hydrate($data);
        return $model;
    }

    /**
     * Hydrate model from array
     */
    protected function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $property = $this->snakeToCamel($key);
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Convert model to array
     */
    public function toArray(): array
    {
        $data = [];
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED) as $property) {
            $propertyName = $property->getName();
            $value = $this->$propertyName ?? null;

            if ($value !== null) {
                $key = $this->camelToSnake($propertyName);
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * JSON serialize
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert snake_case to camelCase
     */
    protected function snakeToCamel(string $value): string
    {
        return lcfirst(str_replace('_', '', ucwords($value, '_')));
    }

    /**
     * Convert camelCase to snake_case
     */
    protected function camelToSnake(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }

    /**
     * Get value by key
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * Set value by key
     */
    public function __set(string $name, $value): void
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }
}
