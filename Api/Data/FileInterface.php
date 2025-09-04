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
     * Set file identifier
     *
     * @param string $value
     * @return void
     */
    public function setIdentifier(string $value): void;

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
     * Get file relative path
     *
     * @return string
     */
    public function getRelativePath(): string;

    /**
     * Set file relative path
     *
     * @param string $value
     * @return void
     */
    public function setRelativePath(string $value): void;

    /**
     * Get full file path
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getFullPath(): string;

    /**
     * Set full file path
     *
     * @param string $value
     * @return void
     */
    public function setFullPath(string $value): void;

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
     * Gets the file extension
     *
     * @return string|null
     */
    public function getExtension(): ?string;

    /**
     * Sets the file extension
     *
     * @param string|null $value
     * @return void
     */
    public function setExtension(?string $value): void;

    /**
     * Get file media URL
     *
     * @return string|null
     */
    public function getMediaUrl(): ?string;

    /**
     * Set file media URL
     *
     * @param string|null $value
     * @return void
     */
    public function setMediaUrl(?string $value): void;
}
