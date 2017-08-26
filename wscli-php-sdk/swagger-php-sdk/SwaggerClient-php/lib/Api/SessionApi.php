<?php
/**
 * SessionApi
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * ISECure WS Channel API
 *
 * The API provides normal file based access to all common banks in Finland via WebServices channel on the service side, including certificate enrollment (PKI) with automatic renewals. Additionally, user account management, password recovery, and SMS based 2nd or Multi Factor Authentication (MFA) are provided (AWS Cognito Your User Pool). Access is secured with HTTPS/TLS (AWS API Gateway), using email address as username and password. On login, password is RSA encrypted along with dynamic parameters fetched from the service (username specific challenge response). Every integrator has own API Key and every user account belongs to one integrator. Certificate sharing between accounts is possible under the same integrator API Key, meaning that the enrolled bank connection certificates are shared. Under the same email address / username, both *admin* and *data* accounts must be used as they have separate passwords and differing functions.
 *
 * OpenAPI spec version: v2.2.4
 * Contact: dan.forsberg@isecure.fi
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Api;

use \Swagger\Client\ApiClient;
use \Swagger\Client\ApiException;
use \Swagger\Client\Configuration;
use \Swagger\Client\ObjectSerializer;

/**
 * SessionApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class SessionApi
{
    /**
     * API Client
     *
     * @var \Swagger\Client\ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param \Swagger\Client\ApiClient|null $apiClient The api client to use
     */
    public function __construct(\Swagger\Client\ApiClient $apiClient = null)
    {
        if ($apiClient === null) {
            $apiClient = new ApiClient();
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return \Swagger\Client\ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param \Swagger\Client\ApiClient $apiClient set the API client
     *
     * @return SessionApi
     */
    public function setApiClient(\Swagger\Client\ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation initLogin
     *
     * InitLogin
     *
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\InitLoginResp
     */
    public function initLogin($email, $mode)
    {
        list($response) = $this->initLoginWithHttpInfo($email, $mode);
        return $response;
    }

    /**
     * Operation initLoginWithHttpInfo
     *
     * InitLogin
     *
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\InitLoginResp, HTTP status code, HTTP response headers (array of strings)
     */
    public function initLoginWithHttpInfo($email, $mode)
    {
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling initLogin');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling initLogin');
        }
        // parse inputs
        $resourcePath = "/session/{Email}/{Mode}";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                "{" . "Email" . "}",
                $this->apiClient->getSerializer()->toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                "{" . "Mode" . "}",
                $this->apiClient->getSerializer()->toPathValue($mode),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\InitLoginResp',
                '/session/{Email}/{Mode}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\InitLoginResp', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\InitLoginResp', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation login
     *
     * Login
     *
     * @param \Swagger\Client\Model\LoginReq $login_req Login body parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\LoginResp
     */
    public function login($login_req, $email, $mode)
    {
        list($response) = $this->loginWithHttpInfo($login_req, $email, $mode);
        return $response;
    }

    /**
     * Operation loginWithHttpInfo
     *
     * Login
     *
     * @param \Swagger\Client\Model\LoginReq $login_req Login body parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\LoginResp, HTTP status code, HTTP response headers (array of strings)
     */
    public function loginWithHttpInfo($login_req, $email, $mode)
    {
        // verify the required parameter 'login_req' is set
        if ($login_req === null) {
            throw new \InvalidArgumentException('Missing the required parameter $login_req when calling login');
        }
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling login');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling login');
        }
        // parse inputs
        $resourcePath = "/session/{Email}/{Mode}";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                "{" . "Email" . "}",
                $this->apiClient->getSerializer()->toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                "{" . "Mode" . "}",
                $this->apiClient->getSerializer()->toPathValue($mode),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($login_req)) {
            $_tempBody = $login_req;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'POST',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\LoginResp',
                '/session/{Email}/{Mode}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\LoginResp', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\LoginResp', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation loginMFA
     *
     * LoginMFA
     *
     * @param \Swagger\Client\Model\LoginMFAReq $login_mfa_req Session parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\LoginMFAResp
     */
    public function loginMFA($login_mfa_req, $email, $mode)
    {
        list($response) = $this->loginMFAWithHttpInfo($login_mfa_req, $email, $mode);
        return $response;
    }

    /**
     * Operation loginMFAWithHttpInfo
     *
     * LoginMFA
     *
     * @param \Swagger\Client\Model\LoginMFAReq $login_mfa_req Session parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\LoginMFAResp, HTTP status code, HTTP response headers (array of strings)
     */
    public function loginMFAWithHttpInfo($login_mfa_req, $email, $mode)
    {
        // verify the required parameter 'login_mfa_req' is set
        if ($login_mfa_req === null) {
            throw new \InvalidArgumentException('Missing the required parameter $login_mfa_req when calling loginMFA');
        }
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling loginMFA');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling loginMFA');
        }
        // parse inputs
        $resourcePath = "/session/{Email}/{Mode}/mfacode";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                "{" . "Email" . "}",
                $this->apiClient->getSerializer()->toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                "{" . "Mode" . "}",
                $this->apiClient->getSerializer()->toPathValue($mode),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($login_mfa_req)) {
            $_tempBody = $login_mfa_req;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'PUT',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\LoginMFAResp',
                '/session/{Email}/{Mode}/mfacode'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\LoginMFAResp', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\LoginMFAResp', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation logout
     *
     * Logout
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\Response
     */
    public function logout($authorization, $email, $mode)
    {
        list($response) = $this->logoutWithHttpInfo($authorization, $email, $mode);
        return $response;
    }

    /**
     * Operation logoutWithHttpInfo
     *
     * Logout
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function logoutWithHttpInfo($authorization, $email, $mode)
    {
        // verify the required parameter 'authorization' is set
        if ($authorization === null) {
            throw new \InvalidArgumentException('Missing the required parameter $authorization when calling logout');
        }
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling logout');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling logout');
        }
        // parse inputs
        $resourcePath = "/session/{Email}/{Mode}";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = $this->apiClient->getSerializer()->toHeaderValue($authorization);
        }
        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                "{" . "Email" . "}",
                $this->apiClient->getSerializer()->toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                "{" . "Mode" . "}",
                $this->apiClient->getSerializer()->toPathValue($mode),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        
        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires API key authentication
        $apiKey = $this->apiClient->getApiKeyWithPrefix('Authorization');
        if (strlen($apiKey) !== 0) {
            $headerParams['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->apiClient->getApiKeyWithPrefix('x-api-key');
        if (strlen($apiKey) !== 0) {
            $headerParams['x-api-key'] = $apiKey;
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'DELETE',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\Response',
                '/session/{Email}/{Mode}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\Response', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Response', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ErrorResponse', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }
}
