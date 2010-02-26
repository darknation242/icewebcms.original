<?php

class modules
{
	public $ar_modules = array();
	private $loadmods = array();
	function __construct()
	{
		global $modules,$config;
		$modules = $this;
		$dir = $config->modules_config['root'];
		$len = strlen($dir);
		if ($dir[$len - 1] != "/")
		{
			$dir .= "/";
		}
		$loadmods = array();
		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if (filetype($dir . $file) == 'dir')
					{
						if (($file != '.') || ($file != '..'))
						{
							array_push($loadmods, $file);
						}
					}
				}
				closedir($dh);
			}
		}
		for ($y = 0; $y <= 6; $y++)
		{
			for ($i = 0; $i < count($loadmods); $i++)
			{
				$loadmods[$i] = str_replace(".php", "", $loadmods[$i]);
				$loadmods[$i] = str_replace($dir, "", $loadmods[$i]);
				if (file_exists("$dir/{$loadmods[$i]}/{$loadmods[$i]}.php"))
				{
					$upper = strtoupper($loadmods[$i]);
					if (file_exists("$dir/{$loadmods[$i]}/level_$y"))
					{
						if (!isset($modules->ar_modules[$upper]))
						{
							if (!class_exists($upper))
							{
								include "$dir/{$loadmods[$i]}/{$loadmods[$i]}.php";
							}
							$$loadmods[$i] = new $upper();
							$modules->add_obj($upper, $$loadmods[$i]);
							$this->run('ONLOAD', $upper);
						}
					} elseif ($y == 6)
					{
						if (!isset($modules->ar_modules[$upper]))
						{
							if (!class_exists($upper))
							{
								include "$dir/{$loadmods[$i]}/{$loadmods[$i]}.php";
							}
							$$loadmods[$i] = new $upper();
							$modules->add_obj($upper, $$loadmods[$i]);
							$this->run('ONLOAD', $upper);
						}
					}


				}
			}
		}


	}

	public function add_module($shrt, $desc, $name, $version, $url)
	{
		global $modules;
		$modules->ar_modules[$shrt]['name'] = $name;
		$modules->ar_modules[$shrt]['desc'] = $desc;
		$modules->ar_modules[$shrt]['version'] = $version;
		$modules->ar_modules[$shrt]['url'] = $url;
		$modules->ar_modules[$shrt]['obj'] = null;
	}
	public function run($event, $module = null)
	{
		global $DMBot, $modules;
		$ar_mods = $modules->ar_modules;
		if ($module == null)
		{
			while (list($key, $val) = each($ar_mods))
			{
				if (method_exists($modules->ar_modules[$key]['obj'], $event))
				{
					$modules->ar_modules[$key]['obj']->$event();
				}
			}
		} else
		{
			if (method_exists($module, $event))
			{
				$modules->ar_modules[$module]['obj']->$event();
			}
		}

	}
	public function add_obj($shrt, $obj)
	{
		global $modules;
		$modules->ar_modules[$shrt]['obj'] = $obj;
	}
}

?>