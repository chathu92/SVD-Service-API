<?php
/**
 * Class to handle all the exam details
 * This class will have CRUD methods for exam
 *
 * @author Chathuri Gunarathna
 *
 */

class GuardianManagement {

    private $conn;

    function __construct() {
        require_once '../../model/commen/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	
/*
*
* ------------- 'guardian' table methods ------------------
*
*/

    /**
     * Creating new guardian
     *
     * @param String $gur_name Guardian name for the system
     * @param int    $gur_phone_number Guardian Phone Number
     * @param String $gur_adress Guardian Address
     * @param String $gur_email_address Guardian email adress
     * @param String $gur_occupation Guardian occupation
     * @param String $gur_occupation _type Guardian occupation type
	 * @param String $gur_office_address Guardian office adress
     * @param String $gur_office_phone_number Guardian office phone number
     * @param int    $gar_nic Guardian nic number
	 * @param int    $gar_tea_id Guardian tea id
	 * @param String $recode_added_by 
     *
     *
     * @return database transaction status
     */
    public function createGuardian($gur_name, $gur_phone_number, $gur_adress, $gur_email_address,$gur_occupation, $gur_occupation_type,$gur_office_address, $gur_office_phone_number,$gur_stu_addmision_number, $gur_old_student_number,$gur_other_interactions_with_dp, $gur_nic, $gur_tea_id, $recode_added_by ) {

		
        $response = array();
		

  
        // insert query
		$stmt = $this->conn->prepare("INSERT INTO guardian(gur_name, gur_phone_number, gur_adress, gur_email_address, gur_occupation, gur_occupation_type, gur_office_address, gur_office_phone_number, gur_stu_addmision_number, gur_old_student_number, gur_other_interactions_with_dp, gur_nic, gur_tea_id, recode_added_by) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssssissssssii", $gur_name, $gur_phone_number, $gur_adress, $gur_email_address,$gur_occupation, $gur_occupation_type,$gur_office_address, $gur_office_phone_number,$gur_stu_addmision_number, $gur_old_student_number,$gur_other_interactions_with_dp, $gur_nic, $gur_tea_id, $recode_added_by );
		$result = $stmt->execute();

        // Check for successful insertion
        if ($result) {
			$stmt = $this->conn->prepare("SELECT LAST_INSERT_ID();");
			if ($stmt->execute()) {
				$stmt->bind_result($LAST_INSERT_ID);
				$stmt->fetch();
				$stmt->close();
				return $LAST_INSERT_ID;
			} else {
				return CREATE_FAILED;
			}
        } else {
            // Failed to create project
            return CREATE_FAILED;
        }
        
		return $response;

    }
	
		/**
     * Update Guardian  
     *
     * @param String $gur_name Guardian  name for the system
     * @param String $gur_phone_number Guardian  phone number
     * @param String $gur_adress Guardian  address
     * @param String $gur_email_address Guardian  email adress
     * @param String $gur_occupation Guardian  occupation
     * @param String $gur_occupation _type Guardian occupation type
	 * @param String $gur_office_address Guardian office adress
     * @param String $gur_office_phone_number Guardian office phone number
     * @param int    $gar_nic Guardian nic number
	 * @param int    $gar_tea_id Guardian tea id
	 * @param String $recode_added_by
     *
     * @return database transaction status
     */
    public function updateGuardianManagement($gur_name, $gur_phone_number, $gur_adress, $gur_email_address,$gur_occupation, $gur_occupation_type,$gur_office_address, $gur_office_phone_number,$gur_stu_addmision_number, $gur_old_student_number,$gur_other_interactions_with_dp, $gur_nic, $gur_tea_id, $recode_added_by) {

		
        $response = array();
        // First check if project already existed in db
        if ($this->isOccupation_typeExists($gur_name)) {
            
			//
			$stmt = $this->conn->prepare("UPDATE guardian set status = 2,  recode_modified_at = now() , recode_modified_by = ?, gur_phone_number = ?, gur_adress = ?, gur_email_address = ?, gur_occupation =?, gur_occupation _type = ?, gur_office_address = ?, gur_office_phone_number = ?, gur_stu_addmision_number, gur_old_student_number, gur_other_interactions_with_dp, gur_nic = ?, gar_tea_id = ? where gur_name = ? and status = 1");
			$stmt->bind_param("issssssissssis", $recode_added_by, $gur_phone_number, $gur_adress, $gur_email_address, $gur_occupation, $gur_occupation_type, $gur_office_address, $gur_office_phone_number,$gur_stu_addmision_number, $gur_old_student_number, $gur_other_interactions_with_dp,  $gar_nic, $gar_tea_id, $gur_name);
			$result = $stmt->execute();
			

			$stmt->close();

        } else {
            // project is not already existed in the db
            return NOT_EXISTED;
        }
		
         

        // Check for successful update
        if ($result) {
			// project successfully update
            return UPDATE_SUCCESSFULLY;
        } else {
            // Failed to update project
            return UPDATE_FAILED;
        }
        
		return $response;

    }
	
/**
     * Delete Guardian
     *
     * @param String $gur_name Guardian name for the system
	 * @param int  $recode_added_by User ID for the system
     *
     * @return database transaction status
     */
    public function deleteGuardianManagement($gur_name,$recode_added_by) {

		
        $response = array();
        // First check if project already existed in db
        if ($this->isProjectExists($gur_name)) {
           			
			//
			$stmt = $this->conn->prepare("UPDATE guardian set status = 3, recode_modified_at = now() , recode_modified_by = ? where gur_name = ? and status=1");
			$stmt->bind_param("is",$recode_added_by, $gur_name);
			$result = $stmt->execute();
			
            $stmt->close();

        } else {
            // Project is not already existed in the db
            return NOT_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// exam successfully deleted
            return DELETE_SUCCESSFULLY;
        } else {
            // Failed to delete exam
            return DELETE_FAILED;
        }
        
		return $response;

    }
	  
	/**
     * Fetching guardian by gur_name
	 *
     * @param String $gur_name_in Guardian name
	 *
	 *@return guardian object only needed data
     */
    public function getGurdianByGurdianName($gur_name) {
        $stmt = $this->conn->prepare("SELECT gur_name, gur_adress, gur_email_address, gur_occupation, gur_occupation _type, gur_office_address, gur_office_phone_number, gur_stu_addmision_number, gur_old_student_number, gur_other_interactions_with_dp, gur_nic, gar_tea_id, recode_added_by FROM operational_user_view_release WHERE gur_name = ?");
        $stmt->bind_param("s", $gur_name);
        if ($stmt->execute()) {
            $stmt->bind_result($gur_name, $gur_phone_number, $gur_adress, $gur_email_address,$gur_occupation, $gur_occupation_type,$gur_office_address, $gur_office_phone_number,$gur_stu_addmision_number, $gur_old_student_number,$gur_other_interactions_with_dp, $gur_nic, $gur_tea_id, $recode_added_by);
            $stmt->fetch();
            $guardian = array();
            $guardian["gur_name"] = $gur_name;
			$guardian["gur_phone_number"] = $gur_phone_number;
            $guardian["gur_adress"] = $gur_adress;
            $guardian["gur_email_address"] = $gur_email_address;
            $guardian["gur_occupation"] = $gur_occupation;
			$guardian["gur_occupation_type"] = $gur_occupation_type;
            $guardian["gur_office_address"] = $gur_office_address;
			$guardian["gur_office_phone_number"] = $gur_office_phone_number;
			$guardian["gur_stu_addmision_number"] = $gur_stu_addmision_number;
			$guardian["gur_old_student_number"] = $gur_old_student_number;
			$guardian["gur_other_interactions_with_dp"] = $gur_other_interactions_with_dp;
		    $guardian["gur_nic"] = $gur_nic;
			$guardian["$gur_tea_id"]=$gur_tea_id;
			$guardian["recode_added_by"] = $recode_added_by;
            

            $stmt->close();
            return $guardian;
        } else {
            return NULL;
        }
    }
  
  
	/**
     * Fetching all guardian
	 *
     * @return $gur_name object set of all guardian
     */
    public function getAllProjects() {
        $stmt = $this->conn->prepare("SELECT * FROM guardian WHERE (status = 1 or status = 2) ORDER BY gur_name");
        $stmt->execute();
        $guardian = $stmt->get_result();
        $stmt->close();
        return $guardian;
    }
	
  
  
  
  
  
/*
 * ------------------------ SUPPORTIVE METHODS ------------------------
 */

	/**
     * Checking for duplicate guardian by gur_name
     *
     * @param String $guardian  gur_name name to check in db
     *
     * @return boolean
     */
    private function isGuardianExists($gur_name) {
		$stmt = $this->conn->prepare("SELECT gur_name from guardian WHERE (status = 1 or status = 2)  and gur_name = ?  ");
        $stmt->bind_param("s",$gur_name);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return ($num_rows > 0); //if it has more than zero number of rows; then  it sends true
    }

}

?>
