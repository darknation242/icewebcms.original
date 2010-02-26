<?php

class template
{
	public $module;
	public $template_root;
	public function __construct($module, $root = null)
	{
	   global $config;
		$this->module = $module;
		if ($root == "")
		{
			$this->template_root = $config->template_config['root']."modules/" . $module . "/";
		} else
		{
			$this->template_root = $config->template_config['root'] . $root;
		}

	}
	public function loadiwt($name, $vars = null)
	{
		include ($this->template_root . $name . ".iwt");
		$file = new $name;
		if ($file->dynamic)
		{
			foreach ($vars as $foo)
			{
				$key = array_search($foo, $vars);
				$file->content = str_replace("{" . $key . "}", $foo, $file->content);
			}
			return $file->content;
		} else
		{
			return $file->content;
		}
	}

}
