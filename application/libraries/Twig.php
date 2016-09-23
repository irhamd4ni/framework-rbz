<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Twig Environment
 */
class Twig {

    private $CI;
    private $_twig;
    private $_template_dir;
    private $_cache_dir;
    
    /**
     * 
     * The Constructor twig init
     * @param boolean $debug 
     * @author Panji
     */
    public function __construct($debug = false)
    {
        $this->CI =& get_instance();
        
        log_message('debug', "Twig Autoloader Loaded");
        
        Twig_Autoloader::register();

        $this->_template_dir = APPPATH . "views";
        $this->_cache_dir = APPPATH . "cache/twig";
        
        $loader = new Twig_Loader_Filesystem($this->_template_dir);
        
        $this->_twig = new Twig_Environment($loader, array(
                        'cache' => false,
                        'auto_reload' => true,
                        'debug' => $debug,
        ));

        foreach(get_defined_functions() as $functions) {
            foreach($functions as $function) {
                $this->_twig->addFunction($function, new Twig_SimpleFunction($function, $function));
            }
        }

        $this->add_function('site_url');
        $this->add_function('base_url');
        $this->add_function('get_user_info');
        
    }

    public function add_function($name, $funct = null)
    {
        $this->_twig->addFunction($name, new Twig_SimpleFunction($name, ($funct === null ? $name : $funct)));
    }

    
    /**
     * Render Twig Template
     *
     * @author Panji
     * 
     * @param  string $template 
     * @param  array  $data     
     * @return string           
     */
    public function render($template, $data = array())
    {
        $template = $this->_twig->loadTemplate($template);
        return $template->render($data);
    }

    /**
     * Render twig
     *
     * @author Panji
     * 
     * @param  string $template
     * @param  array[]  $data
     * @return string
     */
    public function display($template, $data = array())
    {
        $template = $this->_twig->loadTemplate($template);
        $data['elapsed_time'] = $this->CI->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
        $memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2) . 'MB';
        $data['memory_usage'] = $memory;
        $template->display($data);
    }
}