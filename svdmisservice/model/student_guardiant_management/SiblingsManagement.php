<?php
/**
 * Class to handle all the exam details
 * This class will have CRUD methods for exam
 *
 * @author Chathuri Gunarathna
 *
 */

class SiblingsManagement {

    private $conn;

    function __construct() {
        require_once '../../model/commen/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	
	
/*
 * ------------------------ OCCUPATION TYPE TABLE METHODS ------------------------
 */

    /**
     * Creating new Occupation_type
     *
     * @param String $occ_type_name Occupation_type name
     * @param String $occ_type_description Occupation_type Discription
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function createSiblings($stu_id, $slib_id, $recode_added_by ) {

		
        
        $response = array();
		
        // First check if project already existed in db
        if (!$this->isSiblingsExists($stu_id, $slib_id)) {
  
            // insert query
			 $stmt = $this->conn->prepare("call insert_siblings(?,?,?)");
			 $stmt->bind_param("iii", $stu_id, $slib_id, $recode_added_by );
			 $result = $stmt->execute();

			 $stmt->close();

        } else {
            // Project is not already existed in the db
            return ALREADY_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// project successfully inserted
            return CREATED_SUCCESSFULLY;
        } else {
            // Failed to create project
            return CREATE_FAILED;
        }
        
		return $response;

    }
	
	/**
     * Update  Occupation_type
     *
     * @param String $occ_type_name Occupation_type name
	 * @param String $occ_type_description Occupation_type Discription 
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function updateOccupation_type($occ_type_name, $occ_type_description, $recode_added_by) {

		
        $response = array();
        // First check if project already existed in db
        if ($this->isOccupation_typeExists($occ_type_name)) {
            
			//
			$stmt = $this->conn->prepare("UPDATE occupation_type set status = 2,  recode_modified_at = now() , recode_modified_by = ?, occ_type_description= ? where occ_type_name= ? and (status = 1 or status = 2)");
			$stmt->bind_param("iss", $recode_added_by, $occ_type_description, $occ_type_name);
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
     * Delete Occupation_type 
     *
     * @param String $occ_type_name for the system
	 * @param String $recode_added_by
     *
     * @return database transaction status
     */
    public function deleteOccupationType($occ_type_name, $recode_added_by) {

		
        $response = array();
        // First check if project already existed in db
        if ($this->isProjectExists($occ_type_name)) {
           			
			//
			$stmt = $this->conn->prepare("UPDATE occupation_type set status = 3, recode_modified_at = now() , recode_modified_by = ? where occ_type_name = ? and (status = 1 or status = 2)");
			$stmt->bind_param("is", $recode_added_by,$occ_type_name);
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
     * Fetching occupation_type by occ_type_name
	 *
     * @param String $occ_type_name  occupation_type name
	 *
	 *@return Project object only needed data
     */
    public function getOccTypeNametByProjectName($occ_type_name) {
        $stmt = $this->conn->prepare("SELECT occ_type_name, occ_type_description, status, recode_added_at, recode_added_by FROM occupation_type WHERE occ_type_name = ? and (status = 1 or status = 2)");
        $stmt->bind_param("s", $occ_type_name);
        if ($stmt->execute()) {
            $stmt->bind_result($occ_type_name, $cc_type_description, $status, $recode_added_at, $recode_added_by);
            $stmt->fetch();
            $occupation_type = array();
            $occupation_type["occ_type_name"] = $occ_type_name;
            $occupation_type["occ_type_description"] = $occ_type_description;
            $occupation_type["status"] = $status;
            $occupation_type["recode_added_at"] = $recode_added_at;
			$occupation_type["recode_added_by"] = $recode_added_by;

            $stmt->close();
            return $occupation_type;
        } else {
            return NULL;
        }
    }
  
  
	/**
     * Fetching all occupation_types
	 *
     * @return $occupation_types object set of all occupation_types
     */
    public function getAllProjects() {
        $stmt = $this->conn->prepare("SELECT * FROM occupation_type WHERE (status = 1 or status = 2) ORDER BY occ_type_name");
        $stmt->execute();
        $occupation_types = $stmt->get_result();
        $stmt->close();
        return $occupation_types;
    }
	
  
  
  
  
  
/*
 * ------------------------ SUPPORTIVE METHODS ------------------------
 */

	/**
     * Checking for duplicate occupation type by occ_type_name
     *
     * @param String $occ_type_name occupation type name to check in db
     *
     * @return boolean
     */
    private function isSiblingsExists($stu_id, $slib_id) {
		$stmt = $this->conn->prepare("SELECT stu_id from siblings WHERE (status = 1 or status = 2)  and stu_id = ?  and slib_id = ?");
        $stmt->bind_param("ii",$stu_id, $slib_id);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return ($num_rows > 0); //if it has more than zero number of rows; then  it sends true
    }

}

?>
