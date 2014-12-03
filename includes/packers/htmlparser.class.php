<?php
define( 'HDOM_TYPE_ELEMENT', 1);
define( 'HDOM_TYPE_COMMENT', 2);
define( 'HDOM_TYPE_TEXT',    3);
define( 'HDOM_TYPE_ENDTAG',  4);
define( 'HDOM_TYPE_ROOT',    5);
define( 'HDOM_TYPE_UNKNOWN', 6);
define( 'HDOM_QUOTE_DOUBLE', 0);
define( 'HDOM_QUOTE_SINGLE', 1);
define( 'HDOM_QUOTE_NO',     3);
define( 'HDOM_INFO_BEGIN',   0);
define( 'HDOM_INFO_END',     1);
define( 'HDOM_INFO_QUOTE',   2);
define( 'HDOM_INFO_SPACE',   3);
define( 'HDOM_INFO_TEXT',    4);
define( 'HDOM_INFO_INNER',   5);
define( 'HDOM_INFO_OUTER',   6);
define( 'HDOM_INFO_ENDSPACE',7);
define( 'DEFAULT_TARGET_CHARSET', 'UTF-8');
define( 'DEFAULT_BR_TEXT', "\r\n");

/**
 * file_get_html()
 * Get html dom from file
 * @param string $url
 * @param bool $use_include_path
 * @param mixed $context
 * @param integer $offset
 * @param integer $maxLen
 * @param bool $lowercase
 * @param bool $forceTagsClosed
 * @param mixed $target_charset
 * @param bool $stripRN
 * @param mixed $defaultBRText
 * @return html dom
*/
function file_get_html( $url, $use_include_path = false, $context = null, $offset = -1, $maxLen = -1, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT ) {
  $dom  = new simple_html_dom( null, $lowercase, $forceTagsClosed, $target_charset, $defaultBRText );
  $contents = file_get_contents( $url, $use_include_path, $context, $offset );
  if ( empty( $contents ) ) {
    return false;
  }
  $dom->load( $contents, $lowercase, $stripRN );
  return $dom;
}

/**
 * str_get_html()
 * Get html dom from string
 * @param string $str
 * @param bool $lowercase
 * @param bool $forceTagsClosed
 * @param mixed $target_charset
 * @param bool $stripRN
 * @param mixed $defaultBRText
 * @return html dom
*/
function str_get_html( $str, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT ) {
  $dom  = new simple_html_dom( null, $lowercase, $forceTagsClosed, $target_charset, $defaultBRText );
  if ( empty( $str ) ) {
    $dom->clear();
    return false;
  }
  $dom->load( $str, $lowercase, $stripRN );
  return $dom;
}

/**
 * dump_html_tree()
 * Dump html dom tree
 * @param mixed $node
 * @param bool $show_attr
 * @param integer $deep
 * @return void
*/
function dump_html_tree( $node, $show_attr = true, $deep = 0 ) {
  $node->dump( $node );
}

/**
 * simple_html_dom_node
 * Added ability for "find" routine to lowercase the value of the selector.
 * Added $tag_start to track the start position of the tag in the total byte index
 * @package PlaceLocalInclude
 * @author bystwn22
 * @copyright 2012
 * @version 2.3
 * @access public
*/
class simple_html_dom_node {
  public $nodetype  = HDOM_TYPE_TEXT;
  public $tag       = "text";
  public $attr      = array();
  public $children  = array();
  public $nodes     = array();
  public $parent    = null;
  public $_         = array();
  public $tag_start = 0;
  private $dom      = null;

  /**
   * simple_html_dom_node::__construct()
   * @param mixed $dom
   * @return void
  */
  function __construct( $dom ) {
    $this->dom = $dom;
    $dom->nodes[] = $this;
  }

  /**
   * simple_html_dom_node::__destruct()
   * Clean up memory due to php5 circular references memory leak...
   * @return void
  */
  function __destruct() {
    $this->clear();
  }

  /**
   * simple_html_dom_node::__toString()
   * Get outer text of html element
   * @return outer text
  */
  function __toString() {
    return $this->outertext();
  }

  /**
   * simple_html_dom_node::clear()
   * Clean up memory due to php5 circular references memory leak...
   * @return void
   */
  function clear() {
    $this->dom      = null;
    $this->nodes    = null;
    $this->parent   = null;
    $this->children = null;
  }

  /**
   * simple_html_dom_node::dump()
   * Dump node's tree
   * @param bool $show_attr
   * @param integer $deep
   * @return void
  */
  function dump( $show_attr = true, $deep = 0 ) {
    $lead = str_repeat( '    ', $deep );
    echo $lead.$this->tag;
    if ( $show_attr && count( $this->attr ) > 0 ) {
      echo '(';
      foreach( $this->attr as $k => $v ) {
        echo "[$k]=>\"".$this->$k.'", ';
      }
      echo ')';
    }
    echo "\n";
    foreach( $this->nodes as $c ) {
      $c->dump( $show_attr, $deep+1 );
    }
  }

  /**
   * simple_html_dom_node::dump_node()
   * Debugging function to dump a single dom node with a bunch of information about it.
   * @return void
  */
  function dump_node() {
    echo $this->tag;
    if ( count( $this->attr ) > 0 ) {
      echo '(';
      foreach( $this->attr as $k => $v ) {
        echo "[$k]=>\"".$this->$k.'", ';
      }
      echo ')';
    }
    if ( count( $this->attr ) > 0 ) {
      echo ' $_ (';
      foreach ( $this->_ as $k => $v ) {
        if ( is_array( $v ) ) {
          echo "[$k]=>(";
          foreach( $v as $k2 => $v2 ) {
            echo "[$k2]=>\"".$v2.'", ';
          }
          echo ")";
        }
        else {
          echo "[$k]=>\"".$v.'", ';
        }
      }
      echo ")";
    }
    if ( isset( $this->text ) ) {
      echo " text: (".$this->text .")";
    }
    echo " children: ".count( $this->children );
    echo " nodes: ".count( $this->nodes );
    echo " tag_start: ".$this->tag_start;
    echo "\n";
  }

  /**
   * simple_html_dom_node::parent()
   * Returns the parent of node
   * @return parent of node
  */
  function parent() {
    return $this->parent;
  }

  /**
   * simple_html_dom_node::children()
   * Returns children of node
   * @param integer $idx
   * @return children of node
  */
  function children( $idx = -1 ) {
    if ( $idx === -1 ) {
      return $this->children;
    }
    if ( isset( $this->children[$idx] ) ) {
      return $this->children[$idx];
    }
    return null;
  }

  /**
   * simple_html_dom_node::first_child()
   * Returns the first child of node
   * @return first child of node
  */
  function first_child() {
    if ( count( $this->children ) > 0 ) {
      return $this->children[0];
    }
    return null;
  }

