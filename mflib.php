  <?php
/**
 * This class is a wrapper to the MediaFire.com API.
 *
 * It provides functions to interact with the MediaFire account, such as
 * retrieving or updating account information or more. It also works with the
 * account files and folders as well; including uploading file, creating new
 * folder, renaming file or folder...
 *
 * @package mflib
 */
/**
 * MediaFire API PHP Library
 *
 * @author windylea <www.windylea.com>
 * @copyright Copyright (c) 2012, WindyLea. All right reserved
 * @version 0.33
 */
 if(@!include_once(dirname(__FILE__)."/upload.php")){
 	echo 'Upload Module not installed';
 } 
class mflib
{
    /**
     * The unique application ID given to each MediaFire's developer
     *
     * @access public
     * @var string
     */
    public $appId;

    /**
     * The unique API key given to each MediaFire's developer
     *
     * @access public
     * @var string
     */
    public $apiKey;

    /**
     * A MediaFire user's email for getting session token
     *
     * @access public
     * @var string
     */
    public $email;

    /**
     * A MediaFire user's password for getting session token
     *
     * @access public
     * @var string
     */
    public $password;

    /**
     * The Facebook Access Token of the user whose MediaFire account is 
     * associated with the corresponding Facebook Account (not required if 
     * $email and $password are provided), for getting session token
     *
     * @access public
     * @var string
     */
    public $fbAccessToken;

    /**
     * Set to FALSE to retrieve remote content by creating a socket connection
     * instead of using cURL. Default is FALSE
     *
     * @access public
     * @var bool
     */
    public $useCurl = false;

