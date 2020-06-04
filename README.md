# php-pairs-data

[![github action status](https://github.com/ashikov/php-pairs-data/workflows/master/badge.svg)](https://github.com/hexlet-components/php-pairs-data/actions)

Functions for working with Lists.

## Examples

```php
<?php

use function PhpPairsData\Lists\l;
use function PhpPairsData\Lists\length;
use function PhpPairsData\Lists\filter;
use function PhpPairsData\Lists\toString;

$list = l(1, 2, 3, 4, 5, 6);
$length = length($list); // $length = 6;
$filter = filter($list, fn($x) => $x % 2 == 0);
$result = toString($filter); // $result = "(2, 4, 6)";
```

[![Hexlet Ltd. logo](https://raw.githubusercontent.com/Hexlet/hexletguides.github.io/master/images/hexlet_logo128.png)](https://ru.hexlet.io/pages/about?utm_source=github&utm_medium=link&utm_campaign=php-pairs-data)

This repository is created and maintained by the team and the community of Hexlet, an educational project. [Read more about Hexlet (in Russian)](https://ru.hexlet.io/pages/about?utm_source=github&utm_medium=link&utm_campaign=php-pairs-data).
