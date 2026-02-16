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
    private string $pattern = '/(\w+\/\b\p{Ll}\p{L}*)|(\w+:\w+)|(\w+\*\w+)|\w+/u';

    /**
     * @param positive-int $minWordLength
     */
    public function __construct(
        private readonly int $minWordLength
    ) {
    }

    /**
     * Extracts all words that match the given minimum character count
     * and returns them as a list.
     *
     * @return array<int, string>
     */
    public function getWords(string $content): array
    {
        $words = [];

        $wordExtractCallback = function (string $word) use (&$words): string {
            $words[] = $word;
            return $word;
        };

        $this->getWithWordsHandled($content, $wordExtractCallback);

        return $words;
    }

    /**
     * Extracts all words that match the given minimum character count
     * and sends them to the word handler. The updated input string will be returned.
     *
     * @param callable(string):string $wordHandler
     */
    public function getWithWordsHandled(string $content, callable $wordHandler): string
    {
        $pregReplaceCallback = function (array $match) use ($wordHandler): string {
            /** @var array<int, string> $match */
            $word = $match[0];

            if (mb_strlen($word) < $this->minWordLength) {
                return $word;
            }

            return (string) $wordHandler($word);
        };

        return (string) preg_replace_callback(
            $this->pattern,
            $pregReplaceCallback,
            $content
        );
    }
}
