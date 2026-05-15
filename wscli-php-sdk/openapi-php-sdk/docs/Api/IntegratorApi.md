# IsecureFi\WsApi\Client\IntegratorApi



All URIs are relative to https://ws-api.test.isecure.fi/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**listAccounts()**](IntegratorApi.md#listAccounts) | **GET** /integrator/accounts | ListAccounts |


## `listAccounts()`

```php
listAccounts($authorization, $xApiKey): \IsecureFi\WsApi\Client\Model\ListAccountsResp
```

ListAccounts

List accounts registered under the integrator's API key. Account that created the API key is authorized to call this.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\IntegratorApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header

try {
    $result = $apiInstance->listAccounts($authorization, $xApiKey);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling IntegratorApi->listAccounts: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |

### Return type

[**\IsecureFi\WsApi\Client\Model\ListAccountsResp**](../Model/ListAccountsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
