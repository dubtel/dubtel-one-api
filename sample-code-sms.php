<?php
    // Sample Code to Send SMS with the Dubtel One REST API

    // Include the PHP dubtelOne Library
    require "dubtel-one.php";
    
    // Set our Registration Info
    $applID  = "1010101010101010101010";  // Your APPID from Dubtel
    $applKEY = "ATTATTATTATTATTATTATTATT";  // Your PASSPHRASE from Dubtel
    
    // Set CallerID: You can use any CallerID, Leave blank to use your Dubtel One Number
    $callerID = '6145590061';
    
    // Create a new Dubtel One Rest Client Object
    $client = new dubtelOneClient($applID, $applKEY);

    /* 
     * Send an SMS to "614-559-0062"
     */
    $params = array(
    	"Caller" => $callerID, 	      	  // SMS Caller ID (CallerID)
    	"Called" => "6145590062",	  // The Phone number you are sending SMS to
					  // (SMS Recepient)
	"Message" => "Hello World",
    	"GateWay" => "http://api.dubtel.com"
    );
    
    // Send SMS to "6145590062"
    $result = $client->Send("sms", $params);

    // Check result for issues (Success/Error)
    if($result->_isError)
    {
    	echo "Error Sending SMS: {$result->_errorMessage}\n";
	// print_r($result);
    }
    else
    {
    	echo "SMS Sent Successfully: {$result->_responseXml->sms->ssid}\n";
    } 

?>
