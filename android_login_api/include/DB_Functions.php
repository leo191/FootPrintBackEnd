<?php

class DB_Functions {

    private $conn;

    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    // destructor
    function __destruct() {

    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($bus_no, $name, $email, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt

        $stmt = $this->conn->prepare("INSERT INTO users(unique_id, bus_no, name, email, encrypted_password, salt, created_at) VALUES(?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssss", $uuid, $bus_no, $name, $email, $encrypted_password, $salt);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return $user;
        } else {
            return false;
        }
    }

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");

        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // verifying user password
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $stmt = $this->conn->prepare("SELECT email from users WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // user existed
            $stmt->close();
            return true;
        } else {
            // user not existed
            $stmt->close();
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }


/**
*bus number query

*/
 public function GetBusLoc($bus_no)
  {
    $stmt = $this->conn->prepare("SELECT * FROM bus_location WHERE bus_no = ?");

    $stmt->bind_param("s", $bus_no);

    if ($stmt->execute()) {
        $bus_location = $stmt->get_result()->fetch_assoc();
        $stmt->close();
      return $bus_location;
    }
    else {
      return false;
    }

  }

 public function DriverLocationStore($bus_no, $latitude, $longitude, $flag)
 {


   if($flag)
   {
     $stmt = $this->conn->prepare("UPDATE bus_location SET latitude = ?, longitude = ? WHERE bus_no = ?");
     $stmt->bind_param('sss',$latitude,$longitude,$bus_no);

   }
   else{
   $stmt = $this->conn->prepare("INSERT INTO bus_location(bus_no, latitude, longitude) VALUES(?, ?, ?)");
   $stmt->bind_param("sss",$latitude, $longitude, $bus_no);
   }
   $result=$stmt->execute();
   $stmt->close();
   if($result)
   {
     return true;
   }
   return false;
 }




 public function isBusRegistered($bus_no)
 {
   $stmt = $this->conn->prepare("SELECT bus_no FROM bus_location where bus_no = ?");
   $stmt->bind_param('s',$bus_no);
   $stmt->execute();
   $stmt->store_result();

   if($stmt->num_rows() > 0 )
   {
     $stmt->close();
     return true;
   }
   $stmt->close();
   return false;
 }





}

?>
