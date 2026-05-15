<?php
/**
 * CertsApi
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
 * CertsApi Class Doc Comment
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class CertsApi
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
        'configCerts' => [
            'application/json',
        ],
        'enrollCert' => [
            'application/json',
        ],
        'exportCert' => [
            'application/json',
        ],
        'importCert' => [
            'application/json',
        ],
        'listCerts' => [
            'application/json',
        ],
        'shareCerts' => [
            'application/json',
        ],
        'unshareCerts' => [
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
     * Operation configCerts
     *
     * ConfigCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  \IsecureFi\WsApi\Client\Model\ConfigCertsReq $configCertsReq Certs handling settings (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['configCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function configCerts($authorization, $xApiKey, $configCertsReq, string $contentType = self::contentTypes['configCerts'][0])
    {
        list($response) = $this->configCertsWithHttpInfo($authorization, $xApiKey, $configCertsReq, $contentType);
        return $response;
    }

    /**
     * Operation configCertsWithHttpInfo
     *
     * ConfigCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  \IsecureFi\WsApi\Client\Model\ConfigCertsReq $configCertsReq Certs handling settings (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['configCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function configCertsWithHttpInfo($authorization, $xApiKey, $configCertsReq, string $contentType = self::contentTypes['configCerts'][0])
    {
        $request = $this->configCertsRequest($authorization, $xApiKey, $configCertsReq, $contentType);

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
                case 401:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
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
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
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
     * Operation configCertsAsync
     *
     * ConfigCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  \IsecureFi\WsApi\Client\Model\ConfigCertsReq $configCertsReq Certs handling settings (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['configCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function configCertsAsync($authorization, $xApiKey, $configCertsReq, string $contentType = self::contentTypes['configCerts'][0])
    {
        return $this->configCertsAsyncWithHttpInfo($authorization, $xApiKey, $configCertsReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation configCertsAsyncWithHttpInfo
     *
     * ConfigCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  \IsecureFi\WsApi\Client\Model\ConfigCertsReq $configCertsReq Certs handling settings (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['configCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function configCertsAsyncWithHttpInfo($authorization, $xApiKey, $configCertsReq, string $contentType = self::contentTypes['configCerts'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->configCertsRequest($authorization, $xApiKey, $configCertsReq, $contentType);

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
     * Create request for operation 'configCerts'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  \IsecureFi\WsApi\Client\Model\ConfigCertsReq $configCertsReq Certs handling settings (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['configCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function configCertsRequest($authorization, $xApiKey, $configCertsReq, string $contentType = self::contentTypes['configCerts'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling configCerts'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling configCerts'
            );
        }

        // verify the required parameter 'configCertsReq' is set
        if ($configCertsReq === null || (is_array($configCertsReq) && count($configCertsReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $configCertsReq when calling configCerts'
            );
        }


        $resourcePath = '/certs/';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = ObjectSerializer::toHeaderValue($authorization);
        }
        // header params
        if ($xApiKey !== null) {
            $headerParams['x-api-key'] = ObjectSerializer::toHeaderValue($xApiKey);
        }



        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($configCertsReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($configCertsReq));
            } else {
                $httpBody = $configCertsReq;
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

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
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
     * Operation enrollCert
     *
     * EnrollCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\EnrollCertReq $enrollCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['enrollCert'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function enrollCert($authorization, $xApiKey, $bank, $enrollCertReq, string $contentType = self::contentTypes['enrollCert'][0])
    {
        list($response) = $this->enrollCertWithHttpInfo($authorization, $xApiKey, $bank, $enrollCertReq, $contentType);
        return $response;
    }

    /**
     * Operation enrollCertWithHttpInfo
     *
     * EnrollCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\EnrollCertReq $enrollCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['enrollCert'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function enrollCertWithHttpInfo($authorization, $xApiKey, $bank, $enrollCertReq, string $contentType = self::contentTypes['enrollCert'][0])
    {
        $request = $this->enrollCertRequest($authorization, $xApiKey, $bank, $enrollCertReq, $contentType);

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
                case 401:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
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
                case 201:
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
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
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
     * Operation enrollCertAsync
     *
     * EnrollCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\EnrollCertReq $enrollCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['enrollCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function enrollCertAsync($authorization, $xApiKey, $bank, $enrollCertReq, string $contentType = self::contentTypes['enrollCert'][0])
    {
        return $this->enrollCertAsyncWithHttpInfo($authorization, $xApiKey, $bank, $enrollCertReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation enrollCertAsyncWithHttpInfo
     *
     * EnrollCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\EnrollCertReq $enrollCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['enrollCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function enrollCertAsyncWithHttpInfo($authorization, $xApiKey, $bank, $enrollCertReq, string $contentType = self::contentTypes['enrollCert'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->enrollCertRequest($authorization, $xApiKey, $bank, $enrollCertReq, $contentType);

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
     * Create request for operation 'enrollCert'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\EnrollCertReq $enrollCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['enrollCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function enrollCertRequest($authorization, $xApiKey, $bank, $enrollCertReq, string $contentType = self::contentTypes['enrollCert'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling enrollCert'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling enrollCert'
            );
        }

        // verify the required parameter 'bank' is set
        if ($bank === null || (is_array($bank) && count($bank) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $bank when calling enrollCert'
            );
        }

        // verify the required parameter 'enrollCertReq' is set
        if ($enrollCertReq === null || (is_array($enrollCertReq) && count($enrollCertReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $enrollCertReq when calling enrollCert'
            );
        }


        $resourcePath = '/certs/{Bank}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = ObjectSerializer::toHeaderValue($authorization);
        }
        // header params
        if ($xApiKey !== null) {
            $headerParams['x-api-key'] = ObjectSerializer::toHeaderValue($xApiKey);
        }

        // path params
        if ($bank !== null) {
            $resourcePath = str_replace(
                '{Bank}',
                ObjectSerializer::toPathValue($bank),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($enrollCertReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($enrollCertReq));
            } else {
                $httpBody = $enrollCertReq;
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

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
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
     * Operation exportCert
     *
     * ExportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  string $pgpKeyId Short version of a PGP Key id idenfiying the exported Private Key, e.g. &#x60;3A3A59B2&#x60; (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['exportCert'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\ExportCertResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function exportCert($authorization, $xApiKey, $bank, $pgpKeyId, string $contentType = self::contentTypes['exportCert'][0])
    {
        list($response) = $this->exportCertWithHttpInfo($authorization, $xApiKey, $bank, $pgpKeyId, $contentType);
        return $response;
    }

    /**
     * Operation exportCertWithHttpInfo
     *
     * ExportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  string $pgpKeyId Short version of a PGP Key id idenfiying the exported Private Key, e.g. &#x60;3A3A59B2&#x60; (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['exportCert'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\ExportCertResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function exportCertWithHttpInfo($authorization, $xApiKey, $bank, $pgpKeyId, string $contentType = self::contentTypes['exportCert'][0])
    {
        $request = $this->exportCertRequest($authorization, $xApiKey, $bank, $pgpKeyId, $contentType);

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
                        '\IsecureFi\WsApi\Client\Model\ExportCertResp',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
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
                '\IsecureFi\WsApi\Client\Model\ExportCertResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ExportCertResp',
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
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
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
     * Operation exportCertAsync
     *
     * ExportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  string $pgpKeyId Short version of a PGP Key id idenfiying the exported Private Key, e.g. &#x60;3A3A59B2&#x60; (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['exportCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function exportCertAsync($authorization, $xApiKey, $bank, $pgpKeyId, string $contentType = self::contentTypes['exportCert'][0])
    {
        return $this->exportCertAsyncWithHttpInfo($authorization, $xApiKey, $bank, $pgpKeyId, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation exportCertAsyncWithHttpInfo
     *
     * ExportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  string $pgpKeyId Short version of a PGP Key id idenfiying the exported Private Key, e.g. &#x60;3A3A59B2&#x60; (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['exportCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function exportCertAsyncWithHttpInfo($authorization, $xApiKey, $bank, $pgpKeyId, string $contentType = self::contentTypes['exportCert'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\ExportCertResp';
        $request = $this->exportCertRequest($authorization, $xApiKey, $bank, $pgpKeyId, $contentType);

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
     * Create request for operation 'exportCert'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  string $pgpKeyId Short version of a PGP Key id idenfiying the exported Private Key, e.g. &#x60;3A3A59B2&#x60; (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['exportCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function exportCertRequest($authorization, $xApiKey, $bank, $pgpKeyId, string $contentType = self::contentTypes['exportCert'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling exportCert'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling exportCert'
            );
        }

        // verify the required parameter 'bank' is set
        if ($bank === null || (is_array($bank) && count($bank) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $bank when calling exportCert'
            );
        }

        // verify the required parameter 'pgpKeyId' is set
        if ($pgpKeyId === null || (is_array($pgpKeyId) && count($pgpKeyId) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $pgpKeyId when calling exportCert'
            );
        }


        $resourcePath = '/certs/{Bank}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $pgpKeyId,
            'PgpKeyId', // param base name
            'string', // openApiType
            '', // style
            false, // explode
            true // required
        ) ?? []);

        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = ObjectSerializer::toHeaderValue($authorization);
        }
        // header params
        if ($xApiKey !== null) {
            $headerParams['x-api-key'] = ObjectSerializer::toHeaderValue($xApiKey);
        }

        // path params
        if ($bank !== null) {
            $resourcePath = str_replace(
                '{Bank}',
                ObjectSerializer::toPathValue($bank),
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

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
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
     * Operation importCert
     *
     * ImportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\ImportCertReq $importCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['importCert'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function importCert($authorization, $xApiKey, $bank, $importCertReq, string $contentType = self::contentTypes['importCert'][0])
    {
        list($response) = $this->importCertWithHttpInfo($authorization, $xApiKey, $bank, $importCertReq, $contentType);
        return $response;
    }

    /**
     * Operation importCertWithHttpInfo
     *
     * ImportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\ImportCertReq $importCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['importCert'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function importCertWithHttpInfo($authorization, $xApiKey, $bank, $importCertReq, string $contentType = self::contentTypes['importCert'][0])
    {
        $request = $this->importCertRequest($authorization, $xApiKey, $bank, $importCertReq, $contentType);

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
                case 401:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
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
                case 201:
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
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
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
     * Operation importCertAsync
     *
     * ImportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\ImportCertReq $importCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['importCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function importCertAsync($authorization, $xApiKey, $bank, $importCertReq, string $contentType = self::contentTypes['importCert'][0])
    {
        return $this->importCertAsyncWithHttpInfo($authorization, $xApiKey, $bank, $importCertReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation importCertAsyncWithHttpInfo
     *
     * ImportCert
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\ImportCertReq $importCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['importCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function importCertAsyncWithHttpInfo($authorization, $xApiKey, $bank, $importCertReq, string $contentType = self::contentTypes['importCert'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->importCertRequest($authorization, $xApiKey, $bank, $importCertReq, $contentType);

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
     * Create request for operation 'importCert'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\ImportCertReq $importCertReq Certs parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['importCert'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function importCertRequest($authorization, $xApiKey, $bank, $importCertReq, string $contentType = self::contentTypes['importCert'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling importCert'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling importCert'
            );
        }

        // verify the required parameter 'bank' is set
        if ($bank === null || (is_array($bank) && count($bank) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $bank when calling importCert'
            );
        }

        // verify the required parameter 'importCertReq' is set
        if ($importCertReq === null || (is_array($importCertReq) && count($importCertReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $importCertReq when calling importCert'
            );
        }


        $resourcePath = '/certs/{Bank}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = ObjectSerializer::toHeaderValue($authorization);
        }
        // header params
        if ($xApiKey !== null) {
            $headerParams['x-api-key'] = ObjectSerializer::toHeaderValue($xApiKey);
        }

        // path params
        if ($bank !== null) {
            $resourcePath = str_replace(
                '{Bank}',
                ObjectSerializer::toPathValue($bank),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($importCertReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($importCertReq));
            } else {
                $httpBody = $importCertReq;
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

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
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
     * Operation listCerts
     *
     * ListCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\ListCertsResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function listCerts($authorization, $xApiKey, string $contentType = self::contentTypes['listCerts'][0])
    {
        list($response) = $this->listCertsWithHttpInfo($authorization, $xApiKey, $contentType);
        return $response;
    }

    /**
     * Operation listCertsWithHttpInfo
     *
     * ListCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\ListCertsResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function listCertsWithHttpInfo($authorization, $xApiKey, string $contentType = self::contentTypes['listCerts'][0])
    {
        $request = $this->listCertsRequest($authorization, $xApiKey, $contentType);

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
                        '\IsecureFi\WsApi\Client\Model\ListCertsResp',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
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
                '\IsecureFi\WsApi\Client\Model\ListCertsResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ListCertsResp',
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
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
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
     * Operation listCertsAsync
     *
     * ListCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listCertsAsync($authorization, $xApiKey, string $contentType = self::contentTypes['listCerts'][0])
    {
        return $this->listCertsAsyncWithHttpInfo($authorization, $xApiKey, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listCertsAsyncWithHttpInfo
     *
     * ListCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listCertsAsyncWithHttpInfo($authorization, $xApiKey, string $contentType = self::contentTypes['listCerts'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\ListCertsResp';
        $request = $this->listCertsRequest($authorization, $xApiKey, $contentType);

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
     * Create request for operation 'listCerts'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listCertsRequest($authorization, $xApiKey, string $contentType = self::contentTypes['listCerts'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling listCerts'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling listCerts'
            );
        }


        $resourcePath = '/certs/';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = ObjectSerializer::toHeaderValue($authorization);
        }
        // header params
        if ($xApiKey !== null) {
            $headerParams['x-api-key'] = ObjectSerializer::toHeaderValue($xApiKey);
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

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
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
     * Operation shareCerts
     *
     * ShareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Share certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['shareCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\ShareCertsResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function shareCerts($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['shareCerts'][0])
    {
        list($response) = $this->shareCertsWithHttpInfo($authorization, $xApiKey, $extEmail, $contentType);
        return $response;
    }

    /**
     * Operation shareCertsWithHttpInfo
     *
     * ShareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Share certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['shareCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\ShareCertsResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function shareCertsWithHttpInfo($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['shareCerts'][0])
    {
        $request = $this->shareCertsRequest($authorization, $xApiKey, $extEmail, $contentType);

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
                        '\IsecureFi\WsApi\Client\Model\ShareCertsResp',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
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
                '\IsecureFi\WsApi\Client\Model\ShareCertsResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ShareCertsResp',
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
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
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
     * Operation shareCertsAsync
     *
     * ShareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Share certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['shareCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function shareCertsAsync($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['shareCerts'][0])
    {
        return $this->shareCertsAsyncWithHttpInfo($authorization, $xApiKey, $extEmail, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation shareCertsAsyncWithHttpInfo
     *
     * ShareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Share certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['shareCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function shareCertsAsyncWithHttpInfo($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['shareCerts'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\ShareCertsResp';
        $request = $this->shareCertsRequest($authorization, $xApiKey, $extEmail, $contentType);

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
     * Create request for operation 'shareCerts'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Share certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['shareCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function shareCertsRequest($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['shareCerts'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling shareCerts'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling shareCerts'
            );
        }

        // verify the required parameter 'extEmail' is set
        if ($extEmail === null || (is_array($extEmail) && count($extEmail) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $extEmail when calling shareCerts'
            );
        }


        $resourcePath = '/certs/shared/{ExtEmail}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = ObjectSerializer::toHeaderValue($authorization);
        }
        // header params
        if ($xApiKey !== null) {
            $headerParams['x-api-key'] = ObjectSerializer::toHeaderValue($xApiKey);
        }

        // path params
        if ($extEmail !== null) {
            $resourcePath = str_replace(
                '{ExtEmail}',
                ObjectSerializer::toPathValue($extEmail),
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

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
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
     * Operation unshareCerts
     *
     * UnshareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Unshare certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unshareCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\UnshareCertsResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function unshareCerts($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['unshareCerts'][0])
    {
        list($response) = $this->unshareCertsWithHttpInfo($authorization, $xApiKey, $extEmail, $contentType);
        return $response;
    }

    /**
     * Operation unshareCertsWithHttpInfo
     *
     * UnshareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Unshare certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unshareCerts'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\UnshareCertsResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function unshareCertsWithHttpInfo($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['unshareCerts'][0])
    {
        $request = $this->unshareCertsRequest($authorization, $xApiKey, $extEmail, $contentType);

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
                        '\IsecureFi\WsApi\Client\Model\UnshareCertsResp',
                        $request,
                        $response,
                    );
                case 400:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 401:
                    return $this->handleResponseWithDataType(
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $request,
                        $response,
                    );
                case 403:
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
                '\IsecureFi\WsApi\Client\Model\UnshareCertsResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\UnshareCertsResp',
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
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    throw $e;
                case 403:
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
     * Operation unshareCertsAsync
     *
     * UnshareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Unshare certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unshareCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function unshareCertsAsync($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['unshareCerts'][0])
    {
        return $this->unshareCertsAsyncWithHttpInfo($authorization, $xApiKey, $extEmail, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation unshareCertsAsyncWithHttpInfo
     *
     * UnshareCerts
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Unshare certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unshareCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function unshareCertsAsyncWithHttpInfo($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['unshareCerts'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\UnshareCertsResp';
        $request = $this->unshareCertsRequest($authorization, $xApiKey, $extEmail, $contentType);

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
     * Create request for operation 'unshareCerts'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $extEmail Unshare certs with _ExtEMail_ account. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['unshareCerts'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function unshareCertsRequest($authorization, $xApiKey, $extEmail, string $contentType = self::contentTypes['unshareCerts'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling unshareCerts'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling unshareCerts'
            );
        }

        // verify the required parameter 'extEmail' is set
        if ($extEmail === null || (is_array($extEmail) && count($extEmail) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $extEmail when calling unshareCerts'
            );
        }


        $resourcePath = '/certs/shared/{ExtEmail}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = ObjectSerializer::toHeaderValue($authorization);
        }
        // header params
        if ($xApiKey !== null) {
            $headerParams['x-api-key'] = ObjectSerializer::toHeaderValue($xApiKey);
        }

        // path params
        if ($extEmail !== null) {
            $resourcePath = str_replace(
                '{ExtEmail}',
                ObjectSerializer::toPathValue($extEmail),
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

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
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
            'DELETE',
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
