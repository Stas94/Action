<?php

namespace Puga\Action\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Catalog image uploader
 */
class ImageUploader
{

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
     * Base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * List of allowed image mime types
     *
     * @var string[]
     */
    private $allowedMimeTypes;

    const FILE_DIR = 'puga/action/image';

    /**
     * ImageUploader constructor
     *
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param string $basePath
     * @param string[] $allowedMimeTypes
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        $basePath,
        $allowedMimeTypes = []
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->basePath = $basePath;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Retrieve temp media url
     *
     * @param string $file
     * @return string
     */
    protected function getMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . self::FILE_DIR . '/' . $this->prepareFile($file);
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
     * Checking file for save and save it to tmp dir
     *
     * @param string $fileId
     *
     * @return string[]
     *
     * @throws LocalizedException
     */
    public function saveFileToDir($fileId)
    {
        /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $uploader->setAllowRenameFiles(true);
        if (!$uploader->checkMimeType($this->allowedMimeTypes)) {
            throw new LocalizedException(__('File validation failed.'));
        }
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath(self::FILE_DIR));
        $result['url'] = $this->getMediaUrl($result['file']);

        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }

;        return $result;
    }
}
