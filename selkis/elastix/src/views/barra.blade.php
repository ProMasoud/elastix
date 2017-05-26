<html>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.css"/>
<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>


<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrap-switch.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>

<style>
.top{ margin-top:20px;}
.left{float:left; margin-left:16px;}
.top a {
    display: none;
}
</style>

<body bgcolor="transparent" style="background-color: #E3A21A !important; padding: 0; margin:0; border: none !important; overflow:hidden">

<div class="top row">
    <div class="left">
        <a id="login" href="#" onClick="agentlogin();" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-earphone"></span> Recibir llamadas</a>
          <a id="logout" href="#" onClick="agentlogout();" class="btn  btn-sm btn-primary btn-danger"><span class="glyphicon glyphicon-ban-circle"></span> No disponible</a>
          <a id="hangup" href="#" onClick="hangup();" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-phone-alt"></span> Colgar</a>
          <a id="break" href="#" onClick="abreak();" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-cutlery"></span> Almuerzo</a>
          <a id="unbreak" href="#" onClick="unbreak();" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-cutlery"></span> Volver de Almuerzo</a>
          <a id="hold" href="#" onClick="hold();" class="btn btn-sm btn-primary btn-primary"><span class="glyphicon glyphicon-pause"></span> Hold</a>
          <a id="unhold" href="#" onClick="unhold();" class="btn btn-sm btn-primary btn-primary"><span class="glyphicon glyphicon-play"></span> Unhold</a>
          
          <input name="extnum" type="text" placeholder="Ext. a transferir" id="extnum" />
          <button id="transfer" href="#" onClick="transfer(this);" class="btn btn-sm btn-default"><span class="glyphicon"></span>Transferir </button>
    
          <input name="num" type="text" placeholder="Numero" id="callnum" />
          <button id="llamar" href="#" onClick="call(this);" class="btn btn-sm btn-success"><span class="glyphicon"></span>Llamar </button>
          <a id="colgar" href="#" onClick="colgar();" class="btn btn-default"><span class="glyphicon glyphicon-phone-alt"></span> Colgar</a>   
    </div>
    <img id="loading" width="100" height="30" src="img/loading_dots.gif" class="img-responsive" alt="Image">
</div>

<script>
$(document).ready(function(e) {
$('#loading').hide();
setInterval(function(){
        $.ajax({
          url: "/status",
          beforeSend: function( xhr ) {
            $('#loading').show();
            $('.top a, #extnum, #transfer, #callnum, #llamar, #colgar').hide();
          },
          success: function(data){
            $('#loading').hide();
            if( data.status == 'offline' ) {
                $('.top a#login').show();
                $('.top a#llamar').show();
                $('#llamar').show();
                $('#callnum').show();
                $('#colgar').show();
            } else if( data.status == 'online' ) {
                $('.top a#logout').show();
                $('.top a#hold').show();
                $('.top a#break').show();
            } else if( data.status == 'oncall' ) {
                $('.top a#hold').show();
                $('.top a#hangup').show();
                $('#transfer').show();
                $('#extnum').show();
            } else if( data.status == 'paused' ) {
                $('.top a#logout').show();
                $('.top a#unbreak').show();
            }else{
                $('.top a#login').show();
                $('.top a#llamar').show();
                $('#llamar').show();
                $('#callnum').show();
                $('#colgar').show();
            }
            if( data.onhold > 0 ) {
                $('.top a#unhold').show();
                $('.top a#hold').hide();
            }
          },
          error: function(error){
            $('#loading').hide();
            console.log(error);
          }
        })
    }, 1000);
});

function agentlogin() {
    $.post( "/agentlogin");
}

function agentlogout() {
    $.post( "/agentlogout");
}

function abreak() {
    $.post( "/break");
}

function unbreak() {
    $.post( "/unbreak");
}

function hold() {
    $.post( "/park");
}

function unhold() {
    $.post( "/unhold");
}

function hangup() {
    $.post( "/hangup");
}

function colgar() {
    $.post( "/cuelga");
}

function call(button) {
    if( $('input#callnum').val() != "" ) 
     $.ajax({
      type: "POST",
      url: "/call",
      data: { destino: $('input#callnum').val() }
    })
      .done(function() {
        button.setAttribute('disabled', true);
        setTimeout(function(){
            button.removeAttribute('disabled');
        }, 10000)
          
      });
 }
 
 function transfer(button) {
    if( $('input#extnum').val() != "" ) 
     $.ajax({
      type: "POST",
      url: "/transfer",
      data: { extnum: $('input#extnum').val() }
    })
      .done(function() {
        button.setAttribute('disabled', true);
        setTimeout(function(){
            button.removeAttribute('disabled');
        }, 3000)
          
      });
 }

</script>

</body>


</html>