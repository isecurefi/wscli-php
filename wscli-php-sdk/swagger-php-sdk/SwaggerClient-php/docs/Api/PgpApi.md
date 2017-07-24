# Swagger\Client\PgpApi

All URIs are relative to *https://ws-api.test.isecure.fi/v2/*

Method | HTTP request | Description
------------- | ------------- | -------------
[**deleteKey**](PgpApi.md#deleteKey) | **DELETE** /pgp | DeleteKey
[**listKeys**](PgpApi.md#listKeys) | **GET** /pgp | ListKeys
[**uploadKey**](PgpApi.md#uploadKey) | **PUT** /pgp | UploadKey


# **deleteKey**
> \Swagger\Client\Model\DeleteKeyResp deleteKey($authorization, $delete_key_req)

DeleteKey

Delete PGP key

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

$api_instance = new Swagger\Client\Api\PgpApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$delete_key_req = new \Swagger\Client\Model\DeleteKeyReq(); // \Swagger\Client\Model\DeleteKeyReq | Pgp parameters

try {
    $result = $api_instance->deleteKey($authorization, $delete_key_req);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpApi->deleteKey: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **delete_key_req** | [**\Swagger\Client\Model\DeleteKeyReq**](../Model/\Swagger\Client\Model\DeleteKeyReq.md)| Pgp parameters |

### Return type

[**\Swagger\Client\Model\DeleteKeyResp**](../Model/DeleteKeyResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **listKeys**
> \Swagger\Client\Model\ListKeysResp listKeys($authorization)

ListKeys

List PGP keys

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

$api_instance = new Swagger\Client\Api\PgpApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header

try {
    $result = $api_instance->listKeys($authorization);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpApi->listKeys: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |

### Return type

[**\Swagger\Client\Model\ListKeysResp**](../Model/ListKeysResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **uploadKey**
> \Swagger\Client\Model\Response uploadKey($authorization, $upload_key_req)

UploadKey

Upload PGP key

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

$api_instance = new Swagger\Client\Api\PgpApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$upload_key_req = new \Swagger\Client\Model\UploadKeyReq(); // \Swagger\Client\Model\UploadKeyReq | Pgp parameters

try {
    $result = $api_instance->uploadKey($authorization, $upload_key_req);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpApi->uploadKey: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **upload_key_req** | [**\Swagger\Client\Model\UploadKeyReq**](../Model/\Swagger\Client\Model\UploadKeyReq.md)| Pgp parameters |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

