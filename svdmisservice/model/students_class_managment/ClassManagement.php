<?php
/**
 * Class to handle all the exam details
 * This class will have CRUD methods for exam
 *
 * @author Randi Kodikara
 *
 */

class ClassManagement {

    private $conn;

    function __construct() {
        require_once '../../model/commen/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
	
	
/*
 * ------------------------ CLASS TABLE METHODS ------------------------
 */

    /**
     * Creating new class
     *
     * @param Int $clz_grade Class grade for the system
     * @param String $clz_class Discription of the Class
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function createClass($clz_grade, $clz_class,$recode_added_by ) {

		
        $response = array();
		
        // First check if class already existed in db
        if (!$this->isClassExists($clz_grade,$clz_class)) {
  
            // insert query
			 $stmt = $this->conn->prepare("INSERT INTO class(clz_grade, clz_class, recode_added_by) values(?, ?, ?)");
			 $stmt->bind_param("isi", $clz_grade, $clz_class, $recode_added_by );
			 $result = $stmt->execute();

			 $stmt->close();

        } else {
            // Class is not already existed in the db
            return ALREADY_EXISTED;
        }
		
         

        // Check for successful insertion
        if ($result) {
			// class successfully inserted
            return CREATED_SUCCESSFULLY;
        } else {
            // Failed to create class
            return CREATE_FAILED;
        }
        
		return $response;

    }
	
	/**
     * Update class
     *
     * @param Int $clz_grade Class name for the system
     * @param String $clz_class Discription of the Class
	 * @param String $recode_added_by 
     *
     * @return database transaction status
     */
    public function updateClass($clz_grade, $clz_class,$recode_added_by) {

		
        $response = array();
        // First check if class already existed in db
        if ($this->isClassExists($clz_grade)) {
            
			//
			$stmt = $this->conn->prepare("UPDATE class set status = 2,  recode_modified_at = now() , recode_modified_by = ? where clz_grade = ? and status = 1");
			$stmt->bind_param("is", $recode_added_by, $clz_grade);
			$result = $stmt->execute();
			
            // insert updated recode
			$stmt = $this->conn->prepare("INSERT INTO class(clz_grade, clz_class, recode_added_by) values(?, ?, ?)");
			$stmt->bind_param("isi", $clz_grade, $clz_class, $recode_added_by );
			$result = $stmt->execute();

			$stmt->close();

        } else {
            // class is not already existed in the db
            return NOT_EXISTED;
        }
		
         

        // Check for successful update
        if ($result) {
			// class successfully update
            return UPDATE_SUCCESSFULLY;
        } else {
            // Failed to update class
            return UPDATE_FAILED;
        }
        
		return $response;

    }
	
/**
     * Delete class
     *
     * @param Int $clz_grade Class name for the system
	 * @param String $clz_class Discription of the Class
	 * @param String $recode_added_by
     *
     * @return database transaction status
     */
    public function deleteExam($clz_grade, $recode_added_by) {

		
        $response = array();
        // First check if exam already existed in db
        if ($this->isExamExists($clz_grade)) {
           			
			//
			$stmt = $this->conn->prepare("UPDATE exam set status = 3, recode_modified_at = now() , recode_modified_by = ? where exm_name = ? and status=1");
			$stmt->bind_param("is",$recode_added_by, $clz_grade);
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
     * Checking for duplicate class by clz_grade and clz_class
     *
     * @param Int $clz_grade class grade to check in db
     * @param String $clz_class class class to check in db
     * @return boolean
     */
    private function isClassExists($clz_grade, $clz_class) {
		//$exm_name = "exm1";
		$stmt = $this->conn->prepare("SELECT clz_grade from class WHERE status = 1 and clz_grade = ? and clz_class = ?  ");
        $stmt->bind_param("is",$clz_grade, $clz_class);
        $stmt->execute();
		$stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return ($num_rows > 0); //if it has more than zero number of rows; then  it sends true
    }

}

?>
