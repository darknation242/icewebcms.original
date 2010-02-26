<?php

class main
{
    
private $main;

	function __construct()
	{
		global $modules;
		$modules->add_module('MAIN', 'Main Page', 'Main', '0.0.1', '/?m=MAIN');
	}

	public function ONLOAD()
	{
		//runs when module is loaded
       }
	public function MAIN()
	{
		echo("HAX");
		//runs when $module is called.
	}
 }
?>