    /**
     * The User-Agent value in every HTTP request. Default is 'Internet Explorer 10'
     *
     * @access public
     * @var string
     */
    public $userAgent = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)";

    /**
     * Format of the response from MediaFire, can either be JSON or XML. 
     * Default is 'json'
     *
     * @access protected
     * @var string
     */
    protected $responseFormat = "json";

    /**
     * An associative array contains the list of the API's URLs
     *
     * @access protected
     * @var array
     */
    protected $apiUrl = array(
        "FILE_COLLABORATE" => "http://www.mediafire.com/api/file/collaborate.php",
        "FILE_CONFIGURE_ONE_TIME_DOWNLOAD" => "http://www.mediafire.com/api/file/configure_one_time_download.php",
        "FILE_COPY" => "http://www.mediafire.com/api/file/copy.php",
        "FILE_DELETE" => "http://www.mediafire.com/api/file/delete.php",
        "FILE_GET_INFO" => "http://www.mediafire.com/api/file/get_info.php",
        "FILE_GET_LINKS" => "http://www.mediafire.com/api/file/get_links.php",
        "FILE_MOVE" => "http://www.mediafire.com/api/file/move.php",
        "FILE_ONE_TIME_DOWNLOAD" => "http://www.mediafire.com/api/file/one_time_download.php",
        "FILE_UPDATE" => "http://www.mediafire.com/api/file/update.php",
        "FILE_UPDATE_FILE" => "http://www.mediafire.com/api/file/update_file.php",
        "FILE_UPDATE_PASSWORD" => "http://www.mediafire.com/api/file/update_password.php",
        "FILE_UPLOAD" => "http://www.mediafire.com/api/upload/upload.php",
        "FILE_UPLOAD_CONFIG" => "http://www.mediafire.com/basicapi/uploaderconfiguration.php",
        "FILE_UPLOAD_GETTYPE" => "http://www.mediafire.com/basicapi/getfiletype.php",
        "FILE_UPLOAD_POLL" => "http://www.mediafire.com/api/upload/poll_upload.php",
        "FOLDER_ATTACH_FOREIGN" => "http://www.mediafire.com/api/folder/attach_foreign.php",
        "FOLDER_CREATE" => "http://www.mediafire.com/api/folder/create.php",
        "FOLDER_DELETE" => "http://www.mediafire.com/api/folder/delete.php",
        "FOLDER_DETACH_FOREIGN" => "http://www.mediafire.com/api/folder/detach_foreign.php",
        "FOLDER_GET_CONTENT" => "http://www.mediafire.com/api/folder/get_content.php",
        "FOLDER_GET_DEPTH" => "http://www.mediafire.com/api/folder/get_depth.php",
        "FOLDER_GET_INFO" => "http://www.mediafire.com/api/folder/get_info.php",
        "FOLDER_GET_REVISION" => "http://www.mediafire.com/api/folder/get_revision.php",
        "FOLDER_GET_SIBLINGS" => "http://www.mediafire.com/api/folder/get_siblings.php",
        "FOLDER_MOVE" => "http://www.mediafire.com/api/folder/move.php",
        "FOLDER_SEARCH" => "http://www.mediafire.com/api/folder/search.php",
        "FOLDER_UPDATE" => "http://www.mediafire.com/api/folder/update.php",
        "SYSTEM_GET_EDITABLE_MEDIA" => "http://www.mediafire.com/api/system/get_editable_media.php",
        "SYSTEM_GET_INFO" => "http://www.mediafire.com/api/system/get_info.php",
        "SYSTEM_GET_MIME_TYPES" => "http://www.mediafire.com/api/system/get_mime_types.php",
        "SYSTEM_GET_SUPPORTED_MEDIA" => "http://www.mediafire.com/api/system/get_supported_media.php",
        "SYSTEM_GET_VERSION" => "http://www.mediafire.com/api/system/get_version.php",
        "USER_ACCEPT_TOS" => "http://www.mediafire.com/api/user/accept_tos.php",
        "USER_FETCH_TOS" => "http://www.mediafire.com/api/user/fetch_tos.php",
        "USER_GET_INFO" => "http://www.mediafire.com/api/user/get_info.php",
        "USER_GET_LOGIN_TOKEN" => "https://www.mediafire.com/api/user/get_login_token.php",
        "USER_GET_SESSION_TOKEN" => "https://www.mediafire.com/api/user/get_session_token.php",
        "USER_LOGIN" => "http://www.mediafire.com/api/user/login_with_token.php",
        "USER_MYFILES" => "http://www.mediafire.com/api/user/myfiles.php",
        "USER_MYFILES_REVISION" => "http://www.mediafire.com/api/user/myfiles_revision.php",
        "USER_REGISTER" => "https://www.mediafire.com/api/user/register.php",
        "USER_RENEW_SESSION_TOKEN" => "http://www.mediafire.com/api/user/renew_session_token.php",
        "USER_UPDATE" => "http://www.mediafire.com/api/user/update.php",
        "MEDIA_CONVERSION" => "http://www.mediafire.com/conversion_server.php"
    );

    /**
     * All logged activities of the class
     *
     * @access protected
     * @var array
     */
    protected $actions = array();

    /**
     * HTTP response messages returned from the media conversion server with
     * their code
     *
     * @access protected
     * @var array
     */
    protected $mcStatusCode = array(
        "200" => "Conversion is ready. The pdf is sent with the response",
        "202" => "Request is accepted and in progress",
        "204" => "Unable to fulfill request. The document will not be converted",
        "400" => "Bad request. Check your arguments",
        "404" => "Unable to find file from quickkey"
    );

    /**
     * The image size for converting with the media conversion server
     *
     * @access protected
     * @var array
     */
    protected $mcSizeId = array(
        "d" => array("0", "1", "2"),
        "i" => array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f")
    );

    /**
     * Result messages of the uploaded file
     *
     * @access protected
     * @var array
     */
    protected $uploadResult = array(
        -80 => "Upload Key not found", 
        -20 => "Invalid Upload Key",
        0 => "Success"
    );

    /**
     * Current status of the uploaded file
     *
     * @access protected
     * @var array
     */
    protected $uploadStatus = array(
        0 => "Unknown or no status available for this key", 
        2 => "Key is ready for use",
        3 => "Upload is in progress",
        4 => "Upload is completed",
        5 => "Waiting for verification",
        6 => "Verifying File",
        11 => "Finished verification",
        99 => "No more requests for this key"
    );

    /**
     * Error messages of the uploaded file
     *
     * @access protected
     * @var array
     */
    protected $uploadFileError = array(
        1 => "File is larger than the maximum filesize allowed",
        2 => "File size cannot be 0",
        3 => "Found a bad RAR file",
        4 => "Found a bad RAR file",
        5 => "Virus found",
        6 => "Unknown internal error",
        7 => "Unknown internal error",
        8 => "Unknown internal error",
        9 => "Found a bad RAR file",
        12 => "Failed to insert data into database",
        13 => "File name already exists in the same parent folder, skipping",
        14  => "Destination folder does not exist"
    );

    /**
     * HTTP response headers
     *
     * @access protected
     * @var array
     */
    protected $httpResponseHeader;

    /**
     * Class constructor
     *
     * @access public
     */
    public function __construct()
    {
        $arguments = func_get_args();

        if (count($arguments) >= 2)
        {
            $this->appId = $arguments[0];
            $this->apiKey = $arguments[1];

            if (count($arguments) >= 4)
            {
                $this->email = $arguments[2];
                $this->password = $arguments[3];
            }
        }
    }

    /**
     * Retrieves remote contents from MediaFire and parse the result into object
     *
     * Additional HTTP options that can be set are
     * - 'method' : GET, POST, or any other HTTP method supported by the
     *  remote server. Defaults to GET.
     * - 'header' : Additional headers to be sent during request. Can either be
     *  a string or an array.
     * - 'content' : Additional data to be sent after the headers. Typically
     *  used with POST or PUT requests. This parameter can either be passed as
     *  a urlencoded string like 'para1=val1&para2=val2&...' or as an array
     *  with the field name as key and field data as value.
     * - 'file' : The file to be sent to the server. Typically used with POST
     *  or PUT requests. If this value is set, then the class will automatically
     *   use socket connection regardless useCurl property is set to TRUE.
     * - 'protocol_version' : HTTP protocol version. Defaults to 1.0.
     * - 'timeout' : Read timeout in seconds, specified by a float (e.g. 10.5).
     *  Default is 30.
     *
     * @access protected
     * @author windylea
     * @param string $url
     * @param array $httpOptions (Optional) An array contains additional
        HTTP options to be sent to the server
     * @param bool $returnRaw (Optional) Set it to TRUE to return the raw HTTP
        response from the remote server. Default is FALSE
     * @return array|bool|string An array contains the parsed information from
        the remote server, otherwise a string if $returnRaw is set to TRUE
     */
    public function getContents($url, $httpOptions = null, $returnRaw = false)
    {
        if ($this->useCurl === true && !isset($httpOptions["file"]))
        {
            /**
             * Open a connection to the server by cURL
             */
            $handle = curl_init();

            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_FRESH_CONNECT => true,
                CURLOPT_HEADER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => $this->userAgent
            );

            /**
             * Set the request method
             */
            $requestMethod = "GET";
            if (isset($httpOptions["method"]) && is_string($httpOptions["method"]))
            {
                $requestMethod = strtoupper(trim($httpOptions["method"]));
                $options[CURLOPT_CUSTOMREQUEST] = $requestMethod;
            }

            /*
             * Include additional POST values if present
             */
            if ($requestMethod == "POST" && isset($httpOptions["content"]))
            {
                $options[CURLOPT_POSTFIELDS] = $httpOptions["content"];
            }

            /**
             * Set protocol version
             */
            if (isset($httpOptions["protocol_version"]) && $httpOptions["protocol_version"] == "1.0")
            {
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
            } else
            {
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
            }

            $timeout = 30;
            if (isset($httpOptions["timeout"]) && is_int($httpOptions["timeout"]))
            {
                $options[CURLOPT_TIMEOUT] = $timeout;
            }

            /**
             * Build the request header
             */
            if (isset($httpOptions["header"]) && !empty($httpOptions["header"]))
            {
                if (is_string($httpOptions["header"]))
                {
                    $requestHeader = explode("\r\n", $httpOptions["header"]);
                } else
                {
                    $requestHeader = $httpOptions["header"];
                }

                $options[CURLOPT_HTTPHEADER] = $requestHeader;
            }

            /**
             * Now send the all data and get the response
             */
            curl_setopt_array($handle, $options);
            $response = curl_exec($handle);

            if (curl_errno($handle))
            {
                $this->showError("cURL error : " . curl_error($handle));
                curl_close($handle);
                return false;
            }

            curl_close($handle);
        } else
        {
            /**
             * Open a socket connection to the server
             */
            $components = parse_url($url);

            $timeout = 30;
            if (isset($httpOptions["timeout"]) && is_int($httpOptions["timeout"]))
            {
                $timeout = $httpOptions["timeout"];
            }

            if ($components["scheme"] == "https")
            {
                $handle = @fsockopen("ssl://" . $components["host"], 443, $errno, $errstr, $timeout);
            } else
            {
                $handle = @fsockopen($components["host"], 80, $errno, $errstr, $timeout);
            }

            if (!$handle)
            {
                /**
                 * An error occurred, cancel the operation
                 */
                $this->showError("Socket error : " . $errstr);
                return false;
            } else
            {
                /**
                 * Set request method
                 */
                $requestMethod = "GET";
                if (isset($httpOptions["method"]) && is_string($httpOptions["method"]))
                {
                    $requestMethod = strtoupper(trim($httpOptions["method"]));
                }

                /**
                 * Set protocol version
                 */
                $protocol_version = "1.1";
                if (isset($httpOptions["protocol_version"]) && is_string($httpOptions["protocol_version"]))
                {
                    $protocol_version = trim($httpOptions["protocol_version"]);
                }

                /**
                 * Get the query string
                 */
                $query = "";
                if (isset($components["query"]))
                {
                    $query = "?" . $components["query"];
                }

                /**
                 * Build the request header
                 */
                $response = "";
                $header = $requestMethod . " " . $components["path"] . $query . " HTTP/" . $protocol_version . "\r\n";
                $header .= "Host: " . $components["host"] . "\r\n";
                $header .= "Connection: Close\r\n";
                $header .= "User-agent: " . $this->userAgent . "\r\n";

                if (isset($httpOptions["header"]) && !empty($httpOptions["header"]))
                {
                    if (is_array($httpOptions["header"]))
                    {
                        $requestHeader = implode("\r\n", $httpOptions["header"]);
                    } else
                    {
                        $requestHeader = $httpOptions["header"];
                    }

                    $header .= $requestHeader . "\r\n";
                }

                /**
                 * Build the request header for POST method
                 *
                 * This part is based on Duukkis's contributed notes at php.net ({@link http://php.net/manual/fr/function.fsockopen.php#62380 Source})
                 */
                $data = "";

                if ($requestMethod == "POST")
                {
                    if (isset($httpOptions["file"]) && !empty($httpOptions["file"]))
                    {
                        srand((double) microtime() * 1000000);
                        $boundary = "---------------------------" . substr(md5(rand(0, 32000)), 0, 11);
                        $header .= "Content-Type: multipart/form-data, boundary=$boundary\r\n";

                        /*
                         * Include additional POST values if present
                         */
                        if (isset($httpOptions["content"]))
                        {
                            if (is_string($httpOptions["content"]))
                            {
                                $httpPostContent = parse_str($httpOptions["content"]);
                            } else
                            {
                                $httpPostContent = $httpOptions["content"];
                            }
                        }

                        if (!empty($httpPostContent))
                        {
                            foreach($httpPostContent as $name => $value)
                            {
                                $data .= "--$boundary\r\n";
                                $data .= "Content-Disposition: form-data; name=\"" . $name . "\"\r\n";
                                $data .= "\r\n" . $value . "\r\n";
                                $data .= "--$boundary\r\n";
                            }
                        } else
                        {
                            $data .= "--$boundary\r\n";
                        }

                        /**
                         * Open the file and get its contents
                         */
                        $pathname = $httpOptions["file"]["path"];
                        if (isset($httpOptions["file"]["customName"])) {
                            $basename = $httpOptions["file"]["customName"];
                        } else {
                            $basename = pathinfo($pathname, PATHINFO_BASENAME);
                        }

                        $fileHandle = fopen($pathname, "r");
                        $contents = fread($fileHandle, filesize($pathname));
                        fclose($fileHandle);

                        $data .= "Content-Disposition: form-data; name=\"" . $httpOptions["file"]["name"] . "\"; filename=\"$basename\"\r\n";
                        $data .= "Content-Type: " . $httpOptions["file"]["type"] . "\r\n\r\n";
                        $data .= $contents . "\r\n";
                        $data .= "--$boundary--\r\n";
                        $header .= "Content-Length: " . strlen($data) . "\r\n";
                    } else
                    {
                        if (is_array($httpOptions["content"]))
                        {
                            $httpPostContent = http_build_query($httpOptions["content"], "", "&");
                        } else
                        {
                            $httpPostContent = $httpOptions["content"];
                        }

                        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
                        $header .= "Content-Length: " . strlen($httpPostContent) . "\r\n\r\n";
                        $header .= $httpPostContent;
                    }
                }

                $header .= "\r\n";

                /**
                 * Now send the all data and get the response
                 */
                fwrite($handle, $header . $data);

                while (!feof($handle))
                {
                    $response .= fgets($handle, 32768);
                }

                fclose($handle);
            }
        }

        /**
         * Split the response header and body
         */
        $position = strpos($response, "\r\n\r\n");
        $responseHeader = trim(substr($response, 0, $position));
        $responseBody = trim(substr($response, $position));
        $this->httpResponseHeader = explode("\r\n", $responseHeader);

        if(stripos($responseHeader, "Transfer-Encoding: chunked") !== false && $this->useCurl === false)
        {
            $responseBody = self::httpChunkedDecode($responseBody);
        }

        if ($returnRaw)
        {
            return $responseBody;
        }

        /**
         * Parse the response body into array
         */
        if (strpos($responseHeader, "application/json") !== false)
        {
            $data = json_decode($responseBody, true);
            $data = $data["response"];
        } elseif (strpos($responseHeader, "text/xml") !== false)
        {
            $data = self::xml2array(simplexml_load_string($responseBody));
        } else
        {
            $this->showError("Unsupported server response format");
        }

        if (isset($data["result"]) && trim($data["result"]) == "Success")
        {
            return $data;
        } else
        {
            $this->showError(trim($data["message"]), trim($data["error"]));
            return false;
        }
    }

    /**
     * Dechunks an HTTP 'Transfer-Encoding: chunked' message
     *
     * @access public
     * @author Marques Johansson
     * @link http://www.php.net/manual/en/function.http-chunked-decode.php#89786 Source
     * @param string $chunk The encoded message
     * @return string Returns the decoded message. If $chunk wasn't encoded
        properly it will be returned unmodified.
     * @static
     */
    public static function httpChunkedDecode($chunk)
    {
        $pos = 0;
        $len = strlen($chunk);
        $dechunk = null;

        while(($pos < $len)
            && ($chunkLenHex = substr($chunk, $pos,
                ($newlineAt = strpos($chunk, "\n", $pos + 1)) - $pos)))
        {
            if (!self::isHex($chunkLenHex))
            {
                trigger_error("Value is not properly chunk encoded", E_USER_WARNING);
                return $chunk;
            }

            $pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex, "\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
        }

        return $dechunk;
    }

    /**
     * Determines if a string can represent a number in hexadecimal
     *
     * @access public
     * @author Marques Johansson
     * @link http://www.php.net/manual/en/function.http-chunked-decode.php#89786 Source
     * @param string $hex
     * @return boolean Returns TRUE if the string is a hex, otherwise FALSE
     * @static
     */
    public static function isHex($hex)
    {
        // regex is for weenies
        $hex = strtolower(trim(ltrim($hex, "0")));
        if (empty($hex)) { $hex = 0; };
        $dec = hexdec($hex);
        return ($hex == dechex($dec));
    }

    /**
     * Prints the error message
     *
     * @access protected
     * @author windylea
     * @param string $errorMessage The error message
     * @param string $errorCode The error code returned from MediaFire. If
        this value equals to "0" then the error message is from the class itself
     */
    protected function showError($errorMessage, $errorCode = "0")
    {
        exit("Error - " . end($this->actions) . " : \"" . $errorMessage . "\" (" . $errorCode . ")");
    }

    /**
     * Recursively converts a SimpleXML object into array
     *
     * @access public
     * @author Julio Cesar Oliveira
     * @link http://www.php.net/manual/en/function.simplexml-load-string.php#89951
     * @param object $xml A SimpleXML object
     * @return array Returns the converted array
     * @static
     */
    public static function xml2array($xml)
    {
        $newArray = array();
        $array = (array) $xml;
        foreach($array as $key => $value)
        {
            $value = (array) $value;
            if (isset($value[0]))
            {
                if (is_object($value[0]))
                    $newArray[$key] = self::xml2array($value, true);
                else
                    $newArray[$key] = trim($value[0]);
            }
            else
            {
                $newArray[$key] = self::xml2array($value, true);
            }
        }
        return $newArray;
    }

    /**
     * Copy values from existing array by their keys into a new array
     *
     * The first argument is the input array. The following arguments are the
     * array keys to extract values
     *
     * @access public
     * @author windylea
     * @return array
     * @static
     */
    public static function copy()
    {
        $arguments = func_get_args();
        $array = array_shift($arguments);
        $return = null;

        if (count($arguments) > 0)
        {
            $return = array();
            foreach($arguments as $key)
            {
                if (isset($array[$key]))
                {
                    $return[$key] = $array[$key];
                }
            }
        }

        return $return;
    }

    /**
     * Returns the HTML format of the MediaFire Terms of Service and its
     * revision, date, whether the user has accepted it not not, and the
     * acceptance token if the user has not accepted the latest terms
     *
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @access public
     * @author windylea
     * @return array|bool
     */
    public function userFetchTos($sessionToken)
    {
        $this->actions[] = "Fetching T.O.S document";

        $query = array(
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["USER_FETCH_TOS"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["terms_of_service"];
    }

    /**
     * Accept the Terms of Service by sending the acceptance token
     *
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $acceptanceToken The token returned by userFetchTos() method
     * @access public
     * @author windylea
     * @return bool Returns TRUE if user agrees to accept the Terms of Service
     */
    public function userAcceptTos($sessionToken, $acceptanceToken)
    {
        $this->actions[] = "Accepting T.o.S document";

        $query = array(
            "session_token" => $sessionToken,
            "acceptance_token" => $acceptanceToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["USER_ACCEPT_TOS"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return true;
    }

    /**
     * Generates a 60-second Login Token to be used by the developer to login a
     * user directly to their account. Note: This call requires SSL
     *
     * @access protected
     * @author windylea
     * @return string
     */
    protected function userGetLoginToken()
    {
        $this->actions[] = "Getting login token";

        $query = array(
            "email" => $this->email,
            "password" => $this->password,
            "application_id" => $this->appId,
            "signature" => $this->userGetSignature(),
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["USER_GET_LOGIN_TOKEN"] . "?" . http_build_query($query, "", "&");

        $data = $this->getContents($url);
        if (!$data)
        {
            return false;
        }

        return trim($data["login_token"]);
    }

    /**
     * Generates a 10-minute Access Session Token to be used in upcoming API
     * requests. Note: This call requires SSL
     *
     * @access public
     * @author windylea
     * @see userRenewSessionToken()
     * @return string|bool Returns a 10-minute Access Session Token on success,
        otherwise FALSE if an error occurred
     */
    public function userGetSessionToken($version)
    {
        $this->actions[] = "Getting new session token";
	
        $query = array(
            "application_id" => $this->appId,
            "signature" => $this->userGetSignature(),
            "response_format" => $this->responseFormat
        );
        if($version == TRUE){
		$query = array(
            "application_id" => $this->appId,
            "signature" => $this->userGetSignature(),
            "token_version" => '2',
            "response_format" => $this->responseFormat
        );	
		} 

        if (!empty($this->fbAccessToken))
        {
            $query["fb_access_token"] = $this->fbAccessToken;
        } else
        {
            $query["email"] = $this->email;
            $query["password"] = $this->password;
        }

        $url = $this->apiUrl["USER_GET_SESSION_TOKEN"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return trim($data["session_token"]);
    }

    /**
     * Generates a signature to be used in upcoming API requests
     *
     * @access protected
     * @author windylea
     * @return string Returns a SHA1-hashed string
     */
    public function userGetSignature()
    {
        return sha1($this->email . $this->password . $this->appId . $this->apiKey);
    }

    /**
     * Extends the life of the session token by another 10 minutes.
     *
     * If the session token is less than 5 minutes old, then it does not get
     * renewed and the same token is returned.
     *
     * If the token is more than 5 minutes old, then, depending on the
     * application configuration, the token gets extended or a new token is
     * generated and returned.
     *
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @access public
     * @author windylea
     * @return string|bool Returns a new 10-minute Access Session Token on
        success, otherwise FALSE if an error occurred
     */
    public function userRenewSessionToken($sessionToken)
    {
        $this->actions[] = "Renewing current session token";

        $query = array(
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["USER_RENEW_SESSION_TOKEN"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return trim($data["session_token"]);
    }

    /**
     * Returns a list of the user's personal information
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $singleInfo Instead of returning all of the user's
        personal information, returns a single value only. Default is NULL
     * @return bool|array|string Returns an array when $singleInfo is NULL,
        otherwise a string
     */
    public function userGetInfo($sessionToken, $singleInfo = null)
    {
        $this->actions[] = "Getting user info";

        $query = array(
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["USER_GET_INFO"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        if (is_string($singleInfo) && !empty($singleInfo))
        {
            $singleInfo = trim(strtolower($singleInfo));
            if (isset($data["user_info"]["$singleInfo"]))
            {
                return $data["user_info"]["$singleInfo"];
            }
        }

        return $data["user_info"];
    }

    /**
     * Returns a fraction number indicating the global revision of 'Myfiles'
     *
     * The revision is in the x.y format. 'x' is the folders-only revision.
     * 'y' is the folders-and-files revision. When the revision resets to 1.0,
     * the time stamp 'epoch' is updated so both 'revision' and 'epoch'
     * can be used to identify a unique revision
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @return array|bool Returns an array contains epoch and revision number
     */
    public function userMyfilesRevision($sessionToken)
    {
        $this->actions[] = "Getting user Myfiles revision";

        $query = array(
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["USER_MYFILES_REVISION"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return self::copy($data, "revision", "epoch");
    }

    /**
     * Note: This method was removed from the MediaFire API in 31-Jan-2013 and 
     * is no longer available since version 0.33 of this library. The code is 
     * still kept for reference only
     *
     * Registers a MediaFire account. Note: This call requires SSL
     *
     * Example of usage :
     * <code>$mflib->userRegister(array("email" => "email@example.com", "password" => "123456"));</code>
     *
     * @access public
     * @author windylea
     * @param string $email The user email address
     * @param string $password The user password
     * @param array $information (Optional) An associative array contains
        additional user information
     * @return bool Returns TRUE on success
     */
    /*public function userRegister($email, $password, $information = null)
    {
        $this->actions[] = "Registering new user";

        if(empty($email) || empty($password))
        {
            $this->showError("User email and password are both required");
            return false;
        }

        $query = array(
            "email" => $email,
            "password" => $password,
            "application_id" => $this->appId,
            "response_format" => $this->responseFormat
        );

        $parameters = array("first_name", "last_name", "display_name");

        if (is_array($information) && !empty($information))
        {
            foreach($information as $key => $value)
            {
                $key = trim(strtolower($key));
                $value = trim($value);

                if (in_array($key, $parameters))
                {
                    $query[$key] = $value;
                }
            }
        }

        $url = $this->apiUrl["USER_REGISTER"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return true;
    }*/

    /**
     * Updates the user's personal information
     *
     * Example of usage :
     * <code>$mflib->userUpdate(array("first_name" => "John", "last_name" => "Doe"));</code>
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array $information An associative array contains the updated
        user's personal information
     * @return bool Returns TRUE on success
     */
    public function userUpdate($sessionToken, $information)
    {
        $this->actions[] = "Updating user information";

        $genders = array("male", "female", "none");
        $primary_usages = array("home", "work", "school", "none");
        $parameters = array("display_name", "first_name", "last_name",
                            "birth_date", "gender", "website",
                            "location", "newsletter", "primary_usage");

        $query = array(
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        if (is_array($information) && !empty($information))
        {
            foreach($information as $key => $value)
            {
                $key = trim(strtolower($key));
                $value = trim($value);
                $addTo = false;

                if (in_array($key, $parameters))
                {
                    switch ($key)
                    {
                        case "birth_date":
                            if (preg_match("/\d{4}-\d{2}-\d{2}/", $value))
                                $addTo = true;
                            break;
                        case "gender":
                            if (in_array($value, $genders))
                                $addTo = true;
                            break;
                        case "newsletter":
                            if ($value == "yes" || $value == "no")
                                $addTo = true;
                            break;
                        case "primary_usage":
                            if (in_array($value, $primary_usages))
                                $addTo = true;
                            break;
                    }
                }

                if($addTo === true)
                {
                    $query[$key] = $value;
                }
            }

            $url = $this->apiUrl["USER_UPDATE"] . "?" . http_build_query($query, "", "&");
            $data = $this->getContents($url);

            if (!$data)
            {
                return false;
            }

            return true;
        }
    }

    /**
     * Generate link(s) for multiple people to edit files
     *
     * If email addresses are passed, contacts are created. If email addresses
     * are not passed, only edit links are returned. This API also returns the
     * daily collaboration link request count.
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $quickkey (Optional) The quickkey or comma-separated
        list of quickkeys to be shared. If quickkeys are not passed,
        the daily sharing limit is returned
     * @param array|string $emails (Optional) A comma-separated list of email
        addresses to which an edit link will be sent. Can be an array instead
     * @param int $duration (Optional) The number of minutes the share link
        is valid. If an email address was not passed, the duration parameter is
        ignored, and the edit link is valid for 30 days
     * @param string $message (Optional) A short message to be sent with the
        notification emails. If email addresses were not passed, the message
        parameter is ignored
     * @param bool $public (Optional) If this parameter is set to TRUE,
        multiple people can use the same link to edit the document.
        The default is FALSE.
     * @return array|bool|string Returns an array contain the collaboration
        links and the number of daily collaboration link request count
     */
    public function fileCollaborate($sessionToken, $quickkey = null,
        $emails = null, $duration = null, $message = null, $public = null,
        $emailNotification = null)
    {
        $this->actions[] = "Generating collaboration links";

        if (!empty($quickkey))
        {
            if (is_array($quickkey))
            {
                $quickkey = implode(",", $quickkey);
            }

            if (!empty($emails))
            {
                $message = null;
            } else
            {
                if (is_array($emails))
                {
                    $emails = implode(",", $emails);
                }
            }

            if (!is_int($duration))
            {
                $duration = null;
            }

            if ($public === false || $public == 0)
            {
                $public = "no";
            } else
            {
                $public = "yes";
            }
        }

        $query = array(
            "quick_key" => $quickkey,
            "emails" => $emails,
            "duration" => $duration,
            "message" => $message,
            "public" => $public,
            "email_notification" => $emailNotification,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_COLLABORATE"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);
        if (!$data)
        {
            return false;
        }

        return self::copy($data, "collaboration_links", "daily_sharing_count");
    }

    /**
     * Copy a file to a specified folder
     *
     * Any file can be copied whether it belongs to the session user or another
     * user. However, the target folder must be owned by the session caller.
     * Private files not owned by the session caller cannot be copied.
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $quickkey The quickkey or a list of quickkeys that
        identify the files to be saved
     * @param string $folderKey (Optional) The key that identifies the
        destination folder. If omitted, the destination folder will be the
        root folder (Myfiles)
     * @return string
     */
    public function fileCopy($sessionToken, $quickkey, $folderKey = null)
    {
        $this->actions[] = "Copying file";

        if (is_array($quickkey))
        {
            $quickkey = implode(",", $quickkey);
        }

        $query = array(
            "quick_key" => $quickkey,
            "folder_key" => ($folderKey != "")
                            ? $folderKey : null,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_COPY"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["skipped_count"];
    }

    /**
     * Deletes a single file or multiple files
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $quickkey The quickkey that identifies the file.
        You can also specify multiple quickkeys separated by comma or just put
        them into an array
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function fileDelete($sessionToken, $quickkey)
    {
        $this->actions[] = "Deleting file";

        if (is_array($quickkey))
        {
            $quickkey = implode(",", $quickkey);
        }

        $query = array(
            "quick_key" => $quickkey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_DELETE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Returns details of a single file or multiple files
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $quickkey The quickkey that identifies the file.
        You can also specify multiple quickkeys separated by comma or just put
        them into an array
     * @return array|bool Returns an array contains details of the file(s)
     */
    public function fileGetInfo($sessionToken, $quickkey)
    {
        $this->actions[] = "Getting file information";

        if (is_array($quickkey))
        {
            $quickkey = implode(",", $quickkey);
        }

        $query = array(
            "quick_key" => $quickkey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_GET_INFO"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        if(strpos($quickkey, ",") !== false)
        {
            return $data["file_infos"];
        } else
        {
            return $data["file_info"];
        }
    }

    /**
     * Return the view link, normal download link, and if possible the direct
     * download link of a file.
     *
     * The direct download link can only be generated for files uploaded by the 
     * MediaFire account owner himself/herself. If the link is not generated, 
     * an error message is returned explaining the reason
     *
     * @access public
     * @author windylea
     * @param array|string $quickkey The quickkey that identifies the file.
        You can also specify multiple quickkeys separated by comma or just put
        them into an array
     * @param string $linkType (Optional) Specify which link type is to be
        returned. If not passed, all link types are returned.
     * @param string $sessionToken (Optional) A Session Access Token to
        authenticate the user's current session
     * @return array|bool Returns an array contains links of the specified
        files, otherwise FALSE if an error occurred
     */
    public function fileGetLinks($quickkey, $linkType = null,
        $sessionToken = null)
    {
        $this->actions[] = "Get file links";

        if (is_array($quickkey))
        {
            $quickkey = implode(",", $quickkey);
        } elseif (!is_string($quickkey))
        {
            $this->showError("Invalid parameter 'quickkey' specified");
            return false;
        }

        $linkType = trim(strtolower($linkType));
        $linkTypes = array("view", "edit", "normal_download",
            "direct_download", "one_time_download");
        if (!is_string($linkType) || !in_array($linkType, $linkTypes))
        {
            $linkType = null;
        }

        $query = array(
            "quick_key" => $quickkey,
            "link_type" => $linkType,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_GET_LINKS"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);
        if (!$data)
        {
            return false;
        }

        return self::copy($data, "links",
            "one_time_download_request_count",
            "direct_download_free_bandwidth");

        return $return;
    }

    /**
     * Moves a single file or multiple files to a folder
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $quickkey The quickkey that identifies the file.
        You can also specify multiple quickkeys separated by comma or just put
        them into an array
     * @param string $folderKey (Optional) The key that identifies the
        destination folder. If omitted, the destination folder will be the
        root folder (My files)
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function fileMove($sessionToken, $quickkey, $folderKey = null)
    {
        $this->actions[] = "Moving file";

        if (is_array($quickkey))
        {
            $quickkey = implode(",", $quickkey);
        }

        $query = array(
            "quick_key" => $quickkey,
            "folder_key" => ($folderKey != "")
                            ? $folderKey : null,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_MOVE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Create a one-time download link. This method can also be used to
     * configure/update an existing one-time download link
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $quickkey The quickkey of the file to generate the
        one-time download link. If it is not passed, no link is generated, and
        the daily limit will be returned.
     * @param string $information (Optional) The updated information of the
        new/existing one-time download link
     * @return array|bool Returns an array on success, otherwise FALSE if an
        error occurred
     */
    public function fileOneTimeDownload($sessionToken, $quickkey, $information)
    {
        if (strlen($quickkey) < 16)
        {
            $this->actions[] = "Creating a one-time download link";
            $param = "quick_key";
            $url = $this->apiUrl["FILE_ONE_TIME_DOWNLOAD"];
        } else
        {
            $this->actions[] = "Configuring a one-time download link";
            $url = $this->apiUrl["FILE_CONFIGURE_ONE_TIME_DOWNLOAD"];
            $param = "token";
        }


        $query = array(
            $param => $quickkey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $parameters = array("get_counts_only", "duration", "email_notification",
                            "success_callback_url", "error_callback_url", "bind_ip",
                            "burn_after_use");

        if (is_array($information) && !empty($information))
        {
            foreach($information as $key => $value)
            {
                $key = trim(strtolower($key));
                $value = trim($value);

                if (in_array($key, $parameters))
                {
                    $query[$key] = $value;
                }
            }
        }

        $url .= "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        if (strlen($quickkey) < 16)
        {
            return self::copy($data, "one_time_download_request_count",
                "one_time_download", "token");
        } else
        {
            return $data["one_time_download_request_count"];
        }
    }

    /**
     * Updates a file's information
     *
     * Example of usage :
     * <code>$mflib->fileUpdateInfo("8c4ff4fzufdbbip", array("description" => "a test file", "tags" => "test,file"));</code>
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $quickkey The quickkey that identifies the file
     * @param array $information An associative array contains the updated
        file information
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function fileUpdateInfo($sessionToken, $quickkey, $information)
    {
        $this->actions[] = "Updating file information";

        $query = array(
            "quick_key" => $quickkey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $parameters = array("filename", "description", "tags",
                            "privacy", "note_subject", "note_description",
                            "timezone");

        if (is_array($information) && !empty($information))
        {
            foreach($information as $key => $value)
            {
                $key = trim(strtolower($key));
                $value = trim($value);

                if (in_array($key, $parameters))
                {
                    $query[$key] = $value;
                }
            }
        }

        $url = $this->apiUrl["FILE_UPDATE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Updates a file's quickkey with another file's quickkey. Note: Only files
     * with the same file extension can be used with this operation
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $fromQuickkey The quickkey of the file to be overriden.
        After this operation, this quickkey will be invalid
     * @param string $toQuickkey The new quickkey that will point to the file
        previously identified by fromQuickkey
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function fileUpdate($sessionToken, $fromQuickkey, $toQuickkey)
    {
        $this->actions[] = "Updating file's quickey";

        $query = array(
            "from_quickkey" => $fromQuickkey,
            "to_quickkey" => $toQuickkey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_UPDATE_FILE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Updates a file's password
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $quickkey The quickkey that identifies the file
     * @param string $password (Optional) The new password to be set. To remove
        the password protection, pass an empty string
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function fileUpdatePassword($sessionToken, $quickkey, $password = null)
    {
        $this->actions[] = "Updating file password";

        $query = array(
            "quick_key" => $quickkey,
            "password" => $password,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_UPDATE_PASSWORD"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Uploads a file
     *
     * The filetype can be explicitly specified by following the filename with
     * the type in the format ';type=mimetype'
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $filename Path to the file to be uploaded
     * @param string $uploadKey (Optional) The quickkey of the destination
      folder. Default is 'myfiles', which means that the uploaded file will be
      stored in the root folder (My Files)
     * @param string $customName (Optional) Path to the file to be uploaded
     * @return bool|string Returns the upload key of the file, otherwise FALSE
        if an error occurred
     */
    public function fileUpload($sessionToken, $filename, $uploadKey = "myfiles",
        $customName = null)
    {
        $mimetype = "application/octet-stream";
        if(strpos($filename, ";type=") !== false)
        {
            $parts = explode(";type=", $filename, 2);
            $filename = $parts[0];
            if (!empty($parts[1]))
            {
                $mimetype = $parts[1];
            }
        }

        if (!file_exists($filename) || !is_readable($filename))
        {
            $this->showError("File is not exist or not readable");
            return false;
        }

        $filesize = filesize($filename);
        if ($filesize == 0)
        {
            $this->showError("File has no content");
            return false;
        }

        $httpOptions = array(
            "method" => "POST",
            "file" => array(
                "name" => "Filedata",
                "path" => $filename,
                "type" => $mimetype
            ),
            "header" => array(
                "Referer: http://www.mediafire.com",
                "x-filesize: $filesize"
            )
        );

        if (is_string($customName) && !empty($customName))
        {
            $httpOptions["header"][] = "x-filename: $customName";
            $httpOptions["file"]["customName"] = $customName;
        }

        $query = array(
            "uploadkey" => $uploadKey,
            "session_token" => $sessionToken,
            "response_format" => "xml"
        );

        $this->actions[] = "Uploading";
        $url = $this->apiUrl["FILE_UPLOAD"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url, $httpOptions);

        if (!$data)
        {
            return false;
        }

        if (!isset($data["doupload"]["key"]) || trim($data["doupload"]["key"]) == "")
        {
            $this->showError("Unable to upload file - Code '" . $data["doupload"]["result"] . "'");
        }

        return $data["doupload"]["key"];
    }

    /**
     * Check for the status of a current Upload
     *
     * This can be called after the call to upload file. Use the key returned
     * by fileUpload method to request the status of the current upload.
     * Keep calling this API every few seconds  until you get the status value
     * 99 which means that the upload is complete. The quickkey of the file and
     * other related information is also returned along when the upload is
     * complete.
     *
     * @access public
     * @author windylea
     * @see fileUpload()
     * @see $uploadResult, $uploadStatus, $uploadFileError
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $uploadKey The upload key returned from method 'fileUpload'
     * @return array|bool Returns an array contains information of the
        upload process
     */
    public function filePollUpload($sessionToken, $uploadKey)
    {
        if (!is_string($sessionToken) || empty($sessionToken))
            return false;

        $this->actions[] = "Getting upload result";
        $query = array(
            "session_token" => $sessionToken,
            "key" => $uploadKey,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FILE_UPLOAD_POLL"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

		$data["doupload"]["result_message"] = "";
		if (isset($this->uploadResult[$data["doupload"]["result"]]))
		{
			$data["doupload"]["result_message"] = $this->uploadResult[$data["doupload"]["result"]];
		}

		$data["doupload"]["fileerror_message"] = "";
		if (isset($this->uploadFileError[$data["doupload"]["fileerror"]]))
		{
			$data["doupload"]["fileerror_message"] = $this->uploadFileError[$data["doupload"]["fileerror"]];
		}

        return $data["doupload"];
    }

    /**
     * Adds shared folders to the account
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $folderKey The key that identifies the folder to be
        attached. You can also specify multiple folderkeys separated by comma
        or just put them into an array
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function folderAttachForeign($sessionToken, $folderKey)
    {
        if (is_array($folderKey))
        {
            $folderKey = implode(",", $folderKey);
        }

        $this->actions[] = "Adding shared folder";
        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_ATTACH_FOREIGN"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Creates a new folder
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $folderName The name of the new folder to be created
     * @param string $parentKey (Optional) The key that identifies an existing
        folder in which the new folder is to be created. If not specified,
        the new folder will be created in the root folder (My files)
     * @return array|bool Returns an array contain the quickkey, upload key and
        created date of the newly created folder; otherwise FALSE if an error occurred
     */
    public function folderCreate($sessionToken, $folderName, $parentKey = null)
    {
        $this->actions[] = "Creating new folder";

        $query = array(
            "foldername" => $folderName,
            "parent_key" => ($parentKey != "") ? $parentKey : null,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_CREATE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return self::copy($data, "folder_key", "upload_key", "created");
    }

    /**
     * Removes shared folders from the account
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $folderKey The key that identifies the folder to be
        deattached. You can also specify multiple folderkeys separated by comma
        or just put them into an array
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function folderDetachForeign($sessionToken, $folderKey)
    {
        if (is_array($folderKey))
        {
            $folderKey = implode(",", $folderKey);
        }

        $this->actions[] = "Removing shared folder";
        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_DETACH_FOREIGN"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Deletes a folder
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $folderKey The key that identifies the folder to be
        moved. You can also specify multiple folderkeys separated by comma or
        just put them into an array
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function folderDelete($sessionToken, $folderKey)
    {
        if (is_array($folderKey))
        {
            $folderKey = implode(",", $folderKey);
        }

        $this->actions[] = "Deleting folder";
        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_DELETE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Returns a folder's immediate sub folders and files.
     *
     * @access public
     * @author windylea
     * @param string $folderKey (Optional) If folder_key is not passed, the API
        will return the root folder content (session token is required)
     * @param string $sessionToken (Optional) A Session Access Token to
        authenticate the user's current session (needed only when accessing
        root folder, private folder, or the folder's private content)
     * @param string $contentType (Optional) Request what type of content.
        Can be 'folders' or 'files'. Default is 'folders'
     * @param string $orderBy (Optional) Can be 'name', 'created', 'size',
        'downloads' (default is 'name'). When requesting folders, only 'name'
        and 'created' are considered. If 'order_by' is set to anything other
        than 'name' or 'created' when requesting folders, the output order
        will default to 'name'
     * @param string $orderDirection (Optional) Order direction. Can be 'asc'
        or 'desc' (default 'asc')
     * @param int $chunk (Optional) The chunk number starting from 1
     * @return array|bool|null Returns an array contains folder's contents,
        otherwise FALSE if an error occurred
     */
    public function folderGetContent($folderKey = null, $sessionToken = null,
        $contentType = "folders", $orderBy = null, $orderDirection = null, $chunk = null)
    {
        $this->actions[] = "Getting folder contents";

        if (empty($folderKey) && empty($sessionToken))
        {
            return false;
        }

        if (!($contentType == "folders" || $contentType == "files"))
        {
            $contentType = "folders";
        }

        $orderBy = trim(strtolower($orderBy));
        $orderBys = array("name", "created", "size", "downloads");
        if (!is_string($orderBy) || !in_array($orderBy, $orderBys))
        {
            $orderBy = null;
        }

        if (!($orderDirection == "asc" || $orderDirection == "desc"))
        {
            $orderDirection = null;
        }

        if (!is_int($chunk))
        {
            $chunk = null;
        }

        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "content_type" => $contentType,
            "order_by" => $orderBy,
            "order_direction" => $orderDirection,
            "chunk" => $chunk,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_GET_CONTENT"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);
        if (!$data)
        {
            return false;
        }

        return $data["folder_content"];
    }

    /**
     * Returns information about folder nesting (distance from root)
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $folderKey The key that identifies the folder
     * @return array|bool Returns an array contains folder depth, otherwise
        FALSE if an error occurred
     */
    public function folderGetDepth($sessionToken, $folderKey)
    {
        $this->actions[] = "Getting folder distance from root";

        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_GET_DEPTH"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);
        if (!$data)
        {
            return false;
        }

        return $data["folder_depth"];
    }

    /**
     * Returns a list of the a folder's details
     *
     * @access public
     * @author windylea
     * @param array|string $folderKey The key that identifies the folder.
        You can also specify multiple folderkeys separated by comma or just put
        them into an array
     * @param string $sessionToken (Optional) A Session Access Token to authenticate the
        user's current session
     * @return array|bool Returns an array contains details of the folder(s)
     */
    public function folderGetInfo($folderKey, $sessionToken)
    {
        if (is_array($folderKey))
        {
            $folderKey = implode(",", $folderKey);
        }

        $this->actions[] = "Getting folder information";
        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_GET_INFO"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        if(strpos($folderKey, ",") !== false)
        {
            return $data["folder_infos"];
        } else
        {
            return $data["folder_info"];
        }
    }

    /**
     * Returns the number indicating the revision of a folder
     *
     * Any changes made to this folder or its content will increment the
     * revision. when the revision resets to 1, the time stamp 'epoch' is
     * updated so both 'revision' and 'epoch' can be used to identify a
     * unique revision
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $folderKey The key that identifies the folder
     * @return array|bool Returns an array contains epoch and revision
        number for the folder, otherwise FALSE if an error occurred
     */
    public function folderGetRevision($sessionToken, $folderKey)
    {
        $this->actions[] = "Getting folder's revision";

        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_GET_REVISION"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return self::copy($data, "revision", "epoch");
    }

    /**
     * Returns the sibling folders
     *
     * @access public
     * @author windylea
     * @param string $folderKey The key that identifies the folder
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $contentFilter (Optional) Can be 'info', 'files',
        'folders', 'content' or 'all' (default 'all').
        "content" refers to both files and folders.
     * @param int $start (Optional) Request to return results starting from
        this number
     * @param int $limit (Optional) The maximum results to be returned
     * @return array|bool|null Returns an array contains folder sibling,
        otherwise FALSE if an error occurred
     */
    public function folderGetSiblings($folderKey, $sessionToken = null,
        $contentFilter = "all", $start = null, $limit = null)
    {
        $this->actions[] = "Getting folder sibling";

        $contentFilter = trim(strtolower($contentFilter));
        $contentFilters = array("info", "files", "folders", "content", "all");
        if (!is_string($contentFilter) || !in_array($contentFilter, $contentFilters))
        {
            $contentFilter = "all";
        }

        if (!is_int($start))
        {
            $start = null;
        }

        if (!is_int($limit))
        {
            $limit = null;
        }

        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "content_filter" => $contentFilter,
            "start" => $start,
            "limit" => $limit,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_GET_SIBLINGS"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);
        if (!$data)
        {
            return false;
        }

        return self::copy($data, "siblings");
    }

    /**
     * Moves a folder to another folder. Note: This operation also works with
     * foreign folders
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $folderKeySrc The key that identifies the folder
        to be moved. You can also specify multiple folderkeys separated by
        comma or just put them into an array
     * @param string $folderKeyDst (Optional) The key that identifies the
        destination folder. If omitted, the destination folder will be the
        root folder (My Files)
     * @return array|bool Returns an array contains epoch and revision
        number, otherwise FALSE if an error occurred
     */
    public function folderMove($sessionToken, $folderKeySrc, $folderKeyDst = null)
    {
        if (is_array($folderKeySrc))
        {
            $folderKeySrc = implode(",", $folderKeySrc);
        }

        $this->actions[] = "Moving folder";
        $query = array(
            "folder_key_src" => $folderKeySrc,
            "folder_key_dst" => ($folderKeySrc != "")
                                ? $folderKeyDst : null,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_MOVE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Searches the content of the given folder
     *
     * @access public
     * @author windylea
     * @param string $searchText The keywords to search for in filenames,
        folder names, descriptions and tags
     * @param string $folderKey (Optional) If folder_key is not passed, the API
        will return the root folder content ($sessionToken is required this point)
     * @param string $sessionToken (Optional) A Session Access Token to
        authenticate the user's current session (needed only when accessing
        root folder, private folder, or the folder's private content)
     * @return array|bool|null Returns an array contains the search result,
        otherwise FALSE if an error occurred
     */
    public function folderSearch($searchText, $folderKey = null,
        $sessionToken = null)
    {
        $this->actions[] = "Searching folder contents";

        if (empty($folderKey) && empty($sessionToken))
        {
            return false;
        }

        $query = array(
            "search_text" => $searchText,
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["FOLDER_SEARCH"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);
        if (!$data)
        {
            return false;
        }

        return self::copy($data, "results_count", "results");
    }

    /**
     * Updates a folder's information
     *
     * Example of usage :
     * <code>$mflib->folder_update("wl88kcc0k0xvj", array("description" => "a test folder", "tags" => "test,folder"));</code>
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param string $folderKey The quickkey that identifies the folder
     * @param array $information An associative array contains the updated
        folder information
     * @return array|bool Returns an array contains epoch and revision number,
        otherwise FALSE if an error occurred
     */
    public function folderUpdate($sessionToken, $folderKey, $information)
    {
        $this->actions[] = "Updating folder's information";

        $query = array(
            "folder_key" => $folderKey,
            "session_token" => $sessionToken,
            "response_format" => $this->responseFormat
        );

        $parameters = array("foldername", "description", "tags", "privacy",
                            "privacy_recursive", "note_subject", "note_description");

        if (is_array($information) && !empty($information))
        {
            foreach($information as $key => $value)
            {
                $key = trim(strtolower($key));
                $value = trim($value);

                if (in_array($key, $parameters))
                {
                    $query[$key] = $value;
                }
            }
        }

        $url = $this->apiUrl["FOLDER_UPDATE"] . "?" . http_build_query($query, "", "&");
        $data = $this->getContents($url);

        if (!$data)
        {
            return false;
        }

        return $data["myfiles_revision"];
    }

    /**
     * Returns the current API version (major.minor)
     *
     * @access public
     * @author windylea
     * @return string Returns the current API version
     */
    public function systemVersion()
    {
        $this->actions[] = "Getting API version";
        $query = array(
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["SYSTEM_GET_VERSION"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);

        return $data["current_api_version"];
    }

    /**
     * Returns all the configuration data about the MediaFire system.
     *
     * @access public
     * @author windylea
     * @return string Returns an array contains the configuration data about
        the MediaFire system
     */
    public function systemInfo()
    {
        $this->actions[] = "Getting MediaFire system information";
        $query = array(
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["SYSTEM_GET_INFO"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);

        return self::copy($data, "current_api_version", "viewable", "editable",
            "image_sizes", "max_keys", "max_objects", "max_image_size");
    }

    /**
     * Returns the list of all supported document types for preview
     *
     * @access public
     * @author windylea
     * @param bool $groupByFiletype Whether to group lists by filetype or not
     * @return string Returns an array contains list of all supported document
        types for preview
     */
    public function systemSupportedMedia($groupByFiletype = false)
    {
        $this->actions[] = "Getting supported document types for preview list";

        if ($groupByFiletype == false || $groupByFiletype == 0)
        {
            $groupByFiletype = "no";
        } else
        {
            $groupByFiletype = "yes";
        }

        $query = array(
            "group_by_filetype" => $groupByFiletype,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["SYSTEM_GET_SUPPORTED_MEDIA"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);

        return $data["viewable"];
    }

    /**
     * Returns the list of all supported documents for editing
     *
     * @access public
     * @author windylea
     * @param bool $groupByFiletype Whether to group lists by filetype or not
     * @return string Returns an array contains list of all editable document
        types
     */
    public function systemEditableMedia($groupByFiletype = false)
    {
        $this->actions[] = "Getting supported document types for editting";

        if ($groupByFiletype == false || $groupByFiletype == 0)
        {
            $groupByFiletype = "no";
        } else
        {
            $groupByFiletype = "yes";
        }

        $query = array(
            "group_by_filetype" => $groupByFiletype,
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["SYSTEM_GET_EDITABLE_MEDIA"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);

        return $data["editable"];
    }

    /**
     * Returns a list of file extensions, their document types and MIME types
     *
     * @access public
     * @author windylea
     * @return string Returns an array contains the information
     */
    public function systemMimeTypes()
    {
        $this->actions[] = "Getting MIME type list";

        $query = array(
            "response_format" => $this->responseFormat
        );

        $url = $this->apiUrl["SYSTEM_GET_MIME_TYPES"] . "?" .
            http_build_query($query, "", "&");

        $data = $this->getContents($url);

        return $data["mime_types"];
    }

    /**
     * Convert a document/image file in user account
     *
     * @access public
     * @author windylea
     * @param string $sessionToken A Session Access Token to authenticate the
        user's current session
     * @param array|string $quickkey The quickkey that identify the files
     * @param array|string $sizeId The output image resolution
     * @param array|string $saveAs (Optional) Path to the file to which the
        binary data will be written
     * @param array|string $page (Optional) The document's page to be converted.
        Default is 'intitial'
     * @param array|string $output (Optional) The output format for document
        conversion. Default is 'pdf'
     * @return bool|string See below
     */
    public function mediaConversion($sessionToken, $quickkey, $saveAs = null,
        $sizeId = "2", $page = "initial", $output = "pdf")
    {
        $this->actions[] = "Converting file";

        /*
         * Get file type and prepare the request parameters
         *
         * $page and $output are required for document conversion only. If file
         * type is image, these parameters will be ignored
         */
        $fileInfo = $this->fileGetInfo($sessionToken, $quickkey);
        if (!$fileInfo)
        {
            $this->showError("Failed to get file information before conversion");
            return false;
        }

        if ($fileInfo["filetype"] == "document")
        {
            $docType = "d";

            $outputs = array("pdf", "swf", "img");
            if (!in_array($output, $outputs))
            {
                $output = "pdf";
            }

            if (!is_int($page) && $page != "initial")
            {
                $page = "initial";
            }
        } elseif ($fileInfo["filetype"] == "image")
        {
            $docType = "i";
            $output = $page = null;
        } else
        {
            $this->showError("File format is not supported");
            return false;
        }

        if (!in_array($sizeId, $this->mcSizeId[$docType]))
        {
            $sizeId = "2";
        }

        $query = array(
            "quickkey" => $quickkey,
            "page" => $page,
            "doc_type" => $docType,
            "output" => $output,
            "size_id" => $sizeId
        );

        $url = $this->apiUrl["MEDIA_CONVERSION"] . "?" . substr($fileInfo["hash"], 0, 4)
            . "&" . http_build_query($query, "", "&");

        $data = $this->getContents($url, null, true);
        $statusCode = preg_match("/ ([0-9]+)/", $this->httpResponseHeader[0], $match);

        switch($match[1])
        {
            /*
             * When the HTTP status code is '200', the request is fulfilled.
             *
             * If $saveAs is set, the received data will be written to this
             * destination file and the data length will be returned. If it is not
             * set then the raw binary data will be returned
             */
            case "200":
                if (is_string($saveAs))
                {
                    $length = file_put_contents($saveAs, $data);
                    if ($length === false)
                    {
                        $this->showError("Failed to write data to file");
                        return false;
                    }

                    return $length;
                } else
                {
                    return $data;
                }
            /*
             * When the HTTP status code is '202', the request is accepted and
             * being processed. The method will return TRUE. Call this method again
             * later to get the converted data.
             */
            case "202":
                return true;
            /*
             * Error occurred
             */
            case "204":
            case "400":
            case "404":
                $this->showError($this->mcStatusCode[$match[1]], $match[1]);
                return false;
        }
    }
}
?>