# IsecureFi\WsApi\Client\AccountApi



All URIs are relative to https://ws-api.test.isecure.fi/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**initPasswordReset()**](AccountApi.md#initPasswordReset) | **GET** /account/{Email}/{Mode}/password | InitPasswordReset |
| [**initRegister()**](AccountApi.md#initRegister) | **GET** /account/{Email}/{Mode} | InitRegister |
| [**passwordReset()**](AccountApi.md#passwordReset) | **POST** /account/{Email}/{Mode}/password | PasswordReset |
| [**register()**](AccountApi.md#register) | **PUT** /account/{Email}/{Mode} | Register |
| [**verifyEmail()**](AccountApi.md#verifyEmail) | **POST** /account/{Email}/{Mode} | VerifyEmail |
| [**verifyPhone()**](AccountApi.md#verifyPhone) | **POST** /account/{Email}/{Mode}/{Phone} | VerifyPhone |


## `initPasswordReset()`

```php
initPasswordReset($email, $mode): \IsecureFi\WsApi\Client\Model\Response
```

InitPasswordReset

Start password reset by getting confirmation SMS code.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\AccountApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $apiInstance->initPasswordReset($email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->initPasswordReset: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |

### Return type

[**\IsecureFi\WsApi\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `initRegister()`

```php
initRegister($email, $mode): \IsecureFi\WsApi\Client\Model\InitRegisterResp
```

InitRegister

Before register (or login), client must fetch `challenge` from the API. Then on register (or login), the challenge must be passed along to the API (as response to the challenge). The challenge is always fresh for some period of time and the API validates it when passed with register (or login). The challenge has form of `base64-string|timestamp|uuid`. For example:  ```ezwXceQ63fV9oWTSJBAE2Zq1Cw5tBIJe+7+Rl8jrgbk=|1475429754114|4017bda8-0a15-4154-a8b7-88069b05cb4e```  **NOTE:** The call must contain the same email as used for registration itself.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\AccountApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $apiInstance->initRegister($email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->initRegister: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |

### Return type

[**\IsecureFi\WsApi\Client\Model\InitRegisterResp**](../Model/InitRegisterResp.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `passwordReset()`

```php
passwordReset($email, $mode, $passwordResetReq): \IsecureFi\WsApi\Client\Model\Response
```

PasswordReset

Set new _password_ for user with _Email_. Provide received SMS _Code_.  **NOTE:** the password must be encrypted, see Register for more details.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\AccountApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode
$passwordResetReq = new \IsecureFi\WsApi\Client\Model\PasswordResetReq(); // \IsecureFi\WsApi\Client\Model\PasswordResetReq | Account parameters

try {
    $result = $apiInstance->passwordReset($email, $mode, $passwordResetReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->passwordReset: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |
| **passwordResetReq** | [**\IsecureFi\WsApi\Client\Model\PasswordResetReq**](../Model/PasswordResetReq.md)| Account parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `register()`

```php
register($email, $mode, $registerReq): \IsecureFi\WsApi\Client\Model\RegisterResp
```

Register

You need to register both *admin* and *data* accounts with the same email address. Both accounts share the same data, but are used for different purposes. *Admin* account must be registered first, then *data* account.  *Admin* account is used to configure setup with **Certs** and **Pgp** operations, while the *data* account is used with **Files** operations only. Both accounts use **Account** and **Session** operations.  *Admin* account always requires SMS MFA during login, whilst *data* account does not. Generally, the *data* account is considered *read-noly* when no PGP keys are configured, since PGP Keys are used to verify file upload signatures and are thus required to successfully upload files with **Files** *UploadFile* operation.  Registrations are independent for both accounts, *admin* and *data* and both require phone number and email verifications.  `email` is the login username for both accounts and `mode` defines the selected &quot;mode&quot; for the login, i.e. *admin* or *data*.  Before registration client must fetch challenge from API (see Account InitRegister operation) and pass it back within the `ChResp` parameter.  The following parameters `name`, `phone`, and `company` are required and must be valid (`phone`, `email`) as they need to be confirmed before registration becomes successful and login possible.  Client must RSA encrypt (OAEP padding) the _password_ and the challenge _timestamp_ as string in the form `password||timestamp`, base64 encode it and provide the resulting string as `Encrypted` parameter. The RSA encryption can be done e.g. for illustration purposes within command line with openssl rsautl: ``` echo -n Toddler_..123456789012345\\|\\|1475175151231 |  openssl rsautl -oaep -encrypt -pubin -inkey test.pem |  base64  ```  The **test** API's RSA public key is as follows:  ``` % cat test.pem -----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkuSaoSZztGAIGDTY7Rff psBHJJT1k207UodOJbYFhHAq0lWJnvMPLl5Q1DUUZdTGtTdL8Dsaj/Bo2+gSykMM R5QiKewvQsLfvqjwOO8JDItnhJl0lUqcPpdQV4M/Ai3YNRjNcVy4a+pichqtSAWl 9S1HV01MNeouk8PEr/zoUasmgfO3mz6N6XTUtF/tIi8K2kBOsLAtqltihFSd/zT8 ifYZE9cZTJ09lUs7kMz1wxFIsiegaE1jUYV+VSLu3PJ97oKhQpqop8EnkBAoBl6r mdmFryBQIdakPIdd4rO5Yg+to10n4u7Wij9ePIwWMfbqY4QoW5nXqMgFJQkIt4TG eQIDAQAB -----END PUBLIC KEY----- ```  The **production** API's RSA public key is as follows:  ``` % cat prod.pem -----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA7wx4l7P3eLsaEyK7ZRME g5urEHwaEoY9LjkYcpMw9gmPIi3RoGjQX7HzPad2D7ES2yIGdmyxjN8R2LyFa8ke EE+VY3ISYzP2cOjd/zDkX01yjDXQLRxntXbtqIypGQAzmZbCyIB226ZKEE+ldh6M YyM41YWYikfocYssFEjY7fpPGeUg4FOmHmyWIZeMkXYovskoi1jZ1Ay1qn95XlpA /Ptru2efro4T1xksv4WBBrj8bMNwdDpf4oyzH2PKYkn3/KlNTBCHlAmzP0jd4pIa N0tAf2m8TcNq7kuBzyfs8AcCUj870p8SEiko0PMx6K+zVsTVWsxfUX+/+kmapmp/ AwIDAQAB -----END PUBLIC KEY----- ```    **NOTE:** Password must be at least 20 characters long, have lower and upper case letters, numbers, and special characters.  **NOTE:** Phone number must be provided with country code, e.g. `+358404982201`.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\AccountApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode
$registerReq = new \IsecureFi\WsApi\Client\Model\RegisterReq(); // \IsecureFi\WsApi\Client\Model\RegisterReq | Account parameters

try {
    $result = $apiInstance->register($email, $mode, $registerReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->register: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |
| **registerReq** | [**\IsecureFi\WsApi\Client\Model\RegisterReq**](../Model/RegisterReq.md)| Account parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\RegisterResp**](../Model/RegisterResp.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `verifyEmail()`

```php
verifyEmail($email, $mode, $verifyEmailReq): \IsecureFi\WsApi\Client\Model\Response
```

VerifyEmail

Provide _Code_ received to _Email_ address for email address verification. Provide also _AccessToken_ received during login.  **NOTE**: _Phone_ and _EMail_ verification can be bypassed per API key on request to _dan.forsberg@isecure.fi_. E.g. if integrator verifies EMail and Phone beforehand.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\AccountApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode
$verifyEmailReq = new \IsecureFi\WsApi\Client\Model\VerifyEmailReq(); // \IsecureFi\WsApi\Client\Model\VerifyEmailReq | Account parameters

try {
    $result = $apiInstance->verifyEmail($email, $mode, $verifyEmailReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->verifyEmail: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |
| **verifyEmailReq** | [**\IsecureFi\WsApi\Client\Model\VerifyEmailReq**](../Model/VerifyEmailReq.md)| Account parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `verifyPhone()`

```php
verifyPhone($email, $mode, $phone, $verifyPhoneReq): \IsecureFi\WsApi\Client\Model\Response
```

VerifyPhone

Confirm phone number for _Email_ _Mode_ user, with _Code_ received via SMS.  **NOTE**: _Phone_ and _EMail_ verification can be bypassed per API key on request to _dan.forsberg@isecure.fi_. E.g. if integrator verifies EMail and Phone beforehand.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\AccountApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode
$phone = 'phone_example'; // string | Phone number with country code, e.g. `+358401234567`
$verifyPhoneReq = new \IsecureFi\WsApi\Client\Model\VerifyPhoneReq(); // \IsecureFi\WsApi\Client\Model\VerifyPhoneReq | Account parameters

try {
    $result = $apiInstance->verifyPhone($email, $mode, $phone, $verifyPhoneReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->verifyPhone: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |
| **phone** | **string**| Phone number with country code, e.g. &#x60;+358401234567&#x60; | |
| **verifyPhoneReq** | [**\IsecureFi\WsApi\Client\Model\VerifyPhoneReq**](../Model/VerifyPhoneReq.md)| Account parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
