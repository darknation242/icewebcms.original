<?php
require_once("tmsdk.include.php");

/**
  This class handles actions related to arena teams
*/
class arenateam
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
      Returns the GUID of the captain of the arena team.
      @param $id the ID of the guild
    */
    public function getATCaptain($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `captainguid` FROM `arena_team` WHERE `arenateamid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['captainguid'];
    }

    /**
      Returns the ID of the arena team.
      @param $name the name of the guild
    */
    public function getATId($name)
    {
        $name = mysql_real_escape_string($name);
        $sql = $this->mysql->retrieve("SELECT `arenateamid` FROM `arena_team` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['arenateamid'];
    }

    /**
      Returns an array with a list of arena teams
      Below, i is an integer that starts at 0 and consecutively
      increases by 1 for each arenateams.
      Returns result[i]['id'/'name'/'leaderid']
    */
    public function getATList()
    {
        $sql = $this->mysql->retrieve("SELECT `arenateamid`,`name`,`captainguid` FROM `arena_team`");
        $i = 0;
        while($row = mysql_fetch_array($sql))
        {
            $result[$i]['id'] = $row['arenateamid'];
            $result[$i]['name'] = $row['name'];
            $result[$i]['leaderid'] = $row['captainguid'];
            $i++;
        }
        return $result;
    }

    /**
      Returns the name of the arena team.
      @param $id the ID of the guild
    */
    public function getATName($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT `name` FROM `arena_team` WHERE `arenateamid` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['name'];
    }

    /**
      Sets the captaion of an arena team
      @param $id the ID of the arena team
      @param $leaderid the GUID of the new captain
    */
    public function setATCaptain($id,$leaderid)
    {
        $id = mysql_real_escape_string($id);
        $leader = mysql_real_escape_string($leaderid);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count`, `arenateamid` FROM `arena_team_member` WHERE `guid` = '$leader'");
        $row = mysql_fetch_array($sql);
        if($row['count'] < 1 || $row['arenateamid'] != $id) return false;

        $this->mysql->send("UPDATE `arena_team` SET `captainguid` = '$leader' WHERE `arenateamid` = '$id' LIMIT 1");
        return true;
    }

    /**
      Sets the name of an arena team
      @param $id the ID of the arena team
      @param $name the new name of the arena team
    */
    public function setATName($id, $name)
    {
        $id = mysql_real_escape_string($id);
        $name = mysql_real_escape_string($name);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `arena_team` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;

        $this->mysql->send("UPDATE `arena_team` SET `name` = '$name' WHERE `arenateamid` = '$id' LIMIT 1");
        return true;
    }
}

?>