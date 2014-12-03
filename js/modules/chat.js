/*!
  Copyright 2014 The Impact Plus. All rights reserved.

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
var mentor  = false,
    unds  = _.noConflict(),
    malerts = { "user" : {}, "group" : {} },
    ipWebSocket = false;
function checkInt( arg1, arg2 ) {
  arg1  = parseInt( arg1 );
  if ( arg1 && !isNaN( arg1 ) ) {
    return ( arg1 === arg2 ); 
  }
  return false;
}
function has_alert( user_id, type ) {
  type  = type || "user";
  if ( malerts[type].hasOwnProperty( user_id ) && unds.isArray( malerts[type][user_id] ) && malerts[type][user_id].length ) {
    return malerts[type][user_id].length;
  }
  return 0;
}
function add_alert( user_id, type, message_id ) {
  type  = type || "user";
  if ( !user_id ) {
    return 0;
  }
  malerts[type][user_id]  = malerts[type][user_id] || [];
  if ( $.inArray( message_id, malerts[type][user_id] ) === -1 ) {
    malerts[type][user_id].push( message_id );
  }
  return has_alert( user_id );
}
function clear_alert( user_id, type ) {
  type  = type || "user";
  if ( malerts[type].hasOwnProperty( user_id ) ) {
    malerts[type][user_id]  = [];
    return true;
  }
  return false;
}
var ipChat  = Backbone.Model.extend({
  callbacks: [],
  socket: false,
  sockets: {},
  hooks_ready: false,
  ready: function( callback ) {
    if ( this.hooks_ready ) {
      call_user_func_array( callback );
      return;
    }
    this.callbacks.push( callback );
    return this;
  },
  initialize: function( options ) {
    //this.prevent_console();
    if ( !this.has( "initialized" ) ) {
      this.set_options( 'st', ( new Date() / 1000 ) );
      this.set( "initialized", true );
      this.def_options();
      this.set_options( options );
      this.set_options( 'wl', window.location );
      this.set_options( 'wh', window.location.hostname );
      this.set_options( 'ws', window.location.search );
      this.set_options( 'wp', window.location.pathname );
      this.set( b2rs( [ 1781609216 ] ), true );
      this.set( b2rs( [ 1764766464 ] ), sjcl.encrypt( b2rs( [ 1764766464 ] ), Math.round( ( new Date().getTime() / 1000 ) + 12000 ).toString() ) );
      window.ipExtend = window.ipExtend || {};
      window.ipExtend.ipChat  = this;
      if ( typeof $ === 'undefined' ) {
        throw new ReferenceError( b2rs( "1783723365,1920540777,1931505263,1948279909,1718185573,1677721600".split(",") ) );
      }
    }
  },
  ancestors: function( callback, args ) {
    ipls( b2rs( "1231972724,1767992425,2053729895,542460198,1751477356,1768962816".split(",") ) );
    args  = args || [];
    var scripts = [];
    if ( typeof draggable === "undefined" ) {
      scripts.unshift( b2rs( "1785820517,1920562537".split(",") ) );
    }
    if ( scripts.length ) {
      scripts = scripts.join(",");
      iplj(scripts,'modules',function() {
        if ( typeof callback === "function" ) {
          call_user_func_array( callback, args );
        }
        else {
          iplh();
        }
      },function() {
        iplh();
        ipcl().notice( sprintf( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179685,1903520114,1701060717,1868854636,1702043688,628304128".split(",") ), scripts ), true );
      });
    }
    else {
      if ( typeof callback === "function" ) {
        call_user_func_array( callback, args );
      }
    }
  },
  phpjs: function( callback ) {
    var scripts = ['phpjs','hooks','modules','runtime','xhr','dialog','cookie','token'];
        scripts.join(",");
    iplj(scripts,'modules',function() {
      ipChat.prototype.hooks_ready  = true;
      if ( ipChat.prototype.callbacks.length ) {
        for( var i = 0; i < ipChat.prototype.callbacks.length; i++ ) {
          call_user_func_array( ipChat.prototype.callbacks[i] );
        }
        ipChat.prototype.callbacks  = [];
      }
      if ( typeof callback === "function" ) {
        callback();
      }
    },function() {
      iplh();
      ipcl().notice( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179685,1903520114,1701060717,1868854636,1702043688,1885892714,1932271720,1869572979,740325480,1915289600".split(",") ), true );
    });
  },
  can_play: function( idx, idn ) {
    var muted = $.cookie( "muted_conv" );
        muted = json_decode( muted );
        muted = muted || {};
    if ( muted.hasOwnProperty( idn ) && unds.isArray( muted[idn] ) && $.inArray( idx.toString(), muted[idn] ) !== -1 ) {
      return false;
    }
    return true;
  },
  load_audio: function( audio ) {
    if ( typeof window.Audio == 'function' ) {
      return new Audio( audio );
    }
  },
  play_audio: function( index ) {
    if ( typeof window.Audio == 'function' ) {
      ipgo(index).pause();
      ipgo(index).currentTime = 0;
      ipgo(index).play();
    }
  },
  track_browser: function() {
    if ( typeof version_compare !== "function" ) {
      throw new ReferenceError( b2rs( "1986359923,1768910431,1668246896,1634886944,1769152622,1869881444,1701210478,1701052416".split(",") ) );
    }
    var browsers  = {
          'explorer'  : $.browser.msie,
          'chrome'    : $.browser.chrome,
          'firefox'   : $.browser.mozilla,
          'safari'    : $.browser.safari,
          'opera'     : $.browser.opera
        },
        version = $.browser.version;
    for( x in browsers ) {
      if ( browsers[x] ) {
        $("body").addClass("_"+x);
      }
    }
    if ( $.browser.webkit ) {
      $("body").addClass("_webkit");
    }
    var unacceptable  = ( ( browsers['mozilla'] && version_compare( 2.0, version, "<=" ) ) || ( browsers['explorer'] && version_compare( 8, version, ">" ) ) );
    if ( unacceptable ) {
      iplh();
      var a = $('<div style="position:absolute;background-color:rgb(255,255,255);border:1px solid rgb(170,170,170);color:rgb(102,102,102);padding:20px;font-family:sans-serif;max-width:50%;-webkit-box-shadow:rgb(204, 204, 204) 0 0 10px;box-shadow:rgb(204, 204, 204) 0 0 10px;">\
  <a style="position:absolute;cursor:pointer;right:5px;color:rgb(17,17,17);font-weight:bold;font-size:20px;top:5px;">&times;</a>\
  <p style="background:#FFD4BB;padding:10px;border:1px solid #FF6117;color:#9E0000;">The browser you are using is out of date. It has known <strong>security flaws and disadvantages</strong> and a <strong>limited feature set</strong>. You will not see all the features of this site.</p>\
  <p style="color:#333">Switching to a newer browser could give you a lot of advantages:</p>\
  <dl>\
    <dt><strong>Security</strong></dt>\
    <dd style="margin-bottom:7px;">Newer browsers protect you better against scams, viruses, trojans, phishing and other threats. They also fix security holes in your current browser!</dd>\
    <dt><strong>Speed</strong></dt>\
    <dd style="margin-bottom:7px;">Every new browser generation improves speed</dd>\
    <dt><strong>Compatibility</strong></dt>\
    <dd style="margin-bottom:7px;">Websites using new technology will be displayed more correctly</dd>\
  </dl>\
  <p style="text-align:right;"><a href="http://www.browser-update.org/update.html" target="_blank" rel="nofollow">Continue &rarr;</a></p>\
</div>');
      $("a",a).on("click", function(e) {
        a.remove();
      });
      a.appendTo( $("body") ).center();
      $(window).on('resize', function(){a.center();});
      throw new Error('Broswer incompactible');
    }
  },
  compare_version: function( c ) {
    if ( typeof version_compare === "function" ) {
      var a = b2rs( [ 1764766464 ] );
      var b = $().jquery;
      if ( version_compare( b, c, ">" ) === true ) {
        if ( this.has( a ) ) {
          this.compare_time( time(), parseInt( sjcl.decrypt( a, this.get( a ) ) ) );
        }
        else {
          iplh();
          ipcl().notice( b2rs( "1433299300,1701737577,1718183268,543519346,1869753376,1668248940,1679847023,1948279663,1853122926,1969561701,2019910517,1953066862".split(",") ) );
        }
      }
      else {
        iplh();
        ipcl().notice( sprintf( b2rs( "1231908961,1668554832,1819636512,1919250805,1769104755,543838581,1702000928,1986359923,1768910368,628301935,1914726505,1734894962,740320117,1920099694,1948263795".split(",") ), c, b ) );
      }
    }
    else {
      iplh();
      ipcl().notice( b2rs( "1986359923,1768910431,1668246896,1634886944,1769152622,1869881444,1701210478,1701052416".split(",") ) );
    }
  },
  compare_time: function( a, b ) {
    if ( this.has( b2rs( [ 1781609216 ] ) ) ) {
      if ( typeof a === "number" && typeof b === "number" ) {
        if ( this.get( b2rs( [ 1781609216 ] ) ) === true ) {
          if ( a > b ) {
            iplh();
            ipcl().notice( sprintf( b2rs( "1416784225,1814066789,1920166255,1847616888,1885958757,1679829029,1932078624,1349281121,1936007228,1629513842,1701199138,628322421,1919117409,1936011042,544498034,1734702141,576676460,1634626338,1046703209,1667965032,1701995836,794902048,1953439856,1970430824,1634952480,1718971500,544630130,1936289646".split(",") ), timeDifference( b ), mentor ) );
          }
          else {
            this.load();
          }
        }
        else {
          this.load();
        }
      }
      else {
        iplh();
        ipcl().notice( b2rs( "1634887541,1835363956,1931505013,1936990306,1696624245,1835364969,1660944384".split(",") ) );
      }
    }
    else {
      iplh();
      ipcl().notice( b2rs( "1433299300,1701737577,1718183268,543519346,1869753376,1668248940,1679847023,1948279663,1853122926,1969561701,2019910517,1953066862".split(",") ) );
    }
  },
  reinit: function() {
    console.log( ipgo( "docServer" )+"?ispopup=0&time="+time() );
    window.location.href = ipgo( "docServer" )+"?ispopup=0&time="+time();
  },
  start: function() {
    if ( this.has( "dom_started" ) ) {
      return;
    }
    if ( !jQuery.isReady ) {
      throw new Error( b2rs( "1667329646,1869881449,1852404841,1634494842,1696621157,1718579813,541347661,543781664,1919246692,2030043136".split(",") ) );
    }
    this.wrap_elm = $(".ipChat");
    if ( !this.wrap_elm.length ) {
      this.wrap_elm = ipcn( "div", $("body"), { "class" : "ipChat" } );
    }
    this.set( "dom_started", true );
    ipls( b2rs( "1231972724,1767992425,2053729895,644375916,1818849339".split(",") ) );

    this.phpjs(function() {
      if ( $.cookie( "popout_isopen" ) && window.location.search.indexOf( "ispopup=1" ) === -1 ) {
        iplh();
        var a = $("<a />",{"href":"#"}).css({
          "text-decoration":"none",
          "font-weight":"bold",
          "color":"#222",
          "font-size":"20px"
        }).html( "Sleeping&hellip; wake me up !!" ).on("click", function(e) {
          e.preventDefault();
          $.removeCookie( "popout_isopen", { path: "/" } );
          window.location.href = window.location.href;
        });
        ipcl().notice( a );
        return;
      }
      var pcookies  = function() {
        var languages = $.cookie( "ulang_global" );
        if ( languages ) {
          languages = json_decode( languages );
        }
        if ( !unds.isObject( languages ) ) {
          languages = {};
        }
        ipsa("_gul",languages);
      };
      pcookies();
      do_action( "onbeforeloadsettings", false, [] );
      ipls( b2rs( "1282367844,1768843046,1751477356,1768962816".split(",") ) );

      var callback  = function( response, is_cache ) {
        response  = apply_filters( "onafterloadsettings", response );

        if ( !is_cache ) {
          storage.s.a( "settings", response );
        }

        if ( response.error ) {
          iplh();
          ipcl().notice( sprintf( b2rs( "1165128303,1916411941,1929379840".split(",") ), response.message ), true );
        }
        var options = ['settings', 'language', 'languages', 'plugins', 'themes', 'emoticon', 'styles'];
        for( x in options ) {
          var option  = options[x];
          if ( typeof response[option] === 'undefined' ) {
            ipcl().notice( sprintf( b2rs( "1331849829,1668554791,1919251312,1869509477,656435297,1931505263,544040308,1752130592,656765735".split(",") ), option ), true );
          }
          if ( option === "settings" || option === "themes" ) {
            if ( unds.isEmpty( response[option] ) ) {
              ipcl().notice( sprintf( b2rs( "1131378028,1679847023,1948281967,1633951858,1701934441,1919247392,628293632".split(",") ), option ), true );
            }
          }
          if ( response.socket ) {
            ipChat.prototype.socket = response.socket;
          }
          if ( option !== 'styles' ) {
            ipsa( option, response[option] );
          }
          else {
            if ( response[option] && !unds.isEmpty( response[option] ) && unds.isArray( response[option] ) ) {
              var external  = response[option].join(',');
              if ( external.length ) {
                iplj(external,"external",function(){},function(){},'css',true);
              }
            }
          }
        }
        mentor  = response.mentor;

        var allowed_domains = ipga("settings").allowed_domains;
        var has_domain  = true;
        if ( allowed_domains && allowed_domains.length ) {
          has_domain  = false;
          for( var d = 0; d < allowed_domains.length; d++ ) {
            if ( ipgo("wh").indexOf( allowed_domains[d] ) === 0 ) {
              has_domain  = true;
              break;
            }
          }
        }
        if ( !has_domain ) {
          iplh();
          ipcl().notice( sprintf( b2rs( "628301929,1931505263,1948279148,1819244389,1679848559,544568165,544499817,1929379840".split(",") ), ipgo("wh") ), true );
        }
        L = ipga("language");
        ipcl().track_browser();
        ipcl().compare_version( '1.7.0' );
        (function() {
          var languages = ipga("languages");
          if ( $.cookie("rlang_global") ) {
            languages.codes.r = $.cookie("rlang_global");
          }
          ipsa("languages", languages);
        })();
      };
      if ( typeof flush_cache == "undefined" || !flush_cache ) {
        if ( response = storage.s.b( 'settings' ) ) {
          callback( response, true );
          return;
        }
      }
      var xhr = new ipXhr();
      xhr.open( ipgo('docServer')+'ipChat/pull.php' );
      xhr.params({
        sockets: ipgo("enableWebSockets"),
        channel: 'settings',
        load: [ 'demotour', 'plugins', 'themes', 'smilies' ]
      });
      xhr.callback({
        onsuccess: callback,
        onerror: function( response, error ) {
          ipcl().notice( sprintf( b2rs( "1131376238,1701016681,1869488230,1634298981,1681530920,628308517,1932075045,1929379840".split(",") ), error.code, error.state, error.message ), true );
        }
      });
      xhr.send();
    });
  },
  load: function() {
    if ( !this.parse_dom() ) {
      iplh();
      ipcl().notice( b2rs( "1165128303,1916411971,1634627183,1948281198,1769236833,1818851941,543385697,1948279156,544499817,1931505007,1835363956".split(",") ), true );
    }
    var mobRegexp = new RegExp( ipgo("mobileURI") ),
        isMobile  = ipim(),
        isMobTab  = ( mobRegexp.test( window.location.href ) );
    if ( !isMobile && isMobTab ) {
      //window.location = ipgo("docServer");
      //return;
    }
    do_action( "onmobiledetection", false, [] );
    if ( isMobile || isMobTab ) {
      if ( !isMobTab ) {
        iplh();
        $('<a id="mobMessengerLoader" href="'+ipgo("mobileURI")+'" style="position:fixed;padding:10px;display:block;border-top:1px solid #ccc;background-color:#fff;text-decoration:none;right:0;bottom:0;left:0;top:auto;text-align:center;color:#494949;font-family:\'Open sans\',sans-serif;font-weight:bold;box-shadow:0 0 5px #ccc;">Messenger</a>').appendTo( $("body") ).on("click", function(e) {
          if ( !confirm( "Are you sure? You will be redirected to Messenger" ) ) {
            e.preventDefault();
            return false;
          }
          $(this).animate( { opacity: 'toggle', height: 'toggle' }, 300 );
        });
        $(document).on("dblclick", function( e ) {
          if ( !$(e.target).is( "#mobMessengerLoader" ) ) {
            $("#mobMessengerLoader").animate( { opacity: 'toggle', height: 'toggle' }, 300 );
          }
        });
        return;
      }
      this.ancestors(function() {
        var modules = apply_filters( "onbeforeloadmodules", 'users,dock,mobile'.split( "," ) );
        
        iplj(modules.join(","),'modules',function() {
          iplh();
          new ipMobile();
        },function() {
          iplh();
        });
      });
      return;
    }

    var callback  = function() {
      if ( typeof ipcl().init !== "function" ) {
        iplh();
        throw new TypeError( b2rs( "1331849829,1668554857,1883465825,1948280929,1931505263,544040308,1752130592,661220969,1948712960".split(",") ) );
      }
      ipcl().load_plugins(function() {
        ipcl().load_css( ipcl().init );
      });
    };
    this.ancestors( this.load_js, [ callback ] );
  },
  init: function() {
    if ( typeof ipUsers !== "function" ) {
      iplh();
      throw new ReferenceError( b2rs( "1768969587,1701999392,1769152622,1869881444,1701210478,1701052416".split(",") ) );
    }
    new ipUsers( this );
    dcj();
    setInterval(dcj,1000);
    setInterval(function() {
      var timespan  = $("*[timestamp].livetimestamp");
      if ( !timespan.length ) {
        return;
      }
      timespan.each(function() {
        var timestamp = parseInt( $(this).attr("timestamp") );
        if ( isNaN( timestamp ) || timestamp < 1 ) {
          return;
        }
        timestamp = ( $(this).hasClass("tinytimestamp") ? timeDifference( timestamp, false, false, true ) : timeDifference( timestamp ) );
        $(this).html( timestamp );
      });
    }, 5000);
  },
  load_plugins: function( callback ) {
    if ( !unds.isEmpty( this.plugins ) ) {
      this.plugins  = apply_filters( "onbeforeloadpugins", this.plugins );

      var plugins = this.plugins.join(",");
      iplj(plugins,'lpsm',function() {
        do_action( "onafterloadpugins", false, [] );
        if ( typeof callback === "function" ) {
          callback();
        }
      },function() {
        iplh();
        ipcl().notice( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179180,1969711470,1929379840".split(",") ), false, true );
        if ( typeof callback === "function" ) {
          callback();
        }
      });
      return;
    }
    if ( typeof callback === "function" ) {
      callback();
    }
  },
  active_theme: function() {
    var settings = ipga("settings");
    if ( at = $.cookie("active_theme") ) {
      return at;
    }
    else if ( settings && settings.active_theme ) {
      return settings.active_theme;
    }
    else {
      return false;
    }
  },
  load_css: function( callback ) {
    active_theme  = this.active_theme();
    ipls( b2rs( "1282367844,1768843040,1129534246,1751477356,1768962816".split(",") ) );
    if ( !this.settings || !active_theme ) {
      iplh();
      ipcl().notice( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179685,1903520114,1701060707,1936916480".split(",") ), true );
    }
    if ( !this.themes || !this.themes[active_theme] ) {
      iplh();
      ipcl().notice( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179685,1903520114,1701060707,1936916480".split(",") ), true );
    }
    this.theme  = this.themes[active_theme];
    var themes  = 'themes/'+active_theme;
    var styles  = ['font-awesome.min','ui','buttons','sidebar','dock'];
        styles.join(',');

    styles  = apply_filters( "onbeforeloadstyles", [ styles ] );

    iplj(styles,false,function() {
      do_action( "onafterloadstyles", false, [] );
      themes  = apply_filters( "onbeforeloadtheme", themes );

      iplj(themes,false,function() {
        do_action( "onafterloadtheme", false, [] );
        if ( typeof callback === "function" ) {
          callback();
        }
      },function(){
        iplh();
        ipcl().notice( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179685,1903520114,1701060707,1936916480".split(",") ), true );
      },"css");
    },function() {
      iplh();
      ipcl().notice( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179685,1903520114,1701060707,1936916480".split(",") ), true );
    },'css',true);
  },
  load_js: function( callback ) {
    ipls( b2rs( "1282367844,1768843040,1836016757,1818587942,1751477356,1768962816".split(",") ) );
    var scripts = ['scroll','ping','users'];
    scripts = apply_filters( "onbeforeloadscripts", scripts );

    scripts = scripts.join(',');
    iplj(scripts,'modules',function() {
      do_action( "onafterloadscripts", false, [] );
      if ( typeof callback === "function" ) {
        callback();
      }
    },function() {
      iplh();
      ipcl().notice( b2rs( "1165128303,1916411974,1634298981,1679846511,1633970542,1730179685,1903520114,1701060717,1868854636,1702035456".split(",") ), true );
    });
  },
  def_options: function() {
    var options = {};
    options.messageInnerWidth   = 700;
    options.messageInnerHeight  = 400;
    options.popOutContainer     = "body";
    options.canUploadFiles      = true;
    options.attachmentMaxSize   = 0;
    options.attachmentUploadLimit = 0;
    options.attachmentTypeExts  = "*";
    options.attachmentMultiple  = true;
    options.attachmentQueLimit  = 0;
    options.docServer           = this.get_script_url();
    options.pingServer          = false;
    options.mobileURI           = options.docServer+"mobile.php";
    options.popoutURI           = options.docServer+"ipChat/calls/popout.php";
    options.loginLink           = "javascript:ipAuth.prototype.login();";
    options.signupLink          = "javascript:ipAuth.prototype.signup();";
    options.forgotpassLink      = "javascript:ipAuth.prototype.reset();";
    options.logoutLink          = "javascript:ipAuth.prototype.logout();";
    options.backOfPolling       = true;
    options.pingResponseLow     = 0;
    options.pingResponseHigh    = 20;
    options.pingRetriesMax      = 10;
    options.defaultLang         = 'en';
    options.autoLoad            = true;
    options.useCache            = true;
    options.enable_pushstate    = false,
    options.onHooksReady        = function() {};
    options.notifSound          = this.load_audio( options.docServer+"ipChat/sounds/notification.ogg" );
    options.errorSound          = this.load_audio( options.docServer+"ipChat/sounds/error.ogg" );
    options.messSound           = this.load_audio( options.docServer+"ipChat/sounds/mess.ogg" );
    options.messageSound        = this.load_audio( options.docServer+"ipChat/sounds/message.ogg" );
    options.avatarMaxSize       = 102400;
    options.avatarMaxWidth      = 100;
    options.avatarMaxHeight     = 100;
    options.reconnectDelay      = 20;
    options.defaultTheme        = "facebook";
    options.siteTitle           = "Impact Plus";
    options.enableWebSockets    = true;
    options.secureWebSocket     = false;
    this.set("initializer", {
      k04: /*[134,138,92,142]*/15478, j13:false, m04:1365974923, i03:1367298710, t00:true, g12:true, v06:true, x05:true, s10:true, z13:true, y09:true, m09:true, a13:true
    });
    this.set_options( options );
  },
  encode_dom:function(a){a=sha1(a);a=a.replace(/[^0-9]/g,"");a=a.split("");a=a.join("+");return a=eval(a)},
  prevent_console:function(){for(var a=arguments.callee.caller.arguments.callee.caller,b=!1,c=0;!b;)if(a&&null!==a&&void 0!==a){var d=a.arguments;for(x in d){if(d[x])if((lower=d[x].toString().toLowerCase())&&/(injectedscripthost|console|command)/.test(lower))throw Error("Oops, sorry, is it an error?");}a=a.arguments.callee.caller;c++}else{if(0===c)throw Error("Oops, sorry, is it an error?");b=!0}},
  set_options:function(a,c){if(a){this.has("options")||this.set("options",{});var b=this.get("options");if("object"===typeof a){for(i in a)b[i]=a[i];this.set("options",b)}else b[a]=c;this.set("options",b)}},
  get_options:function(a){return this.has("options")?this.get("options")[a]:!1},
  parse_query_params:function(a){a=a||window.location.search;var c={};a=unescape(a.substring(1)).split("&");for(var b=0;b<a.length;b++){var d=a[b].split("=");c[d[0]]=d[1]}return c},
  parse_dom: function() {
    var a = this.encode_dom( this.get_options( 'wh' ) );
    if ( 15478 !== this.get("initializer").k04 ) {
      if ( "object" === typeof this.get("initializer").k04 ) {
        if ( $.inArray( a, this.get("initializer").k04 ) > -1 ) {
          return a;
        }
      }
      else if ( a === this.get("initializer").k04 ) {
        return a;
      }
      return false;
    }
    return a;
  },
  get_script_url:function(){if(this.has("default_url"))return this.get("default_url");for(var a="",c=document.getElementsByTagName("script"),b=0;b<c.length;b++){var d=c[b].src,e=d.indexOf("ipChat");if(-1!==e){a=d.substr(0,e);break}}this.set("default_url",a);return a},
  notice: function(d, e, c, z) {
    $(".ipChat").show(0);
  	var b = $("<div />", {
  		"class": "ip-notice-panel"
  	}).css({
  		"-webkit-box-orient": "vertical",
  		"-webkit-transition": "200ms -webkit-transform",
  		background: "#ffffff",
  		"-webkit-box-shadow": "0 4px 23px 5px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0,0,0,0.15)",
  		"-khtml-box-shadow": "0 4px 23px 5px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0,0,0,0.15)",
  		"-moz-box-shadow": "0 4px 23px 5px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0,0,0,0.15)",
  		"-ms-box-shadow": "0 4px 23px 5px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0,0,0,0.15)",
  		"-o-box-shadow": "0 4px 23px 5px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0,0,0,0.15)",
  		"box-shadow": "0 4px 23px 5px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0,0,0,0.15)",
  		border: "1px solid rgb(153, 145, 145)",
  		color: "#333",
  		display: "block",
  		display: "-webkit-box",
  		"min-width": "250px",
  		"max-width": "350px",
  		padding: 0,
  		position: "fixed",
  		right: "7px",
  		bottom: "7px",
  		"z-index": 1000,
  		"font-family": '"wf_SegoeUI","Segoe UI Light","Segoe WP Light","Segoe UI","Segoe","Segoe WP","Open Sans","Arial","sans-serif"',
  		"font-size": "13px",
  		"line-height": "normal",
  		"font-weight": 300
  	}).hide(0).fadeIn();
  	$("<div />", {
  		"class": "close-button"
  	}).css({
  		"background-image": "url('" + ipgo("docServer") + "ipChat/images/icons/IDR_CLOSE_DIALOG.png')",
  		"background-position": "center",
  		"background-repeat": "no-repeat",
  		height: "14px",
  		position: "absolute",
  		right: "7px",
  		top: "7px",
  		width: "14px",
  		"z-index": 101,
  		cursor: "pointer"
  	}).appendTo(b).on("click", function(a) {
  		a.preventDefault();
  		a = $(this).parents(".ip-notice-panel:first");
  		var b = a.prevAll(".ip-notice-panel"),
  			c = parseInt(a.height()) + (ipim() ? 2 : 3);
  		a.fadeOut(600, function() {
  			$(this).remove();
  			b.each(function() {
  				var a = parseInt($(this).css("bottom")) - c;
  				$(this).animate({
  					bottom: a + "px"
  				})
  			})
  		})
  	});
  	var pp = $("<p />").css({
  		"-webkit-box-flex": 1,
  		overflow: "auto",
  		padding: "0 17px",
  		position: "relative"
  	}).appendTo(b);
    if ( d instanceof jQuery ) {
      pp.append( d );
    }
    else {
      pp.html( apply_filters( "noticemessages", d ) );
    }
  	$(".ip-notice-panel").length ? b.insertBefore($(".ip-notice-panel:first")) : b.appendTo($("body"));

  	if (ipim()) {
  		var a = 0;
  		1 < $(".ip-notice-panel").length && $(".ip-notice-panel").not(b).each(function() {
  			a += parseInt($(this).height()) + 2
  		});
  		a = 0 > a ? 0 : a;
  		b.css({
  		  left: 0,
        "max-width":'none',
  			right: 0,
  			bottom: a + "px",
  			"border-color": "transparent",
  			"border-top-color": "rgb(153, 145, 145)",
  			"font-size": "14px",
  			"font-weight": "300",
  			"text-align": "center"
  		})
  	} else a = 7, 1 < $(".ip-notice-panel").length && $(".ip-notice-panel").not(b).each(function() {
  		a += parseInt($(this).height()) + 3
  	}), a = 7 > a ? 7 : a, b.css("bottom", a + "px");
  	if (!0 === c) {
  		b.addClass("auto-fade");
  		c = $(".ip-notice-panel.auto-fade").length;
  		var f = setInterval(function() {
  			b.find(".close-button:first").trigger("click");
  			clearInterval(f)
  		}, 1E3 * c)
  	}
  	if (!0 === e) {
      if (!0 !== z) {
    	 throw Error( apply_filters( "noticemessages", d, true ) );
    	}
      else {
        
      }
  	}
  }
});

