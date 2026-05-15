# IsecureFi\WsApi\Client\SessionApi



All URIs are relative to https://ws-api.test.isecure.fi/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**initLogin()**](SessionApi.md#initLogin) | **GET** /session/{Email}/{Mode} | InitLogin |
| [**login()**](SessionApi.md#login) | **POST** /session/{Email}/{Mode} | Login |
| [**loginMFA()**](SessionApi.md#loginMFA) | **PUT** /session/{Email}/{Mode}/mfacode | LoginMFA |
| [**logout()**](SessionApi.md#logout) | **DELETE** /session/{Email}/{Mode} | Logout |


## `initLogin()`

```php
initLogin($email, $mode): \IsecureFi\WsApi\Client\Model\InitLoginResp
```

InitLogin

Before login, client must fetch `challenge` from the API. Then on login, the challenge must be passed along to the API (as response to the challenge). The challenge is always fresh for some period of time and the API validates it when passed with login. The challenge has form of `base64-string|timestamp|uuid`. For example:  ```ezwXceQ63fV9oWTSJBAE2Zq1Cw5tBIJe+7+Rl8jrgbk=|1475429754114|4017bda8-0a15-4154-a8b7-88069b05cb4e```  **NOTE:** The call must contain the same email as used for registration itself.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\SessionApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $apiInstance->initLogin($email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->initLogin: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |

### Return type

[**\IsecureFi\WsApi\Client\Model\InitLoginResp**](../Model/InitLoginResp.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `login()`

```php
login($email, $mode, $loginReq): \IsecureFi\WsApi\Client\Model\LoginResp
```

Login

After `getchallenge`, call `login` with _Email_, _Mode_, and RSA encrypted _admin_ or _data_ account _password_ and _challenge timestamp_. For further API calls (requiring authorization), include the received _IdToken_ into the Authorization header of the request (pass idtoken as required parameter with the client SDK API calls). The _IdToken_ expires in _ExpiresIn_ seconds, after which new login must be performed.  **NOTE:** In case SMS _Code_ is required, the call returns only _Session_, _ResponseCode_, and _ResponseText_ as the login process continues with the _LoginMFA_ API call.  **NOTE:** If _Email_ has not been yet verified, successful login provides only _ResponseCode_, _ResponseText_, and an _AccessToken_ that must be used to verify email address.   ``` % CHALLENGE=`curl -s https://ws-api.test.isecure.fi/v2/session/dan.forsberg@isecure.fi/data | jq -r .Challenge` % TIMESTAMP=`echo $CHALLENGE | cut -f 2 -d \\|` % ENCRYPTED=`echo -n testPassword..123455677098811\\|\\|$TIMESTAMP | openssl rsautl -oaep -encrypt -pubin -inkey test.pem | base64 ```

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\SessionApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode
$loginReq = new \IsecureFi\WsApi\Client\Model\LoginReq(); // \IsecureFi\WsApi\Client\Model\LoginReq | Login body parameters

try {
    $result = $apiInstance->login($email, $mode, $loginReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->login: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |
| **loginReq** | [**\IsecureFi\WsApi\Client\Model\LoginReq**](../Model/LoginReq.md)| Login body parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\LoginResp**](../Model/LoginResp.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `loginMFA()`

```php
loginMFA($email, $mode, $loginMFAReq): \IsecureFi\WsApi\Client\Model\LoginMFAResp
```

LoginMFA

Send SMS _Code_ along with previously received _Session_ token. If _Email_ has not been yet verified, successful login provides only _ResponseCode_, _ResponseText_, and an _AccessToken_ that must be used to verify email address. If email is already verified and the login succeeds, add the _IdToken_ from the login response as Authorization header in API requests requiring authorization (i.e. pass as parameter to client SDK API calls). _IdToken_ expires in _ExpiresIn_ seconds.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new IsecureFi\WsApi\Client\Api\SessionApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode
$loginMFAReq = new \IsecureFi\WsApi\Client\Model\LoginMFAReq(); // \IsecureFi\WsApi\Client\Model\LoginMFAReq | Session parameters

try {
    $result = $apiInstance->loginMFA($email, $mode, $loginMFAReq);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->loginMFA: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |
| **loginMFAReq** | [**\IsecureFi\WsApi\Client\Model\LoginMFAReq**](../Model/LoginMFAReq.md)| Session parameters | |

### Return type

[**\IsecureFi\WsApi\Client\Model\LoginMFAResp**](../Model/LoginMFAResp.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `logout()`

```php
logout($authorization, $xApiKey, $email, $mode): \IsecureFi\WsApi\Client\Model\Response
```

Logout

Logout user.  **NOTE**: AWS Cognito allows user logout, but the received authorization _IdToken_ **is still valid**. When the optional _AccessToken_ parameter is also provided, the _IdToken_ is also revoked.

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


$apiInstance = new IsecureFi\WsApi\Client\Api\SessionApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$authorization = 'authorization_example'; // string | Use _IdToken_ from the Login response as the `Authorization` header
$xApiKey = 'xApiKey_example'; // string | Use _ApiKey_ from the Login response as the `x-api-key` header
$email = 'email_example'; // string | Email address as the account username, e.g. `dan.forsberg@isecure.fi`
$mode = 'mode_example'; // string | Administer account with `admin` mode, exchange files with `data` mode

try {
    $result = $apiInstance->logout($authorization, $xApiKey, $email, $mode);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SessionApi->logout: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **authorization** | **string**| Use _IdToken_ from the Login response as the &#x60;Authorization&#x60; header | |
| **xApiKey** | **string**| Use _ApiKey_ from the Login response as the &#x60;x-api-key&#x60; header | |
| **email** | **string**| Email address as the account username, e.g. &#x60;dan.forsberg@isecure.fi&#x60; | |
| **mode** | **string**| Administer account with &#x60;admin&#x60; mode, exchange files with &#x60;data&#x60; mode | |

### Return type

[**\IsecureFi\WsApi\Client\Model\Response**](../Model/Response.md)

### Authorization

[Authorizer](../../README.md#Authorizer), [X-Api-Key](../../README.md#X-Api-Key)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
