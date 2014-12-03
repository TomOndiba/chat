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

/**
 * ipStreamParser
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipStreamParser {
  private $uri    = false;
  private $data   = false;
  private $find   = false;
  private $meta   = false;
  private $images = false;

  /**
   * ipStreamParser::__construct()
   * 
   * @param bool $uri
   * @param bool $crawl
   * @return
   */
  function __construct( $uri = false, $crawl = false ) {
    $this->set_url( $uri );
    $_SESSION["streams"]  = ( isset( $_SESSION["streams"] ) ) ? $_SESSION["streams"] : array();
  }

  /**
   * ipStreamParser::set_url()
   * 
   * @param bool $uri
   * @return
   */
  public function set_url( $uri = false ) {
    $this->uri  = ( $uri ) ? trim( $uri ) : $this->uri;
  }

  /**
   * ipStreamParser::crawl()
   * 
   * @param bool $uri
   * @return
   */
  public function crawl( $uri = false ) {
    $this->set_url( $uri );
    if ( !$this->uri ) {
      return false;
    }

    if ( $this->request() ) {
      if ( $this->parse() ) {
        $this->format();
      }
    }

    return $this->meta;
  }

  /**
   * ipStreamParser::request()
   * 
   * @return
   */
  private function request() {
    if ( isset( $_SESSION["streams"][$this->uri] ) ) {
      $this->data = $_SESSION["streams"][$this->uri];
      $this->find = new ipStreamTokens( $this->data, $this->uri );
      return true;
    }
    loadClass( "ipCurl", "curl/curl.class.php" );
    $curl = new ipCurl( $this->uri );
    $curl->createCurl();
    $html = trim( $curl->getResponse() );
    if ( $curl->getError() === 0 && $curl->getStatus() === 200 && $html ) {
      $this->data = $html;
      $this->find = new ipStreamTokens( $this->data, $this->uri );
      $_SESSION["streams"][$this->uri]  = $this->data;
      return true;
    }
    return false;
  }

  /**
   * ipStreamParser::parse()
   * 
   * @return
   */
  private function parse() {
    if ( !$this->data ) {
      return false;
    }
    $this->meta = (object)array();
    $this->meta->target   = $this->uri;
    $this->meta->images   = $this->find->images();
    $this->meta->thumb    = ( $this->meta->images && isset( $this->meta->images[0] ) ) ? $this->meta->images[0] : false;
    $this->meta->title    = $this->find->title();
    $this->meta->subtitle = parse_url( $this->uri, PHP_URL_HOST );
    $this->meta->summary  = $this->find->summary();
  }

  /**
   * ipStreamParser::format()
   * 
   * @return
   */
  private function format() {
    if ( !$this->meta ) {
      return false;
    }
  }
}

