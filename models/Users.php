<?php
declare(strict_types=1);
ini_set("display_errors", 1);


class Users
{

    private $connection;
    private $tbl_name;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->tbl_name = "users";
    }

    public function create_user(string $name, string $email, string $password)
    {
        $query = "INSERT INTO $this->tbl_name(name,email,password) VALUES(?,?,?);";
        $stmt = $this->connection->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $password = htmlspecialchars(strip_tags(password_hash($password, PASSWORD_DEFAULT)));

        $stmt->bind_param("sss", $name, $email, $password);
        return $stmt->execute() ? true : false;
    }

    public function get_user_by_id(int $id)
    {
        $query = "SELECT * FROM $this->tbl_name WHERE  id = ?";
        $stmt = $this->connection->prepare($query);

        $stmt->bind_param("i", $id);
        // $executed = $
        $stmt->execute();
        return $stmt->get_result();
    }
    public function get_user_by_email(string $email)
    {
        $query = "SELECT * FROM $this->tbl_name WHERE  email = ?";
        $stmt = $this->connection->prepare($query);
        $email = htmlspecialchars(strip_tags($email));

        $stmt->bind_param("s", $email);

        $stmt->execute();
        return $stmt->get_result();
    }
}