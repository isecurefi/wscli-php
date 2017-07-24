<?php
/**
 * AccountApi
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
 * OpenAPI spec version: v2.1.0
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
 * AccountApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class AccountApi
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
     * @return AccountApi
     */
    public function setApiClient(\Swagger\Client\ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation initPasswordReset
     *
     * InitPasswordReset
     *
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\Response
     */
    public function initPasswordReset($email, $mode)
    {
        list($response) = $this->initPasswordResetWithHttpInfo($email, $mode);
        return $response;
    }

    /**
     * Operation initPasswordResetWithHttpInfo
     *
     * InitPasswordReset
     *
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function initPasswordResetWithHttpInfo($email, $mode)
    {
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling initPasswordReset');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling initPasswordReset');
        }
        // parse inputs
        $resourcePath = "/account/{Email}/{Mode}/password";
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
                '\Swagger\Client\Model\Response',
                '/account/{Email}/{Mode}/password'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\Response', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Response', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation initRegister
     *
     * InitRegister
     *
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\InitRegisterResp
     */
    public function initRegister($email, $mode)
    {
        list($response) = $this->initRegisterWithHttpInfo($email, $mode);
        return $response;
    }

    /**
     * Operation initRegisterWithHttpInfo
     *
     * InitRegister
     *
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\InitRegisterResp, HTTP status code, HTTP response headers (array of strings)
     */
    public function initRegisterWithHttpInfo($email, $mode)
    {
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling initRegister');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling initRegister');
        }
        // parse inputs
        $resourcePath = "/account/{Email}/{Mode}";
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
                '\Swagger\Client\Model\InitRegisterResp',
                '/account/{Email}/{Mode}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\InitRegisterResp', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\InitRegisterResp', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation passwordReset
     *
     * PasswordReset
     *
     * @param \Swagger\Client\Model\PasswordResetReq $password_reset_req Account parameters (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\Response
     */
    public function passwordReset($password_reset_req)
    {
        list($response) = $this->passwordResetWithHttpInfo($password_reset_req);
        return $response;
    }

    /**
     * Operation passwordResetWithHttpInfo
     *
     * PasswordReset
     *
     * @param \Swagger\Client\Model\PasswordResetReq $password_reset_req Account parameters (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function passwordResetWithHttpInfo($password_reset_req)
    {
        // verify the required parameter 'password_reset_req' is set
        if ($password_reset_req === null) {
            throw new \InvalidArgumentException('Missing the required parameter $password_reset_req when calling passwordReset');
        }
        // parse inputs
        $resourcePath = "/account/{Email}/{Mode}/password";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($password_reset_req)) {
            $_tempBody = $password_reset_req;
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
                '\Swagger\Client\Model\Response',
                '/account/{Email}/{Mode}/password'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\Response', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Response', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation register
     *
     * Register
     *
     * @param \Swagger\Client\Model\RegisterReq $register_req Account parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\RegisterResp
     */
    public function register($register_req, $email, $mode)
    {
        list($response) = $this->registerWithHttpInfo($register_req, $email, $mode);
        return $response;
    }

    /**
     * Operation registerWithHttpInfo
     *
     * Register
     *
     * @param \Swagger\Client\Model\RegisterReq $register_req Account parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\RegisterResp, HTTP status code, HTTP response headers (array of strings)
     */
    public function registerWithHttpInfo($register_req, $email, $mode)
    {
        // verify the required parameter 'register_req' is set
        if ($register_req === null) {
            throw new \InvalidArgumentException('Missing the required parameter $register_req when calling register');
        }
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling register');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling register');
        }
        // parse inputs
        $resourcePath = "/account/{Email}/{Mode}";
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
        if (isset($register_req)) {
            $_tempBody = $register_req;
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
                '\Swagger\Client\Model\RegisterResp',
                '/account/{Email}/{Mode}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\RegisterResp', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\RegisterResp', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation verifyEmail
     *
     * VerifyEmail
     *
     * @param \Swagger\Client\Model\VerifyEmailReq $verify_email_req Account parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\Response
     */
    public function verifyEmail($verify_email_req, $email, $mode)
    {
        list($response) = $this->verifyEmailWithHttpInfo($verify_email_req, $email, $mode);
        return $response;
    }

    /**
     * Operation verifyEmailWithHttpInfo
     *
     * VerifyEmail
     *
     * @param \Swagger\Client\Model\VerifyEmailReq $verify_email_req Account parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function verifyEmailWithHttpInfo($verify_email_req, $email, $mode)
    {
        // verify the required parameter 'verify_email_req' is set
        if ($verify_email_req === null) {
            throw new \InvalidArgumentException('Missing the required parameter $verify_email_req when calling verifyEmail');
        }
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling verifyEmail');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling verifyEmail');
        }
        // parse inputs
        $resourcePath = "/account/{Email}/{Mode}";
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
        if (isset($verify_email_req)) {
            $_tempBody = $verify_email_req;
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
                '\Swagger\Client\Model\Response',
                '/account/{Email}/{Mode}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\Response', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Response', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation verifyPhone
     *
     * VerifyPhone
     *
     * @param \Swagger\Client\Model\VerifyPhoneReq $verify_phone_req Account parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param string $phone Phone number with country code, e.g. &#x60;+358401234567&#x60; (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\Response
     */
    public function verifyPhone($verify_phone_req, $email, $mode, $phone)
    {
        list($response) = $this->verifyPhoneWithHttpInfo($verify_phone_req, $email, $mode, $phone);
        return $response;
    }

    /**
     * Operation verifyPhoneWithHttpInfo
     *
     * VerifyPhone
     *
     * @param \Swagger\Client\Model\VerifyPhoneReq $verify_phone_req Account parameters (required)
     * @param string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param string $phone Phone number with country code, e.g. &#x60;+358401234567&#x60; (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function verifyPhoneWithHttpInfo($verify_phone_req, $email, $mode, $phone)
    {
        // verify the required parameter 'verify_phone_req' is set
        if ($verify_phone_req === null) {
            throw new \InvalidArgumentException('Missing the required parameter $verify_phone_req when calling verifyPhone');
        }
        // verify the required parameter 'email' is set
        if ($email === null) {
            throw new \InvalidArgumentException('Missing the required parameter $email when calling verifyPhone');
        }
        // verify the required parameter 'mode' is set
        if ($mode === null) {
            throw new \InvalidArgumentException('Missing the required parameter $mode when calling verifyPhone');
        }
        // verify the required parameter 'phone' is set
        if ($phone === null) {
            throw new \InvalidArgumentException('Missing the required parameter $phone when calling verifyPhone');
        }
        // parse inputs
        $resourcePath = "/account/{Email}/{Mode}/{Phone}";
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
        // path params
        if ($phone !== null) {
            $resourcePath = str_replace(
                "{" . "Phone" . "}",
                $this->apiClient->getSerializer()->toPathValue($phone),
                $resourcePath
            );
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($verify_phone_req)) {
            $_tempBody = $verify_phone_req;
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
                '\Swagger\Client\Model\Response',
                '/account/{Email}/{Mode}/{Phone}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\Response', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Response', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }
}
