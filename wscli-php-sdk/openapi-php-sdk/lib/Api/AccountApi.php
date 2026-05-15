<?php
/**
 * AccountApi
 * PHP version 8.1
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * ISECure WS Channel API
 *
 * The API provides secure file exchange with all common banks in Finland via *SEPA WebServices* channel on the API side towards the banks, including certificate enrollment (PKI) with automatic renewals.  The API specification in OpenAPI v2 format can be found on GitHub [isecurefi/wsapi-v2](https://github.com/isecurefi/wsapi-v2). Command line CLI and beefed-up PHP SDK are also available on GitHub [isecurefi/wscli-php](https://github.com/isecurefi/wscli-php).  ``` % export SESSION=~/.wscli/settings.yaml % wscli session login -c $SESSION % export APIKEY=`yq -r .settings.apikey $SESSION` % export IDTOKEN=`yq -r .settings.idtoken $SESSION` % % curl -H Authorization:$IDTOKEN \\         -H x-api-key:$APIKEY \\         https://ws-api.isecure.fi/v2/files/danskebank ```  API provides simple role based access control (RBAC) and user account management, password recovery, and SMS based Multi Factor Authentication based on AWS Cognito Your User Pool managed service.  *NOTE: The API endpoint for production is the same as for test, but without `test.` in the URL. Production and test APIs are deployd on separate AWS accounts*.  *NOTE: The API is run on AWS API Gateway and with AWS Lambda backend. When Lambda functions are cold, there is a small delay in response time. Additionally, banks have considerable delays in their processings, especially with certificate enrollments.*  ### Service enrollment  Every integrator (partner) has own *API Key* and every user account belongs to one integrator. *API Key* is bound with service subscription. In other words, enrolling fresh *API Key* requires service agreement before file transfers are allowed on production accounts.  If user registers with `0` *API Key* (i.e. no *API Key*) she gets a fresh *API Key* and becomes the *API Key* owner. The *API Key* owner account can list all users under the same *API Key*, see the *Integrator API*. Integrators (partners) registers their own *API Key* owner accounts and use it to register their client accounts.  *NOTE: API call rate limits are set and tracked per API Key by AWS API Gateway*.  ### Account management  A user (email address) can register either *admin* or *data* or both roles. The role in the API is referred to as *mode*. Both modes have separate passwords and provide differing capabilities for the user.  Login always requires account mode parameter in addition to user's email address and password. *Admin* mode login always requires an additional SMS one-time-password (MFA), whilst with *data* mode password is enough (suitable for automation). *Admin* mode is used to configure the account (e.g. adding PGP keys and sharing certs) and *data* mode to exchange files. Listing files is allowed on both modes.  *NOTE: Integrator (partner) registers her customers by using her API Key from the API Key owner account.*  ### Bank certificate enrollment  The *SEPA WebServices* connection to the bank requires enrolling PKI certificate with the bank. The *Admin* mode can enroll certificates for different banks, but only one certificate per bank. The corresponding private key is stored encrypted with AWS KMS service.  ### Bank certificate sharing  It is possible to share the same bank certificate with multiple accounts. Certificate sharing between accounts can be configured when accounts have the same API Key. Account that holds the certificate can share/unshare it with another account (*admin* mode operation). Note that only the account that has the certificate can PGP export the certificate and corresponding private key. This allows creating e.g. one *admin* mode only account and multiple *data* mode only accounts, where the *admin* account shares its certificates with other *data* accounts.  An account can never have multiple certificates per bank, be it shared or account's enrolled certificate. This is because the API requires identification of the bank, but not the certificate and private key pair.  ### Access security  Access is secured with TLS on Amazon Web Services (AWS) API Gateway. Inside TLS, secure sessions are established by using email address as username and by RSA encrypting password along with dynamic username specific parameters fetched from the API with `InitRegister` or `InitLogin` API commands (challenge response).  Successful login provides a session token (AWS Cognito User Pool). Authorization happens with the session token (`Authorization`) and *API Key* (`x-api-key`) headers.  Administrative actions require SMS based MFA authentication (see *admin* mode). User account management is handled with AWS Cognito User Pools and each user (email) has separate *admin* and/or *data* mode (role) accounts sharing the same API account data.  ### CHANGELOG  **2.6.0** :: 2021-06-14 - Added token revocation for logout (AWS SDK based new capability), no changes to the API itself.  **2.5.0** :: 2020-04-19 - Removed OPTIONS - Fixed Login response.
 *
 * The version of the OpenAPI document: v2.6.0
 * Contact: dan.forsberg@isecure.fi
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.22.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace IsecureFi\WsApi\Client\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Configuration;
use IsecureFi\WsApi\Client\FormDataProcessor;
use IsecureFi\WsApi\Client\HeaderSelector;
use IsecureFi\WsApi\Client\ObjectSerializer;

/**
 * AccountApi Class Doc Comment
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class AccountApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'initPasswordReset' => [
            'application/json',
        ],
        'initRegister' => [
            'application/json',
        ],
        'passwordReset' => [
            'application/json',
        ],
        'register' => [
            'application/json',
        ],
        'verifyEmail' => [
            'application/json',
        ],
        'verifyPhone' => [
            'application/json',
        ],
    ];

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ?ClientInterface $client = null,
        ?Configuration $config = null,
        ?HeaderSelector $selector = null,
        int $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation initPasswordReset
     *
     * InitPasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initPasswordReset'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function initPasswordReset($email, $mode, string $contentType = self::contentTypes['initPasswordReset'][0])
    {
        list($response) = $this->initPasswordResetWithHttpInfo($email, $mode, $contentType);
        return $response;
    }

    /**
     * Operation initPasswordResetWithHttpInfo
     *
     * InitPasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initPasswordReset'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function initPasswordResetWithHttpInfo($email, $mode, string $contentType = self::contentTypes['initPasswordReset'][0])
    {
        $request = $this->initPasswordResetRequest($email, $mode, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\IsecureFi\WsApi\Client\Model\Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation initPasswordResetAsync
     *
     * InitPasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initPasswordReset'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function initPasswordResetAsync($email, $mode, string $contentType = self::contentTypes['initPasswordReset'][0])
    {
        return $this->initPasswordResetAsyncWithHttpInfo($email, $mode, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation initPasswordResetAsyncWithHttpInfo
     *
     * InitPasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initPasswordReset'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function initPasswordResetAsyncWithHttpInfo($email, $mode, string $contentType = self::contentTypes['initPasswordReset'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->initPasswordResetRequest($email, $mode, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'initPasswordReset'
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initPasswordReset'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function initPasswordResetRequest($email, $mode, string $contentType = self::contentTypes['initPasswordReset'][0])
    {

        // verify the required parameter 'email' is set
        if ($email === null || (is_array($email) && count($email) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $email when calling initPasswordReset'
            );
        }

        // verify the required parameter 'mode' is set
        if ($mode === null || (is_array($mode) && count($mode) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mode when calling initPasswordReset'
            );
        }


        $resourcePath = '/account/{Email}/{Mode}/password';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                '{Email}',
                ObjectSerializer::toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                '{Mode}',
                ObjectSerializer::toPathValue($mode),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation initRegister
     *
     * InitRegister
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initRegister'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\InitRegisterResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function initRegister($email, $mode, string $contentType = self::contentTypes['initRegister'][0])
    {
        list($response) = $this->initRegisterWithHttpInfo($email, $mode, $contentType);
        return $response;
    }

    /**
     * Operation initRegisterWithHttpInfo
     *
     * InitRegister
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initRegister'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\InitRegisterResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function initRegisterWithHttpInfo($email, $mode, string $contentType = self::contentTypes['initRegister'][0])
    {
        $request = $this->initRegisterRequest($email, $mode, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\InitRegisterResp',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\IsecureFi\WsApi\Client\Model\InitRegisterResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\InitRegisterResp',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation initRegisterAsync
     *
     * InitRegister
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initRegister'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function initRegisterAsync($email, $mode, string $contentType = self::contentTypes['initRegister'][0])
    {
        return $this->initRegisterAsyncWithHttpInfo($email, $mode, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation initRegisterAsyncWithHttpInfo
     *
     * InitRegister
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initRegister'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function initRegisterAsyncWithHttpInfo($email, $mode, string $contentType = self::contentTypes['initRegister'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\InitRegisterResp';
        $request = $this->initRegisterRequest($email, $mode, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'initRegister'
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['initRegister'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function initRegisterRequest($email, $mode, string $contentType = self::contentTypes['initRegister'][0])
    {

        // verify the required parameter 'email' is set
        if ($email === null || (is_array($email) && count($email) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $email when calling initRegister'
            );
        }

        // verify the required parameter 'mode' is set
        if ($mode === null || (is_array($mode) && count($mode) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mode when calling initRegister'
            );
        }


        $resourcePath = '/account/{Email}/{Mode}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                '{Email}',
                ObjectSerializer::toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                '{Mode}',
                ObjectSerializer::toPathValue($mode),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation passwordReset
     *
     * PasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\PasswordResetReq $passwordResetReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['passwordReset'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function passwordReset($email, $mode, $passwordResetReq, string $contentType = self::contentTypes['passwordReset'][0])
    {
        list($response) = $this->passwordResetWithHttpInfo($email, $mode, $passwordResetReq, $contentType);
        return $response;
    }

    /**
     * Operation passwordResetWithHttpInfo
     *
     * PasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\PasswordResetReq $passwordResetReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['passwordReset'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function passwordResetWithHttpInfo($email, $mode, $passwordResetReq, string $contentType = self::contentTypes['passwordReset'][0])
    {
        $request = $this->passwordResetRequest($email, $mode, $passwordResetReq, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\IsecureFi\WsApi\Client\Model\Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation passwordResetAsync
     *
     * PasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\PasswordResetReq $passwordResetReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['passwordReset'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function passwordResetAsync($email, $mode, $passwordResetReq, string $contentType = self::contentTypes['passwordReset'][0])
    {
        return $this->passwordResetAsyncWithHttpInfo($email, $mode, $passwordResetReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation passwordResetAsyncWithHttpInfo
     *
     * PasswordReset
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\PasswordResetReq $passwordResetReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['passwordReset'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function passwordResetAsyncWithHttpInfo($email, $mode, $passwordResetReq, string $contentType = self::contentTypes['passwordReset'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->passwordResetRequest($email, $mode, $passwordResetReq, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'passwordReset'
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\PasswordResetReq $passwordResetReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['passwordReset'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function passwordResetRequest($email, $mode, $passwordResetReq, string $contentType = self::contentTypes['passwordReset'][0])
    {

        // verify the required parameter 'email' is set
        if ($email === null || (is_array($email) && count($email) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $email when calling passwordReset'
            );
        }

        // verify the required parameter 'mode' is set
        if ($mode === null || (is_array($mode) && count($mode) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mode when calling passwordReset'
            );
        }

        // verify the required parameter 'passwordResetReq' is set
        if ($passwordResetReq === null || (is_array($passwordResetReq) && count($passwordResetReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $passwordResetReq when calling passwordReset'
            );
        }


        $resourcePath = '/account/{Email}/{Mode}/password';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                '{Email}',
                ObjectSerializer::toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                '{Mode}',
                ObjectSerializer::toPathValue($mode),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($passwordResetReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($passwordResetReq));
            } else {
                $httpBody = $passwordResetReq;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation register
     *
     * Register
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\RegisterReq $registerReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['register'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\RegisterResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function register($email, $mode, $registerReq, string $contentType = self::contentTypes['register'][0])
    {
        list($response) = $this->registerWithHttpInfo($email, $mode, $registerReq, $contentType);
        return $response;
    }

    /**
     * Operation registerWithHttpInfo
     *
     * Register
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\RegisterReq $registerReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['register'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\RegisterResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function registerWithHttpInfo($email, $mode, $registerReq, string $contentType = self::contentTypes['register'][0])
    {
        $request = $this->registerRequest($email, $mode, $registerReq, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 201:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\RegisterResp',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\IsecureFi\WsApi\Client\Model\RegisterResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\RegisterResp',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation registerAsync
     *
     * Register
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\RegisterReq $registerReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['register'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function registerAsync($email, $mode, $registerReq, string $contentType = self::contentTypes['register'][0])
    {
        return $this->registerAsyncWithHttpInfo($email, $mode, $registerReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation registerAsyncWithHttpInfo
     *
     * Register
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\RegisterReq $registerReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['register'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function registerAsyncWithHttpInfo($email, $mode, $registerReq, string $contentType = self::contentTypes['register'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\RegisterResp';
        $request = $this->registerRequest($email, $mode, $registerReq, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'register'
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\RegisterReq $registerReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['register'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function registerRequest($email, $mode, $registerReq, string $contentType = self::contentTypes['register'][0])
    {

        // verify the required parameter 'email' is set
        if ($email === null || (is_array($email) && count($email) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $email when calling register'
            );
        }

        // verify the required parameter 'mode' is set
        if ($mode === null || (is_array($mode) && count($mode) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mode when calling register'
            );
        }

        // verify the required parameter 'registerReq' is set
        if ($registerReq === null || (is_array($registerReq) && count($registerReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $registerReq when calling register'
            );
        }


        $resourcePath = '/account/{Email}/{Mode}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                '{Email}',
                ObjectSerializer::toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                '{Mode}',
                ObjectSerializer::toPathValue($mode),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($registerReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($registerReq));
            } else {
                $httpBody = $registerReq;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation verifyEmail
     *
     * VerifyEmail
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyEmailReq $verifyEmailReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyEmail'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function verifyEmail($email, $mode, $verifyEmailReq, string $contentType = self::contentTypes['verifyEmail'][0])
    {
        list($response) = $this->verifyEmailWithHttpInfo($email, $mode, $verifyEmailReq, $contentType);
        return $response;
    }

    /**
     * Operation verifyEmailWithHttpInfo
     *
     * VerifyEmail
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyEmailReq $verifyEmailReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyEmail'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function verifyEmailWithHttpInfo($email, $mode, $verifyEmailReq, string $contentType = self::contentTypes['verifyEmail'][0])
    {
        $request = $this->verifyEmailRequest($email, $mode, $verifyEmailReq, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\IsecureFi\WsApi\Client\Model\Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation verifyEmailAsync
     *
     * VerifyEmail
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyEmailReq $verifyEmailReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyEmail'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function verifyEmailAsync($email, $mode, $verifyEmailReq, string $contentType = self::contentTypes['verifyEmail'][0])
    {
        return $this->verifyEmailAsyncWithHttpInfo($email, $mode, $verifyEmailReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation verifyEmailAsyncWithHttpInfo
     *
     * VerifyEmail
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyEmailReq $verifyEmailReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyEmail'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function verifyEmailAsyncWithHttpInfo($email, $mode, $verifyEmailReq, string $contentType = self::contentTypes['verifyEmail'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->verifyEmailRequest($email, $mode, $verifyEmailReq, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'verifyEmail'
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyEmailReq $verifyEmailReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyEmail'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function verifyEmailRequest($email, $mode, $verifyEmailReq, string $contentType = self::contentTypes['verifyEmail'][0])
    {

        // verify the required parameter 'email' is set
        if ($email === null || (is_array($email) && count($email) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $email when calling verifyEmail'
            );
        }

        // verify the required parameter 'mode' is set
        if ($mode === null || (is_array($mode) && count($mode) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mode when calling verifyEmail'
            );
        }

        // verify the required parameter 'verifyEmailReq' is set
        if ($verifyEmailReq === null || (is_array($verifyEmailReq) && count($verifyEmailReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $verifyEmailReq when calling verifyEmail'
            );
        }


        $resourcePath = '/account/{Email}/{Mode}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                '{Email}',
                ObjectSerializer::toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                '{Mode}',
                ObjectSerializer::toPathValue($mode),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($verifyEmailReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($verifyEmailReq));
            } else {
                $httpBody = $verifyEmailReq;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation verifyPhone
     *
     * VerifyPhone
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $phone Phone number with country code, e.g. &#x60;+358401234567&#x60; (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyPhoneReq $verifyPhoneReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyPhone'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function verifyPhone($email, $mode, $phone, $verifyPhoneReq, string $contentType = self::contentTypes['verifyPhone'][0])
    {
        list($response) = $this->verifyPhoneWithHttpInfo($email, $mode, $phone, $verifyPhoneReq, $contentType);
        return $response;
    }

    /**
     * Operation verifyPhoneWithHttpInfo
     *
     * VerifyPhone
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $phone Phone number with country code, e.g. &#x60;+358401234567&#x60; (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyPhoneReq $verifyPhoneReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyPhone'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function verifyPhoneWithHttpInfo($email, $mode, $phone, $verifyPhoneReq, string $contentType = self::contentTypes['verifyPhone'][0])
    {
        $request = $this->verifyPhoneRequest($email, $mode, $phone, $verifyPhoneReq, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 500:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
            }

            

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            return $this->handleResponseWithDataType(
                '\IsecureFi\WsApi\Client\Model\Response',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 500:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
            }
        

            throw $e;
        }
    }

    /**
     * Operation verifyPhoneAsync
     *
     * VerifyPhone
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $phone Phone number with country code, e.g. &#x60;+358401234567&#x60; (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyPhoneReq $verifyPhoneReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyPhone'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function verifyPhoneAsync($email, $mode, $phone, $verifyPhoneReq, string $contentType = self::contentTypes['verifyPhone'][0])
    {
        return $this->verifyPhoneAsyncWithHttpInfo($email, $mode, $phone, $verifyPhoneReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation verifyPhoneAsyncWithHttpInfo
     *
     * VerifyPhone
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $phone Phone number with country code, e.g. &#x60;+358401234567&#x60; (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyPhoneReq $verifyPhoneReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyPhone'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function verifyPhoneAsyncWithHttpInfo($email, $mode, $phone, $verifyPhoneReq, string $contentType = self::contentTypes['verifyPhone'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->verifyPhoneRequest($email, $mode, $phone, $verifyPhoneReq, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'verifyPhone'
     *
     * @param  string $email Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; (required)
     * @param  string $mode Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode (required)
     * @param  string $phone Phone number with country code, e.g. &#x60;+358401234567&#x60; (required)
     * @param  \IsecureFi\WsApi\Client\Model\VerifyPhoneReq $verifyPhoneReq Account parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['verifyPhone'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function verifyPhoneRequest($email, $mode, $phone, $verifyPhoneReq, string $contentType = self::contentTypes['verifyPhone'][0])
    {

        // verify the required parameter 'email' is set
        if ($email === null || (is_array($email) && count($email) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $email when calling verifyPhone'
            );
        }

        // verify the required parameter 'mode' is set
        if ($mode === null || (is_array($mode) && count($mode) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mode when calling verifyPhone'
            );
        }

        // verify the required parameter 'phone' is set
        if ($phone === null || (is_array($phone) && count($phone) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $phone when calling verifyPhone'
            );
        }

        // verify the required parameter 'verifyPhoneReq' is set
        if ($verifyPhoneReq === null || (is_array($verifyPhoneReq) && count($verifyPhoneReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $verifyPhoneReq when calling verifyPhone'
            );
        }


        $resourcePath = '/account/{Email}/{Mode}/{Phone}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($email !== null) {
            $resourcePath = str_replace(
                '{Email}',
                ObjectSerializer::toPathValue($email),
                $resourcePath
            );
        }
        // path params
        if ($mode !== null) {
            $resourcePath = str_replace(
                '{Mode}',
                ObjectSerializer::toPathValue($mode),
                $resourcePath
            );
        }
        // path params
        if ($phone !== null) {
            $resourcePath = str_replace(
                '{Phone}',
                ObjectSerializer::toPathValue($phone),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($verifyPhoneReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($verifyPhoneReq));
            } else {
                $httpBody = $verifyPhoneReq;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        if ($this->config->getCertFile()) {
            $options[RequestOptions::CERT] = $this->config->getCertFile();
        }

        if ($this->config->getKeyFile()) {
            $options[RequestOptions::SSL_KEY] = $this->config->getKeyFile();
        }

        return $options;
    }

    private function handleResponseWithDataType(
        string $dataType,
        RequestInterface $request,
        ResponseInterface $response
    ): array {
        if ($dataType === '\SplFileObject') {
            $content = $response->getBody(); //stream goes to serializer
        } else {
            $content = (string) $response->getBody();
            if ($dataType !== 'string') {
                try {
                    $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    throw new ApiException(
                        sprintf(
                            'Error JSON decoding server response (%s)',
                            $request->getUri()
                        ),
                        $response->getStatusCode(),
                        $response->getHeaders(),
                        $content
                    );
                }
            }
        }

        return [
            ObjectSerializer::deserialize($content, $dataType, []),
            $response->getStatusCode(),
            $response->getHeaders()
        ];
    }

    private function responseWithinRangeCode(
        string $rangeCode,
        int $statusCode
    ): bool {
        $left = (int) ($rangeCode[0].'00');
        $right = (int) ($rangeCode[0].'99');

        return $statusCode >= $left && $statusCode <= $right;
    }
}
