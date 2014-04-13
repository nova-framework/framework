<?php

class Bootstrap {

    protected $_url;
    private $_controller = NULL;
    private $_defaultController;

    public function __construct(){
        //start the session class
        Session::init();

        //sets the protected url
        $this->_getUrl();
    }

    public function setController($name){
        $this->_defaultController = $name; 
    }

    public function setTemplate($template){
       Session::set('template',$template);
    }

    public function init(){

        //if no page requested set default controller
        if(empty($this->_url[0])){
            $this->_loadDefaultController();
            return false;
        }

        $this->_loadExistingController();
        $this->_callControllerMethod();

    }

    protected function _getUrl(){
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : NULL;
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $this->_url = explode('/',$url);
    }

    protected function _loadDefaultController(){
        require 'controllers/'.$this->_defaultController.'.php';
        $this->_controller = new $this->_defaultController();
        $this->_controller->index();
    }

    protected function _loadExistingController(){

        //set url for controllers
        $file = 'controllers/'.$this->_url[0].'.php';

        if(file_exists($file)){
            require $file;

            //instatiate controller
            $this->_controller = new $this->_url[0];


        } else {
            $this->_error("File does not exist: ".$this->_url[0]);
            return false;
        }

    }

    /**
     * If a method is passed in the GET url paremter
     * 
     *  http://localhost/controller/method/(param)/(param)/(param)
     *  url[0] = Controller
     *  url[1] = Method
     *  url[2] = Param
     *  url[3] = Param
     *  url[4] = Param
     */
    protected function _callControllerMethod()
    {
        $length = count($this->_url);
        
        // Make sure the method we are calling exists
        if ($length > 1) {
            if (!method_exists($this->_controller, $this->_url[1])) {
                $this->_error("Method does not exist: ".$this->_url[1]);
                return false;
            }
        }

        
        // Determine what to load
        switch ($length) {
            case 5:
                //Controller->Method(Param1, Param2, Param3)
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3], $this->_url[4]);
                break;
            
            case 4:
                //Controller->Method(Param1, Param2)
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3]);
                break;
            
            case 3:
                //Controller->Method(Param1)
                $this->_controller->{$this->_url[1]}($this->_url[2]);
                break;
            
            case 2:
                //Controller->Method()
                $this->_controller->{$this->_url[1]}();
                break;
            
            default:
                $this->_controller->index();
                break;
        }
    }
    
    /**
     * Display an error page if nothing exists
     * 
     * @return boolean
     */
    protected function _error($error) {
        require 'app/core/error.php';
        $this->_controller = new Error($error);
        $this->_controller->index();
        die;
    }



}
