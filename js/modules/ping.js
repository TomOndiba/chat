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
*/if ( !window.EventSource ) {
  (function(u){function e(){this.data={}}e.prototype={get:function(B){return this.data[B+"~"]},set:function(B,C){this.data[B+"~"]=C},"delete":function(B){delete this.data[B+"~"]}};function k(){this.listeners=new e()}function w(B){setTimeout(function(){throw B},0)}k.prototype={dispatchEvent:function(G){G.target=this;var E=String(G.type);var D=this.listeners;var B=D.get(E);if(!B){return}var F=B.length;var C=-1;var H=null;while(++C<F){H=B[C];try{H.call(this,G)}catch(I){w(I)}}},addEventListener:function(E,F){E=String(E);var D=this.listeners;var B=D.get(E);if(!B){B=[];D.set(E,B)}var C=B.length;while(--C>=0){if(B[C]===F){return}}B.push(F)},removeEventListener:function(F,H){F=String(F);var E=this.listeners;var B=E.get(F);if(!B){return}var G=B.length;var C=[];var D=-1;while(++D<G){if(B[D]!==H){C.push(B[D])}}if(C.length===0){E["delete"](F)}else{E.set(F,C)}}};function A(B){this.type=B;this.target=null}function j(C,B){A.call(this,C);this.data=B.data;this.lastEventId=B.lastEventId}j.prototype=A.prototype;var n=u.XMLHttpRequest;var c=u.XDomainRequest;var s=Boolean(n&&((new n()).withCredentials!==undefined));var l=s;var y=s?n:c;var t=-1;var m=0;var v=1;var d=2;var q=3;var i=4;var r=5;var g=6;var f=7;var h=/^text\/event\-stream;?(\s*charset\=utf\-8)?$/i;var p=1000;var b=18000000;function z(C,B){var D=Number(C)||B;return(D<p?p:(D>b?b:D))}function x(C,D,B){try{if(typeof D==="function"){D.call(C,B)}}catch(E){w(E)}}function a(H,F){H=String(H);var E=Boolean(s&&F&&F.withCredentials);var N=z(F?F.retry:NaN,1000);var J=z(F?F.heartbeatTimeout:NaN,45000);var K=(F&&F.lastEventId&&String(F.lastEventId))||"";var M=this;var W=N;var O=false;var L=new y();var P=0;var Q=0;var X=0;var aa=t;var Z=[];var B="";var V="";var U=null;var G=i;var C="";var S="";F=null;function R(){aa=d;if(L!==null){L.abort();L=null}if(P!==0){clearTimeout(P);P=0}if(Q!==0){clearTimeout(Q);Q=0}M.readyState=d}function D(ak){var ah=aa===v||aa===m?L.responseText||"":"";var ab=null;var ac=false;if(aa===m){var af=0;var ae="";var al="";if(l){try{af=Number(L.status||0);ae=String(L.statusText||"");al=String(L.getResponseHeader("Content-Type")||"")}catch(aj){af=0}}else{af=200;al=L.contentType}if(af===200&&h.test(al)){aa=v;O=true;W=N;M.readyState=v;ab=new A("open");M.dispatchEvent(ab);x(M,M.onopen,ab);if(aa===d){return}}else{if(af!==0){var am="";if(af!==200){am="EventSource's response has a status "+af+" "+ae.replace(/\s+/g," ")+" that is not 200. Aborting the connection."}else{am="EventSource's response has a Content-Type specifying an unsupported type: "+al.replace(/\s+/g," ")+". Aborting the connection."}setTimeout(function(){throw new Error(am)});ac=true}}}if(aa===v){if(ah.length>X){O=true}var ag=X-1;var ad=ah.length;var ai="\n";while(++ag<ad){ai=ah[ag];if(G===q&&ai==="\n"){G=i}else{if(G===q){G=i}if(ai==="\r"||ai==="\n"){if(C==="data"){Z.push(S)}else{if(C==="id"){B=S}else{if(C==="event"){V=S}else{if(C==="retry"){N=z(S,N);W=N}else{if(C==="heartbeatTimeout"){J=z(S,J);if(P!==0){clearTimeout(P);P=setTimeout(U,J)}}}}}}S="";C="";if(G===i){if(Z.length!==0){K=B;if(V===""){V="message"}ab=new j(V,{data:Z.join("\n"),lastEventId:B});M.dispatchEvent(ab);if(V==="message"){x(M,M.onmessage,ab)}if(aa===d){return}}Z.length=0;V=""}G=ai==="\r"?q:i}else{if(G===i){G=r}if(G===r){if(ai===":"){G=g}else{C+=ai}}else{if(G===g){if(ai!==" "){S+=ai}G=f}else{if(G===f){S+=ai}}}}}}X=ad}if((aa===v||aa===m)&&(ak||ac||(X>1024*1024)||(P===0&&!O))){aa=t;L.abort();if(P!==0){clearTimeout(P);P=0}if(W>N*16){W=N*16}if(W>b){W=b}P=setTimeout(U,W);W=W*2+1;M.readyState=m;ab=new A("error");M.dispatchEvent(ab);x(M,M.onerror,ab)}else{if(P===0){O=false;P=setTimeout(U,J)}}}function T(){D(false)}function I(){D(true)}if(l){Q=setTimeout(function Y(){if(L.readyState===3){T()}Q=setTimeout(Y,500)},0)}U=function(){P=0;if(aa!==t){D(false);return}if(l&&(L.sendAsBinary!==undefined||L.onloadend===undefined)&&u.document&&u.document.readyState&&u.document.readyState!=="complete"){P=setTimeout(U,4);return}L.onload=L.onerror=I;if(l){L.onabort=I;L.onreadystatechange=T}L.onprogress=T;O=false;P=setTimeout(U,J);X=0;aa=m;Z.length=0;V="";B=K;S="";C="";G=i;var ab=H.slice(0,5);if(ab!=="data:"&&ab!=="blob:"){ab=H+((H.indexOf("?",0)===-1?"?":"&")+"lastEventId="+encodeURIComponent(K)+"&r="+String(Math.random()+1).slice(2))}else{ab=H}L.open("GET",ab,true);if(l){L.withCredentials=E;L.responseType="text";L.setRequestHeader("Accept","text/event-stream")}L.send(null)};k.call(this);this.close=R;this.url=H;this.readyState=m;this.withCredentials=E;this.onopen=null;this.onmessage=null;this.onerror=null;U()}function o(){this.CONNECTING=m;this.OPEN=v;this.CLOSED=d}o.prototype=k.prototype;a.prototype=new o();o.call(a);if(y){u.NativeEventSource=u.EventSource;u.EventSource=a}}(this));
};
var ipPing  = ipChat.extend({
  isConnectFirst: true,
  socketInterval: false,
  notifInterval: false,
  socketPingId: false,
  socketTyping: {},

  initialize: function() {
    if ( !window.ipExtend ) {
      throw new Error( "'ipExtend' does not exists in window" );
    }
    if ( !this.intervalRunning ) {
      this.intervalRunning  = true;
      this.findConnector();
    }

    return this;
  },
  /** Finding which COnnector is suitable **/
  findConnector: function() {
    if ( this.socket !== false ) {
      this.connectToWebSocket();
    }
    else {
      this.executeConnection();
    }
  },
  /** Execute native connections when WbSocket fails **/
  executeConnection: function() {
    if ( !!window.EventSource ) {
      this.connectEventSource();
    }
    else {
      setTimeout( this.startInterval, 5000 );
    }
  },
  /** Connecting using WebSockets **/
  connectToWebSocket: function() {
    var thisArg = this;
    if ( window.MozWebSocket ) {
      window.WebSocket  = window.MozWebSocket;
    }

    if ( !window.WebSocket ) {
      thisArg.executeConnection();
      thisArg.isConnectFirst = false;
      return;
    }

    var socket = new WebSocket( sprintf( "%s://%s:%d/", ( ( ipgo( "secureWebSocket" ) === true ) ? "wss" : "ws" ), this.socket[0], this.socket[1] ) );
    
    if ( thisArg.socketInterval != false ) {
      clearInterval( this.socketInterval );
      clearInterval( this.notifInterval );
    }
    this.socketPingId = uniqid();
    var socketPing = function() {
      $.post( ipgo('docServer')+'ipChat/pull.php?notif='+time(), {
        channel: 'ping',
        process: 'notif',
        ping_id: thisArg.socketPingId,
        tabs: ipPing.prototype.parse.tabs()
      }, function( response ) {
        if ( response.error ) {
          return;
        }
        if ( !ipPing.prototype.parse.notif( response ) ) {
          var bubble  = $(".ui-notifications span._51jx:first");
          ipChat.prototype.play_audio( "notifSound" );
          bubble.removeClass("animated bounce").addClass("animated bounce");
          setTimeout(function() {
            bubble.removeClass("animated bounce");
          }, 2000);
        }
      }, "json" );
    };
    var sockReconnect = function() {
      socket.send( json_encode( {
		    event: "reconnect",
        user_id: ipga( "user" ).ID
      } ) );
    };

		socket.onopen = function( message ) {
		  thisArg.isConnectFirst = false;
		  ipWebSocket = this;

      var user = ipga( "user" );
		  this.send( json_encode( {
		    event: "connect",
        user_id: user.ID
      } ) );

      thisArg.socketInterval  = setInterval( sockReconnect, 10000 );
      thisArg.notifInterval = setInterval( socketPing, 30000 );
      sockReconnect();
      socketPing();
    };
		socket.onclose  = function( message ) {
      if ( thisArg.isConnectFirst ) {
        thisArg.executeConnection();
        thisArg.isConnectFirst = false;
        return;
      }
      clearInterval( thisArg.socketInterval );
      clearInterval( thisArg.notifInterval );
    };
		socket.onmessage  = function( event ) {
		  try {
		    var message = json_decode( event.data );
        if ( !message || !message.hasOwnProperty( "event" ) ) {
          throw new Error( "invalid message format, event is required" );
        } 
        switch( message.event ) {
          case "connected":
            g_50x5( message.user, function( user ) {
              ipUsers.prototype.set_online( user );
            }, function() {} );
          break;
          case "disconnected":
            g_50x5( message.user, function( user ) {
              ipUsers.prototype.set_offline( user );
            }, function() {} );
          break;
          case "message":
            var idx = ( !message.groupID || message.groupID === 0 ) ? message.sent_from : message.groupID;
            var idn = ( !message.groupID || message.groupID === 0 ) ? "user" : "group";

            ipUsers.prototype.dock( function( idx, idn, message ) {
              var tab = ipDockPanel.prototype.process.tab.open( idx, idn, false, true, true );
              if ( tab && tab.length ) {
                if ( !tab.hasClass("opened") ) {
                  if ( ipChat.prototype.can_play( idx, idn ) ) {
                    ipChat.prototype.play_audio( "messageSound" );
                  }
                  var counter = $("._51jx:first",tab).removeClass("hidden_elem"),
                      count   = add_alert( idx, idn, message.ID );
                  counter.text( count );
                  tab.data( "hasMessage", true );
                }
                ipDockPanel.prototype.process.chat.messages.add( tab, message );
              }
            }, [ idx, idn, message ] );

            this.send( json_encode( {
              event: "seen",
              idx: idx,
              idn: idn,
              group: ( idn === "group" ) ? g_50x4( idx ) : false,
              user: ipga("user").ID
            } ) );
            break;
          case "blocked":
            var user_id = message.user;
            var users = ipga("users");
            delete users[user_id];
            ipsa("users",users);
  
            var users = ipga("users_base");
            delete users[user_id];
            ipsa("users_base",users);

            var tab = $("#userNub"+user_id);
            if ( typeof ipDockPanel === "function" ) {
              ipDockPanel.prototype.process.tab.close( tab, false, true );
              ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );
            }

            ipUsers.prototype.user_dock.list( $("ul.ipChatOrderedList"), users, true );
            break;
          case "unblocked":
            var user_id = message.user;

            setTimeout(function() {
              g_50x5( user_id, function( user ) {
                ipUsers.prototype.user_dock.list( $("ul.ipChatOrderedList"), ipga("users"), true );
              }, function() {}, [], true );
            }, 1000 );
            break;
          case "status":
            users_batch_call( message.user, function( user_id ) {
              ipUsers.prototype.set_online( ipga("users")[user_id] );
            }, [ message.user ], true );
            break; 
          case "typing":
             thisArg.showTyping( message.user, message.idx, message.idn );
            break;
          case "seen":
            var tab = $("#"+message.idn+"Nub"+message.idx);
            if ( !tab.length ) {
              return;
            }
            var text = "Seen "+date( "h:i A", time() );
            var conv = $("._510g",tab);
            if ( !conv.hasClass("seen") ) {
              conv.addClass("seen");
              $("._510h",conv).addClass("icon-time");
            }
            if ( $("._510f",conv).html() != text ) {
              $("._510f",conv).html( text );
              if ( !tab.data("preventScroll") ) {
                $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
              }
            }
            break;
        }
		  }
      catch( e ) {
        console.error( e.stack );
      }
    };
  },
  /** Connecting using EventSource **/
  connectEventSource: function() {
    console.log( "Handler: connectEventSource" );
    var that  = this,
        timerMin  = 2000,
        timerMax  = 80000,
        timerCur  = 0;
    function EventSourceHandler() {
      var query   = {
        pingEvent: true,
        eventSource: true,
        ping_id: ipPing.prototype.pingID,
        tabs: ipPing.prototype.parse.tabs(),
        charset_test: '€,´,€,´,水,Д,Є',
        request_id: "xhr-"+unds.uniqueId(),
        request_sd: $.cookie( "PHPSESSID" )
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
        ipPing.prototype.parse.chat( data );
      }, false );
      source.addEventListener( "status", function( event ) {
        timerCur  = 0;
        var data  = json_decode( event.data );
        ipPing.prototype.parse.status( data );
      }, false );
      source.addEventListener( "seen", function( event ) {
        timerCur  = 0;
        var data  = json_decode( event.data );
        ipPing.prototype.parse.seen( data );
      }, false );
      source.addEventListener( "users", function( event ) {
        var data  = json_decode( event.data );
        ipPing.prototype.parse.users( data );
      }, false );
      source.addEventListener( "notifications", function( event ) {
        var data  = json_decode( event.data );
        if ( !ipPing.prototype.parse.notif( data ) ) {
          var bubble  = $(".ui-notifications span._51jx:first");
          ipChat.prototype.play_audio( "notifSound" );
          bubble.removeClass("animated bounce").addClass("animated bounce");
          setTimeout(function() {
            bubble.removeClass("animated bounce");
          }, 2000);
        }
      }, false );
    }
    EventSourceHandler();
  },
  pingID: uniqid(),
  startInterval: function() {
    ipPing.prototype.ping.start();
  },
  handler: {
    time: {},
    count: {},
    handle: function( idx, idn, idm ) {
      if ( this.count[idx] >= 5 ) {
        this.time[idx]  = this.time[idx] || 0;
        this.time[idx]  +=  1000;
        this.count[idx] = 0;

        if ( idm && !idm.error ) {
          call_user_func_array( idn, [ idm ] );
        }
        //console.log( idx+" error timeout "+this.time[idx] );
        setTimeout(function() {
          ipPing.prototype.ping.start( idx );
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
      ipPing.prototype.ping.start( idx );
      //ipPing.prototype.ping.start( idx );
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
      var idn = {'users':100000,'chat':5000,'seen':10000,'notif':30000,'status':20000};
      //var idn = {'seen':10000};
      if ( ipga("ptl") ) {
        return ipga("ptl");
      }
      return ipsa("ptl",idn);
    }
  },
  ping: {
    start: function( idx ) {
      if ( idx ) {
        var idt = ipPing.prototype.timeout.get( idx );
        var idm = ipga("ping") || ipsa("ping",{});
        if ( parseInt( idt ) === 1 ) {
          call_user_func_array( this[idx] );
        }
        else {
          idm[idx]  = setTimeout( this[idx], idt );
        }
        return;
      }
      var idm = ipga("ping") || ipsa("ping",{});
      var idn = ipPing.prototype.timeout.list();
      for( i in idn ) {
        if ( !idm[i] ) {
          idm[i]  = setTimeout( this[i], idn[i] );
          this[i]();
        }
      }
      ipsa("ping",idm);
    },
    users: function() {
      ipPing.prototype.request( 'users', ipPing.prototype.parse.users );
    },
    chat: function() {
      ipPing.prototype.request( 'chat', ipPing.prototype.parse.chat );
    },
    seen: function() {
      ipPing.prototype.request( 'seen', ipPing.prototype.parse.seen );
    },
    notif: function() {
      ipPing.prototype.request( 'notif', ipPing.prototype.parse.notif );
    },
    status: function() {
      ipPing.prototype.request( 'status', ipPing.prototype.parse.status );
    },
    destroy: function( idx ) {
      
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
          ping_id: ipPing.prototype.pingID,
          tabs: ipPing.prototype.parse.tabs(),
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
        ipPing.prototype.handler.handle( idx, idn, response );
      });
      xhr.fail(function( jqXHR, textStatus, errorThrown ) {
        idm[idx]  = false;
        ipsa("pings",idm);
        ipPing.prototype.handler.handle( idx, idn, false );
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
      ping_id: ipPing.prototype.pingID,
      tabs: ipPing.prototype.parse.tabs()
    });
    xhr.callback({
      onsuccess: function( response ) {
        idm[idx]  = false;
        ipsa("pings",idm);
        ipPing.prototype.handler.handle( idx, idn, response );
      },
      onerror: function( response, error ) {
        idm[idx]  = false;
        ipsa("pings",idm);
        ipPing.prototype.handler.handle( idx, idn, false );
      },
      onloadend: function() {
        
      }
    });
    xhr.send();
  },
  showTyping: function( user, idx, idn ) {
    var tab = $("#"+idn+"Nub"+idx),
        thisArg = this;
    if ( !tab.length || !tab.is(":visible") ) {
      return;
    }
    g_50x5( user, function( u, t ) {
      if ( thisArg.socketTyping.hasOwnProperty( idn ) && thisArg.socketTyping[idn].hasOwnProperty( idx ) ) {
        clearTimeout( thisArg.socketTyping[idn][idx] );
      }
      thisArg.socketTyping[idn] = thisArg.socketTyping[idn] || {};

      if ( t.hasClass("closed") ) {
        var a = $(".ipNubButton:first",t);
        $("._51lqc",a).remove();

        var b = $(".name:first",a),
            c = $("<span />",{"class":"icon-comment-alt _51lqc"});
        c.prependTo( b );
      }
      else {
        var a = $("._51lq:first",t).addClass("typing");
        a.html( '<span class="icon-comment-alt"></span> <span>'+sprintf( L.USER_TYPING, fname( u.NM ) )+'&hellip;</span>' );
        $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
      }
 
      thisArg.socketTyping[idn][idx] = setTimeout( function() {
        $("._51lqc",t).remove();
        if ( $("._51lq:first",t).hasClass("typing") ) {
          $("._51lq:first",t).removeClass("typing").empty();
        }
      }, 1000 );
    }, function( t ) {}, [ tab ] );
  },
  parse: {
    tabs: function() {
      var tabs  = ipga("tabs") || ipsa("tabs",{});
          tabs  = unds.keys( tabs );
      data  = [];
      if ( tabs.length ) {
        for( i = 0; i < tabs.length; i++ ) {
          var uuid  = tabs[i].match( /\d+/gi );
              uuid  = uuid[0];
          var split = [ tabs[i].replace( uuid, "" ), uuid ];
          var chatb = $("#"+split[0]+"Nub"+split[1]);
          var opend = ( chatb.length && chatb.hasClass("opened") );
          var luser = $("._kso:last",chatb).data("message");
          var lmsg  = 0;
          if ( !luser ) {
            luser = 0;
          }
          else {
            lmsg  = parseInt( luser.ID );
            luser = parseInt( luser.sent_from );
          }
          data.push( [ split[1], split[0], opend, luser, lmsg ] );
        }
      }
      return data;
    },
    users: function( response ) {
      if ( !response || unds.isEmpty( response ) ) {
        return;
      }
      ipsa("users_base", response);
      if ( !$(".ipChatTypeahead.uiTypeaheadHasText").length ) {
        ipUsers.prototype.user_dock.list( $("ul.ipChatOrderedList"), response, true );
      }
    },
    chat: function( response, callback ) {
      var messages  = { "user" : {}, "group" : {} };

      if ( response ) {
        for( x in response ) {
          var message = response[x];
          var idx = ( message.groupID === 0 || !message.groupID ) ? message.sent_from : message.groupID;
          var idn = ( message.groupID === 0 || !message.groupID ) ? "user" : "group";
          ipUsers.prototype.dock( function( idx, idn, message ) {
            messages[idn][idx]  = messages[idn][idx] || {};
            messages[idn][idx]["m"] = messages[idn][idx]["m"] || {};
            messages[idn][idx]["m"][message.ID] = message;

            var tab = ipDockPanel.prototype.process.tab.open( idx, idn, false, true, true );
            if ( tab && tab.length ) {
              if ( !tab.hasClass("opened") ) {
                messages[idn][idx]["o"] = false;
                if ( ipChat.prototype.can_play( idx, idn ) ) {
                  ipChat.prototype.play_audio( "messageSound" );
                }
                var counter = $("._51jx:first",tab).removeClass("hidden_elem"),
                    count   = add_alert( idx, idn, message.ID );
                counter.text( count );
                tab.data("hasMessage",true);
              }
              else {
                messages[idn][idx]["o"] = true;
              }
              ipDockPanel.prototype.process.chat.messages.add( tab, message );
            }
          }, [ idx, idn, message ] );
        }

        if ( is_callable( callback ) ) {
          for( type in messages ) {
            for( id in messages[type] ) {
              call_user_func_array( callback, [ id, type, messages[type][id].m, messages[type][id].o ] );
            }
          }
        }
      }
    },
    seen: function( response, itab ) {
      var seen  = response.s;
      var typ   = response.t;

      if ( itab && ( itab instanceof jQuery ) && itab.length ) {
        var conv  = $("._510g",itab);
        if ( conv.hasClass("seen") ) {
          conv.removeClass("seen");
          $("._510h",conv).removeClass("icon-time");
          $("._510f",conv).empty();
          if ( !itab.data("preventScroll") ) {
            $(".ipNubFlyoutBody",itab).ipscroll("scrollToBottom");
          }
        }
      }
      else {
        if ( !unds.isEmpty( seen ) ) {
          if ( $(".ipNubTabGroup .ipNub").length ) {
            for( var i = 0; i < seen.length; i++ ) {
              var seen  = seen[i];
              var tab   = $("#"+seen[0]+"Nub"+seen[1]);
              if ( !tab.length ) {
                continue;
              }
              var conv  = $("._510g",tab);
              if ( !seen[2] ) {
                if ( conv.hasClass("seen") ) {
                  conv.removeClass("seen");
                  $("._510h",conv).removeClass("icon-time");
                  $("._510f",conv).empty();
                  if ( !tab.data("preventScroll") ) {
                    $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
                  }
                }
              }
              else {
                var text  = "Seen "+seen[2];
                if ( !conv.hasClass("seen") ) {
                  conv.addClass("seen");
                  $("._510h",conv).addClass("icon-time");
                }
                if ( $("._510f",conv).html() != text ) {
                  $("._510f",conv).html( text );
                  if ( !tab.data("preventScroll") ) {
                    $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
                  }
                }
              }
            }
          }
        }
      }

      $("._51lq").removeClass("typing").html('');
      $("._51lqc").remove();

      if ( !unds.isEmpty( typ ) ) {
        for( x in typ ) {
          var tab = $("#userNub"+x);
          if ( !tab.length || !tab.is(":visible") ) {
            continue;
          }
          g_50x5( x, function( u, t ) {
            var f = function( a ) {
              var b = a.split( ' ' );
              return ( b[0] || a );
            };
            if ( tab.hasClass("closed") ) {
              var a = $(".ipNubButton:first",t),
                  b = $(".name:first",a),
                  c = $("<span />",{"class":"icon-comment-alt _51lqc"});
              c.prependTo( b );
            }
            else {
              var a = $("._51lq:first",t).addClass("typing");
              a.html( '<span class="icon-comment-alt"></span> <span>'+sprintf( L.USER_TYPING, f( u.NM ) )+'&hellip;</span>' );
              $(".ipNubFlyoutBody",tab).ipscroll("scrollToBottom");
            }
          }, function( t ) {
            
          }, [ tab ] );
        }
      }
    },
    notif: function( response ) {
      if ( unds.isEmpty( response ) ) {
        return;
      }
      var notif = ipga("notifications") || ipsa("notifications",{});
      notif = $.extend( {}, notif, response );
      ipsa("notifications",notif);
      ipUsers.prototype.user_dock.notifications.render();
    },
    status: function( response ) {
      var status_icons  = $(".status-icon").removeClass("status-online status-empty status-offline status-busy status-idle");
      if ( unds.isEmpty( response ) || response.t === "continue" ) {
        $(".status_icons").not(".status-empty").addClass("status-empty");
        $(".ipNub").removeClass("online busy idle").addClass("offline");
        $("._42fz.online, ._42fz.busy, ._42fz.idle").removeClass("online busy idle").addClass("offline").find(".active_time").text('');
        return;
      }

      var users = ipga("users");
      var tabs  = $(".ipNubTabGroup .ipNub");

      unds.each(response, function( status ) {
        var ostatus = ( status.SA || status.ST );
        var chatTab = $("#userNub"+status.ID);
        var user_li = $("._42fz"+status.ID);

        if ( tabs.length ) {
          if ( chatTab.length ) {
            if ( ostatus === "busy" ) {
              dbt( chatTab, "permanent" );
            }
            else {
              abt( chatTab );
            }
            chatTab.removeClass("online busy idle offline").addClass( ostatus );
            $(status_icons,chatTab).removeClass("status-empty status-online status-offline status-busy status-idle").addClass("status-"+ostatus);
          }
        }
        user_li.removeClass("online busy idle offline").addClass(ostatus).find(".active_time").text( timeDifference( status.LS, false, false, true ) ).attr( "timestamp", status.LS );

        if ( users[status.ID] ) {
          users[status.ID].SD = status.SD;
          users[status.ID].SA = status.SA;
          users[status.ID].ST = status.ST;
          users[status.ID].LS = status.LS;
        }
      });

      ipsa("users",users);
    }
  }
});

