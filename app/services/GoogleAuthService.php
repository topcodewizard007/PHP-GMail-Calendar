<?php
session_start();
ob_start();

class GoogleAuthService {
  // Debug variables
    public $debug_mode = true;

    // OAuth2 constants
    const OAUTH2_REVOKE_URI = 'https://accounts.google.com/o/oauth2/revoke';
    const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';
    const OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    const OAUTH2_FEDERATED_SIGNON_CERTS_URL = 'https://www.googleapis.com/oauth2/v1/certs';

    const USER_AGENT_SUFFIX = "google-api-php-client/0.4.8";

    /*
     *   Construct function, sets up the api_config with the requeired variables
     *   @param (array) array
     *       client_id (string)  The client id from Google API Console
     *       redirect_uri (string)   The redirect uri from Google API Console
     *       scope (string)  The scope of the requests, which is essentially what you are planning to  access
     *       access_type (string)   The access type is either 'online' or 'offline', 'offline' gives you a longer access period and allows you to get a refresh_token
     *       response_type (string)  The response type refers to the flow of the program
     */
    public function __construct($array)
    {
        $this->api_config = $array;
        $this->validate_token();
        $this->api_config['access_token'] = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : null;
    }

    /*
     *   Returns the url to the authorisation link, once used and a refresh token is retained, you'll never need this again
     *   @return (string) Google OAutht2 link
     */
    public function create_auth_url()
    {
        $params = array
            (
            'scope=' . urlencode($this->api_config['scope']),
            'redirect_uri=' . urlencode($this->api_config['redirect_uri']),
            'response_type=' . urlencode($this->api_config['response_type']),
            'client_id=' . urlencode($this->api_config['client_id']),
            'access_type=' . urlencode($this->api_config['access_type']),
            'prompt=' . $this->api_config['prompt'],
        );
        $params = implode('&', $params);
        return self::OAUTH2_AUTH_URL . "?$params";
    }

    /*
     *   Returns a new access token from the refresh token
     *   @param (string) refresh_token
     *   @return (string) New access token
     */
    public function refresh_token()
    {
        $info = array
            (
            'refresh_token' => $_COOKIE['refresh_token'],
            'grant_type' => 'refresh_token',
            'client_id' => $this->api_config['client_id'],
            'client_secret' => $this->api_config['client_secret'],
        );

        // Get returned CURL request
        $request = $this->make_request(self::OAUTH2_TOKEN_URI, 'POST', 'normal', $info);

        // Push the new token into the api_config
        $this->api_config['access_token'] = $request->access_token;
        $_SESSION['access_token'] = $request->access_token;
        $_SESSION['access_token_expiry'] = time() + $request->expires_in;

        // Return the token
        return $request;
    }

    /*
     *   Returns an access token from the code given in the first request to Google
     *   @param (string) data - the actual GET code given after authorisation
     *   @param (string) grant_type - always 'authorisation_code' in this instance
     *   @return (array) Contains all the returned data inc. access_token, refresh_token(first time only)
     */
    public function get_token($data, $grant_type = null)
    {
        if (!$grant_type) {
            $grant_type = 'authorization_code';
        }

        $info = array
            (
            'code' => $data,
            'grant_type' => $grant_type,
            'redirect_uri' => $this->api_config['redirect_uri'],
            'client_id' => $this->api_config['client_id'],
            'client_secret' => $this->api_config['client_secret'],
        );

        // Get the returned CURL request
        $request = $this->make_request(self::OAUTH2_TOKEN_URI, 'POST', 'normal', $info);
        // Push the new data into the api_config
        $this->api_config['code'] = $data;
        $this->api_config['access_token'] = $request->access_token;
        $_SESSION['access_token'] = $request->access_token;
        $_SESSION['refresh_token'] = $request->refresh_token;
        $_SESSION['access_token_expiry'] = time() + $request->expires_in;
        /* Save refresh token to a persistence storage,
        but for now I will be using cookies as my persistent storage
         */
        setcookie(
            "refresh_token",
            $request->refresh_token,
            time() + (10 * 365 * 24 * 60 * 60)
        );
        // Return all request data
        return $request;
    }

    /*
     *    Check the access_token is still valid, if not use the refresh_token to get a new one
     *
     */
    public function validate_token()
    {
        if (isset($_SESSION['access_token_expiry'])) {
            if (time() > $_SESSION['access_token_expiry'] && isset($_COOKIE['refresh_token'])) {
                // Get a new access token using the refresh token
                // fetch persistent refreshToken - either from database
                $data = $this->refresh_token();
                // Again save the expiry time of the new token
                $_SESSION['access_token_expiry'] = time() + $data->expires_in;
                // The new access token
                $_SESSION['access_token'] = $data->access_token;
            }
        }
    }

    /*
     *   CURL request function
     *   @param (string) url - Obvious
     *   @param (string) method - POST, GET, PUT, DELETE, whatever...
     *   @param (string) data - We shall see...
     *   @return (object) Returns data cleanly
     */
    public function make_request($url, $method, $type, $data)
    {
        // Init and build/switch methods
        $ch = curl_init();

        // Build basic options array
        $options = array
            (
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => $method,
        );
        if ($type == "json") {
            $header = array("Authorization: Bearer " . $this->api_config['access_token'], "Content-Type: application/json");
            if (isset($data)) {
                $post_fields = json_encode($data);
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = $post_fields;
            }
            $options[CURLOPT_HTTPHEADER] = $header;
        }
        if ($method == "POST" && $type == "normal") {
            $post_fields = http_build_query($data);
            $options[CURLOPT_POSTFIELDS] = $post_fields;
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_CUSTOMREQUEST] = "POST";
        }
        if ($method == "GET" && $type == "normal") {
            $header = array("Authorization: Bearer " . $this->api_config['access_token'], "Content-Type: application/json");
            $options[CURLOPT_HTTPHEADER] = $header;
            $post_fields = http_build_query($data);
            $options[CURLOPT_URL] = $url . '?' . $post_fields;
        }

        // Set CURL options
        curl_setopt_array($ch, $options);

        // Make CURL reponse
        $response = json_decode(curl_exec($ch));

        // CURL info gathering
        $curl_info['sent'] = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $curl_info['respHeaderSize'] = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $curl_info['respHttpCode'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_info['curlErrorNum'] = curl_errno($ch);
        $curl_info['curlError'] = curl_error($ch);
        $curl_info['url'] = $url;

        // Close CURL
        curl_close($ch);

        // Check for errors ** DEV MODE **
        if ($this->debug_mode == true) {
            if ($curl_info['curlErrorNum'] > 0) {
                throw new apiIOException("HTTP Error: ($respHttpCode) $curlError");
            }
            foreach ($curl_info as $k => $v) {
                $error[$k] = $v;
            }
            if (!$response) {
                $response = (object) array();
            }
            $response->headers = $error;
        }

        return $response;
    }
     /*
     *  Formats a response object
     *   @param (string) data - the response data
    calendar_id (string)
    start_time (string) - yyyy-dd-mm
    end_time (string) - yyyy-dd-mm
    summary (string)
    description (string)
     *   @return (object) Returns a calendar event for this calendar
     */
    public function formatResponse($data)
    {
        return json_decode(json_encode($data), true);
    }
}