  /**
   * simple_html_dom_node::last_child()
   * Returns the last child of node
   * @return last child of node
   */
  function last_child() {
    if ( ( $count = count( $this->children ) ) > 0 ) {
      return $this->children[$count-1];
    }
    return null;
  }

  /**
   * simple_html_dom_node::next_sibling()
   * Returns the next sibling of node
   * @return next sibling of node
   */
  function next_sibling() {
    if ( $this->parent === null ) {
      return null;
    }
    $idx    = 0;
    $count  = count( $this->parent->children );
    while( $idx < $count && $this !== $this->parent->children[$idx] ) {
      ++$idx;
    }
    if ( ++$idx >= $count ) {
      return null;
    }
    return $this->parent->children[$idx];
  }

  /**
   * simple_html_dom_node::prev_sibling()
   * Returns the previous sibling of node
   * @return previous sibling of node
  */
  function prev_sibling() {
    if ( $this->parent === null ) {
      return null;
    }
    $idx    = 0;
    $count  = count( $this->parent->children );
    while( $idx < $count && $this !== $this->parent->children[$idx] ) {
      ++$idx;
    }
    if ( --$idx < 0 ) {
      return null;
    }
    return $this->parent->children[$idx];
  }

  /**
   * simple_html_dom_node::find_ancestor_tag()
   * Locate a specific ancestor tag in the path to the root.
   * @param mixed $tag
   * @return a specific ancestor tag
  */
  function find_ancestor_tag( $tag ) {
    global $debugObject;
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLogEntry(1);
    }
    $returnDom = $this;
    while( !is_null( $returnDom ) ) {
      if ( is_object( $debugObject ) ) {
        $debugObject->debugLog( 2, "Current tag is:".$returnDom->tag );
      }
      if ( $returnDom->tag == $tag ) {
        break;
      }
      $returnDom  = $returnDom->parent;
    }
    return $returnDom;
  }

  /**
   * simple_html_dom_node::innertext()
   * Get dom node's inner html
   * @return inner html
   */
  function innertext() {
    if ( isset( $this->_[HDOM_INFO_INNER] ) ) {
      return $this->_[HDOM_INFO_INNER];
    }
    if ( isset( $this->_[HDOM_INFO_TEXT] ) ) {
      return $this->dom->restore_noise( $this->_[HDOM_INFO_TEXT] );
    }
    $ret  = "";
    foreach( $this->nodes as $n ) {
      $ret  .=  $n->outertext();
    }
    return $ret;
  }

  /**
   * simple_html_dom_node::outertext()
   * Get dom node's outer text (with tag)
   * @return outer text
  */
  function outertext() {
    global $debugObject;
    if ( is_object( $debugObject ) ) {
      $text = "";
      if ( $this->tag == "text" ) {
        if ( !empty( $this->text ) ) {
          $text = " with text: ".$this->text;
        }
      }
      $debugObject->debugLog( 1, "Innertext of tag: ".$this->tag.$text );
    }
    if ( $this->tag === "root" ) {
      return $this->innertext();
    }
    if ( $this->dom && $this->dom->callback !== null ) {
      call_user_func_array( $this->dom->callback, array( $this ) );
    }
    if ( isset( $this->_[HDOM_INFO_OUTER] ) ) {
      return $this->_[HDOM_INFO_OUTER];
    }
    if ( isset( $this->_[HDOM_INFO_TEXT] ) ) {
      return $this->dom->restore_noise( $this->_[HDOM_INFO_TEXT] );
    }
    if ( $this->dom && $this->dom->nodes[$this->_[HDOM_INFO_BEGIN]] ) {
      $ret  = $this->dom->nodes[$this->_[HDOM_INFO_BEGIN]]->makeup();
    }
    else {
      $ret  = "";
    }
    if ( isset( $this->_[HDOM_INFO_INNER] ) ) {
      if ( $this->tag != "br" ) {
        $ret  .=  $this->_[HDOM_INFO_INNER];
      }
    }
    else {
      if ( $this->nodes ) {
        foreach( $this->nodes as $n ) {
          $ret  .=  $this->convert_text( $n->outertext() );
        }
      }
    }
    if ( isset( $this->_[HDOM_INFO_END] ) && $this->_[HDOM_INFO_END] != 0 ) {
      $ret  .=  "</".$this->tag.">";
    }
    return $ret;
  }

  /**
   * simple_html_dom_node::text()
   * Get dom node's plain text
   * @return plain text
  */
  function text() {
    if ( isset( $this->_[HDOM_INFO_INNER] ) ) {
      return $this->_[HDOM_INFO_INNER];
    }
    switch( $this->nodetype ) {
      case HDOM_TYPE_TEXT:
        return $this->dom->restore_noise( $this->_[HDOM_INFO_TEXT] );
      case HDOM_TYPE_COMMENT:
        return "";
      case HDOM_TYPE_UNKNOWN:
        return "";
    }
    if ( strcasecmp( $this->tag, "script" ) === 0 ) {
      return "";
    }
    if ( strcasecmp( $this->tag, "style" ) === 0 ) {
      return "";
    }
    $ret  = "";

    if ( !is_null( $this->nodes ) ) {
      foreach( $this->nodes as $n ) {
        $ret  .=  $this->convert_text( $n->text() );
      }
    }
    return $ret;
  }

  /**
   * simple_html_dom_node::xmltext()
   * @return xml text
  */
  function xmltext() {
    $ret  = $this->innertext();
    $ret  = str_ireplace( "<![CDATA[", "", $ret );
    $ret  = str_replace( "]]>", "", $ret );
    return $ret;
  }

  /**
   * simple_html_dom_node::makeup()
   * Build node's text with tag
   * @return
  */
  function makeup() {
    if ( isset( $this->_[HDOM_INFO_TEXT] ) ) {
      return $this->dom->restore_noise( $this->_[HDOM_INFO_TEXT] );
    }
    $ret  = "<".$this->tag;
    $i    = -1;
    foreach( $this->attr as $key => $val ) {
      ++$i;
      if ( $val === null || $val === false) {
        continue;
      }
      $ret  .=  ( isset( $this->_[HDOM_INFO_SPACE][$i] ) ) ? $this->_[HDOM_INFO_SPACE][$i][0] : " ";
      if ( $val === true ) {
        $ret  .=  $key;
      }
      else {
        switch( $this->_[HDOM_INFO_QUOTE][$i] ) {
          case HDOM_QUOTE_DOUBLE:
            $quote  =  "\"";
          break;
          case HDOM_QUOTE_SINGLE:
            $quote  = "'";
          break;
          default:
            $quote  = "";
        }
        $ret  .= $key.$this->_[HDOM_INFO_SPACE][$i][1]."=".$this->_[HDOM_INFO_SPACE][$i][2].$quote.$val.$quote;
      }
    }
    $ret  = $this->dom->restore_noise( $ret );
    return trim( $ret ).$this->_[HDOM_INFO_ENDSPACE].">";
  }

  /**
   * simple_html_dom_node::find()
   * Find elements by css selector
   * @param mixed $selector
   * @param mixed $idx
   * @param bool $lowercase
   * @return elements
  */
  function find( $selector, $idx = null, $lowercase = false ) {
    $selectors  = $this->parse_selector( $selector );
    if ( ( $count = count( $selectors ) ) === 0 ) {
      return array();
    }
    $found_keys = array();
    for( $c = 0; $c < $count; ++$c ) {
      if ( ( $levle = count( $selectors[$c] ) ) === 0 ) {
        return array();
      }
      if ( !isset( $this->_[HDOM_INFO_BEGIN] ) ) {
        return array();
      }
      $head = array( $this->_[HDOM_INFO_BEGIN] => 1 );
      for( $l = 0; $l < $levle; ++$l ) {
        $ret  = array();
        foreach( $head as $k => $v ) {
          $n  = ( $k === -1 ) ? $this->dom->root : $this->dom->nodes[$k];
          $n->seek( $selectors[$c][$l], $ret, $lowercase );
        }
        $head = $ret;
      }
      foreach( $head as $k => $v ) {
        if ( !isset( $found_keys[$k] ) ) {
          $found_keys[$k] = 1;
        }
      }
    }
    ksort( $found_keys );
    $found  = array();
    foreach( $found_keys as $k => $v ) {
      $found[]  = $this->dom->nodes[$k];
    }
    if ( is_null( $idx ) ) {
      return $found;
    }
    else if ( $idx < 0 ) {
      $idx  = count( $found )+$idx;
    }
    return ( isset( $found[$idx] ) ) ? $found[$idx] : null;
  }

  /**
   * simple_html_dom_node::seek()
   * Seek for given conditions
   * @param mixed $selector
   * @param mixed $ret
   * @param bool $lowercase
   * @return void
  */
  protected function seek( $selector, &$ret, $lowercase = false ) {
    global $debugObject;
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLogEntry( 1 );
    }
    list( $tag, $key, $val, $exp, $no_key ) = $selector;
    if ( $tag && $key && is_numeric( $key ) ) {
      $count = 0;
      foreach( $this->children as $c ) {
        if ( $tag === "*" || $tag === $c->tag ) {
          if ( ++$count == $key ) {
            $ret[$c->_[HDOM_INFO_BEGIN]]  = 1;
            return;
          }
        }
      }
      return;
    }
    $end  = ( !empty( $this->_[HDOM_INFO_END] ) ) ? $this->_[HDOM_INFO_END] : 0;
    if ( $end == 0 ) {
      $parent = $this->parent;
      while( !isset( $parent->_[HDOM_INFO_END] ) && $parent !== null ) {
        $end   -= 1;
        $parent = $parent->parent;
      }
      $end  +=  $parent->_[HDOM_INFO_END];
    }
    for( $i = $this->_[HDOM_INFO_BEGIN]+1; $i < $end; ++$i ) {
      $node = $this->dom->nodes[$i];
      $pass = true;
      if ( $tag === "*" && !$key ) {
        if ( in_array( $node, $this->children, true ) ) {
          $ret[$i]  = 1;
        }
        continue;
      }
      if ( $tag && $tag != $node->tag && $tag !== "*" ) {
        $pass = false;
      }
      if ( $pass && $key ) {
        if ( $no_key ) {
          if ( isset( $node->attr[$key] ) ) {
            $pass = false;
          }
        }
        else {
          if ( ( $key != "plaintext" ) && !isset( $node->attr[$key] ) ) {
            $pass = false;
          }
        }
      }
      if ( $pass && $key && $val  && $val !== "*" ) {
        if ( $key == "plaintext" ) {
          $nodeKeyValue = $node->text();
        }
        else {
          $nodeKeyValue = $node->attr[$key];
        }
        if ( is_object( $debugObject ) ) {
          $debugObject->debugLog( 2, "testing node: ".$node->tag." for attribute: ".$key.$exp.$val." where nodes value is: ".$nodeKeyValue );
        }
        if ( $lowercase ) {
          $check  = $this->match( $exp, strtolower( $val ), strtolower( $nodeKeyValue ) );
        }
        else {
          $check  = $this->match( $exp, $val, $nodeKeyValue );
        }
        if ( is_object( $debugObject ) ) {
          $debugObject->debugLog( 2, "after match: ".( ( $check ) ? "true" : "false" ) );
        }
        if ( !$check && strcasecmp( $key, "class" ) === 0 ) {
          foreach( explode( " ", $node->attr[$key] ) as $k ) {
            if ( !empty( $k ) ) {
              if ( $lowercase ) {
                $check  = $this->match( $exp, strtolower( $val ), strtolower( $k ) );
              }
              else {
                $check  = $this->match( $exp, $val, $k );
              }
              if ( $check ) {
                break;
              }
            }
          }
        }
        if ( !$check ) {
          $pass = false;
        }
      }
      if ( $pass ) {
        $ret[$i]  = 1;
      }
      unset( $node );
    }
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLog( 1, "EXIT - ret: ", $ret );
    }
  }

  /**
   * simple_html_dom_node::match()
   * 
   * @param mixed $exp
   * @param mixed $pattern
   * @param mixed $value
   * @return matches
  */
  protected function match( $exp, $pattern, $value ) {
    global $debugObject;
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLogEntry( 1 );
    }
    switch( $exp ) {
      case "=":
        return ( $value === $pattern );
      case "!=":
        return ( $value !== $pattern );
      case "^=":
        return preg_match( "/^".preg_quote( $pattern, "/" )."/", $value );
      case "$=":
        return preg_match( "/".preg_quote( $pattern, "/" )."$/", $value );
      case "*=":
        if ( $pattern[0] == "/" ) {
          return preg_match( $pattern, $value );
        }
        return preg_match( "/".$pattern."/i", $value );
    }
    return false;
  }

  /**
   * simple_html_dom_node::parse_selector()
   * 
   * @param mixed $selector_string
   * @return parsed selectors
  */
  protected function parse_selector( $selector_string ) {
    global $debugObject;
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLogEntry( 1 );
    }
    $pattern  = "/([\w-:\*]*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[@?(!?[\w-:]+)(?:([!*^$]?=)[\"']?(.*?)[\"']?)?\])?([\/, ]+)/is";
    preg_match_all( $pattern, trim( $selector_string )." ", $matches, PREG_SET_ORDER );
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLog( 2, "Matches Array: ", $matches );
    }
    $selectors  = array();
    $result     = array();

    foreach( $matches as $m ) {
      $m[0] = trim( $m[0] );
      if ( $m[0] === "" || $m[0] === "/" || $m[0] === "//" ) {
        continue;
      }
      if ( $m[1] === "tbody" ) {
        continue;
      }
      list( $tag, $key, $val, $exp, $no_key ) = array( $m[1], null, null, '=', false );
      if ( !empty( $m[2] ) ) {
        $key  = "id";
        $val  = $m[2];
      }
      if ( !empty( $m[3] ) ) {
        $key  = "class";
        $val  = $m[3];
      }
      if ( !empty( $m[4] ) ) {
        $key  = $m[4];
      }
      if ( !empty( $m[5] ) ) {
        $exp  = $m[5];
      }
      if ( !empty( $m[6] ) ) {
        $val  = $m[6];
      }
      if ( $this->dom->lowercase ) {
        $tag  = strtolower( $tag );
        $key  = strtolower( $key );
      }
      if ( isset( $key[0] ) && $key[0] === "!" ) {
        $key    = substr( $key, 1 );
        $no_key = true;
      }
      $result[] = array( $tag, $key, $val, $exp, $no_key );
      if ( trim( $m[7] ) === "," ) {
        $selectors[]  = $result;
        $result       = array();
      }
    }
    if ( count( $result ) > 0 ) {
      $selectors[]  = $result;
    }
    return $selectors;
  }

  /**
   * simple_html_dom_node::__get()
   * Get attribute value
   * @param mixed $name
   * @return attrib value
  */
  function __get( $name ) {
    if ( isset( $this->attr[$name] ) ) {
      return $this->convert_text( $this->attr[$name] );
    }
    switch( $name ) {
      case "outertext":
        return $this->outertext();
      case "innertext":
        return $this->innertext();
      case "plaintext":
        return $this->text();
      case "xmltext":
        return $this->xmltext();
      default:
        return array_key_exists( $name, $this->attr );
    }
  }

  /**
   * simple_html_dom_node::__set()
   * Set attribute value
   * @param mixed $name
   * @param mixed $value
   * @return void
  */
  function __set( $name, $value ) {
    switch( $name ) {
      case "outertext":
        return $this->_[HDOM_INFO_OUTER]  = $value;
      case "innertext":
        if ( isset ($this->_[HDOM_INFO_TEXT] ) ) {
          return $this->_[HDOM_INFO_TEXT] = $value;
        }
        return $this->_[HDOM_INFO_INNER]  = $value;
    }
    if ( !isset( $this->attr[$name] ) ) {
      $this->_[HDOM_INFO_SPACE][] = array( " ", "", "" );
      $this->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
    }
    $this->attr[$name]  = $value;
  }

  /**
   * simple_html_dom_node::__isset()
   * Check if an attribute has value
   * @param mixed $name
   * @return true on success false on failure
  */
  function __isset( $name ) {
    switch( $name ) {
      case "outertext":
        return true;
      case "innertext":
        return true;
      case "plaintext":
        return true;
    }
    return ( array_key_exists( $name, $this->attr ) ) ? true : isset( $this->attr[$name] );
  }

  /**
   * simple_html_dom_node::__unset()
   * Unset an attribute
   * @param mixed $name
   * @return void
  */
  function __unset( $name ) {
    if ( isset( $this->attr[$name] ) ) {
      unset( $this->attr[$name] );
    }
  }

  /**
   * simple_html_dom_node::convert_text()
   * Convert the text from one character set to another if the two sets are not the same.
   * @param mixed $text
   * @return converted text
  */
  function convert_text( $text ) {
    global $debugObject;
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLogEntry(1);
    }
    $converted_text = $text;
    $sourceCharset  = "";
    $targetCharset  = "";
    if ( $this->dom ) {
      $sourceCharset  = strtoupper( $this->dom->_charset );
      $targetCharset  = strtoupper( $this->dom->_target_charset );
    }
    if ( is_object( $debugObject ) ) {
        $debugObject->debugLog( 3, "Source charset: ".$sourceCharset.", Target charaset: ".$targetCharset );
    }
    if ( !empty( $sourceCharset ) && !empty( $targetCharset ) && ( strcasecmp( $sourceCharset, $targetCharset ) != 0 ) ) {
      if ( ( strcasecmp( $targetCharset, "UTF-8" ) == 0 ) && ( $this->is_utf8( $text ) ) ) {
        $converted_text = $text;
      }
      else {
        $converted_text = iconv( $sourceCharset, $targetCharset, $text );
      }
    }
    return $converted_text;
  }

  /**
   * simple_html_dom_node::is_utf8()
   * Check whether characer encoding is utf8
   * @param mixed $string
   * @return true if utf8, else false
  */
  function is_utf8( $string ) {
    return ( utf8_encode( utf8_decode( $string ) ) == $string );
  }

  /**
   * simple_html_dom_node::getAllAttributes()
   * 
   * @return
  */
  function getAllAttributes() {
    return $this->attr;
  }

  /**
   * simple_html_dom_node::getAttribute()
   * 
   * @param mixed $name
   * @return
  */
  function getAttribute( $name ) {
    return $this->__get( $name );
  }

  /**
   * simple_html_dom_node::setAttribute()
   * 
   * @param mixed $name
   * @param mixed $value
   * @return void
  */
  function setAttribute( $name, $value ) {
    $this->__set( $name, $value );
  }

  /**
   * simple_html_dom_node::hasAttribute()
   * 
   * @param mixed $name
   * @return
  */
  function hasAttribute( $name ) {
    return $this->__isset( $name );
  }

  /**
   * simple_html_dom_node::removeAttribute()
   * 
   * @param mixed $name
   * @return void
  */
  function removeAttribute( $name ) {
    $this->__set( $name, null );
  }

  /**
   * simple_html_dom_node::getElementById()
   * 
   * @param mixed $id
   * @return
  */
  function getElementById( $id ) {
    return $this->find( "#$id", 0 );
  }

  /**
   * simple_html_dom_node::getElementsById()
   * 
   * @param mixed $id
   * @param mixed $idx
   * @return
  */
  function getElementsById( $id, $idx = null ) {
    return $this->find( "#$id", $idx );
  }

  /**
   * simple_html_dom_node::getElementByTagName()
   * 
   * @param mixed $name
   * @return
  */
  function getElementByTagName( $name ) {
    return $this->find( $name, 0 );
  }

  /**
   * simple_html_dom_node::getElementsByTagName()
   * 
   * @param mixed $name
   * @param mixed $idx
   * @return
  */
  function getElementsByTagName( $name, $idx = null ) {
    return $this->find( $name, $idx );
  }

  /**
   * simple_html_dom_node::parentNode()
   * 
   * @return
  */
  function parentNode() {
    return $this->parent();
  }

  /**
   * simple_html_dom_node::childNodes()
   * 
   * @param integer $idx
   * @return
  */
  function childNodes( $idx = -1 ) {
    return $this->children( $idx );
  }

  /**
   * simple_html_dom_node::firstChild()
   * 
   * @return
  */
  function firstChild() {
    return $this->first_child();
  }

  /**
   * simple_html_dom_node::lastChild()
   * 
   * @return
  */
  function lastChild() {
    return $this->last_child();
  }

  /**
   * simple_html_dom_node::nextSibling()
   * 
   * @return
  */
  function nextSibling() {
    return $this->next_sibling();
  }

  /**
   * simple_html_dom_node::previousSibling()
   * 
   * @return
  */
  function previousSibling() {
    return $this->prev_sibling();
  }
}

