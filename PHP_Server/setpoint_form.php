<?php
	//Author: Cameron Smith
	date_default_timezone_set("Australia/Melbourne");
	function sanitizer($value) {
		$value = filter_var(htmlspecialchars(htmlentities(strip_tags($value),ENT_QUOTES)),FILTER_SANITIZE_STRING);
		return $value;
	}

	//Database credentials
	$server = "localhost";
	$username = "root";
	$password = "";
	$dbname = "industry40db";

	//Global Errors
	$errors = array();

	if(sanitizer(isset($_POST['submit_setpoint']))) {
		//Sanitize inputs and declare variables equal to inputs
		$location = sanitizer(strtoupper($_POST['sensor_location']));
		$measurement = sanitizer($_POST['measurement']);
		$setpoint = sanitizer($_POST['setpoint_value']);
		$deadband = sanitizer($_POST['deadband_value']);

		//Statements to check if fields are not empty
		if(empty($location)) {
			$errors["empty_location"] = "&#10007;&nbsp;Please fill Sensor Location field.";
		}
		
		if(empty($measurement)) {
			$errors["empty_measurement"] = "&#10007;&nbsp;Please fill Measurement field.";
		}
		
		if(empty($setpoint)) {
			
			$errors["empty_setpoint"] = "&#10007;&nbsp;Please fill Set-Point field.";
		}
		
		if(empty($deadband)) {
			$errors["empty_deadband"] = "&#10007;&nbsp;Please fill Dead-Band field.";
		}


		//Statements to check if fields contain correct type of value
		if(!preg_match('/^[A-Za-z0-9]+$/', $location) || strlen($location) != 4) {
			$errors["invalid_location"] = "&#10007;&nbsp;Sensor Location field must be letters and numbers and
			the length must be exactly 4 characters!";
		}
		
		if(!preg_match('/^[A-Za-z0-9 ]+$/', $measurement) || strlen($measurement) < 3 || strlen($measurement) > 20) {
			$errors["invalid_measurement"] = "&#10007;&nbsp;Measurement value must be letters, spaces, and
			the length must be between 3 to 20 characters!";
		}
		
		if(!preg_match('/^[0-9-\/]+$/i', $setpoint) || strlen($setpoint) < 2 || strlen($setpoint) > 3) {
			$errors["empty_setpoint"] = "&#10007;&nbsp;Set-Point value must be numbers and the length must be
			between 2 to 3 characters.";
		}
		
		if(!preg_match('/^[0-9-\/]+$/i', $deadband) || strlen($deadband) < 2 || strlen($deadband) > 3) {
			$errors["invalid_deadband"] = "&#10007;&nbsp;Dead-Band value must be numbers and the length must be between
			2 to 3 characters.";
		}

		if(empty($errors)) {
			//Establish PDO Connection to database
			$connect = new PDO("mysql:host=localhost;dbname=$dbname","$username","$password");
			$connect->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

			//Try statement to insert data
			try {
				//Begin transaction & turn off auto commit
				$connect->beginTransaction();

				$insert = $connect->prepare("INSERT INTO setpointsv3 (IOTSensorLocation,measurement,setpoint,deadband,setDate) 
												VALUES (?,?,?,?,CURRENT_TIMESTAMP)");
				$insert->execute(array($location,$measurement,$setpoint,$deadband));
				$errors["insertion_success"] = "Data inserted successfully!";

				//Commit if all above statements are correct
				$connect->commit();
				
			} catch(PDOException $exception) {
				$connect->rollBack();
				$errors["insertion_failure"] = "Data could not insert successfully!<br/>".$exception->getMessage();
			}
			
			$connect = null;
		}
	}
?>



<html>
    <head>
        <title>Set-Point WebForm</title>
        <style type="text/css">
            *{
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }
			
            html,body {
                background-color: #f7f7f7;
                width:100%;
                height: 100%;
                padding: 0;
                margin: 0;
            }
			
            .middle_section {
                background-color: #FFFFFF;
                position: relative;
                margin: 24px auto auto auto;
                width: 100%;
                height: auto;
                max-width: 512px;
                border-radius: 4px;
                border: 1px solid hsla(212, 100%, 13%, 0.15);
            }
			
            .middle_section form {
                position:relative;
                width:100%;
                height: auto;
                padding: 12px;
                margin:0;
            }
			
            .labels {
                font-family: "Arial",sans-serif;
                font-size: 16px;
                position: relative;
                display: block;
                letter-spacing: 1px;
                margin: 0 0 12px 0;
                color: #002857;
                padding: 8px;
                border-bottom: 1px solid hsla(212, 100%, 13%, 0.15);
            }
			
            .inputs_text {
                background-color: #f7f7f7;
                display: block;
                width:300px;
                height: 45px;
                padding: 8px;
                margin: 0 0 12px 0;
                font-family: "Arial",sans-serif;
                font-size: 16px;
                letter-spacing: 1px;
                color: #002857;
                border-radius: 4px;
                border: 1px solid hsla(212, 100%, 13%, 0.15);
            }
			
            button {
                background-color: #007fff;
                padding: 14px;
                width:100%;
                border-radius: 4px;
                font-family: "Arial",sans-serif;
                font-size: 17px;
                letter-spacing: 1px;
                font-weight: bold;
                color: #f7f7f7;
                cursor: pointer;
                margin: 0;
                border: 1px solid hsla(212, 100%, 13%, 0.15);
            }
			
            ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
			
            ul li {
                display: block;
                padding: 12px;
                margin: 0;
                font-family: "Arial",sans-serif;
                font-size: 16px;
                letter-spacing: 1px;
                color: #002857;
            }
			
            .cmanual {
                position: relative;
                width:100%;
                height:auto;
                font-family: "Arial",sans-serif;
                letter-spacing: 1px;
                margin: 24px 0 0 0;
                border-top: 1px solid hsla(212, 100%, 13%, 0.15);
            }
			
            .cmanual h2 {
                color: ROYALBLUE;
                text-transform: capitalize;
                padding: 8px;
                margin: 0;
            }
			
            .cmanual h4 {
                padding: 8px 16px 8px 16px;
                margin: 0;
                text-transform: capitalize;
                color: MIDNIGHTBLUE;
                font-weight: bold;
            }
			
            .cmanual p {
                padding: 8px 24px 8px 24px;
                margin: 0;
                font-size: 15px;
                color: #111111;
                border-bottom: 1px solid hsla(212, 100%, 13%, 0.15);
            }
			
        </style>
    </head>



    <body>
        <div class="middle_section">
		
            <form action="setpoint_form.php" method="post" enctype="multipart/form-data">
                <label class="labels">Set IOT Sensor Location</label>
                <input type="text" name="sensor_location" class="inputs_text" placeholder="Valid Room Name" required>
				
                <label class="labels">Set Measurement</label>
                <input type="text" name="measurement" class="inputs_text" placeholder="Valid Measurement, e.g. Humidity" required>
				
                <label class="labels">Set-Point Value</label>
                <input type="number" name="setpoint_value" class="inputs_text" placeholder="Valid SetPoint, e.g. 26" required>
				
                <label class="labels">Set DeadBand</label>
                <input type="number" name="deadband_value" class="inputs_text" placeholder="Valid DeadBand, e.g. 36" required>
				
                <label class="labels">Date&nbsp;&amp;&nbsp;Time</label>
                <input type="text" value="<?php echo date("h:i:s.A - d/m/Y"); ?>" style="cursor: not-allowed" class="inputs_text" disabled>
				
                <button role="button" name="submit_setpoint" type="submit">Submit Set-Point</button>
                
				<?php
					
					// for errors
                    if(is_array($errors) && !empty($errors)) {
                        echo '<ul>';
						
                        foreach($errors as $error => $key) {
                            echo '<li>'.$key.'</li>';
                        };
						
                        echo '</ul>';
                        $errors = array();
                    }
					
                ?>
				
                <section class="cmanual">
                    <h2>Measurements unit guide</h2>
                    <h4>&#10033;&nbsp;CO2 - unit > PPM</h4>
                    <p>&#10148;&nbsp;<strong>250 - 400 PPM:</strong>&nbsp;Normal background concentration in outdoor ambient air</p>
                    <p>&#10148;&nbsp;<strong>400 - 1000 PPM:</strong>&nbsp;Concentrations typical of occupied indoor spaces with good air exchange</p>
                    <p>&#10148;&nbsp;<strong>1000 - 2000 PPM:</strong>&nbsp;Complaints of drowsiness and poor air.</p>
                    <p>&#10148;&nbsp;<strong>2000 - 5000 PPM</strong>&nbsp;Headaches, sleepiness and stagnant, stale, stuffy air. Poor concentration, loss of attention, increased heart rate and slight nausea may also be present.</p>
                    <p>&#10148;&nbsp;<strong>5000 PPM:</strong>&nbsp;Workplace exposure limit (as 8-hour TWA) in most jurisdictions.</p>
                    <p>&#10148;&nbsp;<strong>>40000 PPM:</strong>&nbsp;Exposure may lead to serious oxygen deprivation resulting in permanent brain damage, coma, even death.</p>


                    <h4>&#10033;&nbsp;Humidity - unit > Percentage (%)</h4>
                    <p>&#10148;&nbsp;<strong>Starting &amp; Ending points: </strong>&nbsp;It starts from 0 to 100 percents.</p>
                    <p>&#10148;&nbsp;<strong>Indoor ideal levels: </strong>&nbsp;Most comfortable indoor humidity level is 30% to 50%. However, some people set it to 30% to 60%.</p>


                    <h4>&#10033;&nbsp;Light Level - unit > LUX</h4>
                    <p>&#10148;&nbsp;<strong>300 - 500 LUX:</strong>&nbsp;General Classroom</p>
                    <p>&#10148;&nbsp;<strong>500 - 750 LUX:</strong>&nbsp;Laboratory - Classroom</p>
                    <p>&#10148;&nbsp;<strong>750 - 1200 LUX:</strong>&nbsp;Laboratory - Professional</p>
                    <p>&#10148;&nbsp;<strong>200 - 500 LUX:</strong>&nbsp;Library - Stacks</p>
                    <p>&#10148;&nbsp;<strong>300 - 500 LUX:</strong>&nbsp;Library - Reading &amp; Studying</p>
                    <p>&#10148;&nbsp;<strong>300 - 750 LUX:</strong>&nbsp;Workshop</p>
                    <p>&#10148;&nbsp;<strong>50 - 100 LUX:</strong>&nbsp;Stairway</p>
                    <p>&#10148;&nbsp;<strong>100 - 300 LUX:</strong>&nbsp;Restroom &amp; Toilets</p>
                    <p>&#10148;&nbsp;<strong>300 - 500 LUX:</strong>&nbsp;Open Office</p>
                    <p>&#10148;&nbsp;<strong>100 - 300 LUX:</strong>&nbsp;Closed Office</p>
                    <p>&#10148;&nbsp;<strong>200 - 300 LUX:</strong>&nbsp;Cafeteria - Eating</p>
                    <p>&#10148;&nbsp;<strong>50 - 100 LUX:</strong>&nbsp;Corridor</p>


                    <h4>&#10033;&nbsp;Indoor Temperature - unit > Celsius (°C)</h4>
                    <p>&#10148;&nbsp;<strong>Summer:</strong>&nbsp;If humidity = 30% then temperature = 24.5 - 28 °C.<br/>
                    If humidity = 60% then temperature = 23 - 25.5 °C</p>
                    <p>&#10148;&nbsp;<strong>Winter:</strong>&nbsp;If humidity = 30% then temperature = 20.25 - 25.5 °C.<br/>
                        If humidity = 60% then temperature = 20 - 24 °C</p>
                </section>
            </form>
        </div>
    </body>
</html>
