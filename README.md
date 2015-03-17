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
TinyPngImage:
  tinypng_api_key: 'xxxx_yyyy_zzzz'
```

Add the `Compressed` method to your templates when outputting images:

```
{$Image.CroppedImage(150, 300).Compressed}
```

**NOTE:** `Compressed` must be the last modification you call on your image - otherwise you’ll compress an image, then resample it again afterwards, potentially undo-ing the compression.

If you set an invalid API key, or exceed your monthly API allowance, the compression will (intentionally) silently fail: outputting the original, un-compressed image.
