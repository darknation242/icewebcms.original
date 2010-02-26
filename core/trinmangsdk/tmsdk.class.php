<?php
require_once("tmsdk.include.php");

/**
  A class made of public static functions for general use.
*/
class tmsdk
{
    /**
      Boolean, returns true if it's possible to reach the server
      @param $host the IP or the DNS name of the server
      @param $port the port on which try to connect
      @param $timeout (default 3) the timeout of the connection
    */
    public static function checkServerStatus($host, $port, $timeout = 3)
    {
        if($sock = @fsockopen($host, $port, $error_no, $error_str, $timeout))
        {
            fclose($sock);
            return true;
        }
        return false;
    }

    /**
      String, returns the author(s) of the project
    */
    public static function getAuthor()
    {
        return PROJ_AUTHOR;
    }

    /**
      String, returns the licence of the project
    */
    public static function getLicence()
    {
        return PROJ_LICENCE;
    }

    /**
      String, returns the version of the project
    */
    public static function getVersion()
    {
        return PROJ_VERSION;
    }

    /**
      Mixed, returns the n-th offset value of the given data field
      @param $data the data field, divided by spaces
      @param $offset the n-th offset you want to know the value
    */
    public static function getDataOffset($data,$offset)
    {
        $exp = explode(' ',$data);
        return $exp[$offset];
    }

    /**
      Sets an offset in a data field variable, returns the data offset
      @param $data the data field
      @param $offset the offset you want to set
      @param $value the value you want to assign
    */
    public static function setDataOffset($data,$offset,$value)
    {
        $exp = explode(' ',$data);
        $exp[$offset] = $value;
        return implode(' ',$exp);
    }
}
?>
