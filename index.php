<?php
require_once("core/core.int.php");

//load the cookie stuffs
require_once("core/cookie.php");

$smarty->assign('title', $iceweb['global']->config['site_title']);
$smarty->display('header.tpl');
$smarty->assign("test",$modules->ar_modules);
$smarty->display('top_bar.tpl');

if(isset($_GET['p'])){
	$modules->run($_GET['p']);
}else{    
	$modules->run('MAIN');
}

$smarty->display('footer.tpl');
?>
