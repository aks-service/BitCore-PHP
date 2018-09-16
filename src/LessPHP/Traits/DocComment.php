<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.4.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\LessPHP\Traits;

/**
 * Class DocComment
 * Simple splitter for Doc Comments
 * @package Bit\LessPHP\Traits
 */
trait DocComment{

    /**
     * Strips the asterisks from the DocBlock comment.
     *
     * @param string $comment String containing the comment text.
     *
     * @return string
     */
    protected static function __cleanInput(&$comment) {
        $comment = trim(preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]{0,1}(.*)?#u', '$1', $comment));

        // reg ex above is not able to remove */ from a single line docblock
        if (substr($comment, -2) == '*/') {
            $comment = trim(substr($comment, 0, -2));
        }

        // normalize strings
        $comment = str_replace(array("\r\n", "\r"), "\n", $comment);
    }

    /**
     * Splits the DocBlock into a short description, long description and
     * block of tags.
     *
     * @param string $comment Comment to split into the sub-parts.
     *
     * @author RichardJ Special thanks to RichardJ for the regex responsible
     *     for the split.
     *
     * @return string[] containing the short-, long description and an element
     *     containing the tags.
     */
    protected static function splitDocBlock($comment) {
        if (strpos($comment, '@') === 0) {
            $matches = array('', '', $comment);
        } else {
            // clears all extra horizontal whitespace from the line endings
            // to prevent parsing issues
            $comment = preg_replace('/\h*$/Sum', '', $comment);

            /*
             * Big thanks to RichardJ for contributing this Regular Expression
             */
            preg_match('/
            \A (
              [^\n.]+
              (?:
                (?! \. \n | \n{2} ) # disallow the first seperator here
                [\n.] (?! [ \t]* @\pL ) # disallow second seperator
                [^\n.]+
              )*
              \.?
            )
            (?:
              \s* # first seperator (actually newlines but it\'s all whitespace)
              (?! @\pL ) # disallow the rest, to make sure this one doesn\'t match,
              #if it doesn\'t exist
              (
                [^\n]+
                (?: \n+
                  (?! [ \t]* @\pL ) # disallow second seperator (@param)
                  [^\n]+
                )*
              )
            )?
            (\s+ [\s\S]*)? # everything that follows
            /ux', $comment, $matches);
            array_shift($matches);
        }

        while (count($matches) < 3) {
            $matches[] = '';
        }

        list(,$text,$tags) = $matches;
        $_result = [];

        static $i = 0;
        foreach (explode("\n", trim($tags)) as $tag_line) {
            $tag_line = trim($tag_line);
            if ($tag_line === '') {
                continue;
            }
            if (isset($tag_line[0]) && isset($tag_line[1]) && ('@' === $tag_line[0])) {
                $matches = null;
                preg_match("/^@([a-zA-Z]{1,})?(?:[(](.+)?[)])?$/us", trim($tag_line), $matches);
                array_shift($matches);
                if (isset($matches[0]) && count($matches) >= 2){
                    list($tag, $args) = $matches;
                    $tag = strtolower($tag);
                    $_result[$tag][$i++] = json_decode($args);
                }
            }
        }

        return $_result;
    }

    /**
     * parseCommentBlock
     * @param $block
     * @return mixed[]
     */
    public static function parseDocBlock($block){
        self::__cleanInput($block);
        return self::splitDocBlock($block);
    }

}