<?php

    require_once('vendor/autoload.php');
    use \Firebase\JWT\JWT;

	include("credentials.php");
	//session_start();
	$response = array();
	try {
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	catch(PDOException $e)
		{
			die("OOPs something went wrong");
        }

    $secret_key = "Labor!%@$#!";
    $jwt = $_GET['jwt'];
    $CNIC = $_GET['CNIC'];	

    if($jwt)
    {
        try{
            $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
            // access is granted so do work here
            $response["access"] = 1;
		    $response["jwt_verified"] = "token verified";   


            $sql1 = 'SELECT * FROM customer where nic=:nic';		
	        $stmt1= $conn->prepare($sql1);
	        $stmt1->bindParam(':nic', $CNIC, PDO::PARAM_STR);
	        $stmt1->execute();	
            if($stmt1->rowCount())
            {	   
                $response["success"] = 1;
		        $response["message"] = "Customer found.";   
	        }
	        else {
		        $response["success"] = 0;
		        $response["message"] = "Customer not found.";
	        }	

            echo json_encode($response);

        }
        catch (Exception $e){
            $response["success"] = 0;
            $response["message"] = "Access denied";
            $response["access"] = 0;
		    $response["jwt_verified"] = "token not verified";  
            $response["error"] = $e->getMessage();
            echo json_encode($response);
        }
    }
    else
    {
        $response["success"] = 0;
            $response["message"] = "No token found";
            $response["success"] = 0;
            echo json_encode($response);
    }
    
?>