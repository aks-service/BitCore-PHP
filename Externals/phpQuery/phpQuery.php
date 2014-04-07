<?php

/**
 * phpQuery is a server-side, chainable, CSS3 selector driven
 * Document Object Model (DOM) API based on jQuery JavaScript Library.
 * 
 * 
 * added it static
 * 
 * @TODO Check all  port it to a PHP Extension with gumbo and new Jquery Api ;) 
 * @version 0.9.5
 * @link http://code.google.com/p/phpquery/
 * @link http://phpquery-library.blogspot.com/
 * @link http://jquery.com/
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @package phpQuery
 */

// class names for instanceof
// TODO move them as class constants into phpQuery
define('DOMDOCUMENT', 'DOMDocument');
define('DOMELEMENT', 'DOMElement');
define('DOMNODELIST', 'DOMNodeList');
define('DOMNODE', 'DOMNode');

require_once(dirname(__FILE__) . '/phpQuery/DOMDocumentWrapper.php');
require_once(dirname(__FILE__) . '/phpQuery/Callback.php');
require_once(dirname(__FILE__) . '/phpQuery/phpQueryObject.php');
require_once(dirname(__FILE__) . '/phpQuery/compat/mbstring.php');

if (function_exists('domxml_open_mem'))
   throw new Exception("Old PHP4 DOM XML extension detected. phpQuery won't work until this extension is enabled.");


/**
 * Static namespace for phpQuery functions.
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 * @package phpQuery
 */
abstract class phpQuery {

    /**
     * XXX: Workaround for mbstring problems 
     * 
     * @var bool
     */
    public static $mbstringSupport = true;
    public static $debug = false;
    public static $documents = array();
    public static $defaultDocumentID = null;

    /**
     * Applies only to HTML.
     *
     * @var unknown_type
     */
    public static $defaultDoctype = '<!DOCTYPE HTML>';
    public static $defaultCharset = 'UTF-8';

    public static $lastModified = null;
    public static $active = 0;
    public static $dumpCount = 0;

    /**
     * Multi-purpose function.
     * Use pq() as shortcut.
     *
     * @param string|DOMNode|DOMNodeList|array	$arg1	HTML markup, CSS Selector, DOMNode or array of DOMNodes
     * @param string|phpQueryObject|DOMNode	$context	DOM ID from $pq->getDocumentID(), phpQuery object (determines also query root) or DOMNode (determines also query root)
     *
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery|QueryTemplatesPhpQuery|false
     * phpQuery object or false in case of error.
     */
    public static function pq($arg1, $context = null) {
        $domId = (is_null($context)) ? (is_object($arg1) && !($arg1 instanceof phpQueryObject) ? self::getDocumentID($arg1): self::$defaultDocumentID) : self::getDocumentID($context) ;
        
        $phpQuery = new phpQueryObject($domId);
        
        if ($arg1 instanceof phpQueryObject) {
            if ($arg1->getDocumentID() == $domId)
                return $arg1;
            
            $phpQuery->elements = array();
            foreach ($arg1->elements as $node)
                $phpQuery->elements[] = $phpQuery->document->importNode($node, true);
            return $phpQuery;
        } else if ($arg1 instanceof DOMNODE || (is_array($arg1) && isset($arg1[0]) && $arg1[0] instanceof DOMNODE)) {
            if (!($arg1 instanceof DOMNODELIST) && !is_array($arg1))
                $arg1 = array($arg1);
            
            $phpQuery->elements = array();
            foreach ($arg1 as $node) {
                $sameDocument = $node->ownerDocument instanceof DOMDOCUMENT && !$node->ownerDocument->isSameNode($phpQuery->document);
                $phpQuery->elements[] = $sameDocument ? $phpQuery->document->importNode($node, true) : $node;
            }
            return $phpQuery;
        } else if (self::isMarkup($arg1)) {
            return $phpQuery->newInstance(
                            $phpQuery->documentWrapper->import($arg1)
            );
        } else {
            if ($context){
                if($context instanceof phpQueryObject){
                    $phpQuery->elements = $context->elements;
                }else if ($context instanceof DOMNODELIST) {
                    $phpQuery->elements = array();
                    foreach ($context as $node)
                        $phpQuery->elements[] = $node;
                } else if ($context instanceof DOMNODE)
                    $phpQuery->elements = array($context);
                }
            return $phpQuery->find($arg1);
        }
    }

