<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Client\Model;

/**
 * Upload acknowledgement with an optional inline Base64 bank response.
 * Extends Response to preserve compatibility with existing acknowledgement consumers.
 * Prepared alongside the pending UploadFile contract; keep this on regeneration.
 */
class UploadFileResp extends Response
{
    public function __construct(?array $data = null)
    {
        parent::__construct($data);
        $this->container['content'] = $data['content'] ?? null;
    }

    public static function openAPITypes()
    {
        return parent::openAPITypes() + ['content' => 'string'];
    }

    public static function openAPIFormats()
    {
        return parent::openAPIFormats() + ['content' => null];
    }

    public static function attributeMap()
    {
        return parent::attributeMap() + ['content' => 'Content'];
    }

    public static function setters()
    {
        return parent::setters() + ['content' => 'setContent'];
    }

    public static function getters()
    {
        return parent::getters() + ['content' => 'getContent'];
    }

    public function getModelName()
    {
        return 'UploadFileResp';
    }

    public function getContent(): ?string
    {
        return $this->container['content'] ?? null;
    }

    public function setContent(string $content): self
    {
        $this->container['content'] = $content;
        return $this;
    }
}
