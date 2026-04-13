<?php

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label): self
    {
        $v = trim((string)($this->data[$field] ?? ''));
        if ($v === '') $this->errors[$field] = "$label مطلوب";
        return $this;
    }

    public function email(string $field, string $label): self
    {
        $v = trim((string)($this->data[$field] ?? ''));
        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label غير صحيح";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label): self
    {
        $v = (string)($this->data[$field] ?? '');
        if ($v !== '' && mb_strlen($v) < $min) {
            $this->errors[$field] = "$label يجب أن يكون $min أحرف على الأقل";
        }
        return $this;
    }

    public function matches(string $field, string $other, string $label): self
    {
        if (($this->data[$field] ?? '') !== ($this->data[$other] ?? '')) {
            $this->errors[$field] = "$label غير متطابق";
        }
        return $this;
    }

    public function numeric(string $field, string $label): self
{
    $v = trim((string)($this->data[$field] ?? ''));
    if ($v !== '' && !is_numeric($v)) {
        $this->errors[$field] = "$label يجب أن يكون رقماً";
    }
    return $this;
}

public function min(string $field, float $min, string $label): self
{
    $v = $this->data[$field] ?? '';
    if (is_numeric($v) && (float)$v < $min) {
        $this->errors[$field] = "$label يجب أن يكون أكبر من $min";
    }
    return $this;
}

    public function ok(): bool { return empty($this->errors); }
    public function errors(): array { return $this->errors; }
}