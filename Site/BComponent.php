<?php
/**
 * Represents an Master Class
 * 
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * 
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): BComponent.php
 * @package     Site/BComponent
 * @category    Site
 */
abstract class BComponent implements ArrayAccess,ICompomnent, BLessPHP, IDatabaseHolder {

    CONST AUTO = TRUE;
    CONST TYPE = 'void';
    CONST RENDER = 'View';
    CONST APPEND = '#content';
    CONST ATYPE  = 'append';
    const NEEDINIT = false;
    
    protected $_root;
    public $_renderReturn;
    protected $_page;
    protected $_route;
    protected $_append;
    protected $_appendfunc;
    protected $_vars = array();
    protected static $_globalvars = array();
    protected $_less = null;
    protected $_init = array();
    protected $_templates = array();
    protected $_components = array();
    protected $_modules = array();
    protected $_isAjax = false;
    
    /**
     * Standard constant for primary sql statement identifier
     */
    const STANDARD_PREPARE_STATEMENT = 'query';

    protected $_preparedStatements = array();

    //XXX: isinrole? other LessPHP tags
    protected $_starttaghandler = array('var' => 'setVar','global'=>'setGlobalVar','template' => 'LoadTemplate', 'auto' => 'setAuto', 'return' => 'setReturn', 'header' => 'setHeader','prepare' => 'setPrepare');
    protected $_rendertaghandler = array('module' => 'LoadModule');
    protected $_finishtaghandler = array('component' => 'LoadComponent');

    public function setDB($key, Array $value) {
        throw new InvalidException('no_actic_function');
    }

    /**
     * @return PDO
     */
    public function getDB($key = null) {
        if (Bit::isSite())
            return Site::getDB($key);

        throw new SiteException('site_need_init');
    }

    /**
     * Sets a prepared statement
     * @param type $querykey Aliasname for query
     * @param type $databasekey Aliasname for database connection
     * @param type $statement Statement which should be executed
     * @return void
     */
    public function setPrepare($querykey = null, $databasekey = null, $statement = null) {
        $h = explode(" ", $querykey, 3);
        list($querykey, $databasekey, $statement) = $h;
        $this->_preparedStatements[$querykey] = array('0' => $databasekey, '1' => $statement);
    }

    /**
     * Aliasfunction for lazy programmers using executePreparedStatement
     * @see self::executePreparedStatement()
     * @param string $key Querykey
     * @param array $options Options
     * @return PDOStatement
     */
    public function ePS($key = null, $options = array()) {
        return $this->executePreparedStatement($key ? $key : static::STANDARD_PREPARE_STATEMENT, $options);
    }

    /**
     * Function for @prepare usage to execute a prepared statement
     * @param string $key Querykey
     * @param array $options Options
     * @return PDOStatement
     */
    public function executePreparedStatement($key = null, $options = array()) {
        $key = $key ? $key : static::STANDARD_PREPARE_STATEMENT;
        if (isset($this->_preparedStatements[$key])) {
            if (is_array($this->_preparedStatements[$key])) {
                list($databasekey, $statement) = $this->_preparedStatements[$key];
                $this->_preparedStatements[$key] = $this->getDB($databasekey)->prepare($statement);
                $this->_preparedStatements[$key]->setFetchMode(PDO::FETCH_OBJ);
            }
            $this->_preparedStatements[$key]->execute($options);
            return $this->_preparedStatements[$key];
        }
        return null;
    }
    /**
     *  implements BLessPHP
     */
    public function getTagHandlers() {
        return array($this->_starttaghandler, $this->_rendertaghandler, $this->_finishtaghandler);
    }

