<?php
require_once '../../model/user_management/OperationalUserManagement.php';
require_once '../../model/student_guardiant_management/GuardianManagement.php';
require '../.././config/libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$currunt_user_id = NULL;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
   // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $operationalUserManagement = new OperationalUserManagement();

        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$operationalUserManagement->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $currunt_user_id;
            // get user primary key id
            $currunt_user_id = $operationalUserManagement->getUserId($api_key);
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoRespnse(400, $response);
        $app->stop();
    }
}

/*
 * ------------------------ GUARDIAN TABLE METHODS------------------------
 */
/**
 * Guardian Registration
 * url - /guardian_register
 * method - POST
 * params - gur_name, gur_phone_number, gur_adress, gur_email_address, gur_occupation, gur_occupation _type, gur_office_address, gur_office_phone_number, gar_nic, gar_tea_id
 */
$app->post('/guardian_register',   function() use ($app) {
	
            // check for required params
            verifyRequiredParams(array('gur_name' ));
			
			global $currunt_user_id;

            $response = array();

            // reading post params
            $gur_name = $app->request->post('gur_name');
            $gur_phone_number = $app->request->post('gur_phone_number');
            $gur_adress = $app->request->post('gur_adress');
			$gur_email_address = $app->request->post('gur_email_address');
            $gur_occupation = $app->request->post('gur_occupation');
			$gur_occupation_type = $app->request->post('gur_occupation_type');
            $gur_office_address = $app->request->post('gur_office_address');
			$gur_office_phone_number = $app->request->post('gur_office_phone_number');
            $gur_stu_addmision_number = $app->request->post('gur_stu_addmision_number');
			$gur_old_student_number = $app->request->post('gur_old_student_number');
            $gur_other_interactions_with_dp = $app->request->post('gur_other_interactions_with_dp');
			$gur_nic = $app->request->post('gur_nic');
            $gur_tea_id = $app->request->post('gur_tea_id');
           
            $guardianManagement = new GuardianManagement();
			$res = $guardianManagement->createGuardian($gur_name, $gur_phone_number,$gur_adress, $gur_email_address,$gur_occupation, $gur_occupation_type,$gur_office_address, $gur_office_phone_number,$gur_stu_addmision_number, $gur_old_student_number,$gur_other_interactions_with_dp, $gur_nic, $gur_tea_id,1);
			
            if ($res == CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "gardian is successfully registered";
            } else if ($res == CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing gardian";
            } else if ($res == ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this gardian  already exist";
            }else{
				$response["error"] = false;
                $response["message"] = $res;
			}
            // echo json response
            echoRespnse(201, $response);
        });

/**
 * Guardian  Update
 * url - /guardian _update
 * method - PUT
 * params - gur_name, gur_phone_number, gur_adress, gur_email_address, gur_occupation, gur_occupation _type, gur_office_address, gur_office_phone_number, gar_nic, gar_tea_id
 */
$app->put('/guardian_update/:userName','authenticate', function($gur_name) use ($app) {
	
             // check for required params
            verifyRequiredParams(array('gur_name', 'gur_phone_number', 'gur_adress','gur_email_address', 'gur_occupation', 'gur_occupation_type', 'gur_office_address', 'gur_office_phone_number','gur_stu_addmision_number', 'gur_old_student_number', 'gur_other_interactions_with_dp', 'gur_nic', 'gur_tea_id'));
			
			global $currunt_user_id;

            $response = array();

            // reading put params
			$gur_name = $app->request->put('gur_name');
            $gur_phone_number = $app->request->put('gur_phone_number');
            $gur_adress = $app->request->put('gur_adress');
			$gur_email_address = $app->request->put('gur_email_address');
            $gur_occupation = $app->request->put('gur_occupation');
			$gur_occupation_type = $app->request->put('gur_occupation_type');
            $gur_office_address = $app->request->put('gur_office_address');
			$gur_office_phone_number = $app->request->put('gur_office_phone_number');
            $gur_stu_addmision_number = $app->request->put('gur_stu_addmision_number');
			$gur_old_student_number = $app->request->put('gur_old_student_number');
            $gur_other_interactions_with_dp = $app->request->put('gur_other_interactions_with_dp');
			$gur_nic = $app->request->put('gur_nic');
            $gur_tea_id = $app->request->put('gur_tea_id');
      
			$guardianManagement = new GuardianManagement();
			$res = $guardianManagement->updateGuardianManagement($gur_name, $gur_phone_number,$gur_adress, $gur_email_address,$gur_occupation, $gur_occupation_type,$gur_office_address, $gur_office_phone_number,$gur_stu_addmision_number, $gur_old_student_number,$gur_other_interactions_with_dp, $gur_nic, $gur_tea_id,$currunt_user_id);
            
			
            if ($res == UPDATE_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "gardian is successfully Updated";
            } else if ($res == UPDATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while updating gardian ";
            } else if ($res == NOT_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry,this guardian not exist";
            }
            // echo json response
            echoRespnse(201, $response);
        });

