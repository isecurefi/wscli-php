# LoginResp

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**accessToken** | **string** | Access token - **Not** present on MFA login initiation, i.e. &#x60;admin&#x60; mode - **Only** present when Email verification is required | [optional]
**apiKey** | **string** | Integrator API Key - **Not** present on MFA login initiation, i.e. &#x60;admin&#x60; mode) | [optional]
**expiresIn** | **string** | Session expiration time - **Not** present on MFA login initiation, i.e. &#x60;admin&#x60; mode) | [optional]
**idToken** | **string** | ID token - **Not** present on MFA login initiation, i.e. &#x60;admin&#x60; mode | [optional]
**responseCode** | **string** | Two digit response code in string format |
**responseText** | **string** | Human readable response text |
**session** | **string** | Session token - **Only** present on MFA login initiation, i.e. &#x60;admin&#x60; mode) | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
