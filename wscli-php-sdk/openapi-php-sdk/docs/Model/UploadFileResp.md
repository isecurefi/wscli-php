# UploadFileResp

Extends `Response`, preserving acknowledgement compatibility.

| Property | Type | Description |
| --- | --- | --- |
| responseCode | string | Bank response code. Check this before using a report. |
| responseText | string | Bank response description. |
| content | string, optional | Base64 bank report returned directly by UploadFile. |

`getContent(): ?string` returns `null` when the bank only acknowledges the upload.
The SDK preserves the encoded content without downloading another file or decoding
it. Synchronous and asynchronous UploadFile methods return this model.
