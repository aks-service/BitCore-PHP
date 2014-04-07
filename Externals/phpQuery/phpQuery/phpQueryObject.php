<?php
/**
 * Class representing phpQuery objects.
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 * @package phpQuery
 * @method phpQueryObject clone() clone()
 * @method phpQueryObject empty() empty()
 * @property Int $length
 */
class phpQueryObject
	implements Iterator, Countable, ArrayAccess {
	public $documentID = null;
	/**
	 * DOMDocument class.
	 *
	 * @var DOMDocument
	 */
	public $document = null;
	public $charset = null;
        
        static $cacheit = true; 
	/**
	 *
	 * @var DOMDocumentWrapper
	 */
	public $documentWrapper = null;
	/**
	 * XPath interface.
	 *
	 * @var DOMXPath
	 */
	public $xpath = null;
	/**
	 * Stack of selected elements.
	 * @TODO refactor to ->nodes
	 * @var array
	 */
	public $elements = array();
	/**
	 * @access private
	 */
	protected $elementsBackup = array();
	/**
	 * @access private
	 */
	protected $previous = null;
	/**
	 * @access private
	 * @TODO deprecate
	 */
	protected $root = array();
	/**
	 * Indicated if doument is just a fragment (no <html> tag).
	 *
	 * Every document is realy a full document, so even documentFragments can
	 * be queried against <html>, but getDocument(id)->htmlOuter() will return
	 * only contents of <body>.
	 *
	 * @var bool
	 */
	public $documentFragment = true;
	/**
	 * Iterator interface helper
	 * @access private
	 */
	protected $elementsInterator = array();
	/**
	 * Iterator interface helper
	 * @access private
	 */
	protected $valid = false;
	/**
	 * Iterator interface helper
	 * @access private
	 */
	protected $current = null;
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function __construct($documentID) {
                $this->documentID = $documentID instanceof self
			? $documentID->getDocumentID()
			: $documentID;

		if (! isset(phpQuery::$documents[$this->documentID] )) {
			throw new Exception("Document with ID '{$id}' isn't loaded. Use phpQuery::newDocument(\$html) or phpQuery::newDocumentFile(\$file) first.");
		}
		$this->documentWrapper =&phpQuery::$documents[$this->documentID];
                
		$this->document =& $this->documentWrapper->document;
		$this->xpath =& $this->documentWrapper->xpath;
		$this->charset =& $this->documentWrapper->charset;
		$this->documentFragment =& $this->documentWrapper->isDocumentFragment;
		$this->root =& $this->documentWrapper->root;
		$this->elements = array($this->root);
	}
	/**
	 *
	 * @access private
	 * @param $attr
	 * @return unknown_type
	 */
	public function __get($attr) {
		switch($attr) {
			// FIXME doesnt work at all ?
			case 'length':
				return $this->size();
                            break;
                        case 'tag':
                            if(isset($this->elements[0]) && !isset($this->elements[1]))
                                return $this->elements[0]->tagName;
			break;
			default:
                            if(isset($this->elements[0]) && !isset($this->elements[1]))
                                return $this->elements[0]->getAttribute($attr);
                            else
				return $this->$attr;
		}
	}
        /**
	 *
	 * @access private
	 * @param $attr
	 * @return unknown_type
	 */
	public function __set($attr,$value) {
		switch($attr) {
			// FIXME doesnt work at all ?
			case 'length':
                        case 'tag':
                            break;
			default:
                            if(isset($this->elements[0]) && !isset($this->elements[1]))
                                $this->elements[0]->setAttribute($attr,$value);
		}
                return $this;
	}
        /**
	 *
	 * @access private
	 * @param $method
	 * @param $args
	 * @return unknown_type
	 */
	public function __call($method, $args) {
                $aliasMethods= array('empty'=>array($this, '_'.$method));
                if('clone' == $method)
                {
                    $new = new phpQueryObject($this->documentID);
                    $new->previous = $this;
                    $new->elements = array();
                    foreach($this->elements as $node) {
                            $new->elements[] = $node->cloneNode(true);
                    }

                    return $new;
                }
                elseif(isset($aliasMethods[$method]))
                {
                    return $aliasMethods[$method]();
                } else
			throw new Exception("Method '{$method}' doesnt exist");
	}
        // INTERFACE IMPLEMENTATIONS

	// ITERATOR INTERFACE
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
   * @access private
	 */
	public function key(){
		return $this->current;
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
	public function valid(){
		return $this->valid;
	}
	// ITERATOR INTERFACE END
	// ARRAYACCESS INTERFACE
	/**
   * @access private
	 */
	public function offsetExists($offset) {
		return $this->find($offset)->size() > 0;
	}
	/**
   * @access private
	 */
	public function offsetGet($offset) {
		return $this->find($offset);
	}
	/**
   * @access private
	 */
	public function offsetSet($offset, $value) {
//		$this->find($offset)->replaceWith($value);
		$this->find($offset)->html($value);
	}
	/**
   * @access private
	 */
	public function offsetUnset($offset) {
		// empty
		throw new Exception("Can't do unset, use array interface only for calling queries and replacing HTML.");
	}
        
	/**
	 * Saves actual object to $var by reference.
	 * Useful when need to break chain.
	 * @param phpQueryObject $var
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function toReference(&$var) {
		return $var = $this;
	}
	public function documentFragment($state = null) {
		if ($state) {
			phpQuery::$documents[$this->getDocumentID()]['documentFragment'] = $state;
			return $this;
		}
		return $this->documentFragment;
	}
	/**
	 * Enter description here...
	 * NON JQUERY METHOD
	 *
	 * Watch out, it doesn't creates new instance, can be reverted with end().
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function toRoot() {
		$this->elements = array($this->root);
		return $this;
	}
	/**
	 * Saves object's DocumentID to $var by reference.
	 * <code>
	 * $myDocumentId;
	 * phpQuery::newDocument('<div/>')
	 *     ->getDocumentIDRef($myDocumentId)
	 *     ->find('div')->...
	 * </code>
	 *
	 * @param unknown_type $domId
	 * @see phpQuery::newDocument
	 * @see phpQuery::newDocumentFile
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function getDocumentIDRef(&$documentID) {
		$documentID = $this->getDocumentID();
		return $this;
	}
	/**
	 * Returns object with stack set to document root.
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function getDocument() {
		return phpQuery::getDocument($this->getDocumentID());
	}
	/**
	 *
	 * @return DOMDocument
	 */
	public function getDOMDocument() {
		return $this->document;
	}
	/**
	 * Get object's Document ID.
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function getDocumentID() {
		return $this->documentID;
	}
	/**
	 * Unloads whole document from memory.
	 * CAUTION! None further operations will be possible on this document.
	 * All objects refering to it will be useless.
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function unloadDocument() {
		phpQuery::unloadDocuments($this->documentID);
	}
	public function isHTML() {
		return $this->documentWrapper->isHTML;
	}
	public function isXHTML() {
		return $this->documentWrapper->isXHTML;
	}
	public function isXML() {
		return $this->documentWrapper->isXML;
	}
        
	/**
	 * @access private
	 */
	protected function isRegexp($pattern) {
		return in_array(
			$pattern[ mb_strlen($pattern)-1 ],
			array('^','*','$')
		);
	}
        
	/**
	 * Return matched DOM nodes.
	 *
	 * @param int $index
	 * @return array|DOMElement Single DOMElement or array of DOMElement.
	 */
	public function get($index = null) {
		$return = isset($index)
			? (isset($this->elements[$index]) ? $this->elements[$index] : null)
			: $this->elements;
		
		return $return;
	}
	/**
	 * Return matched DOM nodes.
	 * jQuery difference.
	 *
	 * @param int $index
	 * @return array|string Returns string if $index != null
	 * @todo implement callbacks
	 * @todo return only arrays ?
	 * @todo maybe other name...
	 */
	public function getString($index = null) {
		if ($index)
			$return = $this->eq($index)->text();
		else {
			$return = array();
			for($i = 0; $i < $this->size(); $i++) {
				$return[] = $this->eq($i)->text();
			}
		}
		return $return;
	}
        
	/**
	 * Returns new instance of actual class.
	 *
	 * @param array $newStack Optional. Will replace old stack with new and move old one to history.c
	 */
	public function newInstance($newStack = null) {
		$class = get_class($this);
		// support inheritance by passing old object to overloaded constructor
		$new = $class != 'phpQuery'
			? new $class($this, $this->documentID)
			: new phpQueryObject($this->documentID);
		$new->previous = $this;
		if (is_null($newStack)) {
			$new->elements = $this->elements;
			if ($this->elementsBackup)
				$this->elements = $this->elementsBackup;
		} else if (is_string($newStack)) {
			$new->elements = phpQuery::pq($newStack, $this->documentID)->stack();
		} else {
			$new->elements = $newStack;
		}
		return $new;
	}
        
	/**
	 * Enter description here...
	 *
	 * In the future, when PHP will support XLS 2.0, then we would do that this way:
	 * contains(tokenize(@class, '\s'), "something")
	 * @param unknown_type $class
	 * @param unknown_type $node
	 * @return boolean
	 * @access private
	 */
	static function matchClasses($class, $node) {
            $classes = $node->getAttribute('class');
            if(!$classes)
                return false;
            
            if (!self::$explodeCache[$class.$classes] )
                return true;
            
            return self::_matchClasses($classes, $class) == "1" ? true : false;
	}
        
        //utilitaries functions off selectSingleNode 
        private static function getFullXpath($node,$xml = false) 
        { 
            $xpath = array();
            while(!($node instanceof DOMDOCUMENT)) {
                    $i = 1;
                    $sibling = $node;
                    while($sibling->previousSibling) {
                            $sibling = $sibling->previousSibling;
                            $isElement = $sibling instanceof DOMELEMENT;
                            if ($isElement && $sibling->tagName == $node->tagName)
                                    $i++;
                    }
                    $xpath[] = $xml
                            ? "*[local-name()='{$node->tagName}'][{$i}]"
                            : "{$node->tagName}[{$i}]";
                    $node = $node->parentNode;
            }
            return  "/".join('/', array_reverse($xpath));
        } 
	/**
	 * @access private
	 */
	protected function runQuery($XQuery, $selector = null, $compare = null) {
		$stack = array();
                #Better than foreach and while loop
                $total = (int)count($this->elements);
                for($n =0; $n < $total;$n++){
                        $stackNode =&$this->elements[$n];
		//foreach($this->elements as $k => $stackNode) {
			$detachAfter = false;
			/*// to work on detached nodes we need temporary place them somewhere
			// thats because context xpath queries sucks ;]
			$testNode = $stackNode;
			while ($testNode) {
				if (! $testNode->parentNode && ! $this->isRoot($testNode)) {
					$this->root->appendChild($testNode);
					$detachAfter = $testNode;
					break;
				}
				$testNode = isset($testNode->parentNode)
					? $testNode->parentNode
					: null;
			}*/
			$query = $XQuery == '//' 
				? '//*' : ".".$XQuery;
			// run query, get elements
			$nodes = $this->xpath->query($query,$stackNode);
                        
			if (! $nodes->length )
                            continue;
                        
			$debug = array();
			foreach($nodes as $node) {
				$matched = ($compare) ?  false : true;
				if (!$matched && $compare && $compare($selector, $node))
                                    $matched = true;
				if ( $matched) {
					if (phpQuery::$debug)
						$debug[] = $this->whois( $node );
					$stack[] = $node;
				}
			}
			/*if ($detachAfter)
				$this->root->removeChild($detachAfter);*/
		}
		$this->elements = $stack;
	}
        
        protected function containingClass($className) {
            return "contains(concat(' ',normalize-space(@class),' '),' ". $className. " ')";
        }
        
        protected static $selectorCache = [];
        
        protected function parseSelector(&$selector){
            if(!isset(self::$selectorCache[$selector])){
                if(self::$cacheit == true)
                    self::$selectorCache[$selector] = SCache::getCache(sha1($selector));
                
                if(is_null(self::$selectorCache[$selector])){
                    self::$selectorCache[$selector] = self::parse($selector);
                
                    if(self::$cacheit == true)
                        SCache::setCache(sha1($selector),self::$selectorCache[$selector]);
                }
            }
            return self::$selectorCache[$selector];
        } 
        
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function find($selectors, $context = null, $noHistory = false) {
		if (!$noHistory)
			// backup last stack /for end()/
			$this->elementsBackup = $this->elements;
		// allow to define context
		// TODO combine code below with phpQuery::pq() context guessing code
		//   as generic function
		if ($context) {
			if (! is_array($context) && $context instanceof DOMELEMENT)
				$this->elements = array($context);
			else if (is_array($context)) {
				$this->elements = array();
				foreach ($context as $c)
					if ($c instanceof DOMELEMENT)
						$this->elements[] = $c;
			} else if ( $context instanceof self )
				$this->elements = $context->elements;
		}
		$queries = $this->parseSelector($selectors);
		$XQuery = '';
		// remember stack state because of multi-queries
		$oldStack = $this->elements;
		// here we will be keeping found elements
		$stack = array();
                static $mbstring;
                if($mbstring)
                    $mbstring = extension_loaded('mbstring');
		foreach($queries as $selector) {
			$this->elements = $oldStack;
			$delimiterBefore = false;
			foreach($selector as $s) {
				// TAG
				$isTag = $mbstring && phpQuery::$mbstringSupport
					? mb_ereg_match('^[\w|\||-]+$', $s) || $s == '*'
					: preg_match('@^[\w|\||-]+$@', $s) || $s == '*';
                                
				if ($isTag) {
					if ($this->isXML()) {
						// namespace support
						if (strpos($s, '|') !== false) {
							$ns = $tag = null;
							list($ns, $tag) = explode('|', $s);
							$XQuery .= "$ns:$tag";
						} else if ($s == '*') {
							$XQuery .= "*";
						} else {
							$XQuery .= "*[local-name()='$s']";
						}
					} else {
						$XQuery .= $s;
					}
				// ID
				} else if ($s[0] == '#') {
					if ($delimiterBefore)
						$XQuery .= '*';
					$XQuery .= "[@id='".substr($s, 1)."']";
				// ATTRIBUTES
				} else if ($s[0] == '[') {
					if ($delimiterBefore)
						$XQuery .= '*';
					// strip side brackets
					$attr = trim($s, '][');
					$execute = false;
					// attr with specifed value
					if (mb_strpos($s, '=')) {
						$value = null;
						list($attr, $value) = explode('=', $attr);
						$value = trim($value, "'\"");
						if ($this->isRegexp($attr)) {
							// cut regexp character
							$attr = substr($attr, 0, -1);
							$execute = true;
							$XQuery .= "[@{$attr}]";
						} else {
							$XQuery .= "[@{$attr}='{$value}']";
						}
					// attr without specified value
					} else {
						$XQuery .= "[@{$attr}]";
					}
					if ($execute) {
						$this->runQuery($XQuery, $s,  array($this,'is'));
						$XQuery = '';
						if (! $this->length())
							break;
					}
				// CLASSES
				} else if ($s[0] == '.') {
					if ($delimiterBefore)
						$XQuery .= '*';
                                        $XQuery .= '[string-length(@class)>'. (strlen($s)-2).' and php:functionString("phpQueryObject::_matchClasses", @class, "'.$s.'") = "1"]';
                                        
					$this->runQuery($XQuery);
					$XQuery = '';
					if (! $this->length() )
						break;
				// ~ General Sibling Selector
				} else if ($s[0] == '~') {
					$this->runQuery($XQuery);
					$XQuery = '';
					$this->elements = $this
						->siblings(
							substr($s, 1)
						)->elements;
					if (! $this->length() )
						break;
				} else if ($s[0] == '+') {
					// TODO /following-sibling::
					$this->runQuery($XQuery);
					$XQuery = '';
					$subSelector = substr($s, 1);
					$subElements = $this->elements;
					$this->elements = array();
					foreach($subElements as $node) {
						// search first DOMElement sibling
						$test = $node->nextSibling;
						while($test && ! ($test instanceof DOMELEMENT))
							$test = $test->nextSibling;
						if ($test && $this->is($subSelector, $test))
							$this->elements[] = $test;
					}
					if (! $this->length() )
						break;
				// PSEUDO CLASSES
				} else if ($s[0] == ':') {
					// TODO optimization for :first :last
					if ($XQuery) {
						$this->runQuery($XQuery);
						$XQuery = '';
					}
					if (! $this->length())
						break;
					$this->pseudoClasses($s);
					if (! $this->length())
						break;
				// DIRECT DESCENDANDS
				} else if ($s == '>') {
					$XQuery .= '/';
					$delimiterBefore = 2;
				// ALL DESCENDANDS
				} else if ($s == ' ') {
					$XQuery .= '//';
					$delimiterBefore = 2;
				// ERRORS
				} else {
					phpQuery::debug("Unrecognized token '$s'");
				}
				$delimiterBefore = $delimiterBefore === 2;
			}
			// run query if any
			if ($XQuery && $XQuery != '//') {
                                
				$this->runQuery($XQuery);
				$XQuery = '';
			}
                        foreach($this->elements as $node)
                            //if (!$this->elementsContainsNode($node, $stack)) Renew Logik hmm
                                $stack[] = $node;
		}
                $new = new phpQueryObject($this->documentID);
                $new->previous = $this;
                $new->elements = $stack;
                if ($this->elementsBackup)
                        $this->elements = $this->elementsBackup;
		return $new;
	}
        
	/**
	 * @todo create API for classes with pseudoselectors
	 * @access private
	 */
	protected function pseudoClasses($class) {
		// TODO clean args parsing ?
		$class = ltrim($class, ':');
		$haveArgs = mb_strpos($class, '(');
		if ($haveArgs !== false) {
			$args = substr($class, $haveArgs+1, -1);
			$class = substr($class, 0, $haveArgs);
		}
		switch($class) {
			case 'even':
			case 'odd':
				$stack = array();
				foreach($this->elements as $i => $node) {
					if ($class == 'even' && ($i%2) == 0)
						$stack[] = $node;
					else if ( $class == 'odd' && $i % 2 )
						$stack[] = $node;
				}
				$this->elements = $stack;
				break;
			case 'eq':
				$k = intval($args);
				$this->elements = isset( $this->elements[$k] )
					? array( $this->elements[$k] )
					: array();
				break;
			case 'gt':
				$this->elements = array_slice($this->elements, $args+1);
				break;
			case 'lt':
				$this->elements = array_slice($this->elements, 0, $args+1);
				break;
			case 'first':
				if (isset($this->elements[0]))
					$this->elements = array($this->elements[0]);
				break;
			case 'last':
				if ($this->elements)
					$this->elements = array($this->elements[count($this->elements)-1]);
				break;
			/*case 'parent':
				$stack = array();
				foreach($this->elements as $node) {
					if ( $node->childNodes->length )
						$stack[] = $node;
				}
				$this->elements = $stack;
				break;*/
			case 'contains':
				$text = trim($args, "\"'");
				$stack = array();
				foreach($this->elements as $node) {
					if (mb_stripos($node->textContent, $text) === false)
						continue;
					$stack[] = $node;
				}
				$this->elements = $stack;
				break;
			case 'not':
				$selector = self::unQuote($args);
				$this->elements = $this->not($selector)->stack();
				break;
			case 'slice':
				// TODO jQuery difference ?
				$args = explode(',',
					str_replace(', ', ',', trim($args, "\"'"))
				);
				$start = $args[0];
				$end = isset($args[1])
					? $args[1]
					: null;
				if ($end > 0)
					$end = $end-$start;
				$this->elements = array_slice($this->elements, $start, $end);
				break;
			case 'has':
				$selector = trim($args, "\"'");
				$stack = array();
				foreach($this->stack("1") as $el) {
					if ($this->find($selector, $el, true)->length)
						$stack[] = $el;
				}
				$this->elements = $stack;
				break;
			case 'submit':
			case 'reset':
				$this->elements = phpQuery::merge(
					$this->map(array($this, 'is'),
						"input[type=$class]", new CallbackParam()
					),
					$this->map(array($this, 'is'),
						"button[type=$class]", new CallbackParam()
					)
				);
			break;
			case 'input':
				$this->elements = $this->map(
					array($this, 'is'),
					'input', new CallbackParam()
				)->elements;
			break;
			case 'password':
			case 'checkbox':
			case 'radio':
			case 'hidden':
			case 'image':
			case 'file':
				$this->elements = $this->map(
					array($this, 'is'),
					"input[type=$class]", new CallbackParam()
				)->elements;
			break;
			case 'parent':
				$this->elements = $this->map(
					create_function('$node', '
						return $node instanceof DOMELEMENT && $node->childNodes->length
							? $node : null;')
				)->elements;
			break;
			case 'empty':
				$this->elements = $this->map(
					create_function('$node', '
						return $node instanceof DOMELEMENT && $node->childNodes->length
							? null : $node;')
				)->elements;
			break;
			case 'disabled':
			case 'selected':
			case 'checked':
				$this->elements = $this->map(
					array($this, 'is'),
					"[$class]", new CallbackParam()
				)->elements;
			break;
			case 'enabled':
				$this->elements = $this->map(
					create_function('$node', '
						return pq($node)->not(":disabled") ? $node : null;')
				)->elements;
			break;
			case 'header':
				$this->elements = $this->map(
					create_function('$node',
						'$isHeader = isset($node->tagName) && in_array($node->tagName, array(
							"h1", "h2", "h3", "h4", "h5", "h6", "h7"
						));
						return $isHeader
							? $node
							: null;')
				)->elements;
//				)->elements;
			break;
			case 'only-child':
				$this->elements = $this->map(
					create_function('$node',
						'return pq($node)->siblings()->size() == 0 ? $node : null;')
				)->elements;
			break;
			case 'first-child':
				$this->elements = $this->map(
					create_function('$node', 'return pq($node)->prevAll()->size() == 0 ? $node : null;')
				)->elements;
			break;
			case 'last-child':
				$this->elements = $this->map(
					create_function('$node', 'return pq($node)->nextAll()->size() == 0 ? $node : null;')
				)->elements;
			break;
			case 'nth-child':
				$param = trim($args, "\"'");
				if (! $param)
					break;
					// nth-child(n+b) to nth-child(1n+b)
				if ($param{0} == 'n')
					$param = '1'.$param;
				// :nth-child(index/even/odd/equation)
				if ($param == 'even' || $param == 'odd')
					$mapped = $this->map(
						create_function('$node, $param',
							'$index = pq($node)->prevAll()->size()+1;
							if ($param == "even" && ($index%2) == 0)
								return $node;
							else if ($param == "odd" && $index%2 == 1)
								return $node;
							else
								return null;'),
						new CallbackParam(), $param
					);
				else if (mb_strlen($param) > 1 && $param{1} == 'n')
					// an+b
					$mapped = $this->map(
						create_function('$node, $param',
							'$prevs = pq($node)->prevAll()->size();
							$index = 1+$prevs;
							$b = mb_strlen($param) > 3
								? $param{3}
								: 0;
							$a = $param{0};
							if ($b && $param{2} == "-")
								$b = -$b;
							if ($a > 0) {
								return ($index-$b)%$a == 0
									? $node
									: null;
								phpQuery::debug($a."*".floor($index/$a)."+$b-1 == ".($a*floor($index/$a)+$b-1)." ?= $prevs");
								return $a*floor($index/$a)+$b-1 == $prevs
										? $node
										: null;
							} else if ($a == 0)
								return $index == $b
										? $node
										: null;
							else
								// negative value
								return $index <= $b
										? $node
										: null;
//							if (! $b)
//								return $index%$a == 0
//									? $node
//									: null;
//							else
//								return ($index-$b)%$a == 0
//									? $node
//									: null;
							'),
						new CallbackParam(), $param
					);
				else
					// index
					$mapped = $this->map(
						create_function('$node, $index',
							'$prevs = pq($node)->prevAll()->size();
							if ($prevs && $prevs == $index-1)
								return $node;
							else if (! $prevs && $index == 1)
								return $node;
							else
								return null;'),
						new CallbackParam(), $param
					);
				$this->elements = $mapped->elements;
			break;
			default:
				//$this->debug("Unknown pseudoclass '{$class}', skipping...");
		}
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function is($selector, $nodes = null) {
		if (! $selector)
			return false;
		$oldStack = $this->elements;
		$returnArray = false;
		if ($nodes && is_array($nodes)) {
			$this->elements = $nodes;
		} else if ($nodes)
			$this->elements = array($nodes);
		$this->filter($selector, true);
		$stack = $this->elements;
		$this->elements = $oldStack;
		if ($nodes)
			return $stack ? $stack : null;
		return (bool)count($stack);
	}
        
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @link http://docs.jquery.com/Traversing/filter
	 */
	public function filter($selectors, $_skipHistory = false) {
		if (! $_skipHistory)
			$this->elementsBackup = $this->elements;
		$notSimpleSelector = array(' ', '>', '~', '+', '/');
		if (!is_array($selectors))
			$selectors = $this->parseSelector($selectors);
                
		$finalStack = array();
                
                
                $helper = ['[' => 0x001,
                           '#' => 0x002,
                           '.' => 0x004,
                           ''  => 0x008];
                
		foreach($selectors as $selector){
			$stack = array();
			if (!$selector)
				break;
			// avoid first space or /
			if (in_array($selector[0], $notSimpleSelector))
				$selector = array_slice($selector, 1);
			// PER NODE selector chunks
			foreach($this->elements as $node){
				$break = false;
				foreach($selector as $s) {
                                        $call = isset($helper[$s[0]]) ? $helper[$s[0]] : 0;
					
                                        // DOMElement only
                                        // ID
                                        if ( $call & 0x0002) {
                                                if ( $node->getAttribute('id') != substr($s, 1) )
                                                        $break = true;
                                        // CLASSES
                                        } else if ( $call & 0x004) {
                                                if (! $this->matchClasses( $s, $node ) )
                                                        $break = true;
                                        // ATTRS
                                        } else if ( $call & 0x0001) {
                                                // strip side brackets
                                                $attr = trim($s, '[]');
                                                if (mb_strpos($attr, '=')) {
                                                        list($attr, $val) = explode('=', $attr);
                                                        $val = self::unQuote($val);
                                                        if ($attr == 'nodeType') {
                                                                if ($val != $node->nodeType)
                                                                        $break = true;
                                                        } else if ($this->isRegexp($attr)) {
                                                                $val = extension_loaded('mbstring') && phpQuery::$mbstringSupport
                                                                        ? quotemeta(trim($val, '"\''))
                                                                        : preg_quote(trim($val, '"\''), '@');
                                                                // switch last character
                                                                switch( substr($attr, -1)) {
                                                                        // quotemeta used insted of preg_quote
                                                                        // http://code.google.com/p/phpquery/issues/detail?id=76
                                                                        case '^':
                                                                                $pattern = '^'.$val;
                                                                                break;
                                                                        case '*':
                                                                                $pattern = '.*'.$val.'.*';
                                                                                break;
                                                                        case '$':
                                                                                $pattern = '.*'.$val.'$';
                                                                                break;
                                                                }
                                                                // cut last character
                                                                $attr = substr($attr, 0, -1);
                                                                $isMatch = extension_loaded('mbstring') && phpQuery::$mbstringSupport
                                                                        ? mb_ereg_match($pattern, $node->getAttribute($attr))
                                                                        : preg_match("@{$pattern}@", $node->getAttribute($attr));
                                                                if (! $isMatch)
                                                                        $break = true;
                                                        } else if ($node->getAttribute($attr) != $val)
                                                                $break = true;
                                                } else if (! $node->hasAttribute($attr))
                                                        $break = true;

                                        } else if (trim($s)) {
                                                if ($s != '*') {
                                                        // TODO namespaces
                                                        if (isset($node->tagName)) {
                                                                if ($node->tagName != $s)
                                                                        $break = true;
                                                        } else if ($s == 'html' && ! $this->isRoot($node))
                                                                $break = true;
                                                }
                                        // AVOID NON-SIMPLE SELECTORS
                                        } else if (in_array($s, $notSimpleSelector)) {
                                                $break = true;
                                        }
					
					if ($break)
                                            break;
				}
				// if element passed all chunks of selector - add it to new stack
				if (! $break )
					$stack[] = $node;
			}
			$this->elements = $stack;
			// PER ALL NODES selector chunks
			foreach($selector as $s)
				// PSEUDO CLASSES
				if ($s[0] == ':')
					$this->pseudoClasses($s);
			foreach($this->elements as $node)
				// XXX it should be merged without duplicates
				// but jQuery doesnt do that
				$finalStack[] = $node;
		}
		$this->elements = $finalStack;
		if ($_skipHistory) {
			return $this;
		} else {
			return $this->newInstance();
		}
	}
        
	/**
	 *
	 * @param $value
	 * @return unknown_type
	 * @TODO implement in all methods using passed parameters
	 */
	protected static function unQuote($value) {
		return $value[0] == '\'' || $value[0] == '"'
			? substr($value, 1, -1)
			: $value;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param String|phpQuery
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function wrapAllOld($wrapper) {
		$wrapper = pq($wrapper)->_clone();
		if (! $wrapper->length() || ! $this->length() )
			return $this;
		$wrapper->insertBefore($this->elements[0]);
		$deepest = $wrapper->elements[0];
		while($deepest->firstChild && $deepest->firstChild instanceof DOMELEMENT)
			$deepest = $deepest->firstChild;
		pq($deepest)->append($this);
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * TODO testme...
	 * @param String|phpQuery
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function wrapAll($wrapper) {
		if (! $this->length())
			return $this;
		return phpQuery::pq($wrapper, $this->getDocumentID())
			->clone()
			->insertBefore($this->get(0))
			->map(array($this, '___wrapAllCallback'))
			->append($this);
	}
  /**
   *
	 * @param $node
	 * @return unknown_type
	 * @access private
   */
	public function ___wrapAllCallback($node) {
		$deepest = $node;
		while($deepest->firstChild && $deepest->firstChild instanceof DOMELEMENT)
			$deepest = $deepest->firstChild;
		return $deepest;
	}
	/**
	 * Enter description here...
	 * NON JQUERY METHOD
	 *
	 * @param String|phpQuery
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function wrapAllPHP($codeBefore, $codeAfter) {
		return $this
			->slice(0, 1)
				->beforePHP($codeBefore)
			->end()
			->slice(-1)
				->afterPHP($codeAfter)
			->end();
	}
	/**
	 * Enter description here...
	 *
	 * @param String|phpQuery
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function wrap($wrapper) {
		foreach($this->stack() as $node)
			phpQuery::pq($node, $this->getDocumentID())->wrapAll($wrapper);
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @param String|phpQuery
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function wrapInner($wrapper) {
		foreach($this->stack() as $node)
			phpQuery::pq($node, $this->getDocumentID())->contents()->wrapAll($wrapper);
		return $this;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @testme Support for text nodes
	 */
	public function contents() {
		$stack = array();
		foreach($this->stack("1") as $el) {
                    foreach($el->childNodes as $node) {
                            $stack[] = $node;
                    }
		}
		return $this->newInstance($stack);
	}
	/**
	 * Enter description here...
	 *
	 * jQuery difference.
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function contentsUnwrap() {
		foreach($this->stack("1") as $node) {
			if (! $node->parentNode )
				continue;
			$childNodes = array();
			// any modification in DOM tree breaks childNodes iteration, so cache them first
			foreach($node->childNodes as $chNode )
				$childNodes[] = $chNode;
			foreach($childNodes as $chNode )
//				$node->parentNode->appendChild($chNode);
				$node->parentNode->insertBefore($chNode, $node);
			$node->parentNode->removeChild($node);
		}
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * jQuery difference.
	 */
	public function switchWith($markup) {
		$markup = pq($markup, $this->getDocumentID());
		$content = null;
		foreach($this->stack("1") as $node) {
			pq($node)
				->contents()->toReference($content)->end()
				->replaceWith($markup->clone()->append($content));
		}
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function eq($num) {
		$oldStack = $this->elements;
		$this->elementsBackup = $this->elements;
		$this->elements = array();
		if ( isset($oldStack[$num]) )
			$this->elements[] = $oldStack[$num];
		return $this->newInstance();
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function size() {
		return count($this->elements);
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @deprecated Use length as attribute
	 */
	public function length() {
		return $this->size();
	}
	public function count() {
		return $this->size();
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @todo $level
	 */
	public function end($level = 1) {
		return $this->previous
			? $this->previous
			: $this;
	}
	/**
	 * Enter description here...
	 * Normal use ->clone() .
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @access private
	 */
	public function _clone() {
                $new = new phpQueryObject($this->documentID);
                $new->previous = $this;
                $new->elements = array();
		foreach($this->elements as $node) {
			$new->elements[] = $node->cloneNode(true);
		}
                
		return $new;
	}
	/**
	 * Enter description here...
	 *
	 * @param String|phpQuery $content
	 * @link http://docs.jquery.com/Manipulation/replaceWith#content
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function replaceWith($content) {
		return $this->after($content)->remove();
	}
	/**
	 * Enter description here...
	 *
	 * @param String $selector
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @todo this works ?
	 */
	public function replaceAll($selector) {
		foreach(phpQuery::pq($selector, $this->getDocumentID()) as $node)
			phpQuery::pq($node, $this->getDocumentID())
				->after($this->_clone())
				->remove();
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function remove($selector = null) {
		$loop = $selector
			? $this->filter($selector)->elements
			: $this->elements;
		foreach($loop as $node) {
			if (! $node->parentNode )
				continue;
			$node->parentNode->removeChild($node);
		}
		return $this;
	}
	/**
	 * jQuey difference
	 *
	 * @param $markup
	 * @return unknown_type
	 * @TODO trigger change event for textarea
	 */
	public function markup($markup = null, $callback1 = null, $callback2 = null, $callback3 = null) {
		$args = func_get_args();
		if ($this->documentWrapper->isXML)
			return call_user_func_array(array($this, 'xml'), $args);
		else
			return call_user_func_array(array($this, 'html'), $args);
	}
	/**
	 * jQuey difference
	 *
	 * @param $markup
	 * @return unknown_type
	 */
	public function markupOuter($callback1 = null, $callback2 = null, $callback3 = null) {
		$args = func_get_args();
		if ($this->documentWrapper->isXML)
			return call_user_func_array(array($this, 'xmlOuter'), $args);
		else
			return call_user_func_array(array($this, 'htmlOuter'), $args);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $html
	 * @return string|phpQuery|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @TODO force html result
	 */
	public function html($html = null) {
                
		if ($html) {
			// INSERT
                        $nodes = $this->documentWrapper->import($html);
			
                        /*$t = new BitHTML5Tokenizer($html);
                        $document = $t->parse();
			$nodes = $document->firstChild;
			$this->empty();
                        var_dump($document->saveHTML());*/
                        $d = isset($this->elements[1]) ? $this->stack("1") : $this->elements;
			foreach($d as $alreadyAdded => $node) {
                                $node->nodeValue ='';
				foreach($nodes as $newNode) {
					$node->appendChild($alreadyAdded
						? $newNode->cloneNode(true)
						: $newNode
					);
				}
			}
			return $this;
		} else {
                        if(!isset($this->elements[0])){
                            return '';
                        }
			// FETCH
			$return = $this->documentWrapper->markup($this->elements, true);
			return $return;
		}
	}
	
	/**
	 * Enter description here...
	 * @TODO force html result
	 *
	 * @return String
	 */
	public function htmlOuter() {
		return $this->documentWrapper->markup($this->elements);
	}
        
	public function __toString() {
		return $this->markupOuter();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function children($selector = null) {
		$stack = array();
		foreach($this->stack("1") as $node) {
//			foreach($node->getElementsByTagName('*') as $newNode) {
			foreach($node->childNodes as $newNode) {
				if ($newNode->nodeType != 1)
					continue;
				if ($selector && ! $this->is($selector, $newNode))
					continue;
				if ($this->elementsContainsNode($newNode, $stack))
					continue;
				$stack[] = $newNode;
			}
		}
		$this->elementsBackup = $this->elements;
		$this->elements = $stack;
		return $this->newInstance();
	}
        
	/**
            * @access private
            * @TODO documentWrapper
	 */
	protected function isRoot( $node) {
		return $node instanceof DOMDOCUMENT
			|| ($node instanceof DOMELEMENT && $node->tagName == 'html')
			|| $this->root->isSameNode($node);
	}
	/**
          * @access private
	 */
	protected function stackIsRoot() {
		return !isset($this->elements[1]) && $this->isRoot($this->elements[0]);
	}
        
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function ancestors($selector = null) {
		return $this->children( $selector );
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function append( $content) {
		return $this->insert($content, __FUNCTION__);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function prepend( $content) {
		return $this->insert($content, __FUNCTION__);
	}
        
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function before($content) {
		return $this->insert($content, __FUNCTION__);
	}
	
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function after( $content) {
		return $this->insert($content, __FUNCTION__);
	}
        
        /**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function appendTo( $seletor) {
		return $this->insert($seletor, __FUNCTION__);
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function prependTo( $seletor) {
		return $this->insert($seletor, __FUNCTION__);
	}
        /**
	 * Enter description here...
	 *
	 * @param String|phpQuery
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function insertBefore( $seletor) {
		return $this->insert($seletor, __FUNCTION__);
	}
        
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function insertAfter( $seletor) {
		return $this->insert($seletor, __FUNCTION__);
	}
	/**
	 * Internal insert method. Don't use it.
	 *
	 * @param unknown_type $target
	 * @param unknown_type $type
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @access private
	 */
	public function insert($target, $type) {
		$insertFrom = $insertTo = array();
		$to = false;
		switch( $type) {
			case 'appendTo':
			case 'prependTo':
			case 'insertBefore':
			case 'insertAfter':
				$to = true;
		}
		switch(gettype($target)) {
			case 'string':
				if ($to) {
					// INSERT TO
					$insertFrom = $this->elements;
					if (phpQuery::isMarkup($target)) {
						// $target is new markup, import it
						$insertTo = $this->documentWrapper->import($target);
					// insert into selected element
					} else {
						// $tagret is a selector
						$thisStack = $this->elements;
						$this->toRoot();
						$insertTo = $this->find($target)->elements;
						$this->elements = $thisStack;
					}
				} else {
					// INSERT FROM
					$insertTo = $this->elements;
                                        
					$insertFrom = $this->documentWrapper->import($target);
				}
				break;
			case 'object':
				// phpQuery
				if ($target instanceof self) {
					if ($to) {
						$insertTo = $target->elements;
						if ($this->documentFragment && $this->stackIsRoot())
							$loop = $this->root->childNodes;
						else
							$loop = $this->elements;
						// import nodes if needed
						$insertFrom = $this->getDocumentID() == $target->getDocumentID()
							? $loop
							: $target->documentWrapper->import($loop);
					} else {
						$insertTo = $this->elements;
						if ( $target->documentFragment && $target->stackIsRoot() )
							$loop = $target->root->childNodes;
						else
							$loop = $target->elements;
						// import nodes if needed
						$insertFrom = $this->getDocumentID() == $target->getDocumentID()
							? $loop
							: $this->documentWrapper->import($loop);
					}
				// DOMNODE
				} elseif ($target instanceof DOMNODE) {
					if ( $to) {
						$insertTo = array($target);
						if ($this->documentFragment && $this->stackIsRoot())
							// get all body children
							$loop = $this->root->childNodes;
//							$loop = $this->find('body > *')->elements;
						else
							$loop = $this->elements;
						foreach($loop as $fromNode)
							// import nodes if needed
							$insertFrom[] = ! $fromNode->ownerDocument->isSameNode($target->ownerDocument)
								? $target->ownerDocument->importNode($fromNode, true)
								: $fromNode;
					} else {
						// import node if needed
						if (! $target->ownerDocument->isSameNode($this->document))
							$target = $this->document->importNode($target, true);
						$insertTo = $this->elements;
						$insertFrom[] = $target;
					}
				}
				break;
		}
		foreach($insertTo as $insertNumber => $toNode) {
			// we need static relative elements in some cases
			switch( $type) {
				case 'prependTo':
				case 'prepend':
					$firstChild = $toNode->firstChild;
					break;
				case 'insertAfter':
				case 'after':
					$nextSibling = $toNode->nextSibling;
					break;
			}
			foreach($insertFrom as $fromNode) {
				// clone if inserted already before
				$insert = $insertNumber
					? $fromNode->cloneNode(true)
					: $fromNode;
				switch($type) {
					case 'appendTo':
					case 'append':
						$toNode->appendChild($insert);
						$eventTarget = $insert;
						break;
					case 'prependTo':
					case 'prepend':
						$toNode->insertBefore(
							$insert,
							$firstChild
						);
						break;
					case 'insertBefore':
					case 'before':
						if (! $toNode->parentNode)
							throw new Exception("No parentNode, can't do {$type}()");
						else
							$toNode->parentNode->insertBefore(
								$insert,
								$toNode
							);
						break;
					case 'insertAfter':
					case 'after':
						if (! $toNode->parentNode)
							throw new Exception("No parentNode, can't do {$type}()");
						else
							$toNode->parentNode->insertBefore(
								$insert,
								$nextSibling
							);
						break;
				}
			}
		}
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @return Int
	 */
	public function index($subject) {
		$index = -1;
		$subject = $subject instanceof phpQueryObject
			? $subject->elements[0]
			: $subject;
		foreach($this->newInstance() as $k => $node) {
			if ($node->isSameNode($subject))
				$index = $k;
		}
		return $index;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $start
	 * @param unknown_type $end
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @testme
	 */
	public function slice($start, $end = null) {
		if ($end > 0)
			$end = $end-$start;
		return $this->newInstance(
			array_slice($this->elements, $start, $end)
		);
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function reverse() {
		$this->elementsBackup = $this->elements;
		$this->elements = array_reverse($this->elements);
		return $this->newInstance();
	}
	/**
	 * Return joined text content.
	 * @return String
	 */
	public function text($text = null) {
		if (!is_null($text)){
                    $test = $this->documentWrapper->document->createTextNode(htmlspecialchars($text));
                    
                    foreach($this->elements as $node) {  
                        $node->nodeValue = '';
                        $node->appendChild($test->cloneNode(true));
                    }    
                    return $this;
                }
		$return = '';
		foreach($this->elements as $node) {
			$txt = $node->textContent;
			if (count($this->elements) > 1 && $txt)
				$txt .= "\n";
			$return .= $txt;
		}
		return $return;
	}
	
	
	/**
	 * Safe rename of next().
	 *
	 * Use it ONLY when need to call next() on an iterated object (in same time).
	 * Normaly there is no need to do such thing ;)
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @access private
	 */
	public function _next($selector = null) {
		return $this->newInstance(
			$this->getElementSiblings('nextSibling', $selector, true)
		);
	}
	/**
	 * Use prev() and next().
	 *
	 * @deprecated
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @access private
	 */
	public function _prev($selector = null) {
		return $this->prev($selector);
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function prev($selector = null) {
		return $this->newInstance(
			$this->getElementSiblings('previousSibling', $selector, true)
		);
	}
	/**
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @todo
	 */
	public function prevAll($selector = null) {
		return $this->newInstance(
			$this->getElementSiblings('previousSibling', $selector)
		);
	}
	/**
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @todo FIXME: returns source elements insted of next siblings
	 */
	public function nextAll($selector = null) {
		return $this->newInstance(
			$this->getElementSiblings('nextSibling', $selector)
		);
	}
	/**
	 * @access private
	 */
	protected function getElementSiblings($direction, $selector = null, $limitToOne = false) {
		$stack = array();
		$count = 0;
		foreach($this->stack() as $node) {
			$test = $node;
			while( isset($test->{$direction}) && $test->{$direction}) {
				$test = $test->{$direction};
				if (! $test instanceof DOMELEMENT)
					continue;
				$stack[] = $test;
				if ($limitToOne)
					break;
			}
		}
		if ($selector) {
			$stackOld = $this->elements;
			$this->elements = $stack;
			$stack = $this->filter($selector, true)->stack();
			$this->elements = $stackOld;
		}
		return $stack;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function siblings($selector = null) {
		$stack = array();
		$siblings = array_merge(
			$this->getElementSiblings('previousSibling', $selector),
			$this->getElementSiblings('nextSibling', $selector)
		);
		foreach($siblings as $node) {
			if (! $this->elementsContainsNode($node, $stack))
				$stack[] = $node;
		}
		return $this->newInstance($stack);
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function not($selector = null) {
		$stack = array();
		if ($selector instanceof self || $selector instanceof DOMNODE) {
			foreach($this->stack() as $node) {
				if ($selector instanceof self) {
					$matchFound = false;
					foreach($selector->stack() as $notNode) {
						if ($notNode->isSameNode($node))
							$matchFound = true;
					}
					if (! $matchFound)
						$stack[] = $node;
				} else if ($selector instanceof DOMNODE) {
					if (! $selector->isSameNode($node))
						$stack[] = $node;
				} else {
					if (! $this->is($selector))
						$stack[] = $node;
				}
			}
		} else {
			$orgStack = $this->stack();
			$matched = $this->filter($selector, true)->stack();

			foreach($orgStack as $node)
				if (! $this->elementsContainsNode($node, $matched))
					$stack[] = $node;
		}
		return $this->newInstance($stack);
	}
	/**
	 * Enter description here...
	 *
	 * @param string|phpQueryObject
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function add($selector = null) {
		if (! $selector)
			return $this;
		$stack = array();
		$this->elementsBackup = $this->elements;
		$found = phpQuery::pq($selector, $this->getDocumentID());
		$this->merge($found->elements);
		return $this->newInstance();
	}
	/**
	 * @access private
	 */
	protected function merge() {
		foreach(func_get_args() as $nodes)
			foreach($nodes as $newNode )
				if (! $this->elementsContainsNode($newNode) )
					$this->elements[] = $newNode;
	}
	/**
	 * @access private
	 * TODO refactor to stackContainsNode
	 */
	protected function elementsContainsNode($nodeToCheck, $elementsStack = null) {
		$loop = ! is_null($elementsStack)
			? $elementsStack
			: $this->elements;
                
		foreach($loop as $node) {
			if ( $node->isSameNode( $nodeToCheck ) )
				return true;
		}
                
		return false;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function parent($selector = null) {
		$stack = array();
		foreach($this->elements as $node )
			if ( $node->parentNode && ! $this->elementsContainsNode($node->parentNode, $stack) )
				$stack[] = $node->parentNode;
		$this->elementsBackup = $this->elements;
		$this->elements = $stack;
		if ( $selector )
			$this->filter($selector, true);
		return $this->newInstance();
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function parents($selector = null) {
		$stack = array();
		foreach($this->elements as $node) {
			$test = $node;
			while( $test->parentNode) {
				$test = $test->parentNode;
				if ($this->isRoot($test))
					break;
				if (! $this->elementsContainsNode($test, $stack)) {
					$stack[] = $test;
					continue;
				}
			}
		}
		$this->elementsBackup = $this->elements;
		$this->elements = $stack;
		if ( $selector )
			$this->filter($selector, true);
		return $this->newInstance();
	}
	/**
	 * Internal stack iterator.
	 *
	 * @access private
	 */
	public function stack($nodeTypes = null) {
		if (!isset($nodeTypes))
			return $this->elements;
                
                static $stringTypes;
                if(!isset($stringTypes[$nodeTypes]))
                    $stringTypes[$nodeTypes] = array_flip(explode(',',$nodeTypes));
                
		$return = array();
		foreach($this->elements as $node) {
			if (isset($stringTypes[$nodeTypes][$node->nodeType]))
				$return[] = $node;
		}
		return $return;
	}
        
	static protected $_attrF = array('*'=>'');       
	public function attr($attr = null, $value = null) {
                if(!isset($this->elements[0]))
                    return $this;
                
                $vNull = is_null($value);
                $isArray = is_array($attr);
                $Attrset = !$isArray && isset(self::$_attrF[$attr]);
                $return = array();
                $size = isset($this->elements[1]);
                   
                if(!$size)
                    if(!$isArray && $vNull)
                    {
                        if ($Attrset) {
                            foreach(($this->elements[0]->attributes) as $n => $v)
                                $return[$n] = $v->value;
                            return (object)$return;
                        } else
                            return $this->elements[0]->getAttribute($attr);
                    }
                    else{
                        if($isArray){
                            foreach($attr as $k=>$v)
                                @$this->elements[0]->setAttribute($k, $v);
                        }else if($Attrset)
                            foreach(($this->getNodeAttrs($node)) as $a) 
                                @$this->elements[0]->setAttribute($a, $value);
                        else
                                @$this->elements[0]->setAttribute($attr, $value);
                        return $this;
                    }  
                    
                $d = ($size) ? $this->stack("1") : $this->elements;
                foreach($d as $node)
                    if($isArray)
                        foreach($attr as $k=>$v)
                            @$node->setAttribute($k, $v);
                    else if($Attrset)
                        foreach(($this->getNodeAttrs($node)) as $a) 
                            @$node->setAttribute($a, $value);
                    else
                            @$node->setAttribute($attr, $value);
		return $this;
	}
	/**
	 * @access private
	 */
	protected function getNodeAttrs($node) {
		$return = array();
		foreach($node->attributes as $n => $o)
			$return[] = $n;
		return $return;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function removeAttr($attr) {
		foreach($this->stack("1") as $node) {
			$loop = $attr == '*'
				? $this->getNodeAttrs($node)
				: array($attr);
			foreach($loop as $a) {
				$oldValue = $node->getAttribute($a);
				$node->removeAttribute($a);
			}
		}
		return $this;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function andSelf() {
		if ( $this->previous )
			$this->elements = array_merge($this->elements, $this->previous->elements);
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function addClass( $className) {
		if (!isset($this->elements[0]) || !$className)
			return $this;
                
		$d = isset($this->elements[1]) ? $this->stack("1") : $this->elements;
		foreach($d as $node) {
                        $cls = $node->getAttribute('class');
			if ($cls == '' || self::_matchClasses($cls, $className) == "0")
				$node->setAttribute(
					'class',
					trim($cls.' '.$className)
				);
		}
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @param	string	$className
	 * @return	bool
	 */
	public function hasClass($className) {
		foreach($this->stack("1") as $node) {
			if ( $this->is(".$className", $node))
				return true;
		}
		return false;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function removeClass($className) {
		foreach($this->stack("1") as $node) {
			$classes = explode( ' ', $node->getAttribute('class'));
			if ( in_array($className, $classes)) {
				$classes = array_diff($classes, array($className));
				if ( $classes )
					$node->setAttribute('class', implode(' ', $classes));
				else
					$node->removeAttribute('class');
			}
		}
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function toggleClass($className) {
		foreach($this->stack("1") as $node) {
			if ( $this->is( $node, '.'.$className ))
				$this->removeClass($className);
			else
				$this->addClass($className);
		}
		return $this;
	}
	/**
	 * Proper name without underscore (just ->empty()) also works.
	 *
	 * Removes all child nodes from the set of matched elements.
	 *
	 * Example:
	 * pq("p")._empty()
	 *
	 * HTML:
	 * <p>Hello, <span>Person</span> <a href="#">and person</a></p>
	 *
	 * Result:
	 * [ <p></p> ]
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @access private
	 */
	public function _empty() {
                $d = isset($this->elements[1]) ? $this->stack("1") : $this->elements;
		foreach($d as $node) {
			// thx to 'dave at dgx dot cz'
			$node->nodeValue = '';
		}
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @param array|string $callback Expects $node as first param, $index as second
	 * @param array $scope External variables passed to callback. Use compact('varName1', 'varName2'...) and extract($scope)
	 * @param array $arg1 Will ba passed as third and futher args to callback.
	 * @param array $arg2 Will ba passed as fourth and futher args to callback, and so on...
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function each($callback, $param1 = null, $param2 = null, $param3 = null) {
		$paramStructure = null;
		if (func_num_args() > 1) {
			$paramStructure = func_get_args();
			$paramStructure = array_slice($paramStructure, 1);
		}
		foreach($this->elements as $v)
			phpQuery::callbackRun($callback, array($v), $paramStructure);
		return $this;
	}
	/**
	 * Run callback on actual object.
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 */
	public function callback($callback, $param1 = null, $param2 = null, $param3 = null) {
		$params = func_get_args();
		$params[0] = $this;
		phpQuery::callbackRun($callback, $params);
		return $this;
	}
	/**
	 * Enter description here...
	 *
	 * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery
	 * @todo add $scope and $args as in each() ???
	 */
	public function map($callback, $param1 = null, $param2 = null, $param3 = null) {
		$params = func_get_args();
		array_unshift($params, $this->elements);
		return $this->newInstance(
			call_user_func_array(array('phpQuery', 'map'), $params)
		);
	}
	/**
	 * Enter description here...
	 * 
	 * @param <type> $key
	 * @param <type> $value
	 */
	public function data($key, $value = null) {
                if(is_array($key)){
                    $res = array();
                    foreach($key as $k=>$v)
                        $res['data-'.$k] = $v; 
                    
                    return $this->attr($res);
                }
		elseif (! isset($value)) {
                    return $this->attr('data-'.$key);
		} else {
                    $this->attr('data-'.$key, $value);
                    return $this;
		}
	}
	/**
	 * Enter description here...
	 * 
	 * @param <type> $key
	 */
	public function removeData($key) {
		foreach($this as $node)
			phpQuery::removeData($node, $key, $this->getDocumentID());
		return $this;
	}
	
	// HELPERS
	public function whois($oneNode = null) {
		$return = array();
		$loop = $oneNode
			? array( $oneNode )
			: $this->elements;
		foreach($loop as $node) {
			if (isset($node->tagName)) {
				$tag = in_array($node->tagName, array('php', 'js'))
					? strtoupper($node->tagName)
					: $node->tagName;
				$return[] = $tag
					.($node->getAttribute('id')
						? '#'.$node->getAttribute('id'):'')
					.($node->getAttribute('class')
						? '.'.join('.', explode(' ', $node->getAttribute('class'))):'')
					.($node->getAttribute('name')
						? '[name="'.$node->getAttribute('name').'"]':'')
					.($node->getAttribute('selected')
						? '[selected]':'')
					.($node->getAttribute('checked')
						? '[checked]':'')
				;
			} else if ($node instanceof DOMTEXT) {
				if (trim($node->textContent))
					$return[] = 'Text:'.substr(str_replace("\n", ' ', $node->textContent), 0, 15);
			} else {

			}
		}
		return $oneNode && isset($return[0])
			? $return[0]
			: $return;
	}
	
	public function dumpTree($html = true, $title = true) {
		$output = $title
			? 'DUMP #'.(phpQuery::$dumpCount++)." \n" : '';
		$debug = phpQuery::$debug;
		phpQuery::$debug = false;
		foreach($this->stack() as $node)
			$output .= $this->__dumpTree($node);
		phpQuery::$debug = $debug;
		print $html
			? nl2br(str_replace(' ', '&nbsp;', $output))
			: $output;
		return $this;
	}
	private function __dumpTree($node, $intend = 0) {
		$whois = $this->whois($node);
		$return = '';
		if ($whois)
			$return .= str_repeat(' - ', $intend).$whois."\n";
		if (isset($node->childNodes))
			foreach($node->childNodes as $chNode)
				$return .= $this->__dumpTree($chNode, $intend+1);
		return $return;
	}
        
        // These are constants describing the content model
        const PCDATA    = 0;
        const RCDATA    = 1;
        const CDATA     = 2;
        const PLAINTEXT = 3;

        // These are constants describing tokens
        // XXX should probably be moved somewhere else, probably the
        // HTML5 class.
        const DOCTYPE        = 0;
        const STARTTAG       = 1;
        const ENDTAG         = 2;
        const COMMENT        = 3;
        const CHARACTER      = 4;
        const SPACECHARACTER = 5;
        const EOF            = 6;
        const PARSEERROR     = 7;


        const SELECTOR_TAG              = 0x0001;
        const SELECTOR_NEW_QUERY        = 0x0002;
        const SELECTOR_ATTR_DATA        = 0x0004;
        const SELECTOR_ATTR_CLASS       = 0x0008;
        const SELECTOR_ATTR_ID          = 0x0010;
        const SELECTOR_ATTR             = 0x0200;
        const SELECTOR_ATTR_E           = 0x0400;
        const SELECTOR_PRECEDED         = 0x0800;
        const SELECTOR_PSEUDO_CLASS     = 0x1000;
        const SELECTOR_UNK              = 0x2000;
        const SELECTOR_NORMAL_WHITESPACE= 0x4000;
        const SELECTOR_WHITESPACE       = 0x8000;
        // These are constants representing bunches of characters.
        const ALPHA       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        const UPPER_ALPHA = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const LOWER_ALPHA = 'abcdefghijklmnopqrstuvwxyz';
        const DIGIT       = '0123456789';
        const HEX         = '0123456789ABCDEFabcdef';
        const WHITESPACE  = "\t\n\x0c ";

        static protected $cache = array();

        static protected $patterns = array('@\s+@','@\s*(>|\\+|~)\s*@');

        static protected $_parser = ['['=>self::SELECTOR_ATTR,']'=>self::SELECTOR_ATTR_E, '~'=>self::SELECTOR_PRECEDED,'+'=>self::SELECTOR_PRECEDED,':'=>self::SELECTOR_PSEUDO_CLASS,'/'=>self::SELECTOR_UNK,'*' => self::SELECTOR_TAG, '|'  => self::SELECTOR_TAG, '-'  => self::SELECTOR_TAG," "=>self::SELECTOR_NORMAL_WHITESPACE,"\t" => self::SELECTOR_WHITESPACE ,"\n" => self::SELECTOR_WHITESPACE,"\x0c" => self::SELECTOR_WHITESPACE ,"."=>self::SELECTOR_ATTR_CLASS,","=>self::SELECTOR_NEW_QUERY,"#"=>self::SELECTOR_ATTR_ID];

        protected static function parse(&$data)
        {
            if(!$data)
                return[[]];

            $hash = $data;
            if(isset(self::$cache[$hash]))
                return self::$cache[$hash];

            $stream = new InputStream(trim($data));
            $queries = [[]];
            $return =& $queries[0];

            static $ch;
                if(!$ch)
                    $ch = array_flip(str_split(self::ALPHA));

            $parser = &self::$_parser;
            while($char = $stream->char()) {
                    $call = isset($parser[$char]) ? $parser[$char] : 0;

                    if($call & self::SELECTOR_ATTR_CLASS){
                        $return[] = $char.$stream->charsWhile(self::ALPHA.self::DIGIT.'.-_');
                        continue;
                    }elseif($call & self::SELECTOR_ATTR_ID){
                        $return[] = $char.$stream->charsWhile(self::ALPHA.self::DIGIT.'-_');
                        continue;
                    }
                    /* Consume the next input character */
                    elseif ($call & self::SELECTOR_TAG || isset($ch[$char])) {
                        $return[] = $char.$stream->charsWhile(self::ALPHA.self::DIGIT.'*|-');
                        continue;
                    }elseif($call & self::SELECTOR_NEW_QUERY){
                        $stream->char();
                        $queries[] = array();
                        $return =& $queries[ count($queries)-1 ];
                        $stream->charsWhile(' ');
                        continue;
                    }elseif($call & self::SELECTOR_WHITESPACE){
                        $stream->charsWhile(self::WHITESPACE);
                        continue;
                    }elseif($call & self::SELECTOR_NORMAL_WHITESPACE){
                        $return[] = $char;
                        continue;
                    }elseif($call & self::SELECTOR_PRECEDED){
                        $space = $stream->charsWhile(' ');
                        $data  = $stream->charsWhile(self::ALPHA.'.-*');
                        $return[] = $space.$data;
                        continue;
                    }
                    elseif($call & self::SELECTOR_PSEUDO_CLASS){
                        $tmp = $char.$stream->charsWhile(self::ALPHA.self::DIGIT.'-');
                        // with arguments ?
                        if ( $stream->nchar() == '(') {
                                $tmp .= $stream->char();
                                $stack = 1;
                                while( $c = $stream->char()) {
                                        $tmp .= $c;
                                        if ( $c == '(') {
                                                $stack++;
                                        } else if ( $c == ')') {
                                                $stack--;
                                                if (! $stack )
                                                        break;
                                        }
                                }
                        }
                        $return[] = $tmp;
                        continue;
                    }elseif( $call & self::SELECTOR_ATTR) {
                            $stack = 1;
                            $tmp = $char;
                            while( $c = $stream->char()) {
                                    $ca = isset($parser[$c]) ? $parser[$c] : 0;
                                    $tmp .= $c;

                                    $stack += ($ca & self::SELECTOR_ATTR) ? 1 : (($ca & self::SELECTOR_ATTR_E) ? -1 : 0);
                                    if (!$stack)
                                        break;
                            }
                            $return[] = $tmp;
                        continue;    
                    } 
                    elseif($call & SELECTOR_UNK || $stream->nchar() === '/')
                    {
                        $stream->char();
                        $return[] = ' ';
                        continue; 
                    }
                 }
                foreach($queries as $k => $q) {
                        if (isset($q[0])) {
                            if (isset($q[0][0]) && $q[0][0] == ':')
                                    array_unshift($queries[$k], '*');
                            if ($q[0] != '>')
                                    array_unshift($queries[$k], ' ');
                        }
                }
                self::$cache[$hash] = $queries;
                return $queries;
        }

        static private $explodeCache = array();

        /**
         * Enter description here...
         *
         * In the future, when PHP will support XLS 2.0, then we would do that this way:
         * contains(tokenize(@class, '\s'), "something")
         * @param unknown_type $class
         * @param unknown_type $node
         * @return boolean
         * @access private
         */
        static function _matchClasses($classes, $class) {
            if($classes == '')
                return "0";

            if (!isset(self::$explodeCache[$class.$classes])){

                if(!isset(self::$explodeCache[$class]))
                    self::$explodeCache[$class] = (strpos($class, " ") === false) ? [substr($class, 1)] : explode('.', substr($class, 1));

                if(!isset(self::$explodeCache[$classes]))
                    self::$explodeCache[$classes] = (strpos($classes, " ") === false) ? [$classes] : explode(' ', $classes);

                $a = !isset(self::$explodeCache[$class][1]);
                $b = !isset(self::$explodeCache[$classes][1]);

                if($a && $b){
                    $ret = self::$explodeCache[$class][0] == self::$explodeCache[$classes][0] ? 0 : 1;
                    self::$explodeCache[$class.$classes] = $ret;
                }else if($a && !$b){
                    $tmp = array_flip(self::$explodeCache[$classes]);
                    $ret = isset($tmp[self::$explodeCache[$class][0]]) ? 0 : 1;
                    self::$explodeCache[$class.$classes] = $ret;
                }else if($a)
                {
                    $ret = (strpos(" ".$classes." ", " ".self::$explodeCache[$class][0]." ") === false) ? 1 : 0;
                    self::$explodeCache[$class.$classes] = $ret;
                } 
                else
                    self::$explodeCache[$class.$classes] = count(array_diff(
                            self::$explodeCache[$class],
                            self::$explodeCache[$classes]
                    ));
            }
            return (!self::$explodeCache[$class.$classes]) ? "1" : "0";
        }
        
        
}
