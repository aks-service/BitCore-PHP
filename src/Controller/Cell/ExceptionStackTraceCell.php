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

namespace Bit\Controller\Cell;

use Bit\Controller\Cell;
use Bit\Core\Exception\Exception;
use Bit\Error\Debugger;



/**
 * ExceptionStackTrace
 *
 * @Template(["Element/ExceptionStackTrace"])
 */
class ExceptionStackTraceCell extends Cell
{
    /**
     * StackTrace
     *
     * @param \Throwable $error
     */
    public function index(\Throwable $error)
    {
        $trace = $this['#accordion-trace'];

        list($container,$fileH,$table,$tr,$args) = $trace->find('template > *');

        foreach ($error->getTrace() as $i=> $stack){
            $excerpt = $params = [];
            if (isset($stack['file'], $stack['line']))
                $excerpt = Debugger::excerpt($stack['file'], $stack['line'], 4);


            $isFile = isset($stack['file'], $stack['line']);

            $file = $isFile ? $stack['file'].':'.$stack['line'] : $stack['function'].'(%s) [internal function]';

            if ($stack['function']):
                if (!empty($stack['args'])):
                    foreach ((array)$stack['args'] as $arg):
                        $params[] = Debugger::exportVar($arg, 4);
                    endforeach;
                else:
                    $params[] = 'No arguments';
                endif;
            endif;

            $cont = $container->clone();
            $cont->attr('id',"stack-frame-".$i);
            $cont->append($fileH->clone()->text(sprintf($file,implode(", ", $params))));

            if($isFile) {
                $tt = $table->clone();
                $lineno = isset($stack['line']) ? $stack['line'] - 4 : 0;


                foreach ($excerpt as $l => $line) {
                    $t = $tr->clone();
                    if ($stack['line'] == ($lineno + $l))
                        $t->addClass((!$i) ? "table-danger" : "table-success");
                    $t->find('[data-number]')->text($lineno + $l);
                    $t->find('[data-line]')->html($line);
                    $t->appendTo($tt);
                }
                $cont->append($tt);
            }
            //TODO
            //$cont->append($args->clone()->html(h(implode("\n", $params))));
            //$container->print_r($stack);
            $cont->appendTo($trace);
        }
    }
}
