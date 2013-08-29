<?php

namespace Jasny\Audio;

/**
 * Plot a waveform
 */
class Waveform
{
    /**
     * Input track
     * @var Track
     */
    protected $track;

    /**
     * Image width
     * @var int
     */
    public $width = 1800;

    /**
     * Image height
     * @var int
     */
    public $height = 280;

    /**
     * Color of the graph
     * @var string
     */
    public $color = '000001';

    /**
     * Color of x axis
     * @var string
     */
    public $axis = null;

    /**
     * The max amplitute (y axis)
     * @var float
     */
    public $level;
    

    /**
     * Audio samples
     * @var array
     */
    protected $samples;

    /**
     * The length of the graph in seconds (x axis)
     * @var float
     */
    protected $length;
    
    
    /**
     * Class constructor
     * 
     * @param Track|string $track     Input track
     * @param array        $settings  Associated array with settings
     */
    public function __construct($track, array $settings=array())
    {
        if (isset($settings['count']) && !isset($settings['width'])) $settings['width'] = $settings['count'];
        
        foreach ($settings as $key=>$value) {
            if (!property_exists($this, $key)) continue;
            $this->$key = $value;
        }
        
        $this->track = $track instanceof Track ? $track : new Track($track);
    }

    /**
     * Get the input audio track
     *
     * @return Track
     */
    public function getTrack()
    {
        return $this->track;
    }

    
    /**
     * Calculate the min and max sample per pixel using sox.
     * 
     * @return array
     */
    protected function calc()
    {
        if (!file_exists($this->track)) throw new \Exception("File '{$this->track}' doesn't exist");

        $sox = escapeshellcmd(Track::which('sox'));
        $track = escapeshellarg($this->track);
        $length = $this->track->getLength();

        $resample = '';
        $sample_count = $this->track->getSamples();
        
        // Downsample to max 500 samples per pixel with a minimum sample rate of 4k
        if ($sample_count / $this->width > 500) {
            $rate = max(floor(($this->width / $length) * 500), 4000);
            $resample = "-r $rate";
            $sample_count = $rate * $length;
        }
        
        $chunk_size = ceil($sample_count / $this->width);
        
        $descriptorspec = array(
           1 => array("pipe", "w"),  // stdout
           2 => array("pipe", "w")   // stderr
        );
        
        $handle = proc_open("$sox $track -t raw $resample -c 1 -e floating-point -L -", $descriptorspec, $pipes);
        if (!$handle) throw new \Exception("Failed to get the samples using sox");

        $chunk = array();
        $samples = array();
        
        while ($data = fread($pipes[1], 4 * $chunk_size)) {
            $chunk = unpack('f*', $data);
            $chunk[] = 0;
            $samples[] = min($chunk);
            $samples[] = max($chunk);
        };
        
        $err = stream_get_contents($pipes[2]);
        
        $ret = proc_close($handle);
        if ($ret != 0) throw new \Exception("Sox command failed. " . trim($err));

        $this->length = $length * ($this->width / count($samples));
        if (!isset($this->level)) $this->level = max(-1 * min($samples), max($samples));
        $this->samples = $samples;
    }

    /**
     * Get the samples.
     * Averaged to one sample per pixel.
     *
     * @return array
     */
    public function getSamples()
    {
        if (!isset($this->samples)) $this->calc();
        return $this->samples;
    }

    /**
     * Get the samples.
     * Averaged to one sample per pixel.
     *
     * @return array
     */
    public function getLength()
    {
        if (!isset($this->length)) $this->calc();
        return $this->length;
    }

    /**
     * Get the samples.
     * Averaged to one sample per pixel.
     *
     * @return array
     */
    public function getLevel()
    {
        if (!isset($this->level)) $this->calc();
        return $this->level;
    }

    
    /**
     * Plot the waveform
     *
     * @return resource
     */
    public function plot()
    {
        $this->getSamples();
        
        $im = imagecreatetruecolor($this->width, $this->height);
        imagecolortransparent($im, imagecolorallocate($im, 0, 0, 0));
        
        $center = ($this->height / 2);
        $scale = ($center / $this->level);
        $color = self::hex2color($im, $this->color);
        
        for ($i = 0, $n = count($this->samples); $i < $n-1; $i += 2) {
            $min = $center + ($this->samples[$i] * $scale);
            $max = $center + ($this->samples[$i+1] * $scale);
            
            imageline($im, $i / 2, $min, $i / 2, $max, $color);
        }
        
        if (!empty($this->axis)) {
            imageline($im, 0, $this->height / 2, $this->width, $this->height / 2, self::hex2color($im, $this->axis));
        }
        
        return $im;
    }

    /**
     * Create a gd color using a hexidecimal color notation
     *
     * @param resource $im
     * @param string   $color
     * @return resource
     */
    protected static function hex2color($im, $color)
    {
        $color = ltrim($color, '#');

        $red = hexdec(substr($color, 0, 2));
        $green = hexdec(substr($color, 2, 2));
        $blue = hexdec(substr($color, 4, 2));

        return imagecolorallocate($im, $red, $green, $blue);
    }


    /**
     * Output the generated waveform
     * 
     * @param string $format  Options: png or json
     */
    public function output($format='png')
    {
        $fn = "output$format";
        if (!method_exists($this, $fn)) throw new \Exception("Unknown format '$format'");
        
        $this->$fn();
    }
    
    /**
     * Output the generated waveform as PNG
     */
    protected function outputPng()
    {
        $im = $this->plot();
        
        header("X-Waveform-Length: {$this->length}");
        header("X-Waveform-Level: {$this->level}");
        header('Content-Type: image/png');
        imagepng($im);
    }
    
    /**
     * Output the generated waveform as JSON
     */
    protected function outputJson()
    {
        header('Content-Type: application/json');
        echo json_encode(array(
            'length'=>$this->getLength(),
            'level'=>$this->getLevel(),
            'samples'=>$this->getSamples()
        ));
    }
}