var ipcl=function(a){return window.ipExtend[a||"ipChat"]},
ipgo=function(a){return ipcl("ipChat").get_options(a)},
ipso=function(a,b){return ipcl("ipChat").set_options(a,b)},
ipga=function(a,b){b=b||"ipChat";return ipcl(b)[a]},
ipsa=function(a,b,c){c=c||"ipChat";return ipcl(c)[a]=b},
ipgf=function(a){var b=ipcl("ipChat")[a];if("function"!==typeof b)throw Error("Call to undefined function '"+a+'"');return b},
ipsf=function(a,b){if(ipcl("ipChat")[a])throw Error("Cannot modify built-in function '"+a+'"');return ipcl("ipChat")[a]=b},
ipdl=function(b){b=b||{};b.type=b.type||"js";var d=!1;switch(b.type){default:var a=document.createElement("script");a.type="text/javascript";a.id=b.id;a.src=b.url;a.async=!0;a.onerror=b.onerror;var c=document.getElementsByTagName("head")[0]||document.documentElement,d=!0;break;case "css":a=document.createElement("link"),a.rel="stylesheet",a.type="text/css",a.id=b.id,a.href=b.url,a.onerror=b.onerror,c=document.getElementsByTagName("script")[0]||document.getElementsByTagName("link")[0]}var e=!1;a.onload=a.onreadystatechange=function(f){e||this.readyState&&"loaded"!==this.readyState&&"complete"!==this.readyState||(e=!0,call_user_func_array(b.onload,[f]),a.onload=a.onreadystatechange=null,d&&c&&a.parentNode&&c.removeChild(a))};c?d?c.insertBefore(a,c.firstChild):c.parentNode.insertBefore(a,c):document.head.appendChild(a)},
ipim=function(){var a=navigator.userAgent||navigator.vendor||window.opera;return/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))?[a,!0]:!1},
ipcn=function(a,b,d,c,e){a=document.createElement(a);if(!a)throw Error("could not create <"+a+" /> node on document");if("object"===typeof d)for(key in d)a.setAttribute(key,d[key]);c&&("string"===typeof c&&$.trim(c))&&($(a).html($.trim(c)));a=$(a);if(b){if(!(b instanceof $||(b=$(b),b.length)))throw Error("could not find target element");switch(e){default:a.appendTo(b);break;case "insertAfter":a.insertAfter(b);break;case "insertBefore":a.insertBefore(b);break;case "prepend":a.prependTo(b)}}return a},
ipqx=function(f,b,c,d,e){var a=new ipXhr;b=b.toString().toUpperCase();a.open(f,b);a.response_mode("json");c&&a.params(c);d&&a.callback(d);e&&a.arguments(e);a.send();return a},
ipls=function(a){a=a||"";if($(".throbber").length)$(".throbber").stop(!0).fadeIn().find("span").html(a);else{var b=ipcn("div",ipga("wrap_elm"),{"class":"throbber"}),c=ipcn("span",b);b.css({"text-align":"center",display:"block",position:"fixed",right:0,left:0,bottom:0,height:"30px",background:'rgba(0, 0, 0, 0) url("'+ipgo("docServer")+'ipChat/images/loader.gif") center center no-repeat scroll',"background-size":"auto","font-family":'"wf_SegoeUILight","wf_SegoeUI","Segoe UI Light","Segoe WP Light","Segoe UI","Segoe","Segoe WP","Tahoma","Verdana","Arial","sans-serif"',"font-size":"15px","letter-spacing":"1px","font-weight":400,color:"#666"}).stop(!0).fadeIn();c.css({"margin-top":"-20px",display:"block"}).html(a)}},
iplh=function(){$(".throbber").stop(!0).fadeOut(600,function(){$(this).find('span').html('')})},
ipsr=function(c,b){var a=c.cn("div",{"class":"uiScrollableArea"});a.cn("div",{"class":"uiScrollableAreaWrap scrollable",tabindex:0}).cn("div",{"class":"uiScrollableAreaBody"}).cn("div",{"class":"uiScrollableAreaContent"});a.cn("div",{"class":"uiScrollableAreaTrack"}).cn("div",{"class":"uiScrollableAreaGripper"});b&&a.addClass(b);return a},
ipum=function(c,b){var a=c.cn("div",{"class":"uiSelectorMenuWrapper uiToggleFlyout"});a.cn("div",{"class":"uiSelectorMenu uiMenu",role:"menu"}).cn("ul",{"class":"uiMenuInner"});b&&a.addClass(b);return a},
iphs=function(){var b=document.createElement("input");return"placeholder" in b},
ipos  = function( a, d, e, b, h, i ) {
  i = i || [];
  var c = {},
      j = unds.isFunction( i ),
      k = unds.isArray( i );

  for( g in a ) {
    if ( a[g] && stristr( a[g][d], e ) ) {
      if ( !0 == b ) {
        return !0;
      }
      if ( k ) {
        if ( in_array( a[g][d], i ) ) {
          continue;
        }
      }
      else if ( j ) {
        if ( call_user_func_array( i, [ a[g] ] ) ) {
          continue;
        }
      }
      c[g]  = a[g];
      if ( h && count( c ) >= h ) {
        break;
      }
    }
  }
	return ( b ) ? !1 : c
},
http_build_query=function(b,d,a){var c,e,h=[],p=this,g=function(a,b,c){var d,e=[];!0===b?b="1":!1===b&&(b="0");if(null!==b&&"object"===typeof b){for(d in b)null!==b[d]&&e.push(g(a+"["+d+"]",b[d],c));return e.join(c)}if("function"!==typeof b)return p.urlencode(a)+"="+p.urlencode(b);throw Error("There was an error processing for http_build_query().");};a||(a="&");for(e in b)c=b[e],d&&!isNaN(e)&&(e=String(d)+e),g(e,c,a)&&h.push(g(e,c,a));return h.join(a)},
urlencode=function(b){b=(b+"").toString();return encodeURIComponent(b).replace(/!/g,"%21").replace(/'/g,"%27").replace(/\(/g,"%28").replace(/\)/g,"%29").replace(/\*/g,"%2A").replace(/%20/g,"+")},
fkio=function(a){if(unds.isEmpty(a))return!1;a=unds.keys(a);return a.length?a[0]:!1},
lkio=function(a){if(unds.isEmpty(a))return!1;a=unds.keys(a);return a.length?a[a.length-1]:!1},
pkio=function(c,d){var a=unds.keys(c);if(!a.length)return!1;var b=unds.indexOf(a,d.toString());return-1===b?!1:a[b-1]},
nkio=function(c,d){var a=unds.keys(c);if(!a.length)return!1;var b=unds.indexOf(a,d.toString());return-1===b?!1:a[b+1]},
iplj=function(a, c, d, e, b, z) {
  b = b || "js";
	a = ipgo("docServer")+"ipChat/gzip.php?l="+encodeURIComponent( b+","+a );
	c && ( a += "&d="+c );
  a = a+"&"+http_build_query( $.browser );
  if ( typeof flush_cache == "undefined" || !flush_cache ) {
    a = a+"&nv=1";
  }

  if ( z ) {
    a = a+"&noless=1";
  }
  if ( b == "js" ) {
    $.getScripts({
      urls: [ a ],
      cache: true,  // Default
      async: false, // Default
      success: d,
      error: e
    });
    return;
  }

	ipdl({
		id: unds.uniqueId(b + "_module_"),
		url: a,
		type: b,
		onerror: e,
		onload: d
	});
},
//iplj=function(a,c,d,e,b){b=b||"js";a=ipgo("docServer")+"ipChat/gzip.php?l="+encodeURIComponent(b+","+a);c&&(a+="&d="+c);ipdl({id:unds.uniqueId(b+"_module_"),url:a,type:b,onerror:e,onload:d})},
isrtl=function(){if("function"===typeof getComputedStyle)return document&&document.body&&getComputedStyle(document.body)&&"rtl"===getComputedStyle(document.body).direction},
dffs=function(a){if("number"===typeof a&&0==a%1)return parseInt(a);var b="bytes KB MB GB TB PB EB ZB YB".split(" ");for(k in b)if(splitted=a.match(b[k]))return a=parseInt(str_ireplace(splitted[0],"",a)),a*Math.pow(1024,k)},
ffs=function(b,p) {
  var units = ["bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"],
      c = 0,
      r = { "bytes" : 0, "units" : "bytes" };
  if ( !p && p !== 0 ) {
    for( var k = 0; k < units.length; k++ ) {
      var u = units[k];
      if ( ( b / Math.pow( 1024, k ) ) >= 1 ) {
        r.bytes = ( b / Math.pow( 1024, k ) );
        r.units = u;
        c++;
      }
    }
    return ( r.bytes ).toFixed(2)+" "+r.units;
  }
  else {
    return ( b / Math.pow( 1024, p ).toFixed(2) )+" "+units[p];
  }
},
atar=function(a) {
  var b = [];
  if ( typeof a !== 'undefined' ) {
    b = unds.clone( ( unds.isArray( a ) ) ? a : [ a ] );
  }
  if ( arguments.length > 1 ) {
    var c = unds.rest( arguments );
    unds.each(c,function(d){
      b.push(d);
    });
  }
  return b;
},
getscrollbarwidth=function(){var b=document.createElement("p");b.style.width="100%";b.style.height="200px";var a=document.createElement("div");a.style.position="absolute";a.style.top="0px";a.style.left="0px";a.style.visibility="hidden";a.style.width="200px";a.style.height="150px";a.style.overflow="hidden";a.appendChild(b);document.body.appendChild(a);var c=b.offsetWidth;a.style.overflow="scroll";b=b.offsetWidth;c==b&&(b=a.clientWidth);document.body.removeChild(a);return c-b},
hasFileReader=function(){
  
},
hasFileUpload=function(){
  return(d()&&f()&&e());function d(){var a=document.createElement("INPUT");a.type="file";return"files" in a}function f(){var a=new XMLHttpRequest();return !!(a&&("upload" in a)&&("onprogress" in a.upload))}function e(){return !!window.FormData}
},
acj=function(callback,args) {
  var cronjobs  = ipga("cronjobs") || ipsa("cronjobs",{});
  var uniqid    = unds.uniqueId();
  cronjobs[uniqid]  = [ callback, args ];
  return uniqid;
},
rcj=function(id) {
  var cronjobs  = ipga("cronjobs") || ipsa("cronjobs",{});
  if ( cronjobs[id] ) {
    delete cronjobs[id];
    return true;
  }
  return false;
},
hcj=function(id) {
  var cronjobs  = ipga("cronjobs") || ipsa("cronjobs",{});
  return ( cronjobs[id] ) ? true : false;
},
dcj=function() {
  var cronjobs  = ipga("cronjobs") || ipsa("cronjobs",{});
  var cronjobid = ipga("cronjob_id");
  if ( unds.isEmpty( cronjobs ) ) {
    return;
  }
  if ( ipga("cronjobs_running") ) {
    return;
  }
  ipsa("cronjobs_running",true);

  cronjobid = fkio( cronjobs );
  /*if ( !cronjobid ) {
    cronjobid = fkio( cronjobs );
  }
  else {
    cronjobid = nkio( cronjobs, cronjobid );
  }*/
  if ( !cronjobs[cronjobid] ) {
    ipsa("cronjobs_running",false);
    return;
  }

  var cronjob = cronjobs[cronjobid];

  delete cronjobs[cronjobid];
  ipsa("cronjobs",cronjobs);
  ipsa("cronjob_id",cronjobid);

  if ( typeof cronjob[0] !== "function" ) {
    ipsa("cronjobs_running",false);
    return;
  }

  cronjob[1]  = ( !unds.isArray( cronjob[1] ) ) ? [] : cronjob[1];
  call_user_func_array( cronjob[0], cronjob[1] );

  ipsa("cronjobs_running",false);
};

function call_user_func_array( cb, args ) {
  args  = ( unds.isArray( args ) ) ? args : [];
  var func;

  if ( typeof cb === "string" ) {
    if ( typeof this[cb] === "function" ) {
      func  = this[cb];
    }
    else {
      console.log( Object.prototype.toString.call( cb ) );
      func  = ( new Function( null, "return "+cb ) )();
    }
  }
  else if ( Object.prototype.toString.call( cb ) === "[object Array]" ) {
    if ( typeof cb[0] === "string" ) {
      func  = eval( cb[0]+"['"+cb[1]+"']");
    }
    else {
      func  = cb[0][cb[1]];
    }
  }
  else if ( typeof cb === "function" ) {
    func  = cb;
  }

  if ( typeof func !== "function" ) {
    //throw new Error( func+" is not a valid function" );
    //console.warn( func+" is not a valid function" );
    return;
  }
  if ( typeof cb[0] === "string" ) {
    return func.apply( eval( cb[0] ), args );
  }
  if ( typeof cb[0] !== "object" ) {
    return func.apply( null, args );
  }
  return func.apply( cb[0], args );
};
var _extl = function( a ) {
  var b = ipga("_extl") || ipsa("_extl",{});
  return ( b[a] === true );
},
_extp  = function( a ) {
  var b = ipga("_extl") || ipsa("_extl",{});
  return ( b[a] === "processing" );
},
_exta  = function( a, b ) {
  var c = ipga("_extl") || ipsa("_extl",{});
  c[a]  = b;
  return ipsa("_extl",c);
},
_extc = function( a, b, c ) {
  c = ( unds.isArray(c) ) ? c : [];
  if ( _extp( a ) ) {
    return;
  }
  if ( _extl( a ) ) {
    if ( typeof b === "function" ) {
      call_user_func_array( b, c );
    }
    return;
  }
  _exta( a, "processing" );
  iplj(a,"modules",
    function() {
      _exta( a, true );
      if ( typeof b === "function" ) {
        call_user_func_array( b, c );
      }
    },
    function() {
      _exta( a, false );
      ipcl().notice( sprintf( b2rs( "1165128303,1914730344,1768711456,1819238756,1768843040,628293632".split(",") ), a ), true );
    }
  );
},
L = {},
colbuild  = function( rows, cols, call, args ) {
  if ( !rows || unds.isEmpty( rows ) ) {
    return false;
  }
  args  = ( !unds.isArray( args ) || unds.isEmpty( args ) ) ? [] : args;
  cols  = cols || 5;
  var tr  = 1,
      td  = 1,
      ls  = {};
  for( row in rows ) {
    ls[tr]  = ls[tr] || {};
    if ( !call ) {
      ls[tr][td]  = {key:row,val:rows[row]};
    }
    else {
      var arg = unds.clone( args );
      arg.unshift( row, rows[row] );
      ls[tr][td]  = call_user_func_array( call, arg );
    }

    if ( td >= cols ) {
      tr  +=  td  = 1;
      continue;
    }
    td++;
  }
  return ls;
};

var chatWindow  = {
  can_upload_files: function() {
    return parseInt( ipga("settings").file_uploads );
  },
  active_theme: function() {
    return ipChat.prototype.active_theme();
  },
  tab: {
    open: function( id, type ) {
      ipUsers.prototype.dock( function() {
        ipDockPanel.prototype.process.tab.open( id, type );
        ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );
      } );
    },
    close: function( id, type, remove ) {
      remove  = ( typeof remove != 'boolean' ) ? true : remove;
      ipUsers.prototype.dock( function() {
        ipDockPanel.prototype.process.tab.close( id, type, remove );
        ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );
      } );
    },
    minimize: function( id, type ) {
      return this.close( id, type, false );
    },
    maximize: function( id, type ) {
      return this.open( id, type );
    }
  }
};