    /**
     * 
     * @return Self
     */
    public function setVar($key, $value = null) {
        if (strpos($key, '$') === 0)
            $key = substr($key, 1);
        
        if (strpos($key, ' ') !== FALSE) {
            list($key, $value) = explode(' ', $key, 2);
        }
        $this->_vars[$key] = LessPHP::GetArrayVar(LessPHP::callFunc($value));
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVar($key, $ret = false) {
        if(is_object($key))
            return $ret ? $key : null;
        $key = strpos($key, '$') === 0 ? substr($key, 1) : $key;
        return isset($this->_vars[$key]) ? $this->_vars[$key] : ($ret ? $key : null);
    }
    
    /**
     * 
     * @return Self
     */
    public function setGlobalVar($key, $value = null) {
        if (strpos($key, '$') === 0)
            $key = substr($key, 1);
        
        if (strpos($key, ' ') !== FALSE) {
            list($key, $value) = explode(' ', $key, 2);
        }
        static::$_globalvars[$key] = LessPHP::GetArrayVar(LessPHP::callFunc($value));
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGlobalVar($key, $ret = false) {
        $key = strpos($key, '$') === 0 ? substr($key, 1) : $key;
        return isset(static::$_globalvars[$key]) ? static::$_globalvars[$key] : ($ret ? $key : null);
    }
    
    /**
     * return from Rendered FUNC
     */
    public function getReturn() {
        return $this->_renderReturn;
    }

    /**
     * 
     */
    public function setPage(&$page) {
        $this->_page = $page;
    }

    /**
     * 
     */
    public function setHeader($header) {
        header($header);
    }

    /**
     * 
     */
    public function getPage() {
        return $this->_page;
    }

    /**
     * Return a LoadedPage
     * @see phpQuery::pq()
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery|NULL|Array
     */
    public function getTemplate($key = null, $nice = true) {
        return isset($this->_templates[$key]) ? ($nice ? $this->_templates[$key][2] : $this->_templates[$key]) : null;
    }

    /**
     * 
     */
    public function getComponent($key = null, $nice = true) {
        return isset($this->_components[$key]) ? ($nice ? $this->_components[$key][2] : $this->_components[$key]) : null;
    }

    /**
     * 
     */
    public function getRoute() {
        return $this->_route;
    }

    /**
     * 
     */
    public function getRoot() {
        return $this->_root;
    }

    /**
     * 
     */
    public function __construct(ICompomnent &$root, $init = array()) {
        $this->_root = $root;
        $this->_init = $init;
        $this->_vars = array_merge($this->_init,$this->_vars);
        $this->_append = isset($init['appendTo']) ? $init['appendTo'] : static::APPEND;
        $this->_appendfunc = isset($init['appendFunc']) ? $init['appendFunc'] : static::ATYPE;
        $this->_page = $root->getPage();
        $this->_route = $root->getRoute();
        $this->Init();
    }

    /**
     * 
     */
    public function Init() {
        $this->_auto = static::AUTO;
        
        $this->isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])  && 
                    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        
        $this->_renderReturn = [];
        $this->_type = $this->isAjax ? "mixed" : static::TYPE;
        $oRefl = new ReflectionClass($this);
        $this->_less = new LessPHP($this, $oRefl);
    }

    /**
     * 
     */
    public $_auto = null;

    /**
     * 
     */
    public function setAuto($bo) {
        $this->_auto = $bo;
    }

    /**
     * 
     */
    public $_type = null;

    /**
     * @return Self
     */
    public function setReturn($bo) {
        $this->_type = strtolower($bo);
        return $this;
    }
    /**
     * 
     */
    public function getReturnType() {
        return $this->_type;
    }
    /**
     * 
     */
    public function using($namspace, $check = true) {
        Bit::using($namspace, $check);
    }

    /**
     * 
     */
    public static function _getComponent($namespace, $init = true) {
        Bit::using("App." . $namespace, false);
        $dir = Bit::getClassOfNamespace("App." . $namespace, false);
        //$lang = Bit::getPreferredLanguage();
        return $dir;
    }

    /**
     * @see phpQuery::pq()
     * @return phpQueryObject
     */
    public static function _getTemplate($namespace) {
        $dir = Bit::getPathOfNamespace("Themes.Html." . $namespace, 'html');
        $lang = Bit::getPreferredLanguage();

        if (!is_file($file = $dir['dirname'] . DS . $lang . DS . $dir['basename']))
            $file = $dir['dirname'] . DS . $dir['basename'];

        return isset($file) ? phpQuery::newDocumentFile($file) : NULL;
    }

