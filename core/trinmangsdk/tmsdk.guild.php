<?php
require_once("tmsdk.include.php");

/**
  This class handles actions related to guilds
*/
class guild
{
    private $mysql;

    /**
      Class constructer.
      @param $char_db_conn the conndb class variable linked to the character database
    */
    public function __construct($char_db_conn)
    {
        $this->mysql = $char_db_conn;
    }

    /**
      Returns the ID of the guild
      @param $name the name of the guild
    */
    public function getGuildId($name)
    {
        $name = mysql_real_escape_string($name);
        $sql = $this->mysql->retrieve("SELECT `guildid` FROM `guild` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['guildid'];
    }

    /**
      Returns the GUID of the leader of a guild
      @param $id the ID of the guild
    */
    public function getGuildLeader($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `leaderguid` FROM `guild` WHERE `guildid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['leaderguid'];
    }

    /**
      Returns an array with a list of guilds
      Below, i is an integer that starts at 0 and consecutively
      increases by 1 for each guild.
      Returns result[i]['id'/'name'/'leaderid']
    */
    public function getGuildList()
    {
        $sql = $this->mysql->retrieve("SELECT `guildid`,`name`,`leaderguid` FROM `guild`");
        $i = 0;
        while($row = mysql_fetch_array($sql))
        {
            $result[$i]['id'] = $row['guildid'];
            $result[$i]['name'] = $row['name'];
            $result[$i]['leaderid'] = $row['leaderguid'];
            $i++;
        }
        return $result;
    }

    /**
      Returns the name of the guild
      @param $id the ID of the guild
    */
    public function getGuildName($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `name` FROM `guild` WHERE `guildid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['name'];
    }

    /**
      Sets the leader of a guild
      @param $guildid the ID of the guild
      @param $leaderid the GUID of the new leader
    */
    public function setGuildLeader($guildid,$leaderid)
    {
        $guild = mysql_real_escape_string($guildid);
        $leader = mysql_real_escape_string($leaderid);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count`, `guildid` FROM `guild_member` WHERE `guid` = '$leader'");
        $row = mysql_fetch_array($sql);
        if($row['count'] < 1 || $row['guildid'] != $guild) return false;

        $this->mysql->send("UPDATE `guild` SET `leaderguid` = '$leader' WHERE `guildid` = '$guild' LIMIT 1");
        return true;
    }

    /**
      Sets the name of a guild
      @param $guildid the ID of the guild
      @param $guildname the new name of the guild
    */
    public function setGuildName($guildid, $guildname)
    {
        $id = mysql_real_escape_string($guildid);
        $name = mysql_real_escape_string($guildname);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `guild` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;

        $this->mysql->send("UPDATE `guild` SET `name` = '$name' WHERE `guildid` = '$id' LIMIT 1");
        return true;
    }
}

?>