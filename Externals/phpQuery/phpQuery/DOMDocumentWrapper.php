<?php
/**
 * DOMDocumentWrapper class simplifies work with DOMDocument.
 *
 * Know bug:
 * - in XHTML fragments, <br /> changes to <br clear="none" />
 *
 * @todo check XML catalogs compatibility
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 * @package phpQuery
 */

class DOMDocumentWrapper {
	/**
	 * @var DOMDocument
	 */
	public $document;
	public $id;
	/**
	 * @todo Rewrite as method and quess if null.
	 * @var unknown_type
	 */
	public $contentType = '';
	public $xpath;
	public $uuid = 0;
	public $data = array();
	public $dataNodes = array();
	public $events = array();
	public $eventsNodes = array();
	public $eventsGlobal = array();
	/**
	 * @TODO iframes support http://code.google.com/p/phpquery/issues/detail?id=28
	 * @var unknown_type
	 */
	public $frames = array();
	/**
	 * Document root, by default equals to document itself.
	 * Used by documentFragments.
	 *
	 * @var DOMNode
	 */
	public $root;
	public $isDocumentFragment;
	public $isXML = false;
	public $isXHTML = false;
	public $isHTML = false;
	public $charset;
	public function __construct($markup = null, $contentType = null, $newDocumentID = null) {
		if (isset($markup))
			$this->load($markup, $contentType, $newDocumentID);
		$this->id = $newDocumentID
			? $newDocumentID
			: md5(microtime());
	}
	public function load($markup, $contentType = null, $newDocumentID = null) {
                $this->contentType = strtolower($contentType);
                $this->isXML = $this->isXHTML = $this->isHTML = false;
                $loaded = true;
		if ($markup instanceof DOMDOCUMENT) {
			$this->document = $markup;
			$this->root = $this->document;
			$this->charset = $this->document->encoding;
		} else {
                    if ($this->contentType) {
			// content determined by contentType
			list($cType, $charset) = $this->contentTypeToArray($this->contentType);
			switch($cType) {
				case 'text/html':
                                        $this->isHTML = true;
					$loaded = $this->loadMarkupHTML($markup, $charset);
                                        
				break;
				case 'application/xhtml+xml':
                                        $this->isXHTML =true;
				case 'text/xml':
                                        $this->isXML = true;
					$loaded = $this->loadMarkupXML($markup, $charset);
				break;
				default:
					// for feeds or anything that sometimes doesn't use text/xml
					if (strpos('xml', $this->contentType) !== false) {
						$this->isXML = true;
						$loaded = $this->loadMarkupXML($markup, $charset);
					} else
						phpQuery::debug("Could not determine document type from content type '{$this->contentType}'");
			}
                    } else {
                            // content type autodetection
                            if ($this->isXML($markup)) {
                                    $loaded = $this->loadMarkupXML($markup);
                                    if (! $loaded && $this->isXHTML) {
                                            $loaded = $this->loadMarkupHTML($markup);
                                    }
                            } else {
                                    $loaded = $this->loadMarkupHTML($markup);
                            }
                    }
		}
		if ($loaded) {		
			$this->xpath = new DOMXPath($this->document);
                        $this->xpath->registerNamespace("php", "http://php.net/xpath");
                        // Register PHP functions (no restrictions)
                        $this->xpath->registerPHPFunctions();
			$this->afterMarkupLoad();
			return true;
		}
		return false;
	}
	protected function afterMarkupLoad() {
		if ($this->isXHTML) {
                    $this->xpath->registerNamespace("html", "http://www.w3.org/1999/xhtml");
		}
	}
        
	protected function loadMarkupHTML(&$markup, $requestedCharset = null) {
                if (!isset($this->isDocumentFragment))
			$this->isDocumentFragment = self::isDocumentFragmentHTML($markup);
                
                $charset = ($requestedCharset) ? $requestedCharset : $this->charsetFromHTML($markup);
                
		if (!$charset)
			$charset = phpQuery::$defaultCharset ? phpQuery::$defaultCharset : 'utf-8';
		
		$return = false;
		if ($this->isDocumentFragment) {
			$return = $this->documentFragmentLoadMarkup($this, $charset, $markup);
		} else {
                        $this->document = new DOMDocument('1.0', $charset);
                        $this->charset = $this->document->encoding;
                        $this->document->formatOutput = true;
                        $this->document->preserveWhiteSpace = false;
                        
			$return = phpQuery::$debug === 2
				? $this->document->loadHTML($markup)
				: @$this->document->loadHTML($markup);
			if ($return)
				$this->root = $this->document;
		}
		if ($return && ! $this->contentType)
			$this->contentType = 'text/html';
		return $return;
	}
        
