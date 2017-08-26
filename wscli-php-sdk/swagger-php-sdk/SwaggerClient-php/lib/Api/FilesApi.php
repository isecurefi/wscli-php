<?php
/**
 * FilesApi
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
 * FilesApi Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class FilesApi
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
     * @return FilesApi
     */
    public function setApiClient(\Swagger\Client\ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation deleteFile
     *
     * DeleteFile
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param string $file_type File reference *id* from list files (required)
     * @param string $file_reference File reference *id* from list files (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\Response
     */
    public function deleteFile($authorization, $bank, $file_type, $file_reference)
    {
        list($response) = $this->deleteFileWithHttpInfo($authorization, $bank, $file_type, $file_reference);
        return $response;
    }

    /**
     * Operation deleteFileWithHttpInfo
     *
     * DeleteFile
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param string $file_type File reference *id* from list files (required)
     * @param string $file_reference File reference *id* from list files (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function deleteFileWithHttpInfo($authorization, $bank, $file_type, $file_reference)
    {
        // verify the required parameter 'authorization' is set
        if ($authorization === null) {
            throw new \InvalidArgumentException('Missing the required parameter $authorization when calling deleteFile');
        }
        // verify the required parameter 'bank' is set
        if ($bank === null) {
            throw new \InvalidArgumentException('Missing the required parameter $bank when calling deleteFile');
        }
        // verify the required parameter 'file_type' is set
        if ($file_type === null) {
            throw new \InvalidArgumentException('Missing the required parameter $file_type when calling deleteFile');
        }
        // verify the required parameter 'file_reference' is set
        if ($file_reference === null) {
            throw new \InvalidArgumentException('Missing the required parameter $file_reference when calling deleteFile');
        }
        // parse inputs
        $resourcePath = "/files/{Bank}/{FileType}/{FileReference}";
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
        if ($bank !== null) {
            $resourcePath = str_replace(
                "{" . "Bank" . "}",
                $this->apiClient->getSerializer()->toPathValue($bank),
                $resourcePath
            );
        }
        // path params
        if ($file_type !== null) {
            $resourcePath = str_replace(
                "{" . "FileType" . "}",
                $this->apiClient->getSerializer()->toPathValue($file_type),
                $resourcePath
            );
        }
        // path params
        if ($file_reference !== null) {
            $resourcePath = str_replace(
                "{" . "FileReference" . "}",
                $this->apiClient->getSerializer()->toPathValue($file_reference),
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
                '/files/{Bank}/{FileType}/{FileReference}'
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

    /**
     * Operation downloadFile
     *
     * DownloadFile
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param string $file_type File type from list files (required)
     * @param string $file_reference File reference identifier from list files (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\DownloadFileResp
     */
    public function downloadFile($authorization, $bank, $file_type, $file_reference)
    {
        list($response) = $this->downloadFileWithHttpInfo($authorization, $bank, $file_type, $file_reference);
        return $response;
    }

    /**
     * Operation downloadFileWithHttpInfo
     *
     * DownloadFile
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param string $file_type File type from list files (required)
     * @param string $file_reference File reference identifier from list files (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\DownloadFileResp, HTTP status code, HTTP response headers (array of strings)
     */
    public function downloadFileWithHttpInfo($authorization, $bank, $file_type, $file_reference)
    {
        // verify the required parameter 'authorization' is set
        if ($authorization === null) {
            throw new \InvalidArgumentException('Missing the required parameter $authorization when calling downloadFile');
        }
        // verify the required parameter 'bank' is set
        if ($bank === null) {
            throw new \InvalidArgumentException('Missing the required parameter $bank when calling downloadFile');
        }
        // verify the required parameter 'file_type' is set
        if ($file_type === null) {
            throw new \InvalidArgumentException('Missing the required parameter $file_type when calling downloadFile');
        }
        // verify the required parameter 'file_reference' is set
        if ($file_reference === null) {
            throw new \InvalidArgumentException('Missing the required parameter $file_reference when calling downloadFile');
        }
        // parse inputs
        $resourcePath = "/files/{Bank}/{FileType}/{FileReference}";
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
        if ($bank !== null) {
            $resourcePath = str_replace(
                "{" . "Bank" . "}",
                $this->apiClient->getSerializer()->toPathValue($bank),
                $resourcePath
            );
        }
        // path params
        if ($file_type !== null) {
            $resourcePath = str_replace(
                "{" . "FileType" . "}",
                $this->apiClient->getSerializer()->toPathValue($file_type),
                $resourcePath
            );
        }
        // path params
        if ($file_reference !== null) {
            $resourcePath = str_replace(
                "{" . "FileReference" . "}",
                $this->apiClient->getSerializer()->toPathValue($file_reference),
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
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\DownloadFileResp',
                '/files/{Bank}/{FileType}/{FileReference}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\DownloadFileResp', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\DownloadFileResp', $e->getResponseHeaders());
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

    /**
     * Operation listFiles
     *
     * ListFiles
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param string $status Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; (optional)
     * @param string $file_type *Bank* specific *FileType* identifies the file type to be listed (optional)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\ListFilesResp
     */
    public function listFiles($authorization, $bank, $status = null, $file_type = null)
    {
        list($response) = $this->listFilesWithHttpInfo($authorization, $bank, $status, $file_type);
        return $response;
    }

    /**
     * Operation listFilesWithHttpInfo
     *
     * ListFiles
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param string $bank *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. (required)
     * @param string $status Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; (optional)
     * @param string $file_type *Bank* specific *FileType* identifies the file type to be listed (optional)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\ListFilesResp, HTTP status code, HTTP response headers (array of strings)
     */
    public function listFilesWithHttpInfo($authorization, $bank, $status = null, $file_type = null)
    {
        // verify the required parameter 'authorization' is set
        if ($authorization === null) {
            throw new \InvalidArgumentException('Missing the required parameter $authorization when calling listFiles');
        }
        // verify the required parameter 'bank' is set
        if ($bank === null) {
            throw new \InvalidArgumentException('Missing the required parameter $bank when calling listFiles');
        }
        // parse inputs
        $resourcePath = "/files/{Bank}";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // query params
        if ($status !== null) {
            $queryParams['Status'] = $this->apiClient->getSerializer()->toQueryValue($status);
        }
        // query params
        if ($file_type !== null) {
            $queryParams['FileType'] = $this->apiClient->getSerializer()->toQueryValue($file_type);
        }
        // header params
        if ($authorization !== null) {
            $headerParams['Authorization'] = $this->apiClient->getSerializer()->toHeaderValue($authorization);
        }
        // path params
        if ($bank !== null) {
            $resourcePath = str_replace(
                "{" . "Bank" . "}",
                $this->apiClient->getSerializer()->toPathValue($bank),
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
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\ListFilesResp',
                '/files/{Bank}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\ListFilesResp', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Swagger\Client\Model\ListFilesResp', $e->getResponseHeaders());
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

    /**
     * Operation uploadFile
     *
     * UploadFile
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param \Swagger\Client\Model\UploadFileReq $upload_file_req Files parameters (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return \Swagger\Client\Model\Response
     */
    public function uploadFile($authorization, $upload_file_req)
    {
        list($response) = $this->uploadFileWithHttpInfo($authorization, $upload_file_req);
        return $response;
    }

    /**
     * Operation uploadFileWithHttpInfo
     *
     * UploadFile
     *
     * @param string $authorization Use _IdToken_ from the Login response as the Authorization header (required)
     * @param \Swagger\Client\Model\UploadFileReq $upload_file_req Files parameters (required)
     * @throws \Swagger\Client\ApiException on non-2xx response
     * @return array of \Swagger\Client\Model\Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function uploadFileWithHttpInfo($authorization, $upload_file_req)
    {
        // verify the required parameter 'authorization' is set
        if ($authorization === null) {
            throw new \InvalidArgumentException('Missing the required parameter $authorization when calling uploadFile');
        }
        // verify the required parameter 'upload_file_req' is set
        if ($upload_file_req === null) {
            throw new \InvalidArgumentException('Missing the required parameter $upload_file_req when calling uploadFile');
        }
        // parse inputs
        $resourcePath = "/files/{Bank}";
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
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // body params
        $_tempBody = null;
        if (isset($upload_file_req)) {
            $_tempBody = $upload_file_req;
        }

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
                'PUT',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Swagger\Client\Model\Response',
                '/files/{Bank}'
            );

            return [$this->apiClient->getSerializer()->deserialize($response, '\Swagger\Client\Model\Response', $httpHeader), $statusCode, $httpHeader];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
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
