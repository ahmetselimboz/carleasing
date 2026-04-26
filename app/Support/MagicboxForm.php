<?php

namespace App\Support;

final class MagicboxForm
{
    /**
     * Form satırlarından veritabanında saklanacak düz diziyi üretir (yalnızca tekil değerler).
     *
     * @param  list<array{key?: string, type?: string, value?: mixed}>|null  $rows
     * @return array<string, bool|int|string>|null
     */
    public static function toStorage(?array $rows): ?array
    {
        if ($rows === null || $rows === []) {
            return null;
        }

        $out = [];

        foreach ($rows as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            if ($key === '') {
                continue;
            }

            $type = $row['type'] ?? 'string';
            $value = $row['value'] ?? '';

            $out[$key] = match ($type) {
                'int' => (int) $value,
                'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN)
                    || $value === '1'
                    || $value === 1
                    || $value === true,
                default => is_scalar($value) || $value === null
                    ? (string) $value
                    : '',
            };
        }

        return $out === [] ? null : $out;
    }

    /**
     * Mevcut magicbox dizisini form satırlarına çevirir (iç içe yapılar tek satırda JSON metin olarak gösterilir).
     *
     * @param  array<string, mixed>|null  $magicbox
     * @return list<array{key: string, type: string, value: string}>
     */
    public static function toRows(?array $magicbox): array
    {
        if ($magicbox === null || $magicbox === []) {
            return [self::emptyRow()];
        }

        $rows = [];

        foreach ($magicbox as $k => $v) {
            $key = (string) $k;
            if ($key === '') {
                continue;
            }

            if (is_bool($v)) {
                $rows[] = [
                    'key' => $key,
                    'type' => 'bool',
                    'value' => $v ? '1' : '0',
                ];
            } elseif (is_int($v)) {
                $rows[] = [
                    'key' => $key,
                    'type' => 'int',
                    'value' => (string) $v,
                ];
            } elseif (is_float($v)) {
                $rows[] = [
                    'key' => $key,
                    'type' => 'string',
                    'value' => (string) $v,
                ];
            } elseif (is_string($v) || $v === null) {
                $rows[] = [
                    'key' => $key,
                    'type' => 'string',
                    'value' => (string) $v,
                ];
            } else {
                $encoded = json_encode($v, JSON_UNESCAPED_UNICODE);
                $rows[] = [
                    'key' => $key,
                    'type' => 'string',
                    'value' => $encoded === false ? '{}' : $encoded,
                ];
            }
        }

        return $rows === [] ? [self::emptyRow()] : $rows;
    }

    /**
     * @return array{key: string, type: string, value: string}
     */
    public static function emptyRow(): array
    {
        return ['key' => '', 'type' => 'string', 'value' => ''];
    }
}
