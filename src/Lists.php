<?php

namespace PhpPairs\Lists;

use function PhpPairs\Pairs\cons as pairsCons;
use function PhpPairs\Pairs\car;
use function PhpPairs\Pairs\cdr;
use function PhpPairs\Pairs\toString as pairToString;
use function PhpPairs\Pairs\isPair;

/**
 * Check if argument is list
 * @example
 * isList(l()); // true
 * isList(l('a', 5)); // true
 * isList(false); // false
 * isList('hello'); // false
 */
function isList($mix)
{
    if ($mix === null) {
        return true;
    }

    if (isPair($mix)) {
        return isList(cdr($mix));
    }

    return false;
}

function checkList($list)
{
    if (!isList($list)) {
        if (isPair($list)) {
            $value = 'pair: ' . pairToString($list);
        } elseif (is_array($list)) {
            $value = 'array';
        } else {
            $value = (string) $list;
        }
        
        throw new \Exception("Argument must be list, but it was '{$value}'");
    }
}

/**
 * Creates new list with given $elements
 * @param mixed[] $elements elements to add
 * @return callable list
 */
function l(...$elements)
{
    return array_reduce(array_reverse($elements), fn($acc, $item) => cons($item, $acc));
}

/**
 * Add element to list
 * @example
 * cons(5, l(1, 0)); // (5, 1, 0)
 */
function cons($element, $list)
{
    checkList($list);
    return pairsCons($element, $list);
}

/**
 * Get list's head
 * @param callable $list
 * @return mixed
 * @example
 * head(l(10, 15, 20)); // 10
 */
function head($list)
{
    checkList($list);
    return car($list);
}

/**
 * Get list's tail
 * @param callable $list
 * @example
 * tail(l(10, 15, 20)); // (15, 20)
 */
function tail($list)
{
    checkList($list);
    return cdr($list);
}

/**
 * Check if list is empty
 * @example
 * isEmpty(l()); // true
 * isEmpty(l(0)); // false
 * isEmpty(l('a', 5)); // false
 */
function isEmpty($list)
{
    checkList($list);
    return $list === null;
}

/**
 * Compare 2 lists
 * @example
 * isEqual(l(), l()); // true
 * isEqual(l(), l(8, 3)); // false
 * isEqual(l(1, 2, 10), l(1, 2, 10)); // true
 */
function isEqual($list1, $list2)
{
    checkList($list1);
    checkList($list2);
    if (isEmpty($list1) && isEmpty($list2)) {
        return true;
    }

    if (head($list1) !== head($list2)) {
        return false;
    }

    return isEqual(tail($list1), tail($list2));
}

/**
 * Check if list has some element
 * @example
 * const numbers = l(3, 4, 5, 8);
 * has(numbers, 3); // true
 * has(numbers, 8); // true
 * has(numbers, 0); // false
 * has(numbers, 'wow'); // false
 */
function has($list, $element)
{
    checkList($list);
    if (isEmpty($list)) {
        return false;
    }
    if (head($list) === $element) {
        return true;
    }

    return has(tail($list), $element);
}

/**
 * Reverse list $list
 * @param  callable $list list
 * @return callable result
 */
function reverse($list)
{
    $iter = function ($items, $acc) use (&$iter) {
        return isEmpty($items) ? $acc : $iter(tail($items), cons(head($items), $acc));
    };

    return $iter($list, l());
}

/**
 * Filters list $list using callable function $func
 * @param  callable $list list
 * @param  callable $func function
 * @return callable list
 */
function filter($list, callable $func)
{
    checkList($list);
    $iter = function ($func, $items) use (&$iter) {
        if (isEmpty($items)) {
            return null;
        } else {
            $head = head($items);
            $tail = $iter($func, tail($items));
            // filter
            return $func($head) ? cons($head, $tail) : $tail;
        }
    };

    return $iter($func, $list);
}

/**
 * Returns list with uniq values
 * @example
 * $numbers = s(3, 4, 3, 5, 5);
 * toString($numbers) // '(4, 3, 5)'
 */
function s(...$elements)
{
    $reversed = array_reverse($elements);
    return array_reduce($reversed, fn($acc, $item) => (has($acc, $item) ? $acc : conj($acc, $item)), l());
}

/**
 * Conj
 * @example
 * $numbers = l(3, 4, 5, 8);
 * conj($numbers, 5); // (3, 4, 5, 8)
 * conj($numbers, 9); // (9, 3, 4, 5, 8)
 */
function conj($list, $element)
{
    return has($list, $element) ? $list : cons($element, $list);
}

/**
 * Disj
 * @example
 * $numbers = l(5, 4, 5, 8);
 * disj($numbers, 5); // (4, 8)
 */
function disj($list, $element)
{
    return filter($list, fn($e) => $e !== $element);
}


/**
 * Applies callable function $func to list $list
 * @param callable $list list
 * @param callable $func function
 * @return callable list
 */
function map($list, callable $func)
{
    checkList($list);
    $iter = function ($items, $acc) use (&$iter, $func) {
        if (isEmpty($items)) {
            return reverse($acc);
        }
        return $iter(tail($items), cons($func(head($items)), $acc));
    };

    return $iter($list, l());
}

/**
 * Collapses the list $list using callable function $func
 * @param  callable $list list
 * @param  callable $func function
 * @param  mixed   $acc
 * @return mixed
 */
function reduce($list, callable $func, $acc = null)
{
    $iter = function ($items, $acc) use (&$iter, $func) {
        return isEmpty($items) ? $acc : $iter(tail($items), $func(head($items), $acc));
    };

    return $iter($list, $acc);
}

/**
 * Join 2 lists
 * @example
 * $numbers = l(3, 4, 5, 8);
 * $numbers2 = l(3, 2, 9);
 * concat($numbers, $numbers2); // (3, 4, 5, 8, 3, 2, 9)
 * concat(l(), l(1, 10)); (1, 10)
 * concat(l(1, 10), l()); // (1, 10)
 */
function concat($list1, $list2)
{
    checkList($list1);
    checkList($list2);
    if (isEmpty($list1)) {
        return $list2;
    }

    return cons(head($list1), concat(tail($list1), $list2));
}

/**
 * Returns length of list
 * @param  callable $list list
 * @return integer
 */
function length($list)
{
    checkList($list);
    if (isEmpty($list) || !isList($list)) {
        return 0;
    }
    
    return 1 + length(tail($list));
}

/**
 * Get element from list by index
 * @example
 * $numbers = l(3, 4, 5, 8);
 * get(0, $numbers); // 3
 * get(1, $numbers); // 4
 * get(3, $numbers); // 8
 */
function get(int $index, $list)
{
    checkList($list);
    if ($index === 0) {
        return head($list);
    }

    return get($index - 1, tail($list));
}

/**
 * Get random element from list
 * @example
 * $numbers = l(3, 4, 5, 8);
 * random($numbers); // one random item from 3, 4, 5, 8
 */
function random($list)
{
    checkList($list);
    $n = rand(0, length($list) - 1);

    return get($n, $list);
}


/**
 * Converts given list to string
 * @param callalble $list
 * @return string
 */
function toString($list)
{
    if (!isList($list)) {
        if (isPair($list)) {
            return 'pair: ' . pairToString($list);
        }

        return (string) $list;
    }

    if (isEmpty($list)) {
        return '()';
    }

    $rec = function ($p) use (&$rec) {
        $first = head($p);
        $rest = tail($p);
        if (isEmpty($rest)) {
            return toString($first);
        }

        return toString($first) . ', ' . $rec($rest);
    };
    
    return '(' . $rec($list) . ')';
}
