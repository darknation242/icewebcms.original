<?php
require_once("tmsdk.include.php");

/**
  This class handles actions related to creatures
*/
class creature
{
    private $mysql;

    /**
      Class constructer.
      @param $world_db_conn the conndb class variable linked to the world database
    */
    public function __construct($world_db_conn)
    {
        $this->mysql = $world_db_conn;
    }

    /**
      Returns the number of times a spcecified creature is spawned
      @param $id the entry of the creature
    */
    public function countSpawns($id)
    {
        $id = mysql_real_escape_string($id);
        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `creature` WHERE `id` = '$id'");
        $row = mysql_fetch_array($sql);
        return $row['count'];
    }

    /**
      Returns general information on a specified creature
      @param $id the entry of the creature
    */
    public function getGeneralInfo($id)
    {
        $id = mysql_real_escape_string($id);

        $sql = $this->mysql->retrieve("SELECT * FROM `creature_template` WHERE `entry` = '$id' LIMIT 1");
        $row = mysql_fetch_array($sql);
        $result['name'] = $row['name'];
        $result['subname'] = $row['subname'];
        $result['model_A'] = $row['modelid_A'];
        $result['model_H'] = $row['modelid_H'];
        $result['health_min'] = $row['minhealth'];
        $result['health_max'] = $row['maxhealth'];
        $result['level_min'] = $row['minlevel'];
        $result['level_max'] = $row['maxlevel'];
        $result['faction_A'] = $row['faction_A'];
        $result['faction_H'] = $row['faction_H'];
        return $result;
    }
}

?>