    /**
     *  @return Self
     */
    public function LoadComponent($namespace,$append = null, $func = null , $key = null) {
        $h = explode(" ", LessPHP::callFunc($namespace), 4);
       
        if (count($h) == 1)
            $h[] = $append ? $append : static::APPEND ;
        if (count($h) == 2)
            $h[] = $func;
        if (count($h) == 3) 
            $h[] = $key ? $key : Terms::getMicroTimeFloat();
        list($namespaces, $append, $func,$key) = $h;
        $this->_components[$namespaces.$key] = array('0' => $append, '1' => $func, '2' => "Components.".$namespaces);
    
        return $this;
    }

    /**
     * @return Self
     */
    public function LoadTemplate($namespace) {
        $h = explode(" ", LessPHP::callFunc($namespace), 3);
        
        if (count($h) == 1)
            $h[] = $this->_append;
        if (count($h) == 2)
            $h[] = $this->_appendfunc;
        
        list($namespace, $append, $func) = $h;
        $t = $this->LoadMask($namespace);
        
        if ($t) {
            $this->_templates[$namespace] = array('0' => $append, '1' => $func, '2' => $t);
        } else
            throw new ToDoException("Load Template");

        return $this;
    }

    public static function LoadMask($namespace){
        $t = self::_getTemplate($namespace);
                
        foreach ($t->find('[data-template]') as $node) {
            $func = $node->__get('data-func');
            $func = $func ? $func : "append";
            $node->$func(self::LoadMask($node->__get('data-template')));
        }
        
        return $t;
    }
    
    /**
     * 
     */
    public function Render() {
        $render = $this->isAjax ? "Ajax" : static::RENDER;
        
        $func = $render . 'Index';
        $this->_less->run(LessPHP::RENDER);
        
        //Append Templates :D
        foreach ($this->_templates as $key => $v)
                $this->beforeRenderTemplate($key);
        
        try{
            if ($this->_type != 'void')
                $this->_renderReturn = $this->$func();
            else
                $this->$func();
            
        }catch(Exception $e){
            $this->doException($e);
        }
        
        $this->_less->run(LessPHP::FINISH);
        //XXX:IMPLEMENT CACHE + ActionsFunctions

        $this->Finish();
    }

    /**
     * 
     */
    public function Finish() {
            foreach ($this->_page->find('[data-component]') as $node) {
                $func = $node->__get('data-func');
                $func = $func ? $func : "append";
                $key = $node->__get('data-key');
                $key = $key ? $key : null;
                $this->LoadComponent($node->__get('data-component'),$node, $func,$key);
                $node->removeAttr('data-component');
            }
            foreach ($this->_components as $key => $v)
                $this->beforeComponent($key);
    }

    protected function getContent($append = null){
        static $h;
        
        if($append instanceof phpQueryObject)
            return $append;
        
        $append = $append ? $append : $this->_append;
        if(!isset($h[$append])) 
            $h[$append] = $this->_page->find($append);
        
        return $h[$append];
    }
    /*
     * 
     */
    public function beforeRenderTemplate($key = '') {
        list($append, $func, $t) = $this->getTemplate($key, false);
        
        if(isset($this->_vars['key']))
            $t->find('form')->attr('data-comp',$this->_vars['key']);
        $this->getContent($this->getVar($append, true))->$func($t);
    }

