# Swagger\Client\IntegratorApi

All URIs are relative to *https://ws-api.test.isecure.fi/v2/*

Method | HTTP request | Description
------------- | ------------- | -------------
[**listAccounts**](IntegratorApi.md#listAccounts) | **GET** /integrator/accounts | ListAccounts


# **listAccounts**
> \Swagger\Client\Model\ListAccountsResp listAccounts($authorization)

ListAccounts

List accounts registered under the integrator's API key. Account that created the API key is authorized to call this.

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

$api_instance = new Swagger\Client\Api\IntegratorApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header

try {
    $result = $api_instance->listAccounts($authorization);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegratorApi->listAccounts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |

### Return type

[**\Swagger\Client\Model\ListAccountsResp**](../Model/ListAccountsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

