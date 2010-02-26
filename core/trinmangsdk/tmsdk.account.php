<?php

require_once ("tmsdk.include.php");

/**
 * TMSDK class dedicated to manage the accounts.
 */
class account
{
	private $mysql;
	public $type;

	/**
	 * Class constructer.
	 * @param $account_db_conn the conndb class variable linked to the realmd database
	 * @param $type the type of server, either 'trinity' or 'mangos'. Currently no support for both on same realmd.
	 */
	public function __construct($account_db_conn, $type)
	{
		$this->mysql = $account_db_conn;
		$this->type = $type;
	}

	/**
	 * Boolean, ban an account.
	 * If nothing went wrong returns true.
	 * @param $id the account's ID
	 * @param $time the ammount of time (in seconds) the account has to be banned
	 * @param $bannedby the GM who has banned the account (default void)
	 * @param $reason the reason why the account has been banned (default void)
	 */
	public function ban($id, $time, $bannedby = "", $reason = "")
	{
		$id = mysql_real_escape_string($id);
		$bandate = time();
		$unbandate = $bandate + $time;
		$bannedby = mysql_real_escape_string($bannedby);
		$reason = mysql_real_escape_string($reason);
		$this->mysql->send("INSERT INTO `account_banned` (id,bandate,unbandate,bannedby,banreason,active) VALUES ('$id','$bandate','$unbandate','$bannedby','$reason','1')");
		return true;
	}

	/**
	 * String, returns the email of an account.
	 * @param $id the account's ID
	 */
	public function getEmail($id)
	{
		$id = mysql_real_escape_string($id);
		$sql = $this->mysql->retrieve("SELECT `email` FROM `account` WHERE `id` = '$id' LIMIT 1");
		$row = mysql_fetch_array($sql);
		return $row['email'];
	}

	/**
	 * Number, returns the number of expansions the account is enabled to use.
	 * 0 = Classic, 1 = TBC, 2 = WotLK.
	 * @param $id the account's ID
	 */
	public function getExpansion($id)
	{
		$id = mysql_real_escape_string($id);
		$sql = $this->mysql->retrieve("SELECT `expansion` FROM `account` WHERE `id` = '$id' LIMIT 1");
		$row = mysql_fetch_array($sql);
		return $row['expansion'];
	}

	/**
	 * Number, returns the account's GM level.
	 * 0 = no GM rights, >0 GM rights.
	 * @param $id the account's ID
	 */
	public function getGmLevel($id)
	{
		if ($this->type == "mangos")
		{
			$id = mysql_real_escape_string($id);
			$sql = $this->mysql->retrieve("SELECT `gmlevel` FROM `account` WHERE `id` = '$id' LIMIT 1");
			$row = mysql_fetch_array($sql);
			return $row['gmlevel'];
		} elseif ($this->type == "trinity")
		{
			$id = mysql_real_escape_string($id);
			$sql = $this->mysql->retrieve("SELECT `gmlevel` FROM `account_access` WHERE `id` = '$id' LIMIT 1");
			$row = mysql_fetch_array($sql);
			return $row['gmlevel'];
		}
	}

	/**
	 * Number, returns the account's ID.
	 * @param $username the account's name
	 */
	public function getId($username)
	{
		$username = mysql_real_escape_string($username);
		$sql = $this->mysql->retrieve("SELECT `id` FROM `account` WHERE `username` = '$username' LIMIT 1");
		$row = mysql_fetch_array($sql);
		return $row['id'];
	}

	/**
	 * Number, returns the account's ban status.
	 * 0 = not banned, 1 = banned
	 * @param $ip the IP to be checked
	 */
	public function getIPBanStatus($ip)
	{
		$ip = mysql_real_escape_string($ip);
		$date = time();
		$sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `ip_banned` WHERE `ip` = '$ip' AND `unbandate` > '$date' OR `ip` = '$ip' AND `bandate` = `unbandate` LIMIT 1");
		$row = mysql_fetch_array($sql);
		return $row['count'];
	}

