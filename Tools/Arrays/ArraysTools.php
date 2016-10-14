<?php

/*
 * This file is part of the Bayard SharedToolsBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bayard\Bundle\SharedToolsBundle\Tools\Arrays;

/**
 * Tools for handling Arrays
 *
 * @author Massimiliano PASQUESI <massimiliano.pasquesi@bayard-presse.com>
 */
class ArraysTools
{
    /**
     * Insert Element(s) in an Array at choosen position
     *
     * @param  Array  $target         [description]
     * @param  mixed  $byKey          searched key or null
     * @param  mixed  $byOffset       searched offset or null
     * @param  Array  $valuesToInsert [description]
     * @param  bool   $afterKey       true insert after, false insert before
     * @return Array                  Modified Array after insertion
     */
    public static function arrayInsert(Array $target, $byKey, $byOffset, Array $valuesToInsert, $afterKey)
    {
        if (isset($byKey)) {
            if (is_numeric($byKey)) $byKey = (int)floor($byKey);
            $offset = 0;

            foreach ($target as $key => $value) {
                if ($key === $byKey) break;
                $offset++;
            }

            if ($afterKey) $offset++;
        } else {
            $offset = $byOffset;
        }

        $targetLength = count($target);
        $targetA = array_slice($target, 0, $offset, true);
        $targetB = array_slice($target, $offset, $targetLength, true);

        return array_merge($targetA, $valuesToInsert, $targetB);
    }

    /**
     * Inserts any number of scalars or arrays at the point
     * in the haystack immediately after the search key ($needle) was found,
     * or at the end if the needle is not found or not supplied.
     * Modifies $haystack in place.
     *
     * @param array &$haystack the associative array to search. This will be modified by the function
     * @param string $needle the key to search for
     * @param mixed $stuff one or more arrays or scalars to be inserted into $haystack
     * @return int the index at which $needle was found
     */
    public static function arrayInsertAfter(&$haystack, $needle = '', $stuff)
    {
        if (! is_array($haystack) ) return $haystack;

        $new_array = array();
        for ($i = 2; $i < func_num_args(); ++$i){
            $arg = func_get_arg($i);
            if (is_array($arg)) $new_array = array_merge($new_array, $arg);
            else $new_array[] = $arg;
        }

        $i = 0;
        foreach($haystack as $key => $value){
            ++$i;
            if ($key == $needle) break;
        }

        $haystack = array_merge(array_slice($haystack, 0, $i, true), $new_array, array_slice($haystack, $i, null, true));

        return $i;
    }
}