/**
 * ipStreamTokens
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipStreamTokens {
  private $data = false;
  private $back = false;
  private $meta = false;
  private $href = false;
  private $host = false;

  private $ammu = false;

  /**
   * ipStreamTokens::__construct()
   * 
   * @param mixed $data
   * @param mixed $href
   * @return
   */
  public function __construct( $data = null, $href = null ) {
    loadClass( "Tokenizer_Base", "required/ganon.class.php" );
    $this->back = $data;
    $this->data = $data = str_get_dom( $data );
    $this->meta = (object)array();

    $this->href = $href;
    /*if ( $redirect = $data( "meta[http-equiv='refresh']", 0 ) ) {
      $target   = $redirect->content;
      $redirect = trim( substr_replace( $target, "", 0, ( stripos( $target, "url=" ) + 4 ) ) );
      if ( $redirect ) {
        $this->href = $this->cannonical_image_url( $redirect );
      }
    }*/

    $this->host = strtolower( parse_url( $this->href, PHP_URL_HOST ) );
    $this->ammu = new ipAdvStreamParser( $this->data, $this->meta, $this->href );

    $this->advanced_parse();
  }

  /**
   * ipStreamTokens::advanced_parse()
   * 
   * @return
   */
  private function advanced_parse() {
    if ( stristr( $this->host, "soundcloud" ) !== false ) {
      $this->ammu->soundcloud();
    }
    else if ( stristr( $this->host, "youtube" ) !== false ) {
      $this->ammu->youtube();
    }
  }

  /**
   * ipStreamTokens::title()
   * 
   * @return
   */
  public function title() {
    if ( isset( $this->meta->title ) ) {
      return $this->meta->title;
    }
    if ( $title = $this->select( "title", 0 ) ) {
      return ( $this->meta->title = trim( $title->getPlainText() ) );
    }
    return null;
  }

  /**
   * ipStreamTokens::summary()
   * 
   * @return
   */
  public function summary() {
    if ( isset( $this->meta->summary ) ) {
      return $this->meta->summary;
    }
    $this->meta->summary  = null;

    $desc1  = $this->select( "meta[name='description']", 0 );
    $desc2  = $this->select( "meta[property='og:description']", 0 );

    if ( $desc1 && strlen( trim( $desc1->content ) ) > 150 ) {
      $this->meta->summary  = trim( $desc1->content );
    }
    elseif ( $desc2 && strlen( trim( $desc2->content ) ) > 150 ) {
      $this->meta->summary  = trim( $desc2->content );
    }
    else {
      $texts  = array();
      if ( $blocks = $this->select( "p" ) ) {
        foreach( $blocks as $block ) {
          $block  = trim( $block->getPlainTextUTF8() );
          $length = strlen( $block );
          $texts[$length] = $block;
        }
        ksort( $texts );
        $texts  = array_values( array_unique( array_filter( $texts ) ) );
        end( $texts );
        $this->meta->summary  = current( $texts );
      }
    }
    if ( !$this->meta->summary && $desc1 ) {
      $this->meta->summary  = trim( $desc1->content );
    }

    return $this->meta->summary;
  }

  /**
   * ipStreamTokens::images()
   * 
   * @return
   */
  public function images() {
    if ( isset( $this->meta->images ) ) {
      return $this->meta->images;
    }
    $this->meta->images = false;
    $host = parse_url( $this->href, PHP_URL_HOST );

    $islfb  = ( stristr( $host, "mbasic.facebook.com" ) !== false );
    $ismfb  = ( stristr( $host, "m.facebook.com" ) !== false );
    $isdfb  = ( ( !$islfb && !$ismfb ) && stristr( $host, "facebook.com" ) !== false );
    $isfb   = ( $islfb || $ismfb || $isdfb );
    
    $isfb_photo = ( $isfb && preg_match( "/\/photo\.php/", $this->href ) );

    if ( !$isfb ) {
      if ( $images = $this->select( "meta[property='og:image']" ) ) {
        $this->meta->images = $this->parse_images( $images, "content", true );
      }
      elseif ( $images = $this->select( "meta[name='og:image']" ) ) {
        $this->meta->images = $this->parse_images( $images, "content", true );
      }
      elseif ( $images = $this->select( "meta[itemprop*='image']" ) ) {
        $this->meta->images = $this->parse_images( $images, "content", true, "image:itemprop" );
      }
      elseif ( $images = $this->select( "link[rel*='icon']" ) ) {
        $this->meta->images = $this->parse_images( $images, "href", true );
      }
      elseif ( $images = $this->select( "img" ) ) {
        $this->meta->images = $this->parse_images( $images, "src", false );
      }
    }
    else {
      if ( $ismfb || $islfb ) {
        if ( $isfb_photo ) {
          if ( $photo = $this->select( "div.acbk img[alt='Photo']", 0 ) ) {
            $this->meta->images = array( $photo->src );
            if ( $summary = $this->select( ".acw .msg", 0 ) ) {
              if ( $actor = $summary->select( "a.actor-link", 0 ) ) {
                $this->meta->title  = trim( $actor->getPlainTextUTF8() )."'s Photo";
                $actor->delete();
                unset( $actor );
              }
              if ( $fcg = $summary->select( ".fcg", 0 ) ) {
                $fcg->delete();
                unset( $fcg );
              }
              $this->meta->summary  = trim( $summary->getPlainTextUTF8() );
              unset( $summary );
            }
            unset( $photo, $isfb_photo, $ismfb, $islfb, $isdfb, $isfb );
          }
          elseif ( $photo = $this->select( "._57-t i.img", 0 ) ) {
            preg_match( '!http://[^?#]+\.(?:jpe?g|png|gif)!Ui', $photo->style, $matches );
            preg_match_all( "/require\(\"MRenderingScheduler\"\)\.schedule\((.*)\, function(.*)\)/i", $this->back, $summaries );
            if ( $matches && isset( $matches[0] ) ) {
              $this->meta->images = array( $matches[0] );
              unset( $matches, $photo, $isfb_photo, $ismfb, $islfb, $isdfb, $isfb );
            }
            if ( isset( $summaries[1] ) && isset( $summaries[1][0] ) ) {
              $summaries  = json_decode( $summaries[1][0] );
              if ( isset( $summaries->content ) && isset( $summaries->content->__html ) ) {
                $content  = str_get_dom( $summaries->content->__html );
                if ( $summary = $content( ".voice .msg", 0 ) ) {
                  if ( $actor = $summary( "a.actor-link", 0 ) ) {
                    $this->meta->title  = trim( $actor->getPlainTextUTF8() )."'s Photo";
                    $actor->delete();
                    unset( $actor );
                  }
                  if ( $fcg = $summary( ".fcg", 0 ) ) {
                    $fcg->delete();
                    unset( $fcg );
                  }
                  $this->meta->summary  = trim( $summary->getPlainTextUTF8() );
                  unset( $summary );
                }
                unset( $content );
              }
              unset( $summaries );
            }
          }
        }
        elseif ( $images = $this->select( "img" ) ) {
          $this->meta->images = $this->parse_images( $images, "src", false );
        }
        else {
          
        }
      }
    }
    /*if ( ( $images = $this->_html->find( "meta[property='og:image']" ) ) && !$isfb ) {
      $this->parse_images( $images, "content", true );
    }
    if ( $images = $this->select( "img[src]" ) ) {
      $this->meta->images = array();
      foreach( $images as $image ) {
        $this->meta->images[] = $image->src;
      }
    }*/

    return $this->meta->images;
  }

  /**
   * ipStreamTokens::parse_images()
   * 
   * @param mixed $images
   * @param string $attr
   * @param bool $no_check
   * @return
   */
  private function parse_images( $images = array(), $attr = "src", $no_check = false ) {
    $list = array();
    if ( !$images ) {
      return false;
    }
    loadClass( "ipCurl", "curl/curl.class.php" );
    $curl = new ipCurl();
    $curl->excludeBody()->includeHeader()->setBinaryTransfer( true );
    $default  = ( isset( $images[0] ) && isset( $images[0]->{$attr} ) ) ? $this->cannonical_image_url( $images[0]->{$attr} ) : null;
    $i  = 0;
    foreach( $images as $image ) {
      if ( $i == 1 ) {
        break;
      }
      if ( isset( $image->{$attr} ) && !empty( $image->{$attr} ) ) {
        $src  = $this->cannonical_image_url( $image->{$attr} );
        if ( !in_array( $src, $list ) ) {
          if ( $no_check ) {
            $list[] = $src;
            $i++;
            continue;
          }
          if ( (int)$image->width > 80 && (int)$image->height > 15 ) {
            $list[] = $src;
            $i++;
          }
          else {
            list( $width, $height ) = @getimagesize( $src );
            if ( $width > 80 && $height > 15 ) {
              $list[] = $src;
              $i++;
            }
          }
        }
      }
    }
    if ( empty( $list ) && $default ) {
      $list[] = $default;
    }
    return $list;
  }

  /**
   * ipStreamTokens::cannonical_image_url()
   * 
   * @param mixed $src
   * @return
   */
  private function cannonical_image_url( $src = null ) {
    $parse  = parse_url( $src );
    $paths  = substr_count( $src, "../" );

    if ( !isset( $parse["scheme"] ) ) {
      if ( substr( $src, 0, 2 ) == "//" ) {
        $src  = "http:".$src;
      }
      elseif ( substr( $src, 0, 1 ) == "/" ) {
        $src  = $this->get_server_uri().substr( $src, 1, strlen( $src ) );
      }
      elseif ( substr( $src, 0, 2 ) == "./" && !$paths ) {
        $explode  = explode( "/", $this->href );
        if ( trim( $explode[count( $explode ) - 1] ) == "" ) {
          $src  = $this->href.substr( $src, 2, strlen( $src ) );
        }
        else {
          $src  = $this->get_parenturi().substr( $src, 2, strlen( $src ) );
        }
      }
      elseif ( $paths > 0 ) {
        $src  = str_ireplace( "../", "", $src );
        $src  = str_ireplace( "./", "", $src );
        $src  = str_ireplace( " ", "%20", $src );
        $src  = $this->get_parenturi( $paths ).$src;
      }
      else {
        if ( basename( $this->href ) == parse_url( $this->href, PHP_URL_HOST ) ) {
          $src  = $this->href.$src;
        }
        else {
          $explode  = explode( "/", $this->href );
          if ( trim( $explode[count( $explode ) - 1] ) == "" ) {
            $src  = str_ireplace( "./", "", $src );
            $src  = $this->href.$src;
          }
          else {
            $src  = str_ireplace( "./", "", $src );
            $src  = $this->get_parenturi().$src;
          }
        }
      }
    }
    return trim( $src );
  }

  /**
   * ipStreamTokens::get_server_uri()
   * 
   * @return
   */
  private function get_server_uri() {
    $parsed = parse_url( $this->href );
    $server = array();
    if ( isset( $parsed["scheme"] ) && !empty( $parsed["scheme"] ) ) {
      $server[] = $parsed["scheme"]."://";
    }
    if ( ( isset( $parsed["user"] ) && !empty( $parsed["user"] ) ) && isset( $parsed["pass"] ) && !empty( $parsed["pass"] ) ) {
      $server[] = $parsed["user"].":".$parsed["pass"]."@";
    }
    if ( isset( $parsed["host"] ) && !empty( $parsed["host"] ) ) {
      $server[] = $parsed["host"]."/";
    }
    return implode( "", $server );
  }
  /**
   * ipStreamTokens::get_parenturi()
   * 
   * @param integer $up
   * @return
   */
  private function get_parenturi( $up = 0 ) {
    if ( $up == 0 ) {
      return dirname( $this->href )."/";
    }
    $link = $this->href;
    for( $i = 0; $i < $up; $i++ ) {
      $link = dirname( $link );
    }
    return $link."/";
  }

  /**
   * ipStreamTokens::css_image()
   * 
   * @param mixed $content
   * @return
   */
  private function css_image( $content = null ) {
    preg_match( '!http://[^?#]+\.(?:jpe?g|png|gif)!Ui', $content, $matches );
    if ( $matches && isset( $matches[0] ) ) {
      return $matches[0];
    }
    return false;
  }

  /**
   * ipStreamTokens::select()
   * 
   * @param mixed $selector
   * @param bool $index
   * @return
   */
  private function select( $selector = null, $index = false ) {
    $data = $this->data;
    return $data( $selector, $index );
  }
}