/**
 * simple_html_dom
 * 
 * @package PlaceLocalInclude
 * @author bystwn22
 * @copyright 2012
 * @version 2.3
 * @access public
 */
class simple_html_dom {
  public $root = null;
  public $nodes = array();
  public $callback = null;
  public $lowercase = false;
  public $size;
  protected $pos;
  protected $doc;
  protected $char;
  protected $cursor;
  protected $parent;
  protected $noise = array();
  protected $token_blank = " \t\r\n";
  protected $token_equal = ' =/>';
  protected $token_slash = " />\r\n\t";
  protected $token_attr = ' >';
  protected $_charset = '';
  protected $_target_charset = '';
  protected $default_br_text = "";

  protected $self_closing_tags  = array(
    'img'   =>  1,
    'br'    =>  1,
    'input' =>  1,
    'meta'  =>  1,
    'link'  =>  1,
    'hr'    =>  1,
    'base'  =>  1,
    'embed' =>  1,
    'spacer'=>  1
  );
  protected $block_tags = array(
    'root'  =>  1,
    'body'  =>  1,
    'form'  =>  1,
    'div'   =>  1,
    'span'  =>  1,
    'table' =>  1
  );
  protected $optional_closing_tags  = array(
    'tr'  =>  array( 'tr' => 1, 'td' => 1, 'th' => 1 ),
    'th'  =>  array( 'th' => 1 ),
    'td'  =>  array( 'td' => 1 ),
    'li'  =>  array( 'li' => 1 ),
    'dt'  =>  array( 'dt' => 1, 'dd' => 1 ),
    'dd'  =>  array( 'dd' => 1, 'dt' => 1 ),
    'dl'  =>  array( 'dd' => 1, 'dt' => 1 ),
    'p'   =>  array( 'p' => 1 ),
    'nobr'=>  array( 'nobr' => 1 ),
    'b'   =>  array( 'b' => 1 ),
  );

