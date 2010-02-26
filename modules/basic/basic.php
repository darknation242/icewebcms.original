<?php
//basic module
class basic
{

	function __construct()
	{
		global $modules;
		$modules->add_module('BASIC', 'Basic Module', 'Basic', '0.0.1', '/?m=BASIC');
	}

	public function ONLOAD()
	{
		//runs when module is loaded
        //don't print any thing here, as it would come before the headers. 
        //This is always loaded for every page, so dont do anything crazy here.
       }
	public function BASIC()
	{
		//runs when $modules->run('BASIC') is called.
	}
 }
?>