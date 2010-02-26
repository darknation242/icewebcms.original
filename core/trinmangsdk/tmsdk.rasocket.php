<?php
require_once("tmsdk.include.php");

/**
  This class communicates to MaNGOS/Trinity console via telnet
*/
class rasocket
{
    private $handle;
    private $errorstr, $errorno;
    private $auth;
    public $motto;

    /**
      Class constructer.
    */
    public function __construct()
    {
        $this->handle = false;
    }

    /**
      Class destructor. Closes the connection.
      Called with unset($parent).
    */
    public function __destruct()
    {
        if($this->handle)
        {
            fclose($this->handle);
            $this->auth = FALSE;
        }
    }

    /**
      Once connected to the server, this allows you to login
      Returns 0 if it isn't connected yet.
      Returns 1 if it was successful.
      Returns 2 if it was unable to authenticate.
      @param $user the username to login with
      @param $pass the password to login with
    */
    public function auth($user,$pass)
    {
        if(!$this->handle) return 0;

        $user = strtoupper($user);
        fwrite($this->handle, "USER ".$user."\n");
        usleep(50);
        fwrite($this->handle, "PASS ".$pass."\n");
        usleep(300);

        if (substr(trim(fgets($this->handle)),0,1) != "+")
          return 2;
        else
        {
            $this->auth = TRUE;
            return 1;
        }
    }

    /**
      Attempts to connect to console. Returns false if it was unable to connect.
      Returns true if it is successfully connected.
      @param $host the IP or the DNS name of the server
      @param $port the port on which try to connect (default 3443)
    */
    public function connect($host, $port = 3443)
    {
        if($this->handle)
          fclose($this->handle);

        $this->handle = @fsockopen($host, $port, $errorno, $errorstr, 5);

        if(!$this->handle)
          return false;
        else {
            $this->motto = trim(fgets($this->handle));
            return true;
        }
    }

    /**
      Inputs a command into an active connection to MaNGOS/Trinity
      Adds the output of the console into ralog.
      Returns 0 if it's not connected
      Returns 1 if it was successful
      Returns 2 if it's not authenticated
      @param $command the command to enter on console
    */
    public function sendcommand($command)
    {
        if(!$this->handle) return 0;

        if(!$this->auth) return 2;

        fwrite($this->handle, $command."\n");
        usleep(200);

        if(PROJECT == "trinity")
          fgets($this->handle,9);
        else
          fgets($this->handle,8);

        $this->motto = trim(fgets($this->handle));
        return 1;
    }

    /**
      Inputs a command into an active connection to MaNGOS/Trinity with a delay
      Adds the output of the console into ralog.
      Returns 0 if it's not connected
      Returns 1 if it was successful
      Returns 2 if it's not authenticated
      @param $command the command to enter on console
      @param $delay the amount of time in seconds to wait after the command has been entered in console (default 4)
    */
    public function sendcommanddelay($command, $delay = 4)
    {
        if(!$this->handle) return 0;

        if(!$this->auth) return 2;

        fwrite($this->handle, $command."\n");
        sleep($delay);

        if(PROJECT == "trinity")
          fgets($this->handle,9);
        else
          fgets($this->handle,8);

        $this->motto = fgets($this->handle);
        return 1;
    }
}

?>