[![PHP from Packagist](https://img.shields.io/packagist/php-v/bitandblack/word-extract)](http://www.php.net)
[![Total Downloads](https://poser.pugx.org/bitandblack/word-extract/downloads)](https://packagist.org/packages/bitandblack/word-extract)
[![License](https://poser.pugx.org/bitandblack/word-extract/license)](https://packagist.org/packages/bitandblack/word-extract)

<p align="center">
    <a href="https://www.bitandblack.com" target="_blank">
        <img src="https://www.bitandblack.com/build/images/BitAndBlack-Logo-Full.png" alt="Bit&Black Logo" width="400">
    </a>
</p>

# Bit&Black Word Extract

Extract words with a given minimum length.

## Installation

This library is written in [PHP](https://www.php.net) and made for the use with [Composer](https://packagist.org/packages/bitandblack/word-extract). Be sure to have both of them installed on your system.

Add the library then to your project by running `$ composer require bitandblack/word-extract`.

## Usage

Initialise the [`WordExtractor`](./src/WordExtractor.php) class with the minimum number of letters that the words to be extracted should have:

```php
<?php

use BitAndBlack\WordExtract\WordExtractor;

$wordExtractor = new WordExtractor(12);
```

Extract the words from a given string then:

```php
<?php

$sentence = 'Herzlich willkommen in meinem Rosengarten';

$words = $wordExtractor->getWords($sentence);

/**
 * This will dump
 * 
 * array(2) {
 *     [0]=> string(10) "willkommen"
 *     [1]=> string(11) "Rosengarten"
 * }
 */
var_dump($words);
```

## Help

If you have any questions feel free to contact us under `hello@bitandblack.com`.

Further information about Bit&Black can be found under [www.bitandblack.com](https://www.bitandblack.com).