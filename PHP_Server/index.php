<?php
//Author: Haghani Hakimi
date_default_timezone_set("Australia/Melbourne");
$server = "localhost";
$username = "root";
$password = "Rdk77anf";
$dbname = "sensordata_db";
?>
<html>
    <header>
        <title>Live Sensor Data</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    </header>

    <body>

    <table class="table table-bordered table-hover" style="text-align: center">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Room Name</th>
            <th scope="col">Room Measurement</th>
            <th scope="col">Value</th>
            <th scope="col">Set-Point</th>
            <th scope="col">Dead-Band</th>
            <th scope="col">Read Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $connect = new PDO("mysql:host=$server;dbname=$dbname","$username","$password");
        $connect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $unit = "";

            $select = $connect->query("SELECT measureddatav3.id as room_id,measureddatav3.IOTSensorLocation as SensorLocation,
            measureddatav3.Measurement as rmeasurement,measureddatav3.Value as rValue,measureddatav3.readDate as rDate,
            setpointsv3.setpoint as setpoint,setpointsv3.deadband as deadband 
            FROM measureddatav3,setpointsv3 WHERE 
            measureddatav3.IOTSensorLocation = setpointsv3.IOTSensorLocation AND 
             measureddatav3.Measurement = setpointsv3.measurement ORDER BY measureddatav3.id ASC");
            if($select->rowCount()){
                while($rows = $select->fetch(PDO::FETCH_ASSOC)){
                    switch($rows["rmeasurement"]){
                        case "Temperature":
                            $unit = "°C";
                            break;
                        case "Humidity":
                            $unit = "%";
                            break;
                        case "CO2":
                            $unit = "PPM";
                            break;
                        case "Light Level":
                            $unit = "LUX";
                            break;
                        case "Outside Temp":
                            $unit = "°C";
                            break;
                        default:
                            $unit = "Unknown Unit";
                            break;
                    }
                    echo '
                        <tr>
                            <th scope="row">'.$rows['room_id'].'</th>
                            <td>'.$rows['SensorLocation'].'</td>
                            <td>'.$rows['rmeasurement'].'</td>
                            <td>'.sprintf("%02d", $rows['rValue']).''.$unit.'</td>
                            <td>'.sprintf("%02d", $rows['setpoint']).''.$unit.'</td>
                            <td>'.sprintf("%02d", $rows['deadband']).''.$unit.'</td>
                            <td>'.date_format(date_create($rows['rDate']),"d/m/Y - h :i:s A").'</td>
                        </tr>
                    ';
                }
            }
            $connect = null;
        ?>
        </tbody>
    </table>
    </body>
</html>
