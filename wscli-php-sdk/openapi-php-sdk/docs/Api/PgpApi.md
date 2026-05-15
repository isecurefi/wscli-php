# IsecureFi\WsApi\Client\PgpApi



All URIs are relative to https://ws-api.test.isecure.fi/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**deleteKey()**](PgpApi.md#deleteKey) | **DELETE** /pgp | DeleteKey |
| [**listKeys()**](PgpApi.md#listKeys) | **GET** /pgp | ListKeys |
| [**uploadKey()**](PgpApi.md#uploadKey) | **PUT** /pgp | UploadKey |


## `deleteKey()`

```php
deleteKey($authorization, $xApiKey, $deleteKeyReq): \IsecureFi\WsApi\Client\Model\DeleteKeyResp
```

DeleteKey

Delete PGP key.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\PgpApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$deleteKeyReq = new \IsecureFi\WsApi\Client\Model\DeleteKeyReq(); // \IsecureFi\WsApi\Client\Model\DeleteKeyReq | Pgp parameters

try {
    $result = $apiInstance->deleteKey($authorization, $xApiKey, $deleteKeyReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpApi->deleteKey: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **deleteKeyReq** | [**\IsecureFi\WsApi\Client\Model\DeleteKeyReq**](../Model/DeleteKeyReq.md)| Pgp parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\DeleteKeyResp**](../Model/DeleteKeyResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listKeys()`

```php
listKeys($authorization, $xApiKey): \IsecureFi\WsApi\Client\Model\ListKeysResp
```

ListKeys

List PGP keys.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\PgpApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header

try {
    $result = $apiInstance->listKeys($authorization, $xApiKey);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpApi->listKeys: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |

### Return type

[**\IsecureFi\WsApi\Client\Model\ListKeysResp**](../Model/ListKeysResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `uploadKey()`

```php
uploadKey($authorization, $xApiKey, $uploadKeyReq): \IsecureFi\WsApi\Client\Model\Response
```

UploadKey

Upload PGP key.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\PgpApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$uploadKeyReq = new \IsecureFi\WsApi\Client\Model\UploadKeyReq(); // \IsecureFi\WsApi\Client\Model\UploadKeyReq | ASCII armored PGP Key in `PgpKey` and key purpose, i.e. `export` (exporting cert private key) or `authorize` (upload content authorization verification) in `PgpKeyPurpose`.  **NOTE**: The same PGP key can not be used for both `export` and `authorize` purpose at the same time.

try {
    $result = $apiInstance->uploadKey($authorization, $xApiKey, $uploadKeyReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpApi->uploadKey: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **uploadKeyReq** | [**\IsecureFi\WsApi\Client\Model\UploadKeyReq**](../Model/UploadKeyReq.md)| ASCII armored PGP Key in &#x60;PgpKey&#x60; and key purpose, i.e. &#x60;export&#x60; (exporting cert private key) or &#x60;authorize&#x60; (upload content authorization verification) in &#x60;PgpKeyPurpose&#x60;.  **NOTE**: The same PGP key can not be used for both &#x60;export&#x60; and &#x60;authorize&#x60; purpose at the same time. | |

### Return type

[**\IsecureFi\WsApi\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
