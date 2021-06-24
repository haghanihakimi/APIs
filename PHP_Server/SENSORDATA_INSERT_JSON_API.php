<?php
// Author : Haghani Hakimi
header('Content-Type: application/json');
date_default_timezone_set("Australia/Melbourne");

//Sanitize every damn entry right here
function sanitize($array){
    $json_data = json_decode($array,true);
    if(is_array($json_data)){
        $json = [];
        foreach($json_data as $data => $key){
            $json[$data] = filter_var(htmlspecialchars(htmlentities(strip_tags($key),ENT_QUOTES)),FILTER_SANITIZE_STRING);
        }
        return $json;
    }
}

//Database credentials
$server = "localhost";
$username = "root";
$password = "Rdk77anf";
$dbname = "sensordata_db";

$responses = array();

//All json entry data/arrays
$json = sanitize(file_get_contents("php://input"));

//print_r($json);

if($json != null || !empty($json)){

    $connect = new PDO("mysql:host=$server;dbname=$dbname","$username","$password");
    $connect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $IOTSensorLocation = $json["IOTSensorLocation"];
    $Measurement = $json["Measurement"];
    $Value = $json["Value"];

    try{
        //Begin transaction and turn off auto commit
        $connect->beginTransaction();

        //First Insert room data into the table
        $insertData = $connect->prepare("INSERT INTO measureddatav3 (IOTSensorLocation,Measurement,Value,readDate) VALUES (:IOTSensorLocation,:Measurement,:Value,CURRENT_TIMESTAMP)");
        $insertData->bindParam(":IOTSensorLocation",$IOTSensorLocation,PDO::PARAM_STR);
        $insertData->bindParam(":Measurement",$Measurement,PDO::PARAM_STR);
        $insertData->bindParam(":Value",$Value,PDO::PARAM_STR);
        $insertData->execute();

        //Second, select related data from setpoint table and send it back as response
        $selectData = $connect->prepare("SELECT * FROM setpointsv3 WHERE IOTSensorLocation = ? AND measurement = ?");
        $selectData->execute(array($IOTSensorLocation,$Measurement));

        //Check if anything exists in setpoint table
        if($selectData->rowCount()){
            //Go through all available data in the current table
            while($rows = $selectData->fetch(PDO::FETCH_ASSOC)){
                $responses["code"] = "1";
                $responses['message'] = "Data stored successfully";
                $responses["Room_Name"] = $rows['IOTSensorLocation'];
                $responses["Measurement"] = $rows['measurement'];
                $responses["Setpoint"] = $rows['setpoint'];
                $responses["DeadBand"] = $rows['deadband'];
            }
        }else{
            $responses["code"] = "2";
            $responses["message"] = "No room and parameter found!";
        }
        //If all above is correct then commit changes/requests
        $connect->commit();
    }catch(PDOException $exception){
        //If all above IS NOT correct then ignore all request (insert & select)
        $connect->rollBack();
        $responses["code"] = "2";
        $responses["message"] = "Could not process your request.<br/> ".$exception->getMessage();
    }
    $connect = null;
}else{
    $responses["code"] = "2";
    $responses["message"] = "Empty array is sent by client!";
}
echo json_encode($responses);
