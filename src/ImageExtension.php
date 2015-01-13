<?php

namespace Kinglozzer\SilverStripeTinyPng;

use Extension;

class ImageExtension extends Extension
{
    public function Compressed()
    {
        return $this->owner->setCompressed(true);
    }
}
