<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

class ImageComponent extends Component
{
    var $name = 'Image';
    private $file;
    private $image;
    private $info;

    public function prepare($file) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = array(
            	'width'  => $info[0],
            	'height' => $info[1],
            	'bits'   => $info['bits'],
            	'mime'   => $info['mime']
        	);
        	$this->image = $this->create($file);
    	
    }

    public function create($image) {
		$mime = $this->info['mime'];
		if ($mime == 'image/gif') {
			return imagecreatefromgif($image);
		} elseif ($mime == 'image/png') {
			return imagecreatefrompng($image);
		} elseif ($mime == 'image/jpeg') {
			return imagecreatefromjpeg($image);
		}
    }	

    public function save($file, $quality = 100) {
        $info = pathinfo($file);
        $extension = strtolower($info['extension']);
   
        if ($extension == 'jpeg' || $extension == 'jpg') {
            imagejpeg($this->image, $file, $quality);
        } elseif($extension == 'png') {
            imagepng($this->image, $file, 0);
        } elseif($extension == 'gif') {
            imagegif($this->image, $file);
        }
		   
	    imagedestroy($this->image);
    }	

    private function filter($filter) {
        imagefilter($this->image, $filter);
    }
            
    private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
		$rgb = $this->html2rgb($color);
        
		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
    }

    public function crop($width = 0, $height = 0, $left = 0, $top = 0) {
        $image_old = $this->image;
	$current_width = $this->info['width']; 
	$current_height = $this->info['height'];
	if($width+$left > $current_width){
		exit('Error: prekrocenie hranice (sirka) ' . '!');
	}

	if($height+$top > $current_height){
		exit('Error: prekrocenie hranice (vyska) ' . '!');
	}

        $this->image = imagecreatetruecolor($width, $height);
        
        imagecopy($this->image, $image_old, 0, 0, $left, $top, $current_width, $current_height);
        imagedestroy($image_old);
    }

    public function resize($width = 0, $height = 0,$r=255,$g=255,$b=255,$ratio=true) {
    	if (!$this->info['width'] || !$this->info['height']) {
			return;
		}

		$xpos = 0;
		$ypos = 0;

		$scale = min($width / $this->info['width'], $height / $this->info['height']);
		
		if ($scale == 1) {
			return;
		}
		
                if($ratio!=false)
                {
		$new_width = (int)($this->info['width'] * $scale);
		$new_height = (int)($this->info['height'] * $scale);			
    		$xpos = (int)(($width - $new_width) / 2);
   		$ypos = (int)(($height - $new_height) / 2);
        		        
       		$image_old = $this->image;
        	$this->image = imagecreatetruecolor($width, $height);
		
		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {		
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, $r, $g, $b, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, $r, $g, $b);
		}
		
		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
	
        imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);
           
        $this->info['width']  = $width;
        $this->info['height'] = $height;
                }
                else
                {
                    
                $new_width = (int)($width);
		$new_height = (int)($height);			
    		$xpos = (int)(($width - $new_width) / 2);
   		$ypos = (int)(($height - $new_height) / 2);
        		        
       	$image_old = $this->image;
        $this->image = imagecreatetruecolor($width, $height);
		
		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {		
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, $r, $g, $b, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, $r, $g, $b);
		}
		
		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
	
        imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);
           
        $this->info['width']  = $width;
        $this->info['height'] = $height;
                    
                }
        
        
    }
}

