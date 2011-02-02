<?php
    // Sample Code to Place OutBound Calls with the Dubtel One REST API	
    // This Sample will Playback Audio file to Party

    // Include the PHP dubtelOne Library
    require "dubtel-one.php";
    
    // Set our Registration Info
    $applID  = "7234589657746256";  // Your APPID from Dubtel
    $applKEY = "2b47fb821da8ed84b223d678cf22ab03";  // Your PASSPHRASE from Dubtel
    
    // Set CallerID: You can use any CallerID, Leave blank to use your Dubtel One Number
    $callerID = '6145590061';
    
    // Create a new Dubtel One Rest Client Object
    $client = new dubtelOneClient($applID, $applKEY);

    /* 
     * Place a new OutBound Call to "614-559-0062"
     */
    $params = array(
    	"Caller" => $callerID, 	      	  // Outgoing Caller ID (CallerID)
    	"Called" => "6143097502",	  // The Phone number you are calling (Calling Number)
    	"GateWay" => "http://api.dubtel.com"
    );
    
    // Place Call to "614-559-0062"
    $result = $client->Send("call", $params);

    // Check result for issues (Success/Error)
    if($result->_isError)
    {
    	echo "Error Placing Phone Call: {$result->_errorMessage}\n";
    }
    else
    {
	$new_ssid = $result->_responseXml->call->ssid;
    	echo "Call Started Successfully: {$new_ssid}\n";
	// print_r($result); // Uncomment this for debugging
    } 

    /* 
     * Add an Extention to Play an Audio File on OutBound Call
     */
    $params = array(
    	"SSID" => $new_ssid, 	      	  // New SSID From Feature Initiation
    	"audiofilename" => "welcome"	  // Audio File to Play
    );

    // Add Extension to Play AudioFile
    // addExtension(Command, parameters)
    $result = $client->addExtension("Playback", $params);



    // Check result for issues (Success/Error)
    if($result->_isError)
    {
    	echo "Error Generating Extension: {$result->_errorMessage}\n";
    }
    else
    {
    	echo "Extension Generated Successfully: {$result->_responseXml->call->Playback->ssid}\n";
	// print_r($result); // Uncomment this for debugging
    } 

?>
