<?php

namespace App\Services;

class NgWordFilter
{
    public function contains(string $text, string $dictionary = 'contact_message'): bool
    {
        $normalizedText = $this->normalize($text);

        if ($normalizedText === '') {
            return false;
        }

        foreach ($this->words($dictionary) as $word) {
            if ($word !== '' && str_contains($normalizedText, $this->normalize($word))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function words(string $dictionary): array
    {
        $words = config("ng_words.{$dictionary}", []);

        return is_array($words)
            ? array_values(array_filter($words, fn (mixed $word): bool => is_string($word) && trim($word) !== ''))
            : [];
    }

    private function normalize(string $text): string
    {
        $text = mb_convert_kana($text, 'asKVc', 'UTF-8');
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[\s\p{P}\p{S}_ー〜~]+/u', '', $text) ?? $text;

        return trim($text);
    }
}
