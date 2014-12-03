<?php

/**
 * @author bystwn22
 * @copyright 2014
 */

header( "Content-Type: text/plain" );
$file = realpath( dirname( __FILE__ )."/file_info.json" );
if ( $file && is_readable( $file ) ) {
  $finfo  = json_decode( implode( "", file( $file ) ), true );
  /*$finfo["vector"]  = array();
  $finfo["vector"]["icon"]  = array(
    "def" =>  "ai.png",
    "cdr" =>  "cdr.png"
  );
  $finfo["vector"]["info"]  = "As with all media formats, video formats run the spectrum between high quality and low file size. Lossless compression for video files attempts to reduce the file size by removing redundancies. Lossy compression schemes reduce filesize by discarding data without the viewer noticing. The Advanced Video Coding (AVC) standard is one of the most commonly used formats for recording, compressing, and distributing high definition video. File extensions for files produced by AVC are .mp4 and .m4v.";
  $finfo["vector"]["apps"]  = array(
    array(
      "icon"  =>  "http://totallynoob.com/wp-content/uploads/2012/11/vlc_1.png",
      "name"  =>  "Adobe Illustrator",
      "link"  =>  "http://www.adobe.com/",
    ),
    array(
      "icon"  =>  "http://totallynoob.com/wp-content/uploads/2012/11/vlc_1.png",
      "name"  =>  "CorelDraw®",
      "link"  =>  "http://www.adobe.com/",
    )
  );
  file_put_contents( $file, json_encode( $finfo ) );*/
  print_r( $finfo );
}
?>