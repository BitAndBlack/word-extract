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

interface WordExtractorInterface
{
    /**
     * Extracts all words that match the given minimum character count
     * and returns them as a list.
     *
     * @return array<int, non-empty-string>
     */
    public function getWords(string $content): array;

    /**
     * Extracts all words that match the given minimum character count
     * and sends them to the word handler. The updated input string will be returned.
     *
     * @param callable(non-empty-string):string $wordHandler
     */
    public function getWithWordsHandled(string $content, callable $wordHandler, bool $ignoreHtml = false): string;
}
