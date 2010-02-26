<?php
require_once("tmsdk.include.php");

/**
  This class handles actions related to tickets
*/
class ticket
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
      Removes a ticket
      @param $id the ID of the ticket to be deleted
    */
    public function delete($id)
    {
        $id = mysql_real_escape_string($id);
        $this->mysql->send("DELETE FROM `character_ticket` WHERE ticket_id='$id'");
        return true;
    }

    /**
      Returns an array with a list of tickets
      Below, i is an integer that starts at 0 and consecutively
      increases by 1 for each arenateams.
      Returns result[i]['id'/'char'/'text'/'lastchange']
    */
    public function getTicketList()
    {
        $sql = $this->mysql->retrieve("SELECT * FROM `character_ticket`");
        $i = 0;
        while($row = mysql_fetch_array($sql))
        {
            $result[$i]['id'] = $row['ticket_id'];
            $result[$i]['char'] = $row['guid'];
            $result[$i]['text'] = $row['ticket_text'];
            $result[$i]['lastchange'] = $row['ticket_lastchange'];
            $i++;
        }
        return $result;
    }
}

?>