	protected function loadMarkupXML($markup, $requestedCharset = null) {
                if (! $this->contentType) {
                    if ($this->isXHTML)
                            $this->contentType = 'application/xhtml+xml';
                    else
                            $this->contentType = 'text/xml';
                }
		// check agains XHTML in contentType or markup
		$isContentTypeXHTML = $this->isXHTML();
		$isMarkupXHTML = $this->isXHTML($markup);
		// determine document fragment
		if (! isset($this->isDocumentFragment))
			$this->isDocumentFragment = $this->isXHTML
				? self::isDocumentFragmentXHTML($markup)
				: self::isDocumentFragmentXML($markup);
		// this charset will be used
		$charset = null;
                
                $charset = ($requestedCharset) ? $requestedCharset : $this->charsetFromXML($markup);
                if (!$charset)
                    $charset =  ($this->isXHTML) ? $this->charsetFromHTML($markup) : null;
                
		if (!$charset)
			$charset = phpQuery::$defaultCharset ? phpQuery::$defaultCharset : 'utf-8';
                
		$return = false;
		if ($this->isDocumentFragment) {
			$return = $this->documentFragmentLoadMarkup($this, $charset, $markup);
		} else {
                        $this->document = new DOMDocument('1.0', $charset);
                        $this->charset = $this->document->encoding;
                        $this->document->formatOutput = true;
                        $this->document->preserveWhiteSpace = false;
                        
			if (phpversion() < 5.1) {
				$this->document->resolveExternals = true;
				$return = phpQuery::$debug === 2
					? $this->document->loadXML($markup)
					: @$this->document->loadXML($markup);
			} else {
				$libxmlStatic = phpQuery::$debug === 2
					? LIBXML_DTDLOAD|LIBXML_DTDATTR|LIBXML_NONET
					: LIBXML_DTDLOAD|LIBXML_DTDATTR|LIBXML_NONET|LIBXML_NOWARNING|LIBXML_NOERROR;
				$return = $this->document->loadXML($markup, $libxmlStatic);
			}
			if ($return)
				$this->root = $this->document;
		}
                return $return;
	}
	protected function isXHTML($markup = null) {
		if (! isset($markup)) {
			return strpos($this->contentType, 'xhtml') !== false;
		}
		// XXX ok ?
		return strpos($markup, "<!DOCTYPE html") !== false;
	}
	protected function isXML($markup) {
		return strpos(substr($markup, 0, 100), '<'.'?xml') !== false;
	}
	protected function contentTypeToArray($contentType) {
                static $cache;
                if(isset($cache[$contentType]))
                    return $cache[$contentType];
		$matches = explode(';', trim(strtolower($contentType)));
		if (isset($matches[1])) {
			$matches[1] = explode('=', $matches[1]);
			// strip 'charset='
			$matches[1] = isset($matches[1][1]) && trim($matches[1][1])
				? $matches[1][1]
				: $matches[1][0];
		} else
			$matches[1] = null;
                
                $cache[$contentType] =$matches; 
		return $matches;
	}
	/**
	 *
	 * @param $markup
	 * @return array contentType, charset
	 */
	protected function contentTypeFromHTML($markup) {
		$matches = array();
		// find meta tag
		preg_match('@<meta[^>]+http-equiv\\s*=\\s*(["|\'])Content-Type\\1[^>]+content\\s*=\\s*["|\'](.+?)\\1>@i',
			$markup, $matches
		);
		if (! isset($matches[0]))
			return array(null, null);
                
		return $this->contentTypeToArray($matches[2]);
	}
	protected function charsetFromHTML($markup) {
		$contentType = $this->contentTypeFromHTML($markup);
		return $contentType[1];
	}
	protected function charsetFromXML($markup) {
		$matches;
		// find declaration
		preg_match('@<'.'?xml[^>]+encoding\\s*=\\s*(["|\'])(.*?)\\1@i',
			$markup, $matches
		);
		return isset($matches[2])
			? strtolower($matches[2])
			: null;
	}
	
