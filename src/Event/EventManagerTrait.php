<?php
namespace Bit\Event;

/**
 * Provides the event manager interface features for usage in classes that require it.
 *
 * @deprecated 3.0.10 Use Bit\Event\EventDispatcherTrait instead.
 */
trait EventManagerTrait
{

    use EventDispatcherTrait;
}
