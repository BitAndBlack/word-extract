<?php

/**
 * Bit&Black Word Extract.
 *
 * @author Tobias Köngeter
 * @copyright Copyright © Bit&Black
 * @link https://www.bitandblack.com
 * @license MIT
 */

use BitAndBlack\WordExtract\WordExtractor;

require dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$wordExtractor = new WordExtractor(10);

$sentence = 'Herzlich willkommen in meinem Rosengarten';

$words = $wordExtractor->getWords($sentence);

dump($words);