    function doException(Exception $ex) {
        
        $content = $this->getContent($this->_append);
        $content->empty();
        
        $content->append('<h1 class="border-left: 1px #fff dotted;">' . get_class($ex) . '</h1>');
        
        $trace = $ex->getTrace();
        if (isset($trace[0]['class'])){
            $reflection = new ReflectionMethod($trace[0]['class'], $trace[0]['function']);
            $content->append('<h5 class="border-left: 1px #fff dotted;"><b>Function : ' . $trace[0]['class'] . $trace[0]['type'] . $trace[0]['function'] . '()</h5>');
            $debug['title']= 'Function : ' . $trace[0]['class'] . $trace[0]['type'] . $trace[0]['function'] . '()';
        }
        elseif (isset($trace[0]['function']))
        {
            $reflection = new ReflectionFunction($trace[0]['function']);
            $content->append('<h5 class="border-left: 1px #fff dotted;"><b>Function : ' . $trace[0]['function'] . '()</h5>');
            $debug['title']= 'Function : ' . $trace[0]['function'] . '()';
        }
        $content->append('<h5 class="border-left: 1px #fff dotted;"><b>In File   : ' . $ex->getFile() . '@' . $ex->getLine() . '</h5>');
        $debug['file'] = $ex->getFile() . '@' . $ex->getLine();
        
        if ($ex instanceof PDOException){
            $content->append($ex->getMessage());
            $debug['message'] = ($ex->getMessage());
        }elseif ($ex instanceof PrintNiceException){
            $content->append($ex->getErrorMessage());
            $debug['message'] = ($ex->getErrorMessage());
        }else{
            $content->append($ex->getErrorMessage() . '');
            $debug['message'] = ($ex->getErrorMessage());
        }
        

        if (isset($trace[0])) {
            $content->append('<h2 class="border-left: 1px #fff dotted;"><b>Backtrace: ' . '</h3>');
            $content->append('<ol id="trace"></ol>');
            $ol = $content->find("ol#trace");
            foreach ($trace as $value) {
                if (isset($value['line'])) {
                    if (isset($value['class'])){
                        $debug['trace'][] = $value['class'] . $value['type'] . $value['function'];
                        $ol->append('<li>' . $value['class'] . $value['type'] . $value['function'] . '<br> <ul><li>in ' . (isset($value['file']) ? $value['file'] : __FILE__) . '@' . $value['line'] . '</li></ul></li>');
                    }else{
                        $debug['trace'][] = $value['function'];
                        $ol->append('<li>' . $value['function'] . '<br> <ul><li>in ' . (isset($value['file']) ? $value['file'] : __FILE__) . '@' . $value['line'] . '</li></ul></li>');
                    }
                }
            }
        }
        
        $this->_renderReturn = ['debug'=>$debug];
    }
    
    /**
     * Standart Rendering
     */
    public function beforeComponent($key = '') {
        list($append, $func, $ts) = $this->getComponent($key, false);

        $_t = $this->_getComponent($ts);
        $init = isset($this->_vars[$key]) ? $this->_vars[$key] : array();
        
        if($_t::NEEDINIT && !isset($this->_vars[$key]))
            throw new ToDoException('Make Default ErrorHandler');
        
        
        if (!isset($init['key']))
            $init['key'] = $key;
        if (!isset($init['appendTo']))
            $init['appendTo'] = $this->getVar($append, true);
        if (!isset($init['appendFunc']))
            $init['appendFunc'] = $this->getVar($func, true);
        
        $t = new $_t($this, $init);
        $t->Render();
        $this->_renderReturn['components'][$key] = $t->_renderReturn;
    }
    
    public static function getSite(){
        throw new ToDoException('Hmm');
    }
    
    
    /**
     * @return array the list of data in array
     */
    public function toArray() {
        return [];
    }

    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed the offset to check on
     * @return boolean
     */
    public function offsetExists($offset) {
        return true;
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @see phpQuery::pq()
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery|NULL|Array
     */
    public function offsetGet($offset) {
        return $this->_page->find($offset);
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param integer the offset to set element
     * @param mixed the element value
     */
    public function offsetSet($offset, $item) {
        $this->_page->find($offset)->html = $item;
    }

    /**
     * Unsets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed the offset to unset element
     */
    public function offsetUnset($offset) {
        $this->_page->find($offset)->remove();
    }
    
}
