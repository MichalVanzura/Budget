<?php

require_once APPPATH."vendor/IMException.php";

### ALERTS

        const ALERT_SUCCESS = "success";
        const ALERT_INFO = "info";
        const ALERT_WARNING = "warning";
        const ALERT_DANGER = "danger";

class MY_Loader  extends CI_Loader  {
    
    public function __construct() {
        parent::__construct();
        
        $this->library("link");
    }
    
    private $navigation     = array();
    private $navigationLinks = array();
    
    public function addNavigation($url, $title, $index = null) {
        if (!isset($this->navigationLinks[$url])) {
            $item = array(
                "url" => $url,
                "title" => $title
            );
            
            if (is_null($index)) {
                $this->navigation[] = $item;
            } else {
                array_splice($this->navigation, $index, 0, array($item));
            }
            
            
            $this->navigationLinks[$url] = $title;
        }
    }
    
    ##### Header
    
    private $title = "";
    
    /**
     * Sets title to the page
     * @param type $title
     */
    public function title($title) {
        $this->title    = $title;
    }
    
    private $description    = "";
    
    /**
     * Sets description to the page
     * @param type $description
     */
    public function description($description) {
        $this->description    = $description;
    }
    
    private $menu = array();
    
    /**
     * Adds page to current menu
     * @param type $url url of page
     * @param type $title title of page
     * @param type $html for custom classes and etc
     * @param type $subPages an array of menuArray as subpages. Use menuArray to 
     * generate a page.
     */
    public function addLinkToMenu($url, $title, $html = "", $subPages = array(), $active = false) {
        
        // @todo: add automatic detection of active page?
        $this->menu[]   = $this->menuArray($url, $title, $html, $subPages, $active);
    }
    
    public function menuArray($url, $title, $html = "",$subPages = array(), $active) {
        return array(
            "url"       => $url,
            "title"     => $title,
            "html"      => $html,
            "subPages"  => $subPages,
            "active"    => $active
        );
    }
    
    ##### CSS
    
    
    /**
     * List of url to css files that are in header (based on template)
     * @var array
     */
    private $css                = array();
    
    public function css($fileName, $isAbsoluteLink = NULL) {
        $this->helper('url');
        
        $link       = "";
        
        if (!$isAbsoluteLink) {
            $link   .= base_url()."assets/css/";
        }
        
        $finalLink   = "$link$fileName";
        
        $this->css[] = $finalLink;
    }
    
    ###### Javascript
    
    /**
     * List of url to javascript files that are in header (based on template)
     * @var Array
     */
    private $headerJavascripts  = array();
    /**
     * List of url to javascript files that are at the end of html (based on template)
     * @var Array
     */
    private $bodyJavascript     = array();
    
    /***
     * Adds javascript file to be added to the template. If AbsoluteLink is true,
     * the js folder will not be added
     * 
     * @param string $fileName  fileName or url. If $isAbsoluteLink is false, the 
     *                          js folder is used
     * @param boolean $isAbsoluteLink defines if the fileName is url to javascript
     */
    public function javascript($fileName,$isAbsoluteLink = false,$addToBody = true) {
        $this->helper('url');
        
        $link       = "";
        
        if (!$isAbsoluteLink) {
            $link   .= base_url()."assets/js/";
        }
        
        $finalLink   = "$link$fileName";
        
        if ($addToBody) {
            $this->bodyJavascript[] = $finalLink;
        } else {
            $this->headerJavascripts[] = $finalLink;
        }
    }
    
    /**
     * Adds javascript file to the header javascripts array
     * 
     * @param string $fileName  fileName or url. If $isAbsoluteLink is false, the 
     *                          js folder is used
     * @param boolean $isAbsoluteLink defines if the fileName is url to javascript
     */
    public function headerJavascript($fileName,$isAbsoluteLink = false) {
        $this->javascript($fileName,$isAbsoluteLink,false);
    }
    
    /**
     * Prints the template content with header, defined template, and footer.
     * Header and footer is lcoated in global folder
     * 
     * @param string $template_name
     * @param array $vars
     * @param boolean $return should the content be returned
     * @return string|null
     */
    public function template($template_name, $vars = array(), $return = FALSE)
    {
        //$ci = &get_instance();
        
        // try to add current page to navigation. The funciton checks if exits
        $this->addNavigation(uri_string(), $this->title);
        
        $vars["bodyJavascripts"]        = $this->bodyJavascript;
        $vars["headerJavascripts"]      = $this->headerJavascripts;
        $vars["css"]                    = $this->css;
        $vars["title"]                  = $this->title;
        $vars["alerts"]                 = $this->alerts;
        
        $ci = &get_instance();
        // @todo: check if link menu or static menu
        $vars["menu"]                   = $ci->link;
        $vars["navigation"]             = $this->navigation;
        
        $vars["description"]            = $this->description;
        
        $content  = $this->view('global/header', $vars, $return);
        $content .= $this->view($template_name, $vars, $return);
        $content .= $this->view('global/footer', $vars, $return);

        if ($return)
        {
            return $content;
        }
        return null;
    }
    
    public function admintemplate($template_name, $vars = array(), $return = FALSE)
    {
        //$ci = &get_instance();
        
        // try to add current page to navigation. The funciton checks if exits
        $this->addNavigation(uri_string(), $this->title);
        
        $vars["bodyJavascripts"]        = $this->bodyJavascript;
        $vars["headerJavascripts"]      = $this->headerJavascripts;
        $vars["css"]                    = $this->css;
        $vars["title"]                  = $this->title;
        $vars["alerts"]                 = $this->alerts;
        
        $ci = &get_instance();
        // @todo: check if link menu or static menu
        $vars["menu"]                   = $ci->link;
        $vars["navigation"]             = $this->navigation;
        
        $vars["description"]            = $this->description;
        
        $content  = $this->view('admin/global/header', $vars, $return);
        $content .= $this->view($template_name, $vars, $return);
        $content .= $this->view('admin/global/footer', $vars, $return);

        if ($return)
        {
            return $content;
        }
        return null;
    }
    
    private $alerts     = array();
    
    /**
     * Add alert message into the view
     * @param type $message
     * @param type $class
     */
    public function addAlert($message,$class = ALERT_DANGER) {
        $this->alerts[] = array("message" => $message, "class" => $class);
    }
}
