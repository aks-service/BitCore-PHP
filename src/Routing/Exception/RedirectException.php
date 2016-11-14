<?php
namespace Bit\Routing\Exception;

use RuntimeException;

/**
 * An exception subclass used by the routing layer to indicate
 * that a route has resolved to a redirect.
 *
 * The URL and status code are provided as constructor arguments.
 *
 * ```
 * throw new RedirectException('http://example.com/some/path', 301);
 * ```
 */
class RedirectException extends RuntimeException
{
}
