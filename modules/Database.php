<?php

declare(strict_types=1);
ini_set("display_errors",1);



class Database
{
    private $hostname;
    private $db_name;
    private $password;
    private $username;
    // private $conn;

    public function __construct($hostname,$username,$password,$db_name){
        $this->hostname = $hostname;
        $this->db_name = $db_name;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect(){
        $conn = new mysqli($this->hostname,$this->username,$this->password,$this->db_name);
        if($conn->connect_errno){
            print_r($conn->error);
            exit;
        }else {return $conn;}
    }
}
