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
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

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

    public function getWithWordsHandled(string $content, callable $wordHandler, bool $ignoreHtml = false): string
    {
        $pregReplaceCallback = function (array $match) use ($wordHandler): string {
            /** @var array<int, string> $match */
            $word = $match[0];

            if (mb_strlen($word) < $this->minWordLength) {
                return $word;
            }

            return (string) $wordHandler($word);
        };

        $contentStriped = strip_tags($content);
        $doesContentContainHtml = $content !== $contentStriped;

        if (false === $doesContentContainHtml || true === $ignoreHtml) {
            return (string) preg_replace_callback(
                $this->pattern,
                $pregReplaceCallback,
                $content
            );
        }

        $tempNodeName = 'temp';

        $domDocument = new DOMDocument('1.0', 'UTF-8');
        XMLHelper::loadHTML($domDocument, '<' . $tempNodeName . '>' . $content . '</' . $tempNodeName . '>');

        $callback = fn (string $content): string => $this->getWithWordsHandled($content, $wordHandler, $ignoreHtml);

        $this->extractDomContent(
            $domDocument,
            $callback
        );

        $tempNodeFirst = $domDocument->getElementsByTagName($tempNodeName)->item(0);
        $hasChildNodes = null !== $tempNodeFirst && $tempNodeFirst->hasChildNodes();
        $childNodes = true === $hasChildNodes ? $tempNodeFirst->childNodes : [];
        $childNodesHtml = [];

        foreach ($childNodes as $childNode) {
            $childNodesHtml[] = $domDocument->saveHTML($childNode);
        }

        return implode('', $childNodesHtml);
    }

    /**
     * @param callable(non-empty-string):string $callback
     */
    private function extractDomContent(DOMDocument $domDocument, callable $callback): void
    {
        $attributesToHandle = [
            'alt',
            'title',
        ];

        $traverse = static function (DOMNode $domNode) use ($attributesToHandle, &$traverse, $callback): void {
            if ($domNode instanceof DOMText) {
                $value = $domNode->nodeValue;

                if (null === $value || '' === $value) {
                    return;
                }

                $value = $callback($value);
                $domNode->nodeValue = $value;
                return;
            }

            if ($domNode instanceof DomElement) {
                foreach ($attributesToHandle as $attributeToHandle) {
                    if (false === $domNode->hasAttribute($attributeToHandle)) {
                        continue;
                    }

                    $value = $domNode->getAttribute($attributeToHandle);

                    if ('' === $value) {
                        continue;
                    }

                    $value = $callback($value);
                    $domNode->setAttribute($attributeToHandle, $value);
                }
            }

            foreach ($domNode->childNodes as $childNode) {
                $traverse($childNode);
            }
        };

        $node = $domDocument->getElementsByTagName('temp')->item(0);

        if (null === $node) {
            return;
        }

        $traverse($node);
    }
}
