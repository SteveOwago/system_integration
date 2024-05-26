<?php

namespace App\Services;

/**
 * Class SalesforceService.
 */
class SalesforceService
{
    public $access_token, $environment, $client_id, $client_secret, $login_url;

    public function __construct()
    {
        $this->client_id = env('SALESFORCE_CLIENT_ID');
        $this->client_secret = env('SALESFORCE_CLIENT_SECRET');
        $this->access_token = $this->getAccessToken();
        $this->environment = env('SALESFORCE_ENVIRONMENT');
        $this->login_url = "https://login.salesforce.com/services/oauth2/token";
    }
    public function postCourseData($data)
    {

        $environment = $this->environment;
        $request_url = $this->getSalesForceInstanceUrl()."/services/data/v60.0/sobjects/Order"; #SalesForce URL for posting Data for payments and User details(Replace with custom URL For Creating Order)
        return $this->postRequest($request_url, $data);
    }

    private function getResponse($request_url)
    {
        $access_token = $this->access_token;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    private function postRequest($request_url, $data)
    {
        // Convert the data array to a JSON string
        $jsonData = json_encode($data);

        // cURL initialization
        $ch = curl_init($request_url);

        // Authorization Bearer Token
        $access_token = $this->access_token;

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors and Log them
        if (curl_errno($ch)) {
            info('SalesForce Post Data cURL error: ' . curl_error($ch));
        } else {
            // Handle the response as needed
            info('SalesForce Post Data Response: ') . $response;
            return $response;
        }
        // Close the cURL session
        curl_close($ch);
    }





    public function getAccessToken()
    {
        $data_string = "access_token";
        return $this->authInstanceData($data_string);
    }

    private function getSalesForceInstanceUrl()
    {
        $data_string = "instance_url";
        return $this->authInstanceData($data_string);
    }

    private function authInstanceData($data_string)
    {
        // ------------------------PARAMETERS----------------------------------------
        $client_id = $this->client_id;
        $client_secret = $this->client_secret;

        $tokenUrl = $this->login_url;

        $data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret
        );

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt_array($ch, array(
            CURLOPT_URL => $tokenUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            )
        ));

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            // Handle the response as needed
            $api_response = json_decode($response, true);

            if (isset($api_response[$data_string])) {
                // Handle $api_response
                return $api_response[$data_string];
            } else {
                return false;
            }
        }

        // Close the cURL session
        curl_close($ch);
    }
}
