<?php

namespace Kinglozzer\SilverStripeTinyPng;


use Intervention\Image\Image;
use SilverStripe\Assets\Image_Backend;
use SilverStripe\Assets\InterventionBackend;
use SilverStripe\Assets\Storage\DBFile;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extension;

/**
 * Extension that adds the `Compressed` method to Image and DBFile.
 * Provides image compression through the TinyPNG API: https://tinypng.com/developers
 * @package Kinglozzer\SilverStripeTinyPng
 */
class ImageExtension extends Extension
{
    use Configurable;

    /**
     * Apply compression to the file at hand
     * @return DBFile The manipulated file
     */
    public function Compressed(){
        $variant = $this->owner->variantName(__FUNCTION__);
        return $this->owner->manipulateImage(
            $variant,
            function (Image_Backend $backend)  {

                // If the backend is not the InterventionBackend, return the unmodified source
                if (!($backend instanceof InterventionBackend)) {
                    return $backend;
                }

                /** @var Image $resource */
                $resource = $backend->getImageResource();
                $encoder = $resource->getDriver()->encoder;
                $resource->getDriver()->encoder = CompressedEncoder::create(
                    $encoder,
                    $this->config()->tinypng_api_key
                );
                return $backend;
            }
        );
    }
}
