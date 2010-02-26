<?php
require_once("tmsdk.include.php");

/**
  TMSDK class dedicated to manage the characters
*/
class char
{
    private $mysql;

    /**
      Class constructor.
      @param $char_db_conn the conndb class variable linked to the character database
    */
    public function __construct($char_db_conn)
    {
        $this->mysql = $char_db_conn;
    }

    /**
      Adds a spell to a specified character.
      @param $guid the character's GUID
      @param $spell the spell id to be added
      @param $active 1=appears in spellbook 0=doesn't appear in spellbook (default 1)
      @param $disabled 1=not usable by character (default 0)
    */
    public function addSpell($guid, $spell, $active = 1, $disabled = 0)
    {
        $guid = mysql_real_escape_string($guid);
        $spell = mysql_real_escape_string($spell);
        $active = mysql_real_escape_string($active);
        $disabled = mysql_real_escape_string($disabled);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `character_spell` WHERE `guid` = '$guid' AND `spell` = '$spell'");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;

        $this->mysql->send("INSERT INTO `character_spell` (guid,spell,active,disabled) VALUES ('$guid','$spell','$active','$disabled')");
        return true;
    }

    /**
      Adjusts the level of a character.
      @param $guid the character's GUID
      @param $mod the modifier
    */
    public function adjustLevel($guid, $mod)
    {
        $guid = mysql_real_escape_string($guid);
        $adjust = mysql_real_escape_string($mod);
        $adjust = intval($adjust);
        if(PROJECT == "mangos")
        {
            $sql = $this->mysql->retrieve("SELECT `level` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
            $row = mysql_fetch_array($sql);
            $lvl = $row['level']+$adjust;
            $this->mysql->send("UPDATE `characters` SET `level` = '$lvl' WHERE `guid` = '$guid' LIMIT 1");
        } else {
            $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
            $row = mysql_fetch_array($sql);

            $exp = explode(' ',$row['data']);
            // if the final level is 1 or more
            if($exp[DATA_FIELD_LEVEL]+$adjust > 0)
                // apply the modifier
                $data = tmsdk::setDataOffset( $row['data'], DATA_FIELD_LEVEL, $exp[DATA_FIELD_LEVEL]+$adjust );
            else
                // set the level to the minimum value (1)
                $data = tmsdk::setDataOffset( $row['data'], DATA_FIELD_LEVEL, 1 );
            $this->mysql->send("UPDATE `characters` SET `data` = '$data' WHERE `guid` = '$guid' LIMIT 1");
        }
        return true;
    }

    /**
      Adjusts the money of a character.
      @param $guid the character's GUID
      @param $mod the modifier
    */
    public function adjustMoney($guid, $mod)
    {
        $guid = mysql_real_escape_string($guid);
        $adjust = mysql_real_escape_string($mod);
        $adjust = intval($adjust);

        if(PROJECT == "mangos")
        {
            $sql = $this->mysql->retrieve("SELECT `money` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
            $row = mysql_fetch_array($sql);
            $money = $row['money']+$adjust;
            $this->mysql->send("UPDATE `characters` SET `money` = '$money' WHERE `guid` = '$guid' LIMIT 1");
        } else {
            $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
            $row = mysql_fetch_array($sql);

            $exp = explode(' ',$row['data']);
            $exp[DATA_FIELD_MONEY] = $exp[DATA_FIELD_MONEY]+$adjust;
            $imp = implode(' ',$exp);
            $this->mysql->send("UPDATE `characters` SET `data` = '$imp' WHERE `guid` = '$guid' LIMIT 1");
        }
        return true;
    }

    /**
      Number, returns the account ID of a specified character.
      @param $guid the character's GUID
    */
    public function getAccountId($guid)
    {
        $guid = mysql_real_escape_string($guid);

        $sql = $this->mysql->retrieve("SELECT `account` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        return $row['account'];
    }

    /**
      Mixed, returns the account's characters name and GUID.
      result[i]['guid'/'name'], where i is an index starting from 0.
      @param $id the account's ID
    */
    public function getCharactersListFromAccountId($id)
    {
        $id = mysql_real_escape_string($id);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `characters` WHERE `account` = '$id'");
        $row = mysql_fetch_array($sql);
        $result['0'] = $row['count'];

        $sql = $this->mysql->retrieve("SELECT `guid`, `name` FROM `characters` WHERE `account` = '$id'");
        $i = 1;
        while($row = mysql_fetch_array($sql))
        {
            $result[$i]['guid'] = $row['guid'];
            $result[$i]['name'] = $row['name'];
            $i++;
        }

        return $result;
    }

    /**
      CLASS_*, returns the class of the character.
      @param $guid the character's GUID
    */
    public function getClass($guid) {
        $guid = mysql_real_escape_string($guid);

        $sql = $this->mysql->retrieve("SELECT `class` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['class'];
    }

    /**
      String, returns the data field of a specified character.
      @param $guid the character's GUID
    */
    public function getDataField($guid)
    {
        $guid = mysql_real_escape_string($guid);

        $sql = $this->mysql->retrieve("SELECT `data` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        return $row['data'];
    }

    /**
      Returns an array with all the equipped objects.
      result[SLOT_*]['item_entry'/'item_guid']
      @param $guid the character's GUID
    */
    public function getEquippedGear($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT * FROM `character_inventory` WHERE `guid` = '$guid' AND `slot` < '19' AND `bag` = '0'");
        while($row = mysql_fetch_array($sql))
        {
            $slot = $row['slot'];
            $result[$slot]['item_entry'] = $row['item_template'];
            $result[$slot]['item_guid'] = $row['item'];
        }
        return $result;
    }

    /**
      Number, returns the faction of a specified character
      1 = ally, 0 = horde
      @param $guid the character's GUID
    */
    public function getFaction($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $ally = array("1", "3", "4", "7", "11");

        $sql = $this->mysql->retrieve("SELECT `race` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);

        if(in_array($row['race'], $ally))
        {
            return 1;
        } else {
            return 0;
        }
    }

    /**
      GENDER_*, returns the gender of a character.
      @param $guid the character's GUID
    */
    public function getGender($guid)
    {
        $guid = mysql_real_escape_string($guid);

        if(PROJECT == "mangos")
            $sql = $this->mysql->retrieve("SELECT `gender` FROM `characters` WHERE guid = $guid LIMIT 1");
        else
            // took from mmfpm
            $sql = $this->mysql->retrieve("SELECT mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(DATA_FIELD_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender FROM `characters` WHERE guid = $guid LIMIT 1");

        $row = mysql_fetch_array($sql);
        return $row['gender'];
    }

    /**
      Number, returns the GUID of a character
      @param $name the name of the character
    */
    public function getGuid($name)
    {
        $name = mysql_real_escape_string($name);
        $name = strtolower($name);
        $name = ucfirst($name);

        $sql = $this->mysql->retrieve("SELECT `guid` FROM `characters` WHERE `name` = '$name' LIMIT 1");
        $row = mysql_fetch_array($sql);

        return $row['guid'];
    }

    /**
      Mixed, returns the guild rank of a character.
      result['rid'] is the ID of the rank, result['rname'] is the name of the rank.
      @param $guid the character's GUID
    */
    public function getGuildRank($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT `guildid`,`rank` FROM `guild_member` WHERE `guid` = '$guid' LIMIT 1");
        if(mysql_affected_rows() == 0) return false;
        $row = mysql_fetch_array($sql);

        $sql = $this->mysql->retrieve("SELECT `rname`, `rid` FROM `guild_rank` WHERE `rid` = '".$row['rank']."' AND `guildid` = '".$row['guildid']."' LIMIT 1");
        if(mysql_affected_rows() == 0) return false;
        $row = mysql_fetch_array($sql);

        $result = array(
            "rid" => $row['rid'],
            "rank" => $row['rname'],
        );

        return $result;
    }

    /**
      Returns an array with the character's home
      @param $guid the character's GUID
    */
    public function getHome($guid)
    {
        $sql = $this->mysql->retrieve("SELECT `map`, `zone`, `position_x`, `position_y`, `position_z` FROM `character_homebind` WHERE `guid` = '$guid'");
        $result = mysql_fetch_array($sql);
        return $result;
    }

    /**
      Number, returns the honor currency of a character.
      @param $datafield the character's data field
    */
    public static function getHonor($datafield)
    {
        return tmsdk::getDataOffset($datafield,DATA_FIELD_HONOR);
    }

    /**
      Number, returns the number of a specified item
      that a character has in it's inventory,
      bank, and other storage.
      @param $guid the character's GUID
      @param $item the item's entry
    */
    public function getItemCount($guid, $item)
    {
        $guid = mysql_real_escape_string($guid);
        $item = mysql_real_escape_string($item);

        $sql = $this->mysql->retrieve("SELECT `item` FROM `character_inventory` WHERE `guid` = '$guid' AND `item_template` = '$item'");
        $i = 0;
        while($row = mysql_fetch_array($sql))
        {
            $sqlb = $this->mysql->retrieve("SELECT `data` FROM `item_instance` WHERE `guid` = '$row[item]'");
            $rowb = mysql_fetch_array($sqlb);

            $count = $count + tmsdk::getDataOffset($rowb['data'],14);
            $i++;
        }
        return $count;
    }

    /**
      Number, returns the level of a specified character. For Trinity.
      @param $datafield the character's data field
    */
    public static function getLevel($datafield)
    {
        return tmsdk::getDataOffset($datafield,DATA_FIELD_LEVEL);
    }

    /**
      Number, returns the level of a specified character. For MaNGOS.
      @param $guid the GUID of the character
    */
    public function getLevelMangos($guid)
    {
        if(PROJECT != "mangos")
            return false;

        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT `level` FROM `characters` WHERE `guid` = '$guid'");
        $row = mysql_fetch_array($sql);
        return $row['level'];
    }

    /**
      Mixed, returns the contents of a specified character's backpack.
      result[SLOTID (number)]['item' (number) / 'guid' (number)]
      @param $guid the GUID of the character
    */
    public function getMainBag($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT * FROM `character_inventory` WHERE `guid` = '$guid' AND `slot` > '22' AND `slot` < '39'");
        while($row = mysql_fetch_array($sql))
        {
            $result[$row['slot']]['item'] = $row['item_template'];
            $result[$row['slot']]['guid'] = $row['item'];
        }
        return $result;
    }

    /**
      Number, returns the money of a specified character. For Trinity
      Format is XXYYZZ, ZZ = copper, YY = silver, XX = gold,
      starting from the right.
      @param $datafield the character's data field
    */
    public static function getMoney($datafield)
    {
        return tmsdk::getDataOffset($datafield,DATA_FIELD_MONEY);
    }

    /**
      Number, returns the money of a specified character. For MaNGOS
      Format is XXYYZZ, ZZ = copper, YY = silver, XX = gold,
      starting from the right.
      @param $guid the GUID of the character
    */
    public function getMoneyMangos($guid)
    {
        if(PROJECT != "mangos")
            return false;
 
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT `money` FROM `characters` WHERE `guid` = '$guid'");
        $row = mysql_fetch_array($sql);
        return $row['money'];
    }

    /**
      String, returns the name of a specified character.
      @param $guid the character's GUID
    */
    public function getName($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $sql = $this->mysql->retrieve("SELECT `name` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['name'];
    }

    /**
      Number, returns the number of characters on the server.
    */
    public function getNumCharsOnline()
    {
        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `characters` WHERE `online` = '1'");
        $row = mysql_fetch_array($sql);
        return intval($row['count']);
    }

    /**
      Boolean, returns true if the character is online.
      @param $guid the character's GUID
    */
    public function getOnlineStatus($guid)
    {
        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `characters` WHERE `guid` = '$guid' AND `online` = '1'");
        $row = mysql_fetch_array($sql);

        if($row['count'] > 0) return true;

        return false;
    }

    /**
      RACE_*, returns the race of the character.
      @param $guid the character's GUID
    */
    public function getRace($guid)
    {
        $guid = mysql_real_escape_string($guid);

        $sql = $this->mysql->retrieve("SELECT `race` FROM `characters` WHERE `guid` = '$guid' LIMIT 1");
        $row = mysql_fetch_array($sql);
        return $row['race'];
    }

    /**
      Removes a spell from the specified character.
      @param $guid the character's GUID
      @param $spell the spell's ID
    */
    public function removeSpell($guid, $spell)
    {
        $guid = mysql_real_escape_string($guid);
        $spell = mysql_real_escape_string($spell);
        $this->mysql->send("DELETE FROM `character_spell` WHERE `guid` = '$guid' AND `spell` = '$spell'");
        return true;
    }

    /**
      (Hacky) Removes the ghost auras from a character to revive them.
      @param $guid the character's GUID
      @todo need more investigation to create a better solution (see stone coords)
    */
    public function revive($guid)
    {
        $guid = mysql_real_escape_string($guid);
        $this->mysql->send("DELETE FROM `character_aura` WHERE `guid` = '".$guid."' AND `spell` = '20584' OR `guid` = '".$guid."' AND `spell` = '8326'");
        return true;
    }

    /**
      Sets the account id of a character
      @param $guid the character's GUID
      @param $accountId the account ID you want to set it to
    */
    public function setAccountId($guid,$accountId)
    {
        $guid = mysql_real_escape_string($guid);
        $acct = mysql_real_escape_string($accountId);

        $this->mysql->send("UPDATE `characters` SET `account` = '$acct' WHERE `guid` = '$guid' LIMIT 1");
        return true;
    }

    /**
      Sets an offset in the data field
      @param $guid the character's GUID
      @param $offset the offset you want to set
      @param $value the offset's value
    */
    public function setDataField($guid,$offset,$value)
    {
        $guid = mysql_real_escape_string($guid);
        $offset = mysql_real_escape_string($offset);
        $val = mysql_real_escape_string($value);
        $val = intval($val);

        $data = $this->getDataField($guid);
        $data = tmsdk::setDataOffset($data, $offset, $value);

        $this->mysql->send("UPDATE `characters` SET `data` = '$data' WHERE `guid` = '$guid' LIMIT 1");
        return true;
    }

    /**
      Sets the honor of a character.
      @param $guid the character's GUID
      @param $newhonor the new honor
    */
    public function setHonor($guid, $newhonor)
    {
        $guid = mysql_real_escape_string($guid);
        $newhonor = mysql_real_escape_string($newhonor);
        $newhonor = intval($newhonor);
        if($newhonor < 0) $newhonor = 0;

        $this->setDataField($guid, DATA_FIELD_HONOR, $newhonor);
        return true;
    }

    /**
      Sets the level of a character.
      @param $guid the character's GUID
      @param $newlevel the new level
    */
    public function setLevel($guid, $newlevel)
    {
        $guid = mysql_real_escape_string($guid);
        $newlevel = mysql_real_escape_string($newlevel);
        $newlevel = intval($newlevel);
        if($newlevel < 1) $newlevel = 1;

        if(PROJECT != "mangos")
            $this->setDataField($guid, DATA_FIELD_LEVEL, $newlevel);
        else
            $this->mysql->send("UPDATE `characters` SET `level` = '$newlevel' WHERE `guid` = '$guid' LIMIT 1");

        return true;
    }

    /**
      Teleports a character to a given location.
      @param $guid the character's GUID
      @param $x X coordinate
      @param $y Y coordinate
      @param $z Z coordinate
      @param $map the map ID
      @param $zone the zone ID (default void)
    */
    public function setLocation($guid, $x, $y, $z, $map, $zone="")
    {
        $guid = mysql_real_escape_string($guid);
        $x = mysql_real_escape_string($x);
        $y = mysql_real_escape_string($y);
        $z = mysql_real_escape_string($z);
        $map = mysql_real_escape_string($map);
        $zone = mysql_real_escape_string($zone);

        $query = "UPDATE `characters` SET `position_x` = '$x', `position_y` = '$y', `position_z` = '$z', `map` = '$map'";
        if($zone != "") $query .= ", `zone` = '$zone'";
        $query .= " WHERE `guid` = '$guid' LIMIT 1";
        $this->mysql->send($query);

        return true;
    }

    /**
      Sets the money of a character.
      @param $guid the character's GUID
      @param $sumofmoney the new amount of money, in copper (XXYYZZ, XX = gold, YY = silver, ZZ = copper)
    */
    public function setMoney($guid, $sumofmoney)
    {
        $guid = mysql_real_escape_string($guid);
        $sum = mysql_real_escape_string($sumofmoney);
        $sum = intval($sum);
        if($sum < 0) $sum = 0;

        if(PROJECT != "mangos")
            $this->setDataField($data, DATA_FIELD_MONEY, $sum);
        else
            $this->mysql->send("UPDATE `characters` SET `money` = '$sum' WHERE `guid` = '$guid' LIMIT 1");

        return true;
    }

    /**
      Boolean, sets the name of a character.
      Returns false if the name has just been taken, or it's void.
      @param $guid the character's GUID
      @param $newname the new name
    */
    public function setName($guid,$newname)
    {
        if($newname == "") return false;
        $newname = mysql_real_escape_string(strtolower($newname));
        $newname = ucfirst($newname);
        $guid = mysql_real_escape_string($guid);

        $sql = $this->mysql->retrieve("SELECT COUNT(*) AS `count` FROM `characters` WHERE `name` = '$newname' LIMIT 1");
        $row = mysql_fetch_array($sql);
        if($row['count'] > 0) return false;
        $this->mysql->send("UPDATE `characters` SET `name` = '$newname' WHERE `guid` = '$guid' LIMIT 1");
        return true;
    }
}

?>
