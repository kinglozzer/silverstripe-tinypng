<?php

class TinyPngImageExtension extends Extension
{
    public function Compressed()
    {
        $this->owner->setCompressed(true);
        return $this->owner->getFormattedImage('CompressedImage');
    }

    public function generateCompressedImage(Image_Backend $backend)
    {
        return $backend;
    }
}
