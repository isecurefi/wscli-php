# IsecureFi\WsApi\Client\CertsApi



All URIs are relative to https://ws-api.test.isecure.fi/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**configCerts()**](CertsApi.md#configCerts) | **POST** /certs/ | ConfigCerts |
| [**enrollCert()**](CertsApi.md#enrollCert) | **POST** /certs/{Bank} | EnrollCert |
| [**exportCert()**](CertsApi.md#exportCert) | **GET** /certs/{Bank} | ExportCert |
| [**importCert()**](CertsApi.md#importCert) | **PUT** /certs/{Bank} | ImportCert |
| [**listCerts()**](CertsApi.md#listCerts) | **GET** /certs/ | ListCerts |
| [**shareCerts()**](CertsApi.md#shareCerts) | **PUT** /certs/shared/{ExtEmail} | ShareCerts |
| [**unshareCerts()**](CertsApi.md#unshareCerts) | **DELETE** /certs/shared/{ExtEmail} | UnshareCerts |


## `configCerts()`

```php
configCerts($authorization, $xApiKey, $configCertsReq): \IsecureFi\WsApi\Client\Model\Response
```

ConfigCerts

Configure certificate usage parameters. Currently, only settable (permanent) parameter is `export`. Set it to `disabled` for disallowing private key export.  **NOTE**: When export is disabled it is permanent, it can not be re-enabled through the API (safety feature).

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


$apiInstance = new IsecureFi\WsApi\Client\Api\CertsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$configCertsReq = new \IsecureFi\WsApi\Client\Model\ConfigCertsReq(); // \IsecureFi\WsApi\Client\Model\ConfigCertsReq | Certs handling settings

try {
    $result = $apiInstance->configCerts($authorization, $xApiKey, $configCertsReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->configCerts: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **configCertsReq** | [**\IsecureFi\WsApi\Client\Model\ConfigCertsReq**](../Model/ConfigCertsReq.md)| Certs handling settings | |

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

## `enrollCert()`

```php
enrollCert($authorization, $xApiKey, $bank, $enrollCertReq): \IsecureFi\WsApi\Client\Model\Response
```

EnrollCert

Provide WS-Channel user id, _WsUserId_, _Company_, and PIN _Code_ for _Bank_ certificate enrollment. _Company_ must match with the contract with the bank and is part of enrollment process. Note that certificate private key is securely generated and stored encrypted with AWS KMS encrypted authentication on API side. Certificates are automatically renewed when needed.  **NOTE:** For OP bank, ensure that you set the PIN code blocks 1 and 2 in correct order. If not initially in correct order, bank will lock the registration and you need to call them for unlock.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\CertsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$bank = 'bank_example'; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, or `alandsbanken`.
$enrollCertReq = new \IsecureFi\WsApi\Client\Model\EnrollCertReq(); // \IsecureFi\WsApi\Client\Model\EnrollCertReq | Certs parameters

try {
    $result = $apiInstance->enrollCert($authorization, $xApiKey, $bank, $enrollCertReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->enrollCert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. | |
| **enrollCertReq** | [**\IsecureFi\WsApi\Client\Model\EnrollCertReq**](../Model/EnrollCertReq.md)| Certs parameters | |

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

## `exportCert()`

```php
exportCert($authorization, $xApiKey, $bank, $pgpKeyId): \IsecureFi\WsApi\Client\Model\ExportCertResp
```

ExportCert

Download bank certificate and private key encrypted with stored PGP key.  **NOTE**: The previously uploaded `PgpKeyId` must have purpose type `export`. I.e. purpose type `authorize` PGP keys can not be used for exporting.  **NOTE**: If `export` has been set to `disabled` (see ConfigCerts), then exporting private keys is not possible through API.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\CertsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$bank = 'bank_example'; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, or `alandsbanken`.
$pgpKeyId = 'pgpKeyId_example'; // string | Short version of a PGP Key id idenfiying the exported Private Key, e.g. `3A3A59B2`

try {
    $result = $apiInstance->exportCert($authorization, $xApiKey, $bank, $pgpKeyId);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->exportCert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. | |
| **pgpKeyId** | **string**| Short version of a PGP Key id idenfiying the exported Private Key, e.g. &#x60;3A3A59B2&#x60; | |

### Return type

[**\IsecureFi\WsApi\Client\Model\ExportCertResp**](../Model/ExportCertResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `importCert()`

```php
importCert($authorization, $xApiKey, $bank, $importCertReq): \IsecureFi\WsApi\Client\Model\Response
```

ImportCert

Provide _WsUserId_, _Company_, _PrivateKey_, and _Certificate_ for importing existing WS Channel certificate and private key. _Company_ must match with the contract with the bank. Certificate(s) and private key(s) must be PEM formatted.  **NOTE:** _EncCcertificate_ and _EncPrivatekey_ are for DanskeBank only.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\CertsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$bank = 'bank_example'; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, or `alandsbanken`.
$importCertReq = new \IsecureFi\WsApi\Client\Model\ImportCertReq(); // \IsecureFi\WsApi\Client\Model\ImportCertReq | Certs parameters

try {
    $result = $apiInstance->importCert($authorization, $xApiKey, $bank, $importCertReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->importCert: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. | |
| **importCertReq** | [**\IsecureFi\WsApi\Client\Model\ImportCertReq**](../Model/ImportCertReq.md)| Certs parameters | |

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

## `listCerts()`

```php
listCerts($authorization, $xApiKey): \IsecureFi\WsApi\Client\Model\ListCertsResp
```

ListCerts

List certs from all banks

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


$apiInstance = new IsecureFi\WsApi\Client\Api\CertsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header

try {
    $result = $apiInstance->listCerts($authorization, $xApiKey);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->listCerts: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |

### Return type

[**\IsecureFi\WsApi\Client\Model\ListCertsResp**](../Model/ListCertsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `shareCerts()`

```php
shareCerts($authorization, $xApiKey, $extEmail): \IsecureFi\WsApi\Client\Model\ShareCertsResp
```

ShareCerts

Share certs with an existing account _ExtEmail_ under the same API Key.  If the _ExtEmail_ account does not have the specific bank certificate (key pair), then certs (key pair) from this account will be used (shared), if any. Both accounts must be registered and using the same integrator API Key.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\CertsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$extEmail = 'extEmail_example'; // string | Share certs with _ExtEMail_ account.

try {
    $result = $apiInstance->shareCerts($authorization, $xApiKey, $extEmail);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->shareCerts: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **extEmail** | **string**| Share certs with _ExtEMail_ account. | |

### Return type

[**\IsecureFi\WsApi\Client\Model\ShareCertsResp**](../Model/ShareCertsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `unshareCerts()`

```php
unshareCerts($authorization, $xApiKey, $extEmail): \IsecureFi\WsApi\Client\Model\UnshareCertsResp
```

UnshareCerts

Unshare certs with an existing _ExtEmail_ account.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\CertsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$extEmail = 'extEmail_example'; // string | Unshare certs with _ExtEMail_ account.

try {
    $result = $apiInstance->unshareCerts($authorization, $xApiKey, $extEmail);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->unshareCerts: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **extEmail** | **string**| Unshare certs with _ExtEMail_ account. | |

### Return type

[**\IsecureFi\WsApi\Client\Model\UnshareCertsResp**](../Model/UnshareCertsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
