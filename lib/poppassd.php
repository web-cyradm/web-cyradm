<?
    class poppassd {

    var $fp;
    var $err_str;

    function connect( $server, $port ) {
        $this->fp = fsockopen($server, $port, &$err_no, &$err_str);
        if (!$this->fp) {
            $this->err_str = $err_str;
            return false;
        } else {
            return $this->get_prompt();
        }
    }

    function disconnect() {
        if (isset($this->fp)) {
            fputs($this->fp, "quit\n");
            fclose($this->fp);
        }
    }

    function get_prompt() {
        $prompt = fgets($this->fp, 4096);
        if (ereg("^[1-5][0-9][0-9]",$prompt))
        {
            if (substr($prompt, 0, 3) == "200") {
                return true;
            } else {
                $this->err_str = $prompt;
                return false;
            }
        }
        else {
            return true;
        }
    }

    function send_command($cmd, $arg) {
        $line = $cmd . " " . $arg . "\n";
        fputs($this->fp, $line);
        return $this->get_prompt();
    }

    function change_password($user_name, $old_password, $new_password) {

        $server = 'localhost';
        $port = 106;
        if ($this->connect($server, $port)) {
            if ($this->send_command("user", $user_name)) {
                if ($this->send_command("pass", $old_password)) {
                    if ($this->send_command("newpass", $new_password)) {
                        $return_value = true;
                    }
                }
            }
            $this->disconnect();
        }
        return $return_value;
    }

}
?>
