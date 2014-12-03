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
var imageLoad = false,
    pingStartedMob  = false;
var ipMobile  = ipChat.extend({
  imageResizer: function(e,f){var a=parseInt(e),b=parseInt(f),d=$(window).width()-130,c=$(window).height()-130+45;a>d&&b<c?(nw=Math.round(d-50),nh=Math.round(nw/a*b)):b>c&&a<d?(nh=Math.round(c-50),nw=Math.round(nh/b*a)):b>c&&a>d?(rf=b>a?b/a:a/b,b>a?(nh=Math.round(b/rf-160),nw=Math.round(nh/b*a)):(nw=Math.round(a/rf-160),nh=Math.round(nw/a*b))):(nh=b,nw=a);nh>c&&(nh=Math.round(c-50),nw=Math.round(nh/b*a));return{w:nw,h:nh,t:(c-nh)/2,l:(d-nw)/2+40}},
  initialize: function() {
    this.users.that = this;
    this.page.that  = this;
    this.page.init( [ this.users, 'init' ] );
    $(document).on("click", "a[href^=#]", function(e) {
      if ( !$("input",this).length ) {
        e.preventDefault();
      }
    });
    $("body").addClass("ui-nosvg");
    var theme = ipgo("defaultTheme") || "facebook";
    iplj('themes/mobile/'+theme,false,function() {},function() {},'css');
    setInterval(function() {
      $('span[timestamp], abbr[timestamp]').each(function(e) {
        $(this).text( timeDifference( $(this).attr("timestamp"), false, false, $(this).attr("short") ) );
      });
    }, 5000)
    $(document).on("click", "a.image-slider", function(e) {
      e.preventDefault();
      if ( !$(this).data("attachment") ) {
        return;
      }

      var target  = $(this),
          attach  = target.data("attachment"),
          title   = target.attr("title"),
          popupid = "#popup-image-"+attach.ID;
      if ( $(popupid).length ) {
        $(popupid).popup("open");
        return;
      }
      var prev  = target.prev("a.image-slider:first");
      var next  = target.next("a.image-slider:first");

      var close   = '<a href="#" data-rel="back" data-role="button"  class="ui-btn ui-btn-right ui-icon-delete ui-btn-icon-notext ui-corner-all">Close</a>',
          navbtn  = '<div data-role="controlgroup" data-type="horizontal" data-mini="true" class="ui-btn-left"><a href="#" data-role="button" data-theme="a" data-icon="arrow-l" data-iconpos="notext" data-shadow="false" data-iconshadow="false">Left</a><a href="#" data-role="button" data-theme="a" data-icon="arrow-r" data-iconpos="notext" data-shadow="false" data-iconshadow="false">Right</a></div>'
          image   = new Image,
          loader  = '<div class="mCenteredLoaderBase"><div class="mCenteredLoader mCenteredLoaderVertical fcg"><div class="_2so _2sq _2ss _50cg" style="vertical-align: middle;"></div> <span>Loading...</span></div></div>',
          popup   = $('<div data-role="popup" id="popup-image-'+attach.ID+'" data-short="image-'+attach.ID+'" data-theme="none" data-overlay-theme="a" data-corners="false" data-tolerance="15">'+navbtn+close+loader+'</div>');

      $.mobile.activePage.append( popup ).trigger("pagecreate");
      if ( !popup.data("mobile-popup") ) {
        popup.popup();
        $("a",popup).button();
        $('div[data-role="controlgroup"]',popup).controlgroup();
      }
      popup.popup("open");

      var controls  = $(".ui-controlgroup a",popup);
      controls.eq(0).on("click", function(e) {
        e.preventDefault();
        if ( prev.length ) {
          popup.popup("close");
          prev.click();
        }
      });
      controls.eq(1).on("click", function(e) {
        e.preventDefault();
        if ( next.length ) {
          popup.popup("close");
          next.click();
        }
      });
      controls.hide(0);
      if ( !prev.length ) {
        controls.eq(0).addClass("ui-disabled");
      }
      if ( !next.length ) {
        controls.eq(1).addClass("ui-disabled");
      }
      if ( !next.length && !prev.length ) {
        controls.parents(".ui-controlgroup").remove();
      }
      image.onload  = function() {
        controls.show(0);
        var o = ipMobile.prototype.imageResizer( this.width, this.height ),
            w = this.width,
            h = this.height;
        $(this).insertAfter( $(".mCenteredLoaderBase",popup) ).data({
          width: w,
          height: h
        }).addClass("pop-photo").width( o.w ).height( o.h ).on("click", function() {
          var next  = target.next("a.image-slider:first");
          if ( next.length ) {
            popup.popup("close");
            next.click();
          }
        });
        $(".mCenteredLoaderBase",popup).remove();
        popup.popup("reposition", "window");
      };
      image.onerror = function() {
        popup.popup("close");
      };
      image.src = target.attr("href");
      image.alt = title;
    });
    $(document).on("popupbeforeposition", ".ui-popup", function() {
      var image = $(".pop-photo",this);
      if ( image.length && image.data("width") ) {
        var o = ipMobile.prototype.imageResizer( image.data("width"), image.data("height") );
        image.width( o.w ).height( o.h );
      }
    });
    $(document).on("popupafterclose", ".ui-popup", function() {
      $(this).remove();
    });
    $(window).on("resize", function() {
      if ( $(".ui-popup").length ) {
        $(".ui-popup").popup("reposition", "window");
      }
      var sliders = $("._673");
      if ( !sliders.length ) {
        return;
      }
      var totalWidth  = ( $(window).width() - 200 );
      sliders.each(function() {
        var images  = $("i._675",this);
        var imagesLen   = Math.min( ( ( totalWidth > 400 ) ? 4 : 3 ), images.length );
        var availWidth  = Math.min( Math.floor( totalWidth / imagesLen ), 600 );
        if ( totalWidth > 400 && imagesLen == 1 ) {
          availWidth  = Math.min( totalWidth - 100, availWidth );
        }
        availWidth  = Math.max( 226, availWidth );
        images.width( availWidth ).height( availWidth );
      });
    });
  },
  data: false,
  cache: {},
  tokenizer: function( input, callbacks, except ) {
    except  = except || [];
    var search  = function() {
      
    };
    var do_call = function( call, args ) {
      if ( unds.isObject( callbacks ) && callbacks.hasOwnProperty( call ) ) {
        call_user_func_array( callbacks[call], args );
      }
    };
    var get_tokens  = function( tokens, length ) {
      var obj = {};
      tokens.each(function() {
        var inp = $("input",this);
        obj[inp.eq(0).val()]  = inp.eq(1).val();
      });
      return ( length ) ? count( obj ) : obj;
    };
    var hide_picker = function( picker ) {
      if ( !picker.hasClass("fixed-state") ) {
        picker.empty().hide(0);
        return true;
      }
      return false;
    };
    var show_picker = function( picker ) {
      picker.empty().show(0);
    };

    input.on("keyup keydown focus blur", function(e) {
      var value   = $.trim( input.val() ),
          picker  = $("#friend-picker"),
          container = input.prev(".token-container:first"),
          tokens    = $("span._59tt",container);

      if ( 8 === e.keyCode && tokens.length && !value.length && e.type == "keydown" ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var lid = tokens.filter(":last"),
            lvl = lid.find("input:eq(0)").val(),
            lem = $("#_51v0"+lvl);
        lid.remove();
        lem.remove();
        var tok = get_tokens( $("span._59tt",container) );
        do_call( "clear", [ tok, count( tok ) ] );
        return;
      }
      if ( 65 === e.keyCode && e.ctrlKey ) {
        input.select();
        return;
      }
      if ( "blur" === e.type ) {
        input.val("");
        return;
      }
      if ( "keyup" === e.type || "keydown" === e.type ) {
        if ( e.keyCode == 13 ) {
          e.preventDefault();
          return false;
        }
      }
      if ( "keydown" === e.type ) {
        return;
      }
      if ( !picker.hasClass("fixed-state") && value.length < 3 ) {
        hide_picker( picker );
        return;
      }

      var except_cur  = unds.clone( except );
      if ( !picker.hasClass("fixed-state") ) {
        tokens.each(function() {
          except_cur.push( parseInt( $("input",this).eq(0).val() ) );
        });
      }
      except_cur  = unds.uniq( except_cur );

      var users = ipos( ipga("users"), "NM", value, false, 20, function( user ) {
        return ( $.inArray( user.ID, except_cur ) >= 0 );
      });

      if ( !unds.isEmpty( users ) ) {
        show_picker( picker );
        var tok = get_tokens( $("span._59tt",container) );
        do_call( "show", [ tok, count( tok ) ] );

        for( x in users ) {
          var user  = users[x],
              token = $("span#_59tt_"+user.ID,container);

          var a1  = picker.cn("div",{"class":"_51v0 aclb _51v0"+user.ID}).data("user",user),
              b1  = a1.cn("div",{"class":"_51li touchable"+( ( token.length ) ? " selected" : "" )}),
              c1  = b1.cn("img",{"class":"_51u-","src":user.AV}),
              d1  = b1.cn("span",{"class":"_51lj"},user.NM);

          a1.on("click", function(e) {
            e.preventDefault();
            var user  = $(this).data("user"),
                token = $("span#_59tt_"+user.ID,container);
            if ( token.length ) {
              $("._51li",this).removeClass("selected");
              token.remove();
              var tok = get_tokens( $("span._59tt",container) );
              do_call( "clear", [ tok, count( tok ) ] );
            }
            else {
              $("._51li",this).addClass("selected");
              var a2  = container.cn("span",{"class":"_59tt touchable","id":"_59tt_"+user.ID},user.NM),
                  b2  = a2.cn("input",{"type":"hidden","name":"tokens[]"}).val( user.ID ),
                  c2  = a2.cn("input",{"type":"hidden"}).val( user.NM );
              var tok = get_tokens( $("span._59tt",container) );
              do_call( "add", [ {id:user.ID,name:user.NM}, tok, count( tok ) ] );
            }
            if ( hide_picker( picker ) ) {
              var tok = get_tokens( $("span._59tt",container) );
              do_call( "hide", [ tok, count( tok ) ] );
            }
          });
        }
      }
    });

    return {
      has: function() {
        return unds.keys( get_tokens( $("span._59tt",input.prev(".token-container:first")) ) ).length;
      },
      get: function() {
        return unds.keys( get_tokens( $("span._59tt",input.prev(".token-container:first")) ) );
      },
      empty: function() {
        return input.prev(".token-container:first").empty();
      },
      enable: function( check ) {
        return ( check ) ? ( !input.is(":disabled") ) : input.removeAttr("disabled");
      },
      disable: function( check ) {
        return ( check ) ? ( input.is(":disabled") ) : input.prop("disabled", true);
      }
    };
  },
  files: {
    ajax: false,
    list: {},
    upload: function( files, input, holder, upload_callback, remove_callback ) {
      ipMobile.prototype.files.ajax = false;
      var file  = ( files && files.length ) ? files[0] : false;
      if ( !file ) {
        return;
      }
      var mime  = file.type;
      var extn  = pathinfo( file.name, "PATHINFO_EXTENSION" );
      if ( is_blocked = isfbd( extn, mime, true ) ) {
        return;
      }
      if ( !/^image\//.test( mime ) ) {
        return;
      }

      input.prop("disabled", true);
      holder.removeClass("_51r_");

      var tempr   = $("._51s2:last",holder);
      tempr.clone().insertAfter( tempr );
      tempr.removeClass("_51r_");
      $(".async_throbber",tempr).removeClass("async_saving_visible");
      $("._51s3",tempr).on("click", function(e) {
        e.preventDefault();
        input.removeAttr("disabled").empty();
        if ( ipMobile.prototype.files.ajax !== false ) {
          ipMobile.prototype.files.ajax.abort();
        }
        if ( tempr.data("attachment") ) {
          var aid = tempr.data("attachment").ID;
          delete ipMobile.prototype.files.list[aid];
          $.post(ipgo("docServer") + "ipChat/pull.php", {channel:"attachments", process:"attachment", action:"delete", idx:aid}, !1, "json");
        }
        tempr.remove();
        if ( remove_callback ) {
          call_user_func_array( remove_callback, [ $("._51s2",holder).length - 1 ] );
        }
      });

      var form  = new FormData();
      form.append("attachment",file),
      form.append('_ksht',1),
      form.append('_kshc',1),
      form.append("channel","attachments");
      form.append("process", "attachment"),
      form.append("nubuid",1),
      form.append("nubmod",1),
      form.append("source","mobile");
      form.append("relation_id","rel-"+uniqid());

      var xhr = new ipXhr;
      xhr.open( ipgo('docServer')+'ipChat/pull.php', "POST", true );
      xhr.callback({
        onsuccess: function( response ) {
          if ( !response.attachment ) {
            $(".async_throbber",tempr).remove();
            tempr.cn("div",{"class":"_5r-4"});
            //tempr.remove();
            return;
          }
          if ( upload_callback ) {
            call_user_func_array( upload_callback );
          }
          $(".async_throbber",tempr).addClass("async_saving_visible");
          tempr.css({
            'background-image': 'url("'+ipgo("docServer")+response.attachment.thumbnail+'")'
          });
          ipMobile.prototype.files.list[response.attachment.ID] = response.attachment;
          tempr.data("attachment",response.attachment);
        },
        onerror: function() {
          $(".async_throbber",tempr).remove();
          tempr.cn("div",{"class":"_5r-4"});
          //tempr.remove();
        },
        onloadend: function() {
          input.removeAttr("disabled");
          ipMobile.prototype.files.ajax = false;
        }
      });
      xhr.send( form );
      ipMobile.prototype.files.ajax = xhr;
    }
  },
  load: {
    pages: {
      async: function() {
        ipMobile.prototype.data = false;
        var page  = $("#tempLoading");
        $(".ui-header .ui-title",page).html( L.LOADING+"&hellip;" );
        $(".ui-content",page).html( '<div class="mCenteredLoader mCenteredLoaderVertical fcg"><div class="_2so _2sq _2ss img _50cg"></div> <span>'+L.LOADING+'&hellip;</span></div>');
        $.mobile.changePage( page, {changeHash:false,transition:"none"} );
      },
      messages: function() {
        this.async();
        $.mobile.changePage( ipgo("docServer")+"ipChat/pages.php?p=messages", {
          changeHash: false,
          dataUrl: ipgo("mobileURI")+"?p=messages",
          reverse: true,
          reloadPage: false,
          showLoadMsg: false
        } );
      },
      users: function() {
        this.async();
        $.mobile.changePage( ipgo("docServer")+"ipChat/pages.php?p=users", {
          changeHash: false,
          dataUrl: ipgo("mobileURI"),
          reverse: true,
          reloadPage: false,
          showLoadMsg: false
        } );
      },
      compose: function() {
        this.async();
        $.mobile.changePage( ipgo("docServer")+"ipChat/pages.php?p=compose", {
          changeHash: false,
          dataUrl: ipgo("mobileURI")+"?p=compose",
          reverse: true,
          reloadPage: false,
          showLoadMsg: false
        } );
      },
      chat: function() {
        this.async();
        var par = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
            idx = par.i,
            idn = ( par.t == "u" ) ? "user" : "group";
        var xhr = new ipXhr();
        xhr.open( ipgo('docServer')+'ipChat/pull.php' );
        xhr.params({
          channel: 'messages',
          process: 'message',
          action: 'load',
          id: idx,
          type: idn,
          older: false
        });
        xhr.callback({
          onsuccess: function( response ) {
            if ( response.error ) {
              return;
            }
            add_50dw( idx, idn, response );
            ipMobile.prototype.data = response;
          },
          onerror: function( response, error ) {
            
          },
          onloadend: function() {
            $.mobile.changePage( ipgo("docServer")+"ipChat/pages.php?p=chat", {
              changeHash: false,
              dataUrl: ipgo("mobileURI"),
              reverse: true,
              reloadPage: false,
              showLoadMsg: false
            } );
          }
        });
        xhr.send();
      },
      actions: function( action ) {
        var dialog  = $("#dialog-dynamic").data("async","actions");
        $.mobile.changePage( dialog, {
          reverse: true,
          reloadPage: false,
          showLoadMsg: false,
          changeHash: false
        } );
      },
      options: function() {
        var dialog  = $("#dialog-dynamic").data("async","options");
        if ( $.mobile.activePage.is( dialog ) ) {
          $.mobile.activePage.trigger("pageshow");
          return;
        }
        $.mobile.changePage( dialog, {
          reverse: true,
          reloadPage: false,
          showLoadMsg: false,
          changeHash: false
        } );
      }
    }
  },
  chat: {
    send: function( idx, idn, message, call1, call2, call3 ) {
      if ( parseInt( idx ) === 0 || isNaN( parseInt( idx ) ) ) {
        return;
      }
      var page  = $("#messenger");
      var ida = unds.keys( ipMobile.prototype.files.list );
      var idt = $("textarea._52t1",page);
      var id1 = $("._5aqv ._5cqb",page);
      var id2 = $("a.send-btn",page);
      var id3 = $("#m-messaging-composer-container",page);
      var id4 = $("#m-messaging-error-message",page);
      
      if ( typeof message === "object" ) {
        if ( message.failed === true ) {
          var params  = ipDockPanel.prototype.process.chat.messages.params( message, true );
        }
        else {
          var params  = ipDockPanel.prototype.process.chat.messages.params({
            id: message.id,
            type: message.type,
            message_o: message.message_o,
            message_n: message.message_n,
            attachments: message.attachments,
            is_notice: message.is_notice,
            notice_section: message.notice_section
          });
        }
      }
      else {
        if ( typeof message === 'undefined' || !$.trim( message ).length ) {
          message = $.trim( idt.val() );
          idt.val('').trigger('autosize.resize');
        }
        else {
          message = $.trim( message );
        }
        if ( !message.length && !ida.length ) {
          return;
        }
        var params  = ipDockPanel.prototype.process.chat.messages.params({
          id: idx,
          type: idn,
          message_o: message,
          message_n: message,
          attachments: ida,
          is_notice: 0,
          notice_section: 'left'
        });
      }

      var bubble  = unds.clone( params );
      bubble.attachments  = ipMobile.prototype.files.list;
      if ( ida && ida.length ) {
        ipMobile.prototype.files.list = {};
      }
      if ( ida && ( ida.length === ipos( bubble.attachments, "mimegroup", "image" ) ) ) {
        params.has_attachment = bubble.has_attachment = 1;
        params.notice_section = bubble.notice_section = "attachment";
        params.is_notice  = bubble.is_notice = 1;
      }

      if ( idt.length ) {
        id1.addClass("_51r_");
        $("._51s2",id1).not(":last").remove();
        idt.val('').trigger('autosize.resize').prop("disabled",true);
        if ( !id2.data("mobile-button") ) {
          id2.button();
        }
        id2.button("disable");
        id3.addClass("async_composer_saving").removeClass("async_composer");
        id4.hide(0).removeData("message");
      }

      var xhr = new ipXhr();
      xhr.open( ipgo('docServer')+'ipChat/pull.php' );
      xhr.params({
        channel: 'messages',
        process: 'message',
        action: 'send',
        message: params
      });
      xhr.callback({
        onsuccess: function( response ) {
          if ( response.error ) {
            if ( id4.length ) {
              params.failed = true;
              id4.show(0).data("message",params);
            }
            if ( call2 ) {
              call_user_func_array( call2, [ response ] );
            }
            return;
          }
          if ( call1 ) {
            call_user_func_array( call1, [ response ] );
            return;
          }
          add_50dw( idx, idn, response );
          ipMobile.prototype.chat.render.item( response );
          $("body").scrollTop( $("body")[0].scrollHeight );
        },
        onerror: function( response, error ) {
          if ( id4.length ) {
            params.failed = true;
            id4.show(0).data("message",params);
          }
          if ( call2 ) {
            call_user_func_array( call2, [ response ] );
          }
        },
        onloadend: function() {
          if ( idt.length ) {
            id3.addClass("async_composer").removeClass("async_composer_saving");
            id2.button("enable");
            idt.removeAttr("disabled",true).focus();
          }
          if ( call3 ) {
            call_user_func_array( call3, [ response ] );
          }
        }
      });
      xhr.send();
      return message.unID;
    },
    render: {
      history: function( messages ) {
        if ( !$("#see_older_threads").is(":visible") ) {
          return false;
        }
        if ( unds.isEmpty( messages ) ) {
          return false;
        }
        var menu    = $("#threadlist_rows .threadlist:first"),
            userid  = parseInt( ipga("user").ID ),
            temp    = ipMobilePing.prototype.parse.temp;
        for( x in messages ) {
          var message = messages[x],
              sentCat = ( parseInt( message.groupID ) === 0 || isNaN( parseInt( message.groupID ) ) ) ? "user" : "group",
              catID   = ( sentCat === "group" ) ? parseInt( message.sent_to ) : parseInt( ( message.sent_from === userid ) ? message.sent_to : message.sent_from ),
              thread  = $("#threadlist_row_id_"+sentCat+"_"+catID);

          if ( thread.length ) {
            return;
          }

          var a1  = menu.cn("div",{"class":"item del_area async_del tall acw","id":"threadlist_row_id_"+sentCat+"_"+catID}),
              b1  = a1.cn("a",{"href":"#","class":"touchable primary"}).data({
                i: catID,
                t: sentCat.substr(0,1)
              }),
              c1  = b1.cn("div",{"class":"primarywrap"}),
              d1  = c1.cn("div",{"class":"image"}),
              e1  = c1.cn("div",{"class":"content"}),
              f1  = e1.cn("div",{"class":"lr"}),
              g1  = f1.cn("div",{"class":"time r nowrap mfss fcl"},'<abbr timestamp="'+message.sent_date+'" short="true">'+timeDifference( message.sent_date, false, false, true )+'</abbr>'),
              h1  = f1.cn("div",{"class":"title mfsl fcb"},"<strong>Loading&hellip;</strong>"),
              i1  = f1.append( '<div class="clear"></div>' ),
              j1  = e1.cn("div",{"class":"twoLines preview mfss fcg"}),
              k1  = j1.cn("span",{"class":"snippet"});

          if ( temp[sentCat] && temp[sentCat][catID] && temp[sentCat][catID].length ) {
            $("<span />",{"class":"num-msg"}).text( temp[sentCat][catID].length ).appendTo( d1 );
          }

          b1.on("click", function(e) {
            e.preventDefault();
            var d = $(this).data();
            ipMobile.prototype.history.add( "?i="+d.i+"&t="+d.t );
          });
          thread  = a1;

          if ( message.is_sticker ) {
            g_50x5( message.sent_from, function( user, thread, len ) {
              var text  = sprintf( L.SENT_STICKER, ( ( user.ID === userid ) ? L.YOU : user.NM ) );
              $(".snippet",thread).html( text );
            }, function( user, thread, len ) {
              var text  = sprintf( L.SENT_STICKER, ( ( user.ID === userid ) ? L.YOU : L.SOMEONE ) );
              $(".snippet",thread).html( text );
            }, [ thread, count( message.attachments ) ]);
          }
          else if ( message.notice_section === "attachment" ) {
            g_50x5( message.sent_from, function( user, thread, len ) {
              var text  = sprintf( ( ( len === 1 ) ? L.SENT_IMAGE : L.SENT_IMAGES ), ( ( user.ID === userid ) ? L.YOU : user.NM ) );
              $(".snippet",thread).html( text );
            }, false, [ thread, count( message.attachments ) ]);
          }
          else {
            var content = mobileParseMesageText( message.message );
            if ( !content.length ) {
              if ( message.has_attachment === 1 ) {
                if ( !unds.isEmpty( message.attachments ) ) {
                  var images  = count( ipos( message.attachments, "mimegroup", "image" ) );
                  var total   = count( message.attachments );
                  if ( total == images ) {
                    content = sprintf( ( ( total === 1 ) ? L.SENT_IMAGE : L.SENT_IMAGES ), '' )
                  }
                  else {
                    content = $.trim( sprintf( L.SENT_FILES, '' ) );
                  }
                }
              }
            }
            if ( !content.length ) {
              content = "text not available";
            }
            if ( sentCat === "group" && !message.is_notice ) {
              g_50x5( message.sent_from, function( user, thread, content ) {
                var name  = ( user.ID === ipga("user").ID ) ? L.YOU : user.NM;
                $(".snippet",thread).html( name+": "+content );
              }, function( user, thread, content ) {
                $(".snippet",thread).html( "undefined: "+content );
              }, [ thread, content ]);
            }
            else {
              $(".snippet",thread).html( content );
            }
          }

          if ( sentCat == "user" ) {
            var profpic = d1.cn("i",{"class":"img profpic"});
            g_50x5( catID, function( user, profpic, thread ) {
              $(".title strong",thread).html( user.NM );
              profpic.css({
                'background-image': 'url("'+user.AV+'")'
              });
            }, function( user, profpic, thread ) {
              $(".title strong",thread).html( "undefined" );
              profpic.css({
                'background-image': 'url("'+ipgo("docServer")+"ipChat/images/users/default_unavail.jpg"+'")'
              });
            }, [ profpic, thread ] );
          }
          else {
            g_50x4( catID, function( group, thread, sent ) {
              if ( !group.name || !group.name.length ) {
                return;
              }
              $(".title strong",thread).html( group.name );
              var users = unds.clone( group.users );
              if ( sent === ipga("user").ID && users.length !== 3 ) {
                users.unshift( sent );
                users = unds.uniq( users );
              }
              else {
                users = unds.without( users, ipga("user").ID );
              }
              var len = Math.min( 2, ( users.length - 1 ) ),
                  dms = {'2':[[39,58],[19,29],[19,29]],'1':[[29,58],[29,58]]};

              for( i = 0; i <= len; i++ ) {
                var profpic = $(".image",thread).cn("i",{"class":"img _4j2 profpic"}).css({
                  width: dms[len][i][0]+"px",
                  height: dms[len][i][1]+"px"
                });
                if ( i !== 0 ) {
                  profpic.addClass("_4j3");
                }
                g_50x5( users[i], function( user, profpic ) {
                  profpic.css({
                    'background-image': 'url("'+user.AV+'")'
                  });
                }, function( user, profpic ) {
                  profpic.css({
                    'background-image': 'url("'+ipgo("docServer")+"ipChat/images/users/default_unavail.jpg"+'")'
                  });
                }, [ profpic ]);
              }
              $(".image i",thread).wrapAll( $("<div />",{"class":"_4j1"}) );
            }, false, [ thread, message.sent_from ]);
          }
        }
        $(".item",menu).addClass("abt").filter(":first").removeClass("abt");
        $.mobile.activePage.trigger("pagecreate");
      },
      actions: function( dialog ) {
        var async_page  = dialog.data("async");
        if ( !async_page ) {
          return;
        }

        var params  = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
            action  = params.a,
            right_b = $('<a href="#" class="ui-btn ui-btn-icon-notext ui-corner-all" data-rel="back">'+L.LOADING+'&hellip;</a>').button(),
            loader  = $('<div class="mCenteredLoaderBase"><div class="mCenteredLoader mCenteredLoaderVertical fcg"><div class="_2so _2sq _2ss _50cg" style="vertical-align: middle;"></div> <span>'+L.LOADING+'&hellip;</span></div></div>'),
            header  = $(".ui-header",dialog),
            content = $(".ui-content",dialog);

        var add_loader  = function() {
          content.html( loader );
          $("a",header).remove();
          $("h1.ui-title",header).html( L.LOADING+"&hellip;" );
        };
        add_loader();

        if ( async_page == "actions" ) {
          content.addClass("no-padding");
          var l_add   = $('<div class="instant-add-people"><div class="_4g33 _5hu6"><div class="_4g34"><div class="_59tu touchable" id="recipient-tokenizer"><span class="_59tv xm-tokenizer-label">Add:</span><span class="token-container"></span><input class="input _59tw" type="text"></div></div><div><a class="_5jby" rel="ignore" href="#" id="add-button"><i class="img _5jbz"></i>\</a></div></div><div id="friend-picker" style="display: none;"></div><div class="_mMessagesTouchComposer__body acw _5hu7" id="text-area"><textarea class="input _5hu8" placeholder="Write a message..." name="body" rows="5"></textarea></div></div>'),
              l_edit  = $('<div class="edit-group-name">\
                <div class="_4g33 _5hu6">\
                  <div class="_4g34">\
                    <div class="_59tu touchable no-border">\
                      <input class="input _59tw large" type="text" placeholder="Enter a name for this group...">\
                    </div>\
                  </div>\
                <div>\
                <a class="_5jby" rel="ignore" href="#" id="edit-button">\
                  <i class="img _5jcz"></i>\
                </a>\
              </div>\
            </div>\
          </div>')
              token_caller  = {};
  
          if ( !params.i || !params.t || !action ) {
            return;
          }
  
          if ( action == "add" ) {
            $("h1.ui-title",header).html( L.ADD_FRIENDS_TO_CHAT );
            ipMobile.prototype.users.fload(function( a, b, c ) {
              loader.after( l_add ).remove();
  
              var textarea  = $("._mMessagesTouchComposer__body textarea",l_add);
              if ( params.t == "g" ) {
                textarea.parent().remove();
                textarea  = [];
              }
              else {
                textarea.autosize();
              }
  
              var right_btn = right_b.clone().insertAfter( $("h1.ui-title",header) ).removeClass("ui-btn-icon-notext").addClass("ui-btn-right jqm-nav-done").text( L.DONE ).on("click", function(e) {
                if ( unds.isEmpty( token_caller ) ) {
                  return;
                }
                
                if ( !token_caller.has() || token_caller.disable( true ) ) {
                  return;
                }
                if ( textarea.length && !$.trim( textarea.val() ).length ) {
                  return;
                }
  
                var tokens  = token_caller.get();
                if ( params.t == "u" ) {
                  tokens.push( params.i );
                }
                add_loader();
  
                var dataString  = {
                  channel: 'messages',
                  action: 'add',
                  process: 'group',
                  users: tokens
                };
                if ( params.t == "g" ) {
                  dataString.action = 'update',
                  dataString.id     = params.i;
                }
  
                ipqx(ipgo('docServer')+'ipChat/pull.php',"POST", dataString, {
                  onsuccess: function( response ) {
                    if ( response.error ) {
                      ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                      return;
                    }
                    if ( params.t == "u" ) {
                      ipMobile.prototype.chat.send( response.ID, "group", $.trim( textarea.val() ), function() {
                        ipMobile.prototype.history.add( "?i="+response.ID+"&t=g" );
                      }, function() {
                        ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                      });
                      return;
                    }
                    ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                  },
                  onerror: function() {
                    ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                  }
                });
              });
              var left_btn  = right_b.clone().insertBefore( $("h1.ui-title",header) ).removeClass("ui-btn-icon-notext").addClass("ui-btn-left jqm-nav-cancel").text( L.CANCEL ).on("click", function(e) {
                e.preventDefault();
                ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
              });
  
              $("._4g33",l_add).on("click", function(e) {
                e.preventDefault();
                if ( !$(e.target).is("input") ) {
                  $("input",this).trigger("focus");
                }
              });
              $("#add-button",l_add).on("click", function(e) {
                e.preventDefault();
                $("#friend-picker",l_add).toggleClass("fixed-state");
              });
  
              var initTokenizer  = function( input, except ) {
                return ipMobile.prototype.tokenizer( input, {
                  show: function( tokens, len ) {},
                  hide: function( tokens, len ) {},
                  add: function( token, tokens, len ) {},
                  clear: function( tokens, len ) {}
                }, except);
              };
              if ( params.t == "g" ) {
                g_50x4(params.i, function( group, input ) {
                  if ( !group.write ) {
                    ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                    return;
                  }
                  token_caller  = initTokenizer( input, group.users );
                }, function( input ) {
                  token_caller  = initTokenizer( input );
                }, [ $(".input._59tw",l_add) ]);
              }
              else {
                token_caller  = initTokenizer( $(".input._59tw",l_add) );
              }
              $.mobile.activePage.trigger("pagecreate");
            }, [ params.i, params.t, dialog ]);
          }
          else if ( action == "leave" ) {
            ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
              channel: 'messages',
              process: 'group',
              action: 'leave',
              id: params.i
            }, {
              onsuccess: function( response ) {
                if ( response.error ) {
                  ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                  return;
                }
                ipMobile.prototype.history.add( "?p=messages" );
              },
              onerror: function(res,err) {
                ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
              }
            });
          }
          else if ( action == "block" ) {
            ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
              channel: 'users',
              process: 'users',
              action: 'block',
              id: [ params.i ]
            },{
              onsuccess: function( response ) {
                if ( response.error ) {
                  ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                  return;
                }
                delete ipga("users")[params.i];
                ipMobile.prototype.history.add( "?p=messages" );
              },
              onerror: function( res, err ) {
                ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
              }
            });
          }
          else if ( action == "edit" ) {
            g_50x4(params.i, function( group ) {
              if ( !group.write ) {
                ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                return;
              }

              $("h1.ui-title",header).html( L.EDIT_CONV_NAME );
              loader.after( l_edit ).remove();
              var left_btn  = right_b.clone().insertBefore( $("h1.ui-title",header) ).addClass("ui-btn-left ui-icon-arrow-l").text( L.CANCEL ).on("click", function(e) {
                e.preventDefault();
                ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
              });

              var edit_btn  = $("a#edit-button",l_edit),
                  input_txt = $("input",l_edit);

              edit_btn.on("click", function(e) {
                e.preventDefault();
                var name  = $.trim( input_txt.val() );
                if ( !name.length ) {
                  return;
                }
                add_loader();
                ipqx(ipgo('docServer')+'ipChat/pull.php','POST',{
                  channel: 'messages',
                  process: 'group',
                  action: 'naming',
                  id: params.i,
                  name: name
                },{
                  onsuccess: function( res ) {
                    if ( res && !res.err && res.group ) {
                      var groups  = ipga("groups") || ipsa("groups",{}),
                          group   = groups[params.i];
                      if ( group ) {
                        group.name  = res.group.name;
                      }
                    }
                    ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                  },
                  onerror: function( res, err ) {
                    ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
                  }
                });
              });

              $.mobile.activePage.trigger("pagecreate");
              input_txt.focus();
            }, function() {
              ipMobile.prototype.history.add( "?i="+params.i+"&t="+params.t );
            });
          }
        }
        else if ( async_page == "options" ) {
          content.removeClass("no-padding");                    
          if ( params.s && $.inArray( params.s, [ "language", "blocked" ] ) >= 0 ) {
            var back_btn  = right_b.clone().insertAfter( $("h1.ui-title",header) ).addClass("ui-btn-left ui-icon-arrow-l").text( L.OPTIONS ).on("click", function(e) {
              e.preventDefault();
              if ( $(this).data("refresh") ) {
                $(this).removeData("refresh");
                ipMobile.prototype.history.add( "?p=options", "Options", true );
              }
              else {
                ipMobile.prototype.history.add( "?p=options", "Options" );
              }
              
            });

            if ( params.s == "language" ) {
              var list;
              $("h1.ui-title",header).html( L.READING_LANGUAGE );
              loader.after( ( list = $("<ul />",{"class":"languages-list"}) ) ).remove();
              list.listview();
              var languages = ipga("languages"),
                  language  = languages.codes.r;
                  rlanguage = languages.read;

              for( x in rlanguage ) {
                var a1  = list.cn("li").cn("a",{"href":"#"}).data("lang",x).text( rlanguage[x] ).on("click", function(e) {
                  var lang  = $(this).data("lang");
                  e.preventDefault();
                  add_loader();

                  languages.codes.r = lang;
                  $.cookie( "rlang_global", lang, { expires: 365, path: "/" } );
                  ipsa( "languages", languages );
                  ipMobile.prototype.history.add( "?p=options", false, true );
                });
                if ( x == language ) {
                  a1.parent().text( rlanguage[x] );
                }
              }
              list.listview("refresh");
            }
            else {
              var list;
              ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
                channel: 'users',
                process: 'blocked',
                action: 'get'
              },{
                onsuccess: function( response ) {
                  if ( response.error ) {
                    ipMobile.prototype.history.add( "?p=options", "Options" );
                    return;
                  }
                  if ( response.t == "continue" ) {
                    $("h1.ui-title",header).html( L.ERROR );
                    content.html('<div class="acw apl"><div style="text-align:center;"><span class="mfsl fcg">'+L.FEATURE_NOT_AVAIL+'</span></div></div>');
                    return;
                  }
                  $("h1.ui-title",header).html( L.BLOCKED_USERS );
                  loader.after( ( list = $("<ul />",{"class":"blocked-list"}) ) ).remove();
                  list.listview();

                  for( x in response ) {
                    var user  = response[x];
                    var a1  = list.cn("li",{"data-icon":"delete"}),
                        b1  = a1.cn("a",{"href":"#","class":"ul-li-link-alt"},user.NM).data("id",user.ID),
                        c1  = b1.cn("i",{"class":"ui-li-icon ui-li-thumb"},false,'prepend').css({
                          'background-image': 'url("'+user.AV+'")'
                        });
                    b1.on("click", function(e) {
                      e.preventDefault();
                      var id  = $(this).data("id");
                      var dat = {
                        channel: "users",
                        process: "users",
                        action: "unblock",
                        id: [ id ]
                      };
                      $(this).parents("li:first").remove();
                      if ( !$("li",list).length ) {
                        content.html('<div class="acw apl"><div style="text-align:center;"><span class="mfsl fcg">'+L.FEATURE_NOT_AVAIL+'</span></div></div>');
                      }
                      back_btn.data("refresh", true);
                      $.post(ipgo('docServer')+'ipChat/pull.php',dat,function( response ) {
                        if ( !response.error ) {
                          for( i in response ) {
                            ipga("users")[i]  = response;
                          }
                        }
                      },"json");
                    });
                  }
                  list.listview("refresh");

                  console.log( response );
                },
                onerror: function( response, error ) {
                  ipMobile.prototype.history.add( "?p=options", "Options" );
                }
              });
            }

            $.mobile.activePage.trigger("pagecreate");
          }
          else {
            var back_btn  = right_b.clone().insertAfter( $("h1.ui-title",header) ).addClass("ui-btn-left ui-icon-comment").text( L.MESSAGES ).on("click", function(e) {
              e.preventDefault();
              ipMobile.prototype.history.add( "?p=messages", "Messages" );
            });
            var list;
            $("h1.ui-title",header).html( L.OPTIONS );
            loader.after( ( list = $("<ul />",{"class":"options-list"}) ) ).remove();
            list.listview();
            var options = [
              [ L.READING_LANGUAGE, function(e){e.preventDefault();ipMobile.prototype.history.add("?p=options&s=language");} ],
              [ L.BLOCKED_USERS, function(e){e.preventDefault();ipMobile.prototype.history.add("?p=options&s=blocked");} ]
            ];
            for( var i = 0; i < options.length; i++ ) {
              list.cn("li").cn("a",{"href":"#"}).text( options[i][0] ).on("click",options[i][1]);
            }
            list.listview("refresh");
            $.mobile.activePage.trigger("pagecreate");
          }
        }
      },
      base: function() {
        var page  = $("#messenger");

        var messages  = ipMobile.prototype.data;
        var par = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
            idx = par.i,
            idn = ( par.t == "u" ) ? "user" : "group";
        if ( ipMobilePing.prototype.parse.temp[idn] && ipMobilePing.prototype.parse.temp[idn][idx] ) {
          delete ipMobilePing.prototype.parse.temp[idn][idx];
        }
        if ( unds.isEmpty( ipMobile.prototype.data ) ) {
          ipMobile.prototype.history.add( "?p=compose&i="+idx );
          return;
        }

        var messages  = messages.messages,
            seen      = messages.seen,
            id4 = $("#m-messaging-error-message",page);
            threads   = $("#messageGroup");
        if ( !threads.length ) {
          return;
        }
        if ( count( messages ) < 20 ) {
          $("#see_older_messages",page).remove();
        }
        else {
          $("#see_older_messages",page).on("click", function(e) {
            e.preventDefault();
            ipMobile.prototype.chat.render.older();
          });
        }

        var selector  = $("select.jqm-chat-actions");
        if ( idn == "user" ) {
          selector.cn("option",{"value":"add"}).html( L.ADD_FRIENDS_TO_CHAT+"&hellip;" ),
          selector.cn("option",{"value":"block"}).html( L.BLOCK_USER );
        }
        else {
          selector.cn("option",{"value":"add"}).html( L.ADD_FRIENDS_TO_CHAT+"&hellip;" ),
          selector.cn("option",{"value":"edit"}).html( L.EDIT_CONV_NAME ),
          selector.cn("option",{"value":"leave"}).html( L.LEAVE_CONV );
        }
        selector.show(0).selectmenu({
          inline: true,
          iconpos: "notext"
        }).selectmenu("enable").on("change", function(e) {
          if ( !idx || !idn ) {
            return false;
          }
          var value = $.trim( $(this).val() );
          ipMobile.prototype.history.add( "?i="+par.i+"&t="+par.t+"&a="+value );
        });
        selector.parents(".ui-select:first").addClass("ui-btn-right")

        $("._5cni.ui-link",page).on("click", function(e) {
          e.preventDefault();
          $("._5ecn._5d_h",page).trigger("click");
        });
        $("._5ecn._5d_h",page).on("click", function(e) {
          e.preventDefault();
          $(this).toggleClass("_5ar-");
          if ( !$(this).hasClass("_5ar-") ) {
            $("._5as0",page).hide(0);
            return;
          }
          if ( $(this).hasClass("emoji-loaded") ) {
            $("._5as0",page).show(0);
            $("body").scrollTop( $("body")[0].scrollHeight );
            return;
          }
          var container = $("._5as0",page).show(0),
              header    = $("._5as5",container).empty();
              footer    = $("._5ef_",container).empty(),
              emoticons = ipga("emoticon");

          for( loop1 in emoticons ) {
            var current   = loop1;
            var emoticon  = emoticons[loop1];
            var a1  = header.cn("a",{"class":"_5arz _5cni emoticon_"+loop1,"href":"#","title":emoticon.name}).data("emoticon", loop1),
                b1  = a1.cn("i",{"class":"unselected"}).css("background-image",'url("'+emoticon.icon.u+'")'),
                c1  = a1.cn("i",{"class":"selected"}).css("background-image",'url("'+emoticon.icon.s+'")');

            a1.on("click", function(e) {
              e.preventDefault();
              if ( $(this).hasClass("_5ar-") ) {
                return;
              }
              $("._5arz",header).removeClass("_5ar-").filter( $(this) ).addClass("_5ar-");
              var item  = $(this).data("emoticon");
              footer.html('<div class="mCenteredLoaderBase"><div class="mCenteredLoader mCenteredLoaderVertical fcg"><div class="_2so _2sq _2ss _50cg" style="vertical-align: middle;"></div> <span>Loading...</span></div></div>');
              ipDockPanel.prototype.process.render.nub.emoticons.get( item, function( emoticon ) {
                footer.empty();
                if ( emoticon.emoji !== true ) {
                  var tb  = ipDockPanel.prototype.process.render.nub.emoticons.table( emoticon.data );
                  var a2  = footer.cn("table",{"class":"emoticonsTable"}),
                      b2  = a2.cn("tbody");
                  for( tr in tb ) {
                    var c2  = b2.cn("tr");
                    for( td in tb[tr] ) {
                      var d2  = c2.cn("td",{"class":"panelCell"}).data("emoticon",tb[tr][td]),
                          e2  = d2.cn("a",{"class":"emoticon "+item+" "+item+"_"+tb[tr][td].name});
                      d2.on("click",function(e) {
                        e.preventDefault();
                        var emd = $(this).data("emoticon");
                        $("#composerInput",page).insertAtCaret( emd.data[0], true ).trigger("keyup");
                        $("._5ecn._5d_h",page).trigger("click");
                      });
                    }
                  }
                  
                  return;
                }
                for( var loop2 = 0; loop2 < emoticon.data.length; loop2++ ) {
                  var a2  = footer.cn("div",{"class":"_5asg"}).data("emoji",'[emoji]'+item+'.'+loop2+'[/emoji]'),
                      b2  = a2.cn("img",{"src":emoticon.data[loop2]});
                  a2.on("click", function(e) {
                    e.preventDefault();
                    var emoji = $(this).data("emoji");
                    ipMobile.prototype.chat.send( idx, idn, emoji );
                    $("._5ecn._5d_h",page).trigger("click");
                  });
                }
              });
              $.mobile.activePage.trigger("pagecreate");
            });
          }

          $("._5arz:first",header).trigger("click");
          $(this).addClass("emoji-loaded");
          $.mobile.activePage.trigger("pagecreate");
          $("body").scrollTop( $("body")[0].scrollHeight );
        });

        var file_uploads  = parseInt( ipga("settings").file_uploads );

        if ( !file_uploads || !ipgo("canUploadFiles") ) {
          $("._4g33 ._5eco:eq(1)").remove();
        }
        else {
          $("input[type=file]._51sb",page).on("change", function(e) {
            return ipMobile.prototype.files.upload( e.target.files, $(this), $("._5aqv ._5cqb",page) );
          });
        }
        $("textarea._52t1",page).autosize();
        $("a.resend-btn",page).on("click", function(e) {
          e.preventDefault();
          var message = id4.data("message");
          if ( !message ) {
            id4.hide(0);
            return;
          }
          ipMobile.prototype.chat.send( idx, idn, message );
        });
        $("a.send-btn",page).on("click", function(e) {
          e.preventDefault();
          var textarea  = $("textarea._52t1",page);
          var message   = $.trim( textarea.val() );
          var attachments = ipMobile.prototype.files.list;
          if ( ipMobile.prototype.files.ajax !== false ) {
            return;
          }
          if ( unds.isEmpty( attachments ) && !message.length ) {
            return;
          }
          ipMobile.prototype.chat.send( idx, idn, message );
        });
        if ( idn == "user" ) {
          g_50x5(idx, function( user, that, page, messages ) {
            $(".ui-content",page).fadeIn();
            $(".ui-header .ui-title, .acw.apm .mfsm:first, #typingIndicator .c span",page).text( user.NM );
            $(".presence_icon:first",page).removeClass("online_icon offline_icon busy_icon")
            $("#typingIndicator, #m-messaging-error-message",page).hide(0);
            $(".fbLastActiveTimestamp",page).addClass("fbLastActiveTimestamp"+user.ID);
            if ( ( user.SA || user.ST ) == "online" ) {
              $(".presence_icon:first",page).addClass("online_icon");
            }
            else {
              if (  user.LS > 0 ) {
                $(".fbLastActiveTimestamp",page).attr("timestamp",user.LS).text( timeDifference( user.LS ) ).parent().show(0);
              }
            }
            for( x in messages ) {
              that.item( messages[x] );
            }
            $.mobile.activePage.trigger("pagecreate");
          }, function() {
            ipMobile.prototype.history.add( "?p=messages" );
          }, [ this, page, messages ] )
        }
        else {
          g_50x4(idx, function( group, that, page, messages ) {
            $(".ui-content",page).fadeIn();
            $(".ui-header .ui-title, .acw.apm .mfsm:first",page).text( group.name );
            $(".presence_icon:first, .fbLastActiveTimestamp, #typingIndicator",page).remove(0);
            if ( !group.write ) {
              $("#message-reply-composer, .jqm-chat-actions",page).remove();
            }
            for( x in messages ) {
              that.item( messages[x] );
            }
            $.mobile.activePage.trigger("pagecreate");
          }, function() {
            ipMobile.prototype.history.add( "?p=messages" );
          }, [ this, page, messages ] );
        }
        $("body").scrollTop( $("body").get(0).scrollHeight );
      },
      older: function() {
        var messages  = ipMobile.prototype.data;
        if ( unds.isEmpty( ipMobile.prototype.data ) ) {
          return;
        }
        var page  = $("#messenger"),
            seeol = $("#see_older_messages",page),
            thrb  = $(".async_throbber",seeol).removeClass("async_saving_visible");
        if ( seeol.data("loading") == true ) {
          return;
        }
        var par = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
            idx = par.i,
            idn = ( par.t == "u" ) ? "user" : "group";
        if ( !idx || !idn ) {
          thrb.addClass("async_saving_visible");
          return;
        }
        seeol.data("loading",true);
        var body  = $(".message-body:first",page);
        if ( !body.length || !body.data("message") ) {
          thrb.addClass("async_saving_visible");
          return;
        }
        var topPos  = body;
        body  = body.data("message");

        if ( older_items = gspl_50dw( idx, idn, body.ID ) ) {
          for( x in older_items ) {
            ipMobile.prototype.chat.render.item( older_items[x] );
          }
          if ( count( older_items ) < 20 ) {
            seeol.remove();
          }
          $.mobile.silentScroll( topPos.offset().top );
          $.mobile.activePage.trigger("pagecreate");
          return;
        }

        var xhr = new ipXhr();
        xhr.open( ipgo('docServer')+'ipChat/pull.php' );
        xhr.params({
          channel: 'messages',
          process: 'message',
          action: 'load',
          id: idx,
          type: idn,
          older: body.ID
        });
        xhr.callback({
          onsuccess: function( response ) {
            if ( response.error ) {
              seeol.remove();
              return;
            }
            add_50dw( idx, idn, response.messages );
            for( x in response.messages ) {
              ipMobile.prototype.chat.render.item( response.messages[x] );
            }
            if ( count( response.messages ) < 20 ) {
              seeol.remove();
            }
            $.mobile.silentScroll( topPos.offset().top );
            $.mobile.activePage.trigger("pagecreate");
          },
          onloadend: function() {
            thrb.addClass("async_saving_visible");
            seeol.data("loading",false);
            $.mobile.activePage.trigger("pagecreate");
          }
        });
        xhr.send();
      },
      item: function( message ) {
        var threads = $("#messageGroup"),
            sent    = parseInt( message.sent_date ),
            messageID = parseInt( message.ID ),
            bubbles = $(".message-body",threads);

        if ( $(".message-body-"+messageID).length ) {
          return $(".message-body-"+messageID);
        }

        if ( bubbles.length ) {
          var last_timestamp  = parseInt( bubbles.filter(":last").attr("timestamp") );
          if ( last_timestamp == sent ) {
            this.position.insertAfter( message, bubbles.filter(":last"), threads );
          }
          else if ( last_timestamp < sent ) {
            var lastMessage = bubbles.filter(function() {
              return parseInt( $(this).attr("timestamp") ) < sent;
            }).filter(":last");
            this.position.insertAfter( message, lastMessage, threads );
          }
          else {
            var lastMessage = bubbles.filter(function() {
              return parseInt( $(this).attr("timestamp") ) > sent;
            }).filter(":first");
            this.position.insertBefore( message, lastMessage, threads );
          }
        }
        else {
          this.position.append( message, threads );
        }
      },
      position: {
        append: function( message, list ) {
          if ( !list.length ) {
            return;
          }
          var thread  = this.bubble( message );
          thread.appendTo( list );
        },
        prepend: function( message, list ) {
          if ( !list.length ) {
            return;
          }
          var thread  = this.bubble( message );
          thread.insertAfter( $("#see_older_messages",list) );
        },
        insertBefore: function( message, list, threads ) {
          if ( !list.length ) {
            this.prepend( message, threads );
            return;
          }
          var lastData    = ( list && list.length ) ? list.data("message") : false;
          var lastTime    = ( lastData && !list.hasClass("_kson") ) ? ( ( lastData.sent_date - message.sent_date ) <= 100 ) : false;
          var isLastUser  = ( lastTime ) ? ( parseInt( lastData.sent_from ) === parseInt( message.sent_from ) ) : false;

          var body  = this._kso( message );

          if ( lastTime && isLastUser && !message.is_notice && !message.has_attachment ) {
            body.insertBefore( list );
          }
          else {
            var thread  = this.bubble( message );
            thread.insertBefore( list.parents(".voice:first") );
          }
        },
        insertAfter: function( message, list, threads ) {
          if ( !list.length ) {
            this.append( message, threads );
            return;
          }
          var lastData    = ( list && list.length ) ? list.data("message") : false;
          var lastTime    = ( lastData && !list.hasClass("_kson") ) ? ( ( message.sent_date - lastData.sent_date ) <= 100 ) : false;
          var isLastUser  = ( lastTime ) ? ( parseInt( lastData.sent_from ) === parseInt( message.sent_from ) ) : false;

          var body  = this._kso( message );

          if ( lastTime && isLastUser && !message.is_notice && !message.has_attachment ) {
            body.insertAfter( list );
          }
          else {
            this.append( message, threads );
          }
        },
        bubble: function( message ) {
          var attach  = message.attachments;
          var a1  = $().cn("div",{"class":"voice acw abt"}),
              b1  = a1.cn("div",{"class":"ib"},'<a class="darkTouch" href="#"><span><img alt="Loading..." class="img profpic" height="43" width="43"></span></a>'),
              c1  = b1.cn("div",{"class":"c"}),
              d1  = c1.cn("div",{"class":"msg"}),
              e1  = d1.cn("a",{"class":"actor-link ui-link","href":"#"}),
              f1  = e1.cn("strong",{"class":"actor"}),
              g1  = this._kso( message ).appendTo( d1 ),
              h1  = d1.cn("div",{"class":"actions mfss fcg"},'<abbr timestamp=""></abbr>');

          if ( parseInt( message.is_notice ) === 1 ) {
            if ( parseInt( message.has_attachment ) === 0 || ( parseInt( message.has_attachment ) === 1 && unds.isEmpty( attach ) ) ) {
              $("a",b1).remove();
              h1.remove();
              g1.html( $('<span />',{'class':'fcg'}).html( message.message ) );
              var j1  = b1.cn("div",{"class":"machineIcon l"},'<i></i>','prepend');
              if ( message.notice_section === "left" ) {
                $("i",j1).addClass("icon-signout");
              }
              else if ( message.notice_section === "added" ) {
                $("i",j1).addClass("icon-user");
              }
              else if ( message.notice_section === "naming" ) {
                $("i",j1).addClass("icon-pencil");
              }
            }
            g1.addClass("_kson");
          }

          if ( !unds.isEmpty( attach ) ) {
            var images  = ipos( attach, "mimegroup", "image" ),
                streams = ipos( attach, "mimegroup", "stream" );

            if ( !unds.isEmpty( streams ) ) {
              attach  = unds.omit( attach, unds.keys( streams ) );
              for( x in streams ) {
                var st  = streams[x],
                    a2  = g1.cn("div",{"class":"ib attachment"});
                if ( st.thumbnail ) {
                  var b2  = a2.cn("a",{"href":st.target,"target":"_blank","rel":"nofollow","class":"l ed"}).on("click"),
                      c2  = b2.cn("img",{"src":st.thumbnail,"alt":"External Image","class":"img"}).width( 154 );
                }
                var d2  = a2.cn("div",{"class":"desc c mfss"},'<br /><span class="subtitle fcg"><div class="atb">'+( st.summary || st.subtitle )+'</div></span>'),
                    e2  = d2.cn("a",{"href":st.target,"target":"_blank","rel":"nofollow","class":"fcg title"},st.title,'prepend');
                
              }
            }
            if ( !unds.isEmpty( images ) ) {
              attach  = unds.omit( attach, unds.keys( images ) );
              var j1  = g1.cn("div",{"class":"_673"},false,'insertAfter');
              var totalWidth  = ( $(window).width() - 200 );
              var imagesLen   = Math.min( ( ( totalWidth > 400 ) ? 4 : 3 ), count( images ) );
              var availWidth  = Math.min( Math.floor( totalWidth / imagesLen ), 600 );
              if ( totalWidth > 400 && imagesLen == 1 ) {
                var availWidth  = Math.min( totalWidth - 100, availWidth );
              }
              availWidth  = Math.max( 226, availWidth );

              for( x in images ) {
                var img = images[x];
                var k1  = j1.cn("a",{"href":ipgo("docServer")+img.target,"target":"_blank","rel":"dialog","class":"image-slider","title":img.title}).data("attachment",img).after( " " ),
                    l1  = k1.cn("i",{"class":"img _675"}).css({
                      'background-image': 'url("'+ipgo("docServer")+img.thumbnail+'")'
                    }).width( availWidth ).height( availWidth );
              }
            }
            if ( !unds.isEmpty( attach ) ) {
              for( x in attach ) {
                var at  = attach[x];
                var m1  = g1.cn("div",{"class":"ib attachment"},'<i class="l img '+"img-ico ico-"+at.extension+'"></i>'),
                    n1  = m1.cn("div",{"class":"desc c mfss"}),
                    o1  = n1.cn("a",{"href":ipgo("docServer")+at.target,"target":"_blank","rel":"nofollow","class":"ed fcg title"},at.title);
              }
            }
            g1.addClass("_kson");
          }

          g_50x5(message.sent_from, function( user, item ) {
            $("img.profpic",item).attr("src",user.AV);
            $("strong.actor",item).text( user.NM );
          }, false, [ a1 ]);
          $("abbr",h1).attr("timestamp",message.sent_date).text( timeDifference( message.sent_date ) );

          if ( pingStartedMob !== false && parseInt( message.sent_from ) != parseInt( ipga("user").ID ) ) {
            a1.addClass("chatHighlight");
          }
          return a1;
        },
        _kso: function( message ) {
          var a1  = $('<div />').attr({
            timestamp: message.sent_date,
            message: message.ID
          }).addClass("message-body message-body-"+message.ID).html( message.message ).data({
            message: message
          });
          if ( pingStartedMob !== false && parseInt( message.sent_from ) != parseInt( ipga("user").ID ) ) {
            a1.addClass("chatHighlight");
          }
          return a1;
        }
      }
    }
  },
  page: {
    that: this,
    page: $("#messages"),
    user: $("#buddylist"),
    call: false,
    init: function( call ) {
      this.call = call;
      this.events();
    },
    chat: {
      init: function( userID ) {
        if ( !userID || isNaN( userID ) ) {
          return false;
        }
        $.mobile.changePage( $("#messages") );
      },
      start: {
        history: function() {
          var content = $("#messages .ui-content:first");
          if ( $("._4g33:first",content).length ) {
            return;
          }
          var ldr = $('<div class="mCenteredLoader mCenteredLoaderVertical fcg" id="loading_indicator"><div class="_2so _2sq _2ss img _50cg"></div> <span>Loading...</span></div>').appendTo( content );
              a1  = content.cn("div",{"class":"ui-grid-a acw ui-buttons-grid"}),
              b1  = a1.cn("a",{"class":"ui-block-a ui-block","href":"#","rel":"dialog","role":"button"},L.NEW_MESSAGE),
              c1  = a1.cn("a",{"class":"ui-block-b ui-block","href":"#","rel":"dialog","role":"button"},L.OPTIONS);

          /*var a1  = content.cn("div",{"class":"acw"}),
              b1  = a1.cn("div",{"class":"_4g33 _55so _55sp _5i-i"}),
              c1  = b1.cn("div",{"class":"_4g34 _5ca8 _5jjz _55st"}),
              d1  = c1.cn("a",{"class":"touchable _56bz _5c9u _5caa","href":"#","rel":"dialog","role":"button"},'<span class="_55sr">'+L.NEW_MESSAGE+'</span>'),
              e1  = b1.cn("div",{"class":"_4g34 _5ca8 _5jjz _55st"}),
              f1  = e1.cn("a",{"class":"touchable _56bz _5c9u _5caa","href":"#","rel":"dialog","role":"button"},'<span class="_55sr">'+L.OPTIONS+'</span>');*/

          var a2  = content.cn("div",{"id":"threadlist_rows","class":"visible-when"},'<div class="vsb acw apl"><div style="text-align:center;"><span class="mfsl fcg">'+L.NO_MESSAGES+'</span></div></div>').hide(0),
              b2  = a2.cn("div",{"class":"hdn threadlist"}),
              c2  = a2.cn("div",{"class":"hdn item async_elem acw","id":"see_older_threads"}),
              d2  = c2.cn("a",{"class":"touchable primary"}),
              e2  = d2.cn("div",{"class":"primarywrap"}),
              f2  = e2.cn("div",{"class":"content"}),
              g2  = f2.cn("div",{"class":"title mfsm fcl"},'<strong>'+L.SEE_OLDER+'&hellip; <div class="_2so _2sq _2ss img _50cg async_throbber async_saving_visible"></div></strong>');

          b1.on("click", function(e) {
            e.preventDefault();
            ipMobile.prototype.history.add( "?p=compose", "New Message" );
          });
          c1.on("click", function(e) {
            e.preventDefault();
            ipMobile.prototype.history.add( "?p=options", "Options" );
          });
          c2.on("click", function(e) {
            e.preventDefault();
            if ( $(this).data("loading") == true ) {
              return;
            }
            var throbber  = $(".async_throbber",this).removeClass("async_saving_visible"),
                element   = $(this).data("loading", true),
                older     = parseInt( $(this).data("older_id") );
            if ( !older || isNaN( older ) ) {
              return;
            }
            ipqx( ipgo('docServer')+'ipChat/pull.php', "POST", {
              channel: 'messages',
              process: 'history',
              older: older
            }, {
              onsuccess: function( response ) {
                throbber.addClass("async_saving_visible");
                element.data("loading", false);
                if ( response.error ) {
                  element.remove();
                  return;
                }
                element.data("older_id",response.older);
                ipMobile.prototype.chat.render.history( response.messages );
              },
              onerror: function() {
                throbber.addClass("async_saving_visible");
                element.data("loading", false);
              }
            });
          });

          ipqx( ipgo('docServer')+'ipChat/pull.php', "POST", {
            channel: 'messages',
            process: 'history',
            older: false
          }, {
            onsuccess: function( response ) {
              ldr.hide(0);
              if ( response.error ) {
                a2.show(0);
                $(".vsb .mfsl:first").text( response.message );
                return;
              }
              a2.show(0).removeClass("visible-when");
              c2.data("older_id",response.older);
              ipMobile.prototype.chat.render.history( response.messages );
            },
            onerror: function() {
              ldr.hide(0);
              a2.show(0);
            }
          });
        },
        fresh: function( page ) {
          var content = $(".ui-content:first",page),
              token_caller  = {};
          if ( !content.length ) {
            return;
          }
          var file_uploads  = parseInt( ipga("settings").file_uploads );
          if ( !file_uploads || !ipgo("canUploadFiles") ) {
            $("._51s9._5hu9").remove();
          }
          else {
            $("input[type=file]._51sb").on("change",function(e) {
              return ipMobile.prototype.files.upload( e.target.files, $(this), $("._mMessagesTouchComposer__body ._5cqb:first",content), function() {
                var tokens  = $(".token-container",content);
                if ( $("span._59tt",tokens).length ) {
                  $(".jqm-nav-submit").removeClass("ui-disabled");
                }
                else {
                  $(".jqm-nav-submit").addClass("ui-disabled");
                }
              }, function( len ) {
                var tokens  = $(".token-container",content);
                if ( $("span._59tt",tokens).length && len > 0 ) {
                  $(".jqm-nav-submit").removeClass("ui-disabled");
                }
                else {
                  $(".jqm-nav-submit").addClass("ui-disabled");
                }
              });
            });
          }
          $(".jqm-nav-submit").on("click", function(e) {
            e.preventDefault();
            var tokens    = token_caller,
                textarea  = $("._mMessagesTouchComposer__body textarea",content);
            if ( tokens.has() && ( $.trim( textarea.val() ).length || !unds.isEmpty( ipMobile.prototype.files.list ) ) ) {
              var users = tokens.get(),
                  text  = $.trim( textarea.val() );
              $("._mMessagesTouchComposer__body ._5cqb:first ._51s2",content).not(":last").remove();
              $(this).addClass("ui-disabled");
              $(".input._59tw",content).val('').prop("disabled", true);
              tokens.empty();
              textarea.val('').trigger("autosize.resize").prop("disabled", true);
              if ( users.length > 1 ) {
                var xhr = new ipXhr();
                xhr.open( ipgo('docServer')+'ipChat/pull.php' );
                xhr.params({channel:'messages',process:'group',action:'add',users:users});
                xhr.callback({
                  onsuccess: function( response ) {
                    if ( response.error ) {
                      return;
                    }
                    ipMobile.prototype.chat.send(response.ID, "group", text, function() {
                      ipMobile.prototype.history.add( "?i="+response.ID+"&t=g" );
                    }, function() {
                      ipMobile.prototype.history.add( "?p=messages" );
                    });
                  },
                  onerror: function( response ) {
                    
                  }
                });
                xhr.send();
              }
              else {
                ipMobile.prototype.chat.send(users[0], "user", text, function() {
                  ipMobile.prototype.history.add( "?i="+users[0]+"&t=u" );
                }, function() {
                  ipMobile.prototype.history.add( "?p=messages" );
                });
              }
            }
          });
          $("#recipient-tokenizer",content).on("click", function(e) {
            if ( !$(e.target).is("input") ) {
              $("input",this).focus();
            }
          });
          $("._mMessagesTouchComposer__body textarea",content).autosize().on("keydown keyup focus blur", function(e) {
            if ( $(".token-container span._59tt",content).length && ( $.trim( $(this).val() ).length || !unds.isEmpty( ipMobile.prototype.files.list ) ) ) {
              $(".jqm-nav-submit").removeClass("ui-disabled");
            }
            else {
              $(".jqm-nav-submit").addClass("ui-disabled");
            }
          });
          ipMobile.prototype.users.fload(function( a ) {
            var par = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
                idx = par.i;
            if ( idx ) {
              g_50x5(idx, function(l) {
                $('<span class="_59tt touchable" id="_59tt_'+l.ID+'">'+l.NM+'</span>').append( '<input type="hidden" name="tokens[]" value="'+l.ID+'" /><input type="hidden" value="'+l.NM+'" />' ).appendTo( $(".token-container",content) );
              });
            }
            $("a#add-button",content).on("click", function(e) {
              e.preventDefault();
              if ( $(".input._59tw",content).is(":disabled") ) {
                return false;
              }
              var a = $("#friend-picker",content),
                  b = $("._mMessagesTouchComposer__body",content),
                  c = $("#recipient-tokenizer input._59tw",content);
              if ( !a.hasClass("fixed-state") ) {
                a.addClass("fixed-state").show(0);
                b.hide(0);
                c.focus();
                return;
              }
              a.removeClass("fixed-state").hide(0);
              b.show(0);
            });

            var en_dis_area = function( len ) {
              if ( len && ( $.trim( $("._mMessagesTouchComposer__body textarea").val() ).length || !unds.isEmpty( ipMobile.prototype.files.list ) ) ) {
                $(".jqm-nav-submit").removeClass("ui-disabled");
              }
              else {
                $(".jqm-nav-submit").addClass("ui-disabled");
              }
            };
            token_caller  = ipMobile.prototype.tokenizer( $("#recipient-tokenizer input._59tw",content), {
              show: function( tokens, len ) {
                en_dis_area( len );
              },
              hide: function( tokens, len ) {
                en_dis_area( len );
              },
              add: function( token, tokens, len ) {
                en_dis_area( len );
              },
              clear: function( tokens, len ) {
                en_dis_area( len );
              }
            } );
            $("#recipient-tokenizer",content).on("click", function(e) {
              /*if ( ( !$(e.target).is("input") && !$(e.target).hasClass("._51v0") ) && !$("#friend-picker",content).hasClass("fixed-state") ) {
                $("#friend-picker",content).hide(0);
                $("._mMessagesTouchComposer__body",content).show(0);
              }*/
            });
          }, false );
        }
      }
    },
    hash: function() {
      var params  = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() );

      if ( !unds.isEmpty( params ) ) {
        if ( params.p ) {
          if ( params.p == "messages" ) {
            ipMobile.prototype.load.pages.messages();
          }
          else if ( params.p == "compose" ) {
            ipMobile.prototype.load.pages.compose();
          }
          else if ( params.p == "options" ) {
            ipMobile.prototype.load.pages.options();
          }
          else {
            ipMobile.prototype.load.pages.users();
          }
        }
        else if ( params.i && params.t ) {
          if ( params.a && $.inArray( $.trim( params.a ), [ "add", "edit", "block", "leave" ] ) != "-1" ) {
            ipMobile.prototype.load.pages.actions( params.a );
            return;
          }
          ipMobile.prototype.load.pages.chat();
          
        }
        else {
          ipMobile.prototype.load.pages.users();
        }
      }
      else {
        ipMobile.prototype.load.pages.users();
      }
    },
    events: function() {
      var that  = this,
          page  = this.page,
          user  = this.user;

      $(window).on("resize", function(e) {
        $(".ui-content:visible").css({
          'min-height': $(window).height() - $(".ui-header:visible").height()
        });
      });
      
      $(document).bind("pageshow", function() {
        $(".ui-content:visible").css({
          'min-height': $(window).height() - $(".ui-header:visible").height()
        });
      }).bind("pageloadfailed", function(a,b) {
        var page  = $("#tempLoading");
        if ( !page.is(":visible") ) {
          return;
        }
        $(".ui-header .ui-title",page).text( L.ERROR );
        $(".ui-content",page).html( '<div id="mErrorView"><div class="container"><div class="image"></div><div class="message">'+L.NO_INTERNET+'</div><a class="link">'+L.TRY_AGAIN+'</a></div></div>' ).find("a").on("click", function(e) {
          e.preventDefault();
          $(".ui-header .ui-title",page).html( L.LOADING+"&hellip;" );
          $(".ui-content",page).html( '<div class="mCenteredLoader mCenteredLoaderVertical fcg"><div class="_2so _2sq _2ss img _50cg"></div> <span>'+L.LOADING+'&hellip;</span></div>');
          $.mobile.changePage( b.options.target, b.options );
        });
      }).on("click", "a.jqm-users-nav", function(e) {
        e.preventDefault();
        ipMobile.prototype.history.add();
      }).on("click", "a.jqm-chat-nav", function(e) {
        e.preventDefault();
        ipMobile.prototype.history.add( "?p=messages", "Messages" );
      }).on("click", ".jqm-status-nav, .offline_show a", function(e) {
        e.preventDefault();
        var status  = ipMobile.prototype.users.status;
        return ( status.base ) ? status.turn.off( true ) : status.turn.on( true );
      });
      /** Page Init **/
      $(document).on("pageinit", "#compose", function() {
        that.chat.start.fresh( $(this) );
      }).on("pageshow", "#buddylist", function() {
        that.that.users.start( $(this) );
      }).on("pageshow", "#messages", function() {
        that.chat.start.history();
      }).on("pageshow", "#messenger", function() {
        that.that.chat.render.base();
      }).on("pageshow", "#dialog-dynamic", function() {
        that.that.chat.render.actions( $(this) );
      }).on("pageshow", "#ipAuthTriggers", function( a, b ) {
        try {
          document.title = "Authentification Required";
        }
        catch( e ) {}
        var page  = $(this),
            login = function() {
              var box = $(".ui-content",page);
              $(".ui-header h1.ui-title",page).text( "Login" );
              ipAuth.prototype.login( box );
            },
            signup  = function() {
              var box = $(".ui-content",page);
              $(".ui-header h1.ui-title",page).text( "Signup" );
              ipAuth.prototype.signup( box );
            },
            reset_pass = function() {
              var box = $(".ui-content",page);
              $(".ui-header h1.ui-title",page).text( "Reset Password" );
              ipAuth.prototype.reset_pass( box );
            },
            both  = function() {
              var box = $(".ui-content",page);
              var button  = $("input[type=submit]",box);
              var backup  = button;
              var input   = $("input[type=text], input[type=password], input[type=email]",box);

              button  = $("<a />",{"class":"link-submit-btn","data-role":"button"}).attr("data-role","button").insertAfter( button );
              if ( backup.attr("name") == "login" ) {
                button.text( L.LOGIN );
              }
              else if ( backup.attr("name") == "signup" ) {
                button.text( L.SIGN_UP );
              }
              else if ( backup.attr("name") == "reset_pass" ) {
                button.text( L.RESET_PASSWORD );
              }
              backup.remove();

              //input.parent().wrapAll( $("<div />",{"class":"grouped inset"}) );
              input.textinput();
              input.parent().addClass("mobile-login-field area textInputArea acw");
              input.parent().filter(":first").addClass("first");
              input.parent().filter(":last").addClass("last");

              button.removeClass("ibtn ibtni").parent().parent().addClass("button_area aclb apl");

              $("input[name=source]").val( "mobile" );
              $("form a",box).not(".link-submit-btn").wrapAll( $("<span />",{"class":"mfss fcg"}) ).parent().wrapAll( $("<div />",{"class":"other-links aclb apl"}) );
              $('<span role="separator" aria-hidden="true"> &nbsp; </span>').insertAfter( $("form a:eq(1)",box) );
              $(".ialert",box).ialert().prependTo( $("form",box) );
              $("form",box).on("submit",ipAuth.prototype.submit);
              button.on("click", function(e) {
                e.preventDefault();
                $("form",box).trigger("submit");
              });

              var signup_buttons  = $(".other-links a[name=signup]",box).attr("data-role","button").button();
              var login_buttons  = $(".other-links a[name=login]",box).attr("data-role","button").button();
              var reset_buttons = $(".other-links a[name=reset_pass]",box).attr("data-role","button").button();

              $(".other-links a").removeClass("ui-button").addClass("ui-btn").wrapAll( $("<div />",{"class":"ui-grid-a"}) );
              $(".other-links a").eq(0).wrap( $("<div />",{"class":"ui-block-a"}) );
              $(".other-links a").eq(1).wrap( $("<div />",{"class":"ui-block-b"}) );
              $(".other-links").find("span[role=separator]").remove();
              $(".other-links ui-grid-a:first",box).attr({
                "data-type": "horizontal",
                "data-role": "controlgroup",
                "data-mini": "false"
              }).controlgroup();

              if ( signup_buttons.length ) {
                signup_buttons.off("click").on("click",function(e) {
                  e.preventDefault();
                  signup();
                  both();
                });
              }
              if ( login_buttons.length ) {
                login_buttons.off("click").on("click",function(e) {
                  e.preventDefault();
                  login();
                  both();
                });
              }
              if ( reset_buttons.length ) {
                reset_buttons.off("click").on("click",function(e) {
                  e.preventDefault();
                  reset_pass();
                  both();
                });
              }

              button.button().removeClass("ui-button").addClass("ui-btn");
              $.mobile.activePage.trigger("pagecreate");
            };
        login();
        both();
      });
      //that.hash();
      iplj('mobile,themes/mobile/base,buttons','',function() {
        call_user_func_array( that.call );
      },function() {},"css",true);
    }
  },
  history: {
    add: function( a, b, c ) {
      a = a || '';
      b = b || 'Users';
      var d = ipgo("mobileURI")+a;
      if ( c ) {
        window.location.href  = d;
        return;
      }
      if ( this.has.push() ) {
        history.pushState( {}, b, d );
        $(window).trigger("popstate");
      }
      else {
        window.location.hash  = a;
      }
    },
    event: function( func ) {
      if ( this.has.push() ) {
        $(window).on("popstate",func);
      }
      else {
        $(window).on("hashchange",func);
      }
    },
    has: {
      push: function() {
        return ( typeof history === "object" && typeof history.pushState === "function" );
      }
    },
    get: function() {
      return ( this.has.push() ) ? window.location.search : ( ( window.location.hash.indexOf("?") != -1 ) ? "?"+unescape( window.location.hash ).split("?")[1] : '' );
    }
  },
  users: {
    that: this,
    init: function() {
      ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
        channel: 'users',
        action: 'load',
        init: true
      },{
        onsuccess: function( response, that ) {
          $.mobile.initializePage();
          if ( response.error ) {
            ipcl().notice( "Error: "+response.message, true );
            return;
          }
          if ( !response.user ) {
            $.mobile.changePage( $(".ipAuthTriggers"), {
              changeHash: false
            } );
            return;
          }

          $("#ipAuthTriggers").remove();
          ipsa( "user", response.user );
          ipsa( "users", response.users );
          that.page.hash();
          ipMobile.prototype.history.event(function() {
            that.page.hash();
          });

          new ipMobilePing();
        }
      }, {
        onsuccess: [ this.that ]
      });
    },
    fload: function( a, b ) {
      b = b || [];
      if ( ipga("first_degree_running") || ipga("first_degree_finished") ) {
        if ( ipga("first_degree_finished") ) {
          call_user_func_array( a, b );
          return;
        }
        if ( ipga("first_degree_running") ) {
          var callbacks = ipga("first_degree_callbacks") || ipsa("first_degree_callbacks",[]);
          callbacks.push( [ a, b ] );
          ipsa("first_degree_callbacks",callbacks);
        }
        return;
      }
      ipsa("first_degree_running",true);
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
        onsuccess: function( response ) {
          ipsa("first_degree_running",false);
          ipsa("first_degree_finished",true);
          if ( response.error ) {
            return;
          }
          response  = $.extend({}, ipga("users"), response );
          ipsa("users",response);

          var callbacks = ipga("first_degree_callbacks") || ipsa("first_degree_callbacks",[]);
          if ( callbacks.length ) {
            for( i = 0; i < callbacks.length; i++ ) {
              call_user_func_array( callbacks[i][0], callbacks[i][1] );
            }
          }
          call_user_func_array( a, b );
          ipsa("first_degree_callbacks",[]);
        },
        onerror: function( response, error ) {
          ipsa("first_degree_running",false);
        }
      });
      xhr.send();
    },
    start: function() {
      var that  = this,
          page  = $("#buddylist");
      if ( !$("#mobile_buddylist",page).length ) {
        ipMobile.prototype.load.pages.users();
        return;
      }
      $(".ui-title",page).text( L.CHAT );
      if ( !$("#mobile_buddylist ul",page).length ) {
        if ( page.hasClass("ui-page-active") ) {
          $("<ul />").appendTo($("#mobile_buddylist",page)).listview({
            filter: false,
            icon: false
          });
          if ( ( ipga( "user" ).SA || ipga( "user" ).ST ) == "online" ) {
            this.status.turn.on();
            this.status.base  = true;
          }
          else {
            this.status.base  = false;
          }
        }
      }
    },
    status: {
      base: false,
      turn: {
        ajax: false,
        on: function( a ) {
          if ( this.ajax !== false ) {
            return;
          }
          var that  = this;
          var caller  = function() {
            $("#mobile_buddylist ul").empty();
            $("#buddylist_holder").addClass("online");
            $("#buddylist .mCenteredLoader").show(0);
            $(".jqm-status-nav").text( L.CHAT_OFF );
            ipMobile.prototype.users.status.base  = true;

            ipqx(ipgo('docServer')+'ipChat/pull.php',"POST", {
              channel: 'users',
              action: 'load',
              online: true,
              limit: 30
            },{
              onsuccess: function( response ) {
                $("#buddylist .mCenteredLoader").fadeOut(300, function() {
                  ipMobile.prototype.users.render.list( response );
                  $.mobile.activePage.trigger("pagecreate");
                });
              }
            });
          };
          if ( a ) {
            this.ajax = ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{channel:"settings",process:"chat",action:"update",data:"online"},{
              onsuccess: function( res ) {
                caller();
              },
              onerror: function( res, err ) {
                
              },
              onloadend: function() {
                that.ajax = false;
              }
            } );
          }
          else {
            caller();
          }
        },
        off: function( a ) {
          if ( this.ajax !== false ) {
            return;
          }
          $("#buddylist_holder").removeClass("online");
          $("#mobile_buddylist ul").empty();
          $(".jqm-status-nav").text( L.CHAT_ON );
          ipMobile.prototype.users.status.base  = false;
          $.mobile.activePage.trigger("pagecreate");

          if ( a ) {
            ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{channel:"settings",process:"chat",action:"update",data:"offline"},{});
          }
        }
      }
    },
    render: {
      list: function( users ) {
        var list  = $("#mobile_buddylist ul");
        for( x in users ) {
          this.single( users[x], list );
        }
        list.listview("refresh");
        $(".presence_icon.online_icon",list.parent()).parents("li").prependTo( list );
      },
      single: function( user, list ) {
        var a = list.cn("li").data("user",user),
            b = a.cn("a").html( user.NM ),
            c = b.cn("i",{"class":"ui-li-icon ui-corner-none"},false,"prepend"),
            d = a.cn("div",{"class":"icons-right"}),
            e = d.cn("span",{"class":"presence_icon presence_icon_"+user.ID});
        c.css({
          'background-image':'url('+user.AV+')'
        });
        if ( ( user.SA || user.ST ) == "online" ) {
          e.addClass("online_icon");
        }
        b.on("click", function(e) {
          e.preventDefault();
          var user  = a.data("user");
          ipMobile.prototype.history.add( "?i="+user.ID+"&t=u" );
        });
      }
    }
  }
});
var ipMobilePing  = ipMobile.extend({
  initialize: function() {
    if ( !this.intervalRunning ) {
      this.intervalRunning  = true;
      setTimeout( this.startInterval, 5000 );
    }
  },
  ajax: {},
  pingID: uniqid(),
  startInterval: function() {
    pingStartedMob  = true;
    ipMobilePing.prototype.findConnector();
  },
  findConnector: function() {
    var that  = this,
        timerMin  = 2000,
        timerMax  = 60000,
        timerCur  = 0;
    if ( !!window.EventSource ) {
      function EventSourceHandler() {
        var query   = {
          pingEvent: true,
          eventSource: true,
          ping_id: ipMobilePing.prototype.pingID,
          tabs: ipMobilePing.prototype.parse.tabs(),
          charset_test: '€,´,€,´,水,Д,Є',
          request_id: "xhr-"+unds.uniqueId(),
          request_sd: $.cookie( "PHPSESSID" ),
          mobile_only: true,
          page: $.mobile.activePage.attr("id")
        };
        var source  = new EventSource( ipgo("docServer")+"ipChat/pull-event.php?"+http_build_query( query ) );

        source.onopen = function( event ) {
          
        };
        source.onmessage  = function( event ) {
          timerCur  = 0;
        };
        source.onerror  = function( event ) {
          if ( timerCur < timerMax ) {
            timerCur  = ( timerCur + timerMin );
          }
          event.target.close();
          setTimeout( EventSourceHandler, timerCur );
        };
  
        source.addEventListener( "messages", function( event ) {
          timerCur  = 0;
          var data  = json_decode( event.data );
          ipMobilePing.prototype.parse.chat( data );
        }, false );
        source.addEventListener( "status", function( event ) {
          timerCur  = 0;
          var data  = json_decode( event.data );
          ipMobilePing.prototype.parse.status( data );
        }, false );
        source.addEventListener( "seen", function( event ) {
          timerCur  = 0;
          var data  = json_decode( event.data );
          ipMobilePing.prototype.parse.seen( data );
        }, false );
      }
      EventSourceHandler();
    }
    else {
      ipMobilePing.prototype.ping.start( [ 'chat', 'seen', 'status' ] );
    }
  },
  typer: false,
  statuser: false,
  handler: {
    time: {},
    count: {},
    handle: function( idx, idn, idm ) {
      if ( this.count[idx] >= 5 ) {
        this.time[idx]  = this.time[idx] || 0;
        this.time[idx]  +=  500;
        this.count[idx] = 0;

        if ( idm && !idm.error ) {
          call_user_func_array( idn, [ idm ] );
        }
        //console.log( idx+" error timeout "+this.time[idx] );
        setTimeout(function() {
          ipMobilePing.prototype.ping.start( idx );
        }, this.time[idx]);
        return;
      }
      if ( !idm || idm.error ) {
        this.count[idx] = this.count[idx] || 0;
        this.count[idx]++;
        //console.log( idx+" error "+this.count[idx] );
      }
      else {
        //console.log( idx+" error reset" );
        this.time[idx]  = 0;
        this.count[idx] = 0;

        call_user_func_array( idn, [ idm ] );
      }
      ipMobilePing.prototype.ping.start( idx );
    }
  },
  timeout: {
    get: function( idx ) {
      var idn = this.list();
      return idn[idx] || 5000;
    },
    set: function( idx, idt ) {
      var idn = this.list();
      idn[idx]  = idt;
      ipsa("ptl",idn);
      return idt;
    },
    list: function( idx ) {
      var idn = {'users':20000,'chat':500,'seen':10000,'status':10000};
      //var idn = {'seen':10000};
      if ( ipga("ptl") ) {
        return ipga("ptl");
      }
      return ipsa("ptl",idn);
    }
  },
  ping: {
    start: function( idx ) {
      idx = ( unds.isArray( idx ) ) ? idx : [ idx ];

      if ( idx && idx.length ) {
        var idm = ipMobilePing.prototype.ajax;
        for( b in idx ) {
          var idt = ipMobilePing.prototype.timeout.get( idx[b] );
          if ( parseInt( idt ) === 1 ) {
            call_user_func_array( this[idx[b]] );
          }
          else {
            idm[idx[b]] = setTimeout( this[idx[b]], idt );
          }
        }
        ipMobilePing.prototype.ajax = idm;
      }
    },
    users: function() {
      ipMobilePing.prototype.request( 'users', ipMobilePing.prototype.parse.users );
    },
    chat: function() {
      ipMobilePing.prototype.request( 'chat', ipMobilePing.prototype.parse.chat );
    },
    seen: function() {
      ipMobilePing.prototype.request( 'seen', ipMobilePing.prototype.parse.seen );
    },
    status: function() {
      ipMobilePing.prototype.request( 'status', ipMobilePing.prototype.parse.status );
    }
  },
  parse: {
    temp: {},
    tabs: function() {
      var par = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
          idx = par.i,
          idn = ( par.t == "u" ) ? "user" : "group";
      if ( idx && idn ) {
        var body  = $(".message-body:last").data("message"),
            message_id  = false,
            user_id     = false;
        if ( body ) {
          message_id  = body.ID,
          user_id     = body.sent_from;
        }
        return [ [ idx, idn, true, user_id, message_id ] ];
      }
      return [];
    },
    users: function( users ) {
      console.log( "users" );
    },
    chat: function( response ) {
      pingStartedMob  = true;
      var par = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
          pdx = par.i,
          pdn = ( par.t == "u" ) ? "user" : "group",
          tmp = ipMobilePing.prototype.parse.temp,
          did = false;
      if ( response ) {
        for( x in response ) {
          var message = response[x];
          var idx = ( message.groupID === 0 ) ? message.sent_from : message.groupID;
          var idn = ( message.groupID === 0 ) ? "user" : "group";
          if ( pdx == idx && pdn == idn ) {
            did = true;
            ipMobile.prototype.chat.render.item( message );
          }
          else {
            tmp[idn]  = tmp[idn] || {};
            tmp[idn][idx] = tmp[idn][idx] || [];
            if ( $.inArray( message.ID, tmp[idn][idx] ) == '-1') {
              tmp[idn][idx].push( message.ID );
            }
            if ( tmp[idn][idx].length ) {
              var thread  = $("#threadlist_row_id_"+idn+"_"+idx);
              if ( thread.length ) {
                if ( $("span.num-msg",thread).length ) {
                  var num_msg = $("span.num-msg",thread).text( tmp[idn][idx].length );
                }
                else {
                  var num_msg = $("<span />",{"class":"num-msg"}).text( tmp[idn][idx].length ).appendTo( $("div.image",thread) );
                }
                num_msg.stop(true).css("bottom","3px").animate({
                  bottom:"27px"
                },300,"linear",function() {
                  $(this).animate({bottom:"3px"},300).animate({bottom:"20px"},400).animate({bottom:"3px"},500).animate({bottom:"10px"},600).animate({bottom:"3px"});
                });
                if ( ipChat.prototype.can_play( idx, idn ) ) {
                  ipChat.prototype.play_audio( "messageSound" );
                }
              }
            }
          }
        }
        ( did && $("body").scrollTop( $("body")[0].scrollHeight ) );
      }
    },
    seen: function( response ) {
      var par = ipChat.prototype.parse_query_params( ipMobile.prototype.history.get() ),
          pdx = par.i,
          pdn = ( par.t == "u" ) ? "user" : "group";
      var seen  = response.s;
      var typ   = response.t;

      $(".voice .actions span.seen").remove();
      if ( !unds.isEmpty( seen ) ) {
        var last_message  = $(".voice:last .actions"),
            seen_holder   = $("<span />",{"class":"mfss fcg seen"});
        for( var i = 0; i < seen.length; i++ ) {
          var seen  = seen[i];
          if ( seen[0] == pdn && seen[1] == pdx ) {
            if ( seen[2] ) {
              var text  = "Seen "+seen[2];
              seen_holder.text( text ).appendTo( last_message );
            }
            break;
          }
        }
      }
      if ( !$("#typingIndicator").length || pdn != "user" ) {
        return;
      }
      $("#typingIndicator").hide(0);
      if ( !unds.isEmpty( typ ) ) {
        if ( ipMobilePing.prototype.typer ) {
          clearInterval( ipMobilePing.prototype.typer )
        }
        for( x in typ ) {
          if ( x == pdx ) {
            $("#typingIndicator").show(0);
            ipMobilePing.prototype.typer  = setTimeout(function() {
              $("#typingIndicator").hide(0);
            }, 10000);
            break;
          }
        }
      }
    },
    status: function( response ) {
      if ( ipMobilePing.prototype.statuser ) {
        clearTimeout( ipMobilePing.prototype.statuser );
      }
      if ( unds.isEmpty( response ) || response.t === "continue" ) {
        $(".presence_icon").removeClass("online_icon offline_icon busy_icon");
        return;
      }
      var users = ipga("users");

      unds.each(response, function( user ) {
        var user_status = ( user.SA || user.ST );
        var status_icon = $(".presence_icon_"+user.ID);
        var active_time = $(".fbLastActiveTimestamp"+user.ID);
        if ( user_status == "online" ) {
          status_icon.removeClass("online_icon offline_icon busy_icon").addClass("online_icon");
        }
        active_time.attr("timestamp",user.LS).text( timeDifference( user.LS ) ).show(0);

        if ( users[user.ID] ) {
          users[user.ID].SA = user.SA;
          users[user.ID].ST = user.ST;
          users[user.ID].LS = user.LS;
        }
      });

      if ( $("#mobile_buddylist").length ) {
        $(".presence_icon.online_icon","#mobile_buddylist").parents("li").prependTo( $("#mobile_buddylist ul:first") );
      }

      ipsa("users",users);
      ipMobilePing.prototype.statuser = setTimeout(function() {
        $(".presence_icon").removeClass("online_icon offline_icon busy_icon");
        unds.each(ipga("users"),function(b){b.SA=b.ST=false;});
      }, 50000);
    }
  },
  request: function( idx, idn ) {
    var idm = ipga("pings") || ipsa("pings",{});
    if ( idm[idx] ) {
      return;
    }
    idm[idx]  = true;
    ipsa("pings",idm);

    if ( idx === "chat" && ipgo('pingServer') ) {
      var xhr = $.ajax({
        url: ipgo('pingServer')+"pull.php",
        type: "POST",
        cache: false,
        global: false,
        dataType: "jsonp",
        data: {
          channel: 'ping',
          process: idx,
          ping_id: ipMobilePing.prototype.pingID,
          tabs: ipMobilePing.prototype.parse.tabs(),
          charset_test: '€,´,€,´,水,Д,Є',
          request_id: "xhr-"+unds.uniqueId(),
          request_sd: $.cookie( "PHPSESSID" )
        },
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true,
        crossDomain: true
      });
      xhr.done(function( response, textStatus, jqXHR ) {
        idm[idx]  = false;
        ipsa("pings",idm);
        ipMobilePing.prototype.handler.handle( idx, idn, response );
      });
      xhr.fail(function( jqXHR, textStatus, errorThrown ) {
        idm[idx]  = false;
        ipsa("pings",idm);
        ipMobilePing.prototype.handler.handle( idx, idn, false );
      });
      xhr.always(function( response, textStatus, errorThrown ) {
        //console.log( response );
      });
      return;
    }

    var xhr = new ipXhr;
    xhr.open( ipgo('docServer')+'ipChat/pull.php?'+idx+"="+time() );
    xhr.params({
      channel: 'ping',
      process: idx,
      ping_id: ipMobilePing.prototype.pingID,
      tabs: ipMobilePing.prototype.parse.tabs()
    });
    xhr.callback({
      onsuccess: function( response ) {
        idm[idx]  = false;
        ipsa("pings",idm);
        ipMobilePing.prototype.handler.handle( idx, idn, response );
      },
      onerror: function( response, error ) {
        idm[idx]  = false;
        ipsa("pings",idm);
        ipMobilePing.prototype.handler.handle( idx, idn, false );
      },
      onloadend: function() {
        
      }
    });
    xhr.send();
  }
});

