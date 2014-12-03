var time=function(){return Math.floor((new Date).getTime()/1E3)};
(function(d){"function"===typeof define&&define.amd?define(["jquery"],d):d(jQuery)})(function(d){function n(a){return a}function p(a){return decodeURIComponent(a.replace(k," "))}function l(a){0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return e.json?JSON.parse(a):a}catch(c){}}var k=/\+/g,e=d.cookie=function(a,c,b){if(void 0!==c){b=d.extend({},e.defaults,b);if("number"===typeof b.expires){var g=b.expires,f=b.expires=new Date;f.setDate(f.getDate()+g)}c=e.json?
JSON.stringify(c):String(c);return document.cookie=[e.raw?a:encodeURIComponent(a),"=",e.raw?c:encodeURIComponent(c),b.expires?"; expires="+b.expires.toUTCString():"",b.path?"; path="+b.path:"",b.domain?"; domain="+b.domain:"",b.secure?"; secure":""].join("")}c=e.raw?n:p;b=document.cookie.split("; ");for(var g=a?void 0:{},f=0,k=b.length;f<k;f++){var h=b[f].split("="),m=c(h.shift()),h=c(h.join("="));if(a&&a===m){g=l(h);break}a||(g[m]=l(h))}return g};e.defaults={};d.removeCookie=function(a,c){return void 0!==
d.cookie(a)?(d.cookie(a,"",d.extend({},c,{expires:-1})),!0):!1}});
jQuery.fn.extend({insertAtCaret:function(a,d,f){return this.each(function(b){if($(this).is(":disabled"))return!1;if(document.selection)this.focus(),sel=document.selection.createRange(),this.focus();else if(this.selectionStart||"0"==this.selectionStart){b=this.selectionStart;var c=this.selectionEnd,g=this.scrollTop;if(""===this.value)!0===d&&(a+=" "),this.value=a;else{if(b===c){if(d){var e=this.value.substring(0,b),h=this.value.substring(c,this.value.length);0<e.length&&(" "!==e.substring(b,b-1)&&(a=" "+a)," "!==h.substring(0,1)&&(a+=" "))}}else!0===d&&(a=" "+a+" ");this.value=this.value.substring(0,b)+a+this.value.substring(c,this.value.length)}$(this).focus().trigger("input");f?this.selectionStart=this.selectionEnd=b+a.length:(this.selectionStart=b+a.length,this.selectionEnd=b+a.length+this.value.substring(b,c).length);this.scrollTop=g}else this.value+=a,$(this).focus().trigger("input")})}});
(function(e){var t,o={className:"autosizejs",append:"",callback:!1,resizeDelay:10},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',n=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent"],s=e(i).data("autosize",!0)[0];s.style.lineHeight="99px","99px"===e(s).css("lineHeight")&&n.push("lineHeight"),s.style.lineHeight="",e.fn.autosize=function(i){return i=e.extend({},o,i||{}),s.parentNode!==document.body&&e(document.body).append(s),this.each(function(){function o(){var o,a={};if(t=u,s.className=i.className,l=parseInt(h.css("maxHeight"),10),e.each(n,function(e,t){a[t]=h.css(t)}),e(s).css(a),"oninput"in u){var r=u.style.width;u.style.width="0px",o=u.offsetWidth,u.style.width=r}}function a(){var n,a,r,c;t!==u&&o(),s.value=u.value+i.append,s.style.overflowY=u.style.overflowY,a=parseInt(u.style.height,10),"getComputedStyle"in window?(c=window.getComputedStyle(u),r=u.getBoundingClientRect().width,e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,t){r-=parseInt(c[t],10)}),s.style.width=r+"px"):s.style.width=Math.max(h.width(),0)+"px",s.scrollTop=0,s.scrollTop=9e4,n=s.scrollTop,l&&n>l?(u.style.overflowY="scroll",n=l):(u.style.overflowY="hidden",d>n&&(n=d)),n+=p,a!==n&&(u.style.height=n+"px",w&&i.callback.call(u,u))}function r(){clearTimeout(c),c=setTimeout(function(){h.width()!==z&&a()},parseInt(i.resizeDelay,10))}var l,d,c,u=this,h=e(u),p=0,w=e.isFunction(i.callback),f={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},z=h.width();h.data("autosize")||(h.data("autosize",!0),("border-box"===h.css("box-sizing")||"border-box"===h.css("-moz-box-sizing")||"border-box"===h.css("-webkit-box-sizing"))&&(p=h.outerHeight()-h.height()),d=Math.max(parseInt(h.css("minHeight"),10)-p||0,h.height()),h.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word",resize:"none"===h.css("resize")||"vertical"===h.css("resize")?"none":"horizontal"}),"onpropertychange"in u?"oninput"in u?h.on("input.autosize keyup.autosize",a):h.on("propertychange.autosize",function(){"value"===event.propertyName&&a()}):h.on("input.autosize",a),i.resizeDelay!==!1&&e(window).on("resize.autosize",r),h.on("autosize.resize",a),h.on("autosize.resizeIncludeStyle",function(){t=null,a()}),h.on("autosize.destroy",function(){t=null,clearTimeout(c),e(window).off("resize",r),h.off("autosize").off(".autosize").css(f).removeData("autosize")}),a())})}})(window.jQuery||window.Zepto);

(function( d, s, id ) {
  var js,
      fjs = d.getElementsByTagName(s)[0];
  if ( d.getElementById(id) ) {
    return;
  }
  js  = d.createElement(s);
  js.id   = id;
  js.src  = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=742929165733564";
  js.onload = function() {
    setTimeout(function() {
      if ( $(".fb-like-box iframe").length ) {
        $(".fb-like-box iframe")[0].onload=function() {
          $(".fb-feed-loader").remove();
        };
        $(".fb-like-box iframe")[0].onerror=function() {
          setTimeout(function() {
            $(".fb-feed-loader").removeClass("loader").addClass("text-sm text-danger").text('Connection could not be established.');
          }, 2000);
          $(this).remove();
        };
      }
    }, 1000);
  };
  js.onerror  = function() {
    setTimeout(function() {
      $(".fb-feed-loader").removeClass("loader").addClass("text-sm text-danger").text('Connection could not be established.');
    }, 2000);
  };
  fjs.parentNode.insertBefore( js, fjs );
}( document, 'script', 'facebook-jssdk' ) );

$("summary.marquee").each(function(){
  if ( this.scrollHeight > parseInt( $(this).css("max-height") ) ) {
    $(this).addClass("has-overflow");
  }
});

function timeDifference(b,d,a,c){d=d||"d m Y";if(isNaN(parseInt(b)))return b;var e={s:{name:"second",sub:"s",time:30},m:{name:"minute",sub:"m",time:60},h:{name:"hour",sub:"h",time:3600},d:{name:"day",sub:"d",time:86400},w:{name:"week",sub:"w",time:604800},m2:{name:"month",sub:"mn",time:2592E3},y:{name:"year",sub:"y",time:31536E3}};if(b>time()||!0===a){a=b-time();var h="in ",p=""}else a=parseInt(time()-b),h="",p=" ago";var g="";if(60>a)switch(!0){default:g=c?"1s":"just now";break;case 20>a:g=c?a+"s":h+a+" seconds"+p;break;case 40>a:g=c?a+"s":h+"half a minute"+p;break;case 60>a:g=c?a+"s":h+"less than a minute"+p}else for(index in e)if(timei=e[index],a>=timei.time){var g=Math.round(a/timei.time),k=1==g?timei.name:timei.name+"s",t=timei.sub;"mn"===t&&c&&(t="d",g="~"+30*g);g=c?g+t:h+g+" "+k+p}return g?g:c?"":new Date( b * 1000 )};

if ( typeof auto_update === "boolean" ) {
  var has_auto_update = $.cookie( "auto_update" );
  if ( !( parseInt( has_auto_update ) === 1 ) ) {
    auto_update = false;
    $("input#events-toggler").removeAttr("checked");
    $.cookie( "auto_update", 0, { expires: 365, path: "/" } );
  }

  $("input#events-toggler").on("change", function(e) {
    if ( $(this).is(":checked") ) {
      auto_update = true;
      $.cookie( "auto_update", 1, { expires: 365, path: "/" } );
      console.log( "on" );
    }
    else {
      auto_update = false;
      $.cookie( "auto_update", 0, { expires: 365, path: "/" } );
      console.log( "off" );
    }
  });

  setInterval(function() {
    if ( ( time() - sleeper ) > 600 ) {
      window.location.href  = admin_uri+"login.php?referer="+encodeURIComponent( window.location.href );
    }
  }, 10000);
  var EventsGet = function() {
    if ( auto_update ) {
      var lastElem  = $(".eventLeft a.list-group-item:first");
          lastEvent = ( lastElem.length ) ? lastElem.data("eventID") : 0;
      $.getJSON(mentor+"subscribe/events.php?since="+lastEvent+"&api="+ip_api_key+"&version="+ip_version, function( response ) {
        for( x in response ) {
          addMsg( response[x].ID, response[x].title, response[x].date, response[x].thumb, response[x].link );
        }
      });
    }
  };
  setInterval(EventsGet, 30000);
  EventsGet();

  function addMsg( id, title, timestamp, thumbnail, link ) {
    var $el = $(".nav-user"),
				$n  = $(".count:first", $el),
				$v  = parseInt( $n.text() ),
        $e  = $(".eventLeft");
    if ( $("a#event-"+id).length ) {
      return;
    }
    if ( link ) {
      link  = ( link.indexOf( "%s" ) !== -1 ) ? link.replace( "%s", chat_uri ) : link;
    }
    $(".count", $el).fadeOut().fadeIn().text( $v + 1 );
    var a1  = $("<a />",{"href":( ( link ) ? link : "javacript:void(0);" ),"class":"media list-group-item","id":"event-"+id}).data("eventID", id).appendTo( $e );
    if ( thumbnail ) {
      $("<span />",{"class":"pull-left thumb-sm text-center"}).html('<img src="'+thumbnail+'" alt="" class="img-circle">').appendTo( a1 );
    }
    var a2  = $("<span />",{"class":"media-body block m-b-none"}).html( title+'<br />' ).appendTo( a1 ),
        a3  = $("<small />",{"class":"text-muted"}).html( timeDifference( timestamp ) ).appendTo( a2 );

    a1.hide().prependTo( $e ).slideDown().css( "display", "block" );
  }
}

var pluginCode,
    focusTriggered  = false,
    changeTriggered = false,
    defaultValue    = '',
    socket          = false;

(function($) {
  $(function() {
    if ( $.fn.dropdown ) {
      $.fn.dropdown.Constructor.prototype.change  = function(e) {
        e.preventDefault();
        var $item = $(e.target),
            $select,
            $checked  = false,
            $menu,
            $label;
  
        if ( !$item.is( 'a' ) ) {
          $item = $item.closest( 'a' );
        }
  
        $menu   = $item.closest( '.dropdown-menu' );
        $label  = $menu.parent().find( '.dropdown-label' );
  			$labelHolder = $label.text();
  			$select  = $item.find( 'input' );
  			$checked = $select.is( ':checked' );
  
        if ( $select.is( ':disabled' ) ) {
          return;
        }
        if ( $select.attr('type' ) == 'radio' && $checked ) {
          return;
        }
        if ( $select.attr( 'type' ) == 'radio' ) {
          $menu.find( 'li' ).removeClass( 'active' );
        }
  
  			$item.parent().removeClass( 'active' );
        if ( !$checked ) {
          $item.parent().addClass( 'active' );
        }
  			$select.prop( "checked", !$select.prop("checked") );
  
  			$items = $menu.find( 'li > a > input:checked' );
  			if ( $items.length ) {
          $text = [];
          $items.each(function() {
            var $str  = $.trim( $(this).parent().text() );
            if ( $str ) {
              $text.push( $str );
            }
  				});
  				$text = ( $text.length < 4 ) ? $text.join( ', ' ) : $text.length+' selected';
  				$label.html( $text );
  			}
        else {
          $label.html( $label.data('placeholder') );
        }
      };
    }

    var hashParam = window.location.hash;
    if ( hashParam.length ) {
      if ( $(".nav-tabs a[href="+hashParam+"]").length ) {
        $(".nav-tabs a[href="+hashParam+"]").trigger("click");
      }
    }

    if ( $.fn.dropdown ) {
      $(document).on("click.dropdown-menu", ".dropdown-select > li > a", $.fn.dropdown.Constructor.prototype.change);
    }
    $(document).on("click", "[data-toggle=fullscreen]", function(e) {
      if ( screenfull.enabled ) {
        screenfull.request();
      }
    }).on("click", ".popover-title .close", function(e) {
      var $target   = $(e.target),
          $popover  = $target.closest(".popover").prev();
          $popover && $popover.popover("hide");
		}).on("click", "[data-toggle='ajaxModal']", function(e) {
      $("#ajaxModal").remove();
      e.preventDefault();
      var $this   = $(this),
          $remote = $this.data('remote') || $this.attr('href'),
          $modal  = $('<div class="modal" id="ajaxModal"><div class="modal-body"></div></div>');
      $('body').append($modal);
      $modal.modal();
      $modal.load($remote);
    }).on('click', "[data-toggle^='class']", function(e) {
      if ( e ) {
        e.preventDefault();
      }
			var $this  = $(e.target),
          $class,
          $target,
          $tmp,
          $classes,
          $targets,
          $cookie;
      if ( !$this.data( 'toggle' ) ) {
        $this = $this.closest( '[data-toggle^="class"]' );
      }

      $class  = $this.data()['toggle'];
      $target = $this.data('target') || $this.attr('href');

      if ( $class ) {
        $tmp      = $class.split( ':' )[1];
        $classes  = $tmp.split( ',' );
      }
      if ( $target ) {
        $targets  = $target.split( ',' );
      }
      if ( $targets && $targets.length ) {
        $.each( $targets, function( index, value ) {
          if ( $targets[index] != '#' ) {
            $($targets[index]).toggleClass( $classes[index] );
          }
        });
      }
			$this.toggleClass( "active" );
      if ( $cookie = $(this).data( "cookie" ) ) {
        if ( $this.hasClass( "active" ) ) {
          $.cookie( $cookie, true, { expires: 365, path: "/" } );
        }
        else {
          $.removeCookie( $cookie, { path: "/" } );
        }
      }
		});

    
    if ( $.fn.placeholder ) {
      $("input[placeholder], textarea[placeholder]").placeholder();
    }
    if ( $.fn.popover ) {
      $("[data-toggle=popover]").popover();
    }
    if ( $.fn.tooltip ) {
      $("[data-toggle=tooltip]").tooltip();
    }
    if ( $.fn.carousel ) {
      $(".carousel.auto").carousel();
    }

		$(document).on('click', '.panel-toggle', function(e) {
			e && e.preventDefault();
			var $this = $(e.target),
				$class = 'collapse',
				$target;
			if (!$this.is('a')) $this = $this.closest('a');
			$target = $this.closest('.panel');
			$target.find('.panel-body').toggleClass($class);
			$this.toggleClass('active');
		});

		var scrollToTop = function() {
			!location.hash && setTimeout(function() {
				if (!pageYOffset) window.scrollTo(0, 0);
			}, 1000);
		};
		var $window = $(window);
		var mobile = function(option) {
			if (option == 'reset') {
				$('[data-toggle^="shift"]').shift('reset');
				return;
			}
			scrollToTop();
			$('[data-toggle^="shift"]').shift('init');
			return true;
		};
		$window.width() < 768 && mobile();
		var $resize;
		$window.resize(function() {
			clearTimeout($resize);
			$resize = setTimeout(function() {
				$window.width() < 767 && mobile();
				$window.width() >= 768 && mobile('reset');
			}, 500);
		});
		$('.vbox > footer').prev('section').addClass('w-f');
		$(document).on('click', '.nav-primary a', function(e) {
			var $this = $(e.target),
				$active;
			$this.is('a') || ($this = $this.closest('a'));
			if ($('.nav-vertical').length) {
				return;
			}
			$active = $this.parent().siblings(".active");
			$active && $active.find('> a').toggleClass('active') && $active.toggleClass('active').find('> ul:visible').slideUp(200);
			($this.hasClass('active') && $this.next().slideUp(200)) || $this.next().slideDown(200);
			$this.toggleClass('active').parent().toggleClass('active');
			$this.next().is('ul') && e.preventDefault();
		});
		$(document).on('click.bs.dropdown.data-api', '.dropdown .on, .dropup .on', function(e) {
			e.stopPropagation()
		});
	});
})(jQuery);

!(function($) {
	$(function() {
		$(".combodate").each(function() {
			$(this).combodate();
			$(this).next('.combodate').find('select').addClass('form-control');
		});
		$(".datepicker-input").each(function() {
			$(this).datepicker();
		});
		$('.slider').each(function() {
			$(this).slider();
		});
		if ($.fn.sortable) {
			$('.sortable').sortable();
		}
		$('.no-touch .slim-scroll').each(function() {
			var $self = $(this),
				$data = $self.data(),
				$slimResize;
			$self.slimScroll($data);
			$(window).resize(function(e) {
				clearTimeout($slimResize);
				$slimResize = setTimeout(function() {
					$self.slimScroll($data);
				}, 500);
			});
		});
		if ($.support.pjax) {
			$(document).on('click', 'a[data-pjax]', function(event) {
				event.preventDefault();
				var container = $($(this).data('target'));
				$.pjax.click(event, {
					container: container
				});
			})
		};
		$('.portlet').each(function() {
			$(".portlet").sortable({
				connectWith: '.portlet',
				iframeFix: false,
				items: '.portlet-item',
				opacity: 0.8,
				helper: 'original',
				revert: true,
				forceHelperSize: true,
				placeholder: 'sortable-box-placeholder round-all',
				forcePlaceholderSize: true,
				tolerance: 'pointer'
			});
		});
		$('#docs pre code').each(function() {
			var $this = $(this);
			var t = $this.html();
			$this.html(t.replace(/</g, '&lt;').replace(/>/g, '&gt;'));
		});
    $(document).on('change', 'table thead [type="checkbox"]', function( e ) {
      e && e.preventDefault();
      var $table    = $(e.target).closest('table'),
          $checked  = $(e.target).is(':checked');
			$('tbody [type="checkbox"]', $table).prop('checked', $checked);
		});
		$(document).on('click', '[data-toggle^="progress"]', function(e) {
			e && e.preventDefault();
			$el = $(e.target);
			$target = $($el.data('target'));
			$('.progress', $target).each(function() {
				var $max = 50,
					$data, $ps = $('.progress-bar', this).last();
				($(this).hasClass('progress-xs') || $(this).hasClass('progress-sm')) && ($max = 100);
				$data = Math.floor(Math.random() * $max) + '%';
				$ps.css('width', $data).attr('data-original-title', $data);
			});
		});
		if ($.fn.select2) {
			$("#select2-option").select2();
			$("#select2-tags").select2({
				tags: ["red", "green", "blue"],
				tokenSeparators: [",", " "]
			});
		}
	});
})(window.jQuery);

var requests  = {
  total: 0,
  finish: 0
};

$(function() {
  if ( $(".flot-db-messages").length ) {
    var months  = [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ]
    var data  = [];//[ ["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9] ];
    if ( typeof flotPar != "undefined" && flotPar && $.isArray( flotPar ) && flotPar.length ) {
      var lastDay = 0;
      for( var i = 0; i < flotPar.length; i++ ) {
        var p = flotPar[i];
        p.d = parseInt( p.d );
        if ( ( lastDay + 1 ) != p.d ) {
          for( var x = ( lastDay + 1 ); x < p.d; x++ ) {
            data.push( [ months[p.m]+" "+x+", "+p.y, 0 ] );
          }
        }
        data.push( [ months[p.m]+" "+p.d+", "+p.y, p.t ] );
        lastDay = p.d;
      }
      $.plot( ".flot-db-messages", [ data ], {
        series: {
          lines: {
            show: true,
            lineWidth: 2,
            fill: true,
            fillColor: {
              colors: [
                { opacity: 0.0 },
                { opacity: 0.2 }
              ]
            }
          },
          points: {
            radius: 5,
            show: true
          },
          grow: {
            active: true,
            steps: 50
          },
          shadowSize: 2
        },
        crosshair: {
  				mode: "x"
  			},
  			grid: {
  				hoverable: true,
          clickable: true,
          tickColor: "#f0f0f0",
          borderWidth: 1,
          color: '#f0f0f0'
  			},
        colors: ["#65bd77"],
        tooltip: true,
        tooltipOpts: {
          content: "%y messages were sent and recieved",
          defaultTheme: false,
          shifts: {
            x: 0,
            y: 20
          }
        },
        xaxis: {
          mode: "categories",
          tickLength: 0
        }
      });
    }
    else {
      $(".flot-db-messages").empty().append( '<div class="alert alert-danger"><p>No statitstics were available</p></div>' );
      $(".flot-db-messages").height( "auto" );
    }
  }

  if ( $(".tb-messages").length ) {
    (function( table, form ) {
      $(".related-message button", table).remove();
      $(".related-message input[type=checkbox]", table).hide();
      $(".db-message-text.related-message", table).addClass("ellipsis ellipsis-max");

      $("tbody button",table).on("click", function(e) {
        e.preventDefault();
        var row = $(this).parents("tr:first"),
            rel = row.data("rel"),
            rows  = $("tr.mrel-"+rel,table),
            ids = [];
        rows.each(function() {
          ids.push( $(this).find("input[type=checkbox]").val() );
        });
        if ( !ids.length ) {
          return false;
        }
        if ( !confirm( "Are you sure? You want to delete this message." ) ) {
          return false;
        }
        $(".tooltip").remove();
        rows.remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
        requests.total++;
        ajax_request( form.attr("action"), { 'delete' : 1, 'items' : ids }, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });

      $("button[name=action]").on("click", function(e) {
        e.preventDefault();
        var selected  = $("tbody input[type=checkbox]:checked",table),
            rows    = selected.parents("tr"),
            option  = $(this).prev("select[name=action-option]:first").val();
        if ( option !== "clear" ) {
          if ( !selected.length ) {
            alert( "Please select at least one message" );
            return;
          }
          if ( parseInt( option ) === 1 ) {
            alert( "Please select an action to apply" );
            return;
          }
        }
        else {
          rows  = $("tbody tr",table);
        }

        rows.remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
        requests.total++;
        var dataString  = {
          items: []
        };
        selected.each(function() {
          dataString.items.push( $(this).val() );
        });
        dataString[option]  = 1;
        ajax_request( form.attr("action"), dataString, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        });
      });

      $("tbody input[type=checkbox]",table).on("change", function(e) {
        if ( e.isTrigger ) {
          return;
        }
        var parent  = $(this).parents("tr:first");
        var rel_id  = parent.data("rel");
        var checks  = $("tr.mrel-"+rel_id+" input[type=checkbox]",form);
        checks.prop("checked", $(this).is(":checked"));
      });

    })( $(".tb-messages"), $(".tb-messages").closest("form") );
  }
  else if ( $(".tb-files").length ) {
    (function( table, form ) {
      $("button[name=action]").on("click", function(e) {
        e.preventDefault();
        var selected  = $("tbody input[type=checkbox]:checked",table),
            rows    = selected.parents("tr"),
            option  = $(this).prev("select[name=action-option]:first").val();

        var $is_extn  = ( $.inArray( option, [ "add-extn", "del-extn" ] ) != -1 );
        var $is_mime  = ( $.inArray( option, [ "add-mime", "del-mime" ] ) != -1 );

        if ( option !== "clear" ) {
          if ( !selected.length ) {
            alert( "Please select at least one file" );
            return;
          }
          if ( parseInt( option ) === 1 ) {
            alert( "Please select an action to apply" );
            return;
          }
          if ( $is_extn || $is_mime ) {
            if ( $is_extn ) {
              var extensions  = [];
              rows.each(function() {
                if ( $.inArray( $(this).data("extension"), extensions ) === -1 ) {
                  extensions.push( "tr."+$(this).data("extension").toString().toLowerCase().replace( /[^a-z]/gi, '-' ) );
                }
              });
              rows  = $(extensions.join(", "),table);
            }
            else {
              var mimetypes = [];
              rows.each(function() {
                if ( $.inArray( $(this).data("mimetype"), mimetypes ) === -1 ) {
                  mimetypes.push( "tr."+$(this).data("mimetype").toString().toLowerCase().replace( /[^a-z]/gi, '-' ) );
                }
              });
              rows  = $(mimetypes.join(", "),table);
            }
          }
        }
        else {
          rows  = $("tbody tr",table);
        }

        if ( $.inArray( option, [ "add-extn", "add-mime" ] ) != -1 ) {
          rows.removeClass("blacklist whitelist");
          rows.addClass( allow_mode );
          if ( option == "add-extn" ) {
            rows.addClass("list-extension");
          }
          else {
            rows.addClass("list-mimetype");
          }
          rows.filter(".list-extension, .list-mimetype").removeClass("success danger").addClass( ( ( allow_mode == "blacklist" ) ? "danger" : "success" ) );
          $("button.ublk-btn-inline",rows).removeClass("sr-only");
          $("button.blk-btn-inline",rows).addClass("sr-only");
        }
        else if ( $.inArray( option, [ "del-extn", "del-mime" ] ) != -1 ) {
          rows.removeClass("blacklist whitelist");
          rows.addClass( ( ( allow_mode == "blacklist" ) ? "whitelist" : "blacklist" ) );
          if ( option == "del-extn" ) {
            rows.removeClass("list-extension");
          }
          else {
            rows.removeClass("list-mimetype");
          }
          rows.not(".list-extension, .list-mimetype").removeClass("success danger").addClass( ( ( allow_mode == "blacklist" ) ? "success" : "danger" ) );
          $("button.blk-btn-inline",rows).removeClass("sr-only");
          $("button.ublk-btn-inline",rows).addClass("sr-only");
        }
        else {
          rows.remove();
          if ( !$("tbody tr",table).length ) {
            $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
          }
        }

        requests.total++;
        var dataString  = {
          items: []
        };
        if ( option === "delete" ) {
          selected.each(function() {
            dataString.items.push( $(this).val() );
          });
        }
        else if ( option === "clear" ) {
          dataString.items  = false;
        }
        else {
          rows.each(function() {
            if ( $is_extn ) {
              if ( $.inArray( $(this).data("extension"), dataString.items ) === -1 ) {
                dataString.items.push( $(this).data("extension") );
              }
            }
            else if ( $is_mime ) {
              if ( $.inArray( $(this).data("mimetype"), dataString.items ) === -1 ) {
                dataString.items.push( $(this).data("mimetype") );
              }
            }
            else {
              dataString.items.push( $(this).data("mimetype") );
            }
          });
        }
        dataString[option]  = 1;
        ajax_request( form.attr("action"), dataString, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });
      $("button.blk-btn-inline, button.ublk-btn-inline",table).on("click", function(e) {
        e.preventDefault();
        var row   = $(this).parents("tr:first"),
            extn  = row.data("extension"),
            mime  = row.data("mimetype"),
            extn1 = extn.toLowerCase().replace( /[^a-z]/gi, '-' ),
            mime1 = mime.toLowerCase().replace( /[^a-z]/gi, '-' ),
            rows  = $("tr."+extn1+", tr."+mime1,table);

        var dataString  = {
          items: { 'extn' : [ extn ], 'mime' : [ mime ] }
        };

        if ( $(this).hasClass("blk-btn-inline") ) {
          rows.removeClass("blacklist whitelist danger success");
          rows.addClass( allow_mode+" list-extension list-mimetype" );
          rows.addClass( ( ( allow_mode == "blacklist" ) ? "danger" : "success" ) );
          dataString['add-both']  = 1;
          $("button.ublk-btn-inline",rows).removeClass("sr-only");
          $("button.blk-btn-inline",rows).addClass("sr-only");
        }
        else {
          rows.removeClass("blacklist whitelist success danger list-extension list-mimetype");
          rows.addClass( ( ( allow_mode == "blacklist" ) ? "whitelist" : "blacklist" ) );
          rows.addClass( ( ( allow_mode == "blacklist" ) ? "success" : "danger" ) );
          dataString['del-both']  = 1;
          $("button.blk-btn-inline",rows).removeClass("sr-only");
          $("button.ublk-btn-inline",rows).addClass("sr-only");
        }

        requests.total++;
        ajax_request( form.attr("action"), dataString, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });

      $("button[name=delete]",form).on("click", function(e) {
        e.preventDefault();
        var row = $(this).closest("tr"),
              val = $("input[type=checkbox]",row).val();
      
        requests.total++;
        row.remove();
        $(".tooltip").remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
      
        ajax_request( form.attr("action"), { 'items' : [ val ], 'delete' : 1 }, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });
    })( $(".tb-files"), $(".tb-files").closest("form") );
  }
  else if ( $(".tb-plugins").length ) {
    (function( table, form ) {
      $("button[name=edit]",form).on("click", function(e) {
        e.preventDefault();
        var row = $(this).parents("tr:first"),
            val = $("input[type=checkbox]",row).val();
        window.location = admin_uri+"plugins.php?action=edit&format="+plugin_format+"&plugin="+val;
      });
      $("button[name=delete]",form).on("click", function(e) {
        e.preventDefault();
        var row = $(this).parents("tr:first"),
            val = $("input[type=checkbox]",row).val();

        requests.total++;
        row.remove();
        $(".tooltip").remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }

        var dataString  = {
          'items': {},
          'delete' : 1
        };
        dataString.items[plugin_format] = [ val ];
        ajax_request( form.attr("action"), dataString, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });
      $("button[name=action]").on("click", function(e) {
        e.preventDefault();
        var selected  = $("tbody input[type=checkbox]:checked",table),
            rows    = selected.parents("tr"),
            option  = $(this).prev("select[name=action-option]:first").val();
        if ( option !== "clear" ) {
          if ( !selected.length ) {
            alert( "Please select at least one plugin" );
            return;
          }
        }
        var data  = selected.serialize();
            data  = ( option !== "clear" ) ? data+"&"+option+"=1" : option+"=1";

        if ( option === "delete" ) {
          rows.remove();
        }
        else if ( option === "clear" ) {
          $("tbody",table).empty();
        }
        else if ( option == "activate" ) {
          $("button[name=activate]").attr("name","deactivate").removeClass("btn-default").addClass("btn-info active").attr("data-original-title","Deactivate Plugin").find("span.fa").removeClass("fa-star-o").addClass("fa-star");
          rows.addClass("success");
        }
        else if ( option == "deactivate" ) {
          $("button[name=deactivate]").attr("name","activate").removeClass("btn-info active").addClass("btn-default").attr("data-original-title","Activate Plugin").find("span.fa").removeClass("fa-star").addClass("fa-star-o");
          rows.removeClass("success");
        }

        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }

        requests.total++;
        ajax_request( form.attr("action"), data, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        });
      });
      form.on("click", "button[name=activate], button[name=deactivate]", function(e) {
        e.preventDefault();
        var row = $(this).parents("tr:first"),
            val = $("input[type=checkbox]",row).val();

        requests.total++;
        var dataString  = {
          'items': {}
        };
        dataString.items[plugin_format] = [ val ];
        if ( $(this).attr("name") == "activate" ) {
          $(this).attr("name","deactivate").removeClass("btn-default").addClass("btn-info active").attr("data-original-title","Deactivate Plugin").find("span.fa").removeClass("fa-star-o").addClass("fa-star");
          dataString.activate = 1;
          row.addClass("success");
        }
        else {
          $(this).attr("name","activate").removeClass("btn-info active").addClass("btn-default").attr("data-original-title","Activate Plugin").find("span.fa").removeClass("fa-star").addClass("fa-star-o");
          dataString.deactivate = 1;
          row.removeClass("success");
        }
        ajax_request( form.attr("action"), dataString, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });
      
      $("button[name^='install']",form).on("click", function(e) {
        e.preventDefault();
        var row = $(this).parents("tr:first"),
            col = $(this).parent();
        if ( row.hasClass("installed") ) {
          if ( !confirm( "This Plugin is already installed.\nAre you sure? You want to install again?" ) ) {
            row.focus();
            return false;
          }
        }
        row.focus();
        if ( $(this).attr("name") == "install-activate" ) {
          if ( !$("input[name=plugin-activate]",col).length ) {
            $('<input type="hidden" name="plugin-activate" value="1">').insertBefore( $(this) );
          }
        }
        else {
          $("input[name=plugin-activate]",col).remove();
        }
        $("input",col).removeAttr("disabled");
        form.off("submit").trigger("submit");
      });

      if ( $(".transload-status").length ) {
        var transload_status  = $(".transload-status",form),
            plugin_ID   = $("input[name=plugin-ID]",form).val(),
            plugin_name = $("input[name=plugin-name]",form).val();
    
        $("<iframe />",{"class":"sr-only","name":"transloader-frame"}).insertAfter( form );
        form.attr({
          target: 'transloader-frame',
          action: admin_uri+'includes/transloader.php'
        });
        setTimeout(function() {
          transload_status.html( "Initializing connection&hellip;" );
          form.trigger("submit");
        }, 1000);
      }

      if ( typeof codemirrorEditor == "boolean" && codemirrorEditor ) {
        var file_active = $("ul > li.tree-file-item.file-active"),
            file_tree   = $("ul.file-system-tree"),
            folder_item = $("li.tree-folder-item.tree-item");

        folder_item.each(function() {
          $(this).prependTo( $(this).parent() );
        });

        //folder_item.prependTo( folder_item.parent() );
        if ( file_active.length ) {
          file_active.parentsUntil( file_tree ).filter("li").addClass("open");
        }
        if ( area = $("textarea#plugin-content") ) {
          defaultValue  = area.data("original-value") || area.val();
          var format  = ( typeof pluginFormat === "string" ) ? pluginFormat : $("input[name=format]").val(),
              modes   = {
                "js"  : "javascript",
                "json": "javascript",
                "lng" : "javascript",
                "php" : "php",
                "htm" : "htmlmixed",
                "html": "htmlmixed",
                "css" : "css",
                "scss": "css",
                "less": "less",
                "tpl" : "htmlmixed",
                "c"   : "clike",
                "java": "clike",
                "txt" : "null"
              };
          if ( typeof CodeMirror != "undefined" ) {
            area.removeAttr( "disabled" );
            pluginCode  = CodeMirror.fromTextArea( area[0], {
              mode: modes[format] || "null",
              styleActiveLine: true,
              lineNumbers: true,
              lineWrapping: true,
              autofocus: true,
              dragDrop: false,
              viewportMargin: Infinity
            } );
            pluginCode.on("focus", function() {
              if ( !focusTriggered ) {
                focusTriggered  = true;
                pluginCode.setCursor( lineFocus );
              }
            });
            pluginCode.on("change", function() {
              changeTriggered = true;
            });
            if ( $(".codemirror-status").length ) {
              pluginCode.on("cursorActivity", function( doc ) {
                var position  = doc.getCursor(),
                    selection = doc.getSelection(),
                    hasSelection  = !!selection,
                    characters  = ( hasSelection ) ? selection.split( "" ).length : 1;
                    lines = ( hasSelection ) ? selection.split( "\n" ).length : 1,
                    message = '';
                if ( hasSelection ) {
                  if ( lines === 1 ) {
                    message = characters+" characters selected";
                  }
                  else {
                    message = lines+" lines, "+characters+" characters selected";
                  }
                }
                else {
                  message = "Line "+( position.line + 1 )+", Column "+( position.ch + 1 );
                }
    
                $(".codemirror-status").text( message );
              });
    
              pluginCode.on("update", function( doc ) {
                var size  = pluginCode.getValue().length;
                $(".codemirror-size").text( "("+formatFileSize( size )+")" );
              });
            }
          }
          else {
            initializeEditorNative( area );
          }

          if ( $("select#plugin-format").length ) {
            $("select#plugin-format").on("change", function() {
              var value = $(this).val();
              if ( value === "js" ) {
                if ( pluginCode ) {
                  pluginCode.setOption( 'mode', 'text/javascript' );
                  pluginCode.setValue( defaultValue );
                  pluginCode.setCursor( lineFocus );
                  pluginCode.focus();
                }
                else {
                  $("textarea#plugin-content").val( defaultValue ).trigger( "autosize.resize" );
                }
              }
              else {
                if ( pluginCode ) {
                  pluginCode.setOption( 'mode', 'application/x-httpd-php' );
                  pluginCode.setValue( "<?php \n\n"+defaultValue+"\n\n?>" );
                  pluginCode.setCursor( lineFocus + 1 );
                  pluginCode.focus();
                }
                else {
                  $("textarea#plugin-content").val( "<?php \n\n"+defaultValue+"\n\n?>" ).trigger( "autosize.resize" );
                }
              }
            });
          }
        }
      }

      form.on("submit", function(e) {
        if ( typeof pluginCode === "object" ) {
          pluginCode.save();
        }
      }).on("reset", function(e) {
        if ( $("select#plugin-format").length ) {
          setTimeout(function() {
            $("select#plugin-format").trigger("change");
          },100);
        }
        else {
          pluginCode && pluginCode.setValue( $("textarea#plugin-content").val() );
        }
        if ( !pluginCode ) {
          setTimeout(function() {
            $("textarea#plugin-content").trigger( "autosize.resize" );
          },100);
        }
      });
    })( $(".tb-plugins"), $(".tb-plugins").closest("form") );
  }
  else if ( $(".tb-settings").length ) {
    (function( table, form ) {
      var notif_layout  = $(".notif-layout-group:last").clone(),
          notif_count   = 1;
      $(".notif-layout-group:last").remove();
      $("button.notif-layout-add").on("click", function(e) {
        e.preventDefault();
        var row = $(notif_layout).clone().appendTo( $("div#notifications") );
        $("input",row).eq(0).attr("name","notif["+notif_count+"][id]");
        $("input",row).eq(1).attr("name","notif["+notif_count+"][subject]");
        $("textarea",row).eq(0).attr("name","notif["+notif_count+"][message]");
        initNotifControl( row );
        $("input:first",row).focus();
        notif_count++;
      });
      var initNotifControl  = function( elem ) {
        elem  = ( elem && elem.length ) ? elem : $(".notif-layout-group");
        $("textarea",elem).autosize();
        $("button.notif-layout-remove",elem).on("click", function(e) {
          e.preventDefault();
          $(this).parents(".notif-layout-group:first").remove();
        });
      };
      initNotifControl();

      if ( $.fn.select2 ) {
        $("select",table).removeClass("form-control").width("100%").select2();
        $("#blocked_files_extn",table).removeClass("form-control").width("100%").select2({
          placeholder:  "Enter an extension...",
          tags: "3dm 3dmf 3dml 3g2 3gp a aab aac aam aas abc abw acc ace acgi acu acutc adp aep afl afm afp ai aif aifc aiff aim aip air ami ani aos apk application apr aps arc arj art asc asf asm aso asp asx atc atom atomcat atomsvc atx au avi avs aw azf azs azw bat bcpio bdf bdm bh2 bin bm bmi bmp boo book box boz bpk bsh btif bz bz2 c c++ c4d c4f c4g c4p c4u cab car cat cc ccad cco cct ccxml cdbcmsg cdf cdkey cdx cdxml cdy cer cgm cha chat chm chrt cif cii cil cla class clkk clkp clkt clkw clkx clp cmc cmdf cml cmp cmx cod com conf cpio cpp cpt crd crl crt csh csml csp css cst csv cu curl cww cxt cxx daf dataless davmount dcr dcurl dd2 ddd deb deepv def deploy der dfac dic dif diff dir dis dist distz djv djvu dl dll dmg dms dna doc docm docx dot dotm dotx dp dpg drw dsc dtb dtd dts dtshd dump dv dvi dwf dwg dxf dxp dxr ecelp4800 ecelp7470 ecelp9600 ecma edm edx efif ei6 el elc eml emma env eol eot eps epub es es3 esf et3 etx evy exe ext ez ez2 ez3 f f4v f77 f90 fbs fdf fe_launch fg5 fgd fh fh4 fh5 fh7 fhc fif fig fli flo flv flw flx fly fm fmf fnc for fpx frame frl fsc fst ftc fti funk fvt fzs g g3 gac gdl geo gex ggb ggt ghf gif gim gl gmx gnumeric gph gqf gqs gram gre grv grxml gsd gsf gsm gsp gss gtar gtm gtw gv gz gzip h h261 h263 h264 hbci hdf help hgl hh hlb hlp hpg hpgl hpid hps hqx hta htc htke htm html htmls htt htx hvd hvp hvs icc ice icm ico ics idc ief iefs ifb ifm iges igl igs igx iif ima imap imp ims in inf ins ip ipk irm irp iso isu it itp iv ivp ivr ivu ivy jad jam jar jav java jcm jfif jfif-tbnl jisp jlt jnlp joda jpe jpeg jpg jpgm jpgv jpm jps js json jut kar karbon kfo kia kil kml kmz kne knp kon kpr kpt ksh ksp ktr ktz kwd kwt la lam latex lbd lbe les lha lhx link66 list list3820 listafp lma log lostxml lrf lrm lsp lst lsx ltf ltx lvp lwp lzh lzx m m13 m14 m1v m2a m2v m3a m3u m4u m4v ma mag maker man map mar mathml mb mbd mbk mbox mc1 mcd mcf mcp mcurl mdb mdi me mesh mfm mgz mht mhtml mid midi mif mime mj2 mjf mjp2 mjpg mk3d mka mkv mlp mm mmd mme mmf mmr mny mobi mod moov mov movie mp2 mp2a mp3 mp4 mp4a mp4s mp4v mpa mpc mpe mpeg mpg mpg4 mpga mpkg mpm mpn mpp mpt mpv mpx mpy mqy mrc ms mscml mseed mseq msf msh msi msl msty mts mus musicxml mv mvb mwf mxf mxl mxml mxs mxu my mzz n-gage nap naplps nb nc ncm ncx ngdat nif niff nix nlu nml nnd nns nnw npx nsc nsf nvd nws o oa2 oa3 oas obd obj oda odb odc odf odft odg odi odp ods odt oga ogg ogv ogx omc omcd omcr onepkg onetmp onetoc onetoc2 opf oprc org osf osfpvg otc otf otg oth oti otm otp ots ott oxt p p10 p12 p7a p7b p7c p7m p7r p7s part pas pbd pbm pcf pcl pclxl pct pcurl pcx pdb pdf pfa pfb pfm pfr pfunk pfx pgm pgn pgp php pic pict pkg pki pkipath pko pl plb plc plf pls plx pm pm4 pm5 pml png pnm portpkg pot potm potx pov ppa ppam ppd ppm pps ppsm ppsx ppt pptm pptx ppz pqa prc pre prf prt ps psb psd psf ptid pub pvb pvu pwn pwz py pya pyc pyo pyv qam qbo qcp qd3 qd3d qfx qif qps qt qtc qti qtif qwd qwt qxb qxd qxl qxt ra ram rar ras rast rcprofile rdf rdz rep res rexx rf rgb rif rl rlc rld rm rmi rmm rmp rms rnc rng rnx roff rp rpm rpss rpst rq rs rsd rss rt rtf rtx rv s s3m saf saveme sbk sbml sc scd scm scq scs scurl sda sdc sdd sdkd sdkm sdml sdp sdr sdw sea see seed sema semd semf ser set setpay setreg sfd-hdstx sfs sgl sgm sgml sh shar shf shtml si sic sid sig silo sis sisx sit sitx skd skm skp skt sl slc sldm sldx slt smf smi smil snd snf so sol spc spf spl spot spp spq spr sprite spx src srx sse ssf ssi ssm ssml sst stc std step stf sti stk stl stp str stw sus susp sv4cpio sv4crc svd svf svg svgz svr swa swf swi sxc sxd sxg sxi sxm sxw t talk tao tar tbk tcap tcl tcsh teacher tex texi texinfo text tfm tgz tif tiff tmo torrent tpl tpt tr tra trm tsi tsp tsv ttc ttf turbot twd twds txd txf txt u32 udeb ufd ufdl uil umj uni unis unityweb unv uoml uri uris urls ustar utz uu uue vcd vcf vcg vcs vcx vda vdo vew vis viv vivo vmd vmf vob voc vor vos vox vqe vqf vql vrml vrt vsd vsf vss vst vsw vtu vxml w3d w60 w61 w6w wad wav wax wb1 wbmp wbs wbxml wcm wdb web wiz wk1 wks wm wma wmd wmf wml wmlc wmls wmlsc wmv wmx wmz word wp wp5 wp6 wpd wpl wps wq1 wqd wri wrl wrz wsc wsdl wspolicy wsrc wtb wtk wvx x-png x32 x3d xap xar xbap xbd xbm xdm xdp xdr xdw xenc xer xfdf xfdl xgz xht xhtml xhvml xif xl xla xlam xlb xlc xld xlk xll xlm xls xlsb xlsm xlsx xlt xltm xltx xlv xlw xm xml xmz xo xop xpdl xpi xpix xpm xpr xps xpw xpx xsl xslt xsm xspf xsr xul xvm xvml xwd xyz z zaz zip zir zirz zmm zoo zsh 123 woff".split(' '),
          minimumInputLength: 1,
          tokenSeparators:  [",", " "]
        });
        $("#blocked_files_mime",table).removeClass("form-control").width("100%").select2({
          placeholder:  "Enter mimetype...",
          tags: "application/x-bytecode.python application/andrew-inset application/applixware application/atom+xml application/atomcat+xml application/atomsvc+xml application/base64 application/book application/ccxml+xml application/clariscad application/commonground application/cu-seeme application/davmount+xml application/drafting application/ecmascript application/emma+xml application/epub+zip application/excel application/font-tdpfr application/freeloader application/futuresplash application/groupwise application/hta application/hyperstudio application/i-deas application/inf application/java-archive application/java-serialized-object application/json application/lost+xml application/marc application/mathematica application/mathml+xml application/mbedlet application/mbox application/mediaservercontrol+xml application/mime application/mp4 application/mspowerpoint application/msword application/mxf application/netmc application/octet-stream application/oda application/oebps-package+xml application/ogg application/onenote application/patch-ops-error+xml application/pdf application/pgp-encrypted application/pgp-signature application/pics-rules application/pkcs7-signature application/pkix-crl application/pkix-pkipath application/pkixcmp application/pls+xml application/postscript application/pro_eng application/prs.cww application/rdf+xml application/reginfo+xml application/relax-ng-compact-syntax application/resource-lists+xml application/resource-lists-diff+xml application/rls-services+xml application/rsd+xml application/rss+xml application/sbml+xml application/scvp-cv-request application/scvp-cv-response application/scvp-vp-request application/scvp-vp-response application/set application/set-payment-initiation application/set-registration-initiation application/shf+xml application/smil application/solids application/sounder application/sparql-query application/sparql-results+xml application/srgs application/srgs+xml application/ssml+xml application/step application/streamingmedia application/vda application/vnd.3gpp.pic-bw-large application/vnd.3gpp.pic-bw-small application/vnd.3gpp.pic-bw-var application/vnd.3gpp2.tcap application/vnd.3m.post-it-notes application/vnd.accpac.simply.aso application/vnd.accpac.simply.imp application/vnd.acucobol application/vnd.acucorp application/vnd.adobe.air-application-installer-package+zip application/vnd.adobe.xdp+xml application/vnd.adobe.xfdf application/vnd.airzip.filesecure.azf application/vnd.airzip.filesecure.azs application/vnd.amazon.ebook application/vnd.americandynamics.acc application/vnd.amiga.ami application/vnd.android.package-archive application/vnd.anser-web-certificate-issue-initiation application/vnd.anser-web-funds-transfer-initiation application/vnd.antix.game-component application/vnd.apple.installer+xml application/vnd.arastra.swi application/vnd.audiograph application/vnd.blueice.multipass application/vnd.bmi application/vnd.businessobjects application/vnd.chemdraw+xml application/vnd.chipnuts.karaoke-mmd application/vnd.cinderella application/vnd.claymore application/vnd.clonk.c4group application/vnd.commonspace application/vnd.contact.cmsg application/vnd.cosmocaller application/vnd.crick.clicker application/vnd.crick.clicker.keyboard application/vnd.crick.clicker.palette application/vnd.crick.clicker.template application/vnd.crick.clicker.wordbank application/vnd.criticaltools.wbs+xml application/vnd.ctc-posml application/vnd.cups-ppd application/vnd.curl.car application/vnd.curl.pcurl application/vnd.data-vision.rdz application/vnd.denovo.fcselayout-link application/vnd.dna application/vnd.dolby.mlp application/vnd.dpgraph application/vnd.dreamfactory application/vnd.dynageo application/vnd.ecowin.chart application/vnd.enliven application/vnd.epson.esf application/vnd.epson.msf application/vnd.epson.quickanime application/vnd.epson.salt application/vnd.epson.ssf application/vnd.eszigno3+xml application/vnd.ezpix-album application/vnd.ezpix-package application/vnd.fdf application/vnd.fdsn.mseed application/vnd.fdsn.seed application/vnd.flographit application/vnd.fluxtime.clip application/vnd.framemaker application/vnd.frogans.fnc application/vnd.frogans.ltf application/vnd.fsc.weblaunch application/vnd.fujitsu.oasys application/vnd.fujitsu.oasys2 application/vnd.fujitsu.oasys3 application/vnd.fujitsu.oasysgp application/vnd.fujitsu.oasysprs application/vnd.fujixerox.ddd application/vnd.fujixerox.docuworks application/vnd.fujixerox.docuworks.binder application/vnd.fuzzysheet application/vnd.genomatix.tuxedo application/vnd.geogebra.file application/vnd.geogebra.tool application/vnd.geometry-explorer application/vnd.gmx application/vnd.google-earth.kml+xml application/vnd.google-earth.kmz application/vnd.grafeq application/vnd.groove-account application/vnd.groove-help application/vnd.groove-identity-message application/vnd.groove-injector application/vnd.groove-tool-message application/vnd.groove-tool-template application/vnd.groove-vcard application/vnd.handheld-entertainment+xml application/vnd.hbci application/vnd.hhe.lesson-player application/vnd.hp-hpgl application/vnd.hp-hpid application/vnd.hp-hps application/vnd.hp-jlyt application/vnd.hp-pclxl application/vnd.hydrostatix.sof-data application/vnd.hzn-3d-crossword application/vnd.ibm.minipay application/vnd.ibm.modcap application/vnd.ibm.rights-management application/vnd.ibm.secure-container application/vnd.iccprofile application/vnd.igloader application/vnd.immervision-ivp application/vnd.immervision-ivu application/vnd.intercon.formnet application/vnd.intu.qbo application/vnd.intu.qfx application/vnd.ipunplugged.rcprofile application/vnd.irepository.package+xml application/vnd.is-xpr application/vnd.jcp.javame.midlet-rms application/vnd.jisp application/vnd.joost.joda-archive application/vnd.kahootz application/vnd.kde.karbon application/vnd.kde.kchart application/vnd.kde.kformula application/vnd.kde.kivio application/vnd.kde.kontour application/vnd.kde.kpresenter application/vnd.kde.kspread application/vnd.kde.kword application/vnd.kenameaapp application/vnd.kidspiration application/vnd.kinar application/vnd.kodak-descriptor application/vnd.llamagraphics.life-balance.desktop application/vnd.llamagraphics.life-balance.exchange+xml application/vnd.lotus-1-2-3 application/vnd.lotus-approach application/vnd.lotus-notes application/vnd.lotus-organizer application/vnd.lotus-wordpro application/vnd.macports.portpkg application/vnd.medcalcdata application/vnd.mediastation.cdkey application/vnd.mfer application/vnd.mfmp application/vnd.micrografx.igx application/vnd.mobius.daf application/vnd.mobius.dis application/vnd.mobius.mbk application/vnd.mobius.mqy application/vnd.mobius.msl application/vnd.mobius.plc application/vnd.mobius.txf application/vnd.mophun.application application/vnd.mozilla.xul+xml application/vnd.ms-artgalry application/vnd.ms-cab-compressed application/vnd.ms-excel.addin.macroenabled.12 application/vnd.ms-excel.sheet.binary.macroenabled.12 application/vnd.ms-excel.sheet.macroenabled.12 application/vnd.ms-excel.template.macroenabled.12 application/vnd.ms-fontobject application/vnd.ms-htmlhelp application/vnd.ms-ims application/vnd.ms-lrm application/vnd.ms-pki.certstore application/vnd.ms-pki.pko application/vnd.ms-pki.seccat application/vnd.ms-powerpoint application/vnd.ms-powerpoint.addin.macroenabled.12 application/vnd.ms-powerpoint.presentation.macroenabled.12 application/vnd.ms-powerpoint.slide.macroenabled.12 application/vnd.ms-powerpoint.slideshow.macroenabled.12 application/vnd.ms-powerpoint.template.macroenabled.12 application/vnd.ms-project application/vnd.ms-word.document.macroenabled.12 application/vnd.ms-word.template.macroenabled.12 application/vnd.ms-works application/vnd.ms-wpl application/vnd.ms-xpsdocument application/vnd.mseq application/vnd.musician application/vnd.muvee.style application/vnd.neurolanguage.nlu application/vnd.noblenet-directory application/vnd.noblenet-sealer application/vnd.noblenet-web application/vnd.nokia.configuration-message application/vnd.nokia.n-gage.data application/vnd.nokia.n-gage.symbian.install application/vnd.nokia.radio-preset application/vnd.nokia.radio-presets application/vnd.nokia.ringing-tone application/vnd.novadigm.edm application/vnd.novadigm.edx application/vnd.novadigm.ext application/vnd.oasis.opendocument.chart application/vnd.oasis.opendocument.chart-template application/vnd.oasis.opendocument.database application/vnd.oasis.opendocument.formula application/vnd.oasis.opendocument.formula-template application/vnd.oasis.opendocument.graphics application/vnd.oasis.opendocument.graphics-template application/vnd.oasis.opendocument.image application/vnd.oasis.opendocument.image-template application/vnd.oasis.opendocument.presentation application/vnd.oasis.opendocument.presentation-template application/vnd.oasis.opendocument.spreadsheet application/vnd.oasis.opendocument.spreadsheet-template application/vnd.oasis.opendocument.text application/vnd.oasis.opendocument.text-master application/vnd.oasis.opendocument.text-template application/vnd.oasis.opendocument.text-web application/vnd.olpc-sugar application/vnd.oma.dd2+xml application/vnd.openofficeorg.extension application/vnd.openxmlformats-officedocument.presentationml.presentation application/vnd.openxmlformats-officedocument.presentationml.slide application/vnd.openxmlformats-officedocument.presentationml.slideshow application/vnd.openxmlformats-officedocument.presentationml.template application/vnd.openxmlformats-officedocument.spreadsheetml.sheet application/vnd.openxmlformats-officedocument.spreadsheetml.template application/vnd.openxmlformats-officedocument.wordprocessingml.document application/vnd.openxmlformats-officedocument.wordprocessingml.template application/vnd.palm application/vnd.pg.format application/vnd.pg.osasli application/vnd.picsel application/vnd.pocketlearn application/vnd.powerbuilder6 application/vnd.previewsystems.box application/vnd.proteus.magazine application/vnd.publishare-delta-tree application/vnd.pvi.ptid1 application/vnd.quark.quarkxpress application/vnd.recordare.musicxml application/vnd.recordare.musicxml+xml application/vnd.rim.cod application/vnd.rn-realplayer application/vnd.route66.link66+xml application/vnd.seemail application/vnd.sema application/vnd.semd application/vnd.semf application/vnd.shana.informed.formdata application/vnd.shana.informed.formtemplate application/vnd.shana.informed.interchange application/vnd.shana.informed.package application/vnd.simtech-mindmapper application/vnd.smaf application/vnd.smart.teacher application/vnd.solent.sdkm+xml application/vnd.spotfire.dxp application/vnd.spotfire.sfs application/vnd.stardivision.calc application/vnd.stardivision.draw application/vnd.stardivision.impress application/vnd.stardivision.math application/vnd.stardivision.writer application/vnd.stardivision.writer-global application/vnd.sun.xml.calc application/vnd.sun.xml.calc.template application/vnd.sun.xml.draw application/vnd.sun.xml.draw.template application/vnd.sun.xml.impress application/vnd.sun.xml.impress.template application/vnd.sun.xml.math application/vnd.sun.xml.writer application/vnd.sun.xml.writer.global application/vnd.sun.xml.writer.template application/vnd.sus-calendar application/vnd.svd application/vnd.symbian.install application/vnd.syncml+xml application/vnd.syncml.dm+wbxml application/vnd.syncml.dm+xml application/vnd.tao.intent-module-archive application/vnd.tmobile-livetv application/vnd.trid.tpt application/vnd.triscape.mxs application/vnd.trueapp application/vnd.ufdl application/vnd.uiq.theme application/vnd.umajin application/vnd.unity application/vnd.uoml+xml application/vnd.vcx application/vnd.visio application/vnd.visionary application/vnd.vsf application/vnd.wap.sic application/vnd.wap.slc application/vnd.wap.wbxml application/vnd.wap.wmlc application/vnd.wap.wmlscriptc application/vnd.webturbo application/vnd.wqd application/vnd.wt.stf application/vnd.xara application/vnd.xfdl application/vnd.yamaha.hv-dic application/vnd.yamaha.hv-script application/vnd.yamaha.hv-voice application/vnd.yamaha.openscoreformat application/vnd.yamaha.openscoreformat.osfpvg+xml application/vnd.yamaha.smaf-audio application/vnd.yamaha.smaf-phrase application/vnd.yellowriver-custom-menu application/vnd.zul application/vnd.zzazz.deck+xml application/vocaltec-media-desc application/vocaltec-media-file application/voicexml+xml application/wordperfect application/wordperfect6.0 application/wordperfect6.1 application/wsdl+xml application/wspolicy+xml application/x-123 application/x-abiword application/x-ace-compressed application/x-aim application/x-authorware-bin application/x-authorware-map application/x-authorware-seg application/x-bcpio application/x-bittorrent application/x-bsh application/x-bzip application/x-bzip2 application/x-cdlink application/x-chat application/x-chess-pgn application/x-cocoa application/x-compressed application/x-conference application/x-cpio application/x-cpt application/x-debian-package application/x-deepv application/x-director application/x-doom application/x-dtbncx+xml application/x-dtbook+xml application/x-dtbresource+xml application/x-dvi application/x-elc application/x-envoy application/x-esrehber application/x-excel application/x-font-bdf application/x-font-ghostscript application/x-font-linux-psf application/x-font-otf application/x-font-pcf application/x-font-snf application/x-font-ttf application/x-font-type1 application/x-font-woff application/x-freelance application/x-gnumeric application/x-gsp application/x-gss application/x-gtar application/x-gzip application/x-hdf application/x-helpfile application/x-httpd-imap application/x-httpd-php application/x-ima application/x-internett-signup application/x-inventor application/x-ip2 application/x-java-class application/x-java-commerce application/x-java-jnlp-file application/x-javascript application/x-killustrator application/x-koan application/x-latex application/x-lha application/x-livescreen application/x-lotus application/x-lzh application/x-lzx application/x-mac-binhex40 application/x-macbinary application/x-mathcad application/x-meme application/x-mif application/x-mix-transfer application/x-mobipocket-ebook application/x-ms-application application/x-ms-wmd application/x-ms-wmz application/x-ms-xbap application/x-msaccess application/x-msbinder application/x-mscardfile application/x-msclip application/x-msdownload application/x-msexcel application/x-msmediaview application/x-msmoney application/x-mspowerpoint application/x-mspublisher application/x-msschedule application/x-msterminal application/x-navi-animation application/x-navidoc application/x-navimap application/x-navistyle application/x-netcdf application/x-newton-compatible-pkg application/x-nokia-9000-communicator-add-on-software application/x-omc application/x-omcdatamaker application/x-omcregerator application/x-pagemaker application/x-pcl application/x-pixclscript application/x-pkcs10 application/x-pkcs12 application/x-pkcs7-certificates application/x-pkcs7-certreqresp application/x-pkcs7-mime application/x-pkcs7-signature application/x-project application/x-python-code application/x-qpro application/x-rar-compressed application/x-sdp application/x-sea application/x-seelogo application/x-shar application/x-shockwave-flash application/x-silverlight-app application/x-sprite application/x-stuffit application/x-stuffitx application/x-sv4cpio application/x-sv4crc application/x-tar application/x-tbook application/x-tex application/x-tex-tfm application/x-texinfo application/x-troff application/x-troff-man application/x-troff-me application/x-troff-ms application/x-visio application/x-vnd.audioexplosion.mzz application/x-vnd.ls-xpix application/x-wais-source application/x-winhelp application/x-wintalk application/x-wpwin application/x-wri application/x-x509-ca-cert application/x-x509-user-cert application/x-xfig application/x-xpinstall application/xenc+xml application/xhtml+xml application/xml application/xml-dtd application/xop+xml application/xslt+xml application/xspf+xml application/xv+xml audio/adpcm audio/it audio/make audio/make.my.funk audio/mid audio/mp4 audio/mpeg audio/ogg audio/s3m audio/tsp-audio audio/tsplayer audio/vnd.digital-winds audio/vnd.dts audio/vnd.dts.hd audio/vnd.lucent.voice audio/vnd.ms-playready.media.pya audio/vnd.nuera.ecelp4800 audio/vnd.nuera.ecelp7470 audio/vnd.nuera.ecelp9600 audio/vnd.qcelp audio/voxware audio/x-aac audio/x-adpcm audio/x-aiff audio/x-au audio/x-gsm audio/x-jam audio/x-liveaudio audio/x-matroska audio/x-mod audio/x-mpequrl audio/x-ms-wax audio/x-ms-wma audio/x-nspaudio audio/x-pn-realaudio audio/x-pn-realaudio-plugin audio/x-psid audio/x-realaudio audio/x-twinvq audio/x-twinvq-plugin audio/x-vnd.audioexplosion.mjuicemediafile audio/x-voc audio/x-wav audio/xm chemical/x-cdx chemical/x-cif chemical/x-cmdf chemical/x-cml chemical/x-csml chemical/x-pdb i-world/i-vrml image/bmp image/cgm image/cmu-raster image/fif image/florian image/g3fax image/gif image/ief image/jpeg image/jutvision image/naplps image/pict image/pjpeg image/png image/prs.btif image/svg+xml image/vnd.djvu image/vnd.fastbidsheet image/vnd.fst image/vnd.fujixerox.edmics-mmr image/vnd.fujixerox.edmics-rlc image/vnd.ms-modi image/vnd.net-fpx image/vnd.rn-realflash image/vnd.rn-realpix image/vnd.wap.wbmp image/vnd.xiff image/x-cmu-raster image/x-cmx image/x-dwg image/x-freehand image/x-icon image/x-jg image/x-jps image/x-niff image/x-pcx image/x-pict image/x-portable-anymap image/x-portable-bitmap image/x-portable-greymap image/x-portable-pixmap image/x-quicktime image/x-rgb image/x-tiff image/x-windows-bmp image/x-xwindowdump image/xbm image/xpm message/rfc822 model/iges model/mesh model/vnd.dwf model/vnd.gdl model/vnd.gtw model/vnd.mts model/vnd.vtu model/x-pov multipart/x-gzip multipart/x-ustar multipart/x-zip music/x-karaoke paleovu/x-pv text/asp text/calendar text/css text/csv text/html text/mcf text/pascal text/plain text/prs.lines.tag text/richtext text/scriplet text/tab-separated-values text/uri-list text/vnd.abc text/vnd.curl text/vnd.curl.dcurl text/vnd.curl.mcurl text/vnd.curl.scurl text/vnd.fly text/vnd.fmi.flexstor text/vnd.graphviz text/vnd.in3d.3dml text/vnd.in3d.spot text/vnd.rn-realtext text/vnd.sun.j2me.app-descriptor text/vnd.wap.si text/vnd.wap.wml text/vnd.wap.wmlscript text/webviewhtml text/x-asm text/x-audiosoft-intra text/x-c text/x-component text/x-fortran text/x-h text/x-java-source text/x-la-asf text/x-m text/x-pascal text/x-script text/x-script.csh text/x-script.elisp text/x-script.ksh text/x-script.lisp text/x-script.perl text/x-script.perl-module text/x-script.phyton text/x-script.rexx text/x-script.sh text/x-script.tcl text/x-script.tcsh text/x-script.zsh text/x-server-parsed-html text/x-setext text/x-sgml text/x-speech text/x-uil text/x-uuencode text/x-vcalendar text/x-vcard text/xml video/3gpp video/3gpp2 video/animaflex video/avs-video video/h261 video/h263 video/h264 video/jpeg video/jpm video/mj2 video/mp4 video/mpeg video/ogg video/quicktime video/vdo video/vnd.fvt video/vnd.mpegurl video/vnd.ms-playready.media.pyv video/vnd.rn-realvideo video/vnd.vivo video/vosaic video/x-amt-demorun video/x-amt-showrun video/x-atomic3d-feature video/x-dl video/x-dv video/x-f4v video/x-fli video/x-flv video/x-gl video/x-isvideo video/x-m4v video/x-matroska video/x-matroska-3d video/x-motion-jpeg video/x-mpeg video/x-mpeq2a video/x-ms-asf video/x-ms-asf-plugin video/x-ms-wm video/x-ms-wmv video/x-ms-wmx video/x-ms-wvx video/x-msvideo video/x-qtc video/x-scm video/x-sgi-movie windows/metafile www/mime x-conference/x-cooltalk x-music/x-midi x-world/x-3dmf x-world/x-svr x-world/x-vrml x-world/x-vrt xgl/drawing xgl/movie".split(' '),
          minimumInputLength: 3,
          tokenSeparators:  [",", " "]
        });
      }

      var alertLayout = $(".settings-response"),
          classList   = [ 'alert-success', 'alert-danger', 'alert-warning' ];
      form.on("submit", function(e) {
        e.preventDefault();
        alertLayout.fadeOut().empty().removeClass( classList.join( " " ) );
        var data  = $(this).serialize();
        requests.total++;
        ajax_request( form.attr("action"), data, function( response ) {
          alertLayout.addClass( classList[response.error] );
          if ( /^<p/i.test( response.message ) ) {
            alertLayout.html( response.message );
          }
          else {
            alertLayout.html( '<p>'+response.message+'</p>' );
          }
          alertLayout.fadeIn();
          requests.finish++;
          if ( requests.total == requests.finish ) {
            requests.total  = requests.finish = 0;
          }
        } );
      });
    })( $(".tb-settings"), $(".tb-settings").closest("form") );
  }
  else if ( $(".tb-groups").length ) {
    (function( table, form ) {
      $("button[name=delete]",table).on("click", function(e) {
        e.preventDefault();
        var row = $(this).closest("tr"),
              val = $("input[type=checkbox]",row).val();
      
        requests.total++;
        row.remove();
        $(".tooltip").remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
      
        ajax_request( form.attr("action"), { 'items' : [ val ], 'delete' : 1 }, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });

      $("button[name=action]").on("click", function(e) {
        e.preventDefault();
        var selected  = $("tbody input[type=checkbox]:checked",table),
            rows    = selected.parents("tr"),
            option  = $(this).prev("select[name=action-option]:first").val();
        if ( option !== "clear" ) {
          if ( !selected.length ) {
            alert( "Please select at least one group" );
            return;
          }
          if ( parseInt( option ) === 1 ) {
            alert( "Please select an action to apply" );
            return;
          }
        }
        else {
          rows  = $("tbody tr",table);
        }

        rows.remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
        requests.total++;
        var dataString  = {
          items: []
        };
        selected.each(function() {
          dataString.items.push( $(this).val() );
        });
        dataString[option]  = 1;
        ajax_request( form.attr("action"), dataString, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        });
      });
    })( $(".tb-groups"), $(".tb-groups").closest("form") );
  }
  else if ( $(".tb-notifs").length ) {
    (function( table, form ) {
      $("button[name=edit]",form).on("click", function(e) {
        e.preventDefault();
        var row = $(this).closest("tr"),
            val = $("input[type=checkbox]",row).val();
        window.location = admin_uri+"notifications.php?action=edit&id="+val;
      });
      $("button[name=delete]",form).on("click", function(e) {
        e.preventDefault();
        var row = $(this).closest("tr"),
              val = $("input[type=checkbox]",row).val();
      
        requests.total++;
        row.remove();
        $(".tooltip").remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
      
        ajax_request( form.attr("action"), { 'items' : [ val ], 'delete' : 1 }, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });
      $("button[name=action]").on("click", function(e) {
        e.preventDefault();
        var selected  = $("tbody input[type=checkbox]:checked",table),
            rows    = selected.parents("tr"),
            option  = $(this).prev("select[name=action-option]:first").val();
        if ( option !== "clear" ) {
          if ( !selected.length ) {
            alert( "Please select at least one notification" );
            return;
          }
          if ( parseInt( option ) === 1 ) {
            alert( "Please select an action to apply" );
            return;
          }
        }
        else {
          rows  = $("tbody tr",table);
        }

        rows.remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
        requests.total++;
        var dataString  = {
          items: []
        };
        selected.each(function() {
          dataString.items.push( $(this).val() );
        });
        dataString[option]  = 1;
        ajax_request( form.attr("action"), dataString, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        });
      });
    })( $(".tb-notifs"), $(".tb-notifs").closest("form") );
  }
  else if ( $(".tb-users").length ) {
    (function( table, form ) {
      $("button[name=delete]",form).on("click", function(e) {
        e.preventDefault();
        var row = $(this).closest("tr"),
            val = $("input[type=checkbox]",row).val();


        if ( !confirm( "Are you sure? You want to remove this user?" ) ) {
          return false;
        }

        requests.total++;
        row.remove();
        $(".tooltip").remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }
      
        ajax_request( form.attr("action"), { 'items' : [ val ], 'delete' : 1 }, function() {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
        } );
      });
      $("button[name=block], button[name=unblock]",form).on("click", function(e) {
        var is_block  = $(this).is("button[name=block]");
        var row = $(this).closest("tr"),
            val = $("input[type=checkbox]",row).val(),
            rol = function( mode, elem ) {
              if ( mode ) {
                row.addClass("danger");
                elem.removeClass("btn-info").addClass("btn-success").attr({"name":"unblock","data-original-title":"Unlock User"}).find("span").removeClass("fa-circle").addClass("fa-circle-o");
              }
              else {
                row.removeClass("danger");
                elem.removeClass("btn-success").addClass("btn-info").attr({"name":"block","data-original-title":"Block User"}).find("span").removeClass("fa-circle-o").addClass("fa-circle");
              }
            };

        requests.total++;
        rol( is_block, $(this) );
        $(".tooltip").remove();
        if ( !$("tbody tr",table).length ) {
          $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
        }

        var that  = $(this),
            data  = ( is_block ) ? { 'items' : [ val ], 'block' : 1 } : { 'items' : [ val ], 'unblock' : 1 };
        ajax_request( form.attr("action"), data, function( res ) {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
          if ( !res || res.error ) {
            rol( !is_block, that );
          }
        } );
      });
      $("button[name=action]").on("click", function(e) {
        e.preventDefault();
        var selected  = $("tbody input[type=checkbox]:checked",table),
            rows    = selected.parents("tr"),
            option  = $(this).prev("select[name=action-option]:first").val();

        var rol = function( mode ) {
              console.log( rows );
              if ( mode ) {
                rows.addClass("danger");
                $("button[name=block]",rows).each(function() {
                  $(this).removeClass("btn-info").addClass("btn-success").attr({"name":"unblock","data-original-title":"Unlock User"}).find("span").removeClass("fa-circle").addClass("fa-circle-o");
                });
              }
              else {
                rows.removeClass("danger");
                $("button[name=unblock]",rows).each(function() {
                  $(this).removeClass("btn-success").addClass("btn-info").attr({"name":"block","data-original-title":"Block User"}).find("span").removeClass("fa-circle-o").addClass("fa-circle");
                });
              }
            };
        if ( option !== "clear" ) {
          if ( !selected.length ) {
            alert( "Please select at least one users" );
            return;
          }
          if ( parseInt( option ) === 1 ) {
            alert( "Please select an action to apply" );
            return;
          }
          if ( option == "block" ) {
            if ( !confirm( "Are you sure? You want to block selected user(s)?" ) ) {
              return false;
            }
          }
          else if ( option == "unblock" ) {
            if ( !confirm( "Are you sure? You want to unblock selected user(s)?" ) ) {
              return false;
            }
          }
          else {
            if ( !confirm( "Are you sure? You want to remove selected user(s)?" ) ) {
              return false;
            }
          }
        }
        else {
          rows  = $("tbody tr",table);
          if ( !confirm( "Are you sure? You want to remove all users?" ) ) {
            return false;
          }
        }

        if ( option == "delete" ) {
          rows.remove();
          if ( !$("tbody tr",table).length ) {
            $("tbody",table).empty().html('<tr class="empty-table-row"><td colspan="'+$("thead tr:first th",table).length+'"><div class="alert alert-info text-md"><p>Please wait while we finish requests and redirecting you&hellip;</p></div></td></tr>');
          }
        }
        else if ( option == "block" || option == "unblock" ) {
          rol( option == "block" );
        }

        requests.total++;
        var dataString  = {
          items: []
        };
        selected.each(function() {
          dataString.items.push( $(this).val() );
        });
        dataString[option]  = 1;
        ajax_request( form.attr("action"), dataString, function( res ) {
          requests.finish++;
          if ( requests.total == requests.finish && $(".empty-table-row",table).length ) {
            requests.total  = requests.finish = 0;
            window.location.href  = window.location.href;
          }
          if ( option == "block" || option == "unblock" ) {
            if ( !res || res.error ) {
              rol( !( option == "block" ) );
            }
          }
        });
      });
    })( $(".tb-users"), $(".tb-users").closest("form") );
  }
  else if ( $(".notif-controller").length ) {
    (function( controller, form ) {
      var tagusrxhr,
          savedxhr  = {};
      $("textarea.tagged_text").textntags({
        triggers: {
          '@': {
            keys_map: { id: 'ID', title: 'NM', description: '', img: 'AV', no_img_class: 'icon', type: 'type' },
            uniqueTags: false,
            minChars: 1,
            syntax: unds.template( '@[[<%= id %>:<%= title %>]]' ),
            parser: /(@)\[\[(\d+):([\w\s@\.,-\/#!$%\^&\*;:{}=\-_`~()]+)\]\]/gi,
            parserGroups: { id: 2, title: 3 }
          }
        },
        onDataRequest: function( mode, query, triggerChar, callback ) {
          //console.log(  mode, query, triggerChar );
          // fix for overlapping requests
          if ( tagusrxhr ) {
            tagusrxhr.abort();
          }
          if ( savedxhr[query.toLowerCase()] ) {
            var bdata = savedxhr[query.toLowerCase()];
            query = query.toLowerCase();
            var found = unds.filter( bdata, function( item ) { return item.NM.toLowerCase().indexOf( query ) > -1; } );
            callback.call( this, found );
            return;
          }
          tagusrxhr = $.post( chat_uri+"ipChat/users-tokens.php", { search : query }, function( responseData ) {
            if ( responseData ) {
              savedxhr[query.toLowerCase()] = responseData;
            }
            query = query.toLowerCase();
            var found = unds.filter( responseData, function( item ) { return item.NM.toLowerCase().indexOf( query ) > -1; } );
            callback.call( this, found );
            tagusrxhr = false;
          }, "json" );
        }
      });
      $("input.hang-selector",controller).select2({
        placeholder: "Search for a user",
        minimumInputLength: 1,
        id: function( user ) {
          return user.ID;
        },
        allowClear: true,
        ajax: {
          dataType: "json",
          contentType: "application/json; charset=utf-8",
          url: chat_uri+"ipChat/users-tokens.php",
          data: function( term, page ) {
            return {
              search: term
            };
          },
          results: function( data ) {
            var tmp = [];
            for( x in data ) {
              tmp.push( data[x] );
            }
            return {
              results: tmp
            };
          },
          type: "POST"
        },
        initSelection: function( element, callback ) {
          var id  = $(element).val();
          if ( id !== "" ) {
            $.post( chat_uri+"ipChat/users-tokens.php", {
              search: "",
              id: id
            }, "json" ).done( function( data ) { callback( data ); } );
          }
        },
        formatResult: function( user ) {
          var markup  = "<table class='user-result'><tr><td class='user-image'><img src='"+( user.AV || admin_uri+"images/no_img_50.png" )+"'/></td><td class='user-info'><div class='user-name'>"+user.NM+"</div></td></tr></table>";
          return markup;
        },
        formatSelection: function( user ) {
          var markup  = "<table class='user-result'><tr><td class='user-image'><img src='"+( user.AV || admin_uri+"images/no_img_50.png" )+"'/></td><td class='user-info'><div class='user-name'>"+user.NM+"</div></td></tr></table>";
          return markup;
        },
        escapeMarkup: function( m ) {return m;},
        dropdownCssClass: "bigdrop"
      });
      form.on("submit", function( e ) {
        $(this).off("submit").trigger("submit");
      });
    })( $(".notif-controller"), $(".notif-controller").closest("form") );
  }
  else if ( $(".tb-languages").length ) {
    (function( table, form ) {
      if ( !table.hasClass("tb-languages-install") ) {
        var default_data  = form.serialize();
        $(window).bind("beforeunload", function(e) {
          var is_disabled = false;
          if ( $("textarea:disabled",form).length ) {
            is_disabled = true;
            $("input, button, select, textarea").removeAttr("disabled");
          }
          var current_data  = form.serialize();
          if ( default_data != current_data ) {
            return "There is some unsaved changes.";
          }
          else {
            $("input, button, select, textarea").prop("disabled", true);
          }
        });
        $("textarea",form).autosize().tooltip({
          trigger: "focus",
          title: function() {
            return $(this).attr("placeholder");
          },
          placement: "auto top",
          container: ".tooltip-popover"
        });
        form.on("submit", function(e) {
          e.preventDefault();
        });
        $("table tbody td",form).on("click", function(e) {
          if ( $("textarea",this).length && !$(e.target).is("textarea") ) {
            $("textarea",this).focus();
          }
        });
        $("button[name=fill]",form).on("click", function(e) {
          e.preventDefault();
          var textarea  = $("textarea", form);
          textarea.each(function() {
            $(this).val( $(this).attr("placeholder") );
          });
        });
  
        var lang_editor = $($("tr#lang-chooser-lists td select").get(0).outerHTML),
            lang_choose = $($("tr#lang-creator-lists td select").get(0).outerHTML),
            trans_btn   = $("button[name=ime]",form);
        $("tr#lang-chooser-lists, tr#lang-creator-lists",form).remove();
  
        var lang_selector_btn = $("button[name=select]",form).popover({
          content: lang_editor,
          html: true,
          placement: 'left'
        }).on("click",function(e) {
          lang_chooser_btn.popover("hide");
        }),
        lang_chooser_btn  = $("button[name=create]",form).popover({
          content: lang_choose,
          html: true,
          placement: 'left'
        }).on("click",function(e) {
          lang_selector_btn.popover("hide");
        });
  
        (function( selector, chooser ) {
          selector.data( "bs.popover" ).options.content.on("change", function(e) {
            e.preventDefault();
            var uri = admin_uri+"languages.php?lang="+$(this).val();
            $("input, textarea, button, select", form).prop("disabled", true);
            ( chooser && chooser.popover("hide") );
            $(".popover").not(".in").remove();
            window.location.href  = uri;
          });
          chooser.data( "bs.popover" ).options.content.on("change", function(e) {
            e.preventDefault();
            var uri = admin_uri+"languages.php?lang="+$(this).val();
            $("input, textarea, button, select", form).prop("disabled", true);
            ( selector && selector.popover("hide") );
            $(".popover").not(".in").remove();
            window.location.href  = uri;
          });
        })( lang_selector_btn, lang_chooser_btn );
  
        if ( trans_btn.length ) {
          trans_btn.on("click", function(e) {
            e.preventDefault();
            if ( $(this).hasClass("active") ) {
              $(this).removeClass("active").attr("data-original-title", "Turn on Instant Translation").find("span.glyphicon").addClass("glyphicon-star-empty").removeClass("glyphicon-star");
            }
            else {
              $(this).addClass("active").attr("data-original-title", "Turn off Instant Translation").find("span.glyphicon").addClass("glyphicon-star").removeClass("glyphicon-star-empty");
            }
            $(".tooltip").remove();
            $(this).tooltip("show");
          });
          $("textarea",form).on("keyup", function(e) {
            if ( !trans_btn.hasClass("active") ) {
              return;
            }
            if ( e.keyCode != 32 ) {
              return;
            }
            if ( e.shiftKey ) {
              return;
            }
            var text  = $.trim( $(this).val() ),
                event = e.type,
                lang  = $("input[name=lang_idn]",form).val();
            if ( !text.length ) {
              console.log( "Not enough text" );
              return;
            }
            var word  = t.find( this );
            if ( !word || !word.length || !word[0] ) {
              return;
            }
            if ( word[0].match( /[\x00-\x80]+/gi ) && lang === "en" ) {
              return;
            }
            var cache = t.cache( word, lang );
            if ( cache ) {
              console.log( cache );
              t.parse( $(this), word, cache, lang );
              return;
            }
            t.translate( $(this), word, lang );
          });
        }
  
        $("button[name=save]",form).on("click", function(e) {
          e.preventDefault();
          var textarea  = $("textarea", form),
              perfect   = true;
          if ( !textarea.length ) {
            return;
          }
          textarea.each(function() {
            var trans = $.trim( $(this).val().toString() );
            if ( !trans.length ) {
              $(this).focus();
              perfect = false;
              return false;
            }
          });
          if ( !perfect ) {
            return;
          }
  
          var data  = form.serialize()+"&save=1",
              ajax  = $.post( admin_uri+"includes/pages/languages.php", data, false, "json" );
      
          $("input, textarea, button, select",form).prop("disabled", true);
          ajax.success(function( response ) {
            if ( !response || response.error ) {
              stickyMessage( response.message, "error" );
              return;
            }
            default_data  = form.serialize();
            stickyMessage( response.message, "success" );
          });
          ajax.error(function() {
            stickyMessage( 'We were unable to update current language', "error" );
          });
          ajax.always(function() {
            $("input, textarea, button, select",form).removeAttr("disabled");
          });
        });
  
        $("button[name=delete]",form).on("click", function(e) {
          e.preventDefault();
          if ( !confirm( "Are you sure? You want to delete this language permanently?" ) ) {
            return;
          }
          var data  = { 'delete' : true, 'lang_idn' : $("input[name=lang_idn]",form).val() },
              ajax  = $.post( admin_uri+"includes/pages/languages.php", data, false, "json" );
      
          $("input, textarea, button, select").prop("disabled", true);
          ajax.success(function( response ) {
            if ( !response || response.error ) {
              stickyMessage( response.message, "error" );
              $("input, textarea, button, select",form).removeAttr("disabled");
              return;
            }
            stickyMessage( response.message, "success" );
            window.location.href  = admin_uri+"languages.php?lang=en";
          });
          ajax.error(function() {
            stickyMessage( 'We were unable to delete this language', "error" );
            $("input, textarea, button, select",form).removeAttr("disabled");
          });
        });
      }
      else {
        $("button[name=install]").on("click", function() {
          var $this = $(this),
              $data = $this.data(),
              $row  = $this.parents("tr:first");

          if ( $row.hasClass("success") ) {
            if ( !confirm( "You have already installed this language, Are you sure? you want to continue? (It will overwrite the previous version)" ) ) {
              return false;
            }
          }

          stickyMessage( "Installing \""+$data.name+"\"&hellip;" );
          $this.prop("disabled", true);
          $row.removeClass("success danger").addClass("warning");

          var data  = { 'lang_idx' : $data.id, 'lang_idn' : $data.name, 'install' : true },
              ajax  = $.post( admin_uri+"includes/pages/languages.php", data, false, "json" );
          ajax.success(function( response ) {
            if ( !response || response.error ) {
              stickyMessage( response.message, "error" );
              $this.removeAttr("disabled");
              $row.removeClass("warning").addClass("danger");
              return;
            }
            stickyMessage( response.message, "success" );
            $this.removeAttr("disabled");
            $row.removeClass("warning").addClass("success");
          });
          ajax.error(function() {
            stickyMessage( 'Could not install "'+$data.name+'"', "error" );
            $this.removeAttr("disabled");
            $row.removeClass("warning").addClass("danger");
          });
        });
      }
    })( $(".tb-languages"), $(".tb-languages").closest("form") );
  }
  else if ( $(".tb-profile").length ) {
    (function( table, form ) {
      form.on("submit", function(e) {
        e.preventDefault();

        var name  = $.trim( $("#admin-name",form).val() ),
            email = $.trim( $("#admin-email",form).val() ),
            pold  = $.trim( $("#admin-old-pass",form).val() ),
            pnew  = $.trim( $("#admin-new-pass",form).val() ),
            pret  = $.trim( $("#admin-retype-pass",form).val() );
        if ( !name.length ) {
          $("#admin-name",form).trigger("focus");
          return false;
        }
        if ( !email.length ) {
          $("#admin-email",form).trigger("focus");
          return false;
        }
        if ( pold.length ) {
          if ( !pnew.length ) {
            $("#admin-new-pass",form).trigger("focus");
            return false;
          }
        }
        if ( pnew.length ) {
          if ( !pret.length || pnew !== pret ) {
            $("#admin-retype-pass",form).trigger("focus");
            return false;
          }
        }

        var data  = form.serialize(),
            ajax  = $.post( admin_uri+"includes/pages/profile.php", data, false, "json" );
      
        $("input, textarea, button, select",form).prop("disabled", true);
        ajax.success(function( response ) {
          if ( !response || response.error ) {
            stickyMessage( response.message || 'We were unable to update your profile', "error" );
            $("input, textarea, button, select",form).removeAttr("disabled");
            return;
          }
          default_data  = form.serialize();
          stickyMessage( response.message, "success" );
          setTimeout(function() {
            window.location.href  = window.location.href;
          }, 1000)
        });
        ajax.error(function() {
          stickyMessage( 'We were unable to update your profile', "error" );
          $("input, textarea, button, select",form).removeAttr("disabled");
        });
      });
      $("input#admin-avatar").on("change", function(e) {
        $("._m").addClass("_p");
        $(this).closest("form").trigger("submit").trigger("reset");
      });
    })( $(".tb-profile"), $(".tb-profile").closest("form") );
  }
  else if ( $(".tb-updates").length ) {
    (function( table, form ) {
      if ( table.is( "table" ) ) {
        $("button[name=install]").on("click", function(e) {
          e.preventDefault();
          var row = $(this).parents("tr:first");
          $("input",row).removeAttr("disabled");
          form.off("submit").trigger("submit");
          $("button,input",form).prop("disabled", true);
        });
      }
      else {
        
      }
    })( $(".tb-updates"), $(".tb-updates").closest("form") );
  }

  if ( $(".tb-global").length ) {
    (function( table, form ) {
      form.on("submit", function(e) {
        e.preventDefault();
      });
      $("select",form).not(".hang-selector").removeClass("form-control input-sm input-s-sm").select2({
        searchContainer: false
      });
      $("input[name=search]").on("keydown", function(e) {
        var value = $.trim( $(this).val() );
        if ( e.keyCode === 13 ) {
          e.preventDefault();
          if ( value ) {
            if ( $(this).data("href") ) {
              return window.location  = $(this).data("href").replace("%s",value);
            }
            return window.location.search  = "?search="+value;
          }
          return false;
        }
      });
      $("button[name=search-action]").on("click", function(e) {
        e.preventDefault();
        var $input  = $(this).parents("form:first").find("input[name=search]");
        var value = $.trim( $input.val() );
        if ( !value ) {
          $input.trigger("focus");
          return false;
        }
        if ( $input.data("href") ) {
          return window.location  = $input.data("href").replace("%s",value);
        }
        return window.location.search  = "?search="+value;
      });
      $("select[name=action-option]",form).on("change", function() {
        $("select[name=action-option]",form).val( $(this).val() );
      });
      $(".m-ellipse").each(function() {
        if ( $(this).text() == $(this).parent().data("original-title") ) {
          $(this).parent().removeAttr("data-original-title").removeAttr("data-toggle").removeAttr("data-placement");
        }
      })
    })( $(".tb-global"), $(".tb-global").closest("form") );
  }


  $(".db-message-text").each(function(){
    /*var text  = $.trim( $(this).html() );
    var regex = /(((http|ftp|https):\/\/)[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/gi;
    if ( text.match( regex ) ) {
      text  = text.replace( regex, '<a href="$1" target="_blank" rel="nofollow">$1</a>' );
      $(this).html( text );
    }*/
    if ( this.scrollHeight > $(this).height() ) {
      $(this).addClass("has-scrollbar");
    }
  });

  $(".ellipsis.ellipsis-max").each(function() {
    $(this).css("max-width",$(this).parent().width()+"px");
  });

  if ( $(".file-system-tree").length ) {
    (function( tree ) {
      $("li.tree-item.tree-folder-item > a",tree).on("click", function(e) {
        e.preventDefault();
        $(this).parent().toggleClass("open");
      });
    })( $(".file-system-tree") );
  }

  $(window).on("beforeunload", function() {
    if ( requests.total > requests.finish ) {
      return "Some requests are in pending";
    }
  });

  if ( window.screenfull ) {
    screenfull.onchange = function() {
      if ( !screenfull.isFullscreen && $("body").hasClass("fullscreen") ) {
        $("body").removeClass("fullscreen");
        $("a.go-fullscreen .fa-compress").addClass("sr-only");
        $("a.go-fullscreen .fa-expand").removeClass("sr-only");
        $("a.go-fullscreen").attr("data-original-title","Enter Fullscreen");
      }
    };
  }

  $("a.go-fullscreen").on("click", function(e) {
    e.preventDefault();
    $("body").toggleClass("fullscreen");
    if ( $("body").hasClass("fullscreen") ) {
      $(".fa-compress",this).removeClass("sr-only");
      $(".fa-expand",this).addClass("sr-only");
      $(this).attr("data-original-title","Exit Fullscreen");
      if ( screenfull.enabled && !screenfull.isFullscreen ) {
        screenfull.request();
      }
      return;
    }
    $(".fa-compress",this).addClass("sr-only");
    $(".fa-expand",this).removeClass("sr-only");
    $(this).attr("data-original-title","Enter Fullscreen");
    if ( screenfull.enabled && screenfull.isFullscreen ) {
      screenfull.exit();
    }
  });

  $("img[data-src]").each(function() {
    this.onload = function() {
      $(this).addClass("loaded").removeAttr("data-src");
    };
    this.onerror  = function() {
      $(this).addClass("error");
      this.onload = this.onerror  = null;
      this.src  = this.getAttribute( 'data-src' );
    };
    var data_src  = this.getAttribute( 'data-src' );
    this.setAttribute( 'data-src', this.src );
    this.src  = data_src;
  });

  if ( typeof $.prettyPhoto != "undefined" ) {
    $('a[rel^="prettyPhoto"]').prettyPhoto({
      slideshow: 2000,
      autoplay_slideshow: false,
      social_tools: '',
      gallery_markup: ''
    });
  }

  $("select[data-href]").on("change", function(e) {
    var url = $(this).data("href"),
        val = $(this).val();
    var href  = url.replace( '%s%', val );
    window.location = href;
  });

  $("table thead th").on("click", function(e) {
    if ( !$("a",this).length ) {
      return;
    }
    e.preventDefault();
    window.location = $("a",this).attr("href");
  });
});

function change_avatar( src ) {
  if ( $("._m").length ) {
    if ( src ) {
      src = src+"?"+time();
      var img = $("._m img:first");
      var old_src = img.attr("src");
      img[0].onload = img[0].onerror  = function(e) {
        $("._m").removeClass("_p");
        if ( e.type === "error" ) {
          img.attr("src", old_src);
        }
        else {
          $(".thumb-sm.avatar img").attr("src", src);
        }
      };
      img.attr("src", src);
    }
  }
}
function ellipsis( string, maxlen ) {
  maxlen  = Math.max( 10, ( maxlen || 10 ) );
  string  = $.trim( string );
  if ( !( length = string.length ) ) {
    return string;
  }
  if ( length > maxlen ) {
    maxlen  = ( maxlen - 2 );
    middle  = Math.round( length / 2 );
    str1    = string.substr( 0, ( maxlen / 2 ) );
    str2    = string.substr( "-"+( maxlen / 2 ) );
    string  = str1+"..."+str2;
  }
  return string;
}
function ajax_request( a, b, c ) {
  if ( !a ) {
    return false;
  }
  var e = $("#content form header.panel-heading:first");
  if ( e.length ) {
    var f = $(".ajax-counter").stop( true );
    if ( !f.length ) {
      f = $("<div />",{"class":"pull-right ajax-counter"}).html('<div class="progress progress-xs m-t-xs m-b-none progress-striped active"><div class="progress-bar progress-bar-info"></div></div> <span>0 / 0</span>').appendTo( e );
    }
    $(".progress-bar",f).width( ( 100 / requests.total * requests.finish )+"%" );
    $("span",f).text( requests.finish+" / "+requests.total );
  }
  b = b || {};
  var d = $.post( a, b, false, "json" );
  if ( c ) {
    d.always( c ).always(function() {
      $(".progress-bar",f).width( ( 100 / requests.total * requests.finish )+"%" );
      $("span",f).text( requests.finish+" / "+requests.total );
      if ( requests.finish == requests.total ) {
        requests.finish = requests.total  = 0;
        f.remove();
      }
    });
  }
}
function formatFileSize(b,p) {
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
};
var progressBar = {
  createBar: function( bar, classname, method, target ) {
    method  = method || "appendTo";
    target  = target || $("body");
    classname = classname || '';

    if ( bar && bar.length ) {
      return ( this.hasProgress() ) ? bar : $(".progress-bar",bar);
    }
    if ( this.hasProgress() ) {
      return $("<progress />",{"max":"100","value":"0"}).addClass( classname )[method]( target );
    }
    else {
      return $("<div />").addClass( "progress-bar progess-info" ).appendTo( $("<div />").addClass( "progress progress-xs m-t-xs m-b-none progress-striped active" ).addClass( classname )[method]( target ) );
    }
    
  },
  moveBar: function( bar, percentage ) {
    if ( !bar || !bar.length ) {
      return;
    }
    if ( this.hasProgress() ) {
      bar.stop().show( 0 ).animate({value:percentage},200);
    }
    else {
      if ( $(".progress-bar",bar).length ) {
        $(".progress-bar",bar).width( percentage+"%" ).parent().show( 0 );
      }
      else {
        bar.width( percentage+"%" ).parent().show( 0 );
      }
    }
  },
  hasProgress: function() {
    return false;
    var a = document.createElement("progress");
    return ( ( a.constructor && a.constructor.name && a.constructor.name.toLowerCase() === "htmlprogresselement" ) || "max" in a );
  }
};
function transloader_status( message, last ) {
  $(".transload-status").html( message );
  if ( last ) {
    $(".extraction-status, .transloader-progress").remove();
    $("<a />",{"class":"btn btn-success btn-sm","href":decodeURIComponent( $('input[name="plugin-referer"]').val() )}).html('<i class="fa fa-arrow-left"></i> Go back').insertAfter( $(".transload-status") );
  }
};
function transloader_progress( current, total ) {
  var progress  = progressBar.createBar( $(".transloader-progress"), "transloader-progress", "insertAfter", $(".transload-status") );

  if ( total < 0 ) {
    progressBar.moveBar( progress, 100 );
  }
  else {
    var percentage  = Math.round( ( 50 / total ) * current );
    progressBar.moveBar( progress, percentage );
  }
};
function transloader_extraction( current, total, percentage, filename, folder ) {
  var progress  = progressBar.createBar( $(".transloader-progress"), "transloader-progress", "insertAfter", $(".transload-status") );

  var status    = ( $(".extraction-status").length ) ? $(".extraction-status") : $("<p />",{"class":"extraction-status text-muted text-sm"}).insertAfter( ( progress.hasClass( "transloader-progress" ) ) ? progress : progress.parent() );
  if ( total <= 0 ) {
    progressBar.moveBar( progress, 100 );
  }
  else {
    status.html( ( folder ? "Folder:" : "File:" )+" <strong>"+filename+"</strong>" );
    progressBar.moveBar( progress, percentage );
  }
};
function initializeEditorNative( area ) {
  $(".codemirror-size").text( "("+formatFileSize( area.val().length )+")" );
  area.removeAttr("disabled").autosize().on("keydown", function(e) {
    revision  = $(this).val();
    if ( e.keyCode === 9 ) {
      e.preventDefault();
      $(this).insertAtCaret( "\t" );
      return;
    }
    if ( e.keyCode === 90 && e.ctrlKey ) {
      revision  = false;
      e.preventDefault();
      if ( revisions.length ) {
        $(this).val( revisions.pop() );
        revisions.splice( -1 );
      }
      return;
    }
  }).on("keyup", function(e) {
    $(".codemirror-size").text( "("+formatFileSize( $(this).val().length )+")" );

    var current = $(this).val();
    if ( current == revision ) {
      revision  = false;
      return;
    }
    if ( revision !== false ) {
      revisions.push( revision );
    }
  });
};

var t = {
  well: {},
  find: function( textarea ) {
    var cpos  = t.caretPosition( textarea ),
        word  = t.returnWord( textarea.value, cpos );
    return word;
  },
  caretPosition: function( textarea ) {
    var caretPos  = 0;
    if ( document.selection ) {
      textarea.focus();

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
      textarea.focus();
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
    if ( parts && parts.length ) {
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
  cache: function( word, lang ) {
    var cache = t.well || {};
    return ( cache[lang] && cache[lang][word] ) ? cache[lang][word] : false;
  },
  translate: function( textarea, word, lang ) {
    var ajax  = $.post( admin_uri+"includes/translate.php", { l : lang, s : word[0] }, false, "json" );
    ajax.success(function( response ) {
      t.parse( textarea, word, response.text, lang );
    });
  },
  parse: function( textarea, search, replace, lang ) {
    if ( !replace || !replace.length ) {
      return;
    }
    t.well[lang]  = t.well[lang] || {};
    t.well[lang][search]  = replace;

    var caretposition = t.caretPosition( textarea[0] );
    var text  = textarea.val();

    var search_length   = ''+search[0].split("").length,
        replace_length  = ''+replace.split("").length;

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
    t.setCaretPosition( textarea[0], caretposition );
  }
};

var stickyTimer = false;
var stickyMessage = function( message, type ) {
  if ( stickyTimer ) {
    clearTimeout( stickyTimer );
  }
  var _5kdv = $("._5kdv"),
      _5kgu = $("._5kgu i",_5kdv),
      _5kgv = $("._5kgv",_5kdv),
      types = [ "error", "success", "info", "warning" ],
      icons_obj = { "error" : "fa-times", "success" : "fa-check", "info" : "fa-alert", "warning" : "fa-alert" },
      icons_arr = [ "fa-times", "fa-check", "fa-alert", "fa-alert" ];
      type  = ( type && $.inArray( type, types ) !== -1 ) ? type : "info";
  _5kdv.removeClass( types.join( " " ) ).addClass( type );
  _5kgu.removeClass( icons_arr.join( " " ) ).addClass( icons_obj[type] );
  _5kgv.html( message );
  _5kdv.stop(true).css("visibility","visible").css("opacity",1);
  stickyTimer = setTimeout(function() {
    _5kdv.stop(true).css("opacity",0).css("visibility","hidden");
  }, 5000)
};