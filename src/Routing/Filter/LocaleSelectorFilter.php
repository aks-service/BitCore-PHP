<?php
namespace Bit\Routing\Filter;

use Bit\Event\Event;
use Bit\Routing\DispatcherFilter;
use Locale;

/**
 * Sets the runtime default locale for the request based on the
 * Accept-Language header. The default will only be set if it
 * matches the list of passed valid locales.
 */
class LocaleSelectorFilter extends DispatcherFilter
{

    /**
     * List of valid locales for the request
     *
     * @var array
     */
    protected $_locales = [];

    /**
     * Constructor.
     *
     * @param array $config Settings for the filter.
     * @throws \Bit\Core\Exception\Exception When 'when' conditions are not callable.
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        if (!empty($config['locales'])) {
            $this->_locales = $config['locales'];
        }
    }

    /**
     * Inspects the request for the Accept-Language header and sets the
     * Locale for the current runtime if it matches the list of valid locales
     * as passed in the configuration.
     *
     * @param \Bit\Event\Event $event The event instance.
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        $request = $event->data['request'];
        $locale = Locale::acceptFromHttp($request->env->acceptlang);

        $request->addParams(['lang'=>Locale::getPrimaryLanguage(!$locale ? Locale::getDefault() : $locale)]);

        if (!$locale || (!empty($this->_locales) && !in_array($locale, $this->_locales))) {
            return;
        }
        Locale::setDefault($locale);
    }
}