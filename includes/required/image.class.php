<?php

/*!
  Copyright 2013 The Impact Plus. All rights reserved.

  YOU ARE PERMITTED TO:
  * Transfer the Software and license to another party if the other party agrees to accept the terms and conditions of this License Agreement. The license holder is responsible for a transfer fee of $50.95 USD. The license must be at least 90 days old or not transferred within the last 90 days;
  * Modify source codes of the software and add new functionality that does not violate the terms of the current license;
  * Customize the Software's design and operation to suit the internal needs of your web site except to the extent not permitted under this Agreement;
  * Create, sell and distribute applications/modules/plugins which interface (not derivative works) with the operation of the Software provided the said applications/modules/plugins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement;
  * Create, sell and distribute by any means any templates and/or designs/skins which allow you or other users of the Software to customize the appearance of Impact Plus provided the said templates and or designs/skins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement.

  YOU ARE "NOT" PERMITTED TO:
  * Use the Software in violation of any US/India or international law or regulation.
  * Permit other individuals to use the Software except under the terms listed above;
  * Reverse-engineer and/or disassemble the Software for distribution or usage outside your domain if it is not an unlimited licence version;
  * Use the Software in such as way as to condone or encourage terrorism, promote or provide pirated Software, or any other form of illegal or damaging activity;
  * Distribute individual copies of proprietary files, libraries, or other programming material in the Software package.
  * Distribute or modify proprietary graphics, HTML, or CSS packaged with the Software for use in applications other than the Software;
  * Use the Software in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Modify the software and/or create applications and modules which allow the Software to function in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Copy the Software and install that single program for simultaneous use on multiple machines without prior written consent from IMPACT PLUS;
*/

class ipImageManipulation {
  private $original_image;
  private $image;
  private $tmp_image;
  private $type;
  private $info;
  private $output_type;
  private $thumbnail;
  private $font_path;

  const IMAGE_TYPE_JPG  = 1;
  const IMAGE_TYPE_JPEG = 1;
  const IMAGE_TYPE_PNG  = 2;
  const IMAGE_TYPE_GIF  = 3;
  const TOP    = 1;
  const CENTER = 2;
  const BOTTOM = 3;
  const LEFT   = 4;
  const RIGHT  = 5;

  /**
   * ipImageManipulation::load()
   * 
   * @param mixed $file
   * @return
   */
  public function load( $file ) {
    $this->original_image  = $file;
    if ( extension_loaded( "gd" ) && function_exists( "imagecreatefrompng" ) ) {
      $this->info = $this->getImageInfo();
      switch( $this->info[2] ) {
        case IMAGETYPE_PNG:
          $this->type   = IMAGETYPE_PNG;
          $this->image  = imagecreatefrompng( $file );
          break;
        case IMAGETYPE_JPEG:
          $this->type   = IMAGETYPE_JPEG;
          $this->image  = imagecreatefromjpeg( $file );
          break;
        case IMAGETYPE_GIF:
          $this->type   = IMAGETYPE_GIF;
          $this->image  = imagecreatefromgif( $file );
          break;
      }
    }
  }

  /**
   * ipImageManipulation::loadFont()
   * 
   * @param mixed $path
   * @return
   */
  public function loadFont( $path ) {
    $this->font_path  = $path;
  }

  /**
   * ipImageManipulation::getX()
   * 
   * @return
   */
  public function getX() {
    if ( $this->image ) {
      $x = imagesx( $this->image );
      return $x;
    }
  }

  /**
   * ipImageManipulation::getY()
   * 
   * @return
   */
  public function getY() {
    if ( $this->image ) {
      $y = imagesy( $this->image );
      return $y;
    }
  }

  /**
   * ipImageManipulation::get_Color()
   * 
   * @param mixed $hex
   * @return
   */
  private function get_Color( $hex ) {
    $rgb = $this->hexToRGB( $hex );
    return imagecolorallocate( $this->image, $rgb[0], $rgb[1], $rgb[2] );
  }

