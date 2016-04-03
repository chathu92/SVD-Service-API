<?php
require_once '../../model/commen/PassHash.php';
/**
 * Class to handle all the exam details
 * This class will have CRUD methods for exam
 *
 * @author Hasitha Lakmal
 *
 */

class ExamManagement {

    private $conn;

    function __construct() {
        require_once '../../model/commen/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	
	
/*
 * ------------------------ EXAM TABLE METHODS ------------------------
 */

    /**
     * Creating new exam
     *
     * @param String $exm_name Exam name for the system
     * @param String $exm_discription Discription of the Exam
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function createExam($exm_name, $exm_discription,$recode_added_by ) {

		
        $response = array();
		
        // First check if exam already existed in db
        if (!$this->isExamExists($exm_name)) {
  
            // insert query
			 $stmt = $this->conn->prepare("INSERT INTO exam(exm_name, exm_discription, recode_added_by) values(?, ?, ?)");
			 $stmt->bind_param("ssi", $exm_name, $exm_discription, $recode_added_by );
			 $result = $stmt->execute();

			 $stmt->close();

        } else {
            // Exam is not already existed in the db
            return ALREADY_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// exam successfully inserted
            return CREATED_SUCCESSFULLY;
        } else {
            // Failed to create exam
            return CREATE_FAILED;
        }
        
		return $response;

    }
	
	/**
     * Update exam
     *
     * @param String $exm_name Exam name for the system
     * @param String $exm_discription Discription of the Exam
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function updateExam($exm_name, $exm_discription,$recode_added_by) {

		
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
     * Checking for duplicate exam by exm_name
     *
     * @param String $exm_name exam name to check in db
     *
     * @return boolean
     */
    private function isExamExists($exm_name) {
		$stmt = $this->conn->prepare("SELECT exm_name from exam WHERE status = 1 and exm_name = ?  ");
        $stmt->bind_param("s",$exm_name);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return ($num_rows > 0); //if it has more than zero number of rows; then  it sends true
    }

}

?>
