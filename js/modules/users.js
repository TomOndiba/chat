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
var StreamInstance  = {
  a: {},
  b: {},
  is_processing: function( a, b ) {
    return ( this.a[a] && this.a[a][b] );
  },
  is_detected: function( a, b ) {
    return ( this.b[a] && this.b[a][b] );
  },
  process: function( a, b ) {
    this.a[a] = this.a[a] || {};
    return ( this.a[a][b] = true );
  },
  detect: function( a, b ) {
    this.b[a] = this.b[a] || {};
    return ( this.b[a][b] = true );
  },
  deprocess: function( a, b ) {
    if ( this.a[a] ) {
      delete this.a[a][b];
    }
  },
  undetect: function( a, b ) {
    if ( this.b[a] ) {
      delete this.b[a][b];
    }
  }
};
var StreamShare = Backbone.Model.extend({
  instance: {},
  unid: false,

  tab: false,
  inner: false,
  area: false,
  url: false,
  regex: false,
  nubmod: false,
  nubuid: false,
  stream: false,
  col1: false,
  row1: false,
  row2: false,
  ajax: false,

  detect: false,
  process: false,

  response: false,

  images: {
    list: false,
    base: 0
  },

  initialize: function( tab ) {
    if ( !tab.length ) {
      return;
    }
    this.unid   = "stream_"+unds.uniqueId();
    this.tab    = tab;
    this.inner  = $(".ipDockChatTabFlyout:first",tab);
    this.area   = $("._552h:first textarea._552m",tab);
    this.regex  = ipDockPanel.prototype.process.responsive.tab.textarea.format.regex( "int_url" );
    this.nubmod = tab.data("nubmod");
    this.nubuid = tab.data("nubuid");
  },
  detected: function() {
    return StreamInstance.is_detected( this.nubmod, this.nubuid );
  },
  processing: function() {
    return StreamInstance.is_processing( this.nubmod, this.nubuid );
  },
  validate: function() {
    var txt = $.trim( this.area.val() ),
        url = txt.match( this.regex ),
        len = ( url ) ? url.length : false;
    if ( !len ) {
      return false;
    }
    this.url = url[len-1];
    if ( this.url.indexOf( "//" ) === 0 ) {
      this.url  = "http:"+this.url;
    }
    else if ( this.url.indexOf( "http" ) === -1 && this.url.indexOf( "ftp" ) === -1 ) {
      this.url  = "http://"+this.url;
    }
    return this.url;
  },
  render: function() {
    if ( !this.url ) {
      return false;
    }
    var that  = this;

    that.add_instance();
    StreamInstance.process( this.nubmod, this.nubuid );

    that.col1 = ipDockPanel.prototype.process.chat.messages.attachments.uploadRow( that.tab, true ),
    that.row1 = $('<div class="_2qh _2qe _5e52 _5e52_stream" id="'+that.unid+'"><span class="_2qf ip-spinner"></span><a class="_2qg uiCloseButton uiCloseButtonSmall uiCloseButtonSmallDark"><i class="icon-remove"></i></a></div>').data("stream", that).appendTo( that.col1 );

    $("a.uiCloseButton",that.row1).on("click", function(e) {
      e.preventDefault();
      that.cancel();
    });

    ipDockPanel.prototype.process.responsive.tab.inner( this.inner, true );

    this.request();
  },
  request: function() {
    this.process  = true;
    this.ajax = new ipXhr;
    this.ajax.open( ipgo('docServer')+'ipChat/pull.php', "POST" );
    this.ajax.response_mode( "json" );
    this.ajax.params({
      channel: 'messages',
      process: 'stream',
      url: this.url
    });
    this.ajax.callback({
      onsuccess: function( response, stream ) {
        if ( !response || !response.stream || response.error ) {
          stream.cancel();
          return;
        }
        stream.detect   = true;
        stream.response = response;

        call_user_func_array( atam, [ stream.nubuid, stream.nubmod, response.attachment ] );
        StreamShare.prototype.instance[stream.nubmod][stream.nubuid][stream.url]  = response.stream;
        StreamInstance.detect( stream.nubmod, stream.nubuid );
        stream.layout();
        stream.events();
      },
      onerror: function( stream, error ) {
        console.log( arguments );
        //stream.cancel();
      }
    });
    this.ajax.arguments({
      onsuccess: [ this ],
      onerror: [ this ]
    })
    this.ajax.send();
  },
  layout: function() {
    this.row1.addClass("done");

    var that    = this,
        stream  = this.get_instance(),
        images  = stream.images,
        has_img = ( images && unds.isArray( images ) && !unds.isEmpty( images ) );

    this.row2   = $('<div />',{"class":"UIShareStage clearfix"}).appendTo( this.row1 ).on("click", "a", function(e) {
      e.preventDefault();
      e.stopPropagation();
    });
    if ( has_img ) {
      this.images.list = images;
      var a1  = this.row2.addClass("UIShareStage_HasImage").cn("div",{"class":"UIShareStage_Image"}),
          b1  = a1.cn("div",{"class":"UIShareStage_ThumbPager UIThumbPager"}),
          c1  = b1.cn("div",{"class":"UIThumbPager_Loader"}),
          d1  = c1.cn("img",{"src":ipgo("docServer")+"images/ipl-16.gif","alt":"","width":16,"height":11}),
          e1  = b1.cn("div",{"class":"UIThumbPager_Thumbs"});

      for( var a = 0; a < images.length; a++ ) {
        var f1  = e1.cn("img",{"class":"UIThumbPager_Thumb UIThumbPager_Hidden","src":images[a],"alt":""});
        f1[0].onload  = function(e) {
          var src = $(this).attr("src"),
              pos = $.inArray( src, images );
          if ( that.images.base == pos ) {
            if ( c1.is(":visible") || $(this).is(":hidden") ) {
              c1.hide(0);
              $(this).removeClass("UIThumbPager_Hidden");
            }
          }
        };
        f1[0].onerror = function(e) {
          
        };
      }
    }
    var a2  = this.row2.cn("div",{"class":"UIShareStage_ShareContent"}),
        b2  = a2.cn("div",{"class":"UIShareStage_Title"},'<span></span>'),
        c2  = $("span",a2).cn("a",{"href":"#","class":"UIShareStage_InlineEdit inline_edit","role":"button"}).data("editor","title").text( stream.title || "Add title" ),
        d2  = a2.cn("div",{"class":"UIShareStage_Subtitle"}).text( stream.subtitle ),
        e2  = a2.cn("div",{"class":"UIShareStage_Summary"},'<p class="UIShareStage_BottomMargin"></p>'),
        f2  = $("p",e2).cn("a",{"href":"#","class":"UIShareStage_InlineEdit inline_edit","role":"button"}).data("editor","summary").text( stream.summary || "Add summary" );
    $([c2[0],f2[0]]).on("click", function(e) {
      var targ  = $(this).hide(0),
          index = $(this).data("editor"),
          input = $(this).parent().cn("input",{"type":"text","class":"inline_edit_helper","maxlength":100}).val( $.trim( $(this).text() ) );
      input.on("blur", function() {
        var text  = $.trim( $(this).val() );
        $(this).remove();
        targ.show(0);
        if ( text.length ) {
          targ.text( text );
        }
      }).trigger("focus").select();
    });
    if ( has_img ) {
      var a3  = e2.cn("div",{"class":"UIShareStage_ThumbPagerControl UIThumbPagerControl"}),
          b3  = a3.cn("div",{"class":"UIThumbPagerControl_Buttons"},'<a class="UIThumbPagerControl_Button UIThumbPagerControl_Button_Left"></a><a class="UIThumbPagerControl_Button UIThumbPagerControl_Button_Right"></a>'),
          c3  = a3.cn("div",{"class":"UIThumbPagerControl_Text"},'<span class="UIThumbPagerControl_PageNumber">\<span class="UIThumbPagerControl_PageNumber_Current">'+( this.images.base + 1 )+'</span> of <span class="UIThumbPagerControl_PageNumber_Total">'+( images.length )+'</span></span> <span>Choose a Thumbnail</span>'),
          d3  = a3.cn("div",{"class":"uiInputLabel clearfix mts"},'<input class="UIThumbPagerControl_NoPicture uiInputLabelCheckbox" type="checkbox" value="1" name="no_picture"><label for="u_1r_0">No Thumbnail</label>');
      if ( images.length === 1 ) {
        a3.addClass("UIThumbPagerControl_First UIThumbPagerControl_Last");
      }
      else if ( this.images.base == 0 ) {
        a3.addClass("UIThumbPagerControl_First");
      }
      else {
        a3.addClass("UIThumbPagerControl_Last");
      }
      $("a.UIThumbPagerControl_Button_Left",b3).on("click", function(e) {
        var base  = that.images.base,
            prev  = parseInt( pkio( images, base ) );
        if ( !isNaN( prev ) ) {
          var image = $("img",e1).eq( prev );
          if ( image.length ) {
            that.images.base  = prev;
            $("img",e1).addClass("UIThumbPager_Hidden");
            c1.show(0);
            image.load(function() {
              c1.hide(0);
              $(this).removeClass("UIThumbPager_Hidden");
            });
            $(".UIThumbPagerControl_PageNumber_Current",c3).text( prev + 1 );
            if ( prev === 0 ) {
              a3.addClass("UIThumbPagerControl_First").removeClass("UIThumbPagerControl_Last");
            }
          }
        }
      });
      $("a.UIThumbPagerControl_Button_Right",b3).on("click", function(e) {
        var base  = that.images.base,
            next  = parseInt( nkio( images, base ) );
        if ( !isNaN( next ) ) {
          var image = $("img",e1).eq( next );
          if ( image.length ) {
            that.images.base  = next;
            $("img",e1).addClass("UIThumbPager_Hidden");
            c1.show(0);
            image.load(function() {
              c1.hide(0);
              $(this).removeClass("UIThumbPager_Hidden");
            });
            $(".UIThumbPagerControl_PageNumber_Current",c3).text( next + 1 );
            if ( next > 0 && images.length !== 1 ) {
              a3.addClass("UIThumbPagerControl_First").removeClass("UIThumbPagerControl_Last");
            }
          }
        }
      });
    }
    this.col1.scrollTop( this.col1[0].scrollHeight );
  },
  events: function() {
    var that  = this;
    $("a.uiCloseButton",that.row1).on("click", function(e) {
      e.preventDefault();
      call_user_func_array( dtam, [ that.nubuid, that.nubmod, that.response.attachment.ID ] );
      that.cancel();
    });
  },
  cancel: function( ajax ) {
    if ( !ajax && this.ajax && this.ajax.abort ) {
      this.ajax.abort();
    }

    StreamInstance.deprocess( this.nubmod, this.nubuid );
    StreamInstance.undetect( this.nubmod, this.nubuid );

    this.row1.remove();
    this.reset();
    ipDockPanel.prototype.process.responsive.tab.inner( this.inner, true );
  },
  reset: function() {
    this.del_instance();
    this.url    = false;
    this.stream = false;
  },
  get_instance: function() {
    return ( this.has_instance() ) ? StreamShare.prototype.instance[this.nubmod][this.nubuid][this.url] : false;
  },
  has_instance: function() {
    var instance  = StreamShare.prototype.instance;
    return ( instance.hasOwnProperty( this.nubmod ) && instance[this.nubmod].hasOwnProperty( this.nubuid ) && instance[this.nubmod][this.nubuid].hasOwnProperty( this.url ) );
  },
  add_instance: function() {
    if ( this.has_instance() ) {
      return;
    }
    var instance  = StreamShare.prototype.instance;
    instance[this.nubmod]                         = instance[this.nubmod] || {};
    instance[this.nubmod][this.nubuid]            = instance[this.nubmod][this.nubuid] || {};
    instance[this.nubmod][this.nubuid][this.url]  = false;
  },
  del_instance: function() {
    if ( !this.url ) {
      return false;
    }
    if ( this.has_instance() ) {
      delete StreamShare.prototype.instance[this.nubmod][this.nubuid][this.url];
    }
  }
});
var ipUsers = ipChat.extend({
  initialize: function() {
    //this.prevent_console();
    if ( !window.ipExtend ) {
      iplh();
      throw new Error( "'ipExtend' does not exists in window" );
    }
    //window.ipExtend.ipUsers = this;
    $("body").addClass("sidebarMode");
    this.load_users( this.user_dock.layout );
    return this;
  },
  dock: function( callback, args ) {
    args  = ( unds.isArray( args ) ) ? args : [];

    var dc_calls  = ipga("dock_calls") || ipsa("dock_calls",{});
    dc_calls[unds.uniqueId()] = [ callback, args ];
    ipsa("dock_calls",dc_calls);

    if ( ipga("dockLoading") ) {
      return;
    }
    if ( typeof ipDockPanel === "function" ) {
      if ( !unds.isEmpty( dc_calls ) ) {
        for( x in dc_calls ) {
          var dc_call = dc_calls[x];
          if ( dc_call[0] !== "undefined" ) {
            call_user_func_array( dc_call[0], dc_call[1] );
          }
        }
      }
      ipsa("dock_calls",{});
      return;
    }
    ipsa("dockLoading",true);
    ipls("Loading Dock&hellip;");
    var scripts = ["dock"].join(",");
    iplj(scripts,"modules",function() {
      ipsa("dockLoading",false);
      iplh();
      new ipDockPanel();
      var dc_calls  = ipga("dock_calls") || ipsa("dock_calls",{});
      if ( !unds.isEmpty( dc_calls ) ) {
        for( x in dc_calls ) {
          var dc_call = dc_calls[x];
          if ( dc_call[0] !== "undefined" ) {
            call_user_func_array( dc_call[0], dc_call[1] );
          }
          delete dc_calls[x];
        }
      }
      ipsa("dock_calls",dc_calls);
    },function() {
      ipsa("dockLoading",false);
      iplh();
      ipcl().notice( "Error while initializing Dock panel", true );
    });
  },
  first_degree: function( onsuccess, onsuccessargs, onalways, onalwaysargs ) {
    onsuccessargs = onsuccessargs || [];
    onalwaysargs  = onalwaysargs || [];
    if ( ipga("first_degree_running") || ipga("first_degree_finished") ) {
      if ( ipga("first_degree_finished") ) {
        call_user_func_array( onalways, onalwaysargs );
        return;
      }
      if ( ipga("first_degree_running") ) {
        var callbacks = ipga("first_degree_callbacks") || ipsa("first_degree_callbacks",[]);
        callbacks.push( [ onsuccess, onsuccessargs, onalways, onalwaysargs ] );
        ipsa("first_degree_callbacks",callbacks);
      }
      return;
    }
    ipsa("first_degree_running",true);
    var tht = this;
    var onsuccess_func  = function( response ) {
          ipsa("first_degree_running",false);
          ipsa("first_degree_finished",true);
          if ( response.error ) {
            ipcl().notice( "Search Failed: "+response.message );
            return;
          }
          response  = $.extend({}, ipga("users"), response );
          ipsa("users",response);
          var callbacks = ipga("first_degree_callbacks") || ipsa("first_degree_callbacks",[]);
          if ( callbacks.length ) {
            for( i = 0; i < callbacks.length; i++ ) {
              call_user_func_array( callbacks[i][0], callbacks[i][1] );
              call_user_func_array( callbacks[i][2], callbacks[i][3] );
            }
          }
          call_user_func_array( onsuccess, onsuccessargs );
          call_user_func_array( onalways, onalwaysargs );
          ipsa("first_degree_callbacks",[]);
        },
        onerror_func  = function( response, error ) {
          ipsa("first_degree_running",false);
          ipcl().notice( "Search failed:"+( ( error.code && error.state ) ? " "+error.code+":"+error.state : "" )+" "+error.message );
        };

    if ( has_action( "IP_onload_allusers" ) ) {
      do_action( "IP_onload_allusers", true, unds.keys( ipga("users") ), onsuccess_func, onerror_func );
      return;
    }
    var xhr = new ipXhr();
    xhr.open( ipgo('docServer')+'ipChat/pull.php' );
    xhr.params({
      channel: 'users',
      action: 'load',
      init: false,
      limit: false,
      exclude: unds.keys( ipga("users") )
    });
    xhr.callback({
      onsuccess: onsuccess_func,
      onerror: onerror_func
    });
    xhr.send();
  },
  set_online: function( users ) {
    var tabs = $(".ipNubTabGroup .ipNub");
    var ausers = ipga("users");

    users = ( users.hasOwnProperty( "ID" ) ) ? [ users ] : users;
    unds.each( users, function( user ) {
      var status = ( user.SA || user.ST || "online" );
      var user_li = $("._42fz"+user.ID);

      if ( tabs.length ) {
        var chatTab = $("#userNub"+user.ID);
        if ( chatTab.length ) {
          if ( status === "busy" ) {
            dbt( chatTab, "permanent" );
          }
          else {
            abt( chatTab );
          }
          chatTab.removeClass("online busy idle offline").addClass( status );
          $(".status-icon",chatTab).removeClass("status-empty status-online status-offline status-busy status-idle").addClass("status-"+status);
        }
      }

      user_li.removeClass("online busy idle offline").addClass(status).find(".active_time").show().text( timeDifference( user.LS, false, false, true ) ).attr( "timestamp", user.LS );
      user_li.prependTo( user_li.parent() );

      if ( ausers[user.ID] ) {
        ausers[user.ID].SD = user.SD;
        ausers[user.ID].SA = user.SA;
        ausers[user.ID].ST = user.ST;
        ausers[user.ID].LS = user.LS;
      }
    } );

    ipsa("users",ausers);
  },
  set_offline: function( users ) {
    var tabs = $(".ipNubTabGroup .ipNub");
    var ausers = ipga("users");

    users = ( users.hasOwnProperty( "ID" ) ) ? [ users ] : users;
    unds.each( users, function( user ) {
      var status =  "offline";
      var user_li = $("._42fz"+user.ID);

      if ( tabs.length ) {
        var chatTab = $("#userNub"+user.ID);
        if ( chatTab.length ) {
          chatTab.removeClass("online busy idle offline").addClass( status );
          $(".status-icon",chatTab).removeClass("status-empty status-online status-offline status-busy status-idle").addClass("status-"+status);
        }
      }
      
      user_li.removeClass("online busy idle offline").addClass(status).find(".active_time").hide();

      if ( ausers[user.ID] ) {
        ausers[user.ID].SD = user.SD;
        ausers[user.ID].SA = user.SA;
        ausers[user.ID].ST = user.ST;
        ausers[user.ID].LS = user.LS;
      }
    } );
    
    ipsa("users",ausers);
  },
  load_users: function( callback ) {
    ipls("Connecting&hellip;");
    var tht = this;
    var onsuccess_func  = function( response ) {
          iplh();
          if ( response.error ) {
            ipcl().notice( "Error: "+response.message, true );
          }
          if ( !response.user ) {
            var a1  = $().cn("div",false,'Please <span class="iplu"></span> or <span class="ipsu"></span> to Chat'),
                b1  = a1.find(".iplu").cn("a",{"href":ipgo("loginLink"),"title":"Login"},"Login"),
                c1  = a1.find(".ipsu").cn("a",{"href":ipgo("signupLink"),"title":"Signup"},"Signup");
            ipcl().notice( a1, true );
          }
          ipsa( "users_base", response.users );
          ipsa( "users", response.users );
          ipsa( "user", response.user );
          ipsa( "tabs", response.tabs );
  
          if ( response.tabs && !unds.isEmpty( response.tabs ) ) {
            ipUsers.prototype.dock();
          }
          if ( typeof callback === "function" ) {
            call_user_func_array( callback );
          }
        },
        onerror_func  = function( response, error ) {
          iplh();
          ipcl().notice( "Connection failed: ("+error.code+":"+error.state+") "+error.message, true );
        };

    if ( has_action( "IP_users_init_first" ) ) {
      do_action( "IP_users_init_first", true, onsuccess_func, onerror_func );
      return;
    }

    var xhr = new ipXhr();
    xhr.open( ipgo('docServer')+'ipChat/pull.php' );
    xhr.params({
      channel: 'users',
      action: 'load',
      init: true
    });
    xhr.callback({
      onsuccess: onsuccess_func,
      onerror: onerror_func
    });
    xhr.send();
  },
  user_dock: {
    layout: function() {
      var that  = ipUsers.prototype.user_dock;
      var user  = ipga("user");

      var a1  = ipcl().wrap_elm.cn('div',{'class':'ipChatSidebar fixed_always _5468 _5auk'});
          b1  = a1.cn('div',{'class':'uiScrollableArea _4g6b _57yp uiScrollableAreaWithShadow contentAfter'}),
          c1  = b1.cn('div',{'class':'uiScrollableAreaWrap scrollable _pts','tabindex':0}),
          d1  = c1.cn('div',{'class':'uiScrollableAreaBody'}),
          e1  = d1.cn('div',{'class':'uiScrollableAreaContent'}),
          f1  = a1.cn('div',{'class':'_59j-','role':'navigation'},'<div id="sidebar_navigation_top">\
            <div class="_4g5v">\
              <div class="_521h">\
                <a class="_4g5p _521g">\
                  <div class="_4-k7">\
                    <img class="_4g5w img" src="'+user.AV+'">\
                  </div>\
                  <div class="prm _4g5y ellipsis">'+user.NM+'</div>\
                </a>\
                <a class="_4r38" title="Hide Sidebar">\
                  <i class="icon-align-justify"></i>\
                </a>\
              </div>\
            </div>\
          </div>', 'prepend'),
          g1  = e1.cn('div',{'class':'ipChatSidebarBody','role':'complementary'},'<span class="_52d2"></span><div class="_4mq2"><ul class="ipChatOrderedList clearfix"></ul></div><div class="ipChatTypeaheadView _4mq2 hidden_elem"></div>'),
          h1  = e1.cn('div',{'class':'ipChatSidebarMessage clearfix'},'<i class="img"></i><div class="message fcg"></div>'),
          i1  = b1.cn('div',{'class':'uiContextualLayerPositioner uiLayer'},'<div class="uiContextualLayer uiContextualLayerBelowLeft"><div class="_4-th _4--r _50g0"><div class="_4-q5 _50nm"></div><table class="uiGrid _4oes"><tbody><tr><td></td><td></td><td></td><td></td></tr></tbody></table></div></div>');
                i1.find('td').eq(0).html('<div class="uiTypeahead uiClearableTypeahead ipChatTypeahead _57du"><div class="wrap"><div class="innerWrap"></div><i class="uiLoadingIndicatorAsync"></i></div></div>');
      var j1  = i1.find('.innerWrap').cn('a',{'class':'_42ft _50zy clear uiTypeaheadCloseButton _50-0 _50z- icon-remove','title':L.REMOVE,'type':'button'},'<i clas="icon-remove"></i>','insertBefore'),
          k1  = i1.find('.innerWrap').cn('input',{'type':'text','class':'inputtext inputsearch textInput DOMControl_placeholder','autocomplete':'off','placeholder':L.SEARCH+'...','role':'combobox','spellcheck':false,'tabindex':0,'x-webkit-speech':true}).val( L.SEARCH+'...' ).uiPlaceholder();
                $("._521h",f1).append('<div class="_4r38"><div class="uiSelector inlineBlock ipChatSidebarDropdown button uiSelectorBottomUp uiSelectorRight"><div class="uiToggle wrap"><a title="'+L.OPTIONS+'" class="uiSelectorButton ui-options uiCloseButton" role="button" rel="toggle"><i class="icon-gear"></i></a><div class="uiSelectorMenuWrapper uiToggleFlyout"><div role="menu" class="uiMenu uiSelectorMenu"><ul class="uiMenuInner"></ul></div></div></div></div></div>'),
                //i1.find('td').eq(1).html('<div><div class="uiSelector inlineBlock ipChatSidebarDropdown button uiSelectorBottomUp uiSelectorRight"><div class="uiToggle wrap"><a title="'+L.OPTIONS+'" class="uiSelectorButton ui-options uiCloseButton" role="button" rel="toggle"><i class="icon-gear"></i></a><div class="uiSelectorMenuWrapper uiToggleFlyout"><div role="menu" class="uiMenu uiSelectorMenu"><ul class="uiMenuInner"></ul></div></div></div></div></div>'),
                i1.find('td').eq(1).html('<div><div class="uiSelector inlineBlock ipChatSidebarDropdown button uiSelectorBottomUp uiSelectorRight"><div class="uiToggle wrap"><a title="'+L.NOTIFICATIONS+'" class="uiSelectorButton ui-notifications uiCloseButton" role="button" rel="toggle"><i class="icon-bell"></i><span class="_51jx sm hidden_elem">0</span></a><div class="uiSelectorMenuWrapper uiToggleFlyout"><div role="menu" class="uiMenu uiSelectorMenu"><ul class="uiMenuInner ui-notifications-list"></ul></div></div></div></div></div>'),
                i1.find('td').eq(2).html('<div><div class="uiSelector inlineBlock ipChatSidebarDropdown button uiSelectorBottomUp uiSelectorRight"><div class="uiToggle wrap"><a title="'+L.MESSAGES+'" class="uiSelectorButton ui-messages uiCloseButton" role="button" rel="toggle"><i class="icon-comments"></i></a><div class="uiSelectorMenuWrapper uiToggleFlyout"><div role="menu" class="uiMenu uiSelectorMenu"><ul class="uiMenuInner uiMenuMessagesHistory _4kt"><li class="uiMessageItem"><div class="jewelContent"><div></div></div><span class="jewelLoading ip-spinner"></span></li></ul></div></div></div></div></div>');
                i1.find('td').eq(3).html('<div><div class="ipChatSidebarDropdown"><a class="_51sj uiCloseButton" role="button" title="'+L.NEW_MESSAGE+'"><i class="icon-edit"></i></a></div></div>');

      k1.on("webkitspeechchange", function() {
        alert("foo");
      });
      $("*[title]",a1).tipsy({gravity: $.fn.tipsy.autoNS});
      $("*[title]",f1).tipsy({gravity: $.fn.tipsy.autoNS});

      /** Notifications **/
      /*$.post(ipgo("docServer")+"ipChat/pull.php", {channel:"notifications",process:"get"}, function(response) {
        var notif = ( response && !response.error ) ? response : {};
        ipsa("notifications",notif);
      }, "json");*/
      /*var notif = {};
      for( var i = 1; i < 20; i++ ) {
        notif[i]  = {
          ID: i,
          subject: 'Notification '+i,
          content: lorem(),
          sender: "John",
          time: ( new Date().getTime() / 1000 ) - rand( 10, 100 )
        };
        if ( i == 10 ) {
          notif[i].important  = true;
        }
      }
      ipsa("notifications",{});*/

      i1.find('td').eq(1).find(".uiMenu").ipscroll({
        gripper: 'darkGripper'
      });

      /*=============== Sidebar Toggle =====================*/
      /* Hide Sidebar if Cookie exists */
      if ( $.cookie( "dockedSidebar" ) ) {
        a1.addClass("dockedSidebar");
      }
      /* Toggle Function */
      $("a._4r38",f1).on("click",function(e) {
        e.preventDefault();
        a1.toggleClass("dockedSidebar");
        var docked  = a1.hasClass("dockedSidebar");
        if ( docked ) {
          do_action( "onhidesidebar", false, a1 );
          $(this).attr("original-title","Show Sidebar");
          $.cookie( "dockedSidebar", 1, { expires: 365, path: "/" } );
        }
        else {
          do_action( "onshowsidebar", false, a1 );
          $.removeCookie( "dockedSidebar", { path: "/" });
          $(this).attr("original-title","Hide Sidebar");
        }

        if ( typeof ipDockPanel === "function" ) {
          ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );
        }

        var a = $(window).height(),
            b = f1.height(),
            c = $("._50g0",i1).height();
        b1.height( ( a - ( b + c ) ) );
      });
      /* show partial sidebar if sidebar is hidden */
      $(document).on("mousemove", function(event) {
        if ( $(".dockedSidebar").length ) {
          var posLeft = event.clientX;
          if ( posLeft < 70 ) {
            $(".dockedSidebar").stop().animate({marginLeft:"0"},100,"linear");
          }
          else {
            $(".dockedSidebar").stop().animate({marginLeft:"-60px"},100,"linear");
          }
        }
      });
      /*=============== Sidebar Toggle =====================*/

      /*=============== Render Users List =====================*/
      that.list( g1.find("ul.ipChatOrderedList"), ipga("users"), true );
      c1.ipscroll({
        wrapper: ".uiScrollableArea:first"
      });
      /*=============== Render Users List =====================*/

      /*=============== Position Sidebar Contents =====================*/
      var navigation_height = f1.height(),
          search_box_height = $("._50g0",i1).height(),
          window_height     = $(window).height();
      /* Search Box */
      i1.css("top",( window_height - navigation_height )+"px");
      /* Users List **/
      var users_list_height = ( window_height - ( navigation_height + search_box_height ) );
      b1.height( users_list_height );
      /** Dynamic resize on Window resize **/
      $(window).on("resize",function(e) {
        var a = $(this).height();
        i1.css("top",( a - navigation_height )+"px");
        b1.height( ( a - ( navigation_height + search_box_height ) ) );
      });
      /*=============== Position Sidebar Contents =====================*/

      /** Flyout Events **/
      that.menu( $("._521h",f1).find("ul") );
      $([i1[0],f1[0]]).find('.uiSelectorButton').on("click", function(e) {
        e.preventDefault();
        $(".uiToggle").not($(this).parent()).removeClass("openToggler");
        $(this).parent().toggleClass("openToggler");
      });
      that.notifications.cont = i1.find(".ui-notifications").next(".uiToggleFlyout:first");
      i1.find(".ui-notifications").on("click",that.notifications.init);
      i1.find(".ui-messages").one("click",function(event) {
        event.preventDefault();
        ipUsers.prototype.dock( [ "ipDockPanel.prototype.process.chat.messages.history", "init" ], [ i1.find('td').eq(2).find('.uiMessageItem .jewelContent div:first') ] );
      });
      $(document).on("click", function(event) {
        var target  = $(event.target);
        if ( !target.parents(".uiToggle").length ) {
          $(".uiToggle").removeClass("openToggler");
          a1.removeClass("noPreventFyout");
        }
      });

      /** Typeahead **/
      i1.find('td').eq(0).find('.ipChatTypeahead').uiTypeahead({
        callback: {
          keyup: function( typeahead, input, value ) {
            var sidebarWidth  = a1.width();
            if ( !typeahead.data("ow") ) {
              var tw  = typeahead.width();
              typeahead.data("ow",tw);
            }
            if ( value.length < 3 ) {
              var tw  = typeahead.data("ow");
              if ( typeahead.width() != tw ) {
                typeahead.removeClass('uiTypeaheadProessing').stop(true, true).animate({'width':tw},300,function() {
                  $("div._4-th",i1).css("overflow","visible").width("auto");
                });
                that.search.stop( g1.find("ul.ipChatOrderedList") );
              }
              return;
            }
            var tw  = $(".ipChatSidebarBody").width();
            if ( typeahead.width() != tw ) {
              typeahead.stop(true, true).animate({'width':tw},300).addClass('uiTypeaheadProessing');
              $("div._4-th",i1).css("overflow","hidden").width( sidebarWidth );
            }
            that.search.start( value, g1.find("ul.ipChatOrderedList") );
          },
          clear: function( typeahead ) {
            var tw  = typeahead.data("ow");
            typeahead.removeClass('uiTypeaheadProessing').stop(true, true).animate({'width':tw},300,function() {
              $("div._4-th",i1).css("overflow","visible").width("auto");
            });
            that.search.stop( g1.find("ul.ipChatOrderedList") );
          }
        }
      });

      /** Footer Buttons **/
      i1.find('td').eq(3).find("a._51sj:first").on("click",function(event) {
        event.preventDefault();
        ipUsers.prototype.dock( function() {
          var stts  = ipDockPanel.prototype.process.tab.open();
          stts.data("foo","bar");
          ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );
        } );
      });

      new ipPing();
    },
    menu: function( list ) {
      var menus = {
        0: {
          name: L.GO_ONLINE,
          clas: 'ipChatGoOnlineItem',
          call: function(e) {
            e.preventDefault();
            cops( false, "online", function( error ) {
              if ( !error ) {
                $('.ipChatGoOnlineItem',list).hide();
                $('.ipChatGoOfflineItem',list).show();
              }
            });
          }
        },
        1: {
          name: L.GO_OFFLINE,
          clas: 'ipChatGoOfflineItem',
          call: function(e) {
            e.preventDefault();
            cops( false, "offline", function( error ) {
              if ( !error ) {
                $('.ipChatGoOfflineItem',list).hide();
                $('.ipChatGoOnlineItem',list).show();
              }
            });
          },
          sepr: true
        },
        2: {
          name: L.BLOCKED_USERS,
          clas: 'ipChatBlocListsItem',
          call: function(event) {
            event.preventDefault();
            if ( _extp("dialog") ) {
              return;
            }
            $(this).parents(".uiToggle:first").removeClass("openToggler");
            var gthmk = function( ul, bl, btn ) {
              ul.addClass("text_align_ctr").html('<span class="ip-spinner spinner-big"></span>');
              $(".ialert",bl).ialert("hide");
              setTimeout(function() {
                $().ipbox("redraw");
              }, 600);
              $().ipbox("redraw");
              ul.show(0);
              var onsuccess_func  = function( res ) {
                    if ( res.error ) {
                      $(".ialert",bl).ialert("show","error",L.ERROR,res.message);
                      ul.hide(0);
                      $().ipbox("redraw");
                      return;
                    }
                    if ( res.t && res.t === "continue" ) {
                      ul.hide(0);
                      $().ipbox("redraw");
                      return;
                    }
                    var a2  = ul.empty().cn("div",{"class":"ipProfileBrowserResult scrollable fourColumns hideSummary"}),
                        b2  = a2.cn("div",{"class":"ipProfileBrowserListContainer"}),
                        c2  = b2.cn("div",{"class":"listSection clearfix"}),
                        d2  = c2.cn("ul",{"class":"typeahead_list_u_9_r","role":"listbox"});
                    for( x in res ) {
                      var user  = res[x];
                      var e2  = d2.cn("li",{"class":"multiColumnCheckable checkableListItem","role":"options"}),
                          f2  = e2.cn("input",{"type":"checkbox","name":"id","class":"checkbox"}).val( x ),
                          g2  = e2.cn("a",{'class':"anchor","tabindex":"-1","href":"#"}),
                          h2  = g2.cn("div",{"class":"clearfix"}),
                          i2  = h2.cn("img",{"src":user.AV,"class":"photo _8o lfloat"}),
                          j2  = h2.cn("div",{"class":"content _42ef"},'<div class="_6a _6b spacer"></div>'),
                          k2  = j2.cn("div",{"class":"_6a _6b"},'<div class="fcb fwb text">'+user.NM+'</div>');
                      f2.on("change", function() {
                        var par = $(this).parents(".checkableListItem:first");
                        var pul = par.parents("ul.typeahead_list_u_9_r:first");
                        if ( $(this).is(":checked") ) {
                          par.addClass("selectedCheckable");
                        }
                        else {
                          par.removeClass("selectedCheckable");
                        }
                        if ( !$("input:checked",pul).length ) {
                          $(".unblockBtn5").addClass("disabled");
                        }
                        else {
                          $(".unblockBtn5").removeClass("disabled");
                        }
                      });
                      g2.on("click", function( event ) {
                        event.preventDefault();
                        var par = $(this).parents(".checkableListItem:first");
                        var inp = $("input",par);
                        inp.trigger("click");
                      });
                    }
                    $().ipbox("redraw");
                  },
                  onerror_func  = function( res, err ) {
                    $(".ialert",bl).ialert("show","error",L.ERROR,"("+err.state+":"+err.code+") "+err.message);
                    ul.hide(0);
                    $().ipbox("redraw");
                  },
                  onloadend_func  = function( res ) {
                    ul.removeClass("text_align_ctr");
                  };

              if ( has_action( "IP_onload_blocklist" ) ) {
                do_action( "IP_onload_blocklist", true, onsuccess_func, onerror_func, onloadend_func );
                return;
              }

              ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
                channel: 'users',
                process: 'blocked',
                action: 'get'
              },{
                onsuccess: onsuccess_func,
                onerror: onerror_func,
                onloadend: onloadend_func
              });
            };
            var obj = {
              title: L.BLOCKED_USERS,
              content: '<div class="bl-conv"><div class="ialert"></div></div><div class="ubl-conv"></div>',
              streched: true,
              onopen: function( u, o ) {
                $(".ialert",u).ialert();
                var bl  = $(".bl-conv",u),
                    ul  = $(".ubl-conv",u);
                gthmk( ul, bl );
                var a1  = bl.cn("div",{"class":"_54_-"},'<table class="uiGrid"><tbody><tr><td class="vTop _54__"></td><td class="vTop"><input value="'+L.BLOCK+'" type="submit" class="ibtn ibtnsmall ibtnd blockButton"></td></tbody></table>'),
                    b1  = a1.find("td").eq(0).cn("div",{"class":"clearfix uiTokenizer uiInlineTokenizer"},'<div class="tokenarea hidden_elem"></div>'),
                    c1  = b1.cn("div",{"class":"uiTypeahead","tabindex":0},'<div class="wrap"><div class="innerWrap"></div></div>'),
                    d1  = $(".innerWrap",c1).cn("input",{"type":"text","class":"inputtext textInput","tabindex":0,"autofocus":true,"autocomplete":false});
                a1.on("click", function(event) {
                  event.preventDefault();
                  event.stopPropagation();
                  if ( !$(event.target).is("input") ) {
                    d1.focus();
                  }
                });
                $(".blockButton",a1).on("click",function(event) {
                  event.preventDefault();
                  event.stopPropagation();
                  var ids = d1.utokenizer( "get", "id" );
                  if ( !ids.length ) {
                    return false;
                  }
                  var btn = $(this);
                  btn.attr("disabled", true);
                  $(".ialert",u).ialert("hide");
                  setTimeout(function() {
                    $().ipbox("redraw");
                  }, 600);

                  var onsuccess_func  = function( res ) {
                        if ( res.error ) {
                          $(".ialert",u).ialert("show","error",L.ERROR,res.message);
                          $().ipbox("redraw");
                          return;
                        }
                        var users1  = ipga("users");
                        var users2  = ipga("users_base");
                        for( var i = 0; i < ids.length; i++ ) {
                          var id  = ids[i];
                          var tab = $("#userNub"+id);
                          if ( tab.length ) {
                            ipDockPanel.prototype.process.tab.close( tab, false, true );
                          }
                          delete users1[id];
                          delete users2[id];
                        }
                        ipsa("users",users1);
                        ipsa("users_base",users2);
  
                        ipUsers.prototype.user_dock.list( $("ul.ipChatOrderedList"), users2, true );
                        d1.utokenizer( "clear" );
                        $().ipbox("redraw");
                        gthmk( ul, bl );

                        if ( ipWebSocket != false ) {
                          console.log("blocking");
                          ipWebSocket.send(
                            json_encode(
                              {
                                event: "block",
                                users: ids,
                                user: ipga( "user" ).ID
                              }
                            )
                          );
                        }
                      },
                      onerror_func  = function( res, err ) {
                        $(".ialert",u).ialert("show","error",L.ERROR,"("+err.state+":"+err.code+") "+err.message);
                        $().ipbox("redraw");
                      },
                      onloadend_func  = function() {
                        btn.removeAttr("disabled");
                      };

                  if ( has_action( "IP_onblock_users" ) ) {
                    do_action( "IP_onblock_users", true, ids, onsuccess_func, onerror_func, onloadend_func );
                    return;
                  }

                  ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
                    channel: 'users',
                    process: 'users',
                    action: 'block',
                    id: ids
                  },{
                    onsuccess: onsuccess_func,
                    onerror: onerror_func,
                    onloadend: onloadend_func
                  });
                });
                d1.autoGrowInput({
                  minWidth: 20
                }).utokenizer({
                  target: b1,
                  label: "Add peoples to blocked list",
                  list: ipga("users"),
                  search: "NM",
                  limit: 5,
                  onsearch: function( ul, item, tokenizer ) {
                    ipUsers.prototype.first_degree( function( input ) {
                      input.utokenizer("set",ipga("users")).trigger("blur").trigger("focus");
                    }, [ d1 ] );
                    var a2  = ul.cn("li",{"class":"user"}),
                        b2  = a2.cn("img",{"src":item.AV,"alt":item.NM}),
                        c2  = a2.cn("span",{"class":"text"},item.NM),
                        d2  = a2.cn("span",{"class":"subtext"},item.SA||item.ST);
                    a2.on("mousedown",function(event) {
                      event.preventDefault();
                      tokenizer.utokenizer( "add", item.ID, item.NM );
                      $().ipbox("redraw");
                    });
                    a2.on("mouseenter",function() {
                      $("li",ul).removeClass("selected");
                      $(this).addClass("selected");
                    }).on("mouseleave",function() {
                      $(this).removeClass("selected");
                    });
                  },
                  onremove: function( id, name, tokenizer ) {
                    $().ipbox("redraw");
                  }
                });
              },
              buttons: {
                0: {
                  text: L.UNBLOCK,
                  icon: 'icon-print',
                  disb: true,
                  clas: "unblockBtn5 ibtns",
                  call: function( event ) {
                    event.preventDefault();
                    var ul  = $(".typeahead_list_u_9_r"),
                        inp = $("input[type='checkbox']:checked",ul),
                        btn = $(this);
                    if ( $(this).hasClass("disabled") || !inp.length ) {
                      return;
                    }
                    var data  = inp.serializeObject();
                    data.channel  = 'users';
                    data.process  = 'users';
                    data.action   = 'unblock';

                    $(".ialert",$(".bl-conv")).ialert("hide");
                    btn.addClass("disabled");
                    $("input[type='checkbox']",ul).prop("disabled", true);
                    setTimeout(function() {
                      $().ipbox("redraw");
                    }, 600);

                    var onsuccess_func  = function( res ) {
                          if ( res.error ) {
                            $(".ialert",$(".bl-conv")).ialert("show","error",L.ERROR,res.message);
                            $().ipbox("redraw");
                            return;
                          }
                          gthmk( $(".ubl-conv"), $(".bl-conv") );
  
                          var users = ipga("users"),
                              ids = [];
                          for( x in res ) {
                            users[res[x].ID]  = res[x];
                            ids.push( res[x].ID );
                          }
                          ipsa("users",users);

                          if ( ipWebSocket != false ) {
                            ipWebSocket.send(
                              json_encode(
                                {
                                  event: "unblock",
                                  users: ids,
                                  user: ipga( "user" ).ID
                                }
                              )
                            );
                          }
                        },
                        onerror_func  = function( res, err ) {
                          $(".ialert",$(".bl-conv")).ialert("show","error",L.ERROR,"("+err.state+":"+err.code+") "+err.message);
                          $().ipbox("redraw");
                        },
                        onloadend_func  = function() {
                          if ( $("input[type='checkbox']",ul).length ) {
                            btn.removeClass("disabled");
                            $("input[type='checkbox']",ul).removeAttr("disabled");
                          }
                        };

                    if ( has_action( "IP_onunblock_users" ) ) {
                      do_action( "IP_onunblock_users", true, data.id, onsuccess_func, onerror_func, onloadend_func );
                      return;
                    }

                    ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",data,{
                      onsuccess: onsuccess_func,
                      onerror: onerror_func,
                      onloadend: onloadend_func
                    });
                  }
                },
                1: {
                  text: L.CANCEL,
                  icon: 'icon-remove',
                  call: function( e ) {
                    e.preventDefault();
                    $().ipbox("close");
                  }
                }
              }
            };
            _extc("dialog",function(obj) {
              $().ipbox( obj );
            },[obj]);
          }
        },
        3: {
          name: L.EDIT_PROFILE,
          clas: 'ipChatProfileItem',
          call: function( event ) {
            event.preventDefault();
            if ( _extp("dialog") ) {
              return;
            }
            $(this).parents(".uiToggle:first").removeClass("openToggler");

            var epmro = function() {
              var user  = ipga("user");
              var a1  = $().cn("div",{"class":"ipProfileSection"},'<div class="cover">'+user.NM+'</div>'),
                  b1  = a1.cn("div",{"class":"ipProfileHeadline clearfix"}),
                  c1  = b1.cn("div",{"class":"name"},'<div class="photoContainer"><div class="drop_elem"><a class="profilePicThumb"><img src="'+user.AV+'" class="profilePic" alt="'+user.NM+'"/></a></div></div>'),
                  d1  = $(".drop_elem",c1).cn("a",{"class":"ipProfilePicButton ibtn"},'<span class="uiButtonText">'+L.EDIT_PROFILE+' Picture</span>'),
                  e1  = d1.cn('form',{"class":"ipProfileFileForm","accept":"image/*","method":"POST","enctype":"multipart/form-data","target":"u_1i_1","action":ipgo("docServer")+"ipChat/pull.php"},getUploadVars(false,'profile',true)),                  
                  f1  = e1.cn("input",{"class":"ipProfileFileInput","name":"attachment","type":"file","title":"Choose a file to upload","accept":"image/*"});

              var g1  = b1.cn("div",{"class":"_4nap _3sst _51lb"},false,'prepend'),
                  /*h1  = g1.cn("div",{"class":"item _1zq- _51k6 acw"},'<a href="#" class="primary"><div class="primarywrap"><div class="image"><i class="icon-lock"></i></div><div class="content"><div class="title _4o9k mfsm fcb">Change Password</div></div></div></a>'),
                  i1  = g1.cn("div",{"class":"item _1zq- _51k6 acw"},'<a href="#" class="primary"><div class="primarywrap"><div class="image"><i class="icon-refresh"></i></div><div class="content"><div class="title _4o9k mfsm fcb">Reset Chat History</div></div></div></a>'),
                  j1  = g1.cn("div",{"class":"item _1zq- _51k6 acw"},'<a href="#" class="primary"><div class="primarywrap"><div class="image"><i class="icon-remove"></i></div><div class="content"><div class="title _4o9k mfsm fcb">Lives in Ezhukone, India</div></div></div></a>'),*/
                  k1  = g1.cn("div",{"class":"item _1zq- _51k6 acw"},'<a href="#" class="primary"><div class="primarywrap"><div class="image"><i class="icon-remove"></i></div><div class="content"><div class="title _4o9k mfsm fcb">Logout</div></div></div></a>');

              $("a",g1).on("click", function(event) {
                event.preventDefault();
              });
              $("a",k1).on("click", ipAuth.prototype.logout);
              c1.on("mouseenter", $(".profilePicThumb, .ipProfilePicButton"), function( event ) {
                d1.show(0);
              }).on("mouseleave", $(".profilePicThumb, .ipProfilePicButton"), function( event ) {
                d1.hide(0);
              });

              f1.on("change", function(event) {
                var file  = ( event.target.files && event.target.files.length ) ? event.target.files[0] : false,
                    form  = $(this).parents("form:first"),
                    frame = $("iframe",form);

                if ( !hasFileUpload() ) {
                  if ( !frame.length ) {
                    var frameID = "frame_"+unds.uniqueId();
                    form.attr("target",frameID);
                    frame = form.cn("iframe",{"class":"hidden_elem accessible_elem","name":frameID},false,'insertAfter');
                  }
                  form.trigger("submit");
                  return;
                }
                $("input[name=source]",form).val("html5");
                b1.addClass("profilePicLoading");

                if ( !file ) {
                  b1.removeClass("profilePicLoading");
                  return;
                }

                var data  = new FormData( form.get(0) );
                form.trigger("reset");

                var xhr = new ipXhr;
                xhr.open( ipgo("docServer")+"ipChat/pull.php", "POST", true  );
                xhr.callback({
                  onsuccess: function( res ) {
                    if ( res.error ) {
                      return;
                    }
                    $("img.profilePic",c1).attr("src",res.image);
                    $("._4-k7 ._4g5w").attr("src",res.image);
                    ipga("user").AV = res.image;
                  },
                  onerror: function( res, err ) {
                    console.log( res, err );
                  },
                  onloadend: function() {
                    b1.removeClass("profilePicLoading");
                  }
                });
                xhr.send( data );
              });
              return a1;
            };
            var obj = {
              title: L.EDIT_PROFILE,
              content: epmro(),
              streched: true
            };
            _extc("dialog",function(obj) {
              $().ipbox( obj );
            },[obj]);
          }
        },
        4: {
          name: L.CHANGE_THEME,
          clas: 'ipChatThemesItem',
          call: function(event) {
            event.preventDefault();
            if ( _extp("dialog") ) {
              return;
            }
            $(this).parents(".uiToggle:first").removeClass("openToggler");
            var epmrc = function() {
              var themes  = ipga("themes");
              var a1  = $().cn("div",{"class":"details-wrapper clearfix"}),
                  b1  = a1.cn("div",{"class":"side-nav-container"}),
                  c1  = b1.cn("ul"),
                  d1  = a1.cn("div",{"class":"details-info clearfix"});
              for( x in themes ) {
                var theme = themes[x];
                var e1  = c1.cn("li",{"class":"nav-list-item theme-item-"+x,"id":"theme-item-"+x}).data("theme", theme),
                    f1  = e1.cn("a",{"class":"theme-link"},'<span class="hover-target"><span class="title">'+theme.name+'</span></span>');
                f1.on("click", function(e) {
                  e.preventDefault();
                  var theme = $(this).parent().data("theme");
                  if ( !theme || $(this).parent().hasClass("selected") ) {
                    return;
                  }
                  $(".nav-list-item").removeClass("selected");
                  $(this).parent().addClass("selected");
                  var g1  = d1.empty().cn("div",{"class":"cover-container"}),
                      h1  = g1.cn("img",{"class":"cover-image","src":theme.screenshot}),
                      i1  = d1.cn("div",{"class":"info-container"},'<div class="theme-title">'+theme.name+' '+theme.version+'</div>'),
                      j1  = i1.cn("div",{"class":"theme-subtitle"},theme.description),
                      k1  = i1.cn("div",{"class":"theme-author"},'by <a href="'+theme.author_uri+'" target="_blank">'+theme.author+'</a>'),
                      l1  = i1.cn("div",{"class":"theme-actions"}),
                      m1  = l1.cn("a",{"href":"#","class":"ibtn"},"<span>"+L.ACTIVATE+"</span>");
                  h1.on("click", function(e) {
                    if ( _extp("slider") ) {
                      return false;
                    }
                    var items = [{
                      name: theme.name,
                      link: $(this).attr("src"),
                      thumb: $(this).attr("src")
                    }];
                    _extc("slider",function(items) {
                      $().ipslider({
                        images: items,
                        offset: 0
                      });
                    },[items]);
                  });
                  if ( ipChat.prototype.active_theme() === theme.theme_idx ) {
                    m1.addClass("disabled");
                  }
                  if ( theme.theme_uri ) {
                    l1.cn("a",{"href":theme.theme_uri,"class":"ibtn","target":"_blank","rel":"nofollow"},"<span>"+L.MORE_INFO+"</span>");
                  }
                  if ( theme.theme_uri ) {
                    l1.cn("a",{"href":theme.author_uri,"class":"ibtn","target":"_blank","rel":"nofollow"},"<span>"+L.AUTHOR_INFO+"</span>");
                  }
                  m1.on("click", function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    if ( ipChat.prototype.active_theme() === theme.theme_idx || btn.hasClass("disabled") ) {
                      return false;
                    }
                    btn.addClass("disabled");
                    do_action( 'onuserchangetheme', false, theme.theme_idx );
                    $.cookie( "active_theme", theme.theme_idx, { expires: 365, path: "/" } );
                    window.location.reload();
                  });
                });
              }
              $("li#theme-item-"+ipChat.prototype.active_theme()+" a",a1).trigger("click");
              return a1;
            };
            var obj = {
              title: L.CHANGE_THEME,
              content: epmrc(),
              streched: true,
              width: 700
            };
            _extc("dialog",function(obj) {
              $().ipbox( obj );
            },[obj]);
          }
        },
        5: {
          name: L.WRITING_LANGUAGE,
          clas: 'ipChatLangItem',
          call: function(e) {
            e.preventDefault();
            if ( _extp("dialog") ) {
              return;
            }
            $(this).parents(".uiToggle:first").removeClass("openToggler");
            var table = $('<table />',{'border':0,'width':'100%','class':'ipLangSelTable'}).html('<tbody></tbody>'),
                lcpsa = function( a, b ) {
              
            };
            var ilangs  = ipga( "languages" );
            if ( !ilangs || !ilangs.write ) {
              return;
            }
            var wlangs  = ilangs.write;
            var tbl = colbuild( wlangs, 5, function( key, val ) {
              return { c : key, n : val };
            });
            if ( unds.isEmpty( tbl ) ) {
              return;
            }
            var write_lng = ( ilangs.codes && ilangs.codes.w ) ? ilangs.codes.w : "en";
            for( tr in tbl ) {
              var trl = $("tbody",table).cn("tr");
              for( td in tbl[tr] ) {
                var tdl = trl.cn("td").data( "lang", tbl[tr][td] ).html( '<div>'+tbl[tr][td].n+'</div>' ).on("click", function(e2) {
                  e2.preventDefault();
                  var sel_lng = $(this).data( "lang" );
                  if ( sel_lng && sel_lng.c ) {
                    var lngs  = ipga( "languages" );
                    lngs.codes.w  = sel_lng.c;
                    $.cookie( "wlang_global", sel_lng.c, { expires: 365, path: "/" } );
                    ipsa( "languages", lngs );
                  }
                  $().ipbox("close");
                  return;
                });
                if ( write_lng == tbl[tr][td].c ) {
                  tdl.addClass("selected");
                }
              }
            }
            var obj = {
              title: L.WRITING_LANGUAGE,
              content: table,
              streched: true,
              onopen: lcpsa,
              width: 550,
              buttons: {
                0: {
                  text: L.CANCEL,
                  icon: 'icon-remove',
                  call: function( e ) {
                    e.preventDefault();
                    $().ipbox("close");
                  }
                }
              }
            };
            _extc("dialog",function(obj) {
              $().ipbox( obj );
            },[obj]);
          }
        },
        6: {
          name: L.READING_LANGUAGE,
          clas: 'ipChatRLangItem',
          call: function(e) {
            e.preventDefault();
            if ( _extp("dialog") ) {
              return;
            }
            $(this).parents(".uiToggle:first").removeClass("openToggler");
            var table = $('<table />',{'border':0,'width':'100%','class':'ipLangSelTable'}).html('<tbody></tbody>'),
                lcpsa = function( a, b ) {
              
            };
            var ilangs  = ipga( "languages" );
            if ( !ilangs || !ilangs.read ) {
              return;
            }
            var wlangs  = ilangs.read;
            var tbl = colbuild( wlangs, 5, function( key, val ) {
              return { c : key, n : val };
            });
            if ( unds.isEmpty( tbl ) ) {
              return;
            }
            var read_lng  = ( ilangs.codes && ilangs.codes.r ) ? ilangs.codes.r : "en";
            for( tr in tbl ) {
              var trl = $("tbody",table).cn("tr");
              for( td in tbl[tr] ) {
                var tdl = trl.cn("td").data( "lang", tbl[tr][td] ).html( '<div>'+tbl[tr][td].n+'</div>' ).on("click", function(e2) {
                  e2.preventDefault();
                  var sel_lng = $(this).data( "lang" );
                  if ( sel_lng && sel_lng.c ) {
                    var lngs  = ipga( "languages" );
                    lngs.codes.r  = sel_lng.c;
                    $.cookie( "rlang_global", sel_lng.c, { expires: 365, path: "/" } );
                    ipsa( "languages", lngs );
                  }
                  window.location.href  = window.location.href;
                  $().ipbox("close");
                  return;
                });
                if ( read_lng == tbl[tr][td].c ) {
                  tdl.addClass("selected");
                }
              }
            }
            var obj = {
              title: L.READING_LANGUAGE,
              content: table,
              streched: true,
              onopen: lcpsa,
              width: 550,
              buttons: {
                0: {
                  text: L.CANCEL,
                  icon: 'icon-remove',
                  call: function( e ) {
                    e.preventDefault();
                    $().ipbox("close");
                  }
                }
              }
            };
            _extc("dialog",function(obj) {
              $().ipbox( obj );
            },[obj]);
          }
        },
        7: {
          name: L.ADVANCED_SETTINGS,
          clas: 'ipChatAdvSettItem',
          call: function( event ) {
            event.preventDefault();
            if ( _extp("dialog") ) {
              return;
            }
            $(this).parents(".uiToggle:first").removeClass("openToggler");
            var lcpsa = function( a, b ) {
              $(".ialert",a).ialert({
                close: false
              });
              var icpsd = $(".ipChatPrivacySettingsDialog",a);
              var icpsc = function( c, d, e ) {
                var a1  = $().cn("div",{"class":"pbm "+c+"Section difflistSection unselected"},'<table><tbody><tr><td></td><td></td></tr></tbody></table>'),
                    b1  = $("td:eq(0)",a1).cn("input",{"class":c+"Radio","name":c,"id":c,"type":"radio"}),
                    c1  = $("td:eq(1)",a1).cn("label",{"for":c},'<div class="fsl fwb fcb">'+d+'</div>');
                if ( !e ) {
                  $("tbody",a1).append('<tr><td></td><td></td></tr>');
                  var d1  = $("td:eq(3)",a1).cn("div",{"class":c+"Tokenizers selectedContent minWidthArea"}),
                      e1  = d1.cn("div",{"class":"tokenizerArea"}),
                      f1  = e1.cn("div",{"class":"listContainer"}),
                      g1  = f1.cn("div",{"class":"clearfix uiTokenizer uiInlineTokenizer"},'<div class="tokenarea"></div>').css({
                        "max-height": "150px",
                        "overflow-x": "hidden",
                        "overflow-y": "auto"
                      }),
                      h1  = g1.cn("div",{"class":"uiTypeahead"}),
                      i1  = h1.cn("div",{"class":"wrap"}),
                      j1  = i1.cn("div",{"class":"innerWrap"}),
                      k1  = j1.cn("input",{"type":"text","class":"inputtext textInput","role":"combobox","spellcheck":false});
                  d1.on("click", function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if ( !$(event.target).is("input") ) {
                      k1.focus();
                    }
                  });
                  k1.autoGrowInput({
                    minWidth: 20
                  }).utokenizer({
                    target: g1,
                    list: ipga("users"),
                    search: "NM",
                    limit: 5,
                    onsearch: function( ul, item, tokenizer ) {
                      ipUsers.prototype.first_degree( function( input ) {
                        input.utokenizer("set",ipga("users")).trigger("blur").trigger("focus");
                      }, [ k1 ] );
                      var a2  = ul.cn("li",{"class":"user"}),
                          b2  = a2.cn("img",{"src":item.AV,"alt":item.NM}),
                          c2  = a2.cn("span",{"class":"text"},item.NM),
                          d2  = a2.cn("span",{"class":"subtext"},item.SA||item.ST);
                      a2.on("mousedown",function(event) {
                        event.preventDefault();
                        tokenizer.utokenizer( "add", item.ID, item.NM );
                        $().ipbox("redraw");
                        d1.scrollTop( d1[0].scrollHeight + 50 );
                      });
                      a2.on("mouseenter",function() {
                        $("li",ul).removeClass("selected");
                        $(this).addClass("selected");
                      }).on("mouseleave",function() {
                        $(this).removeClass("selected");
                      });
                    },
                    onremove: function( id, name, tokenizer ) {
                      $().ipbox("redraw");
                    }
                  });
                }
                a1.on("click", function() {
                  $("input[type='radio']",icpsd).not(b1).removeAttr("checked");
                  b1.prop("checked", true);
                  $("div.selected",icpsd).not(a1).removeClass("selected").addClass("unselected");
                  a1.removeClass("unselected").addClass("selected");
                  $().ipbox("redraw");
                });
                return a1;
              };
              var blacklist = icpsc( "blacklist", L.TURN_ON_EXCEPT_SOME+"&hellip;" ),
                  whitelist = icpsc( "whitelist", L.TURN_ON_ONLY_SOME+"&hellip;" ),
                  offline   = icpsc( "offline", L.TURN_OFF_CHAT, true );
              var lists     = $("<div />",{"class":"driggles"}).append( blacklist ).append( whitelist ).append( offline );
              $(".ialert",a).ialert("hide");
              var onsuccess_func  = function( res ) {
                    if ( res.error ) {
                      $(".ialert",a).ialert("show","error",L.ERROR,res.message);
                      return;
                    }
                    icpsd.append( lists ).find( lists ).hide(0);
  
                    var current = $("."+res.status+"Section",icpsd);
                    current.addClass("selected").removeClass("unselected");
                    $("input[type='radio']",current).prop("checked", true);
  
                    var tokens  = res.tokens;
                    var btokens = tokens.blacklist || [];
                    var wtokens = tokens.whitelist || [];
                    var mtokens = unds.union( btokens, wtokens );
  
                    if ( mtokens && mtokens.length ) {
                      users_batch_call( mtokens, function( btokens, wtokens ) {
                        for( var i = 0; i < btokens.length; i++ ) {
                          var user  = ipga("users")[btokens[i]];
                          $(".blacklistSection input.textInput").utokenizer( "add", user.ID, user.NM );
                        }
                        for( var i = 0; i < wtokens.length; i++ ) {
                          var user  = ipga("users")[wtokens[i]];
                          $(".whitelistSection input.textInput").utokenizer( "add", user.ID, user.NM );
                        }
                        $(lists, icpsd).show(0);
                        $(".adv_spinner",a).addClass("hidden_elem");
                        icpsd.append( '<div class="fsm uiBoxYellow pam">'+L.NOTE+': '+L.ADV_SETT_WARN+'</div>' );
                        $().ipbox("redraw");
                      }, [ btokens, wtokens ] );
                    }
                    else {
                      $(lists, icpsd).show(0);
                      icpsd.append( '<div class="fsm uiBoxYellow pam">'+L.NOTE+': '+L.ADV_SETT_WARN+'</div>' );
                      $(".adv_spinner",a).addClass("hidden_elem");
                    }
                  },
                  onerror_func  = function( res, err ) {
                    $(".ialert",a).ialert("show","error",L.ERROR,err.message);
                  },
                  onloadend_func  = function() {
                    $().ipbox("redraw");
                  };

              if ( has_action( "IP_onload_chat_settings" ) ) {
                do_action( "IP_onload_chat_settings", true, onsuccess_func, onerror_func, onloadend_func );
                return;
              }

              ipqx(ipgo('docServer')+'ipChat/pull.php', "POST", {
                channel: "settings",
                process: "chat"
              }, {
                onsuccess: onsuccess_func,
                onerror: onerror_func,
                onloadend: onloadend_func
              });
            };
            var obj = {
              title: L.ADVANCED_CHAT_SET,
              content: '<div class="pvm phl ipChatPrivacySettingsDialog"><div class="ialert"></div><div class="adv_spinner text_align_ctr"><span class="ip-spinner spinner-big"></span></div></div>',
              streched: true,
              onopen: lcpsa,
              width: 450,
              buttons: {
                0: {
                  text: L.SAVE,
                  clas: 'ibtns',
                  icon: 'icon-save',
                  call: function( e ) {
                    e.preventDefault();
                    var btn = $(this);
                    if ( btn.hasClass("disabled") ) {
                      return;
                    }
                    var selected  = $(".difflistSection.selected");
                    if ( !selected.length ) {
                      return;
                    }
                    var params  = {
                      channel: "settings",
                      process: "chat",
                      action: "update",
                      data: $("input[type=radio]", selected).attr("name")
                    };
                    var tok_ar  = $("input.textInput", selected);
                    if ( tok_ar.length ) {
                      var tokens  = tok_ar.utokenizer( "get", "id" );
                      if ( !tokens.length ) {
                        params.data = "online";
                      }
                      params.tokens = tokens;
                    }
                    btn.addClass("disabled");
                    var onsuccess_func  = function( res ) {
                          if ( res.error ) {
                            return;
                          }
                          $().ipbox("close");
                          if ( ipWebSocket != false ) {
                            ipWebSocket.send(
                              json_encode({
                                event: "status",
                                user: ipga( "user" ).ID
                              })
                            );
                          }
                        },
                        onerror_func  = function( res, err ) {},
                        onloadend_func  = function() {
                          btn.removeClass("disabled");
                        };

                    do_action( "onupdate_chat_status_adv", false, params.data, params.tokens );
                    if ( has_action( "IP_onupdate_chat_settings" ) ) {
                      do_action( "IP_onupdate_chat_settings", true, params.data, params.tokens, onsuccess_func, onerror_func, onloadend_func )
                      return;
                    }

                    ipqx( ipgo('docServer')+'ipChat/pull.php', "POST", params, {
                      onsuccess: onsuccess_func,
                      onerror: onerror_func,
                      onloadend: onloadend_func
                    } );
                  }
                },
                1: {
                  text: L.CANCEL,
                  clas: 'ibtnd',
                  icon: 'icon-remove',
                  call: function( e ) {
                    e.preventDefault();
                    $().ipbox("close");
                  }
                }
              }
            };
            _extc("dialog",function(obj) {
              $().ipbox( obj );
            },[obj]);
          }
        },
        8: {
          name: L.POPOUT_CHAT,
          clas: 'ipChatPopoutItem',
          call: function(e) {e.preventDefault();},
          sepr: true,
          call: function( e ) {
            e.preventDefault();

            var width  = 600;
            var height = screen.availHeight - 200;
            var left   = (screen.availWidth  - width)/2;
            var top    = 0;
            var params = 'width='+width+', height='+height;
            params += ', top='+top+', left='+left;
            params += ', directories=no';
            params += ', location=no';
            params += ', menubar=no';
            params += ', resizable=no';
            params += ', scrollbars=no';
            params += ', status=no';
            params += ', toolbar=no';

            var popwindow = window.open( ipgo( "mobileURI" )+"?ispopup=1", "ipPopoutChat", params );
            if ( window.focus ) {
              popwindow.focus();
            }
            popwindow.onbeforeunload  = function() {
              popwindow.opener.window.ipChat.prototype.reinit();
            };
            popwindow.onbeforeload = function() {
              console.log("sss");
            };
            $.cookie( "popout_isopen", "1", { expires: 365, path: "/" } );
            if ( window.location.search.indexOf( "ispopup" ) !== -1 ) {
              window.location.search  = "";
            }
            else {
              window.location.href  = window.location.href;
            }
            return false;
          }
        },
        9: {
          name: L.LOGOUT,
          clas: 'ipChatLogoutItem',
          call: ipAuth.prototype.logout
        }
      };
      for( i in menus ) {
        var menu  = menus[i];
        var a = list.cn('li',{'class':'uiMenuItem '+menu.clas}),
            b = a.cn('a',{'class':'itemAnchor','role':'menuitem','tabindex':0,'href':'#'}).on("click",menu.call),
            c = b.cn('span',{'class':'itemLabel fsm'},menu.name);
        if ( menu.sepr ) {
          list.cn('li',{'class':'uiMenuSeparator'});
        }
      }
      var SA  = ipga("user").SA || ipga("user").ST;
      if ( SA === "offline" ) {
        $('.ipChatGoOfflineItem',list).hide();
        $('.ipChatGoOnlineItem',list).show();
      }
      else {
        $('.ipChatGoOfflineItem',list).show();
        $('.ipChatGoOnlineItem',list).hide();
      }
    },
    list: function( list, users, clear, limit, query ) {
      var that  = ipUsers.prototype.user_dock;
      if ( clear === true ) {
        list.empty();
      }
      if ( unds.isEmpty( users ) || !list.length ) {
        return;
      }
      if ( query && query.length ) {
        var regex = new RegExp( '('+query+')', 'gi' );
      }
      if ( !query ) {
        users = unds.sortBy( users, function( item ) { return ( ( item.SA || item.ST ) !== "online" ) } );
      }

      var index = 0;
      $.each(users, function( i, user ) {
        var name  = user.NM;
        var stats = user.SA || user.ST;
        var osts  = user.SA || user.ST;
        if ( typeof regex !== 'undefined' ) {
          name  = name.replace( regex, "<strong>$1</strong>" );
        }
        var a = list.cn('li',{'class':'_42fz _42fz'+user.ID,'tabindex':0}).addClass( osts ).data("user",user),
            b = a.cn('a',{'class':'_50zw _54sz clearfix _54so','rel':'ignore','href':'#'}),
            c = b.cn('div',{'class':'pic_container','title':user.NM}),
            d = c.cn('i',{'class':'pic img pic-loading','alt':user.NM}),
            e = b.cn("div",{"class":"_54sp _52s1"}),
            f = b.cn('div',{'class':'clearfix'},'<div class="rfloat"><div class="icon_container"><div class="active_time icon tinytimestamp livetimestamp" timestamp="0"></div><span class="status icon img"></span></div></div><div><div class="_52zl">'+name+'</div><div class="_52zk"></div></div>');
        if ( typeof regex !== 'undefined' ) {
          $('._52zl',e).addClass('search-highlighted');
        }
        if ( parseInt( user.LS ) !== 0 ) {
          $(".active_time",f).html( timeDifference( user.LS, false, false, true ) ).attr("timestamp", user.LS);
        }
        a.off("mouseenter mouseleave").on("mouseenter",function() {
          var offset  = $(this).offset(),
              width   = $(".dockedSidebar").width(),
              height  = $(this).height();
          if ( !$(this).parents(".dockedSidebar").length ) {
            return;
          }
          var user_box  = $(".user-box");
          if ( !user_box.length ) {
            /*var topW  = ( !isNaN( parseInt( user_box.css("border-top-width") ) ) ) ? parseInt( user_box.css("border-top-width") : 0,
                botW  = ( !isNaN( parseInt( user_box.css("border-top-width") ) ) ) ? parseInt( user_box.css("border-bottom-width") : 0;
            height    = ( height - topW - botW );*/
            user_box  = $("body").cn("div",{"class":"user-box"}).css({
              'line-height': height+"px",
              'height': height+'px',
              'background': b.css("background"),
              'width': ( $(this).width() - width )+'px'
            });
            if ( !isNaN( parseInt( user_box.css("border-top-width") ) ) ) {
              height  = height - parseInt( user_box.css("border-top-width") );
              user_box.height( height );
            }
            if ( !isNaN( parseInt( user_box.css("border-bottom-width") ) ) ) {
              height  = height - parseInt( user_box.css("border-bottom-width") );
              user_box.height( height );
            }
            var g = user_box.cn("div",{"class":"user-box-name"}),
                h = user_box.cn("i",{"class":"user-box-status"});
          }
          if ( !$(".user-box-status",user_box).hasClass( stats ) ) {
            $(".user-box-status",user_box).removeClass("offline online");
          }
          if ( ( offset.top + height ) > $(window).height() || offset.top < 40 ) {
            return;
          }
          user_box.stop().css("top",offset.top+"px").animate({
            left: width+"px"
          },100, function() {
            $(this).css("z-index",1001);
          }).show();
          $(".user-box-name",user_box).html( name );
          if ( !$(".user-box-status",user_box).hasClass( stats ) ) {
            $(".user-box-status",user_box).addClass(stats);
          }
        }).on("mouseleave",function() {
          $(".user-box").css({
            "left": "-"+( $(this).width() - 100 )+"px",
            "z-index": 0
          }).hide();
        });

        /** Events **/
        var image = new Image;
        $(image).data("element",d);
        image.onload  = function() {
          $(this).data("element").removeClass("pic-loading").addClass("pic-loaded").css("background-image",'url("'+this.src+'")');
        };
        image.onerror = function() {
          $(this).data("element").removeClass("pic-loading").addClass("pic-error icon-ban-circle");
        };
        image.src = user.AV;
        b.off("click").on("click",function(event) {
          event.preventDefault();
          var user  = $(this).parent().data("user");
          if ( user && user.ID ) {
            ipUsers.prototype.dock( function( idx, idn ) {
              var tab = ipDockPanel.prototype.process.tab.open( idx, idn );
              ipDockPanel.prototype.process.responsive.tab.resizer.toggle( tab, "open" );
            }, [ user.ID, "user" ] );
          }
        });
        if ( limit ) {
          index++;
          return index < 30;
        }
      });
    },
    resize: function(e) {
      var top = $("._4oes").offset().top;
      $(".ipChatSidebarBody").height( top );
    },
    /*popover: function( elem, uid ) {
      g_50x5( uid, function( user ) {
        user.BT = {
          0: {
            'clas': 'ibtnd',
            'name': 'Block',
            'icon': 'icon-exclamation-sign',
            'link': function(e) {
              e.preventDefault();
            }
          },
          1: {
            'name': 'Chat',
            'icon': 'icon-comment',
            'link': function(e) {
              e.preventDefault();
              ipUsers.prototype.dock( function() {
                ipDockPanel.prototype.process.tab.open( user.ID );
                ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );
              } );
            }
          }
        };
        elem.popover({
          content: user,
          trigger: 'hover'
        }).addClass("popover-hover").popover("show");
      }, function() {
        
      });
    },*/
    notifications: {
      intv: false,
      cont: false,
      list: false,
      init: function( event ) {
        var that  = ipUsers.prototype.user_dock.notifications;
        var uiToggleFlyout  = ( event && event.preventDefault ) ? $(this).next(".uiToggleFlyout:first") : that.cont;
        if ( !that.cont || !that.list ) {
          that.cont = that.cont || uiToggleFlyout;
          that.list = that.list || $(".uiMenuInner",that.cont);
        }
        if ( event && event.preventDefault ) {
          if ( !uiToggleFlyout.length || uiToggleFlyout.is(":hidden") ) {
            return;
          }
        }
        if ( $("li.uiMenuItem",uiToggleFlyout).length ) {
          return;
        }
        if ( typeof ipga("notifications") === "object" && !unds.isEmpty( ipga("notifications") ) ) {
          that.render( $(".uiMenuInner",uiToggleFlyout) );
          return;
        }
        var a = $(".uiMenuInner",uiToggleFlyout).empty().cn("li",{"class":"uiMenuItem withIcon no-notif-item"});
        var b = a.cn("a",{"class":"itemAnchor unselectable","role":"menuitem","tabindex":0},'<i class="icon-warning-sign fcg"></i><span class="itemLabel fsm fcg">'+L.NO_NOTIFICATION+'</span>');
      },
      render: function( list ) {
        var did   = 0;
        var that  = ipUsers.prototype.user_dock.notifications;
        var notifications = ipga("notifications");

        list  = ( list && list.length ) ? list : that.list;
        if ( !that.list ) {
          that.init();
          that  = ipUsers.prototype.user_dock.notifications;
          if ( !list ) {
            list  = that.list;
          }
        }

        if ( !unds.isObject( notifications ) || unds.isEmpty( notifications ) ) {
          return;
        }
        if ( $(".no-notif-item",list).length ) {
          list.empty();
        }

        for( i in notifications ) {
          var notif = notifications[i];
          if ( $('a#ui-notif-'+notif.ID,list).length ) {
            continue;
          }
          var icon  = ( notif.important ) ? 'icon-warning-sign' : ( ( notif.icon ) ? 'icon-'+notif.icon : '' );
          var a = list.cn('li',{'class':'uiMenuItem'+( ( icon ) ? ' hasIcon' : '' )}),
              b = a.cn('a',{'class':'itemAnchor','role':'menuitem','tabindex':0,'href':'#','id':'ui-notif-'+notif.ID}).data("notifID",notif.ID).on("click",that.view),
              c = b.cn('i',{'class':icon}),
              d = b.cn('span',{'class':'itemLabel fsm'},notif.subject);
          did++;
        }

        if ( did > 0 ) {
          ipChat.prototype.play_audio( "notifSound" );
        }

        that.unread( list );
        return did;
      },
      view: function( event ) {
        event.preventDefault();
        if ( _extp("dialog") ) {
          return false;
        }
        var lnk   = $(this);
        var that  = ipUsers.prototype.user_dock.notifications;
        var ID  = lnk.data("notifID");

        _extc("dialog",function(ID,lnk,that) {
          if ( notif = that.read( ID ) ) {
            if ( !notif.important ) {
              lnk.parent().remove();
            }
            return;
          }
          lnk.parent().remove();
        },[ID,lnk,that]);
      },
      unread: function( list, reading ) {
        var that  = ipUsers.prototype.user_dock.notifications;
        list  = ( list && list.length ) ? list : that.list;
        var unread  = ( $("li.uiMenuItem",list).length ).toString(),
            bubble  = $(".ui-notifications span._51jx:first");

        if ( bubble.data("counter") == unread ) {
          return;
        }
        bubble.data("counter", unread);

        if ( unread <= 0 ) {
          bubble.removeClass("sm md lg").addClass("hidden_elem").empty();
          return;
        }

        bubble.removeClass("sm md lg hidden_elem");
        if ( unread < 10 ) {
          bubble.addClass("sm").text( unread );
        }
        else if ( unread < 99 ) {
          bubble.addClass("md").text( unread );
        }
        else {
          bubble.addClass("lg").text( "99+" );
        }

        if ( that.intv ) {
          clearTimeout( that.intv);
        }
        if ( !reading ) {
          bubble.removeClass("animated bounce").addClass("animated bounce");
          that.intv = setTimeout(function() {
            bubble.removeClass("animated bounce");
          }, 2000);
        }
      },
      read: function( id ) {
        var that  = ipUsers.prototype.user_dock.notifications;
        var notifications = ipga("notifications");
        var notif = notifications[id];
        if ( !notif ) {
          return false;
        }
        var el  = $("#ui-notif-"+id);
        if ( el.length ) {
          if ( !notif.important ) {
            el.parent().remove();
            that.unread( false, true );
          }
        }
        var obj = {
          title: notif.subject,
          content: '<div class="notification-time clearfix">\
            <span class="lfloat">\
              <i class="icon-user mrs"></i> '+notif.sender+'\
              '+( ( notif.important ) ? '<span class="mrs mls fcg">|</span> <i class="icon-warning-sign"></i> '+L.IMPORTANT : '' )+'\
            </span>\
            <span class="rfloat">\
              <i class="icon-time mrs"></i> <span timestamp="'+notif.time+'">'+timeDifference( notif.time )+'</span>\
            </span>\
          </div>\
          <div class="notification-content">'+notif.content+'</div>',
          streched: true,
          onopen: function( a, b ) {
            $(".notification-content",a).ipscroll({
              gripper: 'darkGripper'
            });
          },
          buttons: {
            0: {
              text: L.PRINT,
              icon: 'icon-print',
              call: function( e ) {
                e.preventDefault();
                var html  = '<html><head><title>'+notif.subject+'</title></head><body>\
                  <div style="font-family:\'Segoe WP Light\',\'Segoe UI\',Segoe,\'Segoe WP\',Tahoma,Verdana,Arial,sans-serif;">\
                    <h3 style="margin-bottom:0;">'+notif.subject+'</h3>\
                      <div style="margin-bottom:10px;color:#666;">\
                        <small>'+date( 'l, F jS, Y h:i:s A', notif.time )+'</small>\
                      </div>\
                    <div>'+notif.content+'</div>\
                  </div>\
                </body></html>';
                var target  = window.open( "about:blank", "", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=1, resizable=1, copyhistory=no, left=0, top=0, width="+screen.availWidth+", height="+screen.availHeight );
                    target.document.open();
                    target.document.write( html );
                    target.document.close();
                    target.focus();
                    target.print();
                    target.close();
              }
            },
            1: {
              text: L.PREV,
              disb: true,
              icon: 'icon-arrow-left'
            },
            2: {
              text: L.NEXT,
              disb: true,
              icon: 'icon-arrow-right',
              tpos: 'prepend'
            }
          }
        };
        var prev  = pkio( notifications, id );
        var next  = nkio( notifications, id );
        if ( notifications[prev] ) {
          obj.buttons[1].disb = false;
          obj.buttons[1].call = function( e ) {
            e.preventDefault();
            that.read( prev );
          };
        }
        if ( notifications[next] ) {
          obj.buttons[2].disb = false;
          obj.buttons[2].call = function( e ) {
            e.preventDefault();
            that.read( next );
          };
        }
        $().ipbox( obj );
        if ( !notif.important ) {
          ipqx(ipgo('docServer')+'ipChat/pull.php','post',{
            channel: 'notifications',
            process: 'read',
            notif: id
          });
          delete notifications[id];
        }
        return notif;
      }
    },
    search: {
      data: {},
      last: '',
      start: function( query, list ) {
        var that  = ipUsers.prototype.user_dock;
        var data  = that.search.data;
        var hasrs = false;

        if ( that.search.last === query ) {
          return;
        }
        that.search.last  = query;

        $(".uiTypeaheadProessing").addClass("uiTypeaheadProessing");

        if ( ipga("first_degree_finished") ) {
          if ( unds.isObject( data[query] ) ) {
            var results = data[query];
          }
          else {
            var results = ipos( ipga("users"), "NM", query );
            that.search.data[query] = results;
          }
        }
        else {
          var results = ipos( ipga("users"), "NM", query );
        }

        list.addClass("containSearch").empty();
        $(".popover").remove();

        if ( !unds.isEmpty( results ) ) {
          hasrs = true;
          that.list( list, results, true, false, query );
        }

        if ( !hasrs ) {
          if ( !list.find(".single-row").length ) {
            list.addClass("containSearch").empty();
            list.cn('li',{'class':'single-row'},'<i class="icon-warning-sign fcg mrs"></i><span class="fsm fcg">'+L.NO_MATCHES_FOUND+'</span>');
          }
        }

        ipUsers.prototype.first_degree( function( query ) {
          $(".ipChatTypeahead._57du input.inputsearch").trigger("keyup");
        }, [ query ], function() {
          $(".uiTypeaheadProessing").removeClass("uiTypeaheadProessing");
        }, [] );
      },
      stop: function( list ) {
        var that  = ipUsers.prototype.user_dock;
        if ( list.hasClass("containSearch") ) {
          list.removeClass("containSearch");
          $(".popover").remove();
          that.list( list, ipga("users_base"), true, true );
        }
      }
    }
  }
});

