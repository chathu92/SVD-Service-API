<?php
require_once '../../model/user_management/OperationalUserManagement.php';
require_once '../../model/student_guardiant_management/OccupationTypeManagement.php';
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
 * ------------------------ OCCUPATION TYPE TABLE METHODS ------------------------
 */
 
/**
 * Occupation_type Registration
 * url - /occupation_type_register
 * method - POST
 * params - occ_type_name, 	occ_type_description
 */
$app->post('/occupation_type_register',  'authenticate', function() use ($app) {
	
            // check for required params
            verifyRequiredParams(array('occ_type_name', 'occ_type_description' ));
			
			global $currunt_user_id;

            $response = array();

            // reading post params
            $occ_type_name = $app->request->post('occ_type_name');
            $occ_type_description = $app->request->post('occ_type_description');
           
            $occupationTypeManagement = new occupationTypeManagement();
			$res = $occupationTypeManagement->createOccupation_type($occ_type_name, $occ_type_description, $currunt_user_id);
			
            if ($res == CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "Occupation type is successfully registered";
            } else if ($res == CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing occupation type";
            } else if ($res == ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this occupation type already exist";
            }
            // echo json response
            echoRespnse(201, $response);
        });

/**
 * Occupation_type Update
 * url - /occupation_type_updates
 * method - PUT
 * params - occ_type_name, occ_type_description
 */
$app->put('/occupation_type_updates','authenticate', function() use ($app) {
	
             // check for required params
            verifyRequiredParams(array('occ_type_name', 'occ_type_description' ));
			
			global $currunt_user_id;

            $response = array();

            // reading put params
			$occ_type_name = $app->request->put('occ_type_name'); 
            $occ_type_description = $app->request->put('occ_type_description'); 
			
            $occupationTypeManagement = new OccupationTypeManagement();
			$res = $occupationTypeManagement->updateOccupation_type($occ_type_name, $occ_type_description, $currunt_user_id);
			
            if ($res == UPDATE_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "Occupation type is Updated";
            } else if ($res == UPDATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while updating occupation type ";
            } else if ($res == NOT_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry,this occupation type not exist";
            }
            // echo json response
            echoRespnse(201, $response);
        });


/**
 * Occupation_type Delete
 * url - /occupation_type_delete
 * method - DELETE
 * params -occ_type_name
 */
$app->delete('/occupation_type_delete', 'authenticate', function() use ($app) {
	
            // check for required params
            verifyRequiredParams(array('occ_type_name'));
			
			global $currunt_user_id;

			// reading put params
			$occ_type_name = $app->request->delete('occ_type_name'); 
			
            $response = array();

			
			$occupationTypeManagement = new OccupationTypeManagement();
			$res = $occupationTypeManagement->deleteOccupationType($occ_type_name,$currunt_user_id);
			
            if ($res == DELETE_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "Occupation_type is successfully deleted";
            } else if ($res == DELETE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while deleting Occupation_type";
            } else if ($res == NOT_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this Occupation_type is not exist";
            }
            // echo json response
            echoRespnse(201, $response);
        });


		
/**
 * get one occupation_type
 * method GET
 * url /occupation_type/:projectName          
 */
$app->get('/occupation_type/:projectName', 'authenticate', function($occ_type_name) {
            global $currunt_user_id;
            $response = array();
            
			$occupationTypeManagement = new OccupationTypeManagement();
			$res = $occupationTypeManagement->getOccupationTypeByProjectName($occ_type_name);

            $response["error"] = false;
            $response["occupation_type"] = $res;

            

            echoRespnse(200, $response);
        });

/**
 * Listing all projects
 * method GET
 * url /occupation_type     
 */
$app->get('/occupation_type', 'authenticate', function() {
            global $user_id;
			
            $response = array();
			
            $occupationTypeManagement = new OccupationTypeManagement();
			$res = $occupationTypeManagement->getAllProjects();

            $response["error"] = false;
            $response["occupation_type"] = array();

            // looping through result and preparing projects array
            while ($project = $res->fetch_assoc()) {
                $tmp = array();
				
                $tmp["occ_type_name"] = $project["occ_type_name"];
                $tmp["occ_type_description"] = $project["occ_type_description"];
                $tmp["status"] = $project["status"];
                $tmp["recode_added_at"] = $project["recode_added_at"];
				$tmp["recode_added_by"] = $project["recode_added_by"];
				
                array_push($response["occupation_type"], $tmp);
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
