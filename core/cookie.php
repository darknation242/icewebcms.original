<?php
//check, change or set the current realm cookie
if (isset($_POST['changerealm_to']))
{
	setcookie("realmd", intval($_POST['changerealm_to']), time() + (3600 * 24));
} elseif (!isset($_COOKIE['realmd'])) {
	$iceweb['user']['cur_selected_realmd'] = "";
	setcookie("realmd", $iceweb['user']['realmd'], time() + (3600 * 24));
}
$iceweb['user']['realmd'] = intval($_COOKIE['cur_selected_realmd']);

//check to see if the use is logged in.
if(isset($_COOKIE['account'])){
    $user_salt = $db_realmd->retrieve("SELECT `user`, `sha_pass_hash` FROM `account`");
    $user_salt = mysql_fetch_array($user_salt);
    if(sha1($_COOKIE['account'].$_COOKIE['password']) == sha1($user_salt['user'].$user_salt['sha_pass_hash'])){
        $iceweb['user']['account'] = $_COOKIE['account'];
        $iceweb['user']['log_in'] = 'false';
    }else{
        setcookie('account',"",time() - 3600);
        setcookie('account',"",time() - 3600);
        $iceweb['user']['account'] = 'guest';
        $iceweb['user']['log_in'] = 'false';
    }
}else{
    $iceweb['user']['account'] = 'guest';
    $iceweb['user']['log_in'] = 'false';
}
?>