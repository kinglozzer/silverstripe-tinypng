<?php

namespace Kinglozzer\SilverStripeTinyPng;


use Exception;
use Intervention\Image\AbstractEncoder;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injectable;

/**
 * Custom encoder that delegates compression of images to the TinyPNG API.
 * Wraps the existing encoder.
 * Only PNG and JPEG image-types will be compressed, other types will be encoded as-is.
 *
 * @package Kinglozzer\SilverStripeTinyPng
 */
class CompressedEncoder extends AbstractEncoder
{
    use Injectable;

    private static $dependencies = [
        'logger' => '%$Psr\Log\LoggerInterface',
    ];

    /**
     * The logger instance.
     * This will be set automatically via dependency injection, as long as CompressedEncoder is instantiated via Injector
     * @var LoggerInterface
     */
    public $logger;

    /**
     * The wrapped encoder
     * @var AbstractEncoder
     */
    private $encoder;

    /**
     * The Tiny PNG API Key
     * @var string
     */
    private $apiKey;

    /**
     * Create a compressed encoder.
     * This wraps an existing recorder and uses the TinyPNG API to further compress the encoding result
     * @param AbstractEncoder $originalEncoder
     * @param $apiKey
     */
    public function __construct(AbstractEncoder $originalEncoder, $apiKey)
    {
        $this->encoder = $originalEncoder;
        $this->apiKey = $apiKey;
    }

    protected function setImage($image)
    {
        $this->image = $image;
        $this->encoder->setImage($image);
    }

    protected function setFormat($format = null)
    {
        $this->format = $format;
        return $this->encoder->setFormat($format);
    }

    protected function setQuality($quality)
    {
        $this->quality = $quality;
        return $this->encoder->setQuality($quality);
    }

    /**
     * Processes and returns encoded image as JPEG string
     *
     * @return string
     */
    protected function processJpeg()
    {
        return $this->tinify($this->encoder->processJpeg());
    }

    /**
     * Processes and returns encoded image as PNG string
     *
     * @return string
     */
    protected function processPng()
    {
        return $this->tinify($this->encoder->processPng());
    }

    /**
     * Processes and returns encoded image as GIF string
     *
     * @return string
     */
    protected function processGif()
    {
        return $this->encoder->processGif();
    }

    /**
     * Processes and returns encoded image as TIFF string
     *
     * @return string
     */
    protected function processTiff()
    {
        return $this->encoder->processTiff();
    }

    /**
     * Processes and returns encoded image as BMP string
     *
     * @return string
     */
    protected function processBmp()
    {
        return $this->encoder->processBmp();
    }

    /**
     * Processes and returns encoded image as ICO string
     *
     * @return string
     */
    protected function processIco()
    {
        return $this->encoder->processIco();
    }

    /**
     * Processes and returns image as WebP encoded string
     *
     * @return string
     */
    protected function processWebp()
    {
        return $this->encoder->processWebp();
    }

    /**
     * Processes and returns image as AVIF encoded string
     *
     * @return string
     */
    protected function processAvif()
    {
        return $this->encoder->processAvif();
    }

    /**
     * Processes and returns image as Heic encoded string
     *
     * @return string
     */
    protected function processHeic()
    {
        return $this->encoder->processHeic();
    }

    protected function tinify($buffer)
    {
        $compressed = $buffer;
        try {
            \Tinify\setKey($this->apiKey);
            $compressed = \Tinify\fromBuffer($buffer)->toBuffer();
        } catch(\Tinify\AccountException $e) {
            $this->logger->error(
                'Cannot compress image. Invalid API Key or account limit reached.',
                ['exception' => $e]
            );
        } catch(\Tinify\ClientException $e) {
            $this->logger->error(
                'Cannot compress image. Invalid source image or invalid format.',
                ['exception' => $e]
            );
        } catch(\Tinify\ServerException $e) {
            $this->logger->warning(
                'Cannot compress image. Tiny PNG API error',
                ['exception' => $e]
            );
        } catch(\Tinify\ConnectionException $e) {
            $this->logger->warning(
                'Cannot compress image. Network error.',
                ['exception' => $e]
            );
        } catch(Exception $e) {
            $this->logger->warning(
                'Cannot compress image.',
                ['exception' => $e]
            );
        }

        return $compressed;
    }
}
