<?php

declare(strict_types=1);

namespace EPuzzle\FileUploader\Api\Data;

/**
 * The file entity
 */
interface FileInterface
{
    /**
     * Get entity ID
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity ID
     *
     * @param int $entityId
     * @return \EPuzzle\FileUploader\Api\Data\FileInterface
     */
    public function setEntityId($entityId): FileInterface;

    /**
     * Get file identifier
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Get file type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set file type
     *
     * @param string $value
     * @return void
     */
    public function setType(string $value): void;

    /**
     * Get file name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set file name
     *
     * @param string $value
     * @return void
     */
    public function setName(string $value): void;

    /**
     * Get file path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Set file path
     *
     * @param string $value
     * @return void
     */
    public function setPath(string $value): void;

    /**
     * Get file size
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * Set file size
     *
     * @param int $value
     * @return void
     */
    public function setSize(int $value): void;

    /**
     * Get file media URL
     *
     * @return string
     */
    public function getMediaUrl(): string;

    /**
     * Set file media URL
     *
     * @param string $value
     * @return void
     */
    public function setMediaUrl(string $value): void;
}