/**
 * Guardian Delete
 * url - /guardian_delete
 * method - DELETE
 * params - gur_name
 */
$app->delete('/guardian_delete/:userName', 'authenticate', function($gur_name) use ($app) {
	
            // check for required params
            verifyRequiredParams(array('$gur_name'));
			
			global $currunt_user_id;

			// reading put params
			$gur_name = $app->request->delete('gur_name'); 
			
            $response = array();

			
			$guardianManagement = new GuardianManagement();
			$res = $guardianManagement->deleteGuardianManagement($gur_name,$currunt_user_id);
			
            if ($res == DELETE_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "Guardian is successfully deleted";
            } else if ($res == DELETE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while deleting Guardian";
            } else if ($res == NOT_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this Guardian is not exist";
            }
            // echo json response
            echoRespnse(201, $response);
        });


		
/**
 * Listing all guardian
 * method GET
 * url /guardian       
 */
$app->get('/:gur_name', 'authenticate', function($gur_name) {
            global $currunt_user_id;
            $response = array();
            
			$guardianManagement = new GuardianManagement();
			$res = $guardianManagement->getGurdianByGurdianName($gur_name);

            $response["error"] = false;
            $response["guardian"] = $res;

            

            echoRespnse(200, $response);
        });

/**
 * Listing all projects
 * method GET
 * url /guardian     
 */
$app->get('/guardian', 'authenticate', function() {
            global $user_id;
			
            $response = array();
			
            $guardianManagement = new GuardianManagement();
			$res = $guardianManagement->getAllProjects();

            $response["error"] = false;
            $response["guardian"] = array();

            // looping through result and preparing projects array
            while ($guardian = $res->fetch_assoc()) {
                $tmp = array();
				$tmp["gur_name"] = $guardian["gur_name"];
                $tmp["gur_adress"] = $guardian["gur_adress"];
                $tmp["gur_email_address"] = $guardian["gur_email_address"];
                $tmp["gur_occupation"] = $guardian["gur_occupation"];
				$tmp["gur_occupation _type"] = $guardian["gur_occupation _type"];
                $tmp["gur_office_address"] = $guardian["gur_office_address"];
                $tmp["gur_office_phone_number"] = $guardian["gur_office_phone_number"];
				$tmp["gur_stu_addmision_number"] = $guardian["gur_stu_addmision_number"];
			    $tmp["gur_old_student_number"]=$guardian["gur_old_student_number"];
				$tmp["gur_other_interactions_with_dp"]=$guardian["gur_other_interactions_with_dp"];
                $tmp["gur_nic"] = $guardian["gur_nic"];
				$tmp["gur_tea_id"] = $guardian["gur_tea_id"];
				$tmp["status"] = $guardian["status"];
				$tmp["gur_tea_id"] = $guardian["gur_tea_id"];
				$tmp["recode_added_at"] = $guardian["recode_added_at"];
				$tmp["recode_added_by"] = $guardian["recode_added_by"];
				
				
                array_push($response["guardian"], $tmp);
            }

            echoRespnse(200, $response);
        });		
				

/*
 * ------------------------ SUPPORTIVE METHODS ------------------------
 */				
				
/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
	if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>