  /**
   * ipImageManipulation::hexToRGB()
   * 
   * @param mixed $hex
   * @return
   */
  public function hexToRGB( $hex ) {
    $hex = strtoupper( $hex );
    $color_format_pattern = "/^\#?([A-F0-9]{2,6})$/";
    if( preg_match( $color_format_pattern, $hex, $arr ) ) {
      $rgb = $this->html2rgb( $arr[1] );
      return $rgb;
    } else {
      $this->parseError("Invalid Format of Hexadecimal Color");
    }
  }

  /**
   * ipImageManipulation::html2rgb()
   * 
   * @param mixed $color
   * @return
   */
  private function html2rgb( $color ) {
    switch( strlen( $color ) ) {
      case 2:
      case 5:
        $color .= "F";
				break;
      case 4:
        $color .= "FF";
        break;
      case 3:
      case 6:
        break;
      default:
        $color = "FFFFFF";
    }
    if ( $color[0] == '#' ) {
      $color = substr( $color, 1 );
    }
    if ( strlen( $color ) == 6 ) {
      list( $r, $g, $b )  = array( $color[0].$color[1], $color[2].$color[3], $color[4].$color[5] );
    }
    elseif ( strlen( $color ) == 3 ) {
      list( $r, $g, $b )  = array( $color[0].$color[0], $color[1].$color[1], $color[2].$color[2] );
    }
    else {
      return false;
    }
    $r  = hexdec( $r );
    $g  = hexdec( $g );
    $b  = hexdec( $b );
    return array( $r, $g, $b );
  }

  /**
   * ipImageManipulation::resize()
   * 
   * @param mixed $width
   * @param mixed $height
   * @return
   */
  public function resize( $width, $height ) {
    if( !is_numeric( $width ) || !is_numeric( $height ) || $width <= 0 || $height <= 0 ) {
      return false;
    } else {
      $this->tmp_image = imagecreatetruecolor( $width, $height );
      imagecopyresized( $this->tmp_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getX(), $this->getY() );
      $this->image = $this->tmp_image;
    }
  }

  /**
   * ipImageManipulation::resize_Width()
   * 
   * @param mixed $width
   * @return
   */
  public function resize_Width( $width ) {
    if ( $this->image ) {
      if( !is_numeric( $width ) || $width <= 0 ) {
        return false;
      } else {
        $x = $this->getX();
        $y = $this->getY();
        $w = $width;
        $h = floor( $w * ( $y / $x ) );
        $this->resize( $w, $h );
      }
    }
  }

  /**
   * ipImageManipulation::resize_Height()
   * 
   * @param mixed $height
   * @return
   */
  public function resize_Height( $height ) {
    if( !is_numeric( $height ) || $height <= 0 ) {
      return false;
    } else {
      $x = $this->getX();
      $y = $this->getY();
      $w = floor( $height * ( $x / $y ) );
      $h = $height;
      $this->resize( $w, $h );
    }
  }

  /**
   * ipImageManipulation::rotate()
   * 
   * @param mixed $degree
   * @param mixed $bg
   * @return
   */
  public function rotate( $degree, $bg = null ) {
    $degree = ( $degree < 0 ) ? pow( $degree, 2 ) : $degree;
    $degree %= 361;
    $bg     = $this->get_Color( ( $bg ) ? $bg : "#fff");
    $resampled = imagecreatetruecolor( $this->getX(), $this->getY() );
    $this->image = imagerotate( $this->image, -$degree, $bg );
  }

  /**
   * ipImageManipulation::rotate_Left()
   * 
   * @return
   */
  public function rotate_Left() {
    $this->rotate( 90 );
  }

  /**
   * ipImageManipulation::rotate_Right()
   * 
   * @return
   */
  public function rotate_Right() {
    $this->rotate( 270 );
  }

  /**
   * ipImageManipulation::brightness()
   * 
   * @param integer $value
   * @return
   */
  public function brightness( $value = 0 ) {
    if( int( $value ) ) {
      imagefilter( $this->image, IMG_FILTER_BRIGHTNESS, $value );
    }
  }