	/**
	 * Number, returns the number of online players.
	 */
	public function getNumAccountsOnline()
	{
		$sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `account` WHERE `online` = '1'");
		$row = mysql_fetch_array($sql);
		return intval($row['count']);
	}

	/**
	 * String, returns the account's username.
	 * @param $id the account's ID
	 */
	public function getUsername($id)
	{
		$id = mysql_real_escape_string($id);
		$sql = $this->mysql->retrieve("SELECT `username` FROM `account` WHERE `id` = '$id' LIMIT 1");
		$row = mysql_fetch_array($sql);
		return $row['username'];
	}


	/**
	 * Boolean, binds an account to an IP (ie bind to 127.0.0.1 to ban).
	 * Returns true if nothing went wrong.
	 * @param $id the account's ID
	 * @param $ip the IP the account has to be binded to
	 */
	public function lock($id, $ip)
	{
		$id = mysql_real_escape_string($id);
		$ip = mysql_real_escape_string($ip);
		$this->mysql->send("UPDATE `account` SET `locked` = '1', `last_ip` = '$ip' WHERE `id` = '$id'");
		return true;
	}

	/**
	 * Number, simple login function.
	 * 1 = successfull login, not banned; 2 = banned, 0 = wrong login.
	 * @param $user the username of the account
	 * @param $pass the password of the account
	 */
	public function login($user, $pass)
	{
		$user = mysql_real_escape_string($user);
		$pass = mysql_real_escape_string($pass);

		$user = strtoupper($user);
		$pass = strtoupper($pass);
		$pass_hash = SHA1($user . ':' . $pass);

		$sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count`,`id` FROM `account` WHERE `username` = '" . $user . "' AND `sha_pass_hash` = '" . $pass_hash . "' GROUP BY id LIMIT 1");
		$row = mysql_fetch_array($sql);
		$count = $row['count'];
		$id = $row['id'];

		$sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `account_banned` WHERE `id` = '" . $id . "' AND `active` = '1' LIMIT 1");
		$row = mysql_fetch_array($sql);

		if ($row['count'] > 0)
			return 2;
		if ($count > 0)
			return 1;
		return 0;
	}

	/**
	 * Register an account.
	 * Returns 0 if the password or password hash is too long.
	 * Returns 1 if it was successful.
	 * Returns 2 if the IP is banned
	 * Returns 3 if the username or email was taken
	 * @param $user the desired username
	 * @param $pass the desired password
	 * @param $email the email the account will be binded to (one account per email) (default void)
	 * @param $ip the IP with which the account is registered (usefull to take trace of register spammers) (default void)
	 * @param $expansion the number of expansions the account is enabled to use (default 0, WoW Classic)
	 * @param $id the ID to give the account (default Auto Increment)
	 */
	public function register($user, $pass, $email = "", $ip = "", $expansion = 0, $id = "")
	{
		$user = mysql_real_escape_string($user);
		$pass = mysql_real_escape_string($pass);
		$email = mysql_real_escape_string($email);
		$ip = mysql_real_escape_string($ip);
		$expansion = mysql_real_escape_string($expansion);
		$id = mysql_real_escape_string($id);

		if (strlen($pass) > 16)
			return 0;

		$user = strtoupper($user);
		$pass = strtoupper($pass);
		$pass_hash = SHA1($user . ':' . $pass);

		if (strlen($pass_hash) > 40)
			return 0;

		$query = "SELECT COUNT(*) AS `count` FROM `account` WHERE `username` = '$user'";
		if ($email != "")
			$query .= " OR `email` = '$email'";
		$sql = $this->mysql->retrieve($query);
		$row = mysql_fetch_array($sql);
		$count = $row['count'];

		if ($count > 0)
			return 3;

		$chk = $this->getIPBanStatus($ip);
		if (!empty($ip) && $chk == 1)
			return 2;

		if (!empty($id))
		{
			$this->mysql->send("INSERT INTO `account` (id,username,sha_pass_hash,email,last_ip,expansion) VALUES ('$id','$user','$pass_hash','$email','$ip','$expansion')");
		} else
		{
			$this->mysql->send("INSERT INTO `account` (username,sha_pass_hash,email,last_ip,expansion) VALUES ('$user','$pass_hash','$email','$ip','$expansion')");
		}
		return 1;
	}

	/**
	 * Sets the GM level of an account.
	 * @param $id the account's ID
	 * @param $level the desired level
	 */
	public function setGmLevel($id, $level)
	{
		if ($this->type == "mangos")
		{
			$id = mysql_real_escape_string($id);
			$level = mysql_real_escape_string($level);
			$this->mysql->retrieve("UPDATE `account` SET `gmlevel` = '$level' WHERE `id` = '$id' LIMIT 1");
			return true;
		} elseif ($this->type == "trinity")
		{
			$id = mysql_real_escape_string($id);
			$level = mysql_real_escape_string($level);
			$this->mysql->retrieve("UPDATE `account_access` SET `gmlevel` = '$level' WHERE `id` = '$id' LIMIT 1");
			return true;
		}


	}

	/**
	 * Boolean, sets the email of an account.
	 * Returns false if the email has just been taken.
	 * @param $id the account's ID
	 * @param $newemail the new email
	 */
	public function setEmail($id, $newemail)
	{
		$id = mysql_real_escape_string($id);
		$newemail = mysql_real_escape_string($newemail);
		$sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `account` WHERE `email` = '$newemail'");
		$row = mysql_fetch_array($sql);

		if ($row['count'] > 0)
			return false;
		$this->mysql->send("UPDATE `account` SET `email` = '$newemail' WHERE `id` = $id");
		return true;
	}

	/**
	 * Boolean, sets the number of expansions an account is able to use.
	 * Returns true if all the things went good.
	 * @param $id the account's ID
	 * @param $nexp the number of expansions an account is able to use.
	 */
	public function setExpansion($id, $nexp)
	{
		$id = mysql_real_escape_string($id);
		$nexp = mysql_real_escape_string($nexp);
		$this->mysql->send("UPDATE `account` SET `expansion` = '$nexp' WHERE `id` = $id");
		return true;
	}

	/**
	 * Sets a new password for an account.
	 * Returns false if the password is too long.
	 * @param $id the account's ID
	 * @param $newpass the new password
	 */
	public function setPassword($id, $newpass)
	{
		$id = mysql_real_escape_string($id);
		$newpass = mysql_real_escape_string($newpass);

		if (strlen($newpass) > 16)
			return false;

		$sql = $this->mysql->retrieve("SELECT `username` FROM `account` WHERE `id` = '$id' LIMIT 1");
		$row = mysql_fetch_array($sql);

		$pass_hash = SHA1(strtoupper($row['username'] . ":" . strtoupper($newpass)));

		if (strlen($pass_hash) > 40)
			return false;

		$this->mysql->send("UPDATE `account` SET `sha_pass_hash` = '$pass_hash', `v` = 0, `s` = 0 WHERE `id` = '$id' LIMIT 1");
		return true;
	}

	/**
	 * Boolean, unbans an account.
	 * Returns true if all went good.
	 * @param $id the account's ID
	 */
	public function unban($id)
	{
		$id = mysql_real_escape_string($id);
		$this->mysql->send("DELETE FROM `account_banned` WHERE `id` = '$id'");
		return true;
	}

	/**
	 * Boolean, unbinds an account from any IP.
	 * Returns true if all went good.
	 * @param $id the account's ID
	 */
	public function unlock($id)
	{
		$id = mysql_real_escape_string($id);
		$this->mysql->send("UPDATE `account` SET `locked` = '0' WHERE `id` = '$id'");
		return true;
	}
}

?>
