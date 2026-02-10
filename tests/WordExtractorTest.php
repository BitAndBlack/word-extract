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
}
