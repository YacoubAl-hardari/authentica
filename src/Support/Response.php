<?php

namespace Authentica\LaravelAuthentica\Support;

class Response
{
    public function __construct(
        protected array $data,
        protected int $statusCode
    ) {}

    public function successful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function failed(): bool
    {
        return !$this->successful();
    }

    public function status(): int
    {
        return $this->statusCode;
    }

    public function body(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    public function __toString(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }
}