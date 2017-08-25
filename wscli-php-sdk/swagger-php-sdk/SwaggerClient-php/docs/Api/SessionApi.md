# Swagger\Client\SessionApi

All URIs are relative to *https://ws-api.test.isecure.fi/v2/*

Method | HTTP request | Description
------------- | ------------- | -------------
[**initLogin**](SessionApi.md#initLogin) | **GET** /session/{Email}/{Mode} | InitLogin
[**login**](SessionApi.md#login) | **POST** /session/{Email}/{Mode} | Login
[**loginMFA**](SessionApi.md#loginMFA) | **PUT** /session/{Email}/{Mode}/mfacode | LoginMFA
[**logout**](SessionApi.md#logout) | **DELETE** /session/{Email}/{Mode} | Logout


# **initLogin**
> \Swagger\Client\Model\InitLoginResp initLogin($email, $mode)

InitLogin

Before login, client must fetch `challenge` from the server. Then on login, the challenge must be passed along to the server (as response to the challenge). The challenge is always fresh for some period of time and the server validates it when passed with login. The challenge has form of `base64-string|timestamp|uuid`. For example:  ```ezwXceQ63fV9oWTSJBAE2Zq1Cw5tBIJe+7+Rl8jrgbk=|1475429754114|4017bda8-0a15-4154-a8b7-88069b05cb4e```  **NOTE:** The call must contain the same email as used for registration itself.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\SessionApi();
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->initLogin($email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->initLogin: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\InitLoginResp**](../Model/InitLoginResp.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **login**
> \Swagger\Client\Model\LoginResp login($login_req, $email, $mode)

Login

After `getchallenge`, call `login` with _email_, _mode_, and RSA encrypted _admin_ or _data_ account _password_ and _challenge timestamp_. For further API calls (requiring authroization), include the received _idtoken_ into the Authorization header of the request (pass idtoken as required parameter with the client SDK API calls). The _IdToken_ expires in _ExpiresIn_ seconds, after which new login must be performed.  - **NOTE:** In case SMS _code_ is required, the call returns _session_. - **NOTE:** If _email_ has not been yet verified, successful login provides only _accesstoken_ that must be used to verify email address.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\SessionApi();
$login_req = new \Swagger\Client\Model\LoginReq(); // \Swagger\Client\Model\LoginReq | Login body parameters
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->login($login_req, $email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->login: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **login_req** | [**\Swagger\Client\Model\LoginReq**](../Model/\Swagger\Client\Model\LoginReq.md)| Login body parameters |
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\LoginResp**](../Model/LoginResp.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **loginMFA**
> \Swagger\Client\Model\LoginMFAResp loginMFA($login_mfa_req, $email, $mode)

LoginMFA

Send SMS _code_ along with previously received _session_ token. If _email_ has not been yet verified, successful login provides only _accesstoken_ that must be used to verify email address. If email is already verified and the login succeeds, add the _idtoken_ from the login response as Authorization header in API requests requiring authorization (i.e. pass as parameter to client SDK API calls). _IdToken_ expires in _ExpiresIn_ seconds.

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new Swagger\Client\Api\SessionApi();
$login_mfa_req = new \Swagger\Client\Model\LoginMFAReq(); // \Swagger\Client\Model\LoginMFAReq | Session parameters
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->loginMFA($login_mfa_req, $email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->loginMFA: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **login_mfa_req** | [**\Swagger\Client\Model\LoginMFAReq**](../Model/\Swagger\Client\Model\LoginMFAReq.md)| Session parameters |
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\LoginMFAResp**](../Model/LoginMFAResp.md)

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

# **logout**
> \Swagger\Client\Model\Response logout($authorization, $email, $mode)

Logout

Logout user.  - **NOTE**: AWS Cognito allows user logout, but the received authorization _IdToken_ **is still valid**. AWS hopefully fixes this soon.

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

$api_instance = new Swagger\Client\Api\SessionApi();
$authorization = "authorization_example"; // string | Use _IdToken_ from the Login response as the Authorization header
$email = "email_example"; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = "mode_example"; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $api_instance->logout($authorization, $email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->logout: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **authorization** | **string**| Use _IdToken_ from the Login response as the Authorization header |
 **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; |
 **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode |

### Return type

[**\Swagger\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

 - **Content-Type**: application/json
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

