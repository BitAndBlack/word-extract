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

use BitAndBlack\Helpers\XMLHelper;

class WordExtractor implements WordExtractorInterface
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
     * @inheritDoc
     */
    public function getWords(string $content): array
    {
        $words = [];

        $wordExtractCallback = function (string $word) use (&$words): string {
            if ('' !== $word) {
                $words[] = $word;
            }

            return $word;
        };

        $this->getWithWordsHandled($content, $wordExtractCallback);

        return $words;
    }

    /**
     * @inheritDoc
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