    /**
     * Returns source's document ID.
     *
     * @param $source DOMNode|phpQueryObject
     * @return string
     */
    public static function getDocumentID(&$source) {
        if ($source instanceof DOMDOCUMENT) {
            foreach (phpQuery::$documents as $id => $document) {
                if ($source->isSameNode($document->document))
                    return $id;
            }
        } else if ($source instanceof DOMNODE) {
            foreach (phpQuery::$documents as $id => $document) {
                if ($source->ownerDocument->isSameNode($document->document))
                    return $id;
            }
        } else if ($source instanceof phpQueryObject)
            return $source->getDocumentID();
        else if (is_string($source) && isset(phpQuery::$documents[$source]))
            return $source;
    }
    /**
     * Sets default document to $id. Document has to be loaded prior
     * to using this method.
     * $id can be retrived via getDocumentID() or getDocumentIDRef().
     *
     * @param unknown_type $id
     */
    public static function selectDocument($id) {
        $id = self::getDocumentID($id);
        self::$defaultDocumentID = self::getDocumentID($id);
    }

    /**
     * Returns document with id $id or last used as phpQueryObject.
     * $id can be retrived via getDocumentID() or getDocumentIDRef().
     * Chainable.
     *
     * @see phpQuery::selectDocument()
     * @param unknown_type $id
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function getDocument($id = null) {
        if ($id)
            phpQuery::selectDocument($id);
        else
            $id = phpQuery::$defaultDocumentID;
        return new phpQueryObject($id);
    }

    /**
     * Creates new document from markup.
     * Chainable.
     *
     * @param unknown_type $markup
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocument(&$markup, $contentType = null) {
        if (!$markup)
            $markup = '<html><head><title></title></head><body><div/></body></html>';
        $documentID = phpQuery::createDocumentWrapper($markup, $contentType);
        return new phpQueryObject($documentID);
    }

    /**
     * Creates new document from markup.
     * Chainable.
     *
     * @param unknown_type $markup
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocumentHTML(&$markup, $charset = null) {
        $contentType = $charset ? ";charset=$charset" : '';
        return self::newDocument($markup, "text/html{$contentType}");
    }

    /**
     * Creates new document from markup.
     * Chainable.
     *
     * @param unknown_type $markup
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocumentXML(&$markup, $charset = null) {
        $contentType = $charset ? ";charset=$charset" : '';
        return self::newDocument($markup, "text/xml{$contentType}");
    }

    /**
     * Creates new document from markup.
     * Chainable.
     *
     * @param unknown_type $markup
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocumentXHTML(&$markup, $charset = null) {
        $contentType = $charset ? ";charset=$charset" : '';
        return self::newDocument($markup, "application/xhtml+xml{$contentType}");
    }

    /**
     * Creates new document from file $file.
     * Chainable.
     *
     * @param string $file URLs allowed. See File wrapper page at php.net for more supported sources.
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocumentFile($file, $contentType = null) {
        $documentID = self::createDocumentWrapper(
                        file_get_contents($file), $contentType
        );
        return new phpQueryObject($documentID);
    }

    /**
     * Creates new document from markup.
     * Chainable.
     *
     * @param unknown_type $markup
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocumentFileHTML($file, $charset = null) {
        $contentType = $charset ? ";charset=$charset" : '';
        return self::newDocumentFile($file, "text/html{$contentType}");
    }

    /**
     * Creates new document from markup.
     * Chainable.
     *
     * @param unknown_type $markup
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocumentFileXML($file, $charset = null) {
        $contentType = $charset ? ";charset=$charset" : '';
        return self::newDocumentFile($file, "text/xml{$contentType}");
    }

    /**
     * Creates new document from markup.
     * Chainable.
     *
     * @param unknown_type $markup
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
     */
    public static function newDocumentFileXHTML($file, $charset = null) {
        $contentType = $charset ? ";charset=$charset" : '';
        return self::newDocumentFile($file, "application/xhtml+xml{$contentType}");
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $html
     * @param unknown_type $domId
     * @return unknown New DOM ID
     * @todo support PHP tags in input
     * @todo support passing DOMDocument object from self::loadDocument
     */
    protected static function createDocumentWrapper($html, $contentType = null, $documentID = null) {
       
        $document = null;
        if ($html instanceof DOMDOCUMENT) {
            if (self::getDocumentID($html)) {
                // document already exists in phpQuery::$documents, make a copy
                $document = clone $html;
            } else {
                // new document, add it to phpQuery::$documents
                $wrapper = new DOMDocumentWrapper($html, $contentType, $documentID);
            }
        } else {
            $wrapper = new DOMDocumentWrapper($html, $contentType, $documentID);
        }
        // bind document
        phpQuery::$documents[$wrapper->id] = $wrapper;
        // remember last loaded document
        phpQuery::selectDocument($wrapper->id);
        return $wrapper->id;
    }

    /**
     * Unloades all or specified document from memory.
     *
     * @param mixed $documentID @see phpQuery::getDocumentID() for supported types.
     */
    public static function unloadDocuments($id = null) {
        if (isset($id)) {
            if ($id = self::getDocumentID($id))
                unset(phpQuery::$documents[$id]);
        } else {
            foreach (phpQuery::$documents as $k => $v) {
                unset(phpQuery::$documents[$k]);
            }
        }
    }

    /**
     * Checks if $input is HTML string, which has to start with '<'.
     *
     * @deprecated
     * @param String $input
     * @return Bool
     * @todo still used ?
     */
    public static function isMarkup(&$input) {
        return !is_array($input) && substr(trim($input), 0, 1) == '<';
    }

    public static function debug($text) {
        if (self::$debug)
            print var_dump($text);
    }

    /**
     * Get DOMDocument object related to $source.
     * Returns null if such document doesn't exist.
     *
     * @param $source DOMNode|phpQueryObject|string
     * @return string
     */
    public static function getDOMDocument($source) {
        if ($source instanceof DOMDOCUMENT)
            return $source;
        $source = self::getDocumentID($source);
        return $source ? self::$documents[$id]['document'] : null;
    }


    public static function inArray($value, $array) {
        return in_array($value, $array);
    }

    /**
     *
     * @param $callback Callback
     * @param $params
     * @param $paramStructure
     * @return unknown_type
     */
    public static function callbackRun($callback, $params = array(), $paramStructure = null) {
        if (!$callback)
            return;
        if ($callback instanceof CallbackParameterToReference) {
            // TODO support ParamStructure to select which $param push to reference
            if (isset($params[0]))
                $callback->callback = $params[0];
            return true;
        }
        if ($callback instanceof Callback) {
            $paramStructure = $callback->params;
            $callback = $callback->callback;
        }
        if (!$paramStructure)
            return call_user_func_array($callback, $params);
        $p = 0;
        foreach ($paramStructure as $i => $v) {
            $paramStructure[$i] = $v instanceof CallbackParam ? $params[$p++] : $v;
        }
        return call_user_func_array($callback, $paramStructure);
    }

    /**
     * Merge 2 phpQuery objects.
     * @param array $one
     * @param array $two
     * @protected
     * @todo node lists, phpQueryObject
     */
    public static function merge($one, $two) {
        $elements = $one->elements;
        foreach ($two->elements as $node) {
            $exists = false;
            foreach ($elements as $node2) {
                if ($node2->isSameNode($node))
                    $exists = true;
            }
            if (!$exists)
                $elements[] = $node;
        }
        return $elements;
    }

    /**
     *
     * @param $array
     * @param $callback
     * @param $invert
     * @return unknown_type
     * @link http://docs.jquery.com/Utilities/jQuery.grep
     */
    public static function grep($array, $callback, $invert = false) {
        $result = array();
        foreach ($array as $k => $v) {
            $r = call_user_func_array($callback, array($v, $k));
            if ($r === !(bool) $invert)
                $result[] = $v;
        }
        return $result;
    }

    public static function unique($array) {
        return array_unique($array);
    }
        /**
     *
     * @link http://docs.jquery.com/Utilities/jQuery.map
     */
    public static function map($array, $callback, $param1 = null, $param2 = null, $param3 = null) {
        $result = array();
        $paramStructure = null;
        if (func_num_args() > 2) {
            $paramStructure = func_get_args();
            $paramStructure = array_slice($paramStructure, 2);
        }
        foreach ($array as $v) {
            $vv = phpQuery::callbackRun($callback, array($v), $paramStructure);
            
            if (is_array($vv)) {
                foreach ($vv as $vvv)
                    $result[] = $vvv;
            } else if ($vv !== null) {
                $result[] = $vv;
            }
        }
        return $result;
    }
}

/**
 * Shortcut to phpQuery::pq($arg1, $context)
 * Chainable.
 *
 * @see phpQuery::pq()
 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 * @package phpQuery
 */
function pq($arg1, $context = null) {
     
    if (!is_object($arg1) && phpQuery::isMarkup($arg1)) {
        /**
         * Import HTML:
         * pq('<div/>')
         */
        $end = strpos($arg1, '>');
        if($end+1 == strlen($arg1)){
            $domId = (is_null($context)) ? phpQuery::$defaultDocumentID : phpQuery::getDocumentID($context) ;
          
            $phpQuery = new phpQueryObject($domId);
            $phpQuery->elements = array();
            $arg1 = substr($arg1, 1, $end-1);
            $whitespace = strpos($arg1, ' ') ;
            $squenz = $whitespace === false ? $arg1 : substr($arg1,0,$whitespace);
            $attr = $whitespace === false ? '' : substr($arg1,$whitespace+1);
            
            $token = $phpQuery->document->createElement(str_replace('/','',$squenz));
            if($attr != '')
                preg_replace_callback('/([^\\s].*?)=[\'"](.*?)[\'"]/i', function (&$matches) use(&$token){
                    list(,$name,$value) = $matches;
                    $token->setAttribute($name, $value);
                    return '';
                } , $attr);
            $phpQuery->elements[] = $token;
            return $phpQuery;
        }
    }
    
    $args = func_get_args();
    return call_user_func_array(
            array('phpQuery', 'pq'), $args
    );
}