  /**
   * simple_html_dom::__construct()
   * 
   * @param mixed $str
   * @param bool $lowercase
   * @param bool $forceTagsClosed
   * @param mixed $target_charset
   * @param bool $stripRN
   * @param mixed $defaultBRText
   * @return void
  */
  function __construct( $str = null, $lowercase = true, $forceTagsClosed = true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT ) {
    if ( $str ) {
      if ( preg_match( "/^http:\/\//i", $str ) || is_file( $str ) ) {
        $this->load_file( $str );
      }
      else {
        $this->load( $str, $lowercase, $stripRN, $defaultBRText );
      }
    }
    if ( !$forceTagsClosed ) {
      $this->optional_closing_array = array();
    }
    $this->_target_charset  = $target_charset;
  }

  function __destruct() {
    $this->clear();
  }

  /**
   * simple_html_dom::load()
   * Load html from string
   * @param mixed $str
   * @param bool $lowercase
   * @param bool $stripRN
   * @param mixed $defaultBRText
   * @return void
  */
  function load( $str, $lowercase = true, $stripRN = true, $defaultBRText = DEFAULT_BR_TEXT ) {
    global $debugObject;
    $this->prepare( $str, $lowercase, $stripRN, $defaultBRText );
    // strip out comments
    $this->remove_noise( "'<!--(.*?)-->'is" );
    // strip out cdata
    $this->remove_noise( "'<!\[CDATA\[(.*?)\]\]>'is", true );
    // strip out <script> tags
    $this->remove_noise( "'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is" );
    $this->remove_noise( "'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is" );
    // strip out <style> tags
    $this->remove_noise( "'<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>'is" );
    $this->remove_noise( "'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is" );
    // strip out preformatted tags
    $this->remove_noise( "'<\s*(?:code)[^>]*>(.*?)<\s*/\s*(?:code)\s*>'is" );
    // strip out server side scripts
    $this->remove_noise( "'(<\?)(.*?)(\?>)'s", true);
    // strip smarty scripts
    $this->remove_noise( "'(\{\w)(.*?)(\})'s", true);

    // parsing
    while( $this->parse() );
    // end
    $this->root->_[HDOM_INFO_END] = $this->cursor;
    $this->parse_charset();
  }

