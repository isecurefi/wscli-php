<?php
/**
 * FilesApi
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
 * FilesApi Class Doc Comment
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class FilesApi
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
        'deleteFile' => [
            'application/json',
        ],
        'downloadFile' => [
            'application/json',
        ],
        'listFiles' => [
            'application/json',
        ],
        'uploadFile' => [
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
     * Operation deleteFile
     *
     * DeleteFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File reference *id* from list files (required)
     * @param  string $fileReference File reference *id* from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteFile'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function deleteFile($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['deleteFile'][0])
    {
        list($response) = $this->deleteFileWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType);
        return $response;
    }

    /**
     * Operation deleteFileWithHttpInfo
     *
     * DeleteFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File reference *id* from list files (required)
     * @param  string $fileReference File reference *id* from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteFile'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\Response|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteFileWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['deleteFile'][0])
    {
        $request = $this->deleteFileRequest($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType);

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
     * Operation deleteFileAsync
     *
     * DeleteFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File reference *id* from list files (required)
     * @param  string $fileReference File reference *id* from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteFileAsync($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['deleteFile'][0])
    {
        return $this->deleteFileAsyncWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation deleteFileAsyncWithHttpInfo
     *
     * DeleteFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File reference *id* from list files (required)
     * @param  string $fileReference File reference *id* from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function deleteFileAsyncWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['deleteFile'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\Response';
        $request = $this->deleteFileRequest($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType);

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
     * Create request for operation 'deleteFile'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File reference *id* from list files (required)
     * @param  string $fileReference File reference *id* from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['deleteFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function deleteFileRequest($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['deleteFile'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling deleteFile'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling deleteFile'
            );
        }

        // verify the required parameter 'bank' is set
        if ($bank === null || (is_array($bank) && count($bank) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $bank when calling deleteFile'
            );
        }

        // verify the required parameter 'fileType' is set
        if ($fileType === null || (is_array($fileType) && count($fileType) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $fileType when calling deleteFile'
            );
        }

        // verify the required parameter 'fileReference' is set
        if ($fileReference === null || (is_array($fileReference) && count($fileReference) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $fileReference when calling deleteFile'
            );
        }


        $resourcePath = '/files/{Bank}/{FileType}/{FileReference}';
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
        // path params
        if ($fileType !== null) {
            $resourcePath = str_replace(
                '{FileType}',
                ObjectSerializer::toPathValue($fileType),
                $resourcePath
            );
        }
        // path params
        if ($fileReference !== null) {
            $resourcePath = str_replace(
                '{FileReference}',
                ObjectSerializer::toPathValue($fileReference),
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
     * Operation downloadFile
     *
     * DownloadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File type from list files (required)
     * @param  string $fileReference File reference identifier from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['downloadFile'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\DownloadFileResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function downloadFile($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['downloadFile'][0])
    {
        list($response) = $this->downloadFileWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType);
        return $response;
    }

    /**
     * Operation downloadFileWithHttpInfo
     *
     * DownloadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File type from list files (required)
     * @param  string $fileReference File reference identifier from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['downloadFile'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\DownloadFileResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function downloadFileWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['downloadFile'][0])
    {
        $request = $this->downloadFileRequest($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType);

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
                        '\IsecureFi\WsApi\Client\Model\DownloadFileResp',
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
                '\IsecureFi\WsApi\Client\Model\DownloadFileResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\DownloadFileResp',
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
     * Operation downloadFileAsync
     *
     * DownloadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File type from list files (required)
     * @param  string $fileReference File reference identifier from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['downloadFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function downloadFileAsync($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['downloadFile'][0])
    {
        return $this->downloadFileAsyncWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation downloadFileAsyncWithHttpInfo
     *
     * DownloadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File type from list files (required)
     * @param  string $fileReference File reference identifier from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['downloadFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function downloadFileAsyncWithHttpInfo($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['downloadFile'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\DownloadFileResp';
        $request = $this->downloadFileRequest($authorization, $xApiKey, $bank, $fileType, $fileReference, $contentType);

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
     * Create request for operation 'downloadFile'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string $fileType File type from list files (required)
     * @param  string $fileReference File reference identifier from list files (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['downloadFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function downloadFileRequest($authorization, $xApiKey, $bank, $fileType, $fileReference, string $contentType = self::contentTypes['downloadFile'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling downloadFile'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling downloadFile'
            );
        }

        // verify the required parameter 'bank' is set
        if ($bank === null || (is_array($bank) && count($bank) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $bank when calling downloadFile'
            );
        }

        // verify the required parameter 'fileType' is set
        if ($fileType === null || (is_array($fileType) && count($fileType) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $fileType when calling downloadFile'
            );
        }

        // verify the required parameter 'fileReference' is set
        if ($fileReference === null || (is_array($fileReference) && count($fileReference) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $fileReference when calling downloadFile'
            );
        }


        $resourcePath = '/files/{Bank}/{FileType}/{FileReference}';
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
        // path params
        if ($fileType !== null) {
            $resourcePath = str_replace(
                '{FileType}',
                ObjectSerializer::toPathValue($fileType),
                $resourcePath
            );
        }
        // path params
        if ($fileReference !== null) {
            $resourcePath = str_replace(
                '{FileReference}',
                ObjectSerializer::toPathValue($fileReference),
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
     * Operation listFiles
     *
     * ListFiles
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string|null $status Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; (optional)
     * @param  string|null $fileType *Bank* specific *FileType* identifies the file type to be listed (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFiles'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\ListFilesResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function listFiles($authorization, $xApiKey, $bank, $status = null, $fileType = null, string $contentType = self::contentTypes['listFiles'][0])
    {
        list($response) = $this->listFilesWithHttpInfo($authorization, $xApiKey, $bank, $status, $fileType, $contentType);
        return $response;
    }

    /**
     * Operation listFilesWithHttpInfo
     *
     * ListFiles
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string|null $status Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; (optional)
     * @param  string|null $fileType *Bank* specific *FileType* identifies the file type to be listed (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFiles'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\ListFilesResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function listFilesWithHttpInfo($authorization, $xApiKey, $bank, $status = null, $fileType = null, string $contentType = self::contentTypes['listFiles'][0])
    {
        $request = $this->listFilesRequest($authorization, $xApiKey, $bank, $status, $fileType, $contentType);

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
                        '\IsecureFi\WsApi\Client\Model\ListFilesResp',
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
                '\IsecureFi\WsApi\Client\Model\ListFilesResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\ListFilesResp',
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
     * Operation listFilesAsync
     *
     * ListFiles
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string|null $status Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; (optional)
     * @param  string|null $fileType *Bank* specific *FileType* identifies the file type to be listed (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFiles'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listFilesAsync($authorization, $xApiKey, $bank, $status = null, $fileType = null, string $contentType = self::contentTypes['listFiles'][0])
    {
        return $this->listFilesAsyncWithHttpInfo($authorization, $xApiKey, $bank, $status, $fileType, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation listFilesAsyncWithHttpInfo
     *
     * ListFiles
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string|null $status Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; (optional)
     * @param  string|null $fileType *Bank* specific *FileType* identifies the file type to be listed (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFiles'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function listFilesAsyncWithHttpInfo($authorization, $xApiKey, $bank, $status = null, $fileType = null, string $contentType = self::contentTypes['listFiles'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\ListFilesResp';
        $request = $this->listFilesRequest($authorization, $xApiKey, $bank, $status, $fileType, $contentType);

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
     * Create request for operation 'listFiles'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  string|null $status Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; (optional)
     * @param  string|null $fileType *Bank* specific *FileType* identifies the file type to be listed (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['listFiles'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function listFilesRequest($authorization, $xApiKey, $bank, $status = null, $fileType = null, string $contentType = self::contentTypes['listFiles'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling listFiles'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling listFiles'
            );
        }

        // verify the required parameter 'bank' is set
        if ($bank === null || (is_array($bank) && count($bank) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $bank when calling listFiles'
            );
        }




        $resourcePath = '/files/{Bank}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $status,
            'Status', // param base name
            'string', // openApiType
            '', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $fileType,
            'FileType', // param base name
            'string', // openApiType
            '', // style
            false, // explode
            false // required
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
     * Operation uploadFile
     *
     * UploadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\UploadFileReq $uploadFileReq Files parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['uploadFile'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \IsecureFi\WsApi\Client\Model\UploadFileResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse
     */
    public function uploadFile($authorization, $xApiKey, $bank, $uploadFileReq, string $contentType = self::contentTypes['uploadFile'][0])
    {
        list($response) = $this->uploadFileWithHttpInfo($authorization, $xApiKey, $bank, $uploadFileReq, $contentType);
        return $response;
    }

    /**
     * Operation uploadFileWithHttpInfo
     *
     * UploadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\UploadFileReq $uploadFileReq Files parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['uploadFile'] to see the possible values for this operation
     *
     * @throws \IsecureFi\WsApi\Client\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \IsecureFi\WsApi\Client\Model\UploadFileResp|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse|\IsecureFi\WsApi\Client\Model\ErrorResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function uploadFileWithHttpInfo($authorization, $xApiKey, $bank, $uploadFileReq, string $contentType = self::contentTypes['uploadFile'][0])
    {
        $request = $this->uploadFileRequest($authorization, $xApiKey, $bank, $uploadFileReq, $contentType);

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
                        '\IsecureFi\WsApi\Client\Model\UploadFileResp',
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
                '\IsecureFi\WsApi\Client\Model\UploadFileResp',
                $request,
                $response,
            );
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\IsecureFi\WsApi\Client\Model\UploadFileResp',
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
     * Operation uploadFileAsync
     *
     * UploadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\UploadFileReq $uploadFileReq Files parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['uploadFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function uploadFileAsync($authorization, $xApiKey, $bank, $uploadFileReq, string $contentType = self::contentTypes['uploadFile'][0])
    {
        return $this->uploadFileAsyncWithHttpInfo($authorization, $xApiKey, $bank, $uploadFileReq, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation uploadFileAsyncWithHttpInfo
     *
     * UploadFile
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\UploadFileReq $uploadFileReq Files parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['uploadFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function uploadFileAsyncWithHttpInfo($authorization, $xApiKey, $bank, $uploadFileReq, string $contentType = self::contentTypes['uploadFile'][0])
    {
        $returnType = '\IsecureFi\WsApi\Client\Model\UploadFileResp';
        $request = $this->uploadFileRequest($authorization, $xApiKey, $bank, $uploadFileReq, $contentType);

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
     * Create request for operation 'uploadFile'
     *
     * @param  string $authorization Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header (required)
     * @param  string $xApiKey Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header (required)
     * @param  string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param  \IsecureFi\WsApi\Client\Model\UploadFileReq $uploadFileReq Files parameters (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['uploadFile'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function uploadFileRequest($authorization, $xApiKey, $bank, $uploadFileReq, string $contentType = self::contentTypes['uploadFile'][0])
    {

        // verify the required parameter 'authorization' is set
        if ($authorization === null || (is_array($authorization) && count($authorization) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $authorization when calling uploadFile'
            );
        }

        // verify the required parameter 'xApiKey' is set
        if ($xApiKey === null || (is_array($xApiKey) && count($xApiKey) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $xApiKey when calling uploadFile'
            );
        }

        // verify the required parameter 'bank' is set
        if ($bank === null || (is_array($bank) && count($bank) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $bank when calling uploadFile'
            );
        }

        // verify the required parameter 'uploadFileReq' is set
        if ($uploadFileReq === null || (is_array($uploadFileReq) && count($uploadFileReq) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $uploadFileReq when calling uploadFile'
            );
        }


        $resourcePath = '/files/{Bank}';
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
        if (isset($uploadFileReq)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($uploadFileReq));
            } else {
                $httpBody = $uploadFileReq;
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
