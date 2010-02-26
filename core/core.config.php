<?php

/**
 * config
 * 
 * @package IceWebCMS
 * @author ionstorm66
 * @copyright 2010
 * @version $Id$
 * @access public
 */
class config
{
	public $core_config = array();
    public $template_config = array();
    public $modules_config = array();
	/**
	 * config::__construct()
	 */
	public function __construct()
	{
		$this->core_config['root'] = $_SERVER['DOCUMENT_ROOT']."/icewebcms";
		$this->core_config['type'] = 'trinity';
		$this->core_config['realmd_db'] = 'trinity1_realmd';
		$this->core_config['realmd_host'] = '127.0.0.1';
		$this->core_config['realmd_port'] = '3306';
		$this->core_config['realmd_user'] = 'trinity1';
		$this->core_config['realmd_pass'] = 'mEZGtGx5SthYSBPa';
		$this->core_config['salt'] = sha1($core_config['realmd_pass'] . $core_config['realmd_user']);
        
        $this->template_config['root'] = $this->core_config['root']."/templates/";
        
        $this->modules_config['root'] = $this->core_config['root']."/modules/";
	}
}

?>