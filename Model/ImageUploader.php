<?php

namespace Puga\Action\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Catalog image uploader
 */
class ImageUploader
{
    /**
     * Core file storage database
     *
     * @var Database
     */
    protected $coreFileStorageDatabase;

    /**
     * Media directory object (writable).
     *
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Uploader factory
     *
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Base tmp path
     *
     * @var string
     */
    protected $baseTmpPath;

    /**
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Allowed extensions
     *
     * @var string
     */
    protected $allowedExtensions;

    /**
     * List of allowed image mime types
     *
     * @var string[]
     */
    private $allowedMimeTypes;

    const FILE_DIR = 'action/image';

    /**
     * ImageUploader constructor
     *
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param string $baseTmpPath
     * @param string $basePath
     * @param string[] $allowedExtensions
     * @param string[] $allowedMimeTypes
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        $baseTmpPath,
        $basePath,
        $allowedExtensions,
        $allowedMimeTypes = []
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->baseTmpPath = $baseTmpPath;
        $this->basePath = $basePath;
        $this->allowedExtensions = $allowedExtensions;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Set allowed extensions
     *
     * @param string[] $allowedExtensions
     *
     * @return void
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return $this->mediaDirectory->getAbsolutePath('tmp/' . self::FILE_DIR);
    }


    /**
     * Retrieve temp media url
     *
     * @param string $file
     * @return string
     */
    protected function getTmpMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . 'tmp/' . self::FILE_DIR . '/' . $this->prepareFile($file);
    }

    /**
     * Prepare file
     *
     * @param string $file
     * @return string
     */
    protected function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * Retrieve base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return self::FILE_DIR;
    }

    /**
     * Retrieve allowed extensions
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Checking file for moving and move it
     *
     * @param string $imageName
     *
     * @return string
     *
     * @throws LocalizedException
     */
    public function moveFileFromTmp($imageName)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();

        $baseImagePath = $this->getFilePath(
            $basePath,
            Uploader::getNewFileName(
                $this->mediaDirectory->getAbsolutePath(
                    $this->getFilePath($basePath, $imageName)
                )
            )
        );
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);

        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $imageName;
    }

    /**
     * Checking file for save and save it to tmp dir
     *
     * @param string $fileId
     *
     * @return string[]
     *
     * @throws LocalizedException
     */
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();

        /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        if (!$uploader->checkMimeType($this->allowedMimeTypes)) {
            throw new LocalizedException(__('File validation failed.'));
        }
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        $result['url'] = $this->getTmpMediaUrl($result['file']);

        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

        return $result;
    }
}