  /**
   * simple_html_dom::load_file()
   * Load html from file
   * @return void
  */
  function load_file() {
    $args = func_get_args();
    $this->load( call_user_func_array( "file_get_contents", $args ), true );
    if ( ( $error = error_get_last() ) !== null ) {
      $this->clear();
      return false;
    }
  }

  /**
   * simple_html_dom::set_callback()
   * Set callback function
   * @param mixed $function_name
   * @return void
  */
  function set_callback( $function_name ) {
    $this->callback = $function_name;
  }

  /**
   * simple_html_dom::remove_callback()
   * Remove callback function
   * @return void
  */
  function remove_callback() {
    $this->callback = null;
  }

  /**
   * simple_html_dom::save()
   * Save dom as string
   * @param string $filepath
   * @return
  */
  function save( $filepath = "" ) {
    $ret  = $this->root->innertext();
    if ( $filepath !== "" ) {
      file_put_contents( $filepath, $ret, LOCK_EX );
    }
    return $ret;
  }

  /**
   * simple_html_dom::find()
   * Find dom node by css selector
   * @param mixed $selector
   * @param mixed $idx
   * @param bool $lowercase
   * @return dom node
  */
  function find( $selector, $idx = null, $lowercase = false ) {
    return $this->root->find( $selector, $idx, $lowercase );
  }

  /**
   * simple_html_dom::clear()
   * Clean up memory due to php5 circular references memory leak...
   * @return void
  */
  function clear() {
    foreach( $this->nodes as $n ) {
      $n->clear();
      $n  = null;
    }
    if ( isset( $this->children ) ) {
      foreach( $this->children as $n ) {
        $n->clear();
        $n  = null;
      }
    }
    if ( isset( $this->parent ) ) {
      $this->parent->clear();
      unset( $this->parent );
    }
    if ( isset( $this->root ) ) {
      $this->root->clear();
      unset( $this->root );
    }
    unset( $this->doc );
    unset( $this->noise );
  }