var sockInst  = false;
var ipSocket  = ipPing.extend({
  host: false,
  conn: false,
  sess: false,
  initialize: function( url ) {
    this.host = url;
    this.conn = io.connect( this.host );
    this.sess = this.conn.socket;

    if ( !!window.EventSource ) {
      var source = new EventSource( ipgo("docServer")+"ipChat/pull-event.php?node=1&eventSource=1" );
      source.addEventListener( "userlogon", function( e ) {
        var users = json_decode( e.data );
        ipPing.prototype.parse.users( users );
      }, false );
      source.addEventListener( "notification", function( e ) {
        var notif = json_decode( e.data );
        ipPing.prototype.parse.notif( notif );
      }, false );
      source.addEventListener( "open", function( e ) {
        //console.log( "Connection was opened." );
      }, false );
      source.addEventListener( "error", function( e ) {
        if ( e.readyState == EventSource.CLOSED ) {
          //console.log( "Connection was closed." );
        }
      }, false );
    }
    else {
      
    }

    var seenTime  = function( id, type, onsuccess, onerror ) {
      var xhr = new ipXhr;
      xhr.open( ipgo('docServer')+'ipChat/pull.php?'+id+"="+time() );
      xhr.params({
        channel: 'messages',
        process: 'message',
        action: 'addseen',
        idx: id,
        idn: type,
      });
      xhr.send();
      is_callable( onsuccess ) && onsuccess( id, type );
    };

    sockInst  = this;

    $(window).on("unload", function() {
      sockInst.conn.emit( "disconnect", ipga("user").ID );
    });
    this.conn.on( "connect", function() {
      sockInst.conn.emit( "online", ipga("user").ID );
      $.post( ipgo('docServer')+'ipChat/pull.php?'+ipga("user").ID+"="+time(), { ping : 'status' }, false, "json" );
    });
    this.conn.on( "offline", function( offline_id ) {
      
    });
    this.conn.on( "online", function( sockets, offline_id ) {
      ipChat.prototype.sockets  = sockets;
      var user_id = unds.keys( ipChat.prototype.sockets );
          user_id = unds.without( user_id, ipga("user").ID.toString() );
      if ( offline_id ) {
        if ( ipga("users")[offline_id] ) {
          ipga("users")[offline_id].ST  = "offline";
        }
      }
      users_batch_call( user_id, function( response ) {
        var users = unds.pick( ipga("users"), user_id );
        ipPing.prototype.parse.status( users );
      } );
    });
    this.conn.on( "IP loadSeen", function( idx, idn ) {
      var xhr = new ipXhr;
      xhr.open( ipgo('docServer')+'ipChat/pull.php?'+idx+"="+time() );
      xhr.params({
        channel: 'messages',
        process: 'message',
        action: 'getseen',
        idx: idx,
        idn: idn,
      });
      xhr.callback({
        onsuccess: function( response ) {
          if ( response.seen ) {
            var data  = { s: [], t: {} };
            data.s.push( [ idn, idx, response.seen ] );
            ipPing.prototype.parse.seen( data );
          }
        }
      });
      xhr.send();
    });
    this.conn.on( "IP Message", function( message ) {
      var messages  = {};
          messages[message.ID]  = message;
      ipPing.prototype.parse.chat( messages, function( id, type, messages, is_open ) {
        if ( is_open ) {
          seenTime( id, type, function( id, type ) {
            sockInst.conn.emit( "IP loadSeen", ( ( type === "group" ) ? message.sent_from : message.sent_to ), id, type );
          });
        }
      } );
    });
    this.conn.on( "IP Typing", function( id, time ) {
      var data  = {
        s: false,
        t: {}
      };
      data.t[id]  = time;
      ipPing.prototype.parse.seen( data );
      data.t  = {};
      setTimeout(function() {
        ipPing.prototype.parse.seen( data );
      }, 3000);
    });
    this.conn.on( "IP User Status", function( target, status, each ) {
      if ( ipga("users")[target] ) {
        ipga("users")[target].SA  = status;
        ipga("users")[target].ST  = status;
        ipga("users")[target].LS  = time();
        var users = {};
        users[ipga("users")[target].ID] = ipga("users")[target];
        ipPing.prototype.parse.status( users );
      }
    } );
    this.conn.on( "IP Block User", function( id ) {
      if ( ipga("users")[id] ) {
        ipUsers.prototype.dock(function( idx ) {
          ipDockPanel.prototype.process.tab.close( idx, "user", true );
          ipDockPanel.prototype.process.responsive.tab.resizer.window( false, true );

          var users = ipga("users");
          delete users[idx];
          ipsa("users",users);

          var users = ipga("users_base");
          delete users[idx];
          ipsa("users_base",users);

          ipUsers.prototype.user_dock.list( $("ul.ipChatOrderedList"), users, true );
        }, [ id ] );
      }
    } );

    add_action( "on_after_send_message", this.chat );
    add_action( "on_after_send_notice", this.chat );
    add_action( "IP_ontyping", this.typing );
    add_action( "onview_unreaded_message", seenTime );
    add_action( "onupdate_chat_status", this.status );
    add_action( "onupdate_chat_status_adv", this.adv_status );
    add_action( "onblock_user", this.block )
  },
  chat: function( message ) {
    var is_user = ( parseInt( message.groupID ) === 0 || isNaN( parseInt( message.groupID ) ) ) ? true : false;
      var sent_to = [];
      if ( is_user ) {
        sockInst.conn.emit( "IP Message", message, [ message.sent_to ] );
      }
      else {
        g_50x4( message.groupID, function( group ) {
          var users = unds.without( group.avail, ipga("user").ID );
          sockInst.conn.emit( "IP Message", message, users );
        }, false, true );
      }
  },
  typing: function( id, type, time ) {
    sockInst.conn.emit( "IP Typing", ipga("user").ID, id, time );
    return false;
  },
  status: function( target, status ) {
    sockInst.conn.emit( "IP User Status", ipga("user").ID, target, status, !target );
  },
  adv_status: function( status, tokens ) {
    tokens  = ( !unds.isArray( tokens ) || unds.isEmpty( tokens ) ) ? false : tokens;
    if ( status === "offline" || status === "whitelist" ) {
      sockInst.conn.emit( "IP User Status", ipga("user").ID, false, "offline", true );
      if ( status === "whitelist" && tokens ) {
        //sockInst.conn.emit( "IP User Status", ipga("user").ID, tokens, "online", false ) );
      }
    }
    else if ( tokens ) {
      sockInst.conn.emit( "IP User Status", ipga("user").ID, false, "offline", false );
    }
  },
  block: function( idx ) {
    sockInst.conn.emit( "IP Block User", ipga("user").ID, idx );
  }
});

function _kshp( a ) {
  if ( a.error ) {
    return;
  }
  call_user_func_array( ipPing.prototype.parse.chat, [ a ] );
}