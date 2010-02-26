<?php
class iceweb_config{
    public $config = array();
    
    public function __construct(){
        //need some sort of config file loader.
        //for no they are hard coded.
        $this->config['site_title'] = "IceWeb CMS";
    } 
}

?>