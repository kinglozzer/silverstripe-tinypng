# SilverStripe TinyPNG

An extension to SilverStripe's `Image` class to integrate with the [TinyPNG API](https://tinypng.com/developers).

## Installation

With composer:

```
composer require "kinglozzer/silverstripe-tinypng":"dev-master"
```

## Usage

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

If you set an invalid API key, or exceed your monthly API allowance, then a warning will be shown in dev/test mode. In live mode compression will (intentionally) silently fail and output the un-compressed image, but will still log a message via the `SS_Log` API.
