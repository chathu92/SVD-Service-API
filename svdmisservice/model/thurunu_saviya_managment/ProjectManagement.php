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
     * @param String $exm_name Exam name for the system
     * @param String $exm_discription Discription of the Exam
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function updateProject($pro_name, $pro_discription, $pro_PDF_path, $pro_supervisor_id, $recode_added_by) {

		
        $response = array();
        // First check if exam already existed in db
        if ($this->isExamExists($exm_name)) {
            
			//
			$stmt = $this->conn->prepare("UPDATE exam set status = 2,  recode_modified_at = now() , recode_modified_by = ? where exm_name = ? and status = 1");
			$stmt->bind_param("is", $recode_added_by, $exm_name);
			$result = $stmt->execute();
			
            // insert updated recode
			$stmt = $this->conn->prepare("INSERT INTO exam(exm_name, exm_discription, recode_added_by) values(?, ?, ?)");
			$stmt->bind_param("ssi", $exm_name, $exm_discription, $recode_added_by );
			$result = $stmt->execute();

			$stmt->close();

        } else {
            // exam is not already existed in the db
            return NOT_EXISTED;
        }
		
         

        // Check for successful update
        if ($result) {
			// exam successfully update
            return UPDATE_SUCCESSFULLY;
        } else {
            // Failed to update exam
            return UPDATE_FAILED;
        }
        
		return $response;

    }
	
/**
     * Delete exam
     *
     * @param String $exm_name Exam name for the system
	 * @param String $recode_added_by
     *
     * @return database transaction status
     */
    public function deleteExam($exm_name, $recode_added_by) {

		
        $response = array();
        // First check if exam already existed in db
        if ($this->isExamExists($exm_name)) {
           			
			//
			$stmt = $this->conn->prepare("UPDATE exam set status = 3, recode_modified_at = now() , recode_modified_by = ? where exm_name = ? and status=1");
			$stmt->bind_param("is",$recode_added_by, $exm_name);
			$result = $stmt->execute();
			
            $stmt->close();

        } else {
            // Exam is not already existed in the db
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
     * Fetching exam by exm_name
	 *
     * @param String $exm_name Exam name
	 *
	 *@return Exam object only needed data
     */
    public function getExamByExamName($exm_name) {
        $stmt = $this->conn->prepare("SELECT exm_name, exm_discription, status, recode_added_at, recode_added_by FROM exam WHERE exm_name = ? and status=1");
        $stmt->bind_param("s", $exm_name);
        if ($stmt->execute()) {
            $stmt->bind_result($exm_name,  $exm_discription, $status, $recode_added_at, $recode_added_by);
            $stmt->fetch();
            $exam = array();
            $exam["exm_name"] = $exm_name;
            $exam["exm_discription"] = $exm_discription;
            $exam["status"] = $status;
            $exam["recode_added_at"] = $recode_added_at;
			$exam["recode_added_by"] = $recode_added_by;

            $stmt->close();
            return $exam;
        } else {
            return NULL;
        }
    }
  
  
	/**
     * Fetching all exams
	 *
     * @return $exams boject set of all exams
     */
    public function getAllExams() {
        $stmt = $this->conn->prepare("SELECT * FROM exam WHERE status = 1");
        $stmt->execute();
        $exams = $stmt->get_result();
        $stmt->close();
        return $exams;
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
		$stmt = $this->conn->prepare("SELECT pro_name from project WHERE status = 1 and pro_name = ?  ");
        $stmt->bind_param("s",$pro_name);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return ($num_rows > 0); //if it has more than zero number of rows; then  it sends true
    }

}

?>
