<?php

/**
 * Bit&Black Word Extract.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\WordExtract;

class WordExtractor
{
    private string $pattern = '/(\w+\/\w+)|(\w+:\w+)|(\w+\*\w+)|\w+/u';

    /**
     * @param positive-int $minWordLength
     */
    public function __construct(
        private readonly int $minWordLength
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getWords(string $content): array
    {
        $words = [];
        $callback = function (array $match) use (&$words): string {
            $word = $match[0];

            if (false === is_string($word) || mb_strlen($word) < $this->minWordLength) {
                return '';
            }

            $words[] = $word;
            return '';
        };

        preg_replace_callback(
            $this->pattern,
            $callback,
            $content
        );

        return $words;
    }
}
