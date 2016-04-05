<?php
/**
 * Class to handle all the exam details
 * This class will have CRUD methods for exam
 *
 * @author Kalani Parapitiya
 *
 */

class ProjectManagement {

    private $conn;

    function __construct() {
        require_once '../../model/commen/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	
	
/*
 * ------------------------ PROJECT TABLE METHODS ------------------------
 */

    /**
     * Creating new project
     *
     * @param String $pro_name Project name for the system
     * @param String $pro_discription Discription of the project
	 * @param String $pro_PDF_path project pdf path for the system
	 * @param String $pro_supervisor_id Project supervisor id  for the system
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function createProject($pro_name, $pro_discription, $pro_PDF_path, $pro_supervisor_id, $recode_added_by ) {

		
        $response = array();
		
        // First check if project already existed in db
        if (!$this->isProjectExists($pro_name)) {
  
            // insert query
			 $stmt = $this->conn->prepare("INSERT INTO project(pro_name, pro_discription, pro_PDF_path, pro_supervisor_id, recode_added_by) values(?, ?, ?, ?, ?)");
			 $stmt->bind_param("sssii", $pro_name, $pro_discription, $pro_PDF_path, $pro_supervisor_id, $recode_added_by );
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
     * Update project
     *
     * @param String $pro_name project name of the system
     * @param String $pro_discription Discription of the Project 
	 * @param String $pro_PDF_path project PDF path of the system 
	 * @param String $pro_supervisor_id project supervisor id of the System
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function updateProject($pro_name, $pro_discription, $pro_PDF_path, $pro_supervisor_id, $recode_added_by) {

		
        $response = array();
        // First check if project already existed in db
        if ($this->isProjectExists($pro_name)) {
            
			//
			$stmt = $this->conn->prepare("UPDATE project set status = 2,  recode_modified_at = now() , recode_modified_by = ?, pro_discription = ?, pro_PDF_path = ?, pro_supervisor_id = ? where pro_name = ? and (status = 1 or status = 1)");
			$stmt->bind_param("issis", $recode_added_by, $pro_discription, $pro_PDF_path, $pro_supervisor_id, $pro_name);
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
     * Delete project
     *
     * @param String $pro_name Project name for the system
	 * @param String $recode_added_by
     *
     * @return database transaction status
     */
    public function deleteProject($pro_name, $recode_added_by) {

		
        $response = array();
        // First check if project already existed in db
        if ($this->isProjectExists($pro_name)) {
           			
			//
			$stmt = $this->conn->prepare("UPDATE project set status = 3, recode_modified_at = now() , recode_modified_by = ? where pro_name = ? and (status = 1 or status = 1)");
			$stmt->bind_param("is",$recode_added_by, $pro_name);
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
     * Fetching project by pro_name
	 *
     * @param String $pro_name Project name
	 *
	 *@return Project object only needed data
     */
    public function getProjectByProjectName($pro_name) {
        $stmt = $this->conn->prepare("SELECT pro_name, pro_discription, pro_PDF_path, pro_supervisor_id, status, recode_added_at, recode_added_by FROM project WHERE pro_name = ? and (status = 1 or status = 1)");
        $stmt->bind_param("s", $pro_name);
        if ($stmt->execute()) {
            $stmt->bind_result($pro_name,  $pro_discription, $pro_PDF_path, $pro_supervisor_id, $status, $recode_added_at, $recode_added_by);
            $stmt->fetch();
            $project = array();
            $project["pro_name"] = $pro_name;
            $project["pro_discription"] = $pro_discription;
			$project["pro_PDF_path"] = $pro_PDF_path;
			$project["pro_supervisor_id"] = $pro_supervisor_id;
            $project["status"] = $status;
            $project["recode_added_at"] = $recode_added_at;
			$project["recode_added_by"] = $recode_added_by;

            $stmt->close();
            return $project;
        } else {
            return NULL;
        }
    }
  
  
	/**
     * Fetching all projects
	 *
     * @return $projects object set of all projects
     */
    public function getAllProjects() {
        $stmt = $this->conn->prepare("SELECT * FROM project WHERE status = 1");
        $stmt->execute();
        $projects = $stmt->get_result();
        $stmt->close();
        return $projects;
    }
	
  
  
  
  
  
/*
 * ------------------------ SUPPORTIVE METHODS ------------------------
 */

	/**
     * Checking for duplicate project by pro_name
     *
     * @param String $pro_name project name to check in db
     *
     * @return boolean
     */
    private function isProjectExists($pro_name) {
		$stmt = $this->conn->prepare("SELECT pro_name from project WHERE (status = 1 or status = 1)  and pro_name = ?  ");
        $stmt->bind_param("s",$pro_name);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return ($num_rows > 0); //if it has more than zero number of rows; then  it sends true
    }

}

?>
