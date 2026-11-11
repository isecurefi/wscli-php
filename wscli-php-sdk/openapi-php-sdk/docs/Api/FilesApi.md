# IsecureFi\WsApi\Client\FilesApi



All URIs are relative to https://ws-api.test.isecure.fi/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**deleteFile()**](FilesApi.md#deleteFile) | **DELETE** /files/{Bank}/{FileType}/{FileReference} | DeleteFile |
| [**downloadFile()**](FilesApi.md#downloadFile) | **GET** /files/{Bank}/{FileType}/{FileReference} | DownloadFile |
| [**listFiles()**](FilesApi.md#listFiles) | **GET** /files/{Bank} | ListFiles |
| [**uploadFile()**](FilesApi.md#uploadFile) | **PUT** /files/{Bank} | UploadFile |


## `deleteFile()`

```php
deleteFile($authorization, $xApiKey, $bank, $fileType, $fileReference): \IsecureFi\WsApi\Client\Model\Response
```

DeleteFile

Deletes file on bank side. File is identified with _filereference_.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: Authorizer
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

// Configure API key authorization: X-Api-Key
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');


$apiInstance = new IsecureFi\WsApi\Client\Api\FilesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$bank = 'bank_example'; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, `alandsbanken` or `SEB`.
$fileType = 'fileType_example'; // string | File reference *id* from list files
$fileReference = 'fileReference_example'; // string | File reference *id* from list files

try {
    $result = $apiInstance->deleteFile($authorization, $xApiKey, $bank, $fileType, $fileReference);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->deleteFile: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. | |
| **fileType** | **string**| File reference *id* from list files | |
| **fileReference** | **string**| File reference *id* from list files | |

### Return type

[**\IsecureFi\WsApi\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `downloadFile()`

```php
downloadFile($authorization, $xApiKey, $bank, $fileType, $fileReference): \IsecureFi\WsApi\Client\Model\DownloadFileResp
```

DownloadFile

Downloads file identified with _filereference_, _filetype_, and _bank_. _Filereference_ is received in file list from bank. Returns _base64_ encoded file content.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: Authorizer
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

// Configure API key authorization: X-Api-Key
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');


$apiInstance = new IsecureFi\WsApi\Client\Api\FilesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$bank = 'bank_example'; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, `alandsbanken` or `SEB`.
$fileType = 'fileType_example'; // string | File type from list files
$fileReference = 'fileReference_example'; // string | File reference identifier from list files

try {
    $result = $apiInstance->downloadFile($authorization, $xApiKey, $bank, $fileType, $fileReference);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->downloadFile: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. | |
| **fileType** | **string**| File type from list files | |
| **fileReference** | **string**| File reference identifier from list files | |

### Return type

[**\IsecureFi\WsApi\Client\Model\DownloadFileResp**](../Model/DownloadFileResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listFiles()`

```php
listFiles($authorization, $xApiKey, $bank, $status, $fileType, $contentType, $startDate, $endDate): \IsecureFi\WsApi\Client\Model\ListFilesResp
```

ListFiles

Asks _bank_ to list downloadable files matching filters. _Status_ can be e.g. _NEW_, _ALL_, or _DLD_. _Filetype_ is bank specific (ALL is not accepted), see bank specification. _Bank_ is the name of the bank. Returns a list of _FileDescriptors_.  **NOTE:** Certificate must be enrolled before files can be listed, downloaded or uploaded.  **NOTE:** The uploaded files do not show up on the file listing.  ``` % export SESSION=~/.wscli/settings.yaml % wscli session login -c $SESSION % % curl -H Authorization:`yq -r .settings.idtoken $SESSION` \\         -H x-api-key:`yq -r .settings.apikey $SESSION` \\         https://ws-api.isecure.fi/v2/files/danskebank ```

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: Authorizer
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

// Configure API key authorization: X-Api-Key
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');


$apiInstance = new IsecureFi\WsApi\Client\Api\FilesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$bank = 'bank_example'; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, `alandsbanken` or `SEB`.
$status = 'status_example'; // string | Status of the *file*, e.g. `ALL`. `NEW`, `DLD`
$fileType = 'fileType_example'; // string | *Bank* specific *FileType* identifies the file type to be listed

try {
    $result = $apiInstance->listFiles($authorization, $xApiKey, $bank, $status, $fileType);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->listFiles: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. | |
| **status** | **string**| Status of the *file*, e.g. &#x60;ALL&#x60;. &#x60;NEW&#x60;, &#x60;DLD&#x60; | [optional] |
| **fileType** | **string**| *Bank* specific *FileType* identifies the file type to be listed | [optional] |

### Return type

[**\IsecureFi\WsApi\Client\Model\ListFilesResp**](../Model/ListFilesResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `uploadFile()`

```php
uploadFile($authorization, $xApiKey, $bank, $uploadFileReq): \IsecureFi\WsApi\Client\Model\UploadFileResp
```

UploadFile

Uploads file to bank if PGP signature(s) are valid. _FileContents_ is a string of _Base64_ encoded file contents. _FileType_ is bank specific. _Signature_ is detached PGP signature or concatenation of PGP detached signatures in ASCII armor format. PGP signatures are used for authorizing file uploads. Currently one valid PGP authorize registered key signature is enough. _FileName_ is upload filename.  **NOTE:** The uploaded files do not show up on the file listing from bank.  ``` % export SESSION=~/.wscli/settings.yaml % wscli session login -c $SESSION % export APIKEY=`yq -r .settings.apikey $SESSION` % export IDTOKEN=`yq -r .settings.idtoken $SESSION` % % curl -X PUT -H Content-Type:application/json \\         -H Authorization:$IDTOKEN \\         -H x-api-key:$APIKEY \\         -d @request-example.json \\         https://ws-api.isecure.fi/v2/files/danskebank ```

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure API key authorization: Authorizer
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('Authorization', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('Authorization', 'Bearer');

// Configure API key authorization: X-Api-Key
$config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKey('x-api-key', 'YOUR_API_KEY');
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// $config = IsecureFi\WsApi\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('x-api-key', 'Bearer');


$apiInstance = new IsecureFi\WsApi\Client\Api\FilesApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$bank = 'bank_example'; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, `alandsbanken` or `SEB`.
$uploadFileReq = new \IsecureFi\WsApi\Client\Model\UploadFileReq(); // \IsecureFi\WsApi\Client\Model\UploadFileReq | Files parameters

try {
    $result = $apiInstance->uploadFile($authorization, $xApiKey, $bank, $uploadFileReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FilesApi->uploadFile: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, &#x60;alandsbanken&#x60; or &#x60;SEB&#x60;. | |
| **uploadFileReq** | [**\IsecureFi\WsApi\Client\Model\UploadFileReq**](../Model/UploadFileReq.md)| Files parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\UploadFileResp**](../Model/UploadFileResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

Date-filtered listing (optional named arguments preserve existing positional calls):

```php
$apiInstance->listFiles($authorization, $xApiKey, $bank, 'ALL', $fileType,
    startDate: '2026-11-08', endDate: '2026-11-10');
```

`startDate` and `endDate` are inclusive `YYYY-MM-DD` dates.
