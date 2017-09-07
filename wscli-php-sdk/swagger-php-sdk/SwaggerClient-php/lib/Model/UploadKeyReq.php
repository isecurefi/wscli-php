<?php
/**
 * UploadKeyReq
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
 * OpenAPI spec version: v2.2.5
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
 * UploadKeyReq Class Doc Comment
 *
 * @category    Class
 * @package     Swagger\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class UploadKeyReq implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'UploadKeyReq';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'pgp_key' => 'string',
        'pgp_key_purpose' => 'string'
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
        'pgp_key' => 'PgpKey',
        'pgp_key_purpose' => 'PgpKeyPurpose'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'pgp_key' => 'setPgpKey',
        'pgp_key_purpose' => 'setPgpKeyPurpose'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'pgp_key' => 'getPgpKey',
        'pgp_key_purpose' => 'getPgpKeyPurpose'
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
        $this->container['pgp_key'] = isset($data['pgp_key']) ? $data['pgp_key'] : null;
        $this->container['pgp_key_purpose'] = isset($data['pgp_key_purpose']) ? $data['pgp_key_purpose'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];

        if ($this->container['pgp_key'] === null) {
            $invalid_properties[] = "'pgp_key' can't be null";
        }
        if ($this->container['pgp_key_purpose'] === null) {
            $invalid_properties[] = "'pgp_key_purpose' can't be null";
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

        if ($this->container['pgp_key'] === null) {
            return false;
        }
        if ($this->container['pgp_key_purpose'] === null) {
            return false;
        }
        return true;
    }


    /**
     * Gets pgp_key
     * @return string
     */
    public function getPgpKey()
    {
        return $this->container['pgp_key'];
    }

    /**
     * Sets pgp_key
     * @param string $pgp_key ASCII armored PGP Key
     * @return $this
     */
    public function setPgpKey($pgp_key)
    {
        $this->container['pgp_key'] = $pgp_key;

        return $this;
    }

    /**
     * Gets pgp_key_purpose
     * @return string
     */
    public function getPgpKeyPurpose()
    {
        return $this->container['pgp_key_purpose'];
    }

    /**
     * Sets pgp_key_purpose
     * @param string $pgp_key_purpose PGP key purpose, i.e. `export` (exporting cert private key) or `authorize` (upload content authorization verification).
     * @return $this
     */
    public function setPgpKeyPurpose($pgp_key_purpose)
    {
        $this->container['pgp_key_purpose'] = $pgp_key_purpose;

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


