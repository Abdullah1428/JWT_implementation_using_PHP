<?php

    // for jwt we require these two
    ///////////////////////////////
    require_once('vendor/autoload.php');
    use \Firebase\JWT\JWT;
    //////////////////////////////

    include("credentials.php");
	$response = array();	
	if (isset($_GET['cnic']) && isset($_GET['pwd']))
	{	
        try 
        {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            	die("OOPs something went wrong");
        }	
        
        $cnic=$_GET['cnic'];
        $pwd=$_GET['pwd'];
        
        $sql = 'SELECT * FROM customer WHERE  nic = :nic AND password = :pwd';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nic', $cnic, PDO::PARAM_STR);
        $stmt->bindParam(':pwd', $pwd, PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount())
        {
            $customer = array();

            foreach($stmt as $customer){
                $cust_id = $customer['id'];
                $cust_name = $customer['name'];
            
            
                $signature = "Labor!%@$#!";

                $issuer_claim = "Rozgar"; // this can be the servername
                $audience_claim = "labor_recipient";
                $issuedat_claim = time(); // issued at
                $notbefore_claim = $issuedat_claim + 2; //not before in seconds
                //   $expire_claim = $issuedat_claim + 60; // expire time in seconds
                //   "exp" => $expire_claim,

                
                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "data" => array(
                        "id" => $cust_id,
                        "name" => $cust_name,
                ));

                $secret_key = $signature;

                $jwt = JWT::encode($token, $secret_key);

                $response["message"] = "successfull login";
                $response["jwt"] = $jwt;
                $response["id"] = $cust_id;
                $response["name"] = $cust_name;

                echo json_encode($response); 
            }
		
		}
		else
		{
			$response["success"] = 0;
			$response["message"] = "CNIC Or Password Invalid!";
			echo json_encode($response);
        }
	}
	else{
		$response["success"] = 0;
		$response["message"] = "Required fields missing.";
		echo json_encode($response);
	}	
		
?>