  /**
   * simple_html_dom::dump()
   * Sump html dom tree
   * @param bool $show_attr
   * @return void
  */
  function dump( $show_attr = true ) {
    $this->root->dump( $show_attr );
  }

  /**
   * simple_html_dom::prepare()
   * Prepare HTML data and init everything
   * @param mixed $str
   * @param bool $lowercase
   * @param bool $stripRN
   * @param mixed $defaultBRText
   * @return void
  */
  protected function prepare( $str, $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT ) {
    $this->clear();
    $this->size = strlen( $str );
    if ( $stripRN ) {
      $str  = str_replace( "\r", " ", $str );
      $str  = str_replace( "\n", " ", $str );
    }

    $this->doc    = $str;
    $this->pos    = 0;
    $this->cursor = 1;
    $this->noise  = array();
    $this->nodes  = array();
    $this->lowercase        = $lowercase;
    $this->default_br_text  = $defaultBRText;
    $this->root             = new simple_html_dom_node( $this );
    $this->root->tag        = "root";
    $this->root->_[HDOM_INFO_BEGIN] = -1;
    $this->root->nodetype   = HDOM_TYPE_ROOT;
    $this->parent = $this->root;
    if ( $this->size > 0 ) {
      $this->char = $this->doc[0];
    }
  }

  /**
   * simple_html_dom::parse()
   * Parse html content
   * @return true/content on success
  */
  protected function parse() {
    if ( ( $s = $this->copy_until_char( '<' ) ) === '' ) {
      return $this->read_tag();
    }
    $node = new simple_html_dom_node( $this );
    ++$this->cursor;
    $node->_[HDOM_INFO_TEXT]  = $s;
    $this->link_nodes( $node, false );
    return true;
  }

  /**
   * simple_html_dom::parse_charset()
   * Parse html charset
   * @return charset
  */
  protected function parse_charset() {
    global $debugObject;
    $charset  = null;
    if ( function_exists( "get_last_retrieve_url_contents_content_type" ) ) {
      $contentTypeHeader  = get_last_retrieve_url_contents_content_type();
      $success  = preg_match( "/charset=(.+)/", $contentTypeHeader, $matches );
      if ( $success ) {
        $charset  = $matches[1];
        if ( is_object( $debugObject ) ) {
          $debugObject->debugLog( 2, "header content-type found charset of: ".$charset );
        }
      }
    }
    if ( empty( $charset ) ) {
      $el = $this->root->find( "meta[http-equiv=Content-Type]", 0 );
      if ( !empty( $el ) ) {
        $fullvalue  = $el->content;
        if ( is_object( $debugObject ) ) {
          $debugObject->debugLog( 2, "meta content-type tag found ".$fullValue );
        }
        if ( !empty( $fullvalue ) ) {
          $success  = preg_match( "/charset=(.+)/", $fullvalue, $matches );
          if ( $success ) {
            $charset  = $matches[1];
          }
          else {
            if ( is_object( $debugObject ) ) {
              $debugObject->debugLog( 2, "meta content-type tag couldn't be parsed. using iso-8859 default." );
            }
            $charset  = "ISO-8859-1";
          }
        }
      }
    }
    if ( empty( $charset ) ) {
      $charset  = mb_detect_encoding( $this->root->plaintext."ascii", $encoding_list = array( "UTF-8", "CP1252" ) );
      if ( is_object( $debugObject ) ) {
        $debugObject->debugLog( 2, "mb_detect found: ".$charset );
      }
      if ( $charset === false ) {
        if ( is_object( $debugObject ) ) {
          $debugObject->debugLog( 2, "since mb_detect failed - using default of utf-8" );
        }
        $charset  = "UTF-8";
      }
    }
    if ( ( strtolower( $charset ) == strtolower( "ISO-8859-1" ) ) || ( strtolower( $charset ) == strtolower( "Latin1" ) ) || ( strtolower( $charset ) == strtolower( "Latin-1" ) ) ) {
      if ( is_object( $debugObject ) ) {
        $debugObject->debugLog( 2, "replacing ".$charset." with CP1252 as its a superset" );
      }
      $charset  = "CP1252";
    }
    if ( is_object( $debugObject ) ) {
      $debugObject->debugLog( 1, "EXIT - ".$charset );
    }
    return $this->_charset  = $charset;
  }

