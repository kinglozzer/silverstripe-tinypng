#SilverStripe TinyPNG#

An extension to SilverStripe's `Image` class to integrate with the [TinyPNG API](https://tinypng.com/developers).

**NOTE:** this is currently just a proof-of-concept: the API is unstable and will almost certainly undergo heavy changes before release 1.0.0.

##Installation##

With composer:

```
require: "kinglozzer/silverstripe-tinypng": "dev-master"
```

##Usage##

Add your TinyPNG API key to your `_config.yml`:

```yml
Kinglozzer\SilverStripeTinyPng\Image:
  tinypng_api_key: 'xxxx_yyyy_zzzz'
```

Add the `Compressed` method to your templates when outputting images:

```
{$Image.Compressed.CroppedImage(150, 300)}
```

If you set an invalid API key, or exceed your monthly API allowance, the compression will (intentionally) silently fail: outputting the original, un-compressed image.
