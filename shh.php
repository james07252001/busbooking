<?php
class Database {
    private $host = "localhost";
    private $db_name = "u510162695_bobrs";
    private $username = "u510162695_bobrs";
    private $password = "1Bobrs_password";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

    public function addPhoneNumberColumn() {
        try {
            $sql = "ALTER TABLE tblpassenger ADD phone_number VARCHAR(15) NOT NULL";
            $this->getConnection()->exec($sql);
            echo "Column `phone_number` added successfully.";
        } catch (PDOException $exception) {
            echo "Error adding column: " . $exception->getMessage();
        }
    }
}

// Usage
$db = new Database();
$db->addPhoneNumberColumn();
?>
