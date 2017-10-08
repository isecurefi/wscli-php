<?php
/**
 * LoginResp
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
 * The API provides normal file based access to all common banks in Finland via WebServices channel on the service side, including certificate enrollment (PKI) with automatic renewals. Additionally, user account management, password recovery, and SMS based 2nd or Multi Factor Authentication (MFA) are provided (AWS Cognito Your User Pool). Access is secured with HTTPS/TLS (AWS API Gateway), using email address as username and password. On login, password is RSA encrypted along with dynamic parameters fetched from the service (username specific challenge response). Every integrator has own API Key and every user account belongs to one integrator. Certificate sharing between accounts is possible under the same integrator API Key, meaning that the enrolled bank connection certificates are shared. Under the same email address / username, both *admin* and *data* accounts must be used as they have separate passwords and differing functions.
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
 * LoginResp Class Doc Comment
 *
 * @category    Class
 * @package     Swagger\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class LoginResp implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'LoginResp';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'access_token' => 'string',
        'api_key' => 'string',
        'expires_in' => 'string',
        'id_token' => 'string',
        'response_code' => 'string',
        'response_text' => 'string',
        'session' => 'string'
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'access_token' => 'AccessToken',
        'api_key' => 'ApiKey',
        'expires_in' => 'ExpiresIn',
        'id_token' => 'IdToken',
        'response_code' => 'ResponseCode',
        'response_text' => 'ResponseText',
        'session' => 'Session'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'access_token' => 'setAccessToken',
        'api_key' => 'setApiKey',
        'expires_in' => 'setExpiresIn',
        'id_token' => 'setIdToken',
        'response_code' => 'setResponseCode',
        'response_text' => 'setResponseText',
        'session' => 'setSession'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'access_token' => 'getAccessToken',
        'api_key' => 'getApiKey',
        'expires_in' => 'getExpiresIn',
        'id_token' => 'getIdToken',
        'response_code' => 'getResponseCode',
        'response_text' => 'getResponseText',
        'session' => 'getSession'
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
        $this->container['access_token'] = isset($data['access_token']) ? $data['access_token'] : null;
        $this->container['api_key'] = isset($data['api_key']) ? $data['api_key'] : null;
        $this->container['expires_in'] = isset($data['expires_in']) ? $data['expires_in'] : null;
        $this->container['id_token'] = isset($data['id_token']) ? $data['id_token'] : null;
        $this->container['response_code'] = isset($data['response_code']) ? $data['response_code'] : null;
        $this->container['response_text'] = isset($data['response_text']) ? $data['response_text'] : null;
        $this->container['session'] = isset($data['session']) ? $data['session'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];

        if ($this->container['access_token'] === null) {
            $invalid_properties[] = "'access_token' can't be null";
        }
        if ($this->container['api_key'] === null) {
            $invalid_properties[] = "'api_key' can't be null";
        }
        if ($this->container['expires_in'] === null) {
            $invalid_properties[] = "'expires_in' can't be null";
        }
        if ($this->container['id_token'] === null) {
            $invalid_properties[] = "'id_token' can't be null";
        }
        if ($this->container['response_code'] === null) {
            $invalid_properties[] = "'response_code' can't be null";
        }
        if ($this->container['response_text'] === null) {
            $invalid_properties[] = "'response_text' can't be null";
        }
        if ($this->container['session'] === null) {
            $invalid_properties[] = "'session' can't be null";
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

        if ($this->container['access_token'] === null) {
            return false;
        }
        if ($this->container['api_key'] === null) {
            return false;
        }
        if ($this->container['expires_in'] === null) {
            return false;
        }
        if ($this->container['id_token'] === null) {
            return false;
        }
        if ($this->container['response_code'] === null) {
            return false;
        }
        if ($this->container['response_text'] === null) {
            return false;
        }
        if ($this->container['session'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets access_token
     * @return string
     */
    public function getAccessToken()
    {
        return $this->container['access_token'];
    }

    /**
     * Sets access_token
     * @param string $access_token Access token
     * @return $this
     */
    public function setAccessToken($access_token)
    {
        $this->container['access_token'] = $access_token;

        return $this;
    }

    /**
     * Gets api_key
     * @return string
     */
    public function getApiKey()
    {
        return $this->container['api_key'];
    }

    /**
     * Sets api_key
     * @param string $api_key Integrator API Key
     * @return $this
     */
    public function setApiKey($api_key)
    {
        $this->container['api_key'] = $api_key;

        return $this;
    }

    /**
     * Gets expires_in
     * @return string
     */
    public function getExpiresIn()
    {
        return $this->container['expires_in'];
    }

    /**
     * Sets expires_in
     * @param string $expires_in Session expiration time
     * @return $this
     */
    public function setExpiresIn($expires_in)
    {
        $this->container['expires_in'] = $expires_in;

        return $this;
    }

    /**
     * Gets id_token
     * @return string
     */
    public function getIdToken()
    {
        return $this->container['id_token'];
    }

    /**
     * Sets id_token
     * @param string $id_token ID token
     * @return $this
     */
    public function setIdToken($id_token)
    {
        $this->container['id_token'] = $id_token;

        return $this;
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
     * Gets session
     * @return string
     */
    public function getSession()
    {
        return $this->container['session'];
    }

    /**
     * Sets session
     * @param string $session Session token
     * @return $this
     */
    public function setSession($session)
    {
        $this->container['session'] = $session;

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


