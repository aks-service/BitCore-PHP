<?php
namespace Bit\I18n\Formatter;

use Aura\Intl\Exception;
use Aura\Intl\FormatterInterface;
use Bit\I18n\PluralRules;
use MessageFormatter;

/**
 * A formatter that will interpolate variables using the MessageFormatter class
 */
class IcuFormatter implements FormatterInterface
{

    /**
     * Returns a string with all passed variables interpolated into the original
     * message. Variables are interpolated using the MessageFormatter class.
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
        $isString = is_string($message);
        if ($isString && isset($vars['_singular'])) {
            $message = [$vars['_singular'], $message];
            unset($vars['_singular']);
            $isString = false;
        }

        if ($isString) {
            return $this->_formatMessage($locale, $message, $vars);
        }

        if (isset($vars['_context'], $message['_context'])) {
            $message = $message['_context'][$vars['_context']];
            unset($vars['_context']);
        }

        // Assume first context when no context key was passed
        if (isset($message['_context'])) {
            $message = current($message['_context']);
        }

        if (!is_string($message)) {
            $count = isset($vars['_count']) ? $vars['_count'] : 0;
            unset($vars['_count'], $vars['_singular']);
            $form = PluralRules::calculate($locale, $count);
            $message = isset($message[$form]) ? $message[$form] : end($message);
        }

        return $this->_formatMessage($locale, $message, $vars);
    }

    /**
     * Does the actual formatting using the MessageFormatter class
     *
     * @param string $locale The locale in which the message is presented.
     * @param string|array $message The message to be translated
     * @param array $vars The list of values to interpolate in the message
     * @return string The formatted message
     * @throws \Aura\Intl\Exception\CannotInstantiateFormatter if any error occurred
     * while parsing the message
     * @throws \Aura\Intl\Exception\CannotFormat If any error related to the passed
     * variables is found
     */
    protected function _formatMessage($locale, $message, $vars)
    {
        // Using procedural style as it showed twice as fast as
        // its counterpart in PHP 5.5
        $result = MessageFormatter::formatMessage($locale, $message, $vars);

        if ($result === false) {
            // The user might be interested in what went wrong, so replay the
            // previous action using the object oriented style to figure out
            $formatter = new MessageFormatter($locale, $message);
            if (!$formatter) {
                throw new Exception\CannotInstantiateFormatter(intl_get_error_message(), intl_get_error_code());
            }

            $formatter->format($vars);
            throw new Exception\CannotFormat($formatter->getErrorMessage(), $formatter->getErrorCode());
        }

        return $result;
    }
}