  /**
   * ipImageManipulation::contrast()
   * 
   * @param integer $value
   * @return
   */
  public function contrast( $value = 0 ) {
    if( int( $value ) ) {
      imagefilter( $this->image, IMG_FILTER_CONTRAST, $value );
    }
  }

  /**
   * ipImageManipulation::add_Grayscale()
   * 
   * @return
   */
  public function add_Grayscale() {
    if( $this->image ) {
      imagefilter( $this->image, IMG_FILTER_GRAYSCALE );
    }
  }

  /**
   * ipImageManipulation::add_Sketch()
   * 
   * @return
   */
  public function add_Sketch() {
    if( $this->image ) {
      imagefilter( $this->image, IMG_FILTER_MEAN_REMOVAL );
    }
  }

  /**
   * ipImageManipulation::add_Embose()
   * 
   * @return
   */
  public function add_Embose() {
    if( $this->image ) {
      imagefilter( $this->image, IMG_FILTER_EMBOSS );
    }
  }

  /**
   * ipImageManipulation::add_Negate()
   * 
   * @return
   */
  public function add_Negate() {
    if( $this->image ) {
      imagefilter( $this->image, IMG_FILTER_NEGATE );
    }
  }

  /**
   * ipImageManipulation::add_Blur()
   * 
   * @param bool $gausian
   * @return
   */
  public function add_Blur( $gausian = false ) {
    if( !empty( $this->image ) ) {
      imagefilter($this->image, ( $gausian ) ? IMG_FILTER_GAUSSIAN_BLUR : IMG_FILTER_SELECTIVE_BLUR );
    }
  }

  /**
   * ipImageManipulation::add_GaussianBlur()
   * 
   * @return
   */
  public function add_GaussianBlur() {
    $this->addBlur( true );
  }

  /**
   * ipImageManipulation::createThumbnail()
   * 
   * @param mixed $width
   * @param mixed $height
   * @return
   */
  public function createThumbnail( $width, $height ) {
    $this->resize( $width, $height );
    $this->thumbnail  = pathinfo( $this->original_image, PATHINFO_DIRNAME ).DIRECTORY_SEPARATOR."thumb_".pathinfo( $this->original_image, PATHINFO_FILENAME ).'.'.pathinfo( $this->original_image, PATHINFO_EXTENSION );
    $this->save( $this->thumbnail );
  }

  /**
   * ipImageManipulation::getThumbnail()
   * 
   * @param mixed $width
   * @param mixed $height
   * @return
   */
  public function getThumbnail( $width, $height ) {
    return $this->thumbnail;
  }

