<?php
class Passenger
{
    private $conn;
    private $table_name = "tblpassenger";
    private $first_name;
    private $last_name;
    private $email;
    private $phone_number;
    private $address;
    private $password;
    private $verification_code;

    // Database Connection 
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($first_name, $last_name, $email, $phone_number, $address, $password)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone_number = $phone_number;
        $this->address = $address;
        $this->password = $password;

        if ($this->isEmailExist($this->email)) {
            header("location: register.php?error=emailExist");
            exit();
        }

        $this->verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        $this->sendVerificationCode();

        $this->createPassenger();
    }

    public function update($first_name, $last_name, $email, $phone_number, $address, $password, $id)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->phone_number = $phone_number;
        $this->address = $address;
        $this->password = $password;

        if ($this->password) {
            $sql = "UPDATE " . $this->table_name . " SET first_name=?, last_name=?, email=?, phone_number=?, address=?, password=? WHERE id=$id";
            $hashedPwd = password_hash($this->password, PASSWORD_DEFAULT);
        } else {
            $sql = "UPDATE " . $this->table_name . " SET first_name=?, last_name=?, email=?, phone_number=?, address=? WHERE id=$id";
        }

        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: account.php?error=stmtfailed");
            exit();
        }

        if ($hashedPwd) {
            mysqli_stmt_bind_param($stmt, "ssssss", $this->first_name, $this->last_name, $this->email, $this->phone_number, $this->address, $hashedPwd);
        } else {
            mysqli_stmt_bind_param($stmt, "sssss", $this->first_name, $this->last_name, $this->email, $this->phone_number, $this->address);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: account.php?success=updatedPassenger");
        exit();
    }

    public function login($email_or_phone, $password)
{
    $this->password = $password;

    // Check if the input is an email or phone number
    if (filter_var($email_or_phone, FILTER_VALIDATE_EMAIL)) {
        $userData = $this->isEmailExist($email_or_phone);
    } else {
        $userData = $this->isPhoneExist($email_or_phone);
    }

    if ($userData === false) {
        header("location: login.php?signin=fail");
        exit();
    }

    $checkPwd = password_verify($this->password, $userData['password']);

    if ($checkPwd === false) {
        header("location: login.php?signin=fail");
        exit();
    } elseif ($checkPwd === true) {
        session_start();

        $_SESSION["userId"] = $userData['id'];
        $_SESSION["userFname"] = $userData['first_name'];
        $_SESSION["userLname"] = $userData['last_name'];
        $_SESSION["userEmail"] = $userData['email'];
        $_SESSION["userPhone"] = $userData['phone_number'];
        $_SESSION["isVerified"] = ($userData['email_verified_at'] !== '0000-00-00 00:00:00');

        header("location: account.php");
        exit();
    }
}

public function isPhoneExist($phone_number)
{
    $sql = "SELECT * FROM " . $this->table_name . " WHERE phone_number = ?";
    $stmt = mysqli_stmt_init($this->conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: register.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $phone_number);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        return false;
    }

    mysqli_stmt_close($stmt);
}


    public function isEmailExist($email)
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: register.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($resultData)) {
            return $row;
        } else {
            return false;
        }

        mysqli_stmt_close($stmt);
    }

    private function sendVerificationCode()
    {
        $subject = "Verification Code";
        $comment = 'Hello, ' . $this->first_name . "\n\n Your verification code is: " . $this->verification_code;

        mail($this->email, $subject, $comment);
    }

    private function createPassenger()
    {
        $sql = "INSERT INTO " . $this->table_name . " (first_name, last_name, email, phone_number, address, password, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: register.php?error=stmtfailed");
            exit();
        }

        $hashedPwd = password_hash($this->password, PASSWORD_DEFAULT);

        mysqli_stmt_bind_param($stmt, "sssssss", $this->first_name, $this->last_name, $this->email, $this->phone_number, $this->address, $hashedPwd, $this->verification_code);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: login.php?signUp=passengerCreated");
        exit();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = mysqli_stmt_init($this->conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: account.php?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($resultData)) {
            return $row;
        }

        mysqli_stmt_close($stmt);
    }
}
?>
    