var dbt = function(a,b) {
  if ( a.data("disabled") == b ) {
    return;
  }
  var c = $("textarea._552m, input[type=file]",a).attr("disabled",true);
  c.filter("textarea").attr("placeholder",L.YOU_CANNOT_REPLY).parent().addClass("_552hd");
  a.data("disabled",b).on("contextmenu", function(event) {
    event.preventDefault();
    return false;
  }).unselectable();
},
abt = function(a) {
  if ( !a.data("disabled") ) {
    return;
  }
  var b = $("textarea._552m, input[type=file]",a).removeAttr("disabled");
  b.filter("textarea").attr("placeholder",L.WRITE_REPLY+"...").parent().removeClass("_552hd");
  a.removeData("disabled").off("contextmenu");
},
getUploadVars = function( tab, message, prf ) {
  if ( !( tab && ( tab instanceof jQuery ) ) && !prf ) {
    return false;
  }
  message = ( message === true ) ? "message" : ( ( message ) ? message : "attachment" );
  var idx = ( prf ) ? ipga("user").ID : tab.data("nubuid"),
      idn = ( prf ) ? "user" : tab.data("nubmod");
  var a1  = $("<div />"),
      b1  = a1.cn("input",{"type":"hidden","name":"source","value":"frame"}),
      c1  = a1.cn("input",{"type":"hidden","name":"nubuid","value":idx}),
      d1  = a1.cn("input",{"type":"hidden","name":"nubmod","value":idn}),
      e1  = a1.cn("input",{"type":"hidden","name":"channel","value":"attachments"}),
      f1  = a1.cn("input",{"type":"hidden","name":"process","value":message}),
      g1  = a1.cn("input",{"type":"hidden","name":"source_alt_code","value":"chat"}),
      h1  = a1.cn("input",{"type":"hidden","name":"source_alt_name","value":"Chat"}),
      i1  = a1.cn("input",{"type":"hidden","name":"relation_id","value":uniqid()});
  return a1.html();  
},
/** Userinfo with Auto Callback **/
isr_50x5 = function(b) { // Is getUserInfo CB Running
  var a = ipga("r_50x5") || ipsa("r_50x5", {});
  return a && !0 === a[b]
},
rir_50x5 = function(b) {
  var a = ipga("r_50x5") || ipsa("r_50x5", {});
  delete a[b];
  return ipsa("r_50x5", a);
},
sir_50x5 = function(b) { // Set getUserInfo CB Running
  var a = ipga("r_50x5") || ipsa("r_50x5", {});
  a[b] = !0;
  return ipsa("r_50x5", a)
},
ccb_50x5 = function(b, d, f) { // Call getUserInfo CB
  var c = ipga("cb_50x5") || ipsa("cb_50x5", {}), a = unds.isArray(c[b]) ? unds.clone(c[b]) : [];
  c[b] = [];
  ipsa("cb_50x5", c);
  if(a && !unds.isEmpty(a)) {
    for(e = 0;e < a.length;e++) {
      rir_50x5(b);
      b = a[e][2], b.unshift(d), !0 === f ? "function" === typeof a[e][1] && call_user_func_array(a[e][1], b) : "function" === typeof a[e][0] && call_user_func_array(a[e][0], b)
    }
  }
},
acb_50x5 = function(b, c, d, e) { // Add getUserInfo CB
  var a = ipga("cb_50x5") || ipsa("cb_50x5", {});
  a[b] = a[b] || [];
  a[b].push([c, d, e]);
  ipsa("cb_50x5", a);
  return a[b]
},
abcb_50x5  = function(b) { // Add getUserInfo CB
  var a = ipga("bcb_50x5") || ipsa("bcb_50x5", {});
  a[b]  = true;
  return ipsa("bcb_50x5",a);
},
dbcb_50x5  = function(b) { // Add getUserInfo CB
  var a = ipga("bcb_50x5") || ipsa("bcb_50x5", {});
  delete a[b];
  return ipsa("bcb_50x5",a);
},
hbcb_50x5  = function(b) { // Add getUserInfo CB
  var a = ipga("bcb_50x5") || ipsa("bcb_50x5", {});
  return a[b];
},
g_50x5  = function( id, callback1, callback2, args, force ) { //getUserInfo
  args  = ( unds.isArray( args ) ) ? args : [];
  var users = ipga("users");
  if ( parseInt( id ) === parseInt( ipga("user").ID ) ) {
    var user  = ipga("user");
  }
  else {
    var user  = users[id];
  }
  if ( typeof callback1 !== "function" && user ) {
    return user;
  }

  if ( user ) {
    args  = ( unds.isArray( args ) ) ? args : [];
    args.unshift( user );
    if ( typeof callback1 === "function" ) {
      return call_user_func_array( callback1, args );
    }
  }
  else {
    if ( hbcb_50x5( id ) && !force ) {
      args.unshift( false );
      return call_user_func_array( callback2, args );
    }
    if ( isr_50x5( id ) ) {
      acb_50x5( id, callback1, callback2, args );
      return;
    }

    sir_50x5( id );
    acb_50x5( id, callback1, callback2, args );
    if ( has_action( "IP_load_user_info" ) ) {
      do_action( "IP_load_user_info", true, id, function( id, response ) {
        if ( response.error ) {
          abcb_50x5( id );
          //ccb_50x5( id, response.message, true );
          setTimeout( function() {
            //console.log( id, response.message, true );
            ccb_50x5( id, response.message, true );
          }, 1000 );
          return;
        }
        var users = ipga("users") || ipsa("users",{});
            users[response.ID]  = response;
        ipsa("users",users);
        //ccb_50x5( id, response, false );
        setTimeout( function() {
          ccb_50x5( id, response, false );
        }, 1000 );
      } );
      return;
    }
    ipqx( ipgo('docServer')+'ipChat/pull.php', "POST", {
      channel: 'users',
      action: 'get',
      id: id
    }, {
      onsuccess: function( response ) {
        if ( response.error ) {
          abcb_50x5( id );
          //ccb_50x5( id, response.message, true );
          setTimeout( function() {
            //console.log( id, response.message, true );
            ccb_50x5( id, response.message, true );
          }, 1000 );
          return;
        }
        var users = ipga("users") || ipsa("users",{});
            users[response.ID]  = response;
        ipsa("users",users);
        //ccb_50x5( id, response, false );
        setTimeout( function() {
          ccb_50x5( id, response, false );
        }, 1000 );
      }
    });
  }
},
users_batch_call  = function( users_id, callback, args, force ) {
  var users = unds.keys( ipga("users") || ipsa("users",{}) );

  if ( !force ) {
    users_id  = unds.difference( users_id, users );
    if ( !users_id.length ) {
      call_user_func_array( callback, args );
      return;
    }
  }

  ipqx(ipgo('docServer')+'ipChat/pull.php', "POST", {
    channel: 'users',
    action: 'batch',
    id: users_id
  }, {
    onsuccess: function( response ) {
      if ( response.error ) {
        call_user_func_array( callback, args );
        return;
      }
      var users = $.extend({}, ipga("users"), response );
      ipsa("users", users);
      call_user_func_array( callback, args );
    }
  });
},
cops  = function( idx, idn, idy, idz ) {
  idz = ( !unds.isArray( idz ) ) ? [] : idz;
  if ( idx ) {
    idx = ( !unds.isArray( idx ) ) ? [] : idx;
  }
  else {
    idx = false;
  }

  var onsuccess_func  = function( response ) {
        if ( response.error ) {
          idz.push( response.message );
          call_user_func_array( idy, idz );
          return;
        }
        idz.push( false );
        call_user_func_array( idy, idz );
        if ( ipWebSocket != false ) {
          ipWebSocket.send(
            json_encode({
              event: "status",
              user: ipga( "user" ).ID
            })
          );
        }
      },
      onerror_func  = function( response, error ) {
        idz.push( error.message );
        call_user_func_array( idy, idz );
      };

  do_action( "onupdate_chat_status", false, idx, idn );
  if ( has_action( "IP_onupdate_chat_status" ) ) {
    do_action( "IP_onupdate_chat_status", true, idn, idx, onsuccess_func, onerror_func )
    return;
  }

  ipqx(ipgo('docServer')+'ipChat/pull.php', "POST", {
    channel: 'settings',
    process: 'chat',
    action: 'quick',
    idx: idx,
    idn: idn
  }, {
    onsuccess: onsuccess_func,
    onerror: onerror_func
  });
};