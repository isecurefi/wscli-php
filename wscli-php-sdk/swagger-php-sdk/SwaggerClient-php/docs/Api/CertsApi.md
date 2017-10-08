# Swagger\Client\CertsApi

All URIs are relative to *https://ws-api.test.isecure.fi/v2*

Method | HTTP request | Description
------------- | ------------- | -------------
[**configCerts**](CertsApi.md#configCerts) | **POST** /certs/ | ConfigCerts
[**enrollCert**](CertsApi.md#enrollCert) | **POST** /certs/{Bank} | EnrollCert
[**exportCert**](CertsApi.md#exportCert) | **GET** /certs/{Bank} | ExportCert
[**importCert**](CertsApi.md#importCert) | **PUT** /certs/{Bank} | ImportCert
[**listCerts**](CertsApi.md#listCerts) | **GET** /certs/ | ListCerts
[**shareCerts**](CertsApi.md#shareCerts) | **PUT** /certs/shared/{ExtEmail} | ShareCerts
[**unshareCerts**](CertsApi.md#unshareCerts) | **DELETE** /certs/shared/{ExtEmail} | UnshareCerts


# **configCerts**
> \Swagger\Client\Model\Response configCerts($authorization, $config_certs_req)

ConfigCerts

Configure certificate usage parameters. Currently, only settable (permanent) parameter is `export`. Set it to `disabled` for disallowing private key export.  **NOTE**: When export is disabled it is permanent, it can not be re-enabled through the API (safety feature).

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

$api_instance = new Swagger\Client\Api\CertsApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$config_certs_req = new \Swagger\Client\Model\ConfigCertsReq(); // \Swagger\Client\Model\ConfigCertsReq | Certs handling settings

try {
    $result = $api_instance->configCerts($authorization, $config_certs_req);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->configCerts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **config_certs_req** | [**\Swagger\Client\Model\ConfigCertsReq**](../Model/\Swagger\Client\Model\ConfigCertsReq.md)| Certs handling settings |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **enrollCert**
> \Swagger\Client\Model\Response enrollCert($authorization, $enroll_cert_req, $bank)

EnrollCert

Provide WS-Channel user id, _WsUserId_, _Company_, and PIN _Code_ for _Bank_ certificate enrollment. _Company_ must match with the contract with the bank and is part of enrollment process. Note that certificate private key is securely generated and stored encrypted on service side and never leaves from there. Certificates are automatically renewed when needed.

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

$api_instance = new Swagger\Client\Api\CertsApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$enroll_cert_req = new \Swagger\Client\Model\EnrollCertReq(); // \Swagger\Client\Model\EnrollCertReq | Certs parameters
$bank = "bank_example"; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, or `alandsbanken`.

try {
    $result = $api_instance->enrollCert($authorization, $enroll_cert_req, $bank);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->enrollCert: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **enroll_cert_req** | [**\Swagger\Client\Model\EnrollCertReq**](../Model/\Swagger\Client\Model\EnrollCertReq.md)| Certs parameters |
 **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **exportCert**
> \Swagger\Client\Model\ExportCertResp exportCert($authorization, $bank, $pgp_key_id)

ExportCert

Download bank certificate and private key encrypted with stored PGP key.  **NOTE**: The previously uploaded `PgpKeyId` must have purpose type `export`. I.e. purpose type `authorize` PGP keys can not be used for exporting.  **NOTE**: If `export` has been set to `disabled` (see ConfigCerts), then exporting private keys is not possible through API.

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

$api_instance = new Swagger\Client\Api\CertsApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$bank = "bank_example"; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, or `alandsbanken`.
$pgp_key_id = "pgp_key_id_example"; // string | Short version of a PGP Key id idenfiying the exported Private Key, e.g. `3A3A59B2`

try {
    $result = $api_instance->exportCert($authorization, $bank, $pgp_key_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->exportCert: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. |
 **pgp_key_id** | **string**| Short version of a PGP Key id idenfiying the exported Private Key, e.g. &#x60;3A3A59B2&#x60; |

### Return type

[**\Swagger\Client\Model\ExportCertResp**](../Model/ExportCertResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **importCert**
> \Swagger\Client\Model\Response importCert($authorization, $import_cert_req, $bank)

ImportCert

Provide _WsUserId_, _Company_, _PrivateKey_, and _Certificate_ for importing existing WS Channel certificate and private key. _Company_ must match with the contract with the bank. Certificate(s) and private key(s) must be PEM formatted.  - **NOTE:** _EncCcertificate_ and _EncPrivatekey_ are for DanskeBank only.

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

$api_instance = new Swagger\Client\Api\CertsApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$import_cert_req = new \Swagger\Client\Model\ImportCertReq(); // \Swagger\Client\Model\ImportCertReq | Certs parameters
$bank = "bank_example"; // string | *Bank* used for this operation, can have values of `nordea`, `osuuspankki`, `danskebank`, `aktia`, `sp`, `shb`, `pop`, `spankki`, or `alandsbanken`.

try {
    $result = $api_instance->importCert($authorization, $import_cert_req, $bank);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->importCert: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **import_cert_req** | [**\Swagger\Client\Model\ImportCertReq**](../Model/\Swagger\Client\Model\ImportCertReq.md)| Certs parameters |
 **bank** | **string**| *Bank* used for this operation, can have values of &#x60;nordea&#x60;, &#x60;osuuspankki&#x60;, &#x60;danskebank&#x60;, &#x60;aktia&#x60;, &#x60;sp&#x60;, &#x60;shb&#x60;, &#x60;pop&#x60;, &#x60;spankki&#x60;, or &#x60;alandsbanken&#x60;. |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **listCerts**
> \Swagger\Client\Model\ListCertsResp listCerts($authorization)

ListCerts

List certs from all banks

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

$api_instance = new Swagger\Client\Api\CertsApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header

try {
    $result = $api_instance->listCerts($authorization);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->listCerts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |

### Return type

[**\Swagger\Client\Model\ListCertsResp**](../Model/ListCertsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **shareCerts**
> \Swagger\Client\Model\ShareCertsResp shareCerts($authorization, $ext_email)

ShareCerts

Share certs with an existing account _ExtEmail_ under the same API Key.  If the _ExtEmail_ account does not have the specific bank certificate (key pair), then certs (key pair) from this account will be used (shared), if any. Both accounts must be registered and using the same integrator API Key.

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

$api_instance = new Swagger\Client\Api\CertsApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$ext_email = "ext_email_example"; // string | Share certs with _ExtEMail_ account.

try {
    $result = $api_instance->shareCerts($authorization, $ext_email);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->shareCerts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **ext_email** | **string**| Share certs with _ExtEMail_ account. |

### Return type

[**\Swagger\Client\Model\ShareCertsResp**](../Model/ShareCertsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **unshareCerts**
> \Swagger\Client\Model\UnshareCertsResp unshareCerts($authorization, $ext_email)

UnshareCerts

Unshare certs with an existing _ExtEmail_ account.

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

$api_instance = new Swagger\Client\Api\CertsApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$ext_email = "ext_email_example"; // string | Unshare certs with _ExtEMail_ account.

try {
    $result = $api_instance->unshareCerts($authorization, $ext_email);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CertsApi->unshareCerts: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **ext_email** | **string**| Unshare certs with _ExtEMail_ account. |

### Return type

[**\Swagger\Client\Model\UnshareCertsResp**](../Model/UnshareCertsResp.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

