Jasny Audio
===========

Process audio files using [SoX](http://sox.sourceforge.net/)

## Waveform

With the Jasny\Audio\Waveform class you can create a waveform as PNG like:

![waveform](https://f.cloud.github.com/assets/100821/1049488/8c209342-10a6-11e3-9149-cc56e1fcfcea.png)

```php
$waveform = new Waveform($filename, $options);
$waveform->output();
```

Alternatively you can request a set of samples. This can be used to set draw a wavefrom in JavaScript (see waveform.js).

### Settings

option   | default | unit     | description
---------|---------|----------|-----------------------------------------
width    | 1800    | pixels   | Image width
height   | 280     | pixels   | Image height
color    | 000000  | hex|rgba | Color of the graph
axis     | null    | hex|rgba | Color of the x axis
level    | null    |          | The max amplitute (y axis)
offset   | null    | seconds  | Starting point. Negative counts from end
duration | null    | seconds  | Duration of the track of chart


## Track statistics
```php
$track = new Track($filename);
$track->getStats();
```

```json
{
    channels: "1",
    dc_offset: "0.000016",
    min_level: "-0.162134",
    max_level: "0.153157",
    pk_lev: "-15.80",
    rms_lev: "-33.56",
    rms_pk: "-24.31",
    rms_tr: "-55.44",
    crest_factor: "7.72",
    flat_factor: "0.00",
    pk_count: "2",
    bit_depth: "30/32",
    length: "1.935601",
    scale_max: "1.000000",
    window: "0.050",
    samples: "42680",
    scaled_by: "2147483647.0",
    maximum_amplitude: "0.153157",
    minimum_amplitude: "-0.162134",
    midline_amplitude: "-0.004489",
    mean_norm: "0.010709",
    mean_amplitude: "0.000016",
    rms_amplitude: "0.020990",
    maximum_delta: "0.115579",
    minimum_delta: "0.000000",
    mean_delta: "0.003656",
    rms_delta: "0.008325",
    rough_frequency: "1391",
    volume_adjustment: "6.168",
    sample_rate: "22050"
}
```

## Convert track

Convert a track to a different format. Uses avconv (or ffmpeg).

```php
$track = new Track("sometrack.wav");
$track->convert("sometrack.mp3");
```


## Combine tracks

Combine two tracks. Uses [`sox --combine`](http://sox.sourceforge.net/sox.html#OPTIONS).

Available methods
 * concatenate
 * merge
 * mix
 * mix-power
 * multiply
 * sequence

```php
$track = new Track($track1);
$track->combine($method, $track2, $outputFilename);
```
