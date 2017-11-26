<?php
/**
 * ShareCertsResp
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * ISECure WS Channel API
 *
 * The API provides secure file exchange with all common banks in Finland via *SEPA WebServices* channel on the API side towards the banks, including certificate enrollment (PKI) with automatic renewals.  The API specification in OpenAPI v2 format can be found on GitHub [isecurefi/wsapi-v2](https://github.com/isecurefi/wsapi-v2). Command line CLI and beefed-up PHP SDK are also available on GitHub [isecurefi/wscli-php](https://github.com/isecurefi/wscli-php).  API provides simple role based access control (RBAC) and user account management, password recovery, and SMS based Multi Factor Authentication based on AWS Cognito Your User Pool managed service.  *NOTE: The API endpoint for production is the same as for test, but without `test.` in the URL. Production and test APIs are deployd on separate AWS accounts*.  *NOTE: The API is run on AWS API Gateway and with AWS Lambda backend. When Lambda functions are cold, there is a small delay in response time. Additionally, banks have considerable delays in their processings, especially with certificate enrollments.*  ### Service enrollment  Every integrator (partner) has own *API Key* and every user account belongs to one integrator. *API Key* is bound with service subscription. In other words, enrolling fresh *API Key* requires service agreement before file transfers are allowed on production accounts.  If user registers with `0` *API Key* (i.e. no *API Key*) she gets a fresh *API Key* and becomes the *API Key* owner. The *API Key* owner account can list all users under the same *API Key*, see the *Integrator API*. Integrators (partners) registers their own *API Key* owner accounts and use it to register their client accounts.  *NOTE: API call rate limits are set and tracked per API Key by AWS API Gateway*.  ### Account management  A user (email address) can register either *admin* or *data* or both roles. The role in the API is referred to as *mode*. Both modes have separate passwords and provide differing capabilities for the user.  Login always requires account mode parameter in addition to user's email address and password. *Admin* mode login always requires an additional SMS one-time-password (MFA), whilst with *data* mode password is enough (suitable for automation). *Admin* mode is used to configure the account (e.g. adding PGP keys and sharing certs) and *data* mode to exchange files. Listing files is allowed on both modes.  *NOTE: Integrator (partner) registers her customers by using her API Key from the API Key owner account.*  ### Bank certificate enrollment  The *SEPA WebServices* connection to the bank requires enrolling PKI certificate with the bank. The *Admin* mode can enroll certificates for different banks, but only one certificate per bank. The corresponding private key is stored encrypted with AWS KMS service.  ### Bank certificate sharing  It is possible to share the same bank certificate with multiple accounts. Certificate sharing between accounts can be configured when accounts have the same API Key. Account that holds the certificate can share/unshare it with another account (*admin* mode operation). Note that only the account that has the certificate can PGP export the certificate and corresponding private key. This allows creating e.g. one *admin* mode only account and multiple *data* mode only accounts, where the *admin* account shares its certificates with other *data* accounts.  An account can never have multiple certificates per bank, be it shared or account's enrolled certificate. This is because the API requires identification of the bank, but not the certificate and private key pair.  ### Access security  Access is secured with TLS on Amazon Web Services (AWS) API Gateway. Inside TLS, secure sessions are established by using email address as username and by RSA encrypting password along with dynamic username specific parameters fetched from the API with `InitRegister` or `InitLogin` API commands (challenge response).  Successful login provides a session token (AWS Cognito User Pool). Authorization happens with the session token (`Authorization`) and *API Key* (`x-api-key`) headers.  Administrative actions require SMS based MFA authentication (see *admin* mode). User account management is handled with AWS Cognito User Pools and each user (email) has separate *admin* and/or *data* mode (role) accounts sharing the same API account data.
 *
 * OpenAPI spec version: v2.4.0
 * Contact: dan.forsberg@isecure.fi
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;

/**
 * ShareCertsResp Class Doc Comment
 *
 * @category    Class
 * @package     Swagger\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class ShareCertsResp implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'ShareCertsResp';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'response_code' => 'string',
        'response_text' => 'string',
        'shared_from' => 'string[]',
        'shared_to' => 'string[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerFormats = [
        'response_code' => null,
        'response_text' => null,
        'shared_from' => null,
        'shared_to' => null
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'response_code' => 'ResponseCode',
        'response_text' => 'ResponseText',
        'shared_from' => 'SharedFrom',
        'shared_to' => 'SharedTo'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'response_code' => 'setResponseCode',
        'response_text' => 'setResponseText',
        'shared_from' => 'setSharedFrom',
        'shared_to' => 'setSharedTo'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'response_code' => 'getResponseCode',
        'response_text' => 'getResponseText',
        'shared_from' => 'getSharedFrom',
        'shared_to' => 'getSharedTo'
    ];

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    public static function setters()
    {
        return self::$setters;
    }

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['response_code'] = isset($data['response_code']) ? $data['response_code'] : null;
        $this->container['response_text'] = isset($data['response_text']) ? $data['response_text'] : null;
        $this->container['shared_from'] = isset($data['shared_from']) ? $data['shared_from'] : null;
        $this->container['shared_to'] = isset($data['shared_to']) ? $data['shared_to'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];

        if ($this->container['response_code'] === null) {
            $invalid_properties[] = "'response_code' can't be null";
        }
        if ($this->container['response_text'] === null) {
            $invalid_properties[] = "'response_text' can't be null";
        }
        if ($this->container['shared_from'] === null) {
            $invalid_properties[] = "'shared_from' can't be null";
        }
        if ($this->container['shared_to'] === null) {
            $invalid_properties[] = "'shared_to' can't be null";
        }
        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {

        if ($this->container['response_code'] === null) {
            return false;
        }
        if ($this->container['response_text'] === null) {
            return false;
        }
        if ($this->container['shared_from'] === null) {
            return false;
        }
        if ($this->container['shared_to'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets response_code
     * @return string
     */
    public function getResponseCode()
    {
        return $this->container['response_code'];
    }

    /**
     * Sets response_code
     * @param string $response_code Two digit response code in string format
     * @return $this
     */
    public function setResponseCode($response_code)
    {
        $this->container['response_code'] = $response_code;

        return $this;
    }

    /**
     * Gets response_text
     * @return string
     */
    public function getResponseText()
    {
        return $this->container['response_text'];
    }

    /**
     * Sets response_text
     * @param string $response_text Human readable response text
     * @return $this
     */
    public function setResponseText($response_text)
    {
        $this->container['response_text'] = $response_text;

        return $this;
    }

    /**
     * Gets shared_from
     * @return string[]
     */
    public function getSharedFrom()
    {
        return $this->container['shared_from'];
    }

    /**
     * Sets shared_from
     * @param string[] $shared_from _ExtEmail_ account that this account shares certs from
     * @return $this
     */
    public function setSharedFrom($shared_from)
    {
        $this->container['shared_from'] = $shared_from;

        return $this;
    }

    /**
     * Gets shared_to
     * @return string[]
     */
    public function getSharedTo()
    {
        return $this->container['shared_to'];
    }

    /**
     * Sets shared_to
     * @param string[] $shared_to _ExtEmail_ account that this account shares certs for
     * @return $this
     */
    public function setSharedTo($shared_to)
    {
        $this->container['shared_to'] = $shared_to;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\Swagger\Client\ObjectSerializer::sanitizeForSerialization($this));
    }
}


