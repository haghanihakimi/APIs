# Author: Haghani Hakimi

# ref - https://docs.python.org/3/howto/sockets.html
import socket
import json
import string
import random

import urllib.parse 
import requests 

#This is a function to generate letters and numbers for room names
def RandomRoom():
    rooms = ["E255","E220","E221"]
    randomRoom = random.choice(rooms)
    return randomRoom

#This function picks a random measurement from array
RandomMeasurement = random.choice(["Humidity","Temperature","CO2","Outside Temp"])

#This function generates a random value based on chosen measurement
def Values(measure):
    randomValue = 0
    if measure == "Humidity":
        randomValue = random.randint(30,60)
    elif measure == "Temperature":
        randomValue = round(random.uniform(-5.00,55.00),2)
    elif measure == "CO2":
        randomValue = random.randint(300,800)
    elif measure == "Outside Temp":
        randomValue = round(random.uniform(-5.00,55.00),2)
    return randomValue

# VM DHCP Lease in HHK HOME Network
host = 'ipaddress'
port = 80

contentStr='{"IOTSensorLocation": "%s","Measurement": "%s","Value": "%i"}'%(RandomRoom(),RandomMeasurement,Values(RandomMeasurement))

# url = 'http://192.168.100.100/i40Test/v2/InsertJsonRESTData.php' 
# note change the URL to match your server IP address
url = "http://ipaddress/SENSORDATA_INSERT_JSON_API.php" 


json_data = requests.post(url,data = contentStr).json()
print("Sent Data: ",contentStr)
print("\nReceived Data: ",json_data)
code = json_data['code']
message = json_data['message']
#print("code = {0} : message = {1}".format(code,message))
#print("code = {0} : message = {1} : Meas={2} : Setpoint={3} : Deadband={4}".format(json_data['code'],json_data['message'],json_data['Room_Name'],json_data['Measurement'],json_data['Setpoint'],json_data['DeadBand']))     # extract the json data
