<?php
    // Sample Code to Place OutBound Calls with the Dubtel One REST API

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
     * Place a new OutBound Call to "614-309-7502"
     */
    $params = array(
    	"Caller" => $callerID, 	      	  // Outgoing Caller ID (CallerID)
    	"Called" => "6145590062",	  // The Phone number you are calling (Calling Number)
    	"GateWay" => "http://api.dubtel.com"
    );
    
    // Place Call to "614-559-0062"
    $result = $client->Send("call", $params);

    // Check result for issues (Success/Error)
    if($result->_isError)
    {
    	echo "Error Placing Phone Call: {$result->_errorMessage}\n";
	print_r($result);
    }
    else
    {
    	print_r($result); // echo "Call Started Successfully: {$result->dubtelOneResponse->call->ssid}\n";
    } 

?>