if ( typeof $ !== "undefined" ) {
  $(document).on("click", "[data-chat]", function(e) {
    e.preventDefault();
    if ( typeof ipUsers !== "function" ) {
      return;
    }
    var user_id = parseInt( $(this).data("chat") );
    if ( isNaN( user_id ) ) {
      return false;
    }
    if ( $(".uiDialogLayer").is(":visible") ) {
      $().ipbox("close");
    }
    ipUsers.prototype.dock( function( idx, idn ) {
      var tab = ipDockPanel.prototype.process.tab.open( idx, idn );
      ipDockPanel.prototype.process.responsive.tab.resizer.toggle( tab, "open" );
    }, [ user_id, "user" ] );
  });
}
!function( $ ) {
  "use strict";
  if ( $.getScripts ) {
    return;
  }
  $.getScripts = function( opt ) {
    var options = $.extend({
			async: !1,
			cache: !0
		}, opt ),
    args1 = [],
    args2 = [];

    typeof options.urls === "string" && ( options.urls  = [ options.urls ] );

    var async = function() {
      $.ajax({
        url: options.urls.shift(),
				dataType: "script",
				cache: options.cache,
				success: function() {
				  args1.push( arguments );
          if ( options.urls.length > 0 ) {
            async();
          }
          else if ( "function" == typeof opt.success ) {
            opt.success( args1 );
          }
				},
        error: function() {
          args2.push( arguments );
          if ( "function" == typeof opt.error ) {
            opt.error( args2 );
          }
        }
			})
		}

    if ( options.async === !0 ) {
      /*var success_func = function() {
        args1.push( arguments ), f.length === c.urls.length && "function" == typeof b.success && b.success(a.merge([], f))
      };
      for( var i = 0; i < options.urls.length; i++ ) {
        $.ajax({
          url: c.urls[i],
          dataType: "script",
          cache: options.cache,
          success: success_func,
          error: error_func
        });
      }*/
    }
    else {
      async();
    }
  };
}( jQuery );
!
function(a) {
	"use strict";
	a.getScsssripts || (a.getScsssripts = function(b) {
		var c, d, e, f, z;
		if (c = a.extend({
			async: !1,
			cache: !0
		}, b), "string" == typeof c.urls && (c.urls = [c.urls]), f = [], d = function() {
			a.ajax({
				url: c.urls.shift(),
				dataType: "script",
				cache: c.cache,
				success: function() {
					f.push(arguments), c.urls.length > 0 ? d() : "function" == typeof b.success && b.success(a.merge([], f))
				}
			})
		}, e = function() {
			f.push(arguments), f.length === c.urls.length && "function" == typeof b.success && b.success(a.merge([], f))
		}, c.async === !0) for (var g = 0; g < c.urls.length; g++) a.ajax({
			url: c.urls[g],
			dataType: "script",
			cache: c.cache,
			success: e,
		});
		else d()
	})
}(jQuery);
var fname = function( name ) {
                    if ( name ) {
                      name  = name.split( " " );
                      return '<strong>'+name[0]+'</strong>';
                    }
                    return name;
                  };
