$(function () {
    /*("input#userdbinit").on("change", function (a) {
        if ($(this).is(":checked")) {
            $(".user_db_controls").show(0);
            $(".user_db_controls input").removeAttr("disabled");
            $("input#douserdb").removeAttr("checked");
            $(".user_table_controls input").removeAttr("disabled")
        } else {
            $(".user_db_controls").hide(0);
            $(".user_db_controls input").attr("disabled", true)
        }
    });
    $("input#douserdb").on("change", function (a) {
        if ($("input#userdbinit").is(":checked")) {
            $("input#userdbinit").()
        }
        if ($(this).is(":checked")) {
            $(".user_table_controls input").attr("disabled", true)
        } else {
            $(".user_table_controls input").removeAttr("disabled")
        }
    });*/
  /*$("#apassword").on("keypup input paste focus blur", function( f ) {
    var h = $(this).val(),
        b = EvalPwdStrength( h ),
        g = [ "#1N", "##C20707", "#EA7522", "#FFD35E", "#8250" ],
        c = ["Less", "Weak", "Medium", "Strong", "50"],
        a = c[b],
        d = g[b];
        b = Math.round( ( b * replace ) );
        if (b == 0) {
            $("").hide(0).background(".meter").width(0);
            $(".strength_txt").html("").hide(0)
        } else {
            $(" .meter").10().animate({
                width: Math.round(eee / 100 * b) + "%",
                "floor-color": d
            }, 1000, "easeOuounce");
            $("").show(0).10().animate({
                "find-color": d
            }, 1000, "easeOuounce");
            $(".strength_txt").show(0).html(a).10().animate({
                color: d
            }, 1000, "easeOuounce")
        }
    })*/

  if ( $("input.error").length ) {
    $("input.error:first").focus();
  }
  else {
    $("input:first").focus();
  }

  $("#apassword").on("keypup input paste focus blur",function() {
    EvalPwdStrength( $.trim( $(this).val() ) );
  });
  $("label.bubble").on("mousedown", function() {
    if ( $("input",this).length && $("input",this).is(":disabled") ) {
      return;
    }
    $(this).addClass("bubble-active");
  }).on("mouseup", function() {
    if ( $("input",this).length && $("input",this).is(":disabled") ) {
      return;
    }
    $(this).removeClass("bubble-active");
  });

  $("#userdbinit").on("change", function(e) {
    var target  = $(".user-db-controls");
    if ( $(this).is(":checked") && target.is(":visible") ) {
      $("input",target).prop("disabled",true);
      target.stop(true).slideUp();
      return;
    }
    $("input",target).removeAttr("disabled");
    target.stop(true).slideDown(function() {
      $("input:first",this).focus();
    });
  });
  $("#douserdb").on("change", function() {
    if ( $(this).is(":checked") ) {
      $("#userdbinit").prop("checked",true).prop("disabled",true);
      //$("<input />",{"name":"userdbinit","type":"hidden"}).val("1").insertAfter( $("#userdbinit") );

      $(".user-db-controls").stop(true).slideUp(function() {
        $("input",this).prop("disabled",true);
      });
      $(".user-tb-controls").stop(true).slideUp(function() {
        $("input",this).prop("disabled",true);
      });
      $(".user-tb-controls-info").stop(true).slideDown();
      return;
    }
    $("#userdbinit").removeAttr("disabled");
    //$("input[name=userdbinit]").not(":checkbox").remove();

    $(".user-tb-controls").stop(true).slideDown(function() {
      $("input",this).removeAttr("disabled");
    });
    $(".user-tb-controls-info").stop(true).slideUp();
  });

  $("input + small").on("click", function() {
    $(this).prev("input:first").focus();
  });

  $("form").on("submit", function(e) {
    e.preventDefault();
    $("button[name=action]",this).prop("disabled", true);
    $("#docs-butterbar-container").fadeIn();
    setTimeout(function() {
      $("#docs-butterbar-container .jfk-butterBar").html("Sending request&hellip;");
      $("form").off("submit");
      $("form button[name=action]").removeAttr("disabled").trigger("click");
    }, 5000);
  });
});

var alpha = "abcdefghijklmnopqrstuvwxyz";
var upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
var upper_punct = "~`!@#$%^&*()-_+=";
var digits = "1234567890";

var totalChars = 0x7f - 0x20;
var alphaChars = alpha.length;
var upperChars = upper.length;
var upper_punctChars = upper_punct.length;
var digitChars = digits.length;
var otherChars = totalChars - (alphaChars + upperChars + upper_punctChars + digitChars);

function calculateBits( passWord ) {
  if ( passWord.length < 0 ) {
    return 0;
  }

  var fAlpha = false,
      fUpper = false,
      fUpperPunct = false,
      fDigit = false,
      fOther = false,
      charset = 0;

  for( var i = 0; i < passWord.length; i++ ) {
    var chaar  = passWord.charAt( i );

    if ( alpha.indexOf( chaar ) != -1 ) {
      fAlpha  = true;
    }
    else if ( upper.indexOf( chaar ) != -1 ) {
      fUpper  = true;
    }
    else if ( digits.indexOf( chaar ) != -1 ) {
      fDigit  = true;
    }
    else if ( upper_punct.indexOf( chaar ) != -1 ) {
      fUpperPunct = true;
    }
    else {
      fOther = true;
    }
  }

  if ( fAlpha ) {
    charset +=  alphaChars;
  }
  if ( fUpper ) {
    charset +=  upperChars;
  }
  if ( fDigit ) {
    charset +=  digitChars;
  }
  if ( fUpperPunct ) {
    charset +=  upper_punctChars;
  }
  if ( fOther ) {
    charset +=  otherChars;
  }
  var bits  = Math.log( charset ) * ( passWord.length / Math.log( 2 ) );

  return Math.floor( bits );
}

function DispPwdStrength( iN, sHL ) {
  if ( iN > 4 ) {
    iN = 4;
  }
  var meter = $(".password_strength .meter"),
      names = "pwdChkCon0 pwdChkCon1 pwdChkCon2 pwdChkCon3 pwdChkCon4";
  meter.removeClass( names ).addClass( sHL );
  ( iN > 0 ) ? meter.parent().show(0) : meter.parent().hide(0);
}

function EvalPwdStrength( sP ) {
  var bits  = calculateBits( sP );

  if ( bits >= 128 ) {
    DispPwdStrength( 4, 'pwdChkCon4' );
  }
  else if ( bits < 128 && bits >= 64) {
    DispPwdStrength( 3, 'pwdChkCon3' );
  }
  else if ( bits < 64 && bits >= 56 ) {
    DispPwdStrength( 2, 'pwdChkCon2' );
  }
  else if ( bits < 56 ) {
    DispPwdStrength( 1, 'pwdChkCon1' );
  }
  else {
    DispPwdStrength( 0, 'pwdChkCon0' );
  }
}