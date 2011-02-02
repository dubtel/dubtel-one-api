<?php

###################################################################################################
#	===========================================================================================
#	Copyright (c) 2010 Dubtel Inc.
#
#	Permission is hereby granted, free of charge, to any person obtaining a copy
#	of this software and associated documentation files (the "Software"), to deal
#	in the Software without restriction, including without limitation the rights
#	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
#	copies of the Software, and to permit persons to whom the Software is
#	furnished to do so, subject to the following conditions:
#
#	The above copyright notice and this permission notice shall be included in
#	all copies or substantial portions of the Software.
#
#	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
#	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
#	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
#	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
#	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
#	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
#	THE SOFTWARE.
#	
#	===========================================================================================
#
# dubtelOneClient API Classes - Dubtel 2010
#
#	The Dubtel One Client Class allows your application to place and 
#       receive calls, send and receive fax, send sms as well as 
#       query call logs.
#
#       During a call process utilities in the Dubtel One API and 
#	API Library will allow your application to perform tasks like:
#     
#            -- Play Pre-Recorded Audio
#            -- Record Audio
#            -- Say (Digits/Text)
#            -- Receive Input (Digits)
#            -- Send Call to Conference Room
#            -- Forward Call to any number
#            -- Send Fax to Email
#            -- Forward Fax to any number
#
#       The Dubtel One Open API also provides IM Presence service, and 
#       information on this can be located on the Documentation Site,
#	See http://www.dubtel.com/?q=documentation.
#
# Public classes
#	dubtelOneClient(APPID, APPKEY, GATEWAY) 
#	-- main Dubtel One API Class
#
#	dubtelOneResponse(URL, RESULT, STATUS, FEATURE) 
#	-- holds all the REST response data from Dubtel One API
#
# Public methods
#     	dubtelOneClient->Send(feature, params) Sends a Request to Service for initiating feature
#	dubtelOneClient->addException(command, params) Creates a Dynamic Extension
#
###################################################################################################

    /* 
     *	Before doing anything lets make sure curl is enable
     */
    if(!extension_loaded("curl"))
        throw(new Exception(
            "Curl extension is required for dubtelOneClient to work, please resolve this."));

    /* 
     * dubtelOneClient throws dubtelOneException on error 
     * Use this to catch dubtelOneClient specific Exceptions in your application
     */
    class dubtelOneException extends Exception {}


    class dubtelOneClient {

	protected $_APPID; 	// Application ID from Dubtel
	protected $_APPKEY;	// PASSPHRASE from Dubtel
	protected $_GATEWAY;    // Dubtel One REST Gateway URL
	protected $_FEATURE;	// Dubtel Feature (Call, Fax, SMS)
	protected $_SSID;	// Dubtel One SSID (Secure Session ID)
 
        public function __construct($applID, $applKey,
            $applgateway = "http://api.dubtel.com") {
            
            $this->_APPID = $applID;
            $this->_APPKEY = $applKey;
            $this->_GATEWAY = $applgateway;
        }

        /*
	 * addExtension
	 *    Creates a dynamic extension in Dubtel One Service for already
	 *	initiated Feature
	 *
	 *    param(s): command, params[n]
	 */
	public function addExtension($ext_command, $params = array())
	{
	    $myparams = $params;
	    $myparams['command'] = $ext_command;
	    return($this->Send($this->_FEATURE,$myparams));	
	}

        /*
         * Send
         *   Sends a Request to the Dubtel One Service to Initiate Feature
         *   $params : an id/value associative array,
	 *           : containing (callerID, Called and GateWay to Call From, etc)
         */
        public function Send($feature, $params = array()) 
	{

            $_entries = "&";
	    $_auth = "&appid={$this->_APPID}&appkey={$this->_APPKEY}";
	    $this->_FEATURE = $feature;

            foreach($params AS $id=>$value)
                $_entries .= "$id=".urlencode($value)."&";
            $_entries = substr($_entries, 0, -1);
            
            // Initialize a new Curl Object
	    $_url = $this->_GATEWAY."/?q=".$feature."{$_auth}{$_entries}";      
            $_ch = curl_init($_url);

            // Authenticate applicatioin request
            curl_setopt($_ch, CURLOPT_USERPWD, $_auth);
	    curl_setopt($_ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($_ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);

            curl_setopt($_ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($_ch, CURLOPT_POST, 1);
            curl_setopt($_ch, CURLOPT_POSTFIELDS, $_entries);

            $xmlDoc = new DOMDocument();
	    $xmlDoc->load($_url);

	    $_res = $xmlDoc->saveXML(); 
	    $_httpCode = 200;
	                
            return new dubtelOneResponse($_url, $_res, $_httpCode, $feature);
        }
	
    }  


  /* 
     * dubtelOneResponse holds all the REST response data 
     * Before using the reponse, check isError to see if an exception 
     * occurred with the data sent to Dubtel
     * _responseXml will contain a SimpleXml object with the response xml from Dubtel
     * _responseText contains the raw string response
     * _url and QueryString are from the request
     * _httpStatus is the response code of the request
     * _feature is the Dubtel One API Feature Used (Call, Fax, SMS)
     */
    class dubtelOneResponse {
        
        public $_responseText;
        public $_responseXml;
        public $_httpStatus;
        public $_url;
        public $_queryString;
        public $_isError;
        public $_errorMessage;
	public $_feature;
        
        public function __construct($url, $text, $status, $feature) {
            preg_match('/([^?]+)\??(.*)/', $url, $matches);
            $this->_url = $matches[1];
            $this->_queryString = $matches[2];
            $this->_responseText = $text;
            $this->_httpStatus = $status;
            $this->_feature = $feature;
            if($this->_httpStatus != 204)
                $this->_responseXml = @simplexml_load_string($text);
            
            if($this->_isError = ($status >= 400))
                $this->_errorMessage =
                    (string)$this->_responseXml->dubtelOneResponse->Exception;
            
        }
        
    }    

?>
