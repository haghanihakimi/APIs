<?php
//Author: Cameron Smith
//Modified: Haghani Hakimi

//Server Credentials
$servername = "localhost";
$username = "root";
$password = "Rdk77anf";
$database = "sensordata_db";

//Create and establish server connection
$connect = new PDO("mysql:host=$servername;dbname=$database","$username","$password");
$connect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//Lets try it!
try{
    //Begin transaction and turn of auto commit
	$connect->beginTransaction();

	//Select data
	$select1 = $connect->query("SELECT * FROM measureddatav3");
	$select1->execute();

	//Create and open a CSV file named "database_file_roomdata" and keep prepared it to write!
	$RoomDataFile = fopen('database_file_roomdata.csv', 'w');
	if($select1->rowCount()){
		while($rows = $select1->fetch(PDO::FETCH_ASSOC)){
		    //Write data from database into "database_file_roomdata" CSV file
			fputcsv($RoomDataFile,$rows);
		}
	}

	//Close created/open file to avoid any more writing
	fclose($RoomDataFile);

    //Select data
	$select2 = $connect->query("SELECT * FROM setpointsv3");
	$select2->execute();

    //Create and open a CSV file named "database_file_setpoints" and keep prepared it to write!
	$SetPointData = fopen("database_file_setpoints.csv","w");
	if($select2->rowCount()){
	    while($rows = $select2->fetch(PDO::FETCH_ASSOC)){
            //Write data from database into "database_file_setpoints" CSV file
	        fputcsv($SetPointData,$rows);
        }
    }

    //Close created/open file to avoid any more writing
	fclose($SetPointData);

	echo "<strong>database_file_roomdata.csv</strong> and <strong>database_file_setpoints.csv</strong> files created successfully!";

	//Commit requests
	$connect->commit();
}catch(PDOException $exception){
    $connect->rollBack();
	echo "Unfortunately CSV file generating failed! ".$exception->getMessage();
}

$connect = null;
