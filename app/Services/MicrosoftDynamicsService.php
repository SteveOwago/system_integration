<?php

namespace App\Services;

/**
 * Class MicrosoftDynamicsService.
 */
class MicrosoftDynamicsService
{
    public $tenant_id, $company_id, $access_token,$environment,$client_id,$client_secret;
    public function __construct()
    {
        $this->tenant_id = env('BC_TENANT_ID');
        $this->company_id = env('BC_COMPANY');
        $this->client_id = env('BC_CLIENT_ID');
        $this->client_secret = env('BC_CLIENT_SECRET');
        $this->access_token = $this->getAccessToken();
        $this->environment = env('BC_ENVIRONMENT');
    }
    public function postPaymentData($data)
    {
        $company_id = $this->company_id;
        $environment = $this->environment;
        $request_url = "https://api.businesscentral.dynamics.com/v2.0/$environment/ODataV4/PostPaymentSaleData?Company=$company_id"; #Dynamics URL for posting Data on payment and User details
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

        // Check for errors
        if (curl_errno($ch)) {
            info('Microsoft Dymanics Post Data cURL error: ' . curl_error($ch));
        } else {
            // Handle the response as needed
            echo 'Response: ' . $response;
            //Send Email Data Saved Successfull In Microsoft Dynamics Business Central

        }
        // Close the cURL session
        curl_close($ch);
    }





    public function getAccessToken()
    {
        return $this->generateBearerTokenBusinessCentral();
    }

    private function generateBearerTokenBusinessCentral()
    {
        // ------------------------PARAMETERS----------------------------------------
        $tenant_id = $this->tenant_id;
        $client_id = $this->client_id;
        $client_secret = $this->client_secret;

        $tokenUrl = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/token";

        $data = array(
            'client_id' => $client_id,
            'scope' => 'https://api.businesscentral.dynamics.com/.default',
            'client_secret' => $client_secret,
            'grant_type' => 'client_credentials',
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);

        if ($response === false) {
            info('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        $api_response = json_decode($response, true);

        if (isset($api_response['access_token'])) {
            // Handle $api_response
            return $api_response['access_token'];
        } else {
            return false;
        }
    }
}