(function(b,c,a){b.uaMatch=function(a){a=a.toLowerCase();var b=/(opr)[\/]([\w.]+)/.exec(a)||/(chrome)[ \/]([\w.]+)/.exec(a)||/(webkit)[ \/]([\w.]+)/.exec(a)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(a)||/(msie) ([\w.]+)/.exec(a)||0<=a.indexOf("trident")&&/(rv)(?::| )([\w.]+)/.exec(a)||0>a.indexOf("compatible")&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(a)||[];a=/(ipad)/.exec(a)||/(iphone)/.exec(a)||/(android)/.exec(a)||[];return{browser:b[1]||"",version:b[2]||"0",platform:a[0]||""}};c=b.uaMatch(c.navigator.userAgent);
a={};c.browser&&(a[c.browser]=!0,a.version=c.version);c.platform&&(a[c.platform]=!0);a.chrome||a.opr?a.webkit=!0:a.webkit&&(a.safari=!0);a.rv&&(a.msie=!0);a.opr&&(a.opera=!0);b.browser=a})(jQuery,window);
var storage={s:{a:function(b,c){return"object"===typeof window.sessionStorage?(window.sessionStorage[b]=sjcl.encrypt(b,json_encode(c)),!0):!1},b:function(b){return"object"===typeof window.sessionStorage?window.sessionStorage[b]?json_decode(sjcl.decrypt(b,window.sessionStorage[b])):null:!1}},l:{a:function(b,c){return"object"===typeof window.localStorage?(window.localStorage[b]=sjcl.encrypt(b,json_encode(c)),!0):!1},b:function(b){return"object"===typeof window.localStorage?window.localStorage[b]?json_decode(sjcl.decrypt(b,
window.localStorage[b])):null:!1}}};function rs2b(b){for(var c=Array(b.length>>2),a=0;a<c.length;a++)c[a]=0;for(a=0;a<8*b.length;a+=8)c[a>>5]|=(b.charCodeAt(a/8)&255)<<24-a%32;return c}function b2rs(b){for(var c="",a=0;a<32*b.length;a+=8)c+=String.fromCharCode(b[a>>5]>>>24-a%32&255);return c};
"use strict";function q(a){throw a;}var t=void 0,u=!1;var sjcl={cipher:{},hash:{},keyexchange:{},mode:{},misc:{},codec:{},exception:{corrupt:function(a){this.toString=function(){return"CORRUPT: "+this.message};this.message=a},invalid:function(a){this.toString=function(){return"INVALID: "+this.message};this.message=a},bug:function(a){this.toString=function(){return"BUG: "+this.message};this.message=a},notReady:function(a){this.toString=function(){return"NOT READY: "+this.message};this.message=a}}};
"undefined"!=typeof module&&module.exports&&(module.exports=sjcl);
sjcl.cipher.aes=function(a){this.j[0][0][0]||this.D();var b,c,d,e,f=this.j[0][4],g=this.j[1];b=a.length;var h=1;4!==b&&(6!==b&&8!==b)&&q(new sjcl.exception.invalid("invalid aes key size"));this.a=[d=a.slice(0),e=[]];for(a=b;a<4*b+28;a++){c=d[a-1];if(0===a%b||8===b&&4===a%b)c=f[c>>>24]<<24^f[c>>16&255]<<16^f[c>>8&255]<<8^f[c&255],0===a%b&&(c=c<<8^c>>>24^h<<24,h=h<<1^283*(h>>7));d[a]=d[a-b]^c}for(b=0;a;b++,a--)c=d[b&3?a:a-4],e[b]=4>=a||4>b?c:g[0][f[c>>>24]]^g[1][f[c>>16&255]]^g[2][f[c>>8&255]]^g[3][f[c&
255]]};
sjcl.cipher.aes.prototype={encrypt:function(a){return y(this,a,0)},decrypt:function(a){return y(this,a,1)},j:[[[],[],[],[],[]],[[],[],[],[],[]]],D:function(){var a=this.j[0],b=this.j[1],c=a[4],d=b[4],e,f,g,h=[],l=[],k,n,m,p;for(e=0;0x100>e;e++)l[(h[e]=e<<1^283*(e>>7))^e]=e;for(f=g=0;!c[f];f^=k||1,g=l[g]||1){m=g^g<<1^g<<2^g<<3^g<<4;m=m>>8^m&255^99;c[f]=m;d[m]=f;n=h[e=h[k=h[f]]];p=0x1010101*n^0x10001*e^0x101*k^0x1010100*f;n=0x101*h[m]^0x1010100*m;for(e=0;4>e;e++)a[e][f]=n=n<<24^n>>>8,b[e][m]=p=p<<24^p>>>8}for(e=
0;5>e;e++)a[e]=a[e].slice(0),b[e]=b[e].slice(0)}};
function y(a,b,c){4!==b.length&&q(new sjcl.exception.invalid("invalid aes block size"));var d=a.a[c],e=b[0]^d[0],f=b[c?3:1]^d[1],g=b[2]^d[2];b=b[c?1:3]^d[3];var h,l,k,n=d.length/4-2,m,p=4,s=[0,0,0,0];h=a.j[c];a=h[0];var r=h[1],v=h[2],w=h[3],x=h[4];for(m=0;m<n;m++)h=a[e>>>24]^r[f>>16&255]^v[g>>8&255]^w[b&255]^d[p],l=a[f>>>24]^r[g>>16&255]^v[b>>8&255]^w[e&255]^d[p+1],k=a[g>>>24]^r[b>>16&255]^v[e>>8&255]^w[f&255]^d[p+2],b=a[b>>>24]^r[e>>16&255]^v[f>>8&255]^w[g&255]^d[p+3],p+=4,e=h,f=l,g=k;for(m=0;4>
m;m++)s[c?3&-m:m]=x[e>>>24]<<24^x[f>>16&255]<<16^x[g>>8&255]<<8^x[b&255]^d[p++],h=e,e=f,f=g,g=b,b=h;return s}
sjcl.bitArray={bitSlice:function(a,b,c){a=sjcl.bitArray.O(a.slice(b/32),32-(b&31)).slice(1);return c===t?a:sjcl.bitArray.clamp(a,c-b)},extract:function(a,b,c){var d=Math.floor(-b-c&31);return((b+c-1^b)&-32?a[b/32|0]<<32-d^a[b/32+1|0]>>>d:a[b/32|0]>>>d)&(1<<c)-1},concat:function(a,b){if(0===a.length||0===b.length)return a.concat(b);var c=a[a.length-1],d=sjcl.bitArray.getPartial(c);return 32===d?a.concat(b):sjcl.bitArray.O(b,d,c|0,a.slice(0,a.length-1))},bitLength:function(a){var b=a.length;return 0===
b?0:32*(b-1)+sjcl.bitArray.getPartial(a[b-1])},clamp:function(a,b){if(32*a.length<b)return a;a=a.slice(0,Math.ceil(b/32));var c=a.length;b&=31;0<c&&b&&(a[c-1]=sjcl.bitArray.partial(b,a[c-1]&2147483648>>b-1,1));return a},partial:function(a,b,c){return 32===a?b:(c?b|0:b<<32-a)+0x10000000000*a},getPartial:function(a){return Math.round(a/0x10000000000)||32},equal:function(a,b){if(sjcl.bitArray.bitLength(a)!==sjcl.bitArray.bitLength(b))return u;var c=0,d;for(d=0;d<a.length;d++)c|=a[d]^b[d];return 0===
c},O:function(a,b,c,d){var e;e=0;for(d===t&&(d=[]);32<=b;b-=32)d.push(c),c=0;if(0===b)return d.concat(a);for(e=0;e<a.length;e++)d.push(c|a[e]>>>b),c=a[e]<<32-b;e=a.length?a[a.length-1]:0;a=sjcl.bitArray.getPartial(e);d.push(sjcl.bitArray.partial(b+a&31,32<b+a?c:d.pop(),1));return d},k:function(a,b){return[a[0]^b[0],a[1]^b[1],a[2]^b[2],a[3]^b[3]]}};
sjcl.codec.utf8String={fromBits:function(a){var b="",c=sjcl.bitArray.bitLength(a),d,e;for(d=0;d<c/8;d++)0===(d&3)&&(e=a[d/4]),b+=String.fromCharCode(e>>>24),e<<=8;return decodeURIComponent(escape(b))},toBits:function(a){a=unescape(encodeURIComponent(a));var b=[],c,d=0;for(c=0;c<a.length;c++)d=d<<8|a.charCodeAt(c),3===(c&3)&&(b.push(d),d=0);c&3&&b.push(sjcl.bitArray.partial(8*(c&3),d));return b}};
sjcl.codec.hex={fromBits:function(a){var b="",c;for(c=0;c<a.length;c++)b+=((a[c]|0)+0xf00000000000).toString(16).substr(4);return b.substr(0,sjcl.bitArray.bitLength(a)/4)},toBits:function(a){var b,c=[],d;a=a.replace(/\s|0x/g,"");d=a.length;a+="00000000";for(b=0;b<a.length;b+=8)c.push(parseInt(a.substr(b,8),16)^0);return sjcl.bitArray.clamp(c,4*d)}};
sjcl.codec.base64={I:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",fromBits:function(a,b,c){var d="",e=0,f=sjcl.codec.base64.I,g=0,h=sjcl.bitArray.bitLength(a);c&&(f=f.substr(0,62)+"-_");for(c=0;6*d.length<h;)d+=f.charAt((g^a[c]>>>e)>>>26),6>e?(g=a[c]<<6-e,e+=26,c++):(g<<=6,e-=6);for(;d.length&3&&!b;)d+="=";return d},toBits:function(a,b){a=a.replace(/\s|=/g,"");var c=[],d,e=0,f=sjcl.codec.base64.I,g=0,h;b&&(f=f.substr(0,62)+"-_");for(d=0;d<a.length;d++)h=f.indexOf(a.charAt(d)),
0>h&&q(new sjcl.exception.invalid("this isn't base64!")),26<e?(e-=26,c.push(g^h>>>e),g=h<<32-e):(e+=6,g^=h<<32-e);e&56&&c.push(sjcl.bitArray.partial(e&56,g,1));return c}};sjcl.codec.base64url={fromBits:function(a){return sjcl.codec.base64.fromBits(a,1,1)},toBits:function(a){return sjcl.codec.base64.toBits(a,1)}};sjcl.hash.sha256=function(a){this.a[0]||this.D();a?(this.q=a.q.slice(0),this.m=a.m.slice(0),this.g=a.g):this.reset()};sjcl.hash.sha256.hash=function(a){return(new sjcl.hash.sha256).update(a).finalize()};
sjcl.hash.sha256.prototype={blockSize:512,reset:function(){this.q=this.M.slice(0);this.m=[];this.g=0;return this},update:function(a){"string"===typeof a&&(a=sjcl.codec.utf8String.toBits(a));var b,c=this.m=sjcl.bitArray.concat(this.m,a);b=this.g;a=this.g=b+sjcl.bitArray.bitLength(a);for(b=512+b&-512;b<=a;b+=512)z(this,c.splice(0,16));return this},finalize:function(){var a,b=this.m,c=this.q,b=sjcl.bitArray.concat(b,[sjcl.bitArray.partial(1,1)]);for(a=b.length+2;a&15;a++)b.push(0);b.push(Math.floor(this.g/
4294967296));for(b.push(this.g|0);b.length;)z(this,b.splice(0,16));this.reset();return c},M:[],a:[],D:function(){function a(a){return 0x100000000*(a-Math.floor(a))|0}var b=0,c=2,d;a:for(;64>b;c++){for(d=2;d*d<=c;d++)if(0===c%d)continue a;8>b&&(this.M[b]=a(Math.pow(c,0.5)));this.a[b]=a(Math.pow(c,1/3));b++}}};
function z(a,b){var c,d,e,f=b.slice(0),g=a.q,h=a.a,l=g[0],k=g[1],n=g[2],m=g[3],p=g[4],s=g[5],r=g[6],v=g[7];for(c=0;64>c;c++)16>c?d=f[c]:(d=f[c+1&15],e=f[c+14&15],d=f[c&15]=(d>>>7^d>>>18^d>>>3^d<<25^d<<14)+(e>>>17^e>>>19^e>>>10^e<<15^e<<13)+f[c&15]+f[c+9&15]|0),d=d+v+(p>>>6^p>>>11^p>>>25^p<<26^p<<21^p<<7)+(r^p&(s^r))+h[c],v=r,r=s,s=p,p=m+d|0,m=n,n=k,k=l,l=d+(k&n^m&(k^n))+(k>>>2^k>>>13^k>>>22^k<<30^k<<19^k<<10)|0;g[0]=g[0]+l|0;g[1]=g[1]+k|0;g[2]=g[2]+n|0;g[3]=g[3]+m|0;g[4]=g[4]+p|0;g[5]=g[5]+s|0;g[6]=
g[6]+r|0;g[7]=g[7]+v|0}
sjcl.mode.ccm={name:"ccm",encrypt:function(a,b,c,d,e){var f,g=b.slice(0),h=sjcl.bitArray,l=h.bitLength(c)/8,k=h.bitLength(g)/8;e=e||64;d=d||[];7>l&&q(new sjcl.exception.invalid("ccm: iv must be at least 7 bytes"));for(f=2;4>f&&k>>>8*f;f++);f<15-l&&(f=15-l);c=h.clamp(c,8*(15-f));b=sjcl.mode.ccm.K(a,b,c,d,e,f);g=sjcl.mode.ccm.n(a,g,c,b,e,f);return h.concat(g.data,g.tag)},decrypt:function(a,b,c,d,e){e=e||64;d=d||[];var f=sjcl.bitArray,g=f.bitLength(c)/8,h=f.bitLength(b),l=f.clamp(b,h-e),k=f.bitSlice(b,
h-e),h=(h-e)/8;7>g&&q(new sjcl.exception.invalid("ccm: iv must be at least 7 bytes"));for(b=2;4>b&&h>>>8*b;b++);b<15-g&&(b=15-g);c=f.clamp(c,8*(15-b));l=sjcl.mode.ccm.n(a,l,c,k,e,b);a=sjcl.mode.ccm.K(a,l.data,c,d,e,b);f.equal(l.tag,a)||q(new sjcl.exception.corrupt("ccm: tag doesn't match"));return l.data},K:function(a,b,c,d,e,f){var g=[],h=sjcl.bitArray,l=h.k;e/=8;(e%2||4>e||16<e)&&q(new sjcl.exception.invalid("ccm: invalid tag length"));(0xffffffff<d.length||0xffffffff<b.length)&&q(new sjcl.exception.bug("ccm: can't deal with 4GiB or more data"));
f=[h.partial(8,(d.length?64:0)|e-2<<2|f-1)];f=h.concat(f,c);f[3]|=h.bitLength(b)/8;f=a.encrypt(f);if(d.length){c=h.bitLength(d)/8;65279>=c?g=[h.partial(16,c)]:0xffffffff>=c&&(g=h.concat([h.partial(16,65534)],[c]));g=h.concat(g,d);for(d=0;d<g.length;d+=4)f=a.encrypt(l(f,g.slice(d,d+4).concat([0,0,0])))}for(d=0;d<b.length;d+=4)f=a.encrypt(l(f,b.slice(d,d+4).concat([0,0,0])));return h.clamp(f,8*e)},n:function(a,b,c,d,e,f){var g,h=sjcl.bitArray;g=h.k;var l=b.length,k=h.bitLength(b);c=h.concat([h.partial(8,
f-1)],c).concat([0,0,0]).slice(0,4);d=h.bitSlice(g(d,a.encrypt(c)),0,e);if(!l)return{tag:d,data:[]};for(g=0;g<l;g+=4)c[3]++,e=a.encrypt(c),b[g]^=e[0],b[g+1]^=e[1],b[g+2]^=e[2],b[g+3]^=e[3];return{tag:d,data:h.clamp(b,k)}}};
sjcl.mode.ocb2={name:"ocb2",encrypt:function(a,b,c,d,e,f){128!==sjcl.bitArray.bitLength(c)&&q(new sjcl.exception.invalid("ocb iv must be 128 bits"));var g,h=sjcl.mode.ocb2.G,l=sjcl.bitArray,k=l.k,n=[0,0,0,0];c=h(a.encrypt(c));var m,p=[];d=d||[];e=e||64;for(g=0;g+4<b.length;g+=4)m=b.slice(g,g+4),n=k(n,m),p=p.concat(k(c,a.encrypt(k(c,m)))),c=h(c);m=b.slice(g);b=l.bitLength(m);g=a.encrypt(k(c,[0,0,0,b]));m=l.clamp(k(m.concat([0,0,0]),g),b);n=k(n,k(m.concat([0,0,0]),g));n=a.encrypt(k(n,k(c,h(c))));d.length&&
(n=k(n,f?d:sjcl.mode.ocb2.pmac(a,d)));return p.concat(l.concat(m,l.clamp(n,e)))},decrypt:function(a,b,c,d,e,f){128!==sjcl.bitArray.bitLength(c)&&q(new sjcl.exception.invalid("ocb iv must be 128 bits"));e=e||64;var g=sjcl.mode.ocb2.G,h=sjcl.bitArray,l=h.k,k=[0,0,0,0],n=g(a.encrypt(c)),m,p,s=sjcl.bitArray.bitLength(b)-e,r=[];d=d||[];for(c=0;c+4<s/32;c+=4)m=l(n,a.decrypt(l(n,b.slice(c,c+4)))),k=l(k,m),r=r.concat(m),n=g(n);p=s-32*c;m=a.encrypt(l(n,[0,0,0,p]));m=l(m,h.clamp(b.slice(c),p).concat([0,0,0]));
k=l(k,m);k=a.encrypt(l(k,l(n,g(n))));d.length&&(k=l(k,f?d:sjcl.mode.ocb2.pmac(a,d)));h.equal(h.clamp(k,e),h.bitSlice(b,s))||q(new sjcl.exception.corrupt("ocb: tag doesn't match"));return r.concat(h.clamp(m,p))},pmac:function(a,b){var c,d=sjcl.mode.ocb2.G,e=sjcl.bitArray,f=e.k,g=[0,0,0,0],h=a.encrypt([0,0,0,0]),h=f(h,d(d(h)));for(c=0;c+4<b.length;c+=4)h=d(h),g=f(g,a.encrypt(f(h,b.slice(c,c+4))));c=b.slice(c);128>e.bitLength(c)&&(h=f(h,d(h)),c=e.concat(c,[-2147483648,0,0,0]));g=f(g,c);return a.encrypt(f(d(f(h,
d(h))),g))},G:function(a){return[a[0]<<1^a[1]>>>31,a[1]<<1^a[2]>>>31,a[2]<<1^a[3]>>>31,a[3]<<1^135*(a[0]>>>31)]}};
sjcl.mode.gcm={name:"gcm",encrypt:function(a,b,c,d,e){var f=b.slice(0);b=sjcl.bitArray;d=d||[];a=sjcl.mode.gcm.n(!0,a,f,d,c,e||128);return b.concat(a.data,a.tag)},decrypt:function(a,b,c,d,e){var f=b.slice(0),g=sjcl.bitArray,h=g.bitLength(f);e=e||128;d=d||[];e<=h?(b=g.bitSlice(f,h-e),f=g.bitSlice(f,0,h-e)):(b=f,f=[]);a=sjcl.mode.gcm.n(u,a,f,d,c,e);g.equal(a.tag,b)||q(new sjcl.exception.corrupt("gcm: tag doesn't match"));return a.data},U:function(a,b){var c,d,e,f,g,h=sjcl.bitArray.k;e=[0,0,0,0];f=b.slice(0);
for(c=0;128>c;c++){(d=0!==(a[Math.floor(c/32)]&1<<31-c%32))&&(e=h(e,f));g=0!==(f[3]&1);for(d=3;0<d;d--)f[d]=f[d]>>>1|(f[d-1]&1)<<31;f[0]>>>=1;g&&(f[0]^=-0x1f000000)}return e},f:function(a,b,c){var d,e=c.length;b=b.slice(0);for(d=0;d<e;d+=4)b[0]^=0xffffffff&c[d],b[1]^=0xffffffff&c[d+1],b[2]^=0xffffffff&c[d+2],b[3]^=0xffffffff&c[d+3],b=sjcl.mode.gcm.U(b,a);return b},n:function(a,b,c,d,e,f){var g,h,l,k,n,m,p,s,r=sjcl.bitArray;m=c.length;p=r.bitLength(c);s=r.bitLength(d);h=r.bitLength(e);g=b.encrypt([0,
0,0,0]);96===h?(e=e.slice(0),e=r.concat(e,[1])):(e=sjcl.mode.gcm.f(g,[0,0,0,0],e),e=sjcl.mode.gcm.f(g,e,[0,0,Math.floor(h/0x100000000),h&0xffffffff]));h=sjcl.mode.gcm.f(g,[0,0,0,0],d);n=e.slice(0);d=h.slice(0);a||(d=sjcl.mode.gcm.f(g,h,c));for(k=0;k<m;k+=4)n[3]++,l=b.encrypt(n),c[k]^=l[0],c[k+1]^=l[1],c[k+2]^=l[2],c[k+3]^=l[3];c=r.clamp(c,p);a&&(d=sjcl.mode.gcm.f(g,h,c));a=[Math.floor(s/0x100000000),s&0xffffffff,Math.floor(p/0x100000000),p&0xffffffff];d=sjcl.mode.gcm.f(g,d,a);l=b.encrypt(e);d[0]^=l[0];
d[1]^=l[1];d[2]^=l[2];d[3]^=l[3];return{tag:r.bitSlice(d,0,f),data:c}}};sjcl.misc.hmac=function(a,b){this.L=b=b||sjcl.hash.sha256;var c=[[],[]],d,e=b.prototype.blockSize/32;this.o=[new b,new b];a.length>e&&(a=b.hash(a));for(d=0;d<e;d++)c[0][d]=a[d]^909522486,c[1][d]=a[d]^1549556828;this.o[0].update(c[0]);this.o[1].update(c[1])};sjcl.misc.hmac.prototype.encrypt=sjcl.misc.hmac.prototype.mac=function(a){a=(new this.L(this.o[0])).update(a).finalize();return(new this.L(this.o[1])).update(a).finalize()};
sjcl.misc.pbkdf2=function(a,b,c,d,e){c=c||1E3;(0>d||0>c)&&q(sjcl.exception.invalid("invalid params to pbkdf2"));"string"===typeof a&&(a=sjcl.codec.utf8String.toBits(a));e=e||sjcl.misc.hmac;a=new e(a);var f,g,h,l,k=[],n=sjcl.bitArray;for(l=1;32*k.length<(d||1);l++){e=f=a.encrypt(n.concat(b,[l]));for(g=1;g<c;g++){f=a.encrypt(f);for(h=0;h<f.length;h++)e[h]^=f[h]}k=k.concat(e)}d&&(k=n.clamp(k,d));return k};
sjcl.prng=function(a){this.b=[new sjcl.hash.sha256];this.h=[0];this.F=0;this.t={};this.C=0;this.J={};this.N=this.c=this.i=this.T=0;this.a=[0,0,0,0,0,0,0,0];this.e=[0,0,0,0];this.A=t;this.B=a;this.p=u;this.z={progress:{},seeded:{}};this.l=this.S=0;this.u=1;this.w=2;this.Q=0x10000;this.H=[0,48,64,96,128,192,0x100,384,512,768,1024];this.R=3E4;this.P=80};
sjcl.prng.prototype={randomWords:function(a,b){var c=[],d;d=this.isReady(b);var e;d===this.l&&q(new sjcl.exception.notReady("generator isn't seeded"));if(d&this.w){d=!(d&this.u);e=[];var f=0,g;this.N=e[0]=(new Date).valueOf()+this.R;for(g=0;16>g;g++)e.push(0x100000000*Math.random()|0);for(g=0;g<this.b.length&&!(e=e.concat(this.b[g].finalize()),f+=this.h[g],this.h[g]=0,!d&&this.F&1<<g);g++);this.F>=1<<this.b.length&&(this.b.push(new sjcl.hash.sha256),this.h.push(0));this.c-=f;f>this.i&&(this.i=f);this.F++;
this.a=sjcl.hash.sha256.hash(this.a.concat(e));this.A=new sjcl.cipher.aes(this.a);for(d=0;4>d&&!(this.e[d]=this.e[d]+1|0,this.e[d]);d++);}for(d=0;d<a;d+=4)0===(d+1)%this.Q&&A(this),e=B(this),c.push(e[0],e[1],e[2],e[3]);A(this);return c.slice(0,a)},setDefaultParanoia:function(a){this.B=a},addEntropy:function(a,b,c){c=c||"user";var d,e,f=(new Date).valueOf(),g=this.t[c],h=this.isReady(),l=0;d=this.J[c];d===t&&(d=this.J[c]=this.T++);g===t&&(g=this.t[c]=0);this.t[c]=(this.t[c]+1)%this.b.length;switch(typeof a){case "number":b===
t&&(b=1);this.b[g].update([d,this.C++,1,b,f,1,a|0]);break;case "object":c=Object.prototype.toString.call(a);if("[object Uint32Array]"===c){e=[];for(c=0;c<a.length;c++)e.push(a[c]);a=e}else{"[object Array]"!==c&&(l=1);for(c=0;c<a.length&&!l;c++)"number"!=typeof a[c]&&(l=1)}if(!l){if(b===t)for(c=b=0;c<a.length;c++)for(e=a[c];0<e;)b++,e>>>=1;this.b[g].update([d,this.C++,2,b,f,a.length].concat(a))}break;case "string":b===t&&(b=a.length);this.b[g].update([d,this.C++,3,b,f,a.length]);this.b[g].update(a);
break;default:l=1}l&&q(new sjcl.exception.bug("random: addEntropy only supports number, array of numbers or string"));this.h[g]+=b;this.c+=b;h===this.l&&(this.isReady()!==this.l&&C("seeded",Math.max(this.i,this.c)),C("progress",this.getProgress()))},isReady:function(a){a=this.H[a!==t?a:this.B];return this.i&&this.i>=a?this.h[0]>this.P&&(new Date).valueOf()>this.N?this.w|this.u:this.u:this.c>=a?this.w|this.l:this.l},getProgress:function(a){a=this.H[a?a:this.B];return this.i>=a?1:this.c>a?1:this.c/
a},startCollectors:function(){this.p||(window.addEventListener?(window.addEventListener("load",this.r,u),window.addEventListener("mousemove",this.s,u)):document.attachEvent?(document.attachEvent("onload",this.r),document.attachEvent("onmousemove",this.s)):q(new sjcl.exception.bug("can't attach event")),this.p=!0)},stopCollectors:function(){this.p&&(window.removeEventListener?(window.removeEventListener("load",this.r,u),window.removeEventListener("mousemove",this.s,u)):window.detachEvent&&(window.detachEvent("onload",
this.r),window.detachEvent("onmousemove",this.s)),this.p=u)},addEventListener:function(a,b){this.z[a][this.S++]=b},removeEventListener:function(a,b){var c,d,e=this.z[a],f=[];for(d in e)e.hasOwnProperty(d)&&e[d]===b&&f.push(d);for(c=0;c<f.length;c++)d=f[c],delete e[d]},s:function(a){sjcl.random.addEntropy([a.x||a.clientX||a.offsetX||0,a.y||a.clientY||a.offsetY||0],2,"mouse")},r:function(){sjcl.random.addEntropy((new Date).valueOf(),2,"loadtime")}};
function C(a,b){var c,d=sjcl.random.z[a],e=[];for(c in d)d.hasOwnProperty(c)&&e.push(d[c]);for(c=0;c<e.length;c++)e[c](b)}function A(a){a.a=B(a).concat(B(a));a.A=new sjcl.cipher.aes(a.a)}function B(a){for(var b=0;4>b&&!(a.e[b]=a.e[b]+1|0,a.e[b]);b++);return a.A.encrypt(a.e)}sjcl.random=new sjcl.prng(6);try{var D=new Uint32Array(32);crypto.getRandomValues(D);sjcl.random.addEntropy(D,1024,"crypto['getRandomValues']")}catch(E){}
sjcl.json={defaults:{v:1,iter:1E3,ks:128,ts:64,mode:"ccm",adata:"",cipher:"aes"},encrypt:function(a,b,c,d){c=c||{};d=d||{};var e=sjcl.json,f=e.d({iv:sjcl.random.randomWords(4,0)},e.defaults),g;e.d(f,c);c=f.adata;"string"===typeof f.salt&&(f.salt=sjcl.codec.base64.toBits(f.salt));"string"===typeof f.iv&&(f.iv=sjcl.codec.base64.toBits(f.iv));(!sjcl.mode[f.mode]||!sjcl.cipher[f.cipher]||"string"===typeof a&&100>=f.iter||64!==f.ts&&96!==f.ts&&128!==f.ts||128!==f.ks&&192!==f.ks&&0x100!==f.ks||2>f.iv.length||
4<f.iv.length)&&q(new sjcl.exception.invalid("json encrypt: invalid parameters"));"string"===typeof a&&(g=sjcl.misc.cachedPbkdf2(a,f),a=g.key.slice(0,f.ks/32),f.salt=g.salt);"string"===typeof b&&(b=sjcl.codec.utf8String.toBits(b));"string"===typeof c&&(c=sjcl.codec.utf8String.toBits(c));g=new sjcl.cipher[f.cipher](a);e.d(d,f);d.key=a;f.ct=sjcl.mode[f.mode].encrypt(g,b,f.iv,c,f.ts);return e.encode(f)},decrypt:function(a,b,c,d){c=c||{};d=d||{};var e=sjcl.json;b=e.d(e.d(e.d({},e.defaults),e.decode(b)),
c,!0);var f;c=b.adata;"string"===typeof b.salt&&(b.salt=sjcl.codec.base64.toBits(b.salt));"string"===typeof b.iv&&(b.iv=sjcl.codec.base64.toBits(b.iv));(!sjcl.mode[b.mode]||!sjcl.cipher[b.cipher]||"string"===typeof a&&100>=b.iter||64!==b.ts&&96!==b.ts&&128!==b.ts||128!==b.ks&&192!==b.ks&&0x100!==b.ks||!b.iv||2>b.iv.length||4<b.iv.length)&&q(new sjcl.exception.invalid("json decrypt: invalid parameters"));"string"===typeof a&&(f=sjcl.misc.cachedPbkdf2(a,b),a=f.key.slice(0,b.ks/32),b.salt=f.salt);"string"===
typeof c&&(c=sjcl.codec.utf8String.toBits(c));f=new sjcl.cipher[b.cipher](a);c=sjcl.mode[b.mode].decrypt(f,b.ct,b.iv,c,b.ts);e.d(d,b);d.key=a;return sjcl.codec.utf8String.fromBits(c)},encode:function(a){var b,c="{",d="";for(b in a)if(a.hasOwnProperty(b))switch(b.match(/^[a-z0-9]+$/i)||q(new sjcl.exception.invalid("json encode: invalid property name")),c+=d+'"'+b+'":',d=",",typeof a[b]){case "number":case "boolean":c+=a[b];break;case "string":c+='"'+escape(a[b])+'"';break;case "object":c+='"'+sjcl.codec.base64.fromBits(a[b],
0)+'"';break;default:q(new sjcl.exception.bug("json encode: unsupported type"))}return c+"}"},decode:function(a){a=a.replace(/\s/g,"");a.match(/^\{.*\}$/)||q(new sjcl.exception.invalid("json decode: this isn't json!"));a=a.replace(/^\{|\}$/g,"").split(/,/);var b={},c,d;for(c=0;c<a.length;c++)(d=a[c].match(/^(?:(["']?)([a-z][a-z0-9]*)\1):(?:(\d+)|"([a-z0-9+\/%*_.@=\-]*)")$/i))||q(new sjcl.exception.invalid("json decode: this isn't json!")),b[d[2]]=d[3]?parseInt(d[3],10):d[2].match(/^(ct|salt|iv)$/)?
sjcl.codec.base64.toBits(d[4]):unescape(d[4]);return b},d:function(a,b,c){a===t&&(a={});if(b===t)return a;for(var d in b)b.hasOwnProperty(d)&&(c&&(a[d]!==t&&a[d]!==b[d])&&q(new sjcl.exception.invalid("required parameter overridden")),a[d]=b[d]);return a},X:function(a,b){var c={},d;for(d in a)a.hasOwnProperty(d)&&a[d]!==b[d]&&(c[d]=a[d]);return c},W:function(a,b){var c={},d;for(d=0;d<b.length;d++)a[b[d]]!==t&&(c[b[d]]=a[b[d]]);return c}};sjcl.encrypt=sjcl.json.encrypt;sjcl.decrypt=sjcl.json.decrypt;
sjcl.misc.V={};sjcl.misc.cachedPbkdf2=function(a,b){var c=sjcl.misc.V,d;b=b||{};d=b.iter||1E3;c=c[a]=c[a]||{};d=c[d]=c[d]||{firstSalt:b.salt&&b.salt.length?b.salt.slice(0):sjcl.random.randomWords(2,0)};c=b.salt===t?d.firstSalt:b.salt;d[c]=d[c]||sjcl.misc.pbkdf2(a,c,b.iter);return{key:d[c].slice(0),salt:c.slice(0)}};