  /**
   * ipImageManipulation::add_Watermark()
   * 
   * @param mixed $text
   * @param integer $vertical_position
   * @param integer $horizontal_position
   * @param integer $font_size
   * @param string $fontcolor
   * @param integer $angle
   * @param integer $margin
   * @return
   */
  public function add_Watermark( $text, $vertical_position = 2, $horizontal_position = 2, $font_size = 12, $fontcolor = "#ffffff", $angle = 0, $margin = 5 ) {
    if ( empty( $this->image ) ) {
      return false;
    }
    $font = $this->font_path;
    $valid_vpositions = array( self::TOP, self::CENTER, self::BOTTOM );
    $valid_hpositions = array( self::LEFT, self::CENTER, self::RIGHT );
			
    if( !int( $font_size ) || $font_size <= 0 ) {
      $this->parseError( "Invalid Font size!" );
      return;
    }
    elseif( !int( $angle ) ) {
      $this->parseError( "Invalid Angle!" );
      return;
    }
    elseif( !int( $margin ) || $margin < 0 ) {
      $this->parseError( "Invalid Margin!" );
      return;
    }
    else if( !$this->hexToRGB( $fontcolor ) ) {
      $this->parseError( "Image Corrupted!" );
      return;
    }
			
    if( in_array( $vertical_position, $valid_vpositions, true ) && in_array( $horizontal_position, $valid_hpositions, true ) ) {
      $x_padd = 0;
      $y_padd = 0;
      $watermark  = @imagettfbbox( $font_size, $angle, $font, $text );
      if( !$watermark ) {
        $this->parseError( "Failed to find font files!" );
      }
      $w_width  = abs( $watermark[2] - $watermark[0] );
      $w_height = abs( $watermark[7] - $watermark[1] );
      $im_width = $this->getX();
      $im_height = $this->getY();

      if( $horizontal_position == self::LEFT ) {
        $x_padd = $margin;
      }
      elseif( $horizontal_position == self::CENTER ) {
        $x_padd = floor($im_width/2) - floor($w_width/2);
      }
      else {
        $x_padd = $im_width - $w_width - $margin;
      }

      if( $vertical_position == self::CENTER ) {
        $y_padd = floor($im_height/2) - floor($w_height/2);
      }
      else if( $vertical_position == self::BOTTOM ) {
        $y_padd = $im_height - $margin;
      }
      else {
        $y_padd += floor( $w_height ) + $margin;
      }
      imagettftext( $this->image, $font_size, $angle, $x_padd+1, $y_padd+1, $this->get_Color( "#000" ), $font, $text );
      imagettftext( $this->image, $font_size, $angle, $x_padd, $y_padd, $this->get_Color( $fontcolor ), $font, $text );
    }
    else {
      $this->parseError( "Invalid Text Position!" );
    }
  }

  /**
   * ipImageManipulation::save()
   * 
   * @param mixed $to_file
   * @param integer $quality
   * @return
   */
  public function save( $to_file, $quality = 90 ) {
    if ( !file_exists( $to_file ) ) {
      return false;
    }
    if( !is_numeric( $quality ) || $quality < 0 || $quality > 100 ) {
      $quality = 90;
    }
    switch( $this->type ) {
      case IMAGETYPE_PNG:
        imagepng( $this->image, $to_file );
        break;
      case IMAGETYPE_JPEG:
        imagejpeg( $this->image, $to_file, $quality );
      case IMAGETYPE_GIF:
        imagegif( $this->image, $to_file );
        break;
    }
  }

  /**
   * ipImageManipulation::output()
   * 
   * @param integer $quality
   * @return
   */
  public function output( $quality = 90 ) {
    if ( $this->image ) {
      if( !is_numeric( $quality ) || $quality < 0 || $quality > 100 ) {
        $quality = 90;
      }
      switch( $this->type ) {
        case IMAGETYPE_PNG:
          imagepng( $this->image );
          break;
        case IMAGETYPE_JPEG:
          imagejpeg( $this->image, null, $quality );
        case IMAGETYPE_GIF:
          imagegif( $this->image );
          break;
      }
    } else {
      echo file_get_contents( $this->original_image );
    }
  }

  /**
   * ipImageManipulation::destroy()
   * 
   * @return
   */
  public function destroy() {
    if ( !empty( $this->image ) ) {
      imagedestroy( $this->image );
    }
  }

  /**
   * ipImageManipulation::getImageInfo()
   * 
   * @return
   */
  public function getImageInfo() {
    $info = @getimagesize( $this->original_image );
    return ( !$info ) ? $this->parseError( "Only images are allowed" ) : $info;
  }

  /**
   * ipImageManipulation::parseError()
   * 
   * @param mixed $error_msg
   * @return
   */
  private function parseError( $error_msg ) {
    $word_wrap  = wordwrap( strip_tags( $error_msg ), 40, "\n" );
    $exp        = explode( "\n", $word_wrap );
    $img_height = count( $exp ) * 30;
    $img_width  = 400;
    $tmp_im     = imagecreatetruecolor( $img_width, $img_height );
    $color      = imagecolorallocate( $tmp_im, 255, 255, 255 );
    for( $i=0; $i < count( $exp ); $i++ ) {
      imagestring( $tmp_im, 5, 5, 6+($i)*30, $exp[$i], $color );
    }
    imagejpeg( $tmp_im );
    exit();
  }
}
?>