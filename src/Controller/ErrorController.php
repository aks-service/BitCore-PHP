<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Controller;

use Bit\Core\Configure;
use Bit\Routing\Router;
use Bit\Event\Event;
use Bit\Utility\Inflector;

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.
 */
class ErrorController extends Controller
{
    const APPEND = "#error";
    /**
     * wrap template
     *
     * @var string
     */
    public $template = 'error';
    /**
     * is debug enabled
     * @var bool|mixed
     */
    public $debug = false;

    /**
     * Constructor
     *
     * @param \Bit\Network\Request|null $request Request instance.
     * @param \Bit\Network\Response|null $response Response instance.
     */
    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
        $this->debug = Configure::read('debug');
    }


    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     *
     * @param \Bit\Event\Event $event An Event instance
     * @return \Bit\Network\Response|null
     */
    public function beforeRender(Event $event)
    {
        if($this->debug){
            $this->loadCell("ExceptionStackTrace::index",[$this->viewVars['error']]);
        }
//        var_dump($this->viewVars);
//        die('Render');
        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @param $title
     */
    public function setTitle($title)
    {
        $this->page->find('h1')->text($title);
        parent::setTitle($title);
    }

    /**
     * Call template
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        $template = "Error/".Inflector::underscore($name);
        $this->script($template);
    }
}
