<?php
/**
 * HeaderSelector
 * PHP version 8.1
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * ISECure WS Channel API
 *
 * The API provides secure file exchange with all common banks in Finland via *SEPA WebServices* channel on the API side towards the banks, including certificate enrollment (PKI) with automatic renewals.  The API specification in OpenAPI v2 format can be found on GitHub [isecurefi/wsapi-v2](https://github.com/isecurefi/wsapi-v2). Command line CLI and beefed-up PHP SDK are also available on GitHub [isecurefi/wscli-php](https://github.com/isecurefi/wscli-php).  ``` % export SESSION=~/.wscli/settings.yaml % wscli session login -c $SESSION % export APIKEY=`yq -r .settings.apikey $SESSION` % export IDTOKEN=`yq -r .settings.idtoken $SESSION` % % curl -H Authorization:$IDTOKEN \\         -H x-api-key:$APIKEY \\         https://ws-api.isecure.fi/v2/files/danskebank ```  API provides simple role based access control (RBAC) and user account management, password recovery, and SMS based Multi Factor Authentication based on AWS Cognito Your User Pool managed service.  *NOTE: The API endpoint for production is the same as for test, but without `test.` in the URL. Production and test APIs are deployd on separate AWS accounts*.  *NOTE: The API is run on AWS API Gateway and with AWS Lambda backend. When Lambda functions are cold, there is a small delay in response time. Additionally, banks have considerable delays in their processings, especially with certificate enrollments.*  ### Service enrollment  Every integrator (partner) has own *API Key* and every user account belongs to one integrator. *API Key* is bound with service subscription. In other words, enrolling fresh *API Key* requires service agreement before file transfers are allowed on production accounts.  If user registers with `0` *API Key* (i.e. no *API Key*) she gets a fresh *API Key* and becomes the *API Key* owner. The *API Key* owner account can list all users under the same *API Key*, see the *Integrator API*. Integrators (partners) registers their own *API Key* owner accounts and use it to register their client accounts.  *NOTE: API call rate limits are set and tracked per API Key by AWS API Gateway*.  ### Account management  A user (email address) can register either *admin* or *data* or both roles. The role in the API is referred to as *mode*. Both modes have separate passwords and provide differing capabilities for the user.  Login always requires account mode parameter in addition to user's email address and password. *Admin* mode login always requires an additional SMS one-time-password (MFA), whilst with *data* mode password is enough (suitable for automation). *Admin* mode is used to configure the account (e.g. adding PGP keys and sharing certs) and *data* mode to exchange files. Listing files is allowed on both modes.  *NOTE: Integrator (partner) registers her customers by using her API Key from the API Key owner account.*  ### Bank certificate enrollment  The *SEPA WebServices* connection to the bank requires enrolling PKI certificate with the bank. The *Admin* mode can enroll certificates for different banks, but only one certificate per bank. The corresponding private key is stored encrypted with AWS KMS service.  ### Bank certificate sharing  It is possible to share the same bank certificate with multiple accounts. Certificate sharing between accounts can be configured when accounts have the same API Key. Account that holds the certificate can share/unshare it with another account (*admin* mode operation). Note that only the account that has the certificate can PGP export the certificate and corresponding private key. This allows creating e.g. one *admin* mode only account and multiple *data* mode only accounts, where the *admin* account shares its certificates with other *data* accounts.  An account can never have multiple certificates per bank, be it shared or account's enrolled certificate. This is because the API requires identification of the bank, but not the certificate and private key pair.  ### Access security  Access is secured with TLS on Amazon Web Services (AWS) API Gateway. Inside TLS, secure sessions are established by using email address as username and by RSA encrypting password along with dynamic username specific parameters fetched from the API with `InitRegister` or `InitLogin` API commands (challenge response).  Successful login provides a session token (AWS Cognito User Pool). Authorization happens with the session token (`Authorization`) and *API Key* (`x-api-key`) headers.  Administrative actions require SMS based MFA authentication (see *admin* mode). User account management is handled with AWS Cognito User Pools and each user (email) has separate *admin* and/or *data* mode (role) accounts sharing the same API account data.  ### CHANGELOG  **2.6.0** :: 2021-06-14 - Added token revocation for logout (AWS SDK based new capability), no changes to the API itself.  **2.5.0** :: 2020-04-19 - Removed OPTIONS - Fixed Login response.
 *
 * The version of the OpenAPI document: v2.6.0
 * Contact: dan.forsberg@isecure.fi
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.22.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace IsecureFi\WsApi\Client;

/**
 * HeaderSelector Class Doc Comment
 *
 * @category Class
 * @package  IsecureFi\WsApi\Client
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class HeaderSelector
{
    /**
     * @param string[] $accept
     * @param string   $contentType
     * @param bool     $isMultipart
     * @return string[]
     */
    public function selectHeaders(array $accept, string $contentType, bool $isMultipart): array
    {
        $headers = [];

        $accept = $this->selectAcceptHeader($accept);
        if ($accept !== null) {
            $headers['Accept'] = $accept;
        }

        if (!$isMultipart) {
            if($contentType === '') {
                $contentType = 'application/json';
            }

            $headers['Content-Type'] = $contentType;
        }

        return $headers;
    }

    /**
     * Return the header 'Accept' based on an array of Accept provided.
     *
     * @param string[] $accept Array of header
     *
     * @return null|string Accept (e.g. application/json)
     */
    private function selectAcceptHeader(array $accept): ?string
    {
        # filter out empty entries
        $accept = array_filter($accept);

        if (count($accept) === 0) {
            return null;
        }

        # If there's only one Accept header, just use it
        if (count($accept) === 1) {
            return reset($accept);
        }

        # If none of the available Accept headers is of type "json", then just use all them
        $headersWithJson = $this->selectJsonMimeList($accept);
        if (count($headersWithJson) === 0) {
            return implode(',', $accept);
        }

        # If we got here, then we need add quality values (weight), as described in IETF RFC 9110, Items 12.4.2/12.5.1,
        # to give the highest priority to json-like headers - recalculating the existing ones, if needed
        return $this->getAcceptHeaderWithAdjustedWeight($accept, $headersWithJson);
    }

    /**
    * Detects whether a string contains a valid JSON mime type
    *
    * @param string $searchString
    * @return bool
    */
    public function isJsonMime(string $searchString): bool
    {
        return preg_match('~^application/(json|[\w!#$&.+-^_]+\+json)\s*(;|$)~', $searchString) === 1;
    }
    
    /**
    * Select all items from a list containing a JSON mime type
    *
    * @param array $mimeList
    * @return array
    */
    private function selectJsonMimeList(array $mimeList): array {
        $jsonMimeList = [];
        foreach ($mimeList as $mime) {
            if($this->isJsonMime($mime)) {
                $jsonMimeList[] = $mime;
            }
        }
        return $jsonMimeList;
    }


    /**
    * Create an Accept header string from the given "Accept" headers array, recalculating all weights
    *
    * @param string[] $accept            Array of Accept Headers
    * @param string[] $headersWithJson   Array of Accept Headers of type "json"
    *
    * @return string "Accept" Header (e.g. "application/json, text/html; q=0.9")
    */
    private function getAcceptHeaderWithAdjustedWeight(array $accept, array $headersWithJson): string
    {
        $processedHeaders = [
            'withApplicationJson' => [],
            'withJson' => [],
            'withoutJson' => [],
        ];

        foreach ($accept as $header) {

            $headerData = $this->getHeaderAndWeight($header);

            if (stripos($headerData['header'], 'application/json') === 0) {
                $processedHeaders['withApplicationJson'][] = $headerData;
            } elseif (in_array($header, $headersWithJson, true)) {
                $processedHeaders['withJson'][] = $headerData;
            } else {
                $processedHeaders['withoutJson'][] = $headerData;
            }
        }

        $acceptHeaders = [];
        $currentWeight = 1000;

        $hasMoreThan28Headers = count($accept) > 28;

        foreach($processedHeaders as $headers) {
            if (count($headers) > 0) {
                $acceptHeaders[] = $this->adjustWeight($headers, $currentWeight, $hasMoreThan28Headers);
            }
        }

        $acceptHeaders = array_merge(...$acceptHeaders);

        return implode(',', $acceptHeaders);
    }

    /**
     * Given an Accept header, returns an associative array splitting the header and its weight
     *
     * @param string $header "Accept" Header
     *
     * @return array with the header and its weight
     */
    private function getHeaderAndWeight(string $header): array
    {
        # matches headers with weight, splitting the header and the weight in $outputArray
        if (preg_match('/(.*);\s*q=(1(?:\.0+)?|0\.\d+)$/', $header, $outputArray) === 1) {
            $headerData = [
                'header' => $outputArray[1],
                'weight' => (int)($outputArray[2] * 1000),
            ];
        } else {
            $headerData = [
                'header' => trim($header),
                'weight' => 1000,
            ];
        }

        return $headerData;
    }

    /**
     * @param array[] $headers
     * @param float   $currentWeight
     * @param bool    $hasMoreThan28Headers
     * @return string[] array of adjusted "Accept" headers
     */
    private function adjustWeight(array $headers, float &$currentWeight, bool $hasMoreThan28Headers): array
    {
        usort($headers, function (array $a, array $b) {
            return $b['weight'] - $a['weight'];
        });

        $acceptHeaders = [];
        foreach ($headers as $index => $header) {
            if($index > 0 && $headers[$index - 1]['weight'] > $header['weight'])
            {
                $currentWeight = $this->getNextWeight($currentWeight, $hasMoreThan28Headers);
            }

            $weight = $currentWeight;

            $acceptHeaders[] = $this->buildAcceptHeader($header['header'], $weight);
        }

        $currentWeight = $this->getNextWeight($currentWeight, $hasMoreThan28Headers);

        return $acceptHeaders;
    }

    /**
     * @param string $header
     * @param int    $weight
     * @return string
     */
    private function buildAcceptHeader(string $header, int $weight): string
    {
        if($weight === 1000) {
            return $header;
        }

        return trim($header, '; ') . ';q=' . rtrim(sprintf('%0.3f', $weight / 1000), '0');
    }

    /**
     * Calculate the next weight, based on the current one.
     *
     * If there are less than 28 "Accept" headers, the weights will be decreased by 1 on its highest significant digit, using the
     * following formula:
     *
     *    next weight = current weight - 10 ^ (floor(log(current weight - 1)))
     *
     *    ( current weight minus ( 10 raised to the power of ( floor of (log to the base 10 of ( current weight minus 1 ) ) ) ) )
     *
     * Starting from 1000, this generates the following series:
     *
     * 1000, 900, 800, 700, 600, 500, 400, 300, 200, 100, 90, 80, 70, 60, 50, 40, 30, 20, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1
     *
     * The resulting quality codes are closer to the average "normal" usage of them (like "q=0.9", "q=0.8" and so on), but it only works
     * if there is a maximum of 28 "Accept" headers. If we have more than that (which is extremely unlikely), then we fall back to a 1-by-1
     * decrement rule, which will result in quality codes like "q=0.999", "q=0.998" etc.
     *
     * @param int  $currentWeight varying from 1 to 1000 (will be divided by 1000 to build the quality value)
     * @param bool $hasMoreThan28Headers
     * @return int
     */
    public function getNextWeight(int $currentWeight, bool $hasMoreThan28Headers): int
    {
        if ($currentWeight <= 1) {
            return 1;
        }

        if ($hasMoreThan28Headers) {
            return $currentWeight - 1;
        }

        return $currentWeight - 10 ** floor( log10($currentWeight - 1) );
    }
}
