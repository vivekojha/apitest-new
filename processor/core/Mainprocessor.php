<?php

/*
 * It is abstract class that contain comman functionality.
 * Need to override all abstract method in child class and provide definition accordingly.
 */

abstract class Mainprocessor {
    /*
     * It is main method tha tused to process the request for every api.
     * Input@: Array ($username: String, $password: String, $url: String, $issueTitle: String, $issueDescription: String)
     * Output@: String
     */

    public function requestProcess($data) {
        $authInfo = $this->createAuthInfo($data);
        $paramString = $this->createParamString($data);
        $rslt = $this->makeCURLRequest($data['repos_url'], 'POST', $paramString, $authInfo);
        return $this->parseRequestResponse(json_decode($rslt, 1));
    }

    /*
     * It is methos that is used to process curl request.
     * Input@:
     * $url: string
     * $method: string
     * $dataString: string
     * $authInfo: string
     * OutPut@:string
     */

    public function makeCURLRequest($url, $method, $dataString, $authInfo) {


        $ch = curl_init();
        if ($method == 'GET') {
            // Create Url string with query params
            $urlStringData = $url . '?' . $dataString;
            curl_setopt($ch, CURLOPT_URL, $urlStringData);
        } elseif ($method == 'POST') {
            $headers = $this->createBasicAuthHeader($authInfo['username'], $authInfo['password']);
            curl_setopt($ch, CURLOPT_URL, $url);

            // Set post data string as a json/query format 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            // Set basic authentication header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        }

        // Set username as an user agent
        curl_setopt($ch, CURLOPT_USERAGENT, $authInfo['username']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        if ($content === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error in url : ' . $url . " " . $error);
        } else {
            curl_close($ch);
            return $content;
        }
    }

    /*
     * It is an abstract method that used to create param string for request 
     * and need to give defintion in child class.
     */

    abstract protected function createParamString($params);

    /*
     * It is an abstract method that used to create authetication array for request 
     * and need to give defintion in child class.
     */

    abstract protected function createAuthInfo($params);

    /*
     * It is an abstract method that used to create basic authetication header for the request. 
     * and need to give defintion in child class.
     */

    abstract protected function createBasicAuthHeader($username, $password);

    /*
     * It is an abstract method that used to parse response. 
     * and need to give defintion in child class.
     */

    abstract protected function parseRequestResponse($res);
}

?>
