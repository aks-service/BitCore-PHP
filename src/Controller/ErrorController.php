<?php
namespace Bit\Controller;

use Bit\Routing\Router;

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.
 */
class ErrorController extends Controller
{

    /**
     * Constructor
     *
     * @param \Bit\Network\Request|null $request Request instance.
     * @param \Bit\Network\Response|null $response Response instance.
     */
    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
    }
}
