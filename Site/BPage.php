<?php

abstract class BPage extends Component implements IPage {

    private $_viewport = null;
    private $_lessmethod = null;
    protected $_wrapper = 'HTML';
    protected $_t = 'main';

    public function getWrapper() {
        return $this->_wrapper;
    }

    public function setWrapper($bo) {
        $this->_wrapper = $bo;
    }

    public function getWrapperTemplate() {
        return $this->_t;
    }

    public function setWrapperTemplate($bo) {
        $this->_t = $bo;
    }

    protected function CheckRoute(&$r,&$isFinal = true){
        $render = $this->isAjax ? "Ajax" : static::RENDER;
        
        $func = isset($r["values"]['view']) ? $render . $r["values"]['view'] : $render . 'Index';
        try {
            $viewport = new ReflectionMethod(get_class($this), $func);
        } catch (Exception $e) {
            $isFinal = false;
            $viewport = new ReflectionMethod(get_class($this), $render.'Error');
        }
        $viewport->setAccessible(true);
        return $viewport;
    }
    public function Init() {
        $this->_starttaghandler = array_merge($this->_starttaghandler, array('wrapper' => 'setWrapper', 'wtemplate' => 'setWrapperTemplate'));
        $this->_rendertaghandler = array_merge($this->_rendertaghandler, array('title' => 'addTitle'));
        $this->_finishtaghandler = array_merge($this->_finishtaghandler, array('css' => 'addCss'));
        
        parent::Init();
        
        $isFinal = null;
        $viewport = $this->CheckRoute($this->_route, $isFinal);
        if ($viewport instanceof ReflectionMethod)
            if ((($isFinal && $viewport->isFinal()) || !$isFinal) && $viewport->isProtected() && $viewport->getDocComment() !== false) {
                $this->_lessmethod = new LessPHP($this, $viewport);
                $this->_viewport = $viewport;
            } else {
                throw new SecurityException('View Function');
            }
    }

    /**
     * @return string page title.
     */
    public function getTitle() {
        throw new ToDoException("");
    }

    /**
     * Clear Title and Set new One Be CareFull
     * @param string the new Title
     * @return void
     */
    public function setTitle($title) {
        $this->getContent('title')->html($title);
    }

    /**
     * Append Title at the End
     * @param string $title the new Title
     * @param string $key Delemiter default  »
     * @return void
     */
    public function addTitle($title, $key = ' » ',$func = 'append') {
        $title = LessPHP::callFunc($title);
        $this->getContent('title')->$func($func === 'append' ? $key . $title : $title.$key );
    }

    /**
     * Returns the head Reference
     * Nice to use to Manipulate the Meta Tags
     * @return phpQueryObject(HEAD)
     */
    public function Head() {
        return $this->getContent('head');
    }

    /**
     * Append Css or CssLink at the End of Header
     * @param string $css simpel css or
     * @return void
     */
    public function addCss($css) {
        if (strpos($css, '{') === false)
            $this->Head()->append('<link rel="stylesheet" href="' . $css . '" />');
        else
            $this->Head()->append('<style>' . $css . '</style>');
    }

    /* Standart Page Rendering */

    public function Render() {
        $this->_less->run(LessPHP::RENDER);
        
        //Append Templates :D
        foreach ($this->_templates as $key => $v)
            $this->beforeRenderTemplate($key);
        
        if (!is_null($this->_viewport)) {
            $func = $this->_viewport;
            if ($this->_lessmethod instanceof LessPHP) {
                $this->_lessmethod->run(LessPHP::RENDER);
                
                try{
                    if ($this->_type != 'void')
                        $this->_renderReturn['self'] = $func->invoke($this);
                    else
                        $func->invoke($this);
                }catch(Exception $e){
                    $this->doException($e);
                }
                $this->_lessmethod->run(LessPHP::FINISH);
            }
            $this->_less->run(LessPHP::FINISH);
            $this->Finish();
        } else {
            $this->ViewError();
        }
    }

    protected function AjaxError() {
        throw new ToDoException("Make a AjaxError Handler. Maybe Check your route");
    }

    
    protected function ViewError() {
        throw new ToDoException("Make a ViewError or a ViewIndex Handler . Maybe Check your route");
    }

}