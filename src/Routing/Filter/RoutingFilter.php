<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.5.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Routing\Filter;

use Bit\Event\Event;
use Bit\Routing\DispatcherFilter;
use Bit\Routing\Exception\RedirectException;
use Bit\Routing\Router;

/**
 * A dispatcher filter that applies routing rules to the request.
 *
 * This filter will call Router::parse() when the request has no controller
 * parameter defined.
 */
class RoutingFilter extends DispatcherFilter
{

    /**
     * Priority setting.
     *
     * This filter is normally fired last just before the request
     * is dispatched.
     *
     * @var int
     */
    protected $_priority = 10;

    /**
     * Applies Routing and additionalParameters to the request to be dispatched.
     * If Routes have not been loaded they will be loaded, and config/routes.php will be run.
     *
     * @param \Bit\Event\Event $event containing the request, response and additional params
     * @return \Bit\Network\Response|null A response will be returned when a redirect route is encountered.
     */
    public function beforeDispatch(Event $event)
    {
        $request = $event->data['request'];
        Router::setRequestInfo($request);
        try {
            if (empty($request->params['controller'])) {
                $params = Router::parse($request->url);
                $request->addParams($params);
            }
        } catch (RedirectException $e) {
            $response = $event->data['response'];
            $response->statusCode($e->getCode());
            $response->header('Location', $e->getMessage());
            return $response;
        }
    }
}
