<?php

/**************************************************/
//Start the core.
//
//
/**************************************************/
require_once ('core.config.php');

//Starting up core config class
$config = new config;

require_once ('trinmangsdk/tmsdk.include.php');

//Starting up the realmdb class
$db_realmd = new conndb($config->core_config['realmd_host'], $config->core_config['realmd_port'], $config->core_config['realmd_user'], $config->core_config['realmd_pass'], $config->core_config['realmd_db']);

//Staring up accound class
$account = new account($db_realmd, "trinity");

//Get a list of realms
$realmd_id = $db_realmd->retrieve("SELECT `id`, `name`, `address`, `port`, `dbinfo` FROM `realmlist`");
$i = 1;
//$realmd contains an array of realm config info
$realmd = array();
//Looping to get config info for all realms
while(mysql_num_rows($realmd_id) > $i)
{
	$realm = mysql_fetch_array($realmd_id);
	$realmd[$realm['id']]['id'] = $realm['id'];
	$realmd[$realm['id']]['name'] = $realm['name'];
	$realmd[$realm['id']]['port'] = $realm['port'];
	$realmd[$realm['id']]['server'] = $realm['servers'];
	$temp = explode(';', $realm['dbinfo']);
	$realmd[$realm['id']]['mysql_user'] = $temp['0'];
	$realmd[$realm['id']]['mysql_pass'] = $temp['1'];
	$realmd[$realm['id']]['mysql_port'] = $temp['2'];
	$realmd[$realm['id']]['mysql_host'] = $temp['3'];
	$realmd[$realm['id']]['world'] = $temp['4'];
	$realmd[$realm['id']]['characters'] = $temp['5'];
	$realmd[$realm['id']]['worldd'] = new conndb($realmd[$realm['id']]['mysql_host'], $realmd[$realm['id']]['mysql_port'], $realmd[$realm['id']]['mysql_user'], $realmd[$realm['id']]['mysql_pass'], $realmd[$realm['id']]['world']);
	$realmd[$realm['id']]['chard'] = new conndb($realmd[$realm['id']]['mysql_host'], $realmd[$realm['id']]['mysql_port'], $realmd[$realm['id']]['mysql_user'], $realmd[$realm['id']]['mysql_pass'], $realmd[$realm['id']]['characters']);
	unset($temp);
	unset($realm);
	$i++;
}
$iceweb = array();
require_once ('iceweb.config.php');
//Starting up the site wide config class.
$iceweb['global'] = new iceweb_config;
//include and start smarty.
require_once ('Smarty.class.php');
$smarty = new Smarty;

//include and start the module loader.
require_once ('modules.core.php');
$modules = new modules();

?>