	public static function isDocumentFragmentHTML($markup) {
		return stripos($markup, '<html') === false && stripos($markup, '<!doctype') === false;
	}
	public static function isDocumentFragmentXML($markup) {
		return stripos($markup, '<'.'?xml') === false;
	}
	public static function isDocumentFragmentXHTML($markup) {
		return self::isDocumentFragmentHTML($markup);
	}
	public function importAttr($value) {
		// TODO
	}
	/**
	 *
	 * @param $source
	 * @param $target
	 * @param $sourceCharset
	 * @return array Array of imported nodes.
	 */
	public function import(&$source, $sourceCharset = null) {
		// TODO charset conversions
		$return = array();
		if ($source instanceof DOMNODE && !($source instanceof DOMNODELIST))
			$source = array($source);
                
		if (is_array($source) || $source instanceof DOMNODELIST) {
			foreach($source as $node)
				$return[] = $this->document->importNode($node, true);
		} else {
			// string markup
			$fake = $this->documentFragmentCreate($source, $sourceCharset);
			if ($fake === false)
				throw new Exception("Error loading documentFragment markup");
			else
				return $this->import($fake->root->childNodes);
		}
		return $return;
	}
	/**
	 * Creates new document fragment.
	 *
	 * @param $source
	 * @return DOMDocumentWrapper
	 */
	protected function documentFragmentCreate(&$source, $charset = null) {
		$fake = new DOMDocumentWrapper();
		$fake->contentType = $this->contentType;
		$fake->isXML = $this->isXML;
		$fake->isHTML = $this->isHTML;
		$fake->isXHTML = $this->isXHTML;
		$fake->root = $fake->document;
		if (! $charset)
			$charset = $this->charset;
		if ($source instanceof DOMNODE && !($source instanceof DOMNODELIST))
			$source = array($source);
		if (is_array($source) || $source instanceof DOMNODELIST) {
			// dom nodes
			// load fake document
			if (! $this->documentFragmentLoadMarkup($fake, $charset))
				return false;
			$nodes = $fake->import($source);
			foreach($nodes as $node)
				$fake->root->appendChild($node);
		} else {
			// string markup
			$this->documentFragmentLoadMarkup($fake, $charset, $source);
		}
		return $fake;
	}
	/**
	 *
	 * @param $document DOMDocumentWrapper
	 * @param $markup
	 * @return $document
	 */
	private function documentFragmentLoadMarkup(&$fragment, $charset, &$markup = null) {
		$fragment->isDocumentFragment = false;
		if ($fragment->isXML) {
			if ($fragment->isXHTML) {
				// add FAKE element to set default namespace
				$fragment->loadMarkupXML('<?xml version="1.0" encoding="'.$charset.'"?>'
					.'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '
					.'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
					.'<fake xmlns="http://www.w3.org/1999/xhtml">'.$markup.'</fake>');
				$fragment->root = $fragment->document->firstChild->nextSibling;
			} else {
				$fragment->loadMarkupXML('<?xml version="1.0" encoding="'.$charset.'"?><fake>'.$markup.'</fake>');
				$fragment->root = $fragment->document->firstChild;
			}
		} else {
                        
			$markup2 = phpQuery::$defaultDoctype.'<html><head><meta http-equiv="Content-Type" content="text/html;charset='
				.$charset.'"></head>';
			$noBody = strpos($markup, '<body') === false;
			if ($noBody)
				$markup2 .= '<body>';
			$markup2 .= $markup;
			if ($noBody)
				$markup2 .= '</body>';
			$markup2 .= '</html>';
			$fragment->loadMarkupHTML($markup2,$this->charset);
			// TODO resolv body tag merging issue
			$fragment->root = $fragment->document->firstChild->nextSibling->firstChild->nextSibling;
		}
		if (! $fragment->root)
			return false;
		$fragment->isDocumentFragment = true;
		return true;
	}
	protected function documentFragmentToMarkup($fragment) {
		phpQuery::debug('documentFragmentToMarkup');
		$tmp = $fragment->isDocumentFragment;
		$fragment->isDocumentFragment = false;
		$markup = $fragment->markup();
		if ($fragment->isXML) {
			$markup = substr($markup, 0, strrpos($markup, '</fake>'));
			if ($fragment->isXHTML) {
				$markup = substr($markup, strpos($markup, '<fake')+43);
			} else {
				$markup = substr($markup, strpos($markup, '<fake>')+6);
			}
		} else {
				$markup = substr($markup, strpos($markup, '<body>')+6);
				$markup = substr($markup, 0, strrpos($markup, '</body>'));
		}
		$fragment->isDocumentFragment = $tmp;
		if (phpQuery::$debug)
			phpQuery::debug('documentFragmentToMarkup: '.substr($markup, 0, 150));
		return $markup;
	}
	/**
	 * Return document markup, starting with optional $nodes as root.
	 *
	 * @param $nodes	DOMNode|DOMNodeList
	 * @return string
	 */
	public function markup($nodes = null, $innerMarkup = false) {
		if (isset($nodes) && !isset($nodes[1]) && $nodes[0] instanceof DOMDOCUMENT)
			$nodes = null;
                
		if (isset($nodes)) {
			$markup = '';
                        
			if (!is_array($nodes) && !($nodes instanceof DOMNODELIST) )
				$nodes = array($nodes);
			if ($this->isDocumentFragment && ! $innerMarkup)
				foreach($nodes as $i => $node)
					if ($node->isSameNode($this->root)) {
					//	var_dump($node);
						$nodes = array_slice($nodes, 0, $i)
							+ phpQuery::DOMNodeListToArray($node->childNodes)
							+ array_slice($nodes, $i+1);
						}
			if ($this->isXML && ! $innerMarkup) {
				// we need outerXML, so we can benefit from
				// $node param support in saveXML()
				foreach($nodes as $node)
					$markup .= $this->document->saveXML($node);
			} else {
				$loop = array();
				if ($innerMarkup)
					foreach($nodes as $node) {
						if ($node->childNodes)
							foreach($node->childNodes as $child)
								$loop[] = $child;
						else
							$loop[] = $node;
					}
				else
					$loop = $nodes;
				
				$fake = $this->documentFragmentCreate($loop);
				$markup = $this->documentFragmentToMarkup($fake);
			}
			if ($this->isXHTML) {
				self::debug("Fixing XHTML");
				$markup = self::markupFixXHTML($markup);
			}
			
			return $markup;
		} else {
			if ($this->isDocumentFragment) {
				$markup = $this->documentFragmentToMarkup($this);
				return $markup;
			} else {
				$markup = $this->isXML
					? $this->document->saveXML()
					: $this->document->saveHTML();
				if ($this->isXHTML) {
					$markup = self::markupFixXHTML($markup);
				}
				return $markup;
			}
		}
	}
	protected static function markupFixXHTML($markup) {
		$markup = self::expandEmptyTag('script', $markup);
		$markup = self::expandEmptyTag('select', $markup);
		$markup = self::expandEmptyTag('textarea', $markup);
		return $markup;
	}
	public static function debug($text) {
		phpQuery::debug($text);
	}
	/**
	 * expandEmptyTag
	 *
	 * @param $tag
	 * @param $xml
	 * @return unknown_type
	 * @author mjaque at ilkebenson dot com
	 * @link http://php.net/manual/en/domdocument.savehtml.php#81256
	 */
	public static function expandEmptyTag($tag, $xml){
        $indice = 0;
        while ($indice< strlen($xml)){
            $pos = strpos($xml, "<$tag ", $indice);
            if ($pos){
                $posCierre = strpos($xml, ">", $pos);
                if ($xml[$posCierre-1] == "/"){
                    $xml = substr_replace($xml, "></$tag>", $posCierre-1, 2);
                }
                $indice = $posCierre;
            }
            else break;
        }
        return $xml;
	}
}