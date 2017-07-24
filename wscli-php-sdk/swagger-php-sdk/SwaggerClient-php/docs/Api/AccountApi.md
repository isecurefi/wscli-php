# Swagger\Client\AccountApi

All URIs are relative to *https://ws-api.test.isecure.fi/v2/*

Method | HTTP request | Description
------------- | ------------- | -------------
[**initPasswordReset**](AccountApi.md#initPasswordReset) | **GET** /account/{Email}/{Mode}/password | InitPasswordReset
[**initRegister**](AccountApi.md#initRegister) | **GET** /account/{Email}/{Mode} | InitRegister
[**passwordReset**](AccountApi.md#passwordReset) | **POST** /account/{Email}/{Mode}/password | PasswordReset
[**register**](AccountApi.md#register) | **PUT** /account/{Email}/{Mode} | Register
[**verifyEmail**](AccountApi.md#verifyEmail) | **POST** /account/{Email}/{Mode} | VerifyEmail
[**verifyPhone**](AccountApi.md#verifyPhone) | **POST** /account/{Email}/{Mode}/{Phone} | VerifyPhone


# **initPasswordReset**
> \Swagger\Client\Model\Response initPasswordReset($email, $mode)

InitPasswordReset

Start password reset by getting confirmation SMS code.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\AccountApi();
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->initPasswordReset($email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->initPasswordReset: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **initRegister**
> \Swagger\Client\Model\InitRegisterResp initRegister($email, $mode)

InitRegister

Before register (or login), client must fetch `challenge` from the server. Then on register (or login), the challenge must be passed along to the server (as response to the challenge). The challenge is always fresh for some period of time and the server validates it when passed with register (or login). The challenge has form of `base64-string|timestamp|uuid`. For example:  ```ezwXceQ63fV9oWTSJBAE2Zq1Cw5tBIJe+7+Rl8jrgbk=|1475429754114|4017bda8-0a15-4154-a8b7-88069b05cb4e```  **NOTE:** The call must contain the same email as used for registration itself.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\AccountApi();
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->initRegister($email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->initRegister: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\InitRegisterResp**](../Model/InitRegisterResp.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **passwordReset**
> \Swagger\Client\Model\Response passwordReset($password_reset_req)

PasswordReset

Set new _password_ for user with _email_. Provide received SMS _code_.  **NOTE:** the password must be encrypted, see Register for more details.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\AccountApi();
$password_reset_req = new \Swagger\Client\Model\PasswordResetReq(); // \Swagger\Client\Model\PasswordResetReq | Account parameters

try {
    $result = $api_instance->passwordReset($password_reset_req);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->passwordReset: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **password_reset_req** | [**\Swagger\Client\Model\PasswordResetReq**](../Model/\Swagger\Client\Model\PasswordResetReq.md)| Account parameters |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **register**
> \Swagger\Client\Model\RegisterResp register($register_req, $email, $mode)

Register

You need to register both *admin* and *data* accounts with the same email address. Both accounts share the same data, but are used for different purposes. *Admin* account must be registered first, then *data* account.  *Admin* account is used to configure setup with **Certs** and **Pgp** operations, while the *data* account is used with **Files** operations only. Both accounts use **Account** and **Session** operations.  *Admin* account always requires SMS MFA during login, whilst *data* account does not. Generally, the *data* account is considered *read-noly* when no PGP keys are configured, since PGP Keys are used to verify file upload signatures and are thus required to successfully upload files with **Files** *UploadFile* operation.  Registrations are independent for both accounts, *admin* and *data* and both require phone number and email verifications.  `email` is the login username for both accounts and `mode` defines the selected &quot;mode&quot; for the login, i.e. *admin* or *data*.  Before registration client must fetch challenge from server (see Account InitRegister operation) and pass it back within the `ChResp` parameter.  The following parameters `name`, `phone`, and `company` are required and must be valid (`phone`, `email`) as they need to be confirmed before registration becomes successful and login possible.  Client must RSA encrypt (OAEP padding) the _password_ and the challenge _timestamp_ as string in the form `password||timestamp`, base64 encode it and provide the resulting string as `Encrypted` parameter. The RSA encryption can be done e.g. for illustration purposes within command line with openssl rsautl: ``` echo -n 'Toddler_..123456789012345||1475175151231' |  openssl rsautl -oaep -encrypt -pubin -inkey server_rsa_public_key.pem |  base64  ```  The server's RSA public key is as follows:  ``` -----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkuSaoSZztGAIGDTY7Rff psBHJJT1k207UodOJbYFhHAq0lWJnvMPLl5Q1DUUZdTGtTdL8Dsaj/Bo2+gSykMM R5QiKewvQsLfvqjwOO8JDItnhJl0lUqcPpdQV4M/Ai3YNRjNcVy4a+pichqtSAWl 9S1HV01MNeouk8PEr/zoUasmgfO3mz6N6XTUtF/tIi8K2kBOsLAtqltihFSd/zT8 ifYZE9cZTJ09lUs7kMz1wxFIsiegaE1jUYV+VSLu3PJ97oKhQpqop8EnkBAoBl6r mdmFryBQIdakPIdd4rO5Yg+to10n4u7Wij9ePIwWMfbqY4QoW5nXqMgFJQkIt4TG eQIDAQAB -----END PUBLIC KEY-----  ```    - **NOTE:** Password must be at least 20 characters long, have lower and upper case letters, numbers, and special characters. - **NOTE:** Phone number must be provided with country code, e.g. `+358404982201`.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\AccountApi();
$register_req = new \Swagger\Client\Model\RegisterReq(); // \Swagger\Client\Model\RegisterReq | Account parameters
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->register($register_req, $email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->register: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **register_req** | [**\Swagger\Client\Model\RegisterReq**](../Model/\Swagger\Client\Model\RegisterReq.md)| Account parameters |
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\RegisterResp**](../Model/RegisterResp.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **verifyEmail**
> \Swagger\Client\Model\Response verifyEmail($verify_email_req, $email, $mode)

VerifyEmail

Provide _code_ received to _email_ address for email address verification. Provide also _accesstoken_ received during login.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\AccountApi();
$verify_email_req = new \Swagger\Client\Model\VerifyEmailReq(); // \Swagger\Client\Model\VerifyEmailReq | Account parameters
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->verifyEmail($verify_email_req, $email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->verifyEmail: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **verify_email_req** | [**\Swagger\Client\Model\VerifyEmailReq**](../Model/\Swagger\Client\Model\VerifyEmailReq.md)| Account parameters |
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **verifyPhone**
> \Swagger\Client\Model\Response verifyPhone($verify_phone_req, $email, $mode, $phone)

VerifyPhone

Confirm phone number for _email_ _mode_ user, with _code_ received via SMS.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\AccountApi();
$verify_phone_req = new \Swagger\Client\Model\VerifyPhoneReq(); // \Swagger\Client\Model\VerifyPhoneReq | Account parameters
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode
$phone = "phone_example"; // string | Phone number with country code, e.g. `+358401234567`

try {
    $result = $api_instance->verifyPhone($verify_phone_req, $email, $mode, $phone);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->verifyPhone: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **verify_phone_req** | [**\Swagger\Client\Model\VerifyPhoneReq**](../Model/\Swagger\Client\Model\VerifyPhoneReq.md)| Account parameters |
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |
 **phone** | **string**| Phone number with country code, e.g. &#x60;+358401234567&#x60; |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

