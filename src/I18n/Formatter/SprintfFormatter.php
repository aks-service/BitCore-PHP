<?php
namespace Bit\I18n\Formatter;

use Aura\Intl\FormatterInterface;
use Bit\I18n\PluralRules;

/**
 * A formatter that will interpolate variables using sprintf and
 * select the correct plural form when required
 */
class SprintfFormatter implements FormatterInterface
{

    /**
     * Returns a string with all passed variables interpolated into the original
     * message. Variables are interpolated using the sprintf format.
     *
     * If an array is passed in `$message`, it will trigger the plural selection
     * routine. Plural forms are selected depending on the locale and the `_count`
     * key passed in `$vars`.
     *
     * @param string $locale The locale in which the message is presented.
     * @param string|array $message The message to be translated
     * @param array $vars The list of values to interpolate in the message
     * @return string The formatted message
     */
    public function format($locale, $message, array $vars)
    {
        if (is_string($message) && isset($vars['_singular'])) {
            $message = [$vars['_singular'], $message];
            unset($vars['_singular']);
        }

        if (is_string($message)) {
            return vsprintf($message, $vars);
        }

        if (isset($vars['_context']) && isset($message['_context'])) {
            $message = $message['_context'][$vars['_context']];
            unset($vars['_context']);
        }

        // Assume first context when no context key was passed
        if (isset($message['_context'])) {
            $message = current($message['_context']);
        }

        if (!is_string($message)) {
            $count = isset($vars['_count']) ? $vars['_count'] : 0;
            unset($vars['_singular']);
            $form = PluralRules::calculate($locale, $count);
            $message = $message[$form];
        }

        return vsprintf($message, $vars);
    }
}