  /**
   * simple_html_dom::read_tag()
   * Read tag info
   * @return true on success
  */
  protected function read_tag() {
        if ($this->char!=='<') {
            $this->root->_[HDOM_INFO_END] = $this->cursor;
            return false;
        }
        $begin_tag_pos = $this->pos;
        $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next

        // end tag
        if ($this->char==='/') {
            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next
            // This represetns the change in the simple_html_dom trunk from revision 180 to 181.
            // $this->skip($this->token_blank_t);
            $this->skip($this->token_blank);
            $tag = $this->copy_until_char('>');

            // skip attributes in end tag
            if (($pos = strpos($tag, ' '))!==false)
                $tag = substr($tag, 0, $pos);

            $parent_lower = strtolower($this->parent->tag);
            $tag_lower = strtolower($tag);

            if ($parent_lower!==$tag_lower) {
                if (isset($this->optional_closing_tags[$parent_lower]) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[HDOM_INFO_END] = 0;
                    $org_parent = $this->parent;

                    while (($this->parent->parent) && strtolower($this->parent->tag)!==$tag_lower)
                        $this->parent = $this->parent->parent;

                    if (strtolower($this->parent->tag)!==$tag_lower) {
                        $this->parent = $org_parent; // restore origonal parent
                        if ($this->parent->parent) $this->parent = $this->parent->parent;
                        $this->parent->_[HDOM_INFO_END] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                }
                else if (($this->parent->parent) && isset($this->block_tags[$tag_lower])) {
                    $this->parent->_[HDOM_INFO_END] = 0;
                    $org_parent = $this->parent;

                    while (($this->parent->parent) && strtolower($this->parent->tag)!==$tag_lower)
                        $this->parent = $this->parent->parent;

                    if (strtolower($this->parent->tag)!==$tag_lower) {
                        $this->parent = $org_parent; // restore origonal parent
                        $this->parent->_[HDOM_INFO_END] = $this->cursor;
                        return $this->as_text_node($tag);
                    }
                }
                else if (($this->parent->parent) && strtolower($this->parent->parent->tag)===$tag_lower) {
                    $this->parent->_[HDOM_INFO_END] = 0;
                    $this->parent = $this->parent->parent;
                }
                else
                    return $this->as_text_node($tag);
            }

            $this->parent->_[HDOM_INFO_END] = $this->cursor;
            if ($this->parent->parent) $this->parent = $this->parent->parent;

            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        $node = new simple_html_dom_node($this);
        $node->_[HDOM_INFO_BEGIN] = $this->cursor;
        ++$this->cursor;
        $tag = $this->copy_until($this->token_slash);
        $node->tag_start = $begin_tag_pos;

        // doctype, cdata & comments...
        if (isset($tag[0]) && $tag[0]==='!') {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until_char('>');

            if (isset($tag[2]) && $tag[1]==='-' && $tag[2]==='-') {
                $node->nodetype = HDOM_TYPE_COMMENT;
                $node->tag = 'comment';
            } else {
                $node->nodetype = HDOM_TYPE_UNKNOWN;
                $node->tag = 'unknown';
            }
            if ($this->char==='>') $node->_[HDOM_INFO_TEXT].='>';
            $this->link_nodes($node, true);
            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        // text
        if ($pos=strpos($tag, '<')!==false) {
            $tag = '<' . substr($tag, 0, -1);
            $node->_[HDOM_INFO_TEXT] = $tag;
            $this->link_nodes($node, false);
            $this->char = $this->doc[--$this->pos]; // prev
            return true;
        }

        if (!preg_match("/^[\w-:]+$/", $tag)) {
            $node->_[HDOM_INFO_TEXT] = '<' . $tag . $this->copy_until('<>');
            if ($this->char==='<') {
                $this->link_nodes($node, false);
                return true;
            }

            if ($this->char==='>') $node->_[HDOM_INFO_TEXT].='>';
            $this->link_nodes($node, false);
            $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next
            return true;
        }

        // begin tag
        $node->nodetype = HDOM_TYPE_ELEMENT;
        $tag_lower = strtolower($tag);
        $node->tag = ($this->lowercase) ? $tag_lower : $tag;

        // handle optional closing tags
        if (isset($this->optional_closing_tags[$tag_lower]) ) {
            while (isset($this->optional_closing_tags[$tag_lower][strtolower($this->parent->tag)])) {
                $this->parent->_[HDOM_INFO_END] = 0;
                $this->parent = $this->parent->parent;
            }
            $node->parent = $this->parent;
        }

        $guard = 0; // prevent infinity loop
        $space = array($this->copy_skip($this->token_blank), '', '');

        // attributes
        do
        {
            if ($this->char!==null && $space[0]==='') break;
            $name = $this->copy_until($this->token_equal);
            if ($guard===$this->pos) {
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next
                continue;
            }
            $guard = $this->pos;

            // handle endless '<'
            if ($this->pos>=$this->size-1 && $this->char!=='>') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = '<'.$tag . $space[0] . $name;
                $node->tag = 'text';
                $this->link_nodes($node, false);
                return true;
            }

            // handle mismatch '<'
            if ($this->doc[$this->pos-1]=='<') {
                $node->nodetype = HDOM_TYPE_TEXT;
                $node->tag = 'text';
                $node->attr = array();
                $node->_[HDOM_INFO_END] = 0;
                $node->_[HDOM_INFO_TEXT] = substr($this->doc, $begin_tag_pos, $this->pos-$begin_tag_pos-1);
                $this->pos -= 2;
                $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next
                $this->link_nodes($node, false);
                return true;
            }

            if ($name!=='/' && $name!=='') {
                $space[1] = $this->copy_skip($this->token_blank);
                $name = $this->restore_noise($name);
                if ($this->lowercase) $name = strtolower($name);
                if ($this->char==='=') {
                    $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next
                    $this->parse_attr($node, $name, $space);
                }
                else {
                    //no value attr: nowrap, checked selected...
                    $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
                    $node->attr[$name] = true;
                    if ($this->char!='>') $this->char = $this->doc[--$this->pos]; // prev
                }
                $node->_[HDOM_INFO_SPACE][] = $space;
                $space = array($this->copy_skip($this->token_blank), '', '');
            }
            else
                break;
        } while ($this->char!=='>' && $this->char!=='/');

        $this->link_nodes($node, true);
        $node->_[HDOM_INFO_ENDSPACE] = $space[0];

        // check self closing
        if ($this->copy_until_char_escape('>')==='/') {
            $node->_[HDOM_INFO_ENDSPACE] .= '/';
            $node->_[HDOM_INFO_END] = 0;
        }
        else {
            // reset parent
            if (!isset($this->self_closing_tags[strtolower($node->tag)])) $this->parent = $node;
        }
        $this->char = (++$this->pos<$this->size) ? $this->doc[$this->pos] : null; // next

        // If it's a BR tag, we need to set it's text to the default text.
        // This way when we see it in plaintext, we can generate formatting that the user wants.
        if ($node->tag == "br") {
            $node->_[HDOM_INFO_INNER] = $this->default_br_text;
        }

        return true;
  }

  /**
   * simple_html_dom::parse_attr()
   * Parse attributes
   * @param mixed $node
   * @param mixed $name
   * @param mixed $space
   * @return void
   */
  protected function parse_attr( $node, $name, &$space ) {
    if ( isset( $node->attr[$name] ) ) {
      return;
    }
    $space[2] = $this->copy_skip( $this->token_blank );
    switch( $this->char ) {
      case "\"":
        $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_DOUBLE;
        $this->char = ( ++$this->pos < $this->size ) ? $this->doc[$this->pos] : null;
        $node->attr[$name]  = $this->restore_noise( $this->copy_until_char_escape( "\"" ) );
        $this->char = ( ++$this->pos < $this->size ) ? $this->doc[$this->pos] : null;
      break;
      case "'":
        $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_SINGLE;
        $this->char = ( ++$this->pos < $this->size ) ? $this->doc[$this->pos] : null;
        $node->attr[$name]  = $this->restore_noise( $this->copy_until_char_escape( "'" ) );
        $this->char = ( ++$this->pos < $this->size ) ? $this->doc[$this->pos] : null;
      break;
      default:
        $node->_[HDOM_INFO_QUOTE][] = HDOM_QUOTE_NO;
        $node->attr[$name]  = $this->restore_noise( $this->copy_until( $this->token_attr ) );
    }
    $node->attr[$name]    = str_replace( "\r", "", $node->attr[$name] );
    if ( $name != "title" ) {
      $node->attr[$name]  = str_replace( "\n", "", $node->attr[$name] );
    }
    if ( $name == "class" ) {
      $node->attr[$name]  = trim( $node->attr[$name] );
    }
  }

  /**
   * simple_html_dom::link_nodes()
   * Link node's parent
   * @param mixed $node
   * @param mixed $is_child
   * @return void
  */
  protected function link_nodes( &$node, $is_child ) {
    $node->parent = $this->parent;
    $this->parent->nodes[]  = $node;
    if ( $is_child ) {
      $this->parent->children[] = $node;
    }
  }

  /**
   * simple_html_dom::as_text_node()
   * As a text node
   * @param mixed $tag
   * @return true
   */
  protected function as_text_node( $tag ) {
    $node = new simple_html_dom_node( $this );
    ++$this->cursor;
    $node->_[HDOM_INFO_TEXT]  = "</".$tag.">";
    $this->link_nodes( $node, false );
    $this->char = ( ++$this->pos < $this->size ) ? $this->doc[$this->pos] : null;
    return true;
  }

  /**
   * simple_html_dom::skip()
   * 
   * @param mixed $chars
   * @return void
  */
  protected function skip( $chars ) {
    $this->pos += strspn( $this->doc, $chars, $this->pos );
    $this->char = ( $this->pos < $this->size ) ? $this->doc[$this->pos] : null;
  }

  /**
   * simple_html_dom::copy_skip()
   * 
   * @param mixed $chars
   * @return skipped text
  */
  protected function copy_skip( $chars ) {
    $pos  = $this->pos;
    $len  = strspn( $this->doc, $chars, $pos );
    $this->pos += $len;
    $this->char = ( $this->pos < $this->size ) ? $this->doc[$this->pos] : null;
    if ( $len === 0 ) {
      return "";
    }
    return substr( $this->doc, $pos, $len );
  }

  /**
   * simple_html_dom::copy_until()
   * 
   * @param mixed $chars
   * @return copied text
  */
  protected function copy_until( $chars ) {
    $pos  = $this->pos;
    $len  = strcspn( $this->doc, $chars, $pos );
    $this->pos += $len;
    $this->char = ( $this->pos < $this->size ) ? $this->doc[$this->pos] : null; // next
    return substr( $this->doc, $pos, $len );
  }

  /**
   * simple_html_dom::copy_until_char()
   * 
   * @param mixed $char
   * @return copied text
  */
  protected function copy_until_char( $char ) {
    if ( $this->char === null ) {
      return '';
    }
    if ( ( $pos = strpos( $this->doc, $char, $this->pos ) ) === false ) {
      $ret  = substr( $this->doc, $this->pos, $this->size-$this->pos );
      $this->char = null;
      $this->pos  = $this->size;
      return $ret;
    }
    if ( $pos === $this->pos ) {
      return '';
    }
    $pos_old = $this->pos;
    $this->char = $this->doc[$pos];
    $this->pos = $pos;
    return substr( $this->doc, $pos_old, $pos-$pos_old );
  }

  /**
   * simple_html_dom::copy_until_char_escape()
   * 
   * @param mixed $char
   * @return copied text
  */
  protected function copy_until_char_escape( $char ) {
    if ( $this->char===null ) {
      return "";
    }
    $start = $this->pos;
    while( 1 ) {
      if ( ( $pos = strpos( $this->doc, $char, $start ) ) === false ) {
        $ret  = substr( $this->doc, $this->pos, $this->size-$this->pos );
        $this->char = null;
        $this->pos  = $this->size;
        return $ret;
      }
      if ( $pos === $this->pos ) {
        return "";
      }
      if ( $this->doc[$pos-1] === "\\" ) {
        $start  = $pos+1;
        continue;
      }
      $pos_old    = $this->pos;
      $this->char = $this->doc[$pos];
      $this->pos  = $pos;
      return substr( $this->doc, $pos_old, $pos-$pos_old );
    }
  }

  /**
   * simple_html_dom::remove_noise()
   * Remove noise from html content
   * @param mixed $pattern
   * @param bool $remove_tag
   * @return void
   */
  protected function remove_noise( $pattern, $remove_tag = false ) {
    $count  = preg_match_all( $pattern, $this->doc, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE );
    for( $i = $count-1; $i > -1; --$i ) {
      $key  = "___noise___".sprintf( "% 3d", count( $this->noise )+100 );
      $idx  = ( $remove_tag ) ? 0 : 1;
      $this->noise[$key]  = $matches[$i][$idx][0];
      $this->doc  = substr_replace( $this->doc, $key, $matches[$i][$idx][1], strlen( $matches[$i][$idx][0] ) );
    }
    $this->size = strlen( $this->doc );
    if ( $this->size > 0 ) {
      $this->char = $this->doc[0];
    }
  }

  /**
   * simple_html_dom::restore_noise()
   * Restore noise to html content
   * @param mixed $text
   * @return text
  */
  function restore_noise( $text ) {
    while( ( $pos = strpos( $text, "___noise___" ) ) !== false ) {
      $key  = "___noise___".$text[$pos+11].$text[$pos+12].$text[$pos+13];
      if ( isset( $this->noise[$key] ) ) {
        $text = substr( $text, 0, $pos ).$this->noise[$key].substr( $text, $pos+14 );
      }
    }
    return $text;
  }

  /**
   * simple_html_dom::__toString()
   * 
   * @return innertext
  */
  function __toString() {
    return $this->root->innertext();
  }

  /**
   * simple_html_dom::__get()
   * 
   * @param mixed $name
   * @return
  */
  function __get( $name ) {
    switch( $name ) {
      case "outertext":
        return $this->root->innertext();
      case "innertext":
        return $this->root->innertext();
      case "plaintext":
        return $this->root->text();
      case "charset":
        return $this->_charset;
      case "target_charset":
        return $this->_target_charset;
    }
  }

  /**
   * simple_html_dom::childNodes()
   * 
   * @param integer $idx
   * @return
  */
  function childNodes( $idx = -1 ) {
    return $this->root->childNodes( $idx );
  }

  /**
   * simple_html_dom::firstChild()
   * 
   * @return
  */
  function firstChild() {
    return $this->root->first_child();
  }

  /**
   * simple_html_dom::lastChild()
   * 
   * @return
  */
  function lastChild() {
    return $this->root->last_child();
  }

  /**
   * simple_html_dom::getElementById()
   * 
   * @param mixed $id
   * @return
  */
  function getElementById( $id ) {
    return $this->find("#$id", 0);
  }

  /**
   * simple_html_dom::getElementsById()
   * 
   * @param mixed $id
   * @param mixed $idx
   * @return
  */
  function getElementsById( $id, $idx = null ) {
    return $this->find( "#$id", $idx );
  }

  /**
   * simple_html_dom::getElementByTagName()
   * 
   * @param mixed $name
   * @return
  */
  function getElementByTagName( $name ) {
    return $this->find( $name, 0 );
  }

  /**
   * simple_html_dom::getElementsByTagName()
   * 
   * @param mixed $name
   * @param integer $idx
   * @return
  */
  function getElementsByTagName( $name, $idx=-1 ) {
    return $this->find( $name, $idx );
  }

  /**
   * simple_html_dom::loadFile()
   * 
   * @return void
  */
  function loadFile() {
    $args = func_get_args();
    $this->load_file( $args );
  }
}

$ipParser = new simple_html_dom;
?>