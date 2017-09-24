# EnrollCertReq

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**code** | **string** | Full PIN code from bank (e.g. combined from SMS and letter) | 
**company** | **string** | Company name as registered with bank (e.g. full capital letters without Oy, see contract) | 
**ws_target_id** | **string** | WebServices channel target id as in contract with bank. Mandatory for &#x60;nordea&#x60;, set it to &#x60;11111111A1&#x60;. For &#x60;osuuspankki&#x60; API side will set it to &#x60;MLP&#x60;. For Samlink banks (&#x60;shb&#x60;, &#x60;sp&#x60;, &#x60;aktia&#x60;, &#x60;pop&#x60;) and &#x60;spankki&#x60;, not used. For &#x60;danskebank&#x60; set it to &#x60;1&#x60;. | 
**ws_user_id** | **string** | WebServices channel user id as in contract with bank | 

[[Back to Model list]](../README.md#documentation-for-models) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to README]](../README.md)


