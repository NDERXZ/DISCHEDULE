<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Form & Data Validation Engine
 */
class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data, $rules);
        $validator->validate();
        return $validator;
    }

    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleList) {
            if (is_string($ruleList)) {
                $ruleList = explode('|', $ruleList);
            }

            $value = $this->data[$field] ?? null;

            foreach ($ruleList as $ruleItem) {
                $ruleParts = explode(':', $ruleItem, 2);
                $ruleName = $ruleParts[0];
                $param = $ruleParts[1] ?? null;

                $this->applyRule($field, $value, $ruleName, $param);

                // Stop further rules for this field if required check failed
                if ($ruleName === 'required' && isset($this->errors[$field])) {
                    break;
                }
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule, ?string $param): void
    {
        $label = ucwords(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, I18n::trans('validation.required', ['field' => $label]));
                }
                break;

            case 'nullable':
                // Allows null or empty string, subsequent rules should only apply if not empty
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, I18n::trans('validation.email', ['field' => $label]));
                }
                break;

            case 'min':
                $min = (int)$param;
                if (!empty($value)) {
                    if (is_numeric($value)) {
                        if ((float)$value < $min) {
                            $this->addError($field, I18n::trans('validation.min_numeric', ['field' => $label, 'min' => $min]));
                        }
                    } elseif (mb_strlen((string)$value) < $min) {
                        $this->addError($field, I18n::trans('validation.min_string', ['field' => $label, 'min' => $min]));
                    }
                }
                break;

            case 'max':
                $max = (int)$param;
                if (!empty($value)) {
                    if (is_numeric($value)) {
                        if ((float)$value > $max) {
                            $this->addError($field, I18n::trans('validation.max_numeric', ['field' => $label, 'max' => $max]));
                        }
                    } elseif (mb_strlen((string)$value) > $max) {
                        $this->addError($field, I18n::trans('validation.max_string', ['field' => $label, 'max' => $max]));
                    }
                }
                break;

            case 'password_strength':
                if (!empty($value)) {
                    $valStr = (string)$value;
                    $hasUpper = preg_match('/[A-Z]/', $valStr);
                    $hasLower = preg_match('/[a-z]/', $valStr);
                    $hasDigit = preg_match('/[0-9]/', $valStr);
                    $hasSpecial = preg_match('/[^A-Za-z0-9]/', $valStr);

                    if (strlen($valStr) < 8 || !$hasUpper || !$hasLower || !$hasDigit || !$hasSpecial) {
                        $this->addError($field, I18n::trans('validation.password_strength', ['field' => $label]));
                    }
                }
                break;

            case 'matches':
                $otherField = (string)$param;
                $otherValue = $this->data[$otherField] ?? null;
                if ($value !== $otherValue) {
                    $otherLabel = ucwords(str_replace('_', ' ', $otherField));
                    $this->addError($field, I18n::trans('validation.matches', ['field' => $label, 'other' => $otherLabel]));
                }
                break;

            case 'in':
                if (!empty($value)) {
                    $allowed = explode(',', (string)$param);
                    if (!in_array((string)$value, $allowed, true)) {
                        $this->addError($field, I18n::trans('validation.in', ['field' => $label]));
                    }
                }
                break;

            case 'date':
                if (!empty($value)) {
                    $d = \DateTime::createFromFormat('Y-m-d', (string)$value);
                    if (!$d || $d->format('Y-m-d') !== (string)$value) {
                        $this->addError($field, I18n::trans('validation.date', ['field' => $label]));
                    }
                }
                break;

            case 'datetime':
                if (!empty($value)) {
                    $valid = false;
                    foreach (['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d\TH:i', 'Y-m-d\TH:i:s'] as $format) {
                        $d = \DateTime::createFromFormat($format, (string)$value);
                        if ($d) {
                            $valid = true;
                            break;
                        }
                    }
                    if (!$valid) {
                        $this->addError($field, I18n::trans('validation.datetime', ['field' => $label]));
                    }
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, I18n::trans('validation.url', ['field' => $label]));
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, I18n::trans('validation.integer', ['field' => $label]));
                }
                break;

            case 'unique':
                if (!empty($value) && $param !== null) {
                    $parts = explode(',', $param);
                    $table = $parts[0];
                    $column = $parts[1] ?? $field;
                    $exceptId = $parts[2] ?? null;
                    $exceptColumn = $parts[3] ?? 'id';

                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val";
                    $params = ['val' => $value];

                    if ($exceptId !== null && $exceptId !== '') {
                        $sql .= " AND `{$exceptColumn}` != :except_id";
                        $params['except_id'] = $exceptId;
                    }

                    $count = (int)Database::getInstance()->fetchColumn($sql, $params);
                    if ($count > 0) {
                        $this->addError($field, I18n::trans('validation.unique', ['field' => $label]));
                    }
                }
                break;

            case 'file':
                if (isset($this->data[$field]) && is_array($this->data[$field])) {
                    $fileInfo = $this->data[$field];
                    if (($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                        $parts = explode(',', $param ?? '');
                        $allowedExts = !empty($parts[0]) ? explode(';', strtolower($parts[0])) : [];
                        $maxMb = !empty($parts[1]) ? (float)$parts[1] : 10;

                        $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
                        if (!empty($allowedExts) && !in_array($ext, $allowedExts, true)) {
                            $this->addError($field, I18n::trans('validation.file_type', ['field' => $label, 'types' => implode(', ', $allowedExts)]));
                        }

                        if (($fileInfo['size'] / 1048576) > $maxMb) {
                            $this->addError($field, I18n::trans('validation.file_size', ['field' => $label, 'max' => $maxMb]));
                        }
                    }
                }
                break;
        }
    }

    public function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(?string $field = null): ?string
    {
        if ($field !== null) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }

        return null;
    }
}