/**
 * ipAdvStreamParser
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipAdvStreamParser {
  private $dom  = false;
  private $meta = false;
  private $data = array(
    "title"     =>  false,
    "images"    =>  false,
    "thumb"     =>  false,
    "summary"   =>  false,
    "video"     =>  false
  );
  private $href = false;

  /**
   * ipAdvStreamParser::__construct()
   * 
   * @param mixed $dom
   * @param mixed $meta
   * @param mixed $href
   * @return
   */
  function __construct( &$dom, &$meta, $href ) {
    $this->dom  = $dom;
    $this->meta = $meta;
    $this->data = (object)$this->data;
    $this->href = $href;
  }

  /**
   * ipAdvStreamParser::soundcloud()
   * 
   * @return
   */
  public function soundcloud() {
    $this->meta = $this->data;
  }

  /**
   * ipAdvStreamParser::youtube()
   * 
   * @return
   */
  public function youtube() {
    $query  = parse_url( $this->href, PHP_URL_QUERY );
    parse_str( $query, $param );
    if ( isset( $param["v"] ) ) {
      $this->meta->images = array( 'http://i1.ytimg.com/vi/'.$param["v"].'/hqdefault.jpg' );
      $this->meta->thumb  = 'http://i1.ytimg.com/vi/'.$param["v"].'/hqdefault.jpg';
    }
    $this->meta = $this->data;
  }
}
?>