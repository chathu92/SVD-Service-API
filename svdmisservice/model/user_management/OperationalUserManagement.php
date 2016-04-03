<?php
require_once '../../model/commen/PassHash.php';
/**
 * Class to handle all the operational uder details
 * This class will have CRUD methods for operational_user, user_category tables
 *
 * @author Hasitha Lakmal
 *
 */
define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);
define('USER_NOT_EXISTED', 3);
define('USER_UPDATE_SUCCESSFULLY', 4);
define('USER_UPDATE_FAILED', 5);
define('USER_DELETE_SUCCESSFULLY', 4);
define('USER_DELETE_FAILED', 6);


class OperationalUserManagement {

    private $conn;

    function __construct() {
        require_once '../../model/commen/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	
	
/**
*
*
*
* ------------- `operational_user` table methods ------------------
*
*
*
*/
	
	 /**
     * Checking user login
	 *
     * @param String $usr_name User login user name
     * @param String $usr_pwd User login password
	 *
     * @return boolean User login status success/fail
     */
    public function checkLogin($usr_name, $usr_pwd_in) {
        // fetching user by usr_name
        $stmt = $this->conn->prepare("SELECT usr_pwd FROM operational_user WHERE usr_name = ? and status = 1");

        $stmt->bind_param("s", $usr_name);

        $stmt->execute();

        $stmt->bind_result($usr_pwd);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the user name
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($usr_pwd, $usr_pwd_in)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Creating new operational_user
     *
     * @param String $usr_name User name for the system
     * @param String $usr_pwd User login password
     * @param String $usr_full_name User full name
     * @param String $usr_email User email adress
     * @param String $usr_phone_number User phone number
     * @param String $usr_category User usercatogary id
     *
     * @return database transaction status
     */
    public function createOperationalUser($usr_name, $usr_pwd, $usr_full_name, $usr_email, $usr_phone_number, $usr_category, $recode_added_by) {

		
        $response = array();
		
        // First check if user already existed in db
        if (!$this->isOperationalUserExists($usr_name)) {
            // Generating password hash
            $password_hash = PassHash::hash($usr_pwd);

            // Generating API key
            $usr_api_key = $this->generateApiKey();
			
			
            // insert query
			 $stmt = $this->conn->prepare("INSERT INTO operational_user(usr_name, usr_pwd, usr_full_name, usr_email, usr_phone_number, usr_category, usr_api_key, recode_added_by) values(?, ?, ?,  ?, ?, ?, ?, ?)");
			 $stmt->bind_param("sssssisi", $usr_name, $password_hash, $usr_full_name, $usr_email, $usr_phone_number, $usr_category, $usr_api_key,$recode_added_by );
			 $result = $stmt->execute();

			 $stmt->close();

        } else {
            // User is not already existed in the db
            return USER_ALREADY_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// User successfully inserted
            return USER_CREATED_SUCCESSFULLY;
        } else {
            // Failed to create user
            return USER_CREATE_FAILED;
        }
        
		return $response;

    }
	
	/**
     * Update operational_user
     *
     * @param String $usr_name User name for the system
     * @param String $usr_pwd User login password
     * @param String $usr_full_name User full name
     * @param String $usr_email User email adress
     * @param String $usr_phone_number User phone number
     * @param String $usr_category User usercatogary id
     *
     * @return database transaction status
     */
    public function updateOperationalUser($usr_name, $usr_pwd, $usr_full_name, $usr_email, $usr_phone_number, $usr_category, $recode_added_by) {

		
        $response = array();
        // First check if user already existed in db
        if ($this->isOperationalUserExists($usr_name)) {
            // Generating password hash
            $password_hash = PassHash::hash($usr_pwd);

            // Generating API key
            $usr_api_key = $this->generateApiKey();
			
			//
			$stmt = $this->conn->prepare("UPDATE operational_user set status = 2,  recode_modified_at = now() , recode_modified_by = ? where usr_name = ? and status = 1");
			$stmt->bind_param("is", $recode_added_by, $usr_name);
			$result = $stmt->execute();
			
            // insert updated recode
			 $stmt = $this->conn->prepare("INSERT INTO operational_user(usr_name, usr_pwd, usr_full_name, usr_email, usr_phone_number, usr_category, usr_api_key, recode_added_by) values(?, ?, ?,  ?, ?, ?, ?, ?)");
			 $stmt->bind_param("sssssisi", $usr_name, $password_hash, $usr_full_name, $usr_email, $usr_phone_number, $usr_category, $usr_api_key,$recode_added_by );
			 $result = $stmt->execute();

			 $stmt->close();

        } else {
            // User with same email already existed in the db
            return USER_NOT_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// User successfully inserted
            return USER_UPDATE_SUCCESSFULLY;
        } else {
            // Failed to create user
            return USER_UPDATE_FAILED;
        }
        
		return $response;

    }
	
/**
     * Delete operational_user
     *
     * @param String $usr_name User name for the system
	 * @param int $user_id User ID for the system
     *
     * @return database transaction status
     */
    public function deleteOperationalUser($user_id, $usr_name) {

		
        $response = array();
        // First check if user already existed in db
        if ($this->isOperationalUserExists($usr_name)) {
           			
			//
			$stmt = $this->conn->prepare("UPDATE operational_user set status = 3, recode_modified_at = now() , recode_modified_by = ? where usr_name = ? and status=1");
			$stmt->bind_param("is",$user_id, $usr_name);
			$result = $stmt->execute();
			
            $stmt->close();

        } else {
            // User is not already existed in the db
            return USER_NOT_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// User successfully inserted
            return USER_DELETE_SUCCESSFULLY;
        } else {
            // Failed to create user
            return USER_DELETE_FAILED;
        }
        
		return $response;

    }
	  
	/**
     * Fetching user by usr_name
	 *
     * @param String $usr_name_in User name
	 *
	 *@return user object only needed data
     */
    public function getUserByUserName($usr_name_in) {
        $stmt = $this->conn->prepare("SELECT usr_name, usr_full_name, usr_email, usr_phone_number, usr_api_key, ou_status,ou_recode_added_at,ucat_name,ucat_description FROM operational_user_view_release WHERE usr_name = ?");
        $stmt->bind_param("s", $usr_name_in);
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($usr_name,  $usr_full_name, $usr_email, $usr_phone_number, $usr_api_key, $ou_status, $ou_recode_added_at, $ucat_name, $ucat_description);
            $stmt->fetch();
            $user = array();
            $user["usr_name"] = $usr_name;
            $user["usr_full_name"] = $usr_full_name;
            $user["usr_email"] = $usr_email;
            $user["usr_phone_number"] = $usr_phone_number;
			$user["usr_api_key"] = $usr_api_key;
            $user["ou_status"] = $ou_status;
			$user["ou_recode_added_at"] = $ou_recode_added_at;
            $user["ucat_name"] = $ucat_name;
            $user["ucat_description"] = $ucat_description;

            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }
  
  
	/**
     * Fetching all users
	 *
     * @return $users result set of all users
     */
    public function getAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM operational_user_view_release WHERE ou_status = 1");
        $stmt->execute();
        $users = $stmt->get_result();
        $stmt->close();
        return $users;
    }
	
  
  
  
  
  
/**
*
*
*
* ------------- Supportive methods ------------------
*
*
*
*/

  /**
     * Fetching user api key
     * @param String $usr_id user primary key in operational_user table
	 *
	 *@return api_key
     */
    public function getApiKeyById($usr_id) {
        $stmt = $this->conn->prepare("SELECT usr_api_key FROM operational_user WHERE usr_id = ? and status = 1");
        $stmt->bind_param("i", $usr_id);
        if ($stmt->execute()) {
            // $api_key = $stmt->get_result()->fetch_assoc();
            // TODO
            $stmt->bind_result($api_key);
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key operational_user api key
	 *
	 * @return operational_user user_id (primary key in operational_user table)
     */
    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT usr_id FROM operational_user WHERE usr_api_key = ? and status = 1");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($usr_id);
            $stmt->fetch();
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $usr_id;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
	 *
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
	 *
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT usr_id from operational_user WHERE usr_api_key = ? and status = 1");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    

	/**
     * Checking for duplicate operational_user by usr_name
     *
     * @param String $usr_name user name to check in db
     *
     * @return boolean
     */
    private function isOperationalUserExists($usr_name) {
		$stmt = $this->conn->prepare("SELECT usr_name from operational_user WHERE usr_name = ? and status=1");
        $stmt->bind_param("s", $usr_name);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return !($num_rows == 0); //if no any user number of rows ==0; then get negative of it to send false
    }

}

?>
