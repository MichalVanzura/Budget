<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Link{
    
    protected $title = "";
    protected $html;
    protected $url;
    protected $class;
    protected $pages = array();
    protected $active;
    protected $index;
    protected $baseUrl;
    
     public function __construct($url = "", $title = "", $html = "", $active = false, $class = null, $index = 0, $baseUrl = "") {
         
         $this->baseUrl     = $url;
         $this->title       = $title;
         $this->html        = $html;
         $this->url         = $baseUrl.($index == 1 ? "" : "/").$url;
         $this->class       = $class;
         $this->index       = $index;
         
         $this->active = $active;
         
         if (($active || $index==1) && $this->createNaviationForUrl($url, $title, $index)) {
             $this->active = true;
         }
         
         return $this;
    }
    /**
     * 
     * @param type $url
     * @param type $title
     * @param type $html
     * @param type $active
     * @param type $class
     * @param type $pages
     * @return \Link
     */
    public function addPage($url, $title, $html = "" , $active = false, $class = "", $pages = array()){
        
        
        $parentUrl   = $this->getUrl();
        
        if ($parentUrl == "/") {
            $parentUrl = "";
        } else {
            $lastChar = substr($parentUrl, -1);

            if ($lastChar != "/") {
                $parentUrl .= "/";
            }
        }
        
        $page = $this->createPage($url, $title, $html, $this->active, $class, $pages, ($this->index+1), $this->baseUrl);
        
        $this->pages[] = $page;
        return $page;
        //return new Link($title, $html, $url, $active, $class, $pages);
    }
    /**
     * If given url is same in current page index, it will create navigation
     * @param type $url
     * @param type $title
     * @return boolean if active
     */
    private function createNaviationForUrl($url, $title, $index, $aliasToAdd = null) {
        $ci = & get_instance();
        
        $currentUrl     = $ci->uri->segment($index);
        
        if ($currentUrl !== false && $url == $currentUrl) {
            $ci->load->addNavigation((is_null($aliasToAdd) ? $this->url : $this->baseUrl."/".$aliasToAdd), $title);
            return true;
        }
        return false;
    }
    
    public function setAlias($url) {
        
        $this->createNaviationForUrl($url, $this->title, $this->index);
        return $this;
    }
    
    /**
     * Used to create hidden page only for current navigation
     * @param type $url
     * @param type $title
     */
    public function addHiddenPage($url, $title, $aliasToAdd = null) {
        $this->createNaviationForUrl($url, $title, $this->index+1, $aliasToAdd);
        return $this;
    }
    
    public function createPage($url, $title, $html , $active, $class, $pages, $index, $parentUrl){
        $new = new Link($url, $title, $html, $active, $class, $index, $parentUrl);
        $new->setPages($pages);
        
        return $new;
    }
    
    public function getPages(){
        return $this->pages; 
    }
    
    public function getTitle() {
        return $this->title;
    }

    public function getClass() {
        return $this->class;
    }

    public function getActive() {
        return $this->active;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function setClass($class) {
        $this->class = $class;
        return $this;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }
    
    public function setPages($pages){
        $this->pages = $pages;
        return $this;
    }

    public function getHtml(){
        
    }
    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }


   
    
}