function mobileParseMesageText( text ) {
  text  = ( text+'' ).toString();
  var cont  = $("<div />").html( text );
  $("a",cont).each(function() {
    this.outerHTML  = $(this).html();
  });
  return cont.html();
}

if ( !window.EventSource ) {
  (function(u){function e(){this.data={}}e.prototype={get:function(B){return this.data[B+"~"]},set:function(B,C){this.data[B+"~"]=C},"delete":function(B){delete this.data[B+"~"]}};function k(){this.listeners=new e()}function w(B){setTimeout(function(){throw B},0)}k.prototype={dispatchEvent:function(G){G.target=this;var E=String(G.type);var D=this.listeners;var B=D.get(E);if(!B){return}var F=B.length;var C=-1;var H=null;while(++C<F){H=B[C];try{H.call(this,G)}catch(I){w(I)}}},addEventListener:function(E,F){E=String(E);var D=this.listeners;var B=D.get(E);if(!B){B=[];D.set(E,B)}var C=B.length;while(--C>=0){if(B[C]===F){return}}B.push(F)},removeEventListener:function(F,H){F=String(F);var E=this.listeners;var B=E.get(F);if(!B){return}var G=B.length;var C=[];var D=-1;while(++D<G){if(B[D]!==H){C.push(B[D])}}if(C.length===0){E["delete"](F)}else{E.set(F,C)}}};function A(B){this.type=B;this.target=null}function j(C,B){A.call(this,C);this.data=B.data;this.lastEventId=B.lastEventId}j.prototype=A.prototype;var n=u.XMLHttpRequest;var c=u.XDomainRequest;var s=Boolean(n&&((new n()).withCredentials!==undefined));var l=s;var y=s?n:c;var t=-1;var m=0;var v=1;var d=2;var q=3;var i=4;var r=5;var g=6;var f=7;var h=/^text\/event\-stream;?(\s*charset\=utf\-8)?$/i;var p=1000;var b=18000000;function z(C,B){var D=Number(C)||B;return(D<p?p:(D>b?b:D))}function x(C,D,B){try{if(typeof D==="function"){D.call(C,B)}}catch(E){w(E)}}function a(H,F){H=String(H);var E=Boolean(s&&F&&F.withCredentials);var N=z(F?F.retry:NaN,1000);var J=z(F?F.heartbeatTimeout:NaN,45000);var K=(F&&F.lastEventId&&String(F.lastEventId))||"";var M=this;var W=N;var O=false;var L=new y();var P=0;var Q=0;var X=0;var aa=t;var Z=[];var B="";var V="";var U=null;var G=i;var C="";var S="";F=null;function R(){aa=d;if(L!==null){L.abort();L=null}if(P!==0){clearTimeout(P);P=0}if(Q!==0){clearTimeout(Q);Q=0}M.readyState=d}function D(ak){var ah=aa===v||aa===m?L.responseText||"":"";var ab=null;var ac=false;if(aa===m){var af=0;var ae="";var al="";if(l){try{af=Number(L.status||0);ae=String(L.statusText||"");al=String(L.getResponseHeader("Content-Type")||"")}catch(aj){af=0}}else{af=200;al=L.contentType}if(af===200&&h.test(al)){aa=v;O=true;W=N;M.readyState=v;ab=new A("open");M.dispatchEvent(ab);x(M,M.onopen,ab);if(aa===d){return}}else{if(af!==0){var am="";if(af!==200){am="EventSource's response has a status "+af+" "+ae.replace(/\s+/g," ")+" that is not 200. Aborting the connection."}else{am="EventSource's response has a Content-Type specifying an unsupported type: "+al.replace(/\s+/g," ")+". Aborting the connection."}setTimeout(function(){throw new Error(am)});ac=true}}}if(aa===v){if(ah.length>X){O=true}var ag=X-1;var ad=ah.length;var ai="\n";while(++ag<ad){ai=ah[ag];if(G===q&&ai==="\n"){G=i}else{if(G===q){G=i}if(ai==="\r"||ai==="\n"){if(C==="data"){Z.push(S)}else{if(C==="id"){B=S}else{if(C==="event"){V=S}else{if(C==="retry"){N=z(S,N);W=N}else{if(C==="heartbeatTimeout"){J=z(S,J);if(P!==0){clearTimeout(P);P=setTimeout(U,J)}}}}}}S="";C="";if(G===i){if(Z.length!==0){K=B;if(V===""){V="message"}ab=new j(V,{data:Z.join("\n"),lastEventId:B});M.dispatchEvent(ab);if(V==="message"){x(M,M.onmessage,ab)}if(aa===d){return}}Z.length=0;V=""}G=ai==="\r"?q:i}else{if(G===i){G=r}if(G===r){if(ai===":"){G=g}else{C+=ai}}else{if(G===g){if(ai!==" "){S+=ai}G=f}else{if(G===f){S+=ai}}}}}}X=ad}if((aa===v||aa===m)&&(ak||ac||(X>1024*1024)||(P===0&&!O))){aa=t;L.abort();if(P!==0){clearTimeout(P);P=0}if(W>N*16){W=N*16}if(W>b){W=b}P=setTimeout(U,W);W=W*2+1;M.readyState=m;ab=new A("error");M.dispatchEvent(ab);x(M,M.onerror,ab)}else{if(P===0){O=false;P=setTimeout(U,J)}}}function T(){D(false)}function I(){D(true)}if(l){Q=setTimeout(function Y(){if(L.readyState===3){T()}Q=setTimeout(Y,500)},0)}U=function(){P=0;if(aa!==t){D(false);return}if(l&&(L.sendAsBinary!==undefined||L.onloadend===undefined)&&u.document&&u.document.readyState&&u.document.readyState!=="complete"){P=setTimeout(U,4);return}L.onload=L.onerror=I;if(l){L.onabort=I;L.onreadystatechange=T}L.onprogress=T;O=false;P=setTimeout(U,J);X=0;aa=m;Z.length=0;V="";B=K;S="";C="";G=i;var ab=H.slice(0,5);if(ab!=="data:"&&ab!=="blob:"){ab=H+((H.indexOf("?",0)===-1?"?":"&")+"lastEventId="+encodeURIComponent(K)+"&r="+String(Math.random()+1).slice(2))}else{ab=H}L.open("GET",ab,true);if(l){L.withCredentials=E;L.responseType="text";L.setRequestHeader("Accept","text/event-stream")}L.send(null)};k.call(this);this.close=R;this.url=H;this.readyState=m;this.withCredentials=E;this.onopen=null;this.onmessage=null;this.onerror=null;U()}function o(){this.CONNECTING=m;this.OPEN=v;this.CLOSED=d}o.prototype=k.prototype;a.prototype=new o();o.call(a);if(y){u.NativeEventSource=u.EventSource;u.EventSource=a}}(this));
}