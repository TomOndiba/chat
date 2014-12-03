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
 * ipPagination
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipPagination {
  public $items_per_page;
  public $items_total;
  public $current_page;
  public $mid_range;
  public $num_pages;
  public $low   = -1;
  public $high  = -1;
  public $limit;
  public $limit_arr = array();
  public $return = array();

  /**
   * ipPagination::__construct()
   * 
   * @param integer $items_per_page
   * @return
   */
  function __construct( $items_per_page = 1 ) {
    $this->current_page   = 1;
    $this->mid_range      = 7;
    if ( isset( $_GET["ipp"] ) && (int)$_GET["ipp"] ) {
      $this->items_per_page = (int)$_GET["ipp"];
    }
    else {
      $this->items_per_page = ( (int)$items_per_page ) ? $items_per_page : 10;
    }
  }

  /**
   * ipPagination::paginate()
   * 
   * @return
   */
  function paginate() {
    $this->return["prev"] = false;
    $this->return["next"] = false;
    $this->return["nums"] = array();
    $this->return["init"] = false;

    if( !( (int)$this->items_per_page ) || ( $this->items_per_page < 1 ) ) {
      $this->items_per_page = 32;
    }

    $this->num_pages    = ceil( $this->items_total / $this->items_per_page );
    $this->current_page = min( $this->num_pages, max( 1, $this->current_page ) );

    $prev_page  = ( $this->current_page - 1 );
    $next_page  = ( $this->current_page + 1 );

    $this->return["max"]  = $this->num_pages;
    $this->return["min"]  = $this->current_page;

    if ( $this->num_pages > 1 ) {
      $this->return["init"] = true;
      if ( $this->current_page != 1 && $this->items_total >= 1 ) {
        $this->return["prev"] = $prev_page;
      }

      $this->start_range  = ( $this->current_page - floor( $this->mid_range / 2 ) );
      $this->end_range    = ( $this->current_page + floor( $this->mid_range / 2 ) );
      if ( $this->start_range < 1 ) {
        $this->end_range    = ( $this->end_range + ( abs( $this->start_range ) + 1 ) );
        $this->start_range  = 1;
      }
      if ( $this->end_range > $this->num_pages ) {
        $this->start_range  = ( $this->start_range - ( $this->end_range - $this->num_pages ) );
        $this->end_range    = $this->num_pages;
      }

      $this->range  = range( $this->start_range, $this->end_range );

      for( $i = 1; $i <= $this->num_pages; $i++ ) {
        if ( $this->range[0] > 2 && $i == $this->range[0] ) {
          $this->return["nums"][] = array( "link" => false, "text" => "&hellip;" );
        }
        if ( $i == 1 || $i == $this->num_pages || in_array( $i, $this->range ) ) {
          $this->return["nums"][] = array( "link" => ( ( $i == $this->current_page ) ) ? false : $i, "text" => $i );
        }
        if ( ( $this->range[$this->mid_range-1] < ( $this->num_pages - 1 ) ) && $i == $this->range[$this->mid_range-1] ) {
          $this->return["nums"][] = array( "link" => false, "text" => "&hellip;" );
        }
      }

      if ( ( $this->current_page != $this->num_pages && $this->items_total > 0 ) ) {
        $this->return["next"] = $next_page;
      }
    }
    if ( $this->items_total != 0 ) {
      $this->low    = ( ( $this->current_page - 1 ) * $this->items_per_page );
      $this->high   = ( ( $this->current_page * $this->items_per_page ) - 1 );
      $this->limit  = sprintf( "LIMIT %d, %d", $this->low, $this->items_per_page );
      $this->limit_arr  = array( $this->low, $this->items_per_page );
    }
  }

  /**
   * ipPagination::display_pages()
   * 
   * @return
   */
  function display_pages() {
    return json_decode( json_encode( $this->return ) );
  }
}
?>