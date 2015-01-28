<?php

namespace Kinglozzer\SilverStripeTinyPng;

use Image as SilverStripeImage;
use Image_Cached as SilverStripeImage_Cached;
use Kinglozzer\TinyPng\Client;

class Image extends SilverStripeImage implements \Flushable
{
    /**
     * @config
     * @var bool Regenerates images if set to true. This is set by {@link flush()}
     */
    private static $flush = false;
    /**
     * @var boolean
     */
    protected $compressed = false;
    /**
     * @var Kinglozzer\TinyPng\Client
     */
    protected $client;

    /**
     * @param array|null $record
     * @param boolean $isSingleton
     * @param DataModel|null $model
     */
    public function __construct($record = null, $isSingleton = false, $model = null)
    {
        parent::__construct($record, $isSingleton, $model);

        $this->setClient(new Client($this->config()->tinypng_api_key));
    }

    /**
     * Triggered early in the request when someone requests a flush.
     */
    public static function flush()
    {
        self::$flush = true;
    }

    /**
     * @param boolean $compressed
     * @return $this
     */
    public function setCompressed($compressed)
    {
        $this->compressed = $compressed;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCompressed()
    {
        return $this->compressed;
    }

    /**
     * @param \Kinglozzer\TinyPng\Client $client
     * @return $this
     */
    public function setClient(\Kinglozzer\TinyPng\Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Kinglozzer\TinyPng\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Return an image object representing the image in the given format.
     * This image will be generated using generateFormattedImage().
     * The generated image is cached, to flush the cache append ?flush=1 to your URL.
     *
     * Just pass the correct number of parameters expected by the working function
     *
     * @param string $format The name of the format.
     * @return Image_Cached
     */
    public function getFormattedImage($format)
    {
        $args = func_get_args();
        
        if ($this->ID && $this->Filename && \Director::fileExists($this->Filename)) {
            $cacheFile = call_user_func_array(array($this, "cacheFilename"), $args);
            
            if (! file_exists(\Director::baseFolder() . "/" . $cacheFile) || self::$flush) {
                call_user_func_array(array($this, "generateFormattedImage"), $args);
            }
            
            $cached = \Injector::inst()->createWithArgs('Image_Cached', array($cacheFile));
            // Pass through the title so the templates can use it
            $cached->Title = $this->Title;
            // Pass through the parent, to store cached images in correct folder.
            $cached->ParentID = $this->ParentID;
            return $cached;
        }
    }
    
    /**
     * @return string
     */
    public function forTemplate()
    {
        $path = \Director::baseFolder() . '/' . $this->Filename;

        try {
            if (file_exists($path)) {
                $this->client->compress($path);
                $this->client->storeFile($path);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        return parent::forTemplate();
    }
}

class Image_Cached extends Image
{
    /**
     * Create a new cached image.
     * @param string $filename The filename of the image.
     * @param boolean $isSingleton This this to true if this is a singleton() object, a stub for calling methods.
     *                             Singletons don't have their defaults set.
     */
    public function __construct($filename = null, $isSingleton = false)
    {
        parent::__construct(array(), $isSingleton);
        $this->ID = -1;
        $this->Filename = $filename;
    }
    
    /**
     * @return string
     */
    public function getRelativePath()
    {
        return $this->getField('Filename');
    }
    
    /**
     * Prevent creating new tables for the cached record
     * @return false
     */
    public function requireTable()
    {
        return false;
    }
    
    /**
     * Prevent writing the cached image to the database
     * @throws Exception
     */
    public function write($showDebug = false, $forceInsert = false, $forceWrite = false, $writeComponents = false)
    {
        throw new \Exception("{$this->ClassName} can not be written back to the database.");
    }
}
