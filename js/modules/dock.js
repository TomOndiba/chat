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
var ipDockPanel = ipChat.extend({
  initialize: function() {
    //this.prevent_console();
    if ( !window.ipExtend ) {
      throw new Error( b2rs( "661221445,2020894062,1680285796,1868919584,1852797984,1702390131,1953701993,1847621481,1852075895".split(",") ) );
    }
    //window.ipExtend.ipdp  = this;
    this.process.render.base();
    $(window).on("resize", function(e) {
      ipDockPanel.prototype.process.responsive.tab.resizer.window(e);
    });

    return this;
  },
  process: {
    chat: {
      dynamic: function( id, type, done ) {
        if ( !id && !type ) {
          return;
        }
        if ( id instanceof jQuery ) {
          var tab = id;
          id    = tab.data("nubuid");
          type  = tab.data("nubmod");
        }
        else {
          var tab = $("#"+type+"Nub"+id);
        }
        if ( !tab.length ) {
          return;
        }
        var uiTypeaheadText = $("input:data(uiTypeaheadText)",tab);
        var textateaType    = $("._552h textarea._552m",tab);
        var messageText     = ( textateaType.length ) ? $.trim( textateaType.val() ) : false;
        var attachments     = gtam( id, type, true );
        if ( tab.data("istemp") === true && !uiTypeaheadText.length ) {
          $("a.addToThread.button",tab).trigger("click");
          return;
        }
        if ( uiTypeaheadText.length ) {
          var users = uiTypeaheadText.utokenizer( "get", "id" );
          var hasTokens = ( unds.isArray( users ) && !unds.isEmpty( users ) );
          if ( tab.data("istemp") === true && !hasTokens ) {
            return;
          }
          if ( uiTypeaheadText.data("uiTypeaheadCustom") === true ) {
            if ( done !== true && ( messageText.length || attachments.length ) ) {
              ipDockPanel.prototype.process.chat.send( tab );
              return;
            }
            if ( hasTokens && done === true ) {
              ipDockPanel.prototype.process.chat.tokensFormat( tab, users, messageText );
              return;
            }
          }
          else {
            if ( hasTokens && messageText.length ) {
              ipDockPanel.prototype.process.chat.tokensFormat( tab, users, messageText );
              return;
            }
          }
          //uiTypeaheadCustom
          if ( tab.data("istemp") !== true && hasTokens ) {
            return;
          }
        }

        if ( messageText.length || ( attachments && attachments.length ) ) {
          ipDockPanel.prototype.process.chat.send( tab );
        }
      },
      tokensFormat: function( tab, tokens, message ) {
        var tabCaller = ipDockPanel.prototype.process.tab.open;
        var hasTokens = ( unds.isArray( tokens ) && !unds.isEmpty( tokens ) );
        var message   = $.trim( message );
        if ( !( tab instanceof jQuery ) || !tab.length || !tokens ) {
          return false;
        }
        if ( tab.data("istemp") === true && !message.length ) {
          return;
        }
        tab.removeData("foo");
        if ( tokens.length === 1 && !tab.data("istemp") && tab.data("nubmod") !== "group" ) {
          tokens.push( tab.data("nubuid") );
        }
        if ( !tab.data("istemp") ) {
          ipDockPanel.prototype.process.tab.flyout.hide( tab );
        }
        if ( tokens.length === 1 && tab.data("nubmod") !== "group" ) {
          if ( tab.data("istemp") === true ) {
            var ntab  = tabCaller( tokens[0], "user", tab );
            ipDockPanel.prototype.process.responsive.tab.resizer.toggle( ntab );
            if ( message.length ) {
              ipDockPanel.prototype.process.chat.send( ntab, false, message );
            }
          }
          else {
            var ntab  = tabCaller( tokens[0], "user" );
            ipDockPanel.prototype.process.responsive.tab.resizer.toggle( ntab );
          }
          return;
        }
        if ( tab.data("nubmod") === "group" ) {
          ipDockPanel.prototype.process.chat.update.group( tab.data("nubuid"), tokens );
        }
        else {
          ipDockPanel.prototype.process.chat.create.group(
            tokens,
            function( response ) {
              if ( !response || response.error ) {
                ipcl().notice( response.message );
                return false;
              }
              u_50x4( response );
              if ( tab.data("istemp") === true ) {
                tabCaller( response.ID, "group", tab );
                ipDockPanel.prototype.process.responsive.tab.resizer.toggle( tab );
              }
              else {
                var ntab  = tabCaller( response.ID, "group" );
                ipDockPanel.prototype.process.responsive.tab.resizer.toggle( ntab );
              }
            },
            function( response, error ) {
              ipcl().notice( error.message );
            }
          );
        }
      },
      create: {
        group: function( users, onsuccess, onerror ) {
          if ( has_action( "IP_oncreate_group" ) ) {
            do_action( "IP_oncreate_group", true, users, onsuccess, onerror );
            return;
          }
          var xhr = new ipXhr();
          xhr.open( ipgo('docServer')+'ipChat/pull.php' );
          xhr.params({
            channel: 'messages',
            process: 'group',
            action: 'add',
            users: users
          });
          xhr.callback({
            onsuccess: onsuccess,
            onerror: onerror
          });
          xhr.send();
        }
      },
      update: {
        group: function( id, users ) {
          if ( !id || !users || unds.isEmpty( users ) || !unds.isArray( users ) ) {
            return;
          }
          var tab = $("#groupNub"+id);
          if ( !tab.length ) {
            return;
          }
          g_50x4(id, function( group, tab, id, users, cron ) {
            users = unds.difference( users, group.users );
            if ( unds.isEmpty( users ) ) {
              return;
            }
            if ( cron ) {
              return;
            }
            var onsuccess_func  = function( res ) {
                  if ( res.error ) {
                    return;
                  }
                  //do_action( "on_after_send_notice", false, res.message );
                  ipDockPanel.prototype.process.chat.messages.add( tab, res.message );
                  u_50x4( res.group );
                  ipDockPanel.prototype.process.tab.convert( tab, id, "group", true );
                  if ( ipWebSocket != false ) {
                    var message = unds.clone( res.message );
                    message.message = message.message.replace( "<strong>You</strong>", fname( ipga("user").NM ) );
                    ipWebSocket.send(
                      json_encode(
                        $.extend( message, {
                          event: "message",
                          users: g_50x4( message.groupID )
                        } )
                      )
                    );
                  }
                };

            if ( has_action( "IP_onupdate_group" ) ) {
              do_action( "IP_onupdate_group", true, id, users, onsuccess_func );
              return;
            }

            ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
              channel: 'messages',
              process: 'group',
              action: 'update',
              id: id,
              users: users
            },{
              onsuccess: onsuccess_func
            });
          }, false, [ tab, id, users ]);
        }
      },
      send: function( idx, idn, message ) {
        var tab;
        if ( parseInt( idx ) === 0 ) {
          return;
        }
        if ( idx instanceof jQuery ) {
          tab = idx;
          idx = tab.data("nubuid");
          idn = tab.data("nubmod");
        }
        else {
          tab = $("#"+idn+"Nub"+idx);
        }
        if ( !tab || !( tab instanceof jQuery ) || !tab.length ) {
          return;
        }
        if ( tab.data("istemp") === true ) {
          return;
        }

        if ( !uf_kshc( idx, idn ) ) {
          tmas( idx, idn, tab );
          return;
        }

        var inn = $(".ipDockChatTabFlyout:first",tab);
        var idt = $("._552h textarea._552m",tab);
        var ida = gtam( idx, idn, true );

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
  
          //msg = ipDockPanel.prototype.process.responsive.tab.textarea.format.text( msg );
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

        $(".can-destroy",tab).remove();

        var _510g = $("._510g.seen",tab);
        if ( _510g.length ) {
          _510g.removeClass("seen");
          _510g.find("._510h:first").removeClass("icon-time icon-comment-alt");
          _510g.find("._510f:first").html("");
        }

        var bubble  = unds.clone( params );
            bubble.attachments  = gtam( idx, idn );
        if ( ida && ida.length ) {
          ctam( idx, idn );
        }

        if ( ida && ( ida.length === ipos( bubble.attachments, "mimegroup", "image" ) ) ) {
          params.has_attachment = bubble.has_attachment = 1;
          params.notice_section = bubble.notice_section = "attachment";
          params.is_notice  = bubble.is_notice = 1;
        }

        var stream  = $("._5e52_stream",tab);
        if ( stream.length ) {
          stream  = stream.data("stream");
          if ( stream && stream.process && !stream.detect ) {
            stream.cancel();
          }
        }

        $(".chatAttachmentShelf",tab).empty();
        ipDockPanel.prototype.process.responsive.tab.inner( inn, true );
        ipDockPanel.prototype.process.chat.messages.add( tab, bubble );
        ipDockPanel.prototype.process.chat.messages.history.item( $('li.uiMessageItem .jewelContent div:first'), params, "prepend" );
        ipDockPanel.prototype.process.responsive.tab.textarea.typing.clear( idx, idn );

        var onsuccess_func  = function( response ) {
              if ( response.error ) {
                ipDockPanel.prototype.process.chat.messages.error( "message_"+params.unID, response.message );
                return;
              }
              do_action( "on_after_send_message", false, response );
              add_50dw( idx, idn, response );
              ipDockPanel.prototype.process.chat.messages.update( tab, response, params.unID );
              if ( ipWebSocket != false ) {
                ipWebSocket.send(
                  json_encode(
                    $.extend( response, {
                      event: "message",
                      users: ( !checkInt( response.groupID, 0 ) ) ? g_50x4( response.groupID ) : false
                    } )
                  )
                );
              }
            },
            onerror_func  = function( response, error ) {
              ipDockPanel.prototype.process.chat.messages.error( "message_"+params.unID, error.message );
            };

        var kso = $("#message_"+params.unID).addClass("on-progress");
        var mcontainer  = kso.parents(".messages:first");
        $(".on-sending",mcontainer).stop(true).fadeIn();

        do_action( "on_send_message", false, params );

        if ( has_action( "IP_send_message" ) ) {
          do_action( "IP_send_message", true, params, onsuccess_func, onerror_func );
          return message.unID;
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
          onsuccess: onsuccess_func,
          onerror: onerror_func
        });
        xhr.send();
        return message.unID;
      },
      messages: {
        init: function( id, type ) {
          var tab = $("#"+type+"Nub"+id);
          if ( !tab.length ) {
            return;
          }
          var history = get_50dw( id, type );
          if ( history ) {
            for( x in history ) {
              ipDockPanel.prototype.process.chat.messages.add( tab, history[x] );
            }
          }
        },
        history: {
          init: function( menu ) {
            if ( !menu.length ) {
              return;
            }
            if ( !isNaN( parseInt( menu.data("older_id") ) ) ) {
              return;
            }
            menu.parents(".uiMenu:first").ipscroll({
              gripper: 'blackGripper',
              onTotalScroll: function() {
                ipDockPanel.prototype.process.chat.messages.history.older( menu );
              },
              onScroll: function( per ) {
                if ( per > 80 && !menu.data("finished") ) {
                  $(".jewelLoading:first",menu.parents("li.uiMessageItem")).removeClass("hidden_elem");
                }
              }
            });
            var onsuccess_func  = function( response ) {
                  if ( response.error ) {
                    menu.html( '<div class="mas pam uiBoxYellow"><strong>'+response.message+'</strong><div>' );
                    $(".jewelLoading:first",menu.parents("li.uiMessageItem")).addClass("hidden_elem");
                    return;
                  }
                  ipDockPanel.prototype.process.chat.messages.history.list( menu, response.messages, "append" );
                  menu.data("older_id",response.older);
                },
                onerror_func  = function() {
                  menu.parents(".openToggler:first").removeClass("openToggler");
                };

            if ( has_action( "IP_onload_history" ) ) {
              do_action( "IP_onload_history", true, false, onsuccess_func, onerror_func );
              return;
            }

            ipqx( ipgo('docServer')+'ipChat/pull.php', "POST", {
              channel: 'messages',
              process: 'history',
              older: false
            }, {
              onsuccess: onsuccess_func,
              onerror: onerror_func
            });
          },
          older: function( menu ) {
            if ( !menu.length ) {
              return;
            }
            var spinner = $(".jewelLoading:first",menu.parents("li.uiMessageItem"));
            if ( menu.data("loading") ) {
              return;
            }
            if ( menu.data("finished") ) {
              spinner.addClass("hidden_elem");
              return;
            }
            if ( isNaN( parseInt( menu.data("older_id") ) ) ) {
              return;
            }

            spinner.removeClass("hidden_elem");
            menu.parents(".uiMenu:first").ipscroll("scrollToBottom");
            menu.data("loading",true);

            var older = parseInt( menu.data("older_id") ),
                onsuccess_func  = function( response ) {
                  if ( response.error ) {
                    menu.data("finished",true);
                    return;
                  }
                  ipDockPanel.prototype.process.chat.messages.history.list( menu, response.messages, "append" );
                  menu.data("older_id",response.older);
                },
                onloadend_func  = function() {
                  menu.data("loading",false);
                  spinner.addClass("hidden_elem");
                };

            if ( has_action( "IP_onload_history" ) ) {
              do_action( "IP_onload_history", true, older, onsuccess_func, onloadend_func );
              return;
            }

            ipqx( ipgo('docServer')+'ipChat/pull.php', "POST", {
              channel: 'messages',
              process: 'history',
              older: older
            }, {
              onsuccess: onsuccess_func,
              onloadend: onloadend_func
            });
          },
          list: function( menu, messages, method ) {
            $(".uiBoxYellow",menu).remove();
            $(".jewelLoading:first",menu.parents("li.uiMessageItem")).addClass("hidden_elem");
            for( x in messages ) {
              ipDockPanel.prototype.process.chat.messages.history.item( menu, messages[x], method );
            }
          },
          item: function( menu, message, method ) {
            var userID  = parseInt( ipga("user").ID );
            var sentCat = ( parseInt( message.groupID ) === 0 || isNaN( parseInt( message.groupID ) ) ) ? "user" : "group";
            var catID   = ( sentCat === "group" ) ? parseInt( message.sent_to ) : parseInt( ( message.sent_from === userID ) ? message.sent_to : message.sent_from );
            var thread  = $("li#"+sentCat+"Thread"+catID,menu);
                method  = method || "append";
  
            if ( !thread.length ) {
              var a1  = menu.cn("li",{"id":sentCat+"Thread"+catID}, false, method).data("message",message),
                  b1  = a1.cn("a",{"class":"messagesContent","role":"button"}),
                  c1  = b1.cn("div",{"class":"clearfix"}),
                  d1  = c1.cn("div",{"class":"MercuryThreadImage _8o _8s lfloat"},'<div class="_55lt"></div>'),
                  e1  = c1.cn("div",{"class":"clearfix _42ef"}),
                  f1  = e1.cn("div",{"class":"snippetThumbnail rfloat"},'<span class="_56hv hidden_elem"><i></i></span><span class="hidden_elem"><span class="_56hy"></span><span class="_56hv"><i></i></span></span>'),
                  g1  = e1.cn("div",{"class":"content fsm fwn fcg"},'<div class="author"><strong>Loading&hellip;</strong></div><div class="snippet preview fsm fwn fcg"><span></span><span>Loading&hellip;</span></div><div class="time"><abbr class="timestamp">Loading&hellip;</abbr></div>');
              thread  = a1;
            }
            else {
              menu[method]( thread );
            }
  
            thread.on("click", function( event ) {
              event.preventDefault();
              var tab = ipDockPanel.prototype.process.tab.open( catID, sentCat );
              $(this).parents(".uiToggle:first").removeClass("openToggler");
              $(".ipChatSidebar").removeClass("noPreventFyout");
              ipDockPanel.prototype.process.responsive.tab.resizer.toggle( tab );
            });
  
            $(".content .time abbr.timestamp",thread).html( message.timestamp );
            if ( !message.hasOwnProperty( "is_sticker" ) ) {
              var regex = ipDockPanel.prototype.process.responsive.tab.textarea.format.regex( "emoj_reg" );
              message.is_sticker  = regex.test( message.message );
              message.message = ipDockPanel.prototype.process.chat.messages.parse_message( message );
            }
            if ( message.is_sticker ) {
              g_50x5( message.sent_from, function( user, thread, len ) {
                var text  = sprintf( L.SENT_STICKER, ( ( user.ID === userID ) ? L.YOU : user.NM ) );
                $(".content .snippet span:eq(1)",thread).html( text );
                $(".content .snippet span:eq(0)",thread).html('<i class="icon-picture"></i>');
                var sticker = $(ipDockPanel.prototype.process.responsive.tab.textarea.format.emoji( message.message ));
                if ( $("img",sticker).length ) {
                  $(".snippetThumbnail ._56hv:first",thread).removeClass("hidden_elem").find("i").css({
                    backgroundImage: 'url("'+$("img",sticker).attr("src")+'")'
                  });
                  $(".snippetThumbnail span:eq(1)",thread).addClass("hidden_elem");
                }
              }, function( user, thread, len ) {
                var text  = sprintf( L.SENT_STICKER, ( ( user.ID === userID ) ? L.YOU : L.SOMEONE ) );
                $(".content .snippet span:eq(1)",thread).html( text );
                $(".content .snippet span:eq(0)",thread).html('<i class="icon-picture"></i>');
              }, [ thread, count( message.attachments ) ]);
            }
            else if ( message.notice_section === "attachment" ) {
              g_50x5( message.sent_from, function( user, thread, len ) {
                var text  = sprintf( ( ( len === 1 ) ? L.SENT_IMAGE : L.SENT_IMAGES ), ( ( user.ID === userID ) ? L.YOU : user.NM ) );
                $(".content .snippet span:eq(1)",thread).html( text );
                $(".content .snippet span:eq(0)",thread).html('<i class="icon-picture"></i>');
              }, false, [ thread, count( message.attachments ) ]);
            }
            else {
              if ( sentCat !== "group" ) {
                if ( message.seen !== false ) {
                  $(".content .snippet span:eq(0)",thread).html('<i class="icon-ok"></i>');
                }
                else if ( catID !== message.sent_from ) {
                  $(".content .snippet span:eq(0)",thread).html('<i class="icon-reply"></i>');
                }
              }
              var content = apply_filters( "message_text_history", message.message );
              if ( sentCat === "group" && !message.is_notice ) {
                g_50x5( message.sent_from, function( user, thread, content ) {
                  var name  = ( user.ID === ipga("user").ID ) ? L.YOU : user.NM;
                  $(".content .snippet span:eq(1)",thread).html( name+": "+sanitize_msg( content ) );
                }, function( user, thread, content ) {
                  $(".content .snippet span:eq(1)",thread).html( "undefined: "+sanitize_msg( content ) );
                }, [ thread, content ]);
              }
              else {
                if ( !content.length ) {
                  if ( message.has_attachment === 1 ) {
                    content = $.trim( sprintf( L.SENT_FILES, '' ) );
                  }
                }
                content = sanitize_msg( content );
                $(".content .snippet span:eq(1)",thread).html( content );
              }
            }
  
            if ( message.has_attachment === 1 && !unds.isEmpty( message.attachments ) ) {
              var images  = ipos( message.attachments, 'mimegroup', 'image' );
              if ( !unds.isEmpty( images ) ) {
                var imageslen = count( images );
                    images    = images[lkio( images )];
                if ( imageslen > 1 ) {
                  var image_placeholder = $(".snippetThumbnail span:eq(1)",thread).removeClass("hidden_elem");
                  $(".snippetThumbnail ._56hv:first",thread).addClass("hidden_elem");
                }
                else {
                  var image_placeholder = $(".snippetThumbnail ._56hv:first",thread).removeClass("hidden_elem");
                  $(".snippetThumbnail span:eq(1)",thread).addClass("hidden_elem");
                }
                $("i",image_placeholder).css({
                  backgroundImage: 'url("'+ipgo("docServer")+images.thumbnail+'")'
                });
              }
            }
  
            if ( sentCat === "user" ) {
              g_50x5( catID, function( user, thread ) {
                $(".MercuryThreadImage ._55lt",thread).html('<img src="'+user.AV+'" height="50" width="50">');
                $(".content .author strong",thread).html( user.NM );
              }, function( user, thread ) {
                $(".MercuryThreadImage ._55lt",thread).html('<img src="'+ipgo("docServer")+'ipChat/images/users/default_unavail.jpg" height="50" width="50">');
                $(".content .author strong",thread).html( "undefined" );
              }, [ thread ]);
            }
            else {
              g_50x4( catID, function( group, thread, sent ) {
                if ( group.name && group.name.length ) {
                  $(".content .author strong",thread).html( group.name );
  
                  var users = unds.clone( group.users );
                  if ( sent === ipga("user").ID && users.length !== 3 ) {
                    users.unshift( sent );
                    users = unds.uniq( users );
                  }
                  else {
                    users = unds.without( users, ipga("user").ID );
                  }
  
                  $("._55lt div:first",thread).remove();
                  var a2  = $("._55lt",thread).cn("div");
                  for( i = 0; i <= Math.min( 2, ( users.length - 1 ) ); i++ ) {
                    var b2  = a2.cn("div",{"class":"_55lu"}),
                        c2  = b2.cn("img",{"width":50,"height":50});
                    if ( users.length === 2 ) {
                      if ( i === 1 ) {
                        b2.addClass("_57xo");
                      }
                      b2.css({
                        'width': '25px'
                      });
                      c2.css("margin-left","-12px");
                    }
                    else {
                      if ( i === 0 ) {
                        b2.addClass("_57pl").css({
                          'width': '33px'
                        });
                        c2.width(50).height(50).css("margin-left","-4px");
                      }
                      else if ( i === 1 ) {
                        b2.addClass("_57pm");
                      }
                      if ( i !== 0 ) {
                        b2.css({
                          'width': '17px',
                          'height': '25px'
                        });
                        c2.width(25).height(25).css("margin-left","-4px");
                      }
                    }
                    g_50x5( users[i], function( user, target ) {
                      $("img",target).attr('src',user.AV);
                    }, function( user, target ) {
                      $("img",target).attr('src',ipgo("docServer")+"ipChat/images/users/default_unavail.jpg");
                    }, [b2]);
                  }
                }
              }, false, [ thread, message.sent_from ]);
            }
          }
        },
        load: function( id, type ) {
          if ( h_50mz( id, type ) ) {
            if ( !i_50mzr( id, type ) ) {
              ipDockPanel.prototype.process.chat.messages.init( id, type );
              ipDockPanel.prototype.process.tab.flyout.hide( id, type, 300 );
            }
            var tab = $("#"+type+"Nub"+id);
            $(".loading-older",tab).addClass("hidden_elem");
            return;
          }
          s_50mz( id, type );
          s_50mzr( id, type );

          var tab = $("#"+type+"Nub"+id);

          var onsuccess_func  = function( response ) {
            if ( response.error ) {
              ipDockPanel.prototype.process.chat.messages.add_notice( tab, 'warning-sign', response.message, false, true );
              u_50mz( id, type );
              u_50mzr( id, type );
                
              ipDockPanel.prototype.process.tab.flyout.hide( id, type, 300 );

              cf_50dwf( id, type );
              var livetimestamp = $("._511m:first abbr.livetimestamp",tab);
              livetimestamp.html( sprintf( L.CONV_STARTED, livetimestamp.html() ) );
              return;
            }
            tab.data("loaded",true);
            add_50dw( id, type, response.messages );
            for( x in response.messages ) {
              ipDockPanel.prototype.process.chat.messages.add( tab, response.messages[x] );
            }
            if ( response.seen !== false ) {
              var _510g = $("._510g:first",tab).addClass("seen").removeClass("typing sent-mobile sent-chat"),
                  _510h = $("._510h:first",_510g).removeClass("icon-comment-alt").addClass("icon-time"),
                  _510f = $("._510f:first",_510g).html( "Seen "+response.seen );
            }
            ipDockPanel.prototype.process.tab.flyout.hide( id, type, 300 );
            $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
            /*if ( count( response.messages ) < 20 ) {
              cf_50dwf( id, type );
              var livetimestamp = $("._511m:first abbr.livetimestamp",tab);
              livetimestamp.html( "Conversation started "+livetimestamp.html() );
            }*/
          },
          onerror_func  = function( response, error ) {
            ipDockPanel.prototype.process.chat.messages.add_notice( tab, 'warning-sign', L.MSG_FETCH_FAILED+' ('+error.message+').' );
            u_50mz( id, type );
            u_50mzr( id, type );
            ipDockPanel.prototype.process.tab.flyout.hide( id, type, 300 );
          },
          onloadend_func  = function() {
            $(".loading-older",tab).addClass("hidden_elem");
          };

          if ( has_action( "IP_load_messages") ) {            
            do_action( "IP_load_messages", true, id, type, false, onsuccess_func, onerror_func, onloadend_func );
            return;
          }

          var xhr = new ipXhr();
          xhr.open( ipgo('docServer')+'ipChat/pull.php' );
          xhr.params({
            channel: 'messages',
            process: 'message',
            action: 'load',
            id: id,
            type: type,
            older: false
          });
          xhr.callback({
            onsuccess: onsuccess_func,
            onerror: onerror_func,
            onloadend: onloadend_func
          });
          xhr.send();
        },
        load_older: function( id, type ) {
          if ( id instanceof jQuery ) {
            var tab = id;
            if ( tab.length ) {
              var id    = tab.data("nubuid");
              var type  = tab.data("nubmod");
            }
          }
          else {
            var tab = $("."+type+"Nub"+id);
          }
          if ( !tab.length || tab.data("istemp") === true || ( !id || !type ) || fc_50dwf( id, type ) ) {
            return
          }

          var spinner = $(".loading-older:first",tab);
          if ( !spinner.hasClass("hidden_elem") ) {
            return false;
          }
          spinner.removeClass("hidden_elem");

          var _kso  = $(".conversation .messages ._kso._ksou:first",tab);
          if ( !_kso.length || !_kso.data("message") ) {
            spinner.addClass("hidden_elem");
            return;
          }
          var topPos  = _kso.offset().top;
          _kso  = _kso.data("message");

          if ( older_items = gspl_50dw( id, type, _kso.ID ) ) {
            for( x in older_items ) {
              ipDockPanel.prototype.process.chat.messages.add( tab, older_items[x], true );
            }
            $(".ipNubFlyoutBody",tab).ipscroll("scrollTo", topPos);
            spinner.addClass("hidden_elem");
            return;
          }

          var onsuccess_func  = function( response ) {
                if ( response.error ) {
                  cf_50dwf( id, type );
                  var livetimestamp = $("._511m:first abbr.livetimestamp",tab);
                  livetimestamp.html( sprintf( L.CONV_STARTED, livetimestamp.html() ) );
                  return;
                }
                add_50dw( id, type, response.messages );
                for( x in response.messages ) {
                  ipDockPanel.prototype.process.chat.messages.add( tab, response.messages[x], true );
                }
                $(".ipNubFlyoutBody",tab).ipscroll("scrollTo", topPos);
                /*if ( count( response.messages ) < 20 ) {
                  cf_50dwf( id, type );
                  var livetimestamp = $("._511m:first abbr.livetimestamp",tab);
                  livetimestamp.html( "Conversation started "+livetimestamp.html() );
                }*/
              },
              onerror_func  = function( response, error ) {
                cf_50dwf( id, type );
                var livetimestamp = $("._511m:first abbr.livetimestamp",tab);
                livetimestamp.html( sprintf( L.CONV_STARTED, livetimestamp.html() ) );
              },
              onloadend_func  = function() {
                spinner.addClass("hidden_elem");
              };
          if ( has_action( "IP_load_older_messages" ) ) {
            do_action( "IP_load_older_messages", true, id, type, _kso.ID, onsuccess_func, onerror_func, onloadend_func );
            return;
          }

          var xhr = new ipXhr();
          xhr.open( ipgo('docServer')+'ipChat/pull.php' );
          xhr.params({
            channel: 'messages',
            process: 'message',
            action: 'load',
            id: id,
            type: type,
            older: _kso.ID
          });
          xhr.callback({
            onsuccess: onsuccess_func,
            onerror: onerror_func,
            onloadend: onloadend_func,
          });
          xhr.send();
        },
        list: function( id, type, last ) {
          
        },
        add: function( tab, message, prevent ) {
          var ulist = $("div.conversation",tab);
          ipPing.prototype.parse.seen( false, tab );
          $("._51lq",tab).removeClass("typing").html('');
          $("._51lqc",tab).remove();
          return ipDockPanel.prototype.process.chat.messages.skeleton( tab, ulist, message, prevent );
        },
        add_notice: function( tab, icon, message, sent, isEmpty ) {
          sent  = sent || time();
          var ulist = $("div.conversation",tab);
          var last  = $(".ipChatConvItem:last",ulist);
          if ( last.length && last.data("noticeConv") ) {
            $("span._1_zk",last).html( message );
            $("div._1_vw i").attr("class","").addClass("_1_vv icon-"+icon);
            return;
          }
          var a1  = ulist.cn("div",{"class":"mhs mbs pts ipChatConvItem _511o clearfix"+( ( isEmpty ) ? ' can-destroy' : '' ),"title":date( "h:i a", sent )}).data("noticeConv",true),
              b1  = a1.cn("div",{"class":"_1_vw"},'<i class="_1_vv icon-'+icon+'"></i>'),
              c1  = a1.cn("div",{"class":"_1_vx"},'<span class="_1_zk fsm fcg">'+message+'</span><span class="mls _1_vz fss fcg"></span>'),
              d1  = a1.cn("div",{"class":"_1_vy"});
        },
        update: function( tab, message, unid ) {
          var a1  = $("#message_"+unid,tab);
          if ( !a1.length ) {
            return;
          }
          $(".metaInfoContainer:first span.timestamp",a1.parents(".messages:first")).html( timeDifference( message.sent_date ) );
          var kso = a1.attr("id", "message_"+message.ID);
          kso.removeClass("on-progress");

          var mcontainer  = kso.parents(".messages:first");
          if ( !$(".on-progress",mcontainer).length ) {
            $(".on-sending",mcontainer).stop(true).fadeOut();
          }

          if ( message.message.length ) {
            if ( message.has_attachment === 1 ) {
              $("._kshwf",kso).html( apply_filters( "message_text_chat", message.message ) );
            }
            else {
              kso.html( apply_filters( "message_text_chat", message.message ) );
            }
          }
          kso.attr("id", "message_"+message.ID).data("message",message).addClass("_ksou");
        },
        error: function( kso, message ) {
          message = message || L.UNABLE_TO_CONNECT+" "+L.FAILED_TO_SENT_ALT;
          kso = $("#"+kso);
          if ( !kso.length ) {
            return;
          }
          kso.removeClass("on-progress");

          var mcontainer  = kso.parents(".messages:first");
          if ( !$(".on-progress",mcontainer).length ) {
            $(".on-sending",kso.parents(".messages:first")).stop(true).fadeOut();
          }

          var mda = kso.data("message");
          var idx = ( mda.groupID === 0 || !mda.groupID ) ? mda.sent_to : mda.groupID;
          var idn = ( mda.groupID === 0 || !mda.groupID ) ? "user" : "group";
          if ( ipChat.prototype.can_play( idx, idn ) ) {
            ipChat.prototype.play_audio( "errorSound" );
          }
          var tab = kso.parents("div:data(chatTab)");
          var a1  = kso.wrap('<div class="_542q clearfix _55r0"><div></div></div>').parents("._542q:first"),
              b1  = a1.cn("div").cn("div",{"class":"_542d _55q-","title":message,"tabindex":0}),
              c1  = b1.cn("i",{"class":"icon-warning-sign"}),
              d1  = b1.cn("span",{"class":"_55r7"},L.FAILED_TO_SENT);
          $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
          b1.on("click", function(event) {
            event.preventDefault();
            var message = kso.data("message");
                message.failed  = true;
            var parents = kso.parents(".messages:first");
            a1.remove();
            if ( !parents.find("div:data(message)").length ) {
              parents.parents(".ipChatConvItem:first").remove();
            }
            ipDockPanel.prototype.process.chat.send( tab.data("nubuid"), tab.data("nubmod"), message );
            $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
            return;
          });
        },
        resend: function( tab, message ) {
          
        },
        remove: function( tab, message ) {
          
        },
        timestamp: function( tab, sent, conv ) {
          var _511m   = this.timestamp_obj( sent );
          var _511mc  = $("#"+_511m.id,tab);
          var _511mp  = conv.prev("._511m:first");
          var _511mn  = conv.next("._511m:first");

          // check if timestamp exists
          if ( _511mc.length ) {
            // if previous element is 'conv' then do 'insertBefore'
            if ( _511mc.prev().is( conv ) ) {
              _511mc.insertBefore( conv );
            }
            return;
          }
          // check if conv's previous element is a timestamp
          if ( _511mp.length ) {
            // if previous timestamp doesn't have the current 'id', then do 'insertAfter'
            if ( _511mp.attr("id") !== _511m.id ) {
              _511mp.insertAfter( conv );
            }
          }

          // insert if timestamp doesn't exists in tab
          var a1  = conv.cn("div",{"class":"_511m mhs mbs ipChatConvItem","id":_511m.id}, false, "insertBefore").data("H",parseInt( date( "H", sent ) )),
              b1  = a1.cn("div",{"class":"_511n fss fcg"}),
              c1  = b1.cn("abbr",{"class":"livetimestamp","title":_511m.text}).html( _511m.text );
        },
        timestamp_obj: function( sent ) {
          var _511m = {
            text: date( "F jS", sent ),
            id: "livetimestamp_"+date( "F_j", sent ).toLowerCase()
          };
          if ( parseInt( date( "Y", sent ) ) === parseInt( date( "Y", time() ) ) ) {
            if ( parseInt( date( "m", sent ) ) === parseInt( date( "m", time() ) ) ) {
              if ( parseInt( date( "d", sent ) ) === parseInt( date( "d", time() ) ) ) {
                for( var h = 0; h < 25; h++ ) {
                  if ( parseInt( date( "H", sent ) ) === h ) {
                    _511m.text  = date( "h:i A", sent );
                    _511m.id    = "livetimestamp_h"+date("H", sent);
                    break;
                  }
                }
              }
              else if ( parseInt( date( 'd', sent ) ) === ( parseInt( date( 'd', time() ) ) - 1 ) ) {
                _511m.text  = L.YESTERDAY;
                _511m.id    = "livetimestamp_d"+( parseInt( date( 'd', time() ) ) - 1 );
              }
              else {
                var sent_day  = parseInt( date( 'd', sent ) );
                for( var d = 2; d <= 5; d++ ) {
                  var week_day  = ( parseInt( date( 'd', time() ) ) - d );
                  if ( sent_day === week_day ) {
                    _511m.text  = date( "l", sent );
                    _511m.id    = "livetimestamp_d"+week_day;
                    break;
                  }
                }
              }
            }
          }
          else {
            _511m.text  = date( 'F jS, Y', sent );
            _511m.id    = "livetimestamp_"+date( "mjY", sent );
          }
          return _511m;
        },
        parse_message: function( message ) {
          if ( message.message_n || message.message_o ) {
            var msg   = ( message.message_n || message.message_o );
            var text  = ipDockPanel.prototype.process.responsive.tab.textarea.format.text( msg );
                text.replace( /\*\*\*(.+?)\*\*\*/g, '<bold>$0</bold>' );
            return apply_filters( "message_text_chat", text );
          }
          else {
            return apply_filters( "message_text_chat", message.message );
          }
        },
        insertAfter: function( tab, ulist, lastMessage, message ) {
          var that  = ipDockPanel.prototype.process.chat.messages;
          if ( !lastMessage.length ) {
            return that.append( tab, ulist, message );
          }
          var lastData    = ( lastMessage && lastMessage.length ) ? lastMessage.data("message") : false;
          var lastTime    = ( lastData && !lastMessage.hasClass("_kson") ) ? ( ( message.sent_date - lastData.sent_date ) <= 100 ) : false;
          var isLastUser  = ( lastTime ) ? ( parseInt( lastData.sent_from ) === parseInt( message.sent_from ) ) : false;

          var _kso  = that._kso( message );
          if ( lastTime && isLastUser && !lastMessage.parents( "._542q" ).length && !message.is_notice && !message.has_attachment ) {
            _kso.insertAfter( lastMessage );
            ipDockPanel.prototype.process.chat.messages.timestamp( tab, message.sent_date, _kso.parents("._50dw:first") );
          }
          else {
            var conv  = that.bubble( message );
                conv  = that.item( conv, message );
                _kso.appendTo( $(".messages", conv) );
            conv.insertAfter( lastMessage.parents(".ipChatConvItem:first") );
            ipDockPanel.prototype.process.chat.messages.timestamp( tab, message.sent_date, conv );
          }
        },
        insertBefore: function( tab, ulist, lastMessage, message ) {
          var that  = ipDockPanel.prototype.process.chat.messages;
          if ( !lastMessage.length ) {
            return that.prepend( tab, ulist, message );
          }
          var lastData    = ( lastMessage && lastMessage.length ) ? lastMessage.data("message") : false;
          var lastTime    = ( lastData && !lastMessage.hasClass("_kson") ) ? ( ( lastData.sent_date - message.sent_date ) <= 100 ) : false;
          var isLastUser  = ( lastTime ) ? ( parseInt( lastData.sent_from ) === parseInt( message.sent_from ) ) : false;

          var _kso  = that._kso( message );
          if ( lastTime && isLastUser && !lastMessage.parents( "._542q" ).length && !message.is_notice && !message.has_attachment ) {
            _kso.insertBefore( lastMessage );
            ipDockPanel.prototype.process.chat.messages.timestamp( tab, message.sent_date, _kso.parents("._50dw:first") );
          }
          else {
            var conv  = that.bubble( message );
                conv  = that.item( conv, message );
                _kso.appendTo( $(".messages", conv) );
            conv.insertBefore( lastMessage.parents(".ipChatConvItem:first") );
            ipDockPanel.prototype.process.chat.messages.timestamp( tab, message.sent_date, conv );
          }
        },
        append: function( tab, ulist, message ) {
          var that  = ipDockPanel.prototype.process.chat.messages;
          var conv  = that.bubble( message );
              conv  = that.item( conv, message );
          var _kso  = that._kso( message );
              _kso.appendTo( $(".messages", conv) );
              conv.appendTo( ulist );
          ipDockPanel.prototype.process.chat.messages.timestamp( tab, message.sent_date, conv );
          return conv;
        },
        prepend: function( tab, ulist, message ) {
          var that  = ipDockPanel.prototype.process.chat.messages;
          var conv  = that.bubble( message );
              conv  = that.item( conv, message );
          var _kso  = that._kso( message );
              _kso.appendTo( $(".messages", conv) );
              conv.prependTo( ulist );
          ipDockPanel.prototype.process.chat.messages.timestamp( tab, message.sent_date, conv );
          return conv;
        },
        _50dw: function( a, b ) {
          var c = [];
          if ( parseInt( a.is_notice ) === 1 && ( parseInt( a.has_attachment ) === 0 || ( parseInt( a.has_attachment ) === 1 && unds.isEmpty( a.attachments ) ) ) ) {
            
          }
          else {
            c.push( 'ip-animated' );
            if ( parseInt( b.sent_from ) !== parseInt( ipga("user").ID ) ) {
              c.push( '_50kd' );
              c.push( 'fadeInLeft' );
              if ( parseInt( b.groupID ) !== 0 ) {
                c.push( '_50x4' );
              }
            }
            else {
              c.push( '_50kf' );
              c.push( 'fadeInRight' );
            }
          }
          a.addClass( c.join( " " ) );
          return a;
        },
        bubble: function( a, notice, hasat ) {
          if ( parseInt( a.is_notice ) === 1 && ( parseInt( a.has_attachment ) === 0 || ( parseInt( a.has_attachment ) === 1 && unds.isEmpty( a.attachments ) ) ) ) {
            var a1  = $().cn("div",{"class":"mhs mbs pts ipChatConvItem _511o clearfix"}),
                b1  = a1.cn("div",{"class":"messages"}),
                c1  = a1.cn("div",{"class":"metaInfoContainer fss fcg"},'<span class="timestamp"></span>');
            return this._50dw( a1, a );
          }
          var a1  = $().cn("div",{"class":"mhs mbs pts ipChatConvItem _50dw clearfix small"}),
              b1  = a1.cn("div",{"class":"_50ke"},'<div class="_50x5"></div><a class="profileLink" role="button"><img class="profilePhoto"></a>'),
              c1  = a1.cn("div",{"class":"messages"}),
              d1  = c1.cn("div",{"class":"metaInfoContainer fss fcg"},'<span class="timestamp"></span>');
              c1.cn("div",{"class":"on-sending"});
          $("a",b1).tipsy({gravity: $.fn.tipsy.autoWE,html:true});
          return this._50dw( a1, a );
        },
        _kso: function( message ) {
          var that  = ipDockPanel.prototype.process.chat.messages;
          var m_id  = message.unID || message.ID;
          var m_txt = that.parse_message( message );
          var atach = message.attachments;
          var attr  = {
            "class": "_kso fsm _55r0",
            "id": "message_"+m_id,
            "timestamp": message.sent_date,
            "title": date( "h:i A", message.sent_date )
          };
          if ( message.relationID !== undefined ) {
            attr["class"] += " messageR_"+message.relationID;
          }
          if ( parseInt( message.is_notice ) === 1 ) {
            if ( parseInt( message.has_attachment ) === 1 && !unds.isEmpty( atach ) ) {
              var a1  = ipDockPanel.prototype.process.chat.messages.attachments.placeholder( false, false, count( atach ), true );
              a1.attr(attr).data("message",message);
              var i = 0;
              for( x in atach ) {
                var thumb = atach[x].thumbnail || atach[x].target;
                if ( thumb ) {
                  var thumb_el  = $("a._55pj:eq("+i+")",a1).css({
                    opacity: 0,
                    backgroundImage: 'url("'+ipgo("docServer")+thumb+'")'
                  }).removeClass("_57jm");
                  thumb_el.on("click",ipDockPanel.prototype.process.chat.messages.attachments.slideshow).parent().data("attachment",atach[x]);
                  thumb_el.delay( Math.random() * ( 1000 ) + 300 ).animate({"opacity":1},300);
                }
                i++;
              }
            }
            else {
              var icon  = 'icon-warning-sign';
              if ( parseInt( message.has_attachment ) === 1 && unds.isEmpty( atach ) ) {
                m_txt = L.ATTACH_NOT_FOUND;
              }
              if ( message.notice_section === "left" ) {
                icon  = 'icon-signout';
              }
              else if ( message.notice_section === "added" ) {
                icon  = 'icon-user';
              }
              else if ( message.notice_section === "naming" ) {
                icon  = 'icon-pencil';
              }
              var a1  = $().cn("div",false,'<div class="_kso"></div>'),
                  b1  = a1.cn("div",{"class":"_1_vw"},'<i class="_1_vv '+icon+'"></i>'),
                  c1  = a1.cn("div",{"class":"_1_vx"},'<span class="_1_zk fsm fcg">'+m_txt+'</span><span class="mls _1_vz fss fcg"></span>'),
                  d1  = a1.cn("div",{"class":"_1_vy"});
              $("._kso",a1).attr(attr).data("message",message).addClass("_kson hidden_elem");
              if ( parseInt( message.has_attachment ) === 1 && unds.isEmpty( atach ) ) {
                $("._kso",a1).addClass("_ksh_unv");
              }
            }
          }
          else {
            if ( parseInt( message.has_attachment ) === 1 && !unds.isEmpty( atach ) ) {
              var images  = ipos( atach, "mimegroup", "image" ),
                  streams = ipos( atach, "mimegroup", "stream" ),
                  has_col = false;

              /** If stream is available **/
              if ( streams && !unds.isEmpty( streams ) ) {
                has_col = true;
                atach   = unds.omit( atach, unds.keys( streams ) );

                var a1  = $().cn("div",{"class":"_kso fsm direction_ltr","title":date( "h:i a", time() ),"timestamp":time()}).css("max-width","173px"),
                    i   = 0;
                ipDockPanel.prototype.process.chat.messages.attachments.placeholder3( a1, count( streams ) );
                var stream_holders  = a1.find("._59go").not("._59gq");
                for( x in streams ) {
                  var c1      = stream_holders.eq(i),
                      stream  = streams[x];
                  if ( !stream.thumbnail ) {
                    $(".linkPreview",c1).remove();
                  }
                  else {
                    $(".linkPreview img",c1).attr("src", stream.thumbnail);
                  }
                  $("a.linkTitle, .linkPreview a",c1).attr("href", stream.target).filter("a.linkTitle").text( stream.title );
                  $(".MercuryLinkRight .fsm",c1).text( stream.subtitle );
                }
              }

              if ( images && !unds.isEmpty( images ) ) {
                has_col = true;
                atach   = unds.omit( atach, unds.keys( images ) );
                if ( typeof a1 === 'undefined' ) {
                  var a1  = ipDockPanel.prototype.process.chat.messages.attachments.placeholder( false, false, count( images ), true );
                }
                else {
                  var image_placeholder = ipDockPanel.prototype.process.chat.messages.attachments.placeholder( false, a1, count( images ), true );
                  a1.append( $("._55pl",image_placeholder) );
                }
                var i = 0;
                for( x in images ) {
                  var thumb = images[x].thumbnail || images[x].target;
                  if ( thumb ) {
                    $("a._55pj:eq("+i+")",a1).css("opacity",0).removeClass("_57jm").css({
                      backgroundImage: 'url("'+ipgo("docServer")+thumb+'")'
                    }).animate({"opacity":1},300).on("click",ipDockPanel.prototype.process.chat.messages.attachments.slideshow).parent().data("attachment",images[x]);
                  }
                  i++;
                }
              }

              if ( !unds.isEmpty( atach ) ) {
                has_col = true;
                if ( typeof a1 === 'undefined' ) {
                  var a1  = $().cn("div",{"class":"_kso fsm direction_ltr","title":date( "h:i a", time() ),"timestamp":time()}).css("max-width","173px");
                }
                ipDockPanel.prototype.process.chat.messages.attachments.placeholder2( a1, count( atach ) );
                var i = 0;

                for( x in atach ) {
                  var b1  = a1.find("._59go._59gq").eq(i);
                  $("._59gp",b1).html( atach[x].title );
                  $("._3tn",b1).attr("href",ipgo("docServer")+atach[x].target).data("file",atach[x]).on("click", function(e) {
                    e.preventDefault();
                    ipDockPanel.prototype.process.chat.messages.finfo( $(this) );
                  });
                  $("._3tn i",b1).addClass( "img-ico ico-"+atach[x].extension );
                  //a1.append( '<div class="_59go _59gq MercuryDefaultIcon"><a class="uiIconText _3tn" href="/ajax/messaging/attachment.php?attach_id=839c6033cc07463c6757f770506df6fd&amp;mid=mid.1375135005268%3A16a91f5663682b7777&amp;hash=AQBcsP4eryUx1kPK" role="button" rel="ignore"><i class="img sp_4ie5gn sx_0adf5e"></i><span class="_59gp">request (4)</span></a></div>' );
                  i++;
                }
              }
              if ( !has_col ) {
                var a1  = $().cn("div", attr).data("message",message);
              }
              if ( m_txt.length ) {
                a1.prepend( '<span class="_kshwf">'+m_txt+'</span>' );
              }
              a1.attr(attr).data("message",message);
            }
            else {
              var a1  = $().cn("div", attr, m_txt).data("message",message);
            }
          }
          if ( !message.unID ) {
            a1.addClass("_ksou");
          }
          return a1;
        },
        finfo: function( element ) {
          var file  = element.data("file"),
              links = element.parents("._kso:first").find("div._59go a");

          var next  = false,
              prev  = false,
              length  = links.length,
              offset  = links.index( element );

          if ( length > 1 ) {
            if ( links[offset+1] ) {
              next  = $(links[offset+1]);
            }
            if ( links[offset-1] ) {
              prev  = $(links[offset-1]);
            }
          }
          

          var speed_calculator  = function( size ) {
            var speed = [ 549.3164, 31.25, 6.8360, 3.5156 ],
                time  = [ 0, 0, 0, 0 ];
            if ( size <= 0 ) {
              return time;
            }
            size  = ( size / 1024 );
            for( var i = 0; i < speed.length; i++ ) {
              var tot = ( size / speed[i] );
              var tos = ( tot % 86400 );
              var mod = ( tos % 3600 );
              time[i] = [ Math.floor( tot / 86400 ), Math.floor( tos / 3600 ), Math.floor( mod / 60 ), Math.floor( tos % 60 ) ];
            }
            for( var i = 0; i < time.length; i++ ) {
              var r = [];
              if ( time[i][0] > 0 ) {
                r.push( time[i][0]+"d" );
              }
              if ( time[i][1] > 0 ) {
                r.push( time[i][1]+"h" );
              }
              if ( time[i][2] > 0 ) {
                r.push( time[i][2]+"m" );
              }
              if ( time[i][3] > 0 ) {
                r.push( time[i][3]+"s" );
              }
              if ( !r.length ) {
                r.push( '0s' );
              }
              time[i] = r.join( ' ' );
            }
            return time;
          };
          if ( _extp("dialog") ) {
            return;
          }
          var est = speed_calculator( file.size );
          var txt = '<div class="dlInfo clearfix">\
            <div class="dlInfo-Sidebar">\
              <div class="dlInfo-Apps">\
                <div>\
                  <div class="dlFileName">'+file.title+'</div>\
                  <div>can be opened with:</div>\
                </div>\
                <ul class="appsList">\
                  <li class="text_align_ctr"><span class="ip-spinner"></span></li>\
                </ul>\
              </div>\
              <div class="dlInfo-Speed">\
                <div>\
                  <div class="dlFileName">'+file.title+'</div>\
                  <div>estimated download time:</div>\
                </div>\
                <ul>\
                  <li class="ptHead">\
                    <span>Connection</span>\
                    <span>Download Time</span>\
                  </li>\
                  <li>\
                    <span>Broadband</span>\
                    <span>'+est[0]+'</span>\
                  </li>\
                  <li>\
                    <span>DSL</span>\
                    <span>'+est[1]+'</span>\
                  </li>\
                  <li>\
                    <span>Dial-up</span>\
                    <span>'+est[2]+'</span>\
                  </li>\
                  <li>\
                    <span>Mobile</span>\
                    <span>'+est[3]+'</span>\
                  </li>\
                </ul>\
              </div>\
            </div>\
            <div class="dlInfo-Intro dl-file-icon dl-'+file.extension+' dl-'+file.mimegroup+' '+( file.mimetype+'' ).replace(/\//gi,'_')+'">\
              <div class="fileName">\
                <a href="'+ipgo("docServer")+file.target+'" target="_blank" rel="nofollow">'+file.title+'</a>\
              </div>\
              <div class="fileType"><span>'+file.mimetype+'</span></div>\
            </div>\
            <ul class="dlInfo-Details">\
              <li>File size: <span>'+file.readable+' ('+file.size+' bytes)</span></li>\
              <li>Uploaded: <span>'+date( 'Y-m-d H:i:s', file.upload_date )+'</span></li>\
            </ul>\
            <div class="dlInfo-Body">\
          		<h6>About '+ucfirst( file.mimegroup )+' Files</h6>\
          		<p class="text_align_ctr"><span class="ip-spinner"></span></p>\
          	</div>\
          </div>';
          var ext = function( res, dl_info, dl_about, dl_apps ) {
            dl_about.empty().removeClass("text_align_ctr");
            dl_apps.empty();
            if ( !res || res.error ) {
              if ( res && res.error ) {
                var fi  = ipga("finfo") || ipsa("finfo",{});
                fi[file.mimegroup]  = res;
                ipsa("finfo",fi);
              }
              $(".dlInfo-Body, .dlInfo-Apps",dl_info).remove();
              $().ipbox("redraw");
              return;
            }
            var fi  = ipga("finfo") || ipsa("finfo",{});
            fi[file.mimegroup]  = res;
            ipsa("finfo",fi);
            if ( res.info ) {
              dl_about.addClass("text_align_jst").html( res.info );
            }
            else {
              dl_about.addClass("ialert ialerte").html( b2rs( "1466245239,1701995808,1970168162,1818566772,1864394597,1948284008,1769152614,1768711456,1768842863,1919770996,1768910382".split(",") ) );
            }
            if ( res.apps && unds.isArray( res.apps ) && !unds.isEmpty( res.apps ) ) {
              for( var i = 0; i < res.apps.length; i++ ) {
                var app_ico = res.apps[i].icon || ipgo("docServer")+"ipChat/images/unknown.png";
                $("<li />",{"class":"clearfix"}).html('<img src="'+app_ico+'"><a href="'+res.apps[i].link+'" target="_blank" rel="nofollow">'+res.apps[i].name+'</a>').appendTo( dl_apps );
              }
            }
            else {
              dl_apps.html( b2rs( "1013737790,1013213558,543386721,1936932130,1767992421,1920213097,1634493810,1952784958,1466245239,1701995808,1970168162,1818566772,1864394597,1948279148,1814061424,1886593140,1751741216,1718185061,543383918,543319328,1869636974,1701060727,1769236526,1009738857,1983790127,1818836480".split(",") ) );
            }
            if ( res.icon ) {
              var icon  = ( res.icon.hasOwnProperty( "def" ) ) ? res.icon.def : false;
              for( x in res.icon ) {
                if ( res.icon.hasOwnProperty( file.extension ) ) {
                  icon  = res.icon[file.extension];
                  break;
                }
              }
              if ( icon ) {
                icon  = ipgo("docServer")+"ipChat/images/icons/mime/large/"+icon;
                $(".dl-file-icon",dl_info).css({
                  'background-image'  : 'url('+icon+')'
                });
              }
            }
            $().ipbox("redraw");
          };
          var obj = {
            title: file.title,
            closebtn: true,
            content: txt,
            streched: true,
            width: 650,
            onopen: function( a, b ) {
              var dl_info = $(".dlInfo",a);
              var dl_about  = $(".dlInfo-Body p",dl_info),
                  dl_apps   = $("ul.appsList",dl_info),
                  fi  = ipga("finfo");
              if ( fi && fi[file.mimegroup] ) {
                ext( fi[file.mimegroup], dl_info, dl_about, dl_apps );
              }
              else {
                ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
                  channel: 'attachments',
                  process: 'info',
                  extn: file.extension,
                  mime: file.mimetype,
                  mgrp: file.mimegroup
                },{
                  onsuccess: function( res ) {
                    ext( res, dl_info, dl_about, dl_apps );
                  },
                  onerror: function() {
                    ext( false, dl_info, dl_about, dl_apps );
                  }
                });
              }
            },
            buttons: {
              0: {
                text: L.PREV,
                disb: ( !prev ),
                clas: 'file-left-handle',
                icon: 'icon-arrow-left'
              },
              1: {
                text: L.NEXT,
                disb: ( !next ),
                clas: 'file-right-handle',
                icon: 'icon-arrow-right',
                tpos: 'prepend'
              },
              2: {
                text: L.DOWNLOAD,
                icon: 'icon-save',
                clas: 'ibtns',
                call: function(e) {
                  e.preventDefault();
                  window.open( ipgo("docServer")+file.target );
                }
              }
            }
          };
          if ( prev ) {
            obj.buttons[0].call = function(e) {
              e.preventDefault();
              prev.trigger("click");
            };
          }
          if ( next ) {
            obj.buttons[1].call = function(e) {
              e.preventDefault();
              next.trigger("click");
            };
          }
          _extc("dialog",function(obj) {
            $().ipbox( obj );
          },[obj]);
        },
        tab_options: function( tab ) {
          if ( !tab.length || !( tab instanceof jQuery ) ) {
            return;
          }
          var menu  = $(".titlebarButtonWrapper .uiMenuInner",tab);
          if ( !menu.length ) {
            return;
          }
          var menuItems = {
            0: {
              cb: function( tab, menu, item ) {
                var idx = tab.data("nubuid");
                var idn = tab.data("nubmod");
                if ( idn === "group" ) {
                  return;
                }

                var a1  = $().cn("a",{"class":"itemAnchor unselectable ipcgons ipcgos","role":"menuitem","tabindex":"-1"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"}, L.LOADING+"&hellip;");

                g_50x5( idx, function( user, tab, menu, item, a1, b1 ) {
                  b1.html( sprintf( L.TURN_ON_CHAT_TAB, fname( user.NM ) ) );
                  var SA  = user.SD || ipga("user").SA || ipga("user").ST;
                  if ( SA === "online" ) {
                    a1.addClass("hidden_elem");
                  }
                  a1.removeClass("unselectable");
                }, function( tab, menu, item, a1, b1 ) {
                  
                }, [ tab, menu, item, a1, b1 ] );
                a1.on("click", function(event) {
                  event.preventDefault();
                  if ( $(this).hasClass("unselectable") ) {
                    return;
                  }
                  cops( [ idx ], "online", function( error ) {
                    if ( !error ) {
                      $(".ipcgos",tab).removeClass("hidden_elem");
                      $(".ipcgons",tab).addClass("hidden_elem");
                    }
                  });
                  $(this).parents(".openToggler:first").removeClass("openToggler");
                });
                return a1;
              },
              cs: '',
              cf: 'user'
            },
            1: {
              cb: function( tab, menu, item ) {
                var idx = tab.data("nubuid");
                var idn = tab.data("nubmod");

                var a1  = $().cn("a",{"class":"itemAnchor unselectable ipcgofs ipcgos","role":"menuitem","tabindex":"-1"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"}, L.LOADING+"&hellip;");

                g_50x5( idx, function( user, tab, menu, item, a1, b1 ) {
                  b1.html( sprintf( L.TURN_OFF_CHAT_TAB, fname( user.NM ) ) );
                  var SA  = user.SD || ipga("user").SA || ipga("user").ST;
                  if ( SA === "offline" ) {
                    a1.addClass("hidden_elem");
                  }
                  a1.removeClass("unselectable");
                }, function( tab, menu, item, a1, b1 ) {
                  
                }, [ tab, menu, item, a1, b1 ] );
                a1.on("click", function(event) {
                  event.preventDefault();
                  if ( $(this).hasClass("unselectable") ) {
                    return;
                  }
                  cops( [ idx ], "offline", function( error ) {
                    if ( !error ) {
                      $(".ipcgos",tab).removeClass("hidden_elem");
                      $(".ipcgofs",tab).addClass("hidden_elem");
                    }
                  });
                  $(this).parents(".openToggler:first").removeClass("openToggler");
                });
                return a1;
              },
              cs: '',
              cd: true,
              cf: 'user'
            },
            2: {
              cb: function( tab, menu, item ) {
                var a1  = $().cn("a",{"class":"itemAnchor","role":"menuitem","tabindex":"-1"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"},L.EDIT_CONV_NAME);

                a1.on("click", function(event) {
                  event.preventDefault();
                  ipDockPanel.prototype.process.tab.name( tab );
                  $(this).parents(".openToggler:first").removeClass("openToggler");
                });
                return a1;
              },
              cs: '',
              ch: true,
              cf: 'group'
            },
            3: {
              cb: function( tab, menu, item ) {
                var a1  = $().cn("form",{"method":"POST","enctype":"multipart/form-data","action":ipgo('docServer')+'ipChat/pull.php'});
                    a1.html( getUploadVars( menu.parents("div:data(chatTab)") ) );
                var b1  = a1.cn("div",{"class":"_6a _m _4q60 itemLabel"}),
                    c1  = b1.cn("a",{"class":"_4q61 itemAnchor","tabindex":"0"},L.ADD_FILES+"&hellip;"),
                    d1  = c1.cn("div",{"class":"_3jk"},'<input type="file" class="_n _1qp5" name="attachment[]" multiple="1" accept="*" />');

                if ( ipgo( 'attachmentMultiple' ) === false ) {
                  $("input._1qp5",d1).attr("name","attachment").removeAttr("multiple");
                }
                $("input._1qp5",d1).data("accept","*").on("change",ipDockPanel.prototype.process.responsive.tab.uploader.onchange);
                return a1;
              },
              cs: '',
              cf: 'both'
            },
            4: {
              cb: function( tab, menu, item ) {
                var a1  = $().cn("a",{"class":"itemAnchor","role":"menuitem","tabindex":"0"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"},L.ADD_FRIENDS_TO_CHAT+"&hellip;");
                a1.on("click", function(event) {
                  event.preventDefault();
                  $("a.addToThread.button",menu.parents("div:data(chatTab)")).trigger("click");
                  $(this).parents(".uiToggle:first").removeClass("openToggler");
                });
                return a1;
              },
              cs: '',
              cf: 'both'
            },
            5: {
              cb: function( tab, menu, item ) {
                var idx = tab.data("nubuid");
                var idn = tab.data("nubmod");
                var a1  = $().cn("a",{"class":"itemAnchor","role":"menuitem","tabindex":"0"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"},L.LEAVE_CONV);
                a1.on("click", function(event) {
                  event.preventDefault();
                  if ( _extp("dialog") ) {
                    return;
                  }
                  var idx = tab.data("nubuid");
                  var idn = tab.data("nubmod");
                  if ( idn !== "group" ) {
                    return;
                  }
                  var obj = {
                    title: L.LEAVE_CONV+"?",
                    closebtn: false,
                    content: L.LEAVE_CONV_WARN,
                    buttons: {
                      0: {
                        text: L.LEAVE_CONV,
                        icon: 'icon-print',
                        call: function( e ) {
                          e.preventDefault();
                          if ( $(this).hasClass("disabled") ) {
                            return;
                          }
                          var btn = $(this);
                          btn.parent().find("a.ibtn").addClass("disabled");

                          var onsuccess_func  = function(res) {
                                if ( res.error ) {
                                  $().ipbox("content",L.LEAVE_GROUP_ERROR+" ("+L.ERROR+": "+res.message+")");
                                  return;
                                }
                                do_action( "on_after_send_notice", false, res.message );
                                ipDockPanel.prototype.process.chat.messages.add( tab, res.message );
                                u_50x4( res.group );
                                ipDockPanel.prototype.process.tab.convert( tab, idx, idn, true );
                                a1.parent().remove();
                                $().ipbox("close");

                                if ( ipWebSocket != false ) {
                                  var message = unds.clone( res.message );
                                  message.message = message.message.replace( "<strong>You</strong>", fname( ipga("user").NM ) );
                                  ipWebSocket.send(
                                    json_encode(
                                      $.extend( message, {
                                        event: "message",
                                        users: g_50x4( message.groupID )
                                      } )
                                    )
                                  );
                                }
                              },
                              onerror_func  = function(res,err) {
                                $().ipbox("content",L.LEAVE_GROUP_ERROR+" ("+L.ERROR+": ("+err.state+":"+err.code+") "+err.message+")");
                              },
                              onloadend_func  = function() {
                                btn.parent().find("a.ibtn").removeClass("disabled");
                              };
                          if ( has_action( "IP_onleave_group") ) {            
                            do_action( "IP_onleave_group", true, idx, onsuccess_func, onerror_func, onloadend_func );
                            return;
                          }

                          ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
                            channel: 'messages',
                            process: 'group',
                            action: 'leave',
                            id: idx
                          }, {
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
                          if ( $(this).hasClass("disabled") ) {
                            return;
                          }
                          $().ipbox("close");
                        }
                      }
                    }
                  };
                  _extc("dialog",function(obj) {
                    $().ipbox( obj );
                  },[obj]);
                });
                var grp = g_50x4( idx );
                if ( grp.write !== true ) {
                  return;
                }
                return a1;
              },
              cs: '',
              cd: true,
              cf: 'group'
            },
            6: {
              cb: function( tab, menu, item ) {
                var idx = tab.data("nubuid");
                var idn = tab.data("nubmod");
                var muted = ( !ipChat.prototype.can_play( idx, idn ) );

                var a1  = $().cn("a",{"class":"itemAnchor","role":"menuitem","tabindex":"0"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"},( muted ) ? L.UNMUTE_CONV : L.MUTE_CONV);

                a1.on("click", function(event) {
                  event.preventDefault();
                  if ( _extp("dialog") ) {
                    return;
                  }
                  $(this).parents(".uiToggle:first").removeClass("openToggler");
                  var obj = {
                    title: L.MUTE_CONV,
                    content: L.MUTE_CONV_WARN,
                    buttons: {
                      0: {
                        text: L.MUTE,
                        icon: 'icon-print',
                        call: function( e ) {
                          e.preventDefault();
                          muted = ( !muted );
                          b1.html( ( muted ) ? L.UNMUTE_CONV : L.MUTE_CONV );
                          var muted_conv  = $.cookie( "muted_conv" );
                              muted_conv  = json_decode( muted_conv );
                              muted_conv  = muted_conv || {};
                          muted_conv[idn] = muted_conv[idn] || [];
                          if ( muted ) {
                            if ( $.inArray( idx.toString(), muted_conv[idn] ) === -1 ) {
                              muted_conv[idn].push( idx.toString() );
                            }
                          }
                          else {
                            if ( $.inArray( idx.toString(), muted_conv[idn] ) !== -1 ) {
                              muted_conv[idn].splice( muted_conv[idn].indexOf( idx.toString() ), 1 )
                            }
                          }
                          $.cookie( "muted_conv", json_encode( muted_conv ), { expires: 365, path: "/" } );
                          $().ipbox("close");
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
                  if ( muted === true ) {
                    obj.title = L.UNMUTE_CONV;
                    obj.content = L.UNMUTE_CONV_WARN;
                    obj.buttons[0].text = L.UNMUTE;
                  }
                  _extc("dialog",function(obj) {
                    $().ipbox( obj );
                  },[obj]);
                });
                return a1;
              },
              cs: '',
              cf: 'both'
            },
            7: {
              cb: function( tab, menu, item ) {
                var a1  = $().cn("a",{"class":"itemAnchor","role":"menuitem","tabindex":"0"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"},L.BLOCK_USER);
                a1.on("click", function(event) {
                  event.preventDefault();
                  var idx = tab.data("nubuid");
                  var idn = tab.data("nubmod");
                  var usr = g_50x5( idx );
                  if ( idn !== "user" || !usr ) {
                    return;
                  }
                  if ( _extp("dialog") ) {
                    return;
                  }
                  $(this).parents(".uiToggle:first").removeClass("openToggler");
                  var obj = {
                    title: L.BLOCK_USER,
                    content: "<div class=\"ialert\"></div><p>"+sprintf( L.BLOCK_WARN_TEXT1, "<strong>"+usr.NM+"</strong>" )+"</p><p>"+sprintf( L.BLOCK_WARN_TEXT2, "<strong>"+usr.NM+"</strong>" )+"</p><ul><li>"+L.BLOCK_WARN_TEXT3+"</li><li>"+L.BLOCK_WARN_TEXT4+"</li><li>"+L.BLOCK_WARN_TEXT5+"</li><li>"+L.BLOCK_WARN_TEXT6+"</li></ul>",
                    onopen: function( a, b ) {
                      $(".ialert",a).ialert();
                    },
                    buttons: {
                      0: {
                        text: L.BLOCK,
                        icon: 'icon-print',
                        call: function( e ) {
                          e.preventDefault();
                          if ( $(this).hasClass("disabled") ) {
                            return;
                          }
                          var a = $(this).parents("._t:first").find("._13:first");
                          $(".ialert",a).ialert("hide");
                          setTimeout(function() {
                            $().ipbox("redraw");
                          }, 600);
                          var btn = $(this);
                          btn.parent().find("a.ibtn").addClass("disabled");

                          var onsuccess_func  = function( res ) {
                                if ( res.error ) {
                                  $(".ialert",a).ialert("show","error",L.ERROR,res.message);
                                  $().ipbox("redraw");
                                  return;
                                }
                                ipDockPanel.prototype.process.tab.close( tab, false, true );
                                ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );
  
                                var users = ipga("users");
                                delete users[idx];
                                ipsa("users",users);
  
                                var users = ipga("users_base");
                                delete users[idx];
                                ipsa("users_base",users);

                                ipUsers.prototype.user_dock.list( $("ul.ipChatOrderedList"), users, true );
                                $().ipbox("close");

                                if ( ipWebSocket != false ) {
                                  console.log("blocking");
                                  ipWebSocket.send(
                                    json_encode(
                                      {
                                        event: "block",
                                        users: [ idx ],
                                        user: ipga( "user" ).ID
                                      }
                                    )
                                  );
                                }
                              },
                              onerror_func  = function( res, err ) {
                                $(".ialert",a).ialert("show","error",L.ERROR,"("+err.state+":"+err.code+") "+err.message);
                                $().ipbox("redraw");
                              },
                              onloadend_func  = function() {
                                btn.parent().find("a.ibtn").removeClass("disabled");
                              };

                          do_action( "onblock_user", false, [ idx ] );
                          if ( has_action( "IP_onblock_user") ) {            
                            do_action( "IP_onblock_user", true, [ idx ], onsuccess_func, onerror_func, onloadend_func );
                            return;
                          }

                          ipqx(ipgo('docServer')+'ipChat/pull.php',"POST",{
                            channel: 'users',
                            process: 'users',
                            action: 'block',
                            id: [ idx ]
                          },{
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
                          if ( $(this).hasClass("disabled") ) {
                            return;
                          }
                          $().ipbox("close");
                        }
                      }
                    }
                  };
                  _extc("dialog",function(obj) {
                    $().ipbox( obj );
                  },[obj]);
                });
                return a1;
              },
              cs: '',
              cf: 'user'
            },
            8: {
              cb: function( tab, menu, item ) {
                var idx = tab.data("nubuid");
                var idn = tab.data("nubmod");

                var a1  = $().cn("a",{"class":"itemAnchor","role":"menuitem","tabindex":"0"}),
                    b1  = a1.cn("span",{"class":"itemLabel fsm"}, L.WRITING_LANGUAGE);

                a1.on("click", function(event) {
                  event.preventDefault();
                  if ( _extp("dialog") ) {
                    return;
                  }
                  $(this).parents(".uiToggle:first").removeClass("openToggler");
                  var table = $('<table />',{'border':0,'width':'100%','class':'ipLangSelTable'}).html('<tbody></tbody>'),
                      lcpsa = function( a, b ) {};
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
                  var write_lng = _gwl( idx, idn );
                  for( tr in tbl ) {
                    var trl = $("tbody",table).cn("tr");
                    for( td in tbl[tr] ) {
                      var tdl = trl.cn("td").data( "lang", tbl[tr][td] ).html( '<div>'+tbl[tr][td].n+'</div>' ).on("click", function(e2) {
                        e2.preventDefault();
                        var sel_lng = $(this).data( "lang" );
                        if ( sel_lng && sel_lng.c ) {
                          var lngs  = ipga("_gul") || ipsa("_gul",{});
                          if ( !unds.isObject( lngs ) ) {
                            lngs  = {};
                          }
                          if ( sel_lng.c == "en" ) {
                            if ( lngs[idn] && lngs[idn][idx] ) {
                              delete lngs[idn][idx];
                              if ( unds.isEmpty( lngs[idn] ) ) {
                                delete lngs[idn];
                              }
                            }
                          }
                          else {
                            lngs[idn] = lngs[idn] || {};
                            lngs[idn][idx]  = sel_lng.c;
                          }
                          ipsa("_gul",lngs);
                          $.cookie( "ulang_global", json_encode( lngs ), { expires: 365, path: "/" } );
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
                });
                return a1;
              },
              cs: '',
              cf: 'both'
            }
          };
          var file_uploads  = parseInt( ipga("settings").file_uploads );
          if ( !file_uploads ) {
            delete menuItems[3];
          }
          for( i in menuItems ) {
            if ( menuItems[i].cf === 'group' && tab.data("nubmod") !== 'group' ) {
              if ( menuItems[i].cd === true ) {
                var c1  = menu.cn("li",{"class":"uiMenuSeparator"});
              }
              continue;
            }
            if ( menuItems[i].cf === 'user' && tab.data("nubmod") !== 'user' ) {
              if ( menuItems[i].cd === true ) {
                var c1  = menu.cn("li",{"class":"uiMenuSeparator"});
              }
              continue;
            }
            var a1  = menu.cn("li",{"class":"uiMenuItem","tabindex":0});
            if ( menuItems[i].cs ) {
              a1.addClass( menuItems[i].cs );
            }
            if ( typeof menuItems[i].cb === "function" ) {
              var res = call_user_func_array( menuItems[i].cb, [ tab, menu, a1 ] );
              if ( !res ) {
                a1.remove();
                continue;
              }
              if ( !( res instanceof jQuery ) ) {
                a1.html( res );
              }
              else {
                res.appendTo( a1 );
              }
            }
            if ( menuItems[i].cd === true ) {
              var c1  = menu.cn("li",{"class":"uiMenuSeparator"});
            }
            if ( menuItems[i].ch === true ) {
              //a1.hide();
            }
          }
          $(".uiMenuSeparator",menu).not(".uiMenuItem + .uiMenuSeparator").remove();
        },
        skeleton: function( tab, ulist, message, prevent ) {
          var that  = ipDockPanel.prototype.process.chat.messages;
          var messageID = message.unID || message.ID;
          var sent_date = parseInt( message.sent_date );
          var sender_id = parseInt( message.ID );

          if ( $("#message_"+messageID).length ) {
            return ( $("#message_"+messageID).length );
          }
          if ( message.relationID !== undefined ) {
            if ( $(".messageR_"+message.relationID).length ) {
              return $(".messageR_"+message.relationID);
            }
          }

          if ( $("._kso", tab).length ) {
            var last_timestamp  = parseInt( $("._kso:last", tab).attr("timestamp") );
            if ( last_timestamp === sent_date ) {
              that.insertAfter( tab, ulist, $("._kso:last", tab), message );
            }
            else if ( last_timestamp < sent_date ) {
              var lastMessage = $("._kso", tab).filter(function() {
                var this_timestamp  = parseInt( $(this).attr("timestamp") );
                return ( this_timestamp < sent_date );
              }).filter(":last");
              that.insertAfter( tab, ulist, lastMessage, message );
            }
            else {
              var lastMessage = $("._kso", tab).filter(function() {
                var this_timestamp  = parseInt( $(this).attr("timestamp") );
                return ( this_timestamp > sent_date );
              }).filter(":first");
              that.insertBefore( tab, ulist, lastMessage, message );
            }
          }
          else {
            that.append( tab, ulist, message );
          }

          if ( prevent !== true && tab.hasClass("opened") && !tab.data("preventScroll") ) {
            $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
          }
          return;
        },
        item: function( target, message ) {
          var user  = g_50x5( message.sent_from, function( user, target, message ) {
            $("._50x5:first",target).html( user.NM );
            $("img.profilePhoto",target).attr( "src", user.AV );
            $(".metaInfoContainer .timestamp",target).html( timeDifference( message.sent_date ) );
            $("a.profileLink",target).attr({
              title: date("h:ia",message.sent_date)+"<br />"+user.NM
            });
          }, function( user, target, message ) {
            $("._50x5:first",target).html( "undefined" );
            $("img.profilePhoto",target).attr( "src", ipgo("docServer")+"ipChat/images/users/default_unavail.jpg" );
            $(".metaInfoContainer .timestamp",target).html( timeDifference( message.sent_date ) );
            $("a.profileLink",target).attr({
              title: date("h:ia",message.sent_date)+"\n"+"undefined"
            });
          }, [ target, message ] );
          return target;
        },
        attachments: {
          table: function( length ) {
            var cols = 1, rows = 1, size = "large";
            switch( true ) {
              case ( 2 === length || 4 === length ):
                cols  = 2;
                rows  = ( length / 2 );
                size  = "medium";
              break;
              case ( 3 === length || 4 < length ):
                cols  = 3,
                rows  = Math.ceil( length / cols ),
                size  = "small";
              break;
            }
            return [ rows, cols, size ];
          },
          uploadRow: function( tab, ret ) {
            if ( !tab.length ) {
              return false;
            }
            var inner   = $("div:data(tabInner)",tab);
            var shelf1  = $(".ipNubFlyoutAttachments",tab),
                shelf2  = $(".chatAttachmentShelf",shelf1);
            if ( !shelf1.length ) {
              var shelf1  = $(".ipNubFlyoutFooter",tab).cn("div",{"class":"ipNubFlyoutAttachments"},false,"insertAfter");
              var shelf2  = shelf1.cn("div",{"class":"chatAttachmentShelf"});
            }
            if ( !ret ) {
              var a1  = shelf2.cn("div",{"class":"_2qh _2qe uploadFileRow"}),
                  b1  = a1.cn("span",{"class":"_2qf ip-spinner"}),
                  c1  = a1.cn("a",{"class":"_2qg uiCloseButton uiCloseButtonSmall uiCloseButtonSmallDark"},'<i class="icon-remove"></i>'),
                  d1  = a1.cn("div",{"class":"_4-te"}),
                  e1  = d1.cn("span",{"class":"uiIconText _3tn"}),
                  f1  = e1.cn("i",{"class":"img-ico img-ico-16"});
              c1.on("click", function(event) {
                event.preventDefault();
                a1.fadeOut(600, function() {
                  $(this).remove();
                  ipDockPanel.prototype.process.responsive.tab.inner( inner, true );
                });
              });
              shelf2.scrollTop( shelf2[0].scrollHeight );              
            }
            return (!ret ) ? a1 : shelf2;
          },
          slideshow: function( event ) {
            event.preventDefault();
            event.stopPropagation();
            if ( _extp("slider") ) {
              return false;
            }
            var _ksh  = $(this);
            var _rpb  = $(this).parents("._rpb:first");
            var _kso  = _rpb.parents("._kso:first");
            var _len  = $("._rpb:data(attachment)",_kso);
            var _idx  = _len.index( _rpb );

            if ( !_len.length ) {
              return;
            }

            var items = [];
            _len.each(function() {
              var a = $(this).data("attachment");
              items.push({
                name: a.title,
                link: ipgo("docServer")+a.target,
                thumb: ipgo("docServer")+a.thumbnail,
                mime: a.mimetype,
                size: a.size
              });
            });
            
            if ( !items.length ) {
              return;
            }

            _rpb.addClass("async_saving");
            _extc("slider",function(_rpb,items,_idx) {
              $().ipslider({
                images: items,
                offset: _idx,
                onshow: function( image, offset ) {
                  _rpb.removeClass("async_saving");
                },
                onfail: function( image, offset ) {
                  $().ipslider("close");
                }
              });
            },[_rpb,items,_idx]);
          },
          placeholder3: function( base, length ) {
            length  = parseInt( length );
            if ( !length || isNaN( length ) ) {
              return;
            }
            for( var i = 0; i < length; i++ ) {
              var a1  = base.cn("div",{"class":"_59go"}),
                  b1  = a1.cn("div",{"class":"clearfix MercuryExternalLink"}),
                  c1  = b1.cn("div",{"class":"_rpb clearfix stat_elem lfloat linkPreview"}),
                  d1  = c1.cn("a",{"class":"_ksh","href":"#","role":"img","target":"_blank","rel":"ignore"}),
                  e1  = d1.cn("img",{"alt":""}),
                  f1  = b1.cn("div",{"class":"MercuryLinkRight rfloat"}),
                  g1  = f1.cn("div",{"class":"MercuryLinkTitle"}),
                  h1  = g1.cn("a",{"class":"linkTitle","target":"_blank","href":"#","role":"button","rel":"ignore"}),
                  i1  = f1.cn("div",{"class":"fsm fwn fcg"});
            }
          },
          placeholder2: function( base, length ) {
            length  = parseInt( length );
            if ( !length || isNaN( length ) ) {
              return;
            }
            for( var i = 0; i < length; i++ ) {
              var a1  = base.cn("div",{"class":"_59go _59gq MercuryDefaultIcon"}),
                  b1  = a1.cn("a",{"class":"uiIconText _3tn","role":"button","rel":"ignore"}),
                  c1  = b1.cn("i",{"class":""}),
                  d1  = b1.cn("span",{"class":"_59gp"});
            }
          },
          placeholder: function( tab, ulist, length, keep ) {
            length  = parseInt( length );
            if ( !length || isNaN( length ) ) {
              return '';
            }
            var _510g = $("._510g.seen",tab);
            if ( _510g.length ) {
              _510g.removeClass("seen");
              _510g.find("._510h:first").removeClass("icon-time icon-comment-alt");
              _510g.find("._510f:first").html("");
            }
            var base  = ( keep !== true ) ? ulist : $();

            var a1  = base.cn("div",{"class":"mhs mbs pts ipChatConvItem _50dw _50kf clearfix small"}),
                b1  = a1.cn("div",{"class":"_50ke"},'<div class="_50x5"></div><a class="profileLink" role="button"><img class="profilePhoto"></a>'),
                c1  = a1.cn("div",{"class":"messages"}),
                d1  = c1.cn("div",{"class":"metaInfoContainer fss fcg"},'<span class="timestamp"></span>'),
                e1  = c1.cn("div",{"class":"_542q clearfix _55r0"}),
                f1  = e1.cn("div"),
                g1  = f1.cn("div",{"class":"_kso fsm direction_ltr","title":date( "h:i a", time() ),"timestamp":time()}).css("max-width","173px"),
                h1  = g1.cn("div",{"class":"_55pl clearfix"}),
                i1  = h1.cn("div",{"class":"_55pk clearfix"});

            a1  = ipDockPanel.prototype.process.chat.messages.item( a1, { sent_from : ipga("user").ID, sent_date : time() } );
            $("a",b1).tipsy({gravity: $.fn.tipsy.autoWE,html:true});

            if ( keep !== true ) {
              ipDockPanel.prototype.process.chat.messages.timestamp( tab, time(), a1 );
            }

            var table = ipDockPanel.prototype.process.chat.messages.attachments.table( length );
            var rows  = table[0],
                cols  = table[1],
                size  = table[2],
                row = 1,
                col = 1,
                _kso  = 176,
                _ksh  = Math.floor( ( _kso / cols ) - ( ( length === 1 ) ? 0 : 2 ) ),
                _rpb  = Math.min( 100, ( 100 / _kso ) * _ksh ),
                _pad  = ( ( 100 - ( _rpb * cols ) ) / ( cols - 1 ) );
                _pad  = ( isNaN( _pad ) ) ? 0 : _pad;
                
            if ( cols > 1 ) {
              i1  = i1.cn("div",{"class":"_55pm clearfix"})
            }
            for( var i = 0; i < length; i++ ) {
              var l = ( col > 1 ) ? _pad : 0,
                  t = ( row > 1 ) ? _pad : 0;
              var a2  = i1.cn("div",{"class":"_rpb clearfix stat_elem"+( ( cols > 1 ) ? ' _55pn' : '' )}),
                  b2  = a2.cn("a",{"class":"_ksh _57jm _55pj","href":"#","target":"_blank","role":"button"}),
                  c2  = b2.cn("img",{"src":"https://m-static.ak.fbcdn.net/rsrc.php/v2/yw/r/drP8vlvSl_8.gif"});

              a2.css({
                width: _rpb+'%',
                marginLeft: l+'%',
                marginTop: t+'%'
              });
              b2.css({
                width: _ksh+'px',
                maxWidth: _ksh+'px',
                backgroundImage: 'url("'+ipgo('docServer')+'ipChat/images/icons/dots_'+size+'.png")'
              });

              if ( col === cols ) {
                col = 1;
                row++;
                continue;
              }
              col++;
            }

            var j1  = e1.cn("div"),
                k1  = j1.cn("div",{"class":"_542d"},'<span class="_55r6i"></span><span class="_55r6">'+L.SENDING+'...</span>');

            if ( keep !== true ) {
              $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
            }
            return ( keep !== true ) ? a1 : $("._kso",a1);
          }
        },
        params: function( options, keep ) {
          var t = time();
          if ( keep === true ) {
            options.unID  = uniqid();
            options.sent_date = t;
            options.datetime  = date( "Y-m-d", t );
            options.timestamp = date( "Y-m-d H:i:s", t );
            return options;
          }
          var params  = {
            ID: 0,
            unID: uniqid(),
            userID: ipga("user").ID,
            targetID: ( options.type === 'user' ) ? parseInt( options.id ) : 0,
            groupID: ( options.type === 'group' ) ? parseInt( options.id ) : 0,
            relationID: 0,
            message: options.message_n,
            message_o: options.message_o,
            message_n: options.message_n,
            sent_date: t,
            sent_from: ipga("user").ID,
            sent_to: parseInt( options.id ),
            is_readed: 0,
            is_opened: 0,
            read_date: 0,
            read_datetime: 0,
            read_timestamp: 0,
            attachments: options.attachments || [],
            has_attachment: ( options.attachments && options.attachments.length ) ? 1 : 0,
            is_notice: ( options.is_notice ) ? 1 : 0,
            notice_section: options.notice_section || 'left',
            datetime: date( "Y-m-d", t ),
            timestamp: date( "Y-m-d H:i:s", t ),
            source_code: 'chat',
            source_name: 'Chat'
          };
          var removable = [
            'is_readed','is_opened','read_date','read_datetime','read_timestamp',
            'sent_from', 'sent_to', 'relationID', 'groupID', 'targetID', 'ID'
          ];
          for( x in params ) {
            if ( !in_array( x, removable ) ) {
              continue;
            }
            if ( params[x] === 0 ) {
              delete params[x];
            }
          }
          return params;
        }
      }
    },
    responsive: {
      tab: {
        inner: function( tab, animate ) {
          tab = tab || $(".ipNubTabGroup .ipNub.opened .ipDockChatTabFlyout");
          tab.each(function() {
            var windowH = $(window).height(),
                bodyT   = $(".ipNubFlyoutBody:first",this).offset().top,
                footerH = $(".ipNubFlyoutFooter:first",this).height(),
                attachH = ( $(".ipNubFlyoutAttachments",this).length ) ? $(".ipNubFlyoutAttachments:first",this).height() - 1 : 0,
                bodyH   = ( windowH - bodyT - footerH - attachH );
            if ( animate === true ) {
              $(".ipNubFlyoutBody:first",this).stop().animate({height:bodyH},300,"linear",function() {
                $(this).ipscroll("update");
              });
              return;
            }
            $(".ipNubFlyoutBody:first",this).stop().height(bodyH);
            $(".ipNubFlyoutBody:first",this).ipscroll("update");
          });
        },
        resizer: {
          sidebar: false,
          tab_group: false,
          nub_group: false,
          nub_menu: false,
          tabs: false,
          menu: false,
          tabs_len: 0,
          menu_len: 0,

          size: {
            window: 0,
            dock: 0,
            sidebar: 0,
            current: 0,
            available: 0
          },
          past: {},
          init: function() {
            this.sidebar    = $(".ipChatSidebar:first"),
            this.tab_group  = $(".ipNubGroup.ipNubTabGroup:first"),
            this.nub_group  = $(".ipNub._51jt:first"),
            this.nub_menu   = $("ul.uiMenuInner:first",this.nub_group);

            this.tabs = $("div.ipNub",this.tab_group),
            this.menu = $("li.uiMenuItem",this.nub_menu);

            this.tabs_len = this.tabs.length,
            this.menu_len = this.menu.length;
          },
          width: function() {
            this.init();

            this.past = unds.clone( this.size );
            this.size.window  = parseInt( $(window).width() ),
            this.size.dock    = parseInt( this.tab_group.outerWidth( true ) ),
            this.size.sidebar = ( this.sidebar.hasClass("dockedSidebar") ) ? 0 : parseInt( this.sidebar.outerWidth( true ) );
            this.size.current = 0;

            this.size.available = Math.round( this.size.window - ( 60 + this.size.sidebar ) );
          },
          toggle: function(a,b) {
            this.width();

            if ( b === "remove" ) {
              var visible_tabs  = this.tabs.filter(":visible"),
                  visible_len   = visible_tabs.length;
              if ( visible_len === 1 ) {
                return;
              }
              this.window( false, true );
            }
            else {
              this.window( false, true );
              if ( a && a.length && a.is(":hidden") ) {
                var ftab    = this.tabs.filter(":visible:first").eq(0);
                    hidden  = ( this.tabs.filter(":hidden").not(a[0]).get() || [] );

                this.nubs( $(hidden).hide(0) );
                a.insertBefore( ftab ).show(0);
                ipDockPanel.prototype.process.responsive.tab.inner( a.find("div:data(tabInner)") );

                this.width();
                if ( this.size.dock > this.size.available ) {
                  hidden  = ( this.tabs.filter(":hidden").get() || [] );
                  while( this.size.dock > this.size.available ) {
                    var c = this.tabs.filter(":visible").not(":first").eq(0).hide(0);
                    if ( !c.length ) {
                      break;
                    }
                    hidden.push( c[0] );
                    this.width();
                  }
                  if ( hidden.length ) {
                    this.nubs( $(hidden).hide(0) );
                  }
                }
              }
            }
          },
          window: function(a,b) {
            this.width();
            if ( this.past.window === this.size.window && b !== true ) {
              return;
            }
            if ( !this.tabs_len ) {
              return;
            }
            var shown   = [],
                hidden  = [];
            for( var i = ( this.tabs_len - 1 ); i >= 0; i-- ) {
              var tab = this.tabs.eq( i );
              var wid = parseInt( tab.outerWidth( true ) );
              var cur = ( this.size.current + wid );

              if ( cur > this.size.available ) {
                hidden.push( tab[0] );
              }
              else {
                this.size.current = cur;
                shown.push( tab[0] );
              }
            }
            var $shown  = $(shown),
                $hidden = $(hidden);
            $shown.not(":visible").show(0);
            $hidden.not(":hidden").hide(0);

            $shown.filter(".opened").each(function() {
              ipDockPanel.prototype.process.responsive.tab.inner( $(this).find("div:data(tabInner)") );
            });
            this.nubs( $hidden );
          },
          nubs: function( tabs ) {
            if ( tabs && tabs.length ) {
              var temp  = tabs.filter(":data(istemp)");
              if ( temp.length ) {
                temp.each(function() {
                  if ( $(this).data("foo") ) {
                    $(this).remove();
                  }
                });
              }
              //tabs.filter(":data(istemp)").remove();
            }
            if ( !tabs || !tabs.length ) {
              this.nub_group.addClass("hidden_elem"),
              this.menu.empty();
              return false;
            }
            var that  = this,
                nubs  = [];
            //this.menu.empty();
            tabs.each(function() {
              var tab = $(this);
              var nub = ipDockPanel.prototype.process.render.holder( tab, that.nub_menu, "add" );
              if ( nub ) {
                nubs.push( nub[0] );
              }
            });
            $("li.uiMenuItem",this.nub_menu).not( nubs ).remove();
            this.nub_group.removeClass("hidden_elem").find("a.ipNubButton span").text( tabs.length );
            return tabs;
          },
        },
        resize: function( tab, instance, temporary ) {
          var nubuid  = ( tab && tab.length ) ? tab.data("nubuid") : false,
              nubmod  = ( tab && tab.length ) ? tab.data("nubmod") : false;
          console.log( instance, temporary );
        },
        fit: function( dockTab, isInstance, keepPosition, isResize ) {
          return;
          /** Get Elements **/
          var ipChatSidebar = $(".ipChatSidebar:first"),
              ipNubTabGroup = $(".ipNubGroup.ipNubTabGroup:first"),
              ipNubTabBase  = $(".ipNub._51jt:first"),
              ipNubTabMenu  = $("ul.uiMenuInner:first",ipNubTabBase);

          var dockTabs  = $("div.ipNub",ipNubTabGroup),
              menuTabs  = $("li.uiMenuItem",ipNubTabBase);

          var dockTabsLength  = dockTabs.length,
              menuTabsLength  = menuTabs.length,
              tabsHidden      = 0;

          var dockTabFirst  = ( dockTabsLength ) ? dockTabs.eq(dockTabsLength-1) : false;

          /** Calculate Width **/
          var widthSubtract = 50;
          if ( !ipChatSidebar.hasClass("dockedSidebar") ) {
            widthSubtract = ( widthSubtract + parseInt( ipChatSidebar.outerWidth( true ) ) );
          }
          var windowWidth   = parseInt( $(window).width() ),
              dockTabWidth  = parseInt( ipNubTabGroup.outerWidth( true ) ),
              widthCurrent  = ( dockTabFirst && dockTabFirst.length ) ? parseInt( dockTabFirst.outerWidth( true ) ) : false,
              sidebarWidth  = ( ipChatSidebar.length && !ipChatSidebar.hasClass("dockedSidebar") ) ? ipChatSidebar.outerWidth( true ) : 0;
              widthSubtract = ( 50 + sidebarWidth ),
              maxAvailWidth = Math.round( windowWidth - widthSubtract );

          if ( !dockTabsLength ) {
            return;
          }

          var dockTabIndex  = -1;
          if ( dockTab && dockTab.length ) {
            if ( dockTab.is(":visible") ) {
              dockTabIndex  = dockTabs.index( dockTab );
            }
          }

          for( var i = ( dockTabsLength - 2 ); i >= 0; i-- ) {
            var currentTab  = dockTabs.eq( i ),
                tabWidth    = parseInt( currentTab.outerWidth( true ) ),
                baseWidth   = ( widthCurrent + tabWidth );

            if ( baseWidth > maxAvailWidth ) {
              currentTab.hide(0);
              tabsHidden++;
              ipDockPanel.prototype.process.render.holder( currentTab, ipNubTabMenu, "add" );
            }
            else {
              widthCurrent  = baseWidth;
              currentTab.show(0);
              ipDockPanel.prototype.process.render.holder( currentTab, ipNubTabMenu, "substract" );
            }
          }

          if ( tabsHidden > 0 ) {
            ipNubTabBase.removeClass("hidden_elem");
          }
          else {
            ipNubTabBase.addClass("hidden_elem");
          }
          $("a.ipNubButton span",ipNubTabBase).text( tabsHidden );

          console.log( dockTabIndex );

          /*var tabGroup  = $(".ipNubTabGroup");
          if ( single ) {
            var f = $("div.ipNub:first:visible",tabGroup);
            tab.show(0).insertBefore(f);
            f.hide(0);
            ipDockPanel.prototype.process.render.holder( f, $(".ipNub._51jt ul.uiMenuInner"), "add" );
            return;
          }
          if ( version_compare( $().jquery, 1.8, ">" ) ) {
            var windowWidth   = parseInt( $(window).innerWidth() );
            var windowHeight  = parseInt( $(window).innerHeight() );
          }
          else {
            var windowWidth   = parseInt( document.documentElement.clientWidth );
            var windowHeight  = parseInt( document.documentElement.clientHeight );
          }
          if ( window.event && window.event.type === "resize" ) {
            if ( ipga("windowLastWidth") === windowWidth ) {
              return false;
            }
          }
          ipsa("windowLastWidth",windowWidth);
          ipsa("windowLastHeight",windowHeight);
          windowWidth -=  500;
          if ( tab instanceof jQuery ) {
            tab.prependTo( tabGroup );
          }
          var nubHolder = $(".ipNub._51jt"),
              nubTabs   = $("div.ipNub",tabGroup),
              nubLength = ( nubTabs.length - 1 ),
              nubLoop   = 0,
              nubExtend = 0,
              nubWidth  = ( nubLength ) ? $(nubTabs[nubLoop]).outerWidth(true) : 0,
              nubMenu   = $("ul.uiMenuInner",nubHolder),
              firstNub  = false;

          if ( windowWidth < 0 ) {
            nubTabs.hide(0);
          }

          for ( var nubIndex = nubTabs.size(); nubIndex > 0; nubIndex-- ) {
            var i = nubTabs.eq(nubIndex);
            var j = i.outerWidth(true);
            if ( ( nubExtend + j ) <= windowWidth ) {
              nubExtend +=  j;
              nubLoop++;
            }
            else {
              firstNub  = true;
            }
            if ( !firstNub ) {
              if ( i.is(":hidden") ) {
                i.show(0);
                ipDockPanel.prototype.process.render.holder( i, nubMenu, "substract" );
              }
            }
            else {
              if ( i.is(":visible") ) {
                i.hide(0);
                ipDockPanel.prototype.process.render.holder( i, nubMenu, "add" );
              }
            }
            if ( nubMenu.is(":empty") ) {
              nubHolder.addClass("hidden_elem").find(".ipNubButton span:first").html(0);
            }
            else {
              nubHolder.removeClass("hidden_elem").find(".ipNubButton span:first").html( $("li",nubMenu).length );
            }
          }*/
        },
        events: function() {
          $(document).on("keyup", function( event ) {
            if ( event.keyCode === 27 ) {
              if ( $(".uiDialogLayer").is(":visible") ) {
                $(".uiDialogLayer ._t .close-button:first").trigger("click");
              }
              else if ( $(".ipNubTabGroup .ipNub.focusedTab:first").length ) {
                var tab = $(".ipNubTabGroup .ipNub.focusedTab:first");
                $(".titlebarButtonWrapper a.close.button:first",tab).trigger("click");
              }
            }
          }).on("mousedown", function( event ) {
            var target  = $(event.target),
                nubtab  = target.parents(".ipNub:first");
            if ( nubtab.length ) {
              $(".ipNubTabGroup .ipNub").not(nubtab).removeClass("focusedTab");
            }
          });
        },
        uploader: {
          queueList: {},
          onchange: function( event ) {
            event.preventDefault();
            var tab   = $(this).parents("div:data(chatTab)");
            var inner = $("div:data(tabInner)",tab);
            var form  = $(this).parents("form:first");
            var frame = $("iframe",form);
            var accpt = $(this).data("accept");
            var name  = $(this).attr("name");
            var isImg = ( accpt === "image/*" );

            if ( $.trim( $(this).attr("accept") ) === "" || $(this).attr("accept") !== accpt ) {
              $(this).attr( "accept", accpt );
              return;
            }
            if ( tab.data("istemp") ) {
              return;
            }
            if ( !hasFileUpload() ) {
              if ( !frame.length ) {
                var frameID = "frame_"+unds.uniqueId();
                form.attr("target",frameID);
                frame = form.cn("iframe",{"class":"hidden_elem accessible_elem","name":frameID},false,'insertAfter');
              }
              form.trigger("submit");
              return;
            }
            else {
              $("input[name=source]",form).val("html5");
            }
            var files = event.target.files;
            if ( !files.length ) {
              if ( $(this).parents(".uiToggle:first").length ) {
                $(this).parents(".uiToggle:first").removeClass("openToggler");
              }
              return;
            }
            if ( $(this).parents(".uiToggle:first").length ) {
              $(this).parents(".uiToggle:first").removeClass("openToggler");
            }
            var actual_length = 0;
            for( var i = 0; i < files.length; i++ ) {
              var file  = files[i];
              var mime  = file.type;
              var extn  = pathinfo( file.name, "PATHINFO_EXTENSION" );

              if ( is_blocked = isfbd( extn, mime, true ) ) {
                ipcl().notice( "Unsupported "+( ( is_blocked == "mime" ) ? "mimetype '"+mime+"'" : "extension '"+extn+"'" ), false, true );
                continue;
              }
              actual_length++;
            }
            if ( isImg ) {
              var placeholder = ipDockPanel.prototype.process.chat.messages.attachments.placeholder( tab, $(".conversation",tab), actual_length );
              if ( !placeholder.length ) {
                return;
              }
            }

            ipDockPanel.prototype.process.responsive.tab.inner( inner );

            //form.trigger("reset");
            var unid  = unds.uniqueId();
            var relid = uniqid();
            $("input[name=relation_id]",form).val( relid );

            for( var i = 0; i < files.length; i++ ) {
              var file  = files[i];
              var mime  = file.type;
              var extn  = pathinfo( file.name, "PATHINFO_EXTENSION" );

              if ( is_blocked = isfbd( extn, mime, true ) ) {
                ipcl().notice( "Unsupported "+( ( is_blocked == "mime" ) ? "mimetype '"+mime+"'" : "extension '"+extn+"'" ), false, true );
                continue;
              }

              if ( isImg ) {
                var imgp  = $("._55pk",placeholder).find("div._rpb").eq(i);
              }
              else {
                var imgp  = ipDockPanel.prototype.process.chat.messages.attachments.uploadRow( tab );
                ipDockPanel.prototype.process.responsive.tab.inner( inner, true );
                $("span.uiIconText",imgp).append( file.name );
                $("span.uiIconText i.img-ico",imgp).addClass( "ico-"+pathinfo( file.name, "PATHINFO_EXTENSION" ).toString().toLowerCase() );
              }
              $(this).removeAttr("name");
              var fdata = new FormData( form[0] );
              $(this).attr("name",name);
              fdata.append( name, file );
              fdata.append( '_ksht', files.length );
              fdata.append( '_kshc', ( i + 1 ) );
              ipDockPanel.prototype.process.responsive.tab.uploader.queue( unid, tab.data( "nubuid" ), tab.data( "nubmod" ), fdata, file, imgp, isImg );
            }
 
            form.trigger("reset");
          },
          queue: function( uid, id, type, data, file, placeholder, isImage ) {
            var unid  = unds.uniqueId();
            var queueList = ipDockPanel.prototype.process.responsive.tab.uploader.queueList;

            queueList['idx']  = queueList['idx'] || false;
            queueList['idy']  = queueList['idy'] || {};
            queueList['idy'][unid]  = {
              idw: uid,
              idx: id,
              idy: type,
              idz: [ data, file, placeholder, isImage ]
            };
            if ( !isImage ) {
              $("a._2qg",placeholder).on("click",function(event) {
                if ( !placeholder.hasClass("uploading") && !placeholder.hasClass("done") ) {
                  delete ipDockPanel.prototype.process.responsive.tab.uploader.queueList['idy'][unid];
                }
              });
            }

            inc_kshc( uid, id, type );

            ipDockPanel.prototype.process.responsive.tab.uploader.upload();            
          },
          upload: function() {
            var queueList = ipDockPanel.prototype.process.responsive.tab.uploader.queueList;
            if ( queueList['idx'] === false ) {
              ipDockPanel.prototype.process.responsive.tab.uploader.process();
            }
          },
          process: function() {
            var that      = ipDockPanel.prototype.process.responsive.tab.uploader;
            var queueList = ipDockPanel.prototype.process.responsive.tab.uploader.queueList;
            if ( unds.isEmpty( queueList['idy'] ) ) {
              queueList['idx']  = false;
              return;
            }
            var idx = queueList['idx'];
            var idy = queueList['idy'];
            var ind = ( idx !== false ) ? nkio( idy, idx ) : unds.min( unds.keys( idy ) );
            if ( !ind ) {
              queueList['idx']  = false;
              queueList['idy']  = {};
              return;
            }
            if ( idx !== false ) {
              delete queueList['idy'][idx];
            }
            queueList['idx']  = ind;

            var onsuccess_func  = function( response ) {
                  var bidy  = idy[ind];
                  if ( response.error ) {
                    bidy.idz[2].addClass("uploadError done");
                    //ipChat.prototype.notice( response.message, false, true  );
                    return;
                  }
                  if ( response.message ) {
                    if ( ipWebSocket != false ) {
                      ipWebSocket.send(
                        json_encode(
                          $.extend( response.message, {
                            event: "message",
                            users: ( !checkInt( response.message.groupID, 0 ) ) ? g_50x4( response.message.groupID ) : false
                          } )
                        )
                      );
                    }
                    if ( bidy.idz[3] === true ) {
                      var _kso  = bidy.idz[2].parents("div._kso:first");
                      _kso.data("message", response.message);
                      do_action( "on_after_send_message", false, response.message );
                    }
                    ipDockPanel.prototype.process.chat.messages.history.item( $('li.uiMessageItem .jewelContent div:first'), response.message, "prepend" );
                  }
                  bidy.idz[2].addClass("uploadSuccess done").data("attachment",response.attachment);
                  if ( bidy.idz[3] !== true ) {
                    $("a._2qg",bidy.idz[2]).on("click", function(event) {
                      var data  = bidy.idz[2].data("attachment");
                      call_user_func_array( dtam, [ bidy.idx, bidy.idy, data.ID ] );
                    });
                    call_user_func_array( atam, [ bidy.idx, bidy.idy, response.attachment ] );
                  }
                  else {
                    var thumb = response.attachment.thumbnail || response.attachment.target;
                    if ( thumb ) {
                      $("a._55pj",bidy.idz[2]).css("opacity",0).removeClass("_57jm").css({
                        backgroundImage: 'url("'+ipgo("docServer")+thumb+'")'
                      }).animate({"opacity":1}).on("click",ipDockPanel.prototype.process.chat.messages.attachments.slideshow);
                    }
                  }
                },
                onerror_func  = function( response, error ) {
                  var bidy  = idy[ind];
                  bidy.idz[2].addClass("uploadError done");
                  //ipChat.prototype.notice( "Error: could not upload \""+bidy.idz[1].name+"\" ("+error.message+")", false, false );
                },
                onloadend_func  = function() {
                  xhr = null;
                  var bidy  = idy[ind];
                  dec_kshc( bidy.idw, bidy.idx, bidy.idy );
                  if ( bidy.idz[3] !== true ) {
                    if ( bidy.idz[2].hasClass("uploadError") ) {
                      bidy.idz[2].fadeOut(600, function() {
                        var inner = $(this).parents("div:data(tabInner)");
                        $(this).remove();
                        ipDockPanel.prototype.process.responsive.tab.inner( inner );
                      });
                    }
                  }
                  if ( ucf_kshc( bidy.idw, bidy.idx, bidy.idy ) ) {
                    if ( bidy.idz[3] === true ) {
                      var _kso  = bidy.idz[2].parents("div._kso:first");
                      var _542q = _kso.parents("div._542q:first");
                      _kso.insertBefore( _542q );
                      _542q.remove();
  
                      if ( $("._rpb",_kso).length === $("._rpb.uploadError.done",_kso).length ) {
                        var conv  = _kso.parents(".ipChatConvItem:first");
                        if ( $(".messages ._kso",conv).length === 1 ) {
                          conv.fadeOut(600, function() {
                            var inner = $(this).parents("div:data(tabInner)");
                            $(this).remove();
                            ipDockPanel.prototype.process.responsive.tab.inner( inner );
                          });
                        }
                      }
                    }
                    else {
                      if ( hmas( bidy.idx, bidy.idy ) ) {
                        rmas( bidy.idx, bidy.idy );
                        ipDockPanel.prototype.process.chat.send( bidy.idx, bidy.idy );
                      }
                    }
                  }
                  that.process();
                };

            var upload_id = 0;
            if ( has_action( "IP_onupload_start" ) ) {
              upload_id = do_action( "IP_onupload_start", true, idy[ind].idz, onsuccess_func, onerror_func, onloadend_func );
            }
            else {
              var xhr = new ipXhr;
              xhr.open( ipgo('docServer')+'ipChat/pull.php', "POST", true );
              xhr.callback({
                onsuccess: onsuccess_func,
                onerror: onerror_func,
                onloadend: onloadend_func
              });
              xhr.send( idy[ind].idz[0] );
            }

            if ( idy[ind].idz[3] !== true ) {
              idy[ind].idz[2].addClass("uploading");
              $("a._2qg",idy[ind].idz[2]).on("click",function(event) {
                if ( has_action( "IP_onupload_abort" ) ) {
                  do_action( "IP_onupload_abort", true, upload_id );
                  return;
                }
                if ( xhr ) {
                  xhr.abort();
                }
              });
            }
          }
        },
        textarea: {
          typing: {
            timer: {
              requests: {},
              timeout: 1000,
              interval: {},
              hasRequest: function( idx, idn ) {
                return ( this.requests[idn] && this.requests[idn][idx] );
              },
              hasTimeout: function( idx, idn ) {
                return ( this.interval[idn] && this.interval[idn][idx] );
              },
              setRequest: function( idx, idn ) {
                this.clearTimeout( idx, idn )
                if ( !this.clearRequest( idx, idn ) ) {
                  this.requests[idn]  = this.requests[idn] || {};
                }
                var data  = {
                  channel: 'messages',
                  process: 'typing',
                  action: 'add',
                  idx: idx,
                  idn: idn,
                  idj: time()
                };

                if ( has_action( "IP_ontyping" ) ) {
                  do_action( "IP_ontyping", true, data.idx, data.idn, data.idj );
                  return;
                }
                this.requests[idn][idx] = $.post( ipgo('docServer')+'ipChat/pull.php', data, false, "json" );
              },
              setTimeout: function( idx, idn, intval ) {
                intval  = ( typeof intval === 'number') ? intval : this.timeout;
                if ( this.hasTimeout( idx, idn ) ) {
                  return;
                }
                else {
                  this.interval[idn]  = this.interval[idn] || {};
                }
                this.clearRequest( idx, idn );
                var that  = this;
                this.interval[idn][idx] = setTimeout(function() {
                  that.setRequest( idx, idn );
                }, intval);
                return true;
              },
              clearRequest: function( idx, idn ) {
                if ( this.hasRequest( idx, idn ) ) {
                  this.requests[idn][idx].abort();
                  return true;
                }
                return false;
              },
              clearTimeout: function( idx, idn ) {
                if ( this.hasTimeout( idx, idn ) ) {
                  clearTimeout( this.interval[idn][idx] );
                  return true;
                }
                return false;
              }
            },
            add: function( idx, idn, intval ) {
              if ( idn == "group" ) {
                return false;
              }
              return this.timer.setTimeout( idx, idn, intval );
            },
            clear: function( idx, idn ) {
              return this.timer.clearTimeout( idx, idn );
            }
          },
          format: {
            regex: function( idx ) {
              var regex = {
                'fb_code' : /\s+\[\[([\w\d\.\_]+)\]\]/gi,
                'ext_url' : /((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/gi,
                'emoj_reg': /^\[emoji\](\w+)\.([\w\d]+)\[\/emoji\]$/i,
                'int_url' : /(((http|ftp|https):\/\/)?[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/gi
              };
              return regex[idx];
            },
            text: function( text ) {
              var regex = this.regex;
              text  = htmlspecialchars( $.trim( ''+text ) );
              text  = ' '+this.emoticons( text );
              if ( text.match( regex('ext_url') ) ) {
                text  = text.replace( regex('ext_url'), '<a href="$1" alt="External Link">$1</a>' );
              }
              if ( text.match( regex('fb_code') ) ) {
                text  = text.replace( regex('fb_code'), ' <img src="https://graph.facebook.com/$1/picture" height="16" alt="External Image">' );
              }
              text  = this.emoji( text );
              text  = nl2br( text );
              return text;
            },
            emoticons: function( text ) {
              text  = ' '+$.trim( ''+text );
              var emoticons = ipga("emoticon");
              for( x in emoticons ) {
                if ( emoticons[x].emoji === true ) {
                  continue;
                }
                var codes = emoticons[x].data;
                var name  = x;
                for( code in codes ) {
                  var search  = unds.map( unds.clone( codes[code] ), function( str ) { return str.replace( /[-[\]{}()*+?.:,\\^$|#\s]/g, "\\$&" ) });
                  var regex   = new RegExp( '\\s('+search.join('|')+')', 'gi' );
                  text  = text.replace( regex, '<span class="emoticon '+name+' '+name+'_'+code+'"></span>' );
                }
              }
              return $.trim( text );
            },
            emoji: function( text ) {
              text  = $.trim( text );
              if ( !text.length ) {
                return text;
              }
              var sticker = ipga("emoticon");
              var regex = this.regex;
              var match = text.match( regex( 'emoj_reg' ) );
              if ( match ) {
                var idx = $.trim( match[1] );
                var ind = $.trim( match[2] );
                if ( sticker[idx] && sticker[idx]["data"] && sticker[idx]["data"][ind] ) {
                  text  = '<div class="_55r0"><div><img class="mvs sticker sticker_'+idx+' sticker_'+idx+'_'+ind+'" src="'+sticker[idx]["data"][ind]+'" width="'+sticker[idx]["imgw"]+'" height="'+sticker[idx]["imgh"]+'" alt="'+( ( !isNaN( parseInt( ind ) ) ) ? idx : ind )+'"></div></div>';
                }
                else {
                  text  = '<div class="_55r00"><span>'+L.STICKER_MISSING+'</span></div>';
                }
              }
              return text;
            },
            translation: {
              init: function( textarea, data, event ) {
                textarea  = ( !( textarea instanceof jQuery ) ) ? $(textarea) : textarea;
                if ( !textarea.length ) {
                  return;
                }
                var text  = $.trim( textarea.val() );
                if ( !text.length ) {
                  console.log( "Not enough text" );
                  return;
                }

                var lang  = _gwl( data.id, data.type );

                if ( event === "selection" ) {
                  this.revisions.show( text, textarea, data, lang )
                  return;
                }

                var word  = this.find( textarea );

                if ( !unds.isArray( word ) || unds.isEmpty( word ) ) {
                  return;
                }

                if ( word[0].match( /[\x00-\x80]+/gi ) && lang === "en" ) {
                  return;
                }

                var cache = _gtc( word, lang );
                if ( cache ) {
                  this.parse( textarea, word, cache, data, lang );
                  return;
                }

                this.translate( textarea, word, data, lang );
              },
              translate: function( textarea, word, data, lang ) {
                var that  = ipDockPanel.prototype.process.responsive.tab.textarea.format.translation;
                var xhr = new ipXhr;
                xhr.open( ipgo('docServer')+'ipChat/pull.php' );
                xhr.params({
                  channel: 'languages',
                  process: 'translate',
                  search: word[0],
                  from: 'detect',
                  to: lang
                });
                xhr.callback({
                  onsuccess: function( res ) {
                    _slr( lang, res.text, res.revisions );
                    that.parse( textarea, word, res.text, data, lang );
                  }
                })
                xhr.send();
              },
              parse: function( textarea, search, replace, data, lang ) {
                var that  = ipDockPanel.prototype.process.responsive.tab.textarea.format.translation;
                if ( !replace || !replace.length ) {
                  console.log( "Nothing to replace "+replace );
                  return;
                }
                _stc( search, lang, replace );
                if ( !( textarea instanceof jQuery ) ) {
                  textarea  = $(textarea);
                }

                var caretposition = that.caretPosition( textarea[0] );

                var text  = textarea.val();

                var search_length   = ''+search[0].split("").length;
                var replace_length  = ''+replace.split("").length;
 
                var difference  = ( replace_length - search_length );

                var regexpr = new RegExp( "\\b"+search[0]+"\\b", 'g' );
                var nth = 0;
                var new_text  = text.replace( regexpr, function( match, i, original ) {
                  nth++;
                  return ( nth === search[1] ) ? replace : match;
                });

                if ( new_text === text ) {
                  return;
                }

                caretposition = ( caretposition + difference );
                textarea.val( new_text ).trigger("autosize.resize");
                that.setCaretPosition( textarea[0], caretposition );
              },
              find: function( textarea ) {
                var that  = ipDockPanel.prototype.process.responsive.tab.textarea.format.translation;
                if ( textarea instanceof jQuery ) {
                  textarea  = textarea.get(0);
                }
                var cpos  = that.caretPosition( textarea );
                var word  = that.returnWord( textarea.value, cpos );
                return word;
              },
              caretPosition: function( textarea ) {
                if ( textarea instanceof jQuery ) {
                  textarea  = textarea.get(0);
                }
                var caretPos  = 0;
                if ( document.selection ) {
                  $(textarea).trigger("focus");
                  var selection = document.selection.createRange();
                      selection.moveStart( "character", -textarea.value.length );
                  caretPos  = selection.text.length;
                }
                else if ( textarea.selectionStart || parseInt( textarea.selectionStart ) <= 0 ) {
                  caretPos  = textarea.selectionStart;
                }
                return caretPos;
              },
              setCaretPosition: function( textarea, position ) {
                if ( textarea.setSelectionRange ) {
                  $(textarea).trigger("focus");
                  textarea.setSelectionRange( position, position );
                }
                else if ( textarea.createTextRange ) {
                  var range = textarea.createTextRange();
                  range.collapse( true );
                  range.moveEnd( "character", position );
                  range.moveStart( "character", position );
                  range.select();
                }
              },
              returnWord: function( text, position ) {
                text  = ''+text;
                text  = text.substring( 0, position );
                if ( text.substr( -2 ) === "  " ) {
                  return;
                }
                text  = $.trim( text );

                var parts = text.split( " " );
                if ( !unds.isEmpty( parts ) ) {
                  var word  = $.trim( parts[parts.length-1] );
                  if ( word.length ) {
                    var chars = ( word.split("").length );
                    var regex = new RegExp( "\\b"+word+"\\b", 'g' );
                    var index = text.match( regex );
                    return ( index ) ? [ word, index.length ] : false;
                  }
                }
                return false;
              },
              revisions: {
                show: function( text, textarea, data, lang ) {
                  var that  = ipDockPanel.prototype.process.responsive.tab.textarea.format.translation.revisions;
                  var lang  = _gwl( data.id, data.type );
                  var range = new maxkir.SelectionRange( textarea[0] );
                  var selc  = range.get_selection_range();
                  var word  = $.trim( range.get_selection_text() );
                  if ( !word.length ) {
                    that.hide();
                    return;
                  }
                  var revs  = _glr( lang, word );
                  if ( !revs ) {
                    that.hide();
                    return;
                  }
                  that.flyout( textarea );
                  that.render( textarea, revs );
                  var cursor  = new maxkir.CursorPosition( textarea[0], 0 );
                      cursor  = cursor.getPixelCoordinates();

                  var Flyout  = $(".ita-ppe-box",ipga("wrap_elm"));
                  var topPos  = textarea.parent().offset().top;
                  var leftPos = textarea.parent().offset().left;
                  console.log( cursor );

                  if ( cursor[0] > 190 ) {
                    cursor[0] = 0;
                    cursor[1] = cursor[1] + 16;
                  }
                  if ( cursor[1] === 0 ) {
                    cursor[1] = 16;
                  }

                  topPos  = ( topPos - Flyout.outerHeight(true) ) + ( cursor[1] - textarea.parent()[0].scrollTop - 10 );
                  leftPos = ( leftPos + cursor[0] );

                  if ( ( leftPos + Flyout.outerWidth(true) ) > $(window).width() ) {
                    $(".ita-ppe-box",ipga("wrap_elm")).css({
                      top : topPos+"px",
                      left: "auto",
                      right: "0px"
                    });
                  }
                  else {
                    $(".ita-ppe-box",ipga("wrap_elm")).css({
                      top : topPos+"px",
                      left: leftPos+"px",
                      right: "auto"
                    });
                  }
                },
                flyout: function( textarea ) {
                  var that  = ipDockPanel.prototype.process.responsive.tab.textarea.format.translation.revisions;
                  var ppe_box = $(".ita-ppe-box",ipga("wrap_elm"));
                  if ( ppe_box.length ) {
                    ppe_box.show(0);
                    $(".ita-ppe-can-list",ppe_box).empty();
                    return;
                  }
                  var a1  = ipga("wrap_elm").cn("div",{"class":"ita-ppe-box","tabindex":"-1"}).unselectable(),
                      b1  = a1.cn("div",{"class":"ita-ppe-div"}),
                      c1  = b1.cn("div",{"class":"ita-ppe-can-list"});
                  textarea.on("blur click",that.hide);
                },
                hide: function() {
                  $(".ita-ppe-box",ipga("wrap_elm")).hide(0).find(".ita-ppe-can-list").empty();
                },
                render: function( textarea, revs ) {
                  var that  = ipDockPanel.prototype.process.responsive.tab.textarea.format.translation.revisions;
                  var menu  = $(".ita-ppe-box .ita-ppe-can-list",ipga("wrap_elm"));
                  for( var i = 0; i < revs.length; i++ ) {
                    menu.cn("div",{"class":"ita-ppe-can"},( i + 1 )+". "+revs[i]).data({
                      "word": revs[i],
                      "area": textarea
                    }).on("mouseenter", function() {
                      $(this).addClass("ita-ppe-hlt");
                    }).on("mouseleave", function() {
                      $(this).removeClass("ita-ppe-hlt");
                    }).on("mousedown",that.reinsert);
                  }
                },
                reinsert: function( event ) {
                  var area  = $(this).data("area");
                  var range = new maxkir.SelectionRange( area[0] );
                  var selcn = range.get_selection_range();
                  var word  = range.get_selection_text();
                  var repl  = $(this).data("word");

                  if ( word.substr( word.length - 1, 1 ) == ' ' ) {
                    repl  +=  ' ';
                  }
                  if ( word.substr( 0, 1 ) == ' ' ) {
                    repl  = ' '+repl;
                  }
                  area.insertAtCaret( repl, false ).trigger("autosize.resize");
                }
              }
            }
          },
          event: function( event ) {
            var tab = $(this).parents(".ipDockChatTabFlyout:first"),
                ftr = $(this).parents(".ipNubFlyoutFooter:first"),
                inf = $("._552n:first",ftr),
                pos = ( isrtl() ) ? "left" : "right";
            if ( event.type === "input" || event.type === "paste" ) {
              var thisHeight  = parseInt( Math.min( $(this).height(), 75 ) );
              var lastHeight  = $(this).data("lastHeight");
              if ( thisHeight != lastHeight ) {
                if ( thisHeight <= 75 ) {
                  $(this).data("lastHeight",thisHeight)
                }
                ipDockPanel.prototype.process.responsive.tab.inner( tab );
              }
              if ( thisHeight === 75 ) {
                var sbw = getscrollbarwidth();
                if ( inf.css(pos) != sbw ) {
                  inf.css(pos,sbw+"px");
                }
              }
              else {
                if ( parseInt( inf.css(pos) ) > 0 ) {
                  inf.css(pos,"0px");
                }
              }
            }
            else if ( event.type === "keydown" ) {
              var tab     = $(this).parents("div:data(chatTab)"),
                  nubmod  = tab.data("nubmod"),
                  nubuid  = tab.data("nubuid");

              if ( nubuid && nubmod && !( event.keyCode === 65 && event.ctrlKey ) ) {
                if ( nubmod == "user" ) {
                  g_50x5( nubuid, function( user, tab ) {
                    if ( ipWebSocket != false ) {
                      ipWebSocket.send(
                        json_encode({
                          event: "typing",
                          user: ipga( "user" ).ID,
                          idx: user.ID,
                          idn: "user"
                        })
                      );
                    }
                    else {
                      ipDockPanel.prototype.process.responsive.tab.textarea.typing.add( user.ID, "user" );
                    }
                  }, function( tab ) {
                    
                  }, [ tab ]);
                }
                else {
                  if ( ipWebSocket != false ) {
                    ipWebSocket.send(
                      json_encode({
                        event: "typing",
                        user: ipga( "user" ).ID,
                        group: g_50x4( nubuid ),
                        idx: nubuid,
                        idn: "group"
                      })
                    );
                  }
                  else {
                    ipDockPanel.prototype.process.responsive.tab.textarea.typing.add( nubuid, "group" );
                  }
                }
              }

              if ( event.keyCode === 65 && event.ctrlKey ) {
                $(this).select();
              }
              else if ( event.keyCode === 13 && !event.shiftKey ) {
                event.preventDefault();
                var tab = $(this).parents("div:data(chatTab)");
                ipDockPanel.prototype.process.chat.dynamic( tab );
              }
            }
            else if ( event.type === "keyup" ) {
              var tab     = $(this).parents("div:data(chatTab)"),
                  nubmod  = tab.data("nubmod"),
                  nubuid  = tab.data("nubuid");

              if ( event.keyCode === 32 && !event.shiftKey ) {
                /** URL Parser **/
                var inn = $(".ipDockChatTabFlyout:first",tab);
                var uri_reg = ipDockPanel.prototype.process.responsive.tab.textarea.format.regex( "ext_url" );
                var uri_par = ipga("uri_par") || ipsa("uri_par",{});

                var stream  = new StreamShare( tab );
                if ( !stream.detected() && !stream.processing() ) {
                  if ( stream.validate() ) {
                    stream.render();
                  }
                }
  
                var data  = {id:nubuid,type:nubmod};
                ipDockPanel.prototype.process.responsive.tab.textarea.format.translation.init( $(this), data );
                return;
              }
            }
            else if ( event.type === "select" ) {
              var tab = $(this).parents("div:data(chatTab)");
              var data  = {id:tab.data("nubuid"),type:tab.data("nubmod")};
              ipDockPanel.prototype.process.responsive.tab.textarea.format.translation.init( $(this), data, "selection" );
            }
            else if ( event.type === "click" ) {
              if ( !event.isTrigger && !event.relatedTarget ) {
                $(this).data("init", true);
              }
            }
            else if ( event.type === "focus" ) {
              var tab     = $(this).parents("div:data(chatTab)"),
                  nubmod  = tab.data("nubmod"),
                  nubuid  = tab.data("nubuid");
              if ( nubuid && nubmod && !event.isTrigger && !event.relatedTarget ) {
                $(this).removeData("init");
                //ipDockPanel.prototype.process.responsive.tab.textarea.typing.add( nubuid, nubmod, 1 );
              }
              $(this).parent().addClass("_552hf");
            }
            else if ( event.type === "blur" ) {
              var tab     = $(this).parents("div:data(chatTab)"),
                  nubmod  = tab.data("nubmod"),
                  nubuid  = tab.data("nubuid");
              ipDockPanel.prototype.process.responsive.tab.textarea.typing.clear( nubuid, nubmod );
              $(this).parent().removeClass("_552hf");
            }
            else {
              ipDockPanel.prototype.process.responsive.tab.textarea.format.translation.revisions.hide();
            }
          }
        }
      }
    },
    tab: {
      open: function( id, type, temp, send, ping ) {
        send  = ( send === false ) ? false : true;
        if ( !( id instanceof jQuery ) && typeof id !== "number" && typeof id !== "string" ) {
          do_action( "on_open_tab", false, false, false );

          if ( $(".ipNub:data(istemp)").length ) {
            $(".ipNub:data(istemp)").removeClass("closed").addClass("opened").trigger("focus").find(".addToThread.button").trigger("click");
            return $(".ipNub:data(istemp)");
          }
          var tab = ipDockPanel.prototype.process.render.nub.skeleton( L.NEW_MESSAGE );
          abt( tab );
          $(".loading-older",tab).addClass("hidden_elem");
          $(".addToThread.button",tab).trigger("click");
          return tab;
        }
        if ( id instanceof jQuery ) {
          do_action( "on_open_tab", false, id.data("nubuid"), id.data("nubmod") );
          clear_alert( id.data("nubuid"), id.data("nubmod") );

          id.removeClass("closed").addClass("opened").find("textarea._552m").trigger("focus");
          $("._51jx:first",id).text('0').addClass("hidden_elem");
          ipDockPanel.prototype.process.responsive.tab.inner( id.find("div:data(tabInner)") );
          if ( id.data("istemp") !== true ) {
            $.cookie( id.data("nubmod")+'Tab'+id.data("nubuid"), "opened", { expires: 365, path: "/" } );
          }
          if ( id.data("loaded") === false ) {
            ipDockPanel.prototype.process.chat.messages.load( id.data("nubuid"), id.data("nubmod") );
            id.removeData("hasMessage");
          }
          else {
            if ( id.data("hasMessage") ) {
              do_action( "onview_unreaded_message", false, id.data("nubuid"), id.data("nubmod"), function() {
                id.removeData("hasMessage");
              }, function() {
                
              });
            }
          }
          return id;
        }
        if ( id && type ) {
          if ( id == ipga("user").ID && type == "user" ) {
            return false;
          }
          do_action( "on_open_tab", false, id, type );

          var mtab  = ( $("#"+type+"Nub"+id).length ) ? $("#"+type+"Nub"+id) : $("div:data("+type+"TabFor"+id+")");
          if ( mtab.length ) {
            if ( ping !== true ) {
              mtab.removeClass("closed").addClass("opened").find("textarea._552m").trigger("focus");
              $("._51jx:first",mtab).text(0).addClass("hidden_elem");
              clear_alert( id, type );
            }
            if ( temp && ( temp instanceof jQuery ) ) {
              ipDockPanel.prototype.process.tab.close( temp, false, "remove" );
            }
            return mtab;
          }
        }
        if ( temp && ( temp instanceof jQuery ) ) {
          var tab = temp;
        }
        else {
          var tab = ipDockPanel.prototype.process.render.nub.skeleton();
          var tabs  = ipga("tabs") || ipsa("tabs",{});
          if ( !tabs[type+id] ) {
            tabs[type+id] = {
              idx: id,
              idn: type
            };
            ipsa("tabs",tabs);
          }
          if ( $.cookie( type+'Tab'+id ) === "closed" && !send ) {
            tab.removeClass("opened").addClass("closed");
          }
          else {
            $.cookie( type+'Tab'+id, "opened", { expires: 365, path: "/" } );
          }
        }
        ipDockPanel.prototype.process.tab.init( tab, id, type, send );
        return tab;
      },
      close: function( id, type, remove ) {
        if ( !id ) {
          return;
        }
        if ( id && type ) {
          var id  = $("#"+type.toString().toLowerCase()+"Nub"+id);
        }
        if ( ( id instanceof jQuery ) && id.length ) {
          var data  = id.data();
          if ( data.istemp ) {
            if ( data.tabFor ) {
              data.nubmod = data.tabFor[0];
              data.nubuid = data.tabFor[1];
            }
          }

          do_action( "on_close_tab", false, data.nubuid, data.nubmod, !remove );

          if ( remove ) {
            if ( $(".tipsy").length ) {
              $(".tipsy").remove();
            }
            if ( data.nubmod ) {
              $.removeCookie( data.nubmod+'Tab'+data.nubuid, { path: "/" });
              var tabs  = ipga("tabs") || ipsa("tabs",{});
              if ( tabs[data.nubmod+data.nubuid] ) {
                delete tabs[data.nubmod+data.nubuid];
                ipsa("tabs",tabs);
              }
              u_50mzr( data.nubuid, data.nubmod );
            }
            id.remove();
            if ( !data.nubmod || !data.nubuid ) {
              $("li.uiMenuItem_temp_"+data.nubuid).remove();
            }
            else {
              if ( has_action( "IP_onclose_tab" ) ) {
                do_action( "IP_onclose_tab", true, data.nubuid, data.nubmod );
                return;
              }
              ipqx( ipgo('docServer')+'ipChat/pull.php', 'POST', {
                channel: 'tabs',
                process: 'state',
                action: 'close',
                id: data.nubuid,
                type: data.nubmod
              } );
            }
          }
          else {
            $.cookie( data.nubmod+'Tab'+data.nubuid, "closed", { expires: 365, path: "/" } );
            id.removeClass("opened").addClass("closed");
          }
        }
      },
      init: function( tab, id, type, send ) {
        tab.data(type+"TabFor"+id,true);
        tab.data("tabFor",[ type, id ]);
        if ( has_action( "IP_onopen_tab" ) ) {
          do_action( "IP_onopen_tab", true, id, type, !send );
          ipDockPanel.prototype.process.tab.convert( tab, id, type );
          return;
        }
        if ( send === true ) {
          ipqx( ipgo('docServer')+'ipChat/pull.php', 'POST', {
            channel: 'tabs',
            process: 'state',
            action: 'open',
            id: id,
            type: type
          } );
        }
        ipDockPanel.prototype.process.tab.convert( tab, id, type );
      },
      convert: function( tab, id, type, update ) {
        if ( !update ) {
          var loader  = '<div class="pam text_align_ctr"><span class="ip-spinner"></span></div>';
          ipDockPanel.prototype.process.tab.flyout.show( loader, id, type, 'tabLoading', 100, true );
        }
        if ( !tab.data("disabled") ) {
          dbt( tab, true );
        }
        if ( type === "group" ) {
          g_50x4( id, function( group, tab, id, type, update, cron ) {
            var name  = group.name || 'Loading&hellip;';
            if ( group ) {
              if ( !tab.length ) {
                tab = $("#groupNub"+id);
              }
              if ( tab.data("disabled") === true ) {
                abt( tab );
              }
              tab.removeData(type+"TabFor"+id);
              $("a.titlebarText, .ipChatTab .name",tab).html( name ).attr( "title", name );
              tab.removeData("istemp").data({
                nubuid: id,
                nubmod: 'group',
                pusers: group.users
              }).find("textarea._552m").trigger("focus");
              if ( !cron ) {
                if ( !update ) {
                  tab.addClass("groupNub groupNub"+id).attr("id","groupNub"+id);
                  $("form._552o",tab).prepend( getUploadVars( tab, true ) );
                  if ( tab.hasClass("opened") ) {
                    ipDockPanel.prototype.process.chat.send( tab );
                    ipDockPanel.prototype.process.chat.messages.load( id, type );
                  }
                  ipDockPanel.prototype.process.chat.messages.tab_options( tab );
                }
              }
              if ( group.write !== true ) {
                dbt( tab, "permanent" )
              }
              else {
                if ( tab.data("disabled") !== true ) {
                  abt( tab );
                }
              }
            }
            ipDockPanel.prototype.process.tab.flyout.hide( tab );
          }, function( message, tab, id, type, update ) {
            ipDockPanel.prototype.process.chat.messages.add_notice( tab, 'warning-sign', message );
            ipDockPanel.prototype.process.tab.flyout.hide( tab );
          }, [ tab, id, type, update ] );
        }
        else {
          g_50x5( id, function( user, tab, id, type ) {
            if ( ( user.SA || user.ST ) === "busy" ) {
              dbt( tab, "permanent" );
            }
            else {
              if ( tab.data("disabled") === true ) {
                abt( tab );
              }
            }
            tab.removeClass("online busy idle offline").addClass( user.SA || user.ST );
            $(".status-icon",tab).addClass("status-"+( user.SA || user.ST )).removeClass("status-empty");
            tab.removeData(type+"TabFor"+id);
            $("a.titlebarText, .ipChatTab .name",tab).html( user.NM ).attr( "title", user.NM );
            tab.addClass("userNub userNub"+id).attr("id","userNub"+id);
            tab.removeData("istemp").data({
              nubuid: id,
              nubmod: 'user'
            }).find("textarea._552m").trigger("focus");
            $("form._552o",tab).prepend( getUploadVars( tab, true ) );
            if ( tab.hasClass( "opened" ) ) {
              ipDockPanel.prototype.process.chat.send( tab );
              ipDockPanel.prototype.process.chat.messages.load( id, type );
            }
            ipDockPanel.prototype.process.chat.messages.tab_options( tab );
            ipDockPanel.prototype.process.tab.flyout.hide( tab );
          }, function( message, tab, id, type ) {
            $(".loading-older",tab).hide(0);
            $("a.titlebarText, .ipChatTab .name",tab).html( "undefined" ).attr( "title", "undefined" );
            ipDockPanel.prototype.process.chat.messages.add_notice( tab, 'warning-sign', message );
            ipDockPanel.prototype.process.tab.flyout.hide( tab );
          }, [ tab, id, type ], true );
        }
      },
      add: function( id, type, sheet ) {
        var that  = ipDockPanel.prototype.process.tab;
        if ( id instanceof jQuery ) {
          var inner = id.find("div:data(tabInner)");
          if ( id.data("istemp") === true ) {
            var a1  = $().cn("div",{"class":"_54_v"},'<table class="uiGrid"><tbody><tr><td class="vTop _54_x"><span class="fcg">'+L.TO+'</span></td><td class="vTop _54_w"></td></tbody></table>'),
                b1  = a1.find("td").eq(1).cn("div",{"class":"clearfix uiTokenizer uiInlineTokenizer"},'<div class="tokenarea hidden_elem"></div>'),
                c1  = b1.cn("div",{"class":"uiTypeahead","tabindex":0},'<div class="wrap"><div class="innerWrap"></div></div>'),
                d1  = $(".innerWrap",c1).cn("input",{"type":"text","class":"inputtext textInput","tabindex":0}).data("uiTypeaheadText",true);
            that.flyout.show( a1, id, false, sheet, 100, true );
          }
          else {
            var a1  = $().cn("div",{"class":"_54_-"},'<table class="uiGrid"><tbody><tr><td class="vTop _54__"></td><td class="vTop"><input value="'+L.DONE+'" type="submit" class="ibtn ibtni doneButton"></td></tbody></table>'),
                b1  = a1.find("td").eq(0).cn("div",{"class":"clearfix uiTokenizer uiInlineTokenizer"},'<div class="tokenarea hidden_elem"></div>'),
                c1  = b1.cn("div",{"class":"uiTypeahead","tabindex":0},'<div class="wrap"><div class="innerWrap"></div></div>'),
                d1  = $(".innerWrap",c1).cn("input",{"type":"text","class":"inputtext textInput","tabindex":0,"autofocus":true,"autocomplete":false}).data({"uiTypeaheadText":true,"uiTypeaheadCustom":true});
            $(".doneButton",a1).on("click",function(event) {
              event.preventDefault();
              event.stopPropagation();
              var ids = d1.utokenizer( "get", "id" );
              if ( !ids.length ) {
                $("a.addToThread.button",id).trigger("click");
              }
              else {
                ipDockPanel.prototype.process.chat.dynamic( id, false, true );
              }
              return false;
            });
            that.flyout.show( a1, id, false, sheet, 100, true );
          }
          a1.on("click", function(event) {
            event.preventDefault();
            event.stopPropagation();
            if ( !$(event.target).is("input") ) {
              d1.trigger("focus");
            }
          });
          var users_list  = ipga("users");
          if ( id.data("istemp") !== true ) {
            if ( id.data("nubmod") === "group" ) {
              var group = g_50x4( id.data("nubuid") );
              if ( group ) {
                users_list  = unds.omit( users_list, unds.map( group.users, function(num){return num.toString();} ) );
              }
            }
            else {
              users_list  = unds.omit( users_list, id.data("nubuid") );
            }
          }
          d1.autoGrowInput({
            minWidth: 20
          }).utokenizer({
            target: b1,
            list: users_list,
            search: "NM",
            limit: 5,
            onsearch: function( ul, item, tokenizer ) {
              ipUsers.prototype.first_degree( function( tab, input ) {
                var users_list  = ipga("users");
                if ( tab.data("istemp") !== true ) {
                  if ( tab.data("nubmod") === "group" ) {
                    var group = g_50x4( tab.data("nubuid") );
                    if ( group ) {
                      users_list  = unds.omit( users_list, unds.map( group.users, function(num){return num.toString();} ) );
                    }
                  }
                  else {
                    users_list  = unds.omit( users_list, tab.data("nubuid") );
                  }
                }
                input.utokenizer("set",users_list).trigger("blur").trigger("focus");
              }, [ id, d1 ] );
              var a2  = ul.cn("li",{"class":"user"}),
                  b2  = a2.cn("img",{"src":item.AV,"alt":item.NM}),
                  c2  = a2.cn("span",{"class":"text"},item.NM),
                  d2  = a2.cn("span",{"class":"subtext"},item.SA||item.ST);
              a2.on("mousedown",function(event) {
                event.preventDefault();
                tokenizer.utokenizer( "add", item.ID, item.NM );
                ipDockPanel.prototype.process.responsive.tab.inner( inner );
              });
              a2.on("mouseenter",function() {
                $("li",ul).removeClass("selected");
                $(this).addClass("selected");
              }).on("mouseleave",function() {
                $(this).removeClass("selected");
              });
            },
            onremove: function( id, name, tokenizer ) {
              ipDockPanel.prototype.process.responsive.tab.inner( inner );
            }
          });
        }
      },
      name: function( tab ) {
        var that  = ipDockPanel.prototype.process.tab;
        if ( tab instanceof jQuery ) {
          var inner = tab.find("div:data(tabInner)");
          var idx = tab.data("nubuid");
          var idn = tab.data("nubmod");
          if ( idn !== "group" || tab.data("disabled") ) {
            return;
          }
          var a1  = $().cn("div",{"class":"_56jk"},'<table class="uiGrid"><tbody><tr><td class="vTop _56jl"></td><td class="vTop"><button type="submit" class="ibtn ibtni doneButton">'+L.HIDE+'</button></td></tbody></table>'),
              b1  = a1.find("td").eq(0).cn("div",{"class":"uiTypeahead","tabindex":0},'<div class="wrap"><div class="innerWrap"></div></div>'),
              c1  = $(".innerWrap",b1).cn("input",{"type":"text","class":"inputtext textInput","tabindex":0,"autofocus":true,"autocomplete":false,"placeholder":L.NAME_CONV});
          c1.on("keyup", function(event) {
            var name  = $.trim( $(this).val() );
            if ( name.length ) {
              $(".doneButton",a1).html( L.DONE );
            }
            else {
              $(".doneButton",a1).html( L.HIDE );
            }
          });
          $(".doneButton",a1).on("click", function(event) {
            event.preventDefault();
            var name  = $.trim( c1.val() );
            var elms  = $([c1[0],this]);
            if ( name.length ) {
              elms.attr("disabled",true);
              ipqx(ipgo('docServer')+'ipChat/pull.php','POST',{
                channel: 'messages',
                process: 'group',
                action: 'naming',
                id: idx,
                name: name
              },{
                onsuccess: function(res) {
                  if ( res.error ) {
                    elms.removeAttr("disabled").filter("input[type=text]").val('').trigger("focus");
                    return;
                  }
                  that.flyout.hide( tab );
                  ipDockPanel.prototype.process.chat.messages.add( tab, res.message );
                  u_50x4( res.group );
                  that.convert( tab, idx, 'group', true );
                },
                onerror: function(res,err) {
                  elms.removeAttr("disabled").filter("input[type=text]").val('').trigger("focus");
                }
              });
            }
            else {
              that.flyout.hide( tab );
            }
          });
          that.flyout.show( a1, tab, false, "nameConversation", 100, true );
        }
      },
      flyout: {
        show: function( item, id, type, sheet, speed, stop, fade ) {
          speed = speed || 100;
          sheet = sheet || 'offlineMessage';
          stop  = stop || false;
          if ( !( item instanceof jQuery ) ) {
            item  = $(item);
          }
          if ( id instanceof jQuery ) {
            var tab = id;
          }
          else {
            var tab = $("."+type+"Nub"+id);
          }
          if ( tab.length ) {
            if ( tab.data('sheetSlide') === sheet ) {
              return false;
            }
            tab.removeData('sheetSlide');
            if ( stop ) {
              tab.addClass("stopFlyout");
            }
            else {
              tab.removeClass("stopFlyout");
            }
            tab.addClass("_1sk4").data("sheetSlide",sheet);
            var flyout  = tab.find("div:data(flyoutTarget)");
            var inner   = tab.find("div:data(tabInner)");
            //flyout.stop(true, true).css("bottom","1px");
            ipDockPanel.prototype.process.responsive.tab.inner( inner );
            if ( parseInt( flyout.css("bottom") ) <= 0 ) {
              flyout.stop(true, true).removeClass("hidden_elem").animate({bottom:flyout.height()+"px"},speed,"linear",function() {
                $(this).html('').append( item );
                ipDockPanel.prototype.process.responsive.tab.inner( inner );
                $(this).animate({bottom:'0px'},speed,"linear",function() {
                  //tab.removeClass("_1sk4");
                  ipDockPanel.prototype.process.responsive.tab.inner( inner );
                  $(this).find('input[type=text]').trigger("focus");
                  if ( fade === true ) {
                    tab.addClass("stopFlyout");
                    var fadeInterval  = setInterval(function() {
                      clearInterval( fadeInterval );
                      ipDockPanel.prototype.process.tab.flyout.hide( tab );                      
                    }, 2000);
                  }
                });
              });
            }
            else {
              flyout.html('').append( item ).removeClass("hidden_elem");
              ipDockPanel.prototype.process.responsive.tab.inner( inner );
              flyout.animate({bottom:'0px'},speed,"linear",function() {
                //tab.removeClass("");
                ipDockPanel.prototype.process.responsive.tab.inner( inner );
                $(this).find('input[type=text]').trigger("focus");
                if ( fade === true ) {
                  tab.addClass("stopFlyout");
                  var fadeInterval  = setInterval(function() {
                    clearInterval( fadeInterval );
                    ipDockPanel.prototype.process.tab.flyout.hide( tab );                      
                  }, 2000);
                }
              });
            }
          }
        },
        hide: function( id, type, speed ) {
          speed = speed || 100;
          if ( id instanceof jQuery ) {
            var tab = id;
          }
          else {
            var tab = $("."+type+"Nub"+id);
          }
          if ( tab.length ) {
            tab.removeData('sheetSlide');
            tab.removeClass("stopFlyout");
            var flyout  = tab.find("div:data(flyoutTarget)");
            var inner   = tab.find("div:data(tabInner)");
            if ( flyout.is(":hidden") || parseInt( flyout.css("bottom") ) > 0 ) {
              return;
            }
            tab.addClass("_1sk4");
            ipDockPanel.prototype.process.responsive.tab.inner( inner );
            flyout.stop(true, true).animate({bottom:flyout.height()+"px"},speed,"linear",function() {
              $(this).html('').addClass("hidden_elem");
              ipDockPanel.prototype.process.responsive.tab.inner( inner );
            });
          }
        }
      }
    },
    render: {
      holder: function( tab, menu, mode ) {
        mode  = ( mode == "add" ) ? "add" : "subract";
        var idx = tab.data("nubuid"),
            idn = tab.data("nubmod"),
            idt = tab.data("istemp"),
            idl = $('<li class="uiMenuItem"><div class="lfloat"><span class="_51jx hidden_elem">0</span></div><a class="itemAnchor" role="menuitem" tabindex="0" href="#"><span class="itemLabel fcg fsm">Loading</span></a></li>');

        if ( !idx || !idn ) {
          setTimeout(function() {
            ipDockPanel.prototype.process.render.holder( tab, menu, mode );
          }, 500)
          return;
        }

        var ins = 'uiMenuItem'+( ucfirst( idn )+idx ).toString();
        if ( mode === "add" ) {
          if ( $("#"+ins,menu).length ) {
            return $("#"+ins,menu);
          }
          idl.addClass(ins).attr("id",ins).data( tab.data() );
          $("a",idl).on("click",function(e) {
            e.preventDefault();
            ipDockPanel.prototype.process.tab.open( tab );
            ipDockPanel.prototype.process.responsive.tab.resizer.toggle( tab, "open" );
            ipDockPanel.prototype.process.responsive.tab.inner( tab.find("div:data(tabInner)") );
          });
          if ( idn == "user" ) {
            g_50x5( idx, function( user, idx, idn, idl, tab ) {
              $("a span",idl).removeClass("fcg").text( user.NM );
            }, function( idx, idn, idl, tab ) {
            }, [ idx, idn, idl, tab ]);
          }
          else {
            g_50x4( idx, function( group, idx, idn, idl, tab ) {
              $("a span",idl).removeClass("fcg").text( group.name );
            }, function( idx, idn, idl, tab ) {
            }, [ idx, idn, idl, tab ]);
          }
          idl.appendTo( menu );
          return idl;
        }
        else {
          if ( $("#"+ins,menu).length ) {
            $("#"+ins,menu).remove();
          }
        }
      },
      base: function() {
        var a = ipcl().wrap_elm.cn("div",{"class":"_50-- ipDockWrapper ipDockWrapperRight"}),
            b = a.cn("div",{"class":"ipDock clearfix"}),
            c = b.cn("div",{"class":"clearfix nubContainer rNubContainer"}),
            d = c.cn("div",{"id":"ChatTabsPagelet"}),
            e = d.cn("div",{"class":"ipNubGroup clearfix _56oy"}),
            f = e.cn("div",{"class":"ipNubGroup ipNubTabGroup clearfix videoCallEnabled"});

        if ( isrtl() ) {
          a.removeClass("ipDockWrapperRight").addClass("ipDockWrapperLeft");
          c.removeClass("rNubContainer").addClass("lNubContainer");
        }
        ipDockPanel.prototype.process.responsive.tab.events();
        this.nub.holder( e );
      },
      nub: {
        holder: function( wrap ) {
          var a = wrap.cn("div",{"class":"uiToggle _50-v ipNub _51jt hidden_elem"},false,'prepend').unselectable(),
              b = a.cn("a",{"class":"ipNubButton"},'<i class="icon-comment mrs"></i><span>0</span><span class="_51jw hidden_elem">0</span>'),
              c = a.cn("div",{"class":"ipNubFlyout uiToggleFlyout noTitlebar"}),
              d = c.cn("div",{"class":"ipNubFlyoutOuter"}),
              e = d.cn("div",{"class":"ipNubFlyoutInner"}),
              f = e.cn("div",{"class":"ipNubFlyoutBody scrollable"}),
              g = f.cn("div",{"class":"ipNubFlyoutBodyContent"}),
              h = g.cn("div",{"class":"uiMenu","role":"menu"},'<ul class="uiMenuInner"></ul>').ipscroll({
                wrapper: ".ipNubFlyoutBody:first",
                gripper: "blackripper"
              });

          a.on("click", function(event) {
            event.preventDefault();
            $(".uiToggle").not($(this)).removeClass("openToggler");
            if ( $(event.target).parents(".ipNubFlyout:first").length ) {
              return;
            }
            $(this).toggleClass("openToggler");
          });

          if ( tabs = ipga( "tabs" ) ) {
            if ( !unds.isEmpty( tabs ) ) {
              for( tab in tabs ) {
                ipDockPanel.prototype.process.tab.open( tabs[tab].idx, tabs[tab].idn, false, false, false, true );
              }
              ipDockPanel.prototype.process.responsive.tab.resizer.window();
            }
          }
          //for( var i = 1; i < 4; i++ ) {
            //ipDockPanel.prototype.process.tab.open( i, 'user', 'opened' );
            
            /*ipDockPanel.prototype.process.flexi.nub.holder($('.uiMenuInner',h),{
              name: 'User '+i,
              ID: i,
              type: 'user'
            }, function( event ) {
              event.preventDefault();
            });*/
          //}
        },
        skeleton: function( title ) {
          var unid  = unds.uniqueId(),
              that  = this,
              elm   = $('.ipNubTabGroup');
          /** Opened Nub **/
          var a1  = elm.cn("div",{"class":"ipNub bubbles _50-v _50mz _50m_ opened Nub"+unid,"tabindex":0}).data({
                loaded: false,
                istemp: true,
                nubuid: unid,
                chatTab: true
              }),
              b1  = a1.cn("div",{"class":"ipNubFlyout ipDockChatTabFlyout","role":"complementary"}).data({
                tabInner: true
              }),
              c1  = b1.cn("div",{"class":"ipNubFlyoutOuter"}),
              d1  = c1.cn("div",{"class":"ipNubFlyoutInner"}),
              e1  = d1.cn("div",{"class":"clearfix ipNubFlyoutTitlebar titlebar"}).unselectable(),
              f1  = e1.cn("div",{"class":"mls titlebarButtonWrapper rfloat"}),
              g1  = e1.cn("div",{"class":"titlebarLabel clearfix"}),
              h1  = g1.cn("h4",{"class":"titlebarTextWrapper"}),
              i1  = h1.cn("span",{"class":"status-icon status-empty"}),
              j1  = h1.cn("a",{"class":"titlebarText","role":"button"},(title||'&nbsp;')),
              k1  = d1.cn("div",{"class":"ipNubFlyoutHeader"},'<div class="_1sk5"><div class="_1sk6"></div></div>').unselectable(),
              l1  = d1.cn("div",{"class":"ipNubFlyoutBody scrollable"}).ipscroll({
                onTotalScrollBack: function() {
                  ipDockPanel.prototype.process.chat.messages.load_older( a1 );
                  a1.data("preventScroll",true);
                },
                onTotalScroll: function() {
                  a1.data("preventScroll",false);
                },
                onScroll: function( per ) {
                  if ( per <= 80 ) {
                    a1.data("preventScroll",true);
                  }
                  else {
                    a1.data("preventScroll",false);
                  }
                }
              }),
              m1  = l1.cn("div",{"class":"ipNubFlyoutBodyContent"}),
              n1  = m1.cn("table",{"class":"uiGrid conversationContainer","cellspacing":0,"cellpadding":0,"role":"log"},'<tbody><tr><td class="vBot"><i class="pvm loading"></i></td></tr></tbody>'),
              o1  = d1.cn("div",{"class":"ipNubFlyoutFooter"},'<div class="_552h"><textarea class="uiTextareaAutogrow _552m" placeholder="'+L.WRITE_REPLY+'..." spellcheck="false" autocapitalize="off" autocomplete="off" autocorrect="off"></textarea></div>').unselectable(),
              p1  = o1.cn("div",{"class":"_552n"},'<form class="_552o" method="POST" enctype="multipart/form-data" action="'+ipgo('docServer')+'ipChat/pull.php'+'"></form>'),
              q1  = $('form',p1).cn("div",{"class":"_6a _m _4q60"},'<a class="_4q61 _509v" role="button" tabindex="0"><i class="_509w icon-camera"></i><div class="_3jk"><input type="file" class="_n _1qp5" name="attachment[]" multiple="1" accept="image/*" /></div></a>'),
              r1  = p1.cn("div",{"class":"uiToggle emoticonsPanel"},'<a class="emoteTogglerImg _5bvk" title="'+L.CHOOSE_EMOTICON+'" rel="toggle" role="button" tabindex="0"><i class="icon-smile"></i></a>'),
              s1  = r1.cn("div",{"class":'panelFlyout _590j uiToggleFlyout'},'<div><div class="_5906"></div><div class="_5907"></div></div><div class="panelFlyoutArrow"></div>'),
              t1  = $("._5906",s1).cn("div",{"class":"_5908"},'<a class="_58_w lfloat"><span class="_590q"></span></a><a class="_58_x rfloat"><span class="_590q"></span></a><div class="_590r"><div class="_58_y"><div class="_58_z clearfix"></div></div></div>');

          /** Closed Nub **/
          var a2  = a1.cn("a",{"class":"ipNubButton","tabindex":0,"role":"button"}).unselectable(),
              b2  = a2.cn("div",{"class":"clearfix ipChatTab"},'<div class="funhouse rfloat"><div class="close tabCloseHandler" title="'+L.CLOSE+'"><i class="icon-remove"></i></div></div>'),
              c2  = b2.cn("div",{"class":"wrapWrapper"},'<div class="clearfix"><div class="lfloat"><span class="_51jx hidden_elem">0</span></div><div class="name fwb">'+(title||'&nbsp;')+'</div></div>');

          /** Header **/
          $("._1sk6",k1).data("flyoutTarget",true).html('<div class="pam text_align_ctr"><span class="ip-spinner"></span></div>');

          that.titleBarButtons( f1 );
          $(".tabCloseHandler",b2).tipsy({gravity: $.fn.tipsy.autoNS});
          //ipDockPanel.prototype.process.responsive.tab.fit( a1 );

          /** Conversations **/
          $("td",n1).eq(0).html('<div class="accessible_elem">Chat Conversation Start</div><div class="loading-older"><span class="ip-spinner"></span></div><div class="conversation"></div><div class="accessible_elem">Chat Conversation End</div><div class="_510g"><div class="_510h"></div><div class="_510f"></div></div><div class="_51lq"></div>');

          /** Emoticons **/
          $("._5906",s1).hide(0);
          $("._5907",s1).html('<span class="_55ym _55yn _55yo _5905 ip-spinner"></span>');
          $(".emoteTogglerImg",r1).on("click",function( event ) {
            event.preventDefault();
            if ( a1.data("disabled") || a1.data("istemp") ) {
              return;
            }
            if ( !$(this).parent().hasClass("emoticonLoaded") ) {
              $(this).parent().addClass("emoticonLoaded");
              that.emoticons.load( $("._58_z",t1), $("._5907",s1), $("textarea._552m",o1) );
              $("._5906",s1).show(0);
            }
            $(".uiToggle").not($(this).parent()).removeClass("openToggler");
            $(this).parent().toggleClass("openToggler");
          });

          /** Toggle **/
          c2.on("click",function(event) {
            event.preventDefault();
            ipDockPanel.prototype.process.tab.open( a1 );
            ipDockPanel.prototype.process.responsive.tab.resizer.toggle( a1, "open" );
          });
          g1.on("click",function(event) {
            event.preventDefault();
            ipDockPanel.prototype.process.tab.close( a1 );
            ipDockPanel.prototype.process.responsive.tab.resizer.toggle( a1, "close" );      
          });
          $("div.close",b2).on("click",function(event) {
            event.preventDefault();
            ipDockPanel.prototype.process.tab.close( a1, false, "remove" );
            ipDockPanel.prototype.process.responsive.tab.resizer.toggle( a1, "remove" );
          });

          /** Text area**/
          $("textarea._552m",o1).autosize().on("keyup keydown select input paste focus blur",ipDockPanel.prototype.process.responsive.tab.textarea.event);
          $("._552h",o1).on("click",function(event) {
            if ( !$(event.target).is("textarea") ) {
              $("textarea._552m",o1).trigger("focus");
            }
          });
          $("textarea._552m",o1).on("focus", function() {
            $(this).parents(".ipNub:first").addClass("focusedTab");
          }).on("blur", function() {
            $(this).parents(".ipNub:first").removeClass("focusedTab");
          });

          /** Tab Focus/Blur **/
          a1.on("click",function(event) {
            $(this).addClass("focusedTab");
            if ( !$(event.target).is("input") && !$(event.target).is("textarea") ) {
              $("textarea._552m",this).trigger("focus");
            }
          }).on("focus",function(event) {
            $(this).addClass("focusedTab");
            $("textarea._552m",this).trigger("focus");
          }).on("blur",function(event) {
            $(this).removeClass("focusedTab");
          });

          /** Attachment **/
          var file_uploads  = parseInt( ipga("settings").file_uploads );
          if ( !file_uploads ) {
            $("form._552o",p1).remove();
          }
          else {
            if ( ipgo( 'attachmentMultiple' ) === false ) {
              $("input._1qp5",q1).attr("name","attachment").removeAttr("multiple");
            }
            $("input._1qp5",q1).data("accept","image/*").on("change",ipDockPanel.prototype.process.responsive.tab.uploader.onchange).on("click",function(event) {
              if ( a1.data("istemp") || a1.data("disabled") ) {
                event.preventDefault();
                return false;
              }
            });
          }

          ipDockPanel.prototype.process.responsive.tab.inner( b1 );
          dbt( a1, true );
          return a1;
        },
        emoticons: {
          load: function( header, footer, textarea ) {
            var tab = header.parents("div:data(chatTab)");
            var emoticons = ipga("emoticon");
            for( loop1 in emoticons ) {
              var current   = loop1;
              var emoticon  = emoticons[loop1];
              var a1  = header.cn("a",{"class":"_55bn emoticon_"+loop1,"tabindex":0,"title":emoticon.name}).tipsy({gravity: $.fn.tipsy.autoNS}).data("emoticon",loop1),
                  b1  = a1.cn("span",{"class":"_55bw"}).css("background-image",'url("'+emoticon.icon.u+'")'),
                  c1  = a1.cn("span",{"class":"_590u"}).css("background-image",'url("'+emoticon.icon.s+'")');
              a1.on("click",function(event) {
                event.preventDefault();
                if ( $(this).hasClass("_55bo") ) {
                  return;
                }
                var togls = $(this).parent().find("._55bn");
                var index = togls.index( this );
                togls.not(this).removeClass("_55bo");
                $(this).addClass("_55bo");
                ipDockPanel.prototype.process.render.nub.emoticons.lazy( $(this).data("emoticon"), footer, index, textarea );
                $("._55bq",footer).not("._55bq"+index).addClass("hidden_elem");
                $("._55bq"+index,footer).removeClass("hidden_elem");
              });
            }
            $("a._55bn:first",header).trigger("click");
          },
          lazy: function( item, footer, index, textarea ) {
            var that  = this,
                tab   = footer.parents("div:data(chatTab)");;
            var a1  = $("._55bq"+index,footer);
            if ( a1.length ) {
              return;
            }
            $("._55yn._55yo",footer).show(0);
            var before  = $("._55bq"+( index - 1 ),footer);
            if ( before.length ) {
              a1  = before.cn("div",{"class":"_55bq _55bq"+index},false,"insertAfter");
            }
            else {
              a1  = footer.cn("div",{"class":"_55bq _55bq"+index});
            }
            that.get( item, function( emoticon ) {
              $("._55yn._55yo",footer).hide(0);
              if ( emoticon.emoji !== true ) {
                var tb  = that.table( emoticon.data );
                var b2  = a1.cn("table",{"class":"emoticonsTable"}),
                    c2  = b2.cn("tbody");
                for( tr in tb ) {
                  var d2  = c2.cn("tr");
                  for( td in tb[tr] ) {
                    var e2  = d2.cn("td",{"class":"panelCell"}).data("emoticon",tb[tr][td]),
                        f2  = e2.cn("a",{"class":"emoticon "+item+" "+item+"_"+tb[tr][td].name});
                    e2.on("click",function(event) {
                      event.preventDefault();
                      var emd = $(this).data("emoticon");
                      textarea.insertAtCaret( emd.data[0], true ).trigger("keyup");
                      $(this).parents(".openToggler:first").find("a.emoteTogglerImg:first").trigger("click");
                    });
                  }
                }
              }
              else {
                var a3  = a1.cn("div",{"class":"_5bo4"}).ipscroll({
                  gripper: 'blackGripper'
                });
                var emoji = emoticon.data;
                for( x in emoji ) {
                  var idx = item;
                  var ind = x;
                  var b3  = a3.cn("a",{"class":"_55bp","role":"button"}).data("emoji",'[emoji]'+idx+'.'+ind+'[/emoji]'),
                      c3  = b3.cn("img",{"class":"_55bx","alt":x,"src":emoji[x]});
                  b3.on("click", function(event) {
                    event.preventDefault();
                    textarea.val( $(this).data("emoji") );
                    $(this).parents(".openToggler:first").removeClass("openToggler");
                    ipDockPanel.prototype.process.chat.send( tab );
                  });
                }
              }
            });
          },
          get: function( index, callback, args ) {
            args  = args || [];
            var emoticons = ipga("emoticon");
            if ( unds.isObject( emoticons[index] ) && emoticons[index].hasOwnProperty( 'data' ) ) {
              args.unshift( emoticons[index] );
              call_user_func_array( callback, args );
              return;
            }
            ipqx( ipgo('docServer')+'ipChat/pull.php', 'POST', {
              channel: 'settings',
              process: 'emoticons',
              item: index
            }, {
              onsuccess: function( response ) {
                if ( !response.error ) {
                  emoticons[index]  = response;
                  ipsa("emoticon", emoticons);
                  args.unshift( response );
                  call_user_func_array( callback, args );
                }
              }
            });
          },
          table: function( emoticons ) {
            var tr  = 1,
                td  = 1,
                ls  = {};
            for( row in emoticons ) {
              ls[tr]  = ls[tr] || {};
              ls[tr][td]  = {name:row,data:emoticons[row]};

              if ( td >= 7 ) {
                tr  +=  td  = 1;
                continue;
              }
              td++;
            }
            return ls;
          }
        },
        titleBarButtons: function( elm, id, type ) {
          var a = elm.cn("a",{"class":"addToThread button","title":L.ADD_MORE_FRIENDS,"role":"button","tabindex":0},'<i class="icon-user"></i>').tipsy({gravity: $.fn.tipsy.autoNS}),
              b = elm.cn("a",{"class":"videoicon button","title":L.FEATURE_NOT_AVAIL,"role":"button","tabindex":0},'<i class="icon-facetime-video"></i>').tipsy({gravity: $.fn.tipsy.autoNS}),
              c = elm.cn("div",{"class":"uiSelector inlineBlock _510p"}),
              d = c.cn("div",{"class":"uiToggle wrap"},'<a title="'+L.OPTIONS+'" class="button uiSelectorButton" role="button" rel="toggle" tabindex="0"><i class="icon-gear"></i></a>'),
              e = d.cn("div",{"class":"uiSelectorMenuWrapper uiToggleFlyout"},'<div role="menu" class="uiMenu uiSelectorMenu"><ul class="uiMenuInner"></ul></div>'),
              f = elm.cn("a",{"class":"close button tabCloseHandler","title":L.CLOSE,"role":"button","tabindex":0},'<i class="icon-remove"></i>').tipsy({gravity: $.fn.tipsy.autoNS});
          
          $("a.uiSelectorButton",c).on("click",function(event) {
            event.preventDefault();
            var tab = $(this).parents("div:data(chatTab)");
            if ( tab.data("disabled") || tab.data("istemp") ) {
              $(this).parent().removeClass("openToggler");
              return false;
            }
            $(".uiToggle").not($(this).parent()).removeClass("openToggler");
            if ( $(this).parents(".ipNub:first").data("loading") ) {
              $(this).trigger("blur");
              return;
            }
            $(this).parent().toggleClass("openToggler");
          });
          $("a",d).tipsy({gravity: $.fn.tipsy.autoNS});
          f.on("click",function(event) {
            event.preventDefault();
            var tab = $(this).parents("div:data(chatTab)");
            ipDockPanel.prototype.process.tab.close( tab, false, true );
            ipDockPanel.prototype.process.responsive.tab.resizer.toggle( tab, "remove" );
            return false;
          });
          a.on("click",function(event) {
            event.preventDefault();
            var tab = $(this).parents("div:data(chatTab)");
            if ( tab.data("disabled") ) {
              return false;
            }
            if ( tab.data("sheetSlide") === "addToThread" && !tab.data("istemp") ) {
              ipDockPanel.prototype.process.tab.flyout.hide( tab );
              return;
            }
            ipDockPanel.prototype.process.tab.add( tab, false, 'addToThread' );
            return false;
          });
          b.on("click",function(event) {
            event.preventDefault();
          });
        },
        uplFormInput: function( elm, id, type ) {
          var inputs  = unds.object([['groupn',type],['groupi',id],['channel','attachments'],['process','upload'],['source','frame']]);
          for( k in inputs ) {
            var v = inputs[k],
                a = elm.cn("input",{"type":"hidden","name":k,"value":v},false,'prepend');
          }
        }
      }
    },
    flexi: {
      nub: {
        holder: function( elm, data, callback ) {
          callback  = ( typeof callback === "function" ) ? callback : function(e){e.preventDefault()};
          var a = elm.cn("li",{"class":"uiMenuItem"}).data("target",data),
              b = a.cn("a",{"class":"itemAnchor","role":"menuitem","tabindex":0,"href":"#"}).on("click",callback),
              c = b.cn("span",{"class":"itemLabel fsm"},data.name);
        }
      }
    }
  }
});

var _gwl  = function( id, type ) { //getwritinglang
  var bnd_lng = ipga("languages");
  var lang  = bnd_lng.codes.w || "en";
  var langs = ipga("_gul") || ipsa("_gul",{});
  if ( langs[type] && langs[type][id] ) {
    lang  = langs[type][id];
  }
  return lang;
},
_glr=function(lang,word) { //getlanguagerevisions
  var revs  = ipga("_glr") || ipsa("_glr",{});
  var lrevs = revs[lang] || {};
  if ( lrevs[word] ) {
    return unds.without( lrevs[word], word );;
  }
  for( x in lrevs ) {
    if ( unds.indexOf( lrevs[x], word ) !== -1 ) {
      return unds.without( lrevs[x], word );
    }
  }
  return false;
},
_slr=function(lang,word,rev) { //getlanguagerevisions
  var revs  = ipga("_glr") || ipsa("_glr",{});
  revs[lang]  = revs[lang] || {};
  revs[lang][word]  = rev;
  return ipsa("_glr",revs);
},
_gtc=function( word, lang ) { //gettranslation cache
  var cache = ipga("_gtc") || ipsa("_gtc",{});
  return ( cache[lang] && cache[lang][word] ) ? cache[lang][word] : false;
},
_stc=function( word, lang, trans ) { //gettranslation cache
  var cache = ipga("_gtc") || ipsa("_gtc",{});
  cache[lang] = cache[lang] || {};
  cache[lang][word] = trans;
  return ipsa("_gtc",cache);
},
u_50x4  = function( group ) { //addChatGroup
  var groups  = ipga("groups") || ipsa("groups",{});
  groups[group.ID]  = group;
  ipsa("groups",groups);
  return group;
},
/** Groupinfo with Auto Callback **/
isr_50x4  = function( a ) { // Is getGroupInfo CB Running
  var b = ipga("r_50x4") || ipsa("r_50x4",{});
  return ( b && b[a] === true );
},
sir_50x4  = function( a ) { // Set getGroupInfo CB Running
  var b = ipga("r_50x4") || ipsa("r_50x4",{});
  b[a]  = true;
  return ipsa("r_50x4",b);
},
ccb_50x4  = function( a, b, c ) { // Call getGroupInfo CB
  var d = ipga("cb_50x4") || ipsa("cb_50x4",{});
  var e = d[a] || [];
  delete d[a];
  if ( unds.isEmpty( e ) || !unds.isArray( e ) ) {
    return;
  }
  for( f = 0; f < e.length; f++ ) {
    var g = e[f];
    if ( c === true ) {
      if ( typeof g[1] === "function" ) {
        g[2].unshift( b );
        g[2].push( g[3] );
        call_user_func_array( g[1], g[2] );
      }
      continue;
    }
    if ( typeof g[0] === "function" ) {
      if ( b.name && b.name.length ) {
        var args  = unds.clone( g[2] );
          args.unshift( b );
          args.push( g[3] );
        call_user_func_array( g[0], args );
        continue;
      }

      var name  = f_50x4( b );
      if ( g[3] !== true ) {
        b.name  = ( name[1].length ) ? false : name[0];
        u_50x4( b );
        var args  = unds.clone( g[2] );
            args.unshift( b );
            args.push( false );
        call_user_func_array( g[0], args );
      }

      if ( name[1].length ) {
        users_batch_call(name[1], function( group, callbacks, runnable ) {
          var name  = f_50x4( group );
          group.name  = name[0];
          u_50x4( group );
          g_50x4( group.ID, callbacks[0], callbacks[1], callbacks[2], true );
        }, [ b, g, d ]);
      }
    }
  }
  ipsa("cb_50x4",d);
},
acb_50x4  = function( a, b, c, d, e ) { // Add getGroupInfo CB
  var f = ipga("cb_50x4") || ipsa("cb_50x4",{});
  f[a]  = f[a] || [];
  f[a].push( [ b, c, d, e ] );
  ipsa("cb_50x4",f)
  return f[a];
},
g_50x4  = function( id, callback1, callback2, args, clone ) { //getChatGroup
  clone = ( clone === true ) ? true : false;
  args  = ( unds.isArray( args ) ) ? args : [];

  var groups  = ipga("groups") || ipsa("groups",{});
  var group   = groups[id];
  var users   = ipga("users");

  if ( group ) {
    if ( typeof callback1 !== "function" ) {
      return group;
    }
    else {
      var args1 = unds.clone( args );
          args1.unshift( group );
          args1.push( clone );
      call_user_func_array( callback1, args1 );
    }
    if ( unds.isEmpty( group.name ) && !clone ) {
      acb_50x4( id, callback1, callback2, args );
      ccb_50x4( id, group, false );
    }
  }
  else {
    if ( isr_50x4( id ) ) {
      acb_50x4( id, callback1, callback2, args, clone );
      return;
    }
    sir_50x4( id );
    acb_50x4( id, callback1, callback2, args, clone );
    if ( has_action( "IP_load_group_info" ) ) {
      do_action( "IP_load_group_info", true, id, function( id, response ) {
        if ( response.error ) {
          ccb_50x4( id, response.message, true );
          return;
        }
        u_50x4( response );
        ccb_50x4( response.ID, response, false );
      } );
      return;
    }
    ipqx( ipgo('docServer')+'ipChat/pull.php', "POST", {
      channel: 'messages',
      process: 'group',
      action: 'get',
      id: id
    }, {
      onsuccess: function( response ) {
        //acb_50x4( id, callback1, callback2, args, clone );
        if ( response.error ) {
          ccb_50x4( id, response.message, true );
          return;
        }
        u_50x4( response );
        ccb_50x4( response.ID, response, false );
      }
    });
  }
},
f_50x4 = function(b) { //findGroupName
  var a = [], c = [], e = ipga("users");
  for(x in b.users) {
    if(parseInt(b.users[x]) !== parseInt(ipga("user").ID)) {
      var d = e[b.users[x]];
      if(d) {
        if(a.push(d.NM), 3 <= a.length) {
          break
        }
      }else {
        c.push(b.users[x]), a.push("undefined")
      }
    }
  }
  2 === a.length ? a = a[0] + " and " + a[1] : 3 === a.length && 3 === b.users.length ? a = a[0] + ", " + a[1] + " and " + a[2] : (a = a.join(", "), 3 < b.users.length - 1 && (b = b.users.length - 4, a += 1 === b ? " and 1 other" : " and " + b + " others"));
  return[a, c]
},
// Chat First Degree
h_50mz = function(c, a) { //hasFirstDegree
  var b = ipga("_50mz") || ipsa("_50mz", {});
  return b[a] && b[a][c]
},
s_50mz = function(c, b) { //setFirstDegree
  var a = ipga("_50mz") || ipsa("_50mz", {});
  a[b] = a[b] || {};
  a[b][c] = !0;
  ipsa("_50mz", a);
  return!0
},
u_50mz = function(c, b) { //unsetFirstDegree
  var a = ipga("_50mz") || ipsa("_50mz", {});
  if(!a[b] || !a[b][c]) {
    return!0
  }
  delete a[b][c];
  ipsa("_50mz", a);
  return!0
},
// First Degree List Rendered
i_50mzr = function(c, a) { //isListRendered
  var b = ipga("_50mzr") || ipsa("_50mzr", {});
  return b[a] && b[a][c]
},
s_50mzr = function(c, b) { //setListRendered
  var a = ipga("_50mzr") || ipsa("_50mzr", {});
  a[b] = a[b] || {};
  a[b][c] = !0;
  ipsa("_50mzr", a);
  return!0
},
u_50mzr = function(c, b) { //unsetListRendered
  var a = ipga("_50mzr") || ipsa("_50mzr", {});
  if(!a[b] || !a[b][c]) {
    return!0
  }
  delete a[b][c];
  ipsa("_50mzr", a);
  return!0
},
// Upload Counter
get_kshc = function(a, b, c) { //getUploadCounter
  return set_kshc(a, b, c)
},
set_kshc = function(d, c, b, e) { //setUploadCounter
  var a = ipga("_kshc") || ipsa("_kshc", {});
  if(isNaN(parseInt(e)) && a[b] && a[b][c] && a[b][c][d]) {
    return a[b][c][d]
  }
  a[b] = a[b] || {};
  a[b][c] = a[b][c] || {};
  a[b][c][d] = a[b][c][d] || 0;
  isNaN(parseInt(e)) || (a[b][c][d] = e);
  ipsa("_kshc", a);
  return a[b][c][d]
},
inc_kshc = function(a, b, c) { //increaseUploadCounter
  var d = get_kshc(a, b, c);
  return set_kshc(a, b, c, d + 1)
},
dec_kshc = function(a, b, c) { //decreaseUploadCounter
  var d = get_kshc(a, b, c);
  return set_kshc(a, b, c, d - 1)
},
ucf_kshc = function(a, b, c) { //uploadCounterFinished
  return 0 === get_kshc(a, b, c)
},
uf_kshc = function(c, b) { //uploadsFinished
  var a = ipga("_kshc") || ipsa("_kshc", {});
  if(!a[b] || !a[b][c]) {
    return!0
  }
  a = a[b][c];
  return 0 === parseInt(unds.reduce(unds.clone(unds.values(a)), function(a, b) {
    return a + b
  }, 0)) ? (a = {}, !0) : !1
},
// Chat History
add_50dw  = function( idx, idn, idm ) { //addChatHistory
  var idy = ipga("_50dw") || ipsa("_50dw",{});
  idy[idn] = idy[idn] || {};
  idy[idn][idx] = idy[idn][idx] || {};
  if ( idm.ID ) {
    idy[idn][idx][idm.ID] = idm;
  }
  else {
    for( idz in idm ) {
      idy[idn][idx][idm[idz].ID] = idm[idz];
    }
  }
  return ipsa("_50dw",idy);
},
get_50dw  = function( idx, idn, ins ) { //getChatHistory
  var idy = ipga("_50dw") || ipsa("_50dw",{});
  if ( idy[idn] && idy[idn][idx] ) {
    rfc_50dwf( idx, idn );
    return ( !ins ) ? spl_50dw( idy[idn][idx] ) : idy[idn][idx];
  }
  return false;
},
spl_50dw  = function( idn ) { //spliceChatHistory
  if ( unds.isEmpty( idn ) || !unds.isObject( idn ) ) {
    return idn;
  }
  var keys  = unds.keys( idn ),
      lkeys = unds.last( keys, 20 );
  if ( !unds.isEmpty( lkeys ) ) {
    lkeys.unshift( idn );
    return call_user_func_array( [ unds, 'pick' ], lkeys );
  }
  return idn;
},
gspl_50dw = function( idx, idn, idj ) {
  var chats = get_50dw( idx, idn, true );
  if ( !chats ) {
    return false;
  }
  var keys  = unds.keys( chats ),
      index = unds.indexOf( keys, idj ),
      fkey  = unds.first( keys );
  if ( idj == fkey || idj == fc_50dwf( idx, idn ) ) {
    if ( idj == fc_50dwf( idx, idn ) ) {
      cf_50dwf( idx, idn );
    }
    return false;
  }
  if ( index == -1 ) {
    return false;
  }

  var initial = unds.last( keys.splice( 0, index ), 20 ),
      fkey    = unds.first( initial );
  if ( initial.length ) {
    initial.unshift( chats );
    initial = call_user_func_array( [ unds, 'pick' ], initial );
    return initial;
  }

  return false;
},
// No more older message
rfc_50dwf = function( idx, idn ) { //finishedChat
  var idy  = ipga("_50dwf") || ipsa("_50dwf",{});
  if ( idy && idy[idn] && idy[idn][idx] ) {
    delete idy[idn][idx];
    return true;
  }
  return false;
},
fc_50dwf = function( idx, idn ) { //finishedChat
  var idy  = ipga("_50dwf") || ipsa("_50dwf",{});
  return ( idy && idy[idn] && idy[idn][idx] ) ? idy[idn][idx] : false;
},
cf_50dwf  = function( idx, idn ) { //chatFinished
  var idy = ipga("_50dwf") || ipsa("_50dwf",{}),
      idj = unds.first( unds.keys( get_50dw( idx, idn ) || {} ) );
  idy[idn]  = idy[idn] || {};
  idy[idn][idx] = idj;
  return ipsa("_50dwf",idy);
},
// Tab Attachments
atam = function(c, b, d) { // add attachment
  var a = ipga("_ksh_rpb") || ipsa("_ksh_rpb", {});
  a[b] = a[b] || {};
  a[b][c] = a[b][c] || {};
  a[b][c][d.ID] = d;
  ipsa("_ksh_rpb", a);
  return d
},
htam = function(c, a, d) { // has attachment
  var b = ipga("_ksh_rpb") || ipsa("_ksh_rpb", {});
  return c ? d ? b[a] && b[a][c] && b[a][c][d] : b[a] && b[a][c] && !unds.isEmpty(b[a][c]) : b[a] && !unds.isEmpty(b[a])
},
gtam = function(a, b, c) { // get attachment
  if(!htam(a, b)) {
    return!0 === c ? [] : {}
  }
  var d = ipga("_ksh_rpb") || ipsa("_ksh_rpb", {});
  return!0 === c ? unds.keys(d[b][a]) : d[b][a]
},
dtam = function(c, d, e) { // delete attachment
  if(!htam(c, d, e)) {
    return!0
  }
  var a = ipga("_ksh_rpb") || ipsa("_ksh_rpb", {}), b = [];
  if(c) {
    e ? (b.push(e), delete a[d][c][e]) : (b.push(unds.keys(a[d][c])), delete a[d][c])
  }else {
    for(x in a) {
      for(y in a[x]) {
        b.push(unds.keys(a[x][y]))
      }
    }
    delete a[d]
  }
  b = unds.flatten(b);
  if ( has_action( "IP_onuplaod_delete" ) ) {
    do_action( "IP_onuplaod_delete", true, b );
  }
  else {
    $.post(ipgo("docServer") + "ipChat/pull.php", {channel:"attachments", process:"attachment", action:"delete", idx:b}, !1, "json");
  }
  ipsa("_ksh_rpb", a);
  return!0
},
ctam  = function( a, b ) { // clear attachments
  var c = ipga("_ksh_rpb") || ipsa("_ksh_rpb", {});
  if ( c[b] && c[b][a] ) {
    delete c[b][a];
  }
  return ipsa("_ksh_rpb",c);
},
// Message Auto Sender
tmas  = function( idx, idn, idy ) { // add auto sender
  var idz = ipga("mas") || ipsa("mas",{});
  idz[idn]  = idz[idn] || {};
  idz[idn][idx] = idy;
  return ipsa("mas",idz);
},
hmas  = function( idx, idn ) { // has auto sender
  var idz = ipga("mas") || ipsa("mas",{});
  return( idz[idn] && ( idz[idn][idx] instanceof jQuery ) && idz[idn][idx].length );
},
rmas  = function( idx, idn ) { // remove auto sender
  var idz = ipga("mas") || ipsa("mas",{});
  if ( hmas( idx, idn ) ) {
    delete idz[idn][idx];
  }
  return ipsa("mas",idz);
},
isfbd = function( a, b, c ) {
  a = $.trim( a.toString().toLowerCase() );
  b = $.trim( b.toString().toLowerCase() );
  d = ipga( "settings" ).blocked_files || {};
  e = ipga( "settings" ).blocked_files_mode;

  d = ( !( unds.isObject( d ) ) ) ? {} : d;
  d.extn  = ( d.extn && unds.isArray( d.extn ) ) ? d.extn : [];
  d.mime  = ( d.mime && unds.isArray( d.mime ) ) ? d.mime : [];

  if ( ( !a && !b ) || ( c && !a ) ) {
    return false;
  }
  if ( e === "blacklist" ) {
    if ( $.inArray( a, d.extn ) !== -1 ) {
      return "extn";
    }
    if ( $.inArray( b, d.mime ) !== -1 ) {
      return "mime";
    }
  }
  else {
    if ( $.inArray( a, d.extn ) === -1 ) {
      return "extn";
    }
    if ( $.inArray( b, d.mime ) === -1 ) {
      return "mime";
    }
  }
  return false;
},
sanitize_msg  = function( message ) {
  plaintext = $.trim( $('<div />').html( message ).text() );
  return plaintext || message;
};