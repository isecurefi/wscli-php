# Swagger\Client\PgpTBDApi

All URIs are relative to *https://ws-api.test.isecure.fi/v2/*

Method | HTTP request | Description
------------- | ------------- | -------------
[**deleteKey**](PgpTBDApi.md#deleteKey) | **DELETE** /pgp | DeleteKey
[**listKeys**](PgpTBDApi.md#listKeys) | **GET** /pgp | ListKeys
[**uploadKey**](PgpTBDApi.md#uploadKey) | **PUT** /pgp | UploadKey


# **deleteKey**
> \Swagger\Client\Model\DeleteKeyResp deleteKey($authorization, $delete_key_req)

DeleteKey

Delete PGP key. API implementation is to-be-done (TBD).

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

$api_instance = new Swagger\Client\Api\PgpTBDApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$delete_key_req = new \Swagger\Client\Model\DeleteKeyReq(); // \Swagger\Client\Model\DeleteKeyReq | Pgp (TBD) parameters

try {
    $result = $api_instance->deleteKey($authorization, $delete_key_req);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpTBDApi->deleteKey: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **delete_key_req** | [**\Swagger\Client\Model\DeleteKeyReq**](../Model/\Swagger\Client\Model\DeleteKeyReq.md)| Pgp (TBD) parameters |

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

List PGP keys. API implementation is to-be-done (TBD).

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

$api_instance = new Swagger\Client\Api\PgpTBDApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header

try {
    $result = $api_instance->listKeys($authorization);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpTBDApi->listKeys: ', $e->getMessage(), PHP_EOL;
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

Upload PGP key. API implementation is to-be-done (TBD).

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

$api_instance = new Swagger\Client\Api\PgpTBDApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$upload_key_req = new \Swagger\Client\Model\UploadKeyReq(); // \Swagger\Client\Model\UploadKeyReq | ASCII armored PGP Key in `PgpKey` and key purpose, i.e. `Export` (exporting cert private key) or `Authorize` (upload content authorization verification) in `PgpKeyPurpose`.

try {
    $result = $api_instance->uploadKey($authorization, $upload_key_req);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PgpTBDApi->uploadKey: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **upload_key_req** | [**\Swagger\Client\Model\UploadKeyReq**](../Model/\Swagger\Client\Model\UploadKeyReq.md)| ASCII armored PGP Key in &#x60;PgpKey&#x60; and key purpose, i.e. &#x60;Export&#x60; (exporting cert private key) or &#x60;Authorize&#x60; (upload content authorization verification) in &#x60;PgpKeyPurpose&#x60;. |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

