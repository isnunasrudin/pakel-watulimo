<?php

namespace App\Services;

final class ImageBackgroundPalette
{
    public const DEFAULT = '#f3f4f6';

    /**
     * @var array<string, string>
     */
    public const OPTIONS = [
        '#ffffff' => 'Putih',
        '#f8fafc' => 'Abu sangat terang',
        '#f3f4f6' => 'Abu terang (bawaan)',
        '#e5e7eb' => 'Abu',
        '#374151' => 'Abu gelap',
        '#111827' => 'Gelap',
        '#ecfdf5' => 'Hijau muda',
        '#dcfce7' => 'Hijau',
        '#fffbeb' => 'Krem',
        '#fef3c7' => 'Kuning muda',
        '#eff6ff' => 'Biru muda',
        '#e0f2fe' => 'Langit muda',
        '#f5f3ff' => 'Ungu muda',
        '#fdf2f8' => 'Pink muda',
        '#dc2626' => 'Merah berani',
        '#ea580c' => 'Oranye berani',
        '#d97706' => 'Amber tua',
        '#facc15' => 'Kuning cerah',
        '#16a34a' => 'Hijau berani',
        '#0d9488' => 'Teal berani',
        '#2563eb' => 'Biru berani',
        '#4f46e5' => 'Indigo berani',
        '#7c3aed' => 'Violet berani',
        '#db2777' => 'Pink berani',
        '#e11d48' => 'Rose tua',
        '#000000' => 'Hitam',
    ];

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return self::OPTIONS;
    }

    /**
     * @return array<string, string>
     */
    public static function htmlOptions(): array
    {
        return collect(self::OPTIONS)
            ->mapWithKeys(fn (string $label, string $color): array => [
                $color => '<span style="display:inline-flex;align-items:center;gap:.5rem">'
                    .'<span style="display:inline-block;width:.9rem;height:.9rem;border-radius:9999px;border:1px solid #d1d5db;background-color:'.$color.'"></span>'
                    .e($label)
                    .'</span>',
            ])
            ->all();
    }

    /**
     * Resolve a valid palette color with a safe fallback.
     */
    public static function color(?string $color): string
    {
        return is_string($color) && array_key_exists($color, self::OPTIONS)
            ? $color
            : self::DEFAULT;
    }
}
