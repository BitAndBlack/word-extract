<?php

declare(strict_types=1);

/**
 * Bit&Black Word Extract.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

namespace BitAndBlack\WordExtract\Tests;

use BitAndBlack\WordExtract\WordExtractor;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class WordExtractorTest extends TestCase
{
    /**
     * @return Generator<array<int, string|array<int, string>|int>>
     */
    public static function getGetWordsData(): Generator
    {
        yield 'Default extraction' => [
            'Wie nachhaltig die Digitalisierung aber tatsächlich ist, hängt von vielen Kriterien ab. Neben der Verwendung von Ökostrom ist die Verwendung klimaneutraler Webhosting-Lösungen ganz entscheidend.',
            [
                'nachhaltig',
                'Digitalisierung',
                'tatsächlich',
                'Verwendung',
                'Verwendung',
                'klimaneutraler',
                'Webhosting',
                'entscheidend',
            ],
            10,
        ];

        yield 'Handling gender versions' => [
            'Wir möchten Mutmacher/innen, Mutmacher:innen, Mutmacher_innen und Mutmacher*innen sein.',
            [
                'Mutmacher/innen',
                'Mutmacher:innen',
                'Mutmacher_innen',
                'Mutmacher*innen',
            ],
            10,
        ];

        yield 'Extract with other length' => [
            'Wie nachhaltig die Digitalisierung aber tatsächlich ist, hängt von vielen Kriterien ab. Neben der Verwendung von Ökostrom ist die Verwendung klimaneutraler Webhosting-Lösungen ganz entscheidend.',
            [
                'Digitalisierung',
                'klimaneutraler',
                'entscheidend',
            ],
            12,
        ];

        yield 'Uppercase extraction' => [
            'Datenschutzerklärung DATENSCHUTZERKLÄRUNG',
            [
                'Datenschutzerklärung',
                'DATENSCHUTZERKLÄRUNG',
            ],
            12,
        ];

        yield 'Ignores dashes' => [
            'Mutmacher/innen Stuttgart/Hamburg',
            [
                'Mutmacher/innen',
            ],
            10,
        ];
    }

    /**
     * @param array<int, string> $wordsExpected
     * @param positive-int $minWordLength
     */
    #[DataProvider('getGetWordsData')]
    public function testGetWords(string $sentence, array $wordsExpected, int $minWordLength): void
    {
        $wordExtractor = new WordExtractor($minWordLength);

        self::assertSame(
            $wordsExpected,
            $wordExtractor->getWords($sentence)
        );
    }

    /**
     * @return Generator<array<int, string>>
     */
    public static function getGetWithWordsHandledData(): Generator
    {
        yield [
            'Meine neue Datenschutzerklärung',
            'Meine neue [Datenschutzerklärung]',
        ];

        yield [
            'Bodenseefelchen <span class="incrediblylongclassname">sind Fische!</span>. Klicke <a href="/bodenseefelchen.html" title="Alles über Bodenseefelchen">diesen Bodenseefelchenriesenlink</a>',
            '[Bodenseefelchen] <span class="incrediblylongclassname">sind Fische!</span>. Klicke <a href="/bodenseefelchen.html" title="Alles über [Bodenseefelchen]">diesen [Bodenseefelchenriesenlink]</a>',
        ];
    }

    #[DataProvider('getGetWithWordsHandledData')]
    public function testGetWithWordsHandled(string $input, string $wordsHandledExpected): void
    {
        $wordExtractor = new WordExtractor(12);

        $handler = static fn (string $word): string => '[' . $word . ']';
        $wordsHandled = $wordExtractor->getWithWordsHandled($input, $handler);

        self::assertSame(
            $wordsHandledExpected,
            $wordsHandled
        );
    }

    /**
     * @return Generator<array<int, string|bool>>
     */
    public static function getCanIgnoreTagsData(): Generator
    {
        yield [
            '<p class="thisclassnameiswild">Meine neue Datenschutzerklärung – <span>eine Unfassbarkeit</span>!</p>',
            '<p class="thisclassnameiswild">Meine neue [Datenschutzerklärung] – <span>eine [Unfassbarkeit]</span>!</p>',
            false,
        ];

        yield [
            '<p class="thisclassnameiswild">Meine neue Datenschutzerklärung – <span>eine Unfassbarkeit</span>!</p>',
            '<p class="[thisclassnameiswild]">Meine neue [Datenschutzerklärung] – <span>eine [Unfassbarkeit]</span>!</p>',
            true,
        ];
    }

    #[DataProvider('getCanIgnoreTagsData')]
    public function testCanIgnoreTags(string $input, string $wordsHandledExpected, bool $ignoreHtml): void
    {
        $wordExtractor = new WordExtractor(12);

        $handler = static fn (string $word): string => '[' . $word . ']';
        $wordsHandled = $wordExtractor->getWithWordsHandled($input, $handler, $ignoreHtml);

        self::assertSame(
            $wordsHandledExpected,
            $wordsHandled
        );
    }
}
