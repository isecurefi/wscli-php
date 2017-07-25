# Swagger\Client\FilesApi

All URIs are relative to *https://ws-api.test.isecure.fi/v2/*

Method | HTTP request | Description
------------- | ------------- | -------------
[**deleteFile**](FilesApi.md#deleteFile) | **DELETE** /files/{Bank}/{FileType}/{FileReference} | DeleteFile
[**downloadFile**](FilesApi.md#downloadFile) | **GET** /files/{Bank}/{FileType}/{FileReference} | DownloadFile
[**listFiles**](FilesApi.md#listFiles) | **GET** /files/{Bank} | ListFiles
[**uploadFile**](FilesApi.md#uploadFile) | **PUT** /files/{Bank} | UploadFile


# **deleteFile**
> \Swagger\Client\Model\Response deleteFile($authorization, $bank, $file_type, $file_reference)

DeleteFile

Deletes file on bank side. File is identified with _filereference_.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: Authorizer
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');
// Configure API key authorization: X-Api-Key
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');

$api_instance = new Swagger\Client\Api\FilesApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$bank = "bank_example"; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `spankki`, `alandsbanken` or `SEB`.
$file_type = "file_type_example"; // string | File reference *id* from list files
$file_reference = "file_reference_example"; // string | File reference *id* from list files

try {
    $result = $api_instance->deleteFile($authorization, $bank, $file_type, $file_reference);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->deleteFile: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. |
 **file_type** | **string**| File reference *id* from list files |
 **file_reference** | **string**| File reference *id* from list files |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **downloadFile**
> \Swagger\Client\Model\DownloadFileResp downloadFile($authorization, $bank, $file_type, $file_reference)

DownloadFile

Downloads file identified with _filereference_, _filetype_, and _bank_. _Filereference_ is received in file list from bank. Returns _base64_ encoded file content.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: Authorizer
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');
// Configure API key authorization: X-Api-Key
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');

$api_instance = new Swagger\Client\Api\FilesApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$bank = "bank_example"; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `spankki`, `alandsbanken` or `SEB`.
$file_type = "file_type_example"; // string | File reference *id* from list files
$file_reference = "file_reference_example"; // string | File reference *id* from list files

try {
    $result = $api_instance->downloadFile($authorization, $bank, $file_type, $file_reference);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->downloadFile: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. |
 **file_type** | **string**| File reference *id* from list files |
 **file_reference** | **string**| File reference *id* from list files |

### Return type

[**\Swagger\Client\Model\DownloadFileResp**](../Model/DownloadFileResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **listFiles**
> \Swagger\Client\Model\ListFilesResp listFiles($authorization, $bank, $status, $file_type)

ListFiles

Asks _bank_ to list downloadable files matching filters. _Status_ can be e.g. _NEW_, _ALL_, or _DLD_. _Filetype_ is bank specific, see bank specification. _Bank_ is the name of the bank. Returns a list of _FileDescriptors_.  - **NOTE:** Certificate must be enrolled before files can be listed, downloaded or uploaded.  - **NOTE:** The uploaded files do not show up on the file listing.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: Authorizer
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');
// Configure API key authorization: X-Api-Key
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');

$api_instance = new Swagger\Client\Api\FilesApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$bank = "bank_example"; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `spankki`, `alandsbanken` or `SEB`.
$status = "status_example"; // string | Status of the *file*, e.g. `ALL`. `NEW`, `DLD`
$file_type = "file_type_example"; // string | *Bank* specific *FileType* identifies the file type to be listed

try {
    $result = $api_instance->listFiles($authorization, $bank, $status, $file_type);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->listFiles: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. |
 **status** | **string**| Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; | [optional]
 **file_type** | **string**| *Bank* specific *FileType* identifies the file type to be listed | [optional]

### Return type

[**\Swagger\Client\Model\ListFilesResp**](../Model/ListFilesResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **uploadFile**
> \Swagger\Client\Model\Response uploadFile($authorization, $upload_file_req)

UploadFile

Uploads file to bank. _Filecontents_ is a _Base64_ encoded string parameter. _Filetype_ is bank specific.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure API key authorization: Authorizer
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');
// Configure API key authorization: X-Api-Key
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// Swagger\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');

$api_instance = new Swagger\Client\Api\FilesApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$upload_file_req = new \Swagger\Client\Model\UploadFileReq(); // \Swagger\Client\Model\UploadFileReq | Files parameters

try {
    $result = $api_instance->uploadFile($authorization, $upload_file_req);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->uploadFile: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **upload_file_req** | [**\Swagger\Client\Model\UploadFileReq**](../Model/\Swagger\Client\Model\UploadFileReq.md)| Files parameters |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

