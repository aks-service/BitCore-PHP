<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 25.04.16
 * Time: 23:39
 */

namespace Bit\PHPQuery;
use \Iterator;

class QueryObjectList extends Iterator
{

    /**
     * @access private
     */
    public function current(){
        $new = new phpQueryObject($this->documentID);
        $new->elements = array();
        $new->elements[] = $this->elementsInterator[ $this->current ];
        return $new;
    }
    /**
     * Double-function method.
     *
     * First: main iterator interface method.
     * Second: Returning next sibling, alias for _next().
     *
     * Proper functionality is choosed automagicaly.
     *
     * @see phpQueryObject::_next()
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public function next($cssSelector = null){
//		if ($cssSelector || $this->valid)
//			return $this->_next($cssSelector);
        $this->valid = isset( $this->elementsInterator[ $this->current+1 ] )
            ? true
            : false;
        if (! $this->valid && $this->elementsInterator) {
            $this->elementsInterator = null;
        } else if ($this->valid) {
            $this->current++;
        } else {
            return $this->_next($cssSelector);
        }
    }

    /**
     * @access private
     */
    public function key(){
        return $this->current;
    }

    /**
     * @access private
     */
    public function valid(){
        return $this->valid;
    }
    /**
     * @access private
     */
    public function rewind(){
//		phpQuery::selectDocument($this->getDocumentID());
        $this->elementsBackup = $this->elements;
        $this->elementsInterator = $this->elements;
        $this->valid = isset( $this->elements[0] )
            ? 1 : 0;
// 		$this->elements = $this->valid
// 			? array($this->elements[0])
// 			: array();
        $this->current = 0;
    }
}