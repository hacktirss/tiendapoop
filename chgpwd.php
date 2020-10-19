<?php
#Librerias
include_once ("lib/lib.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();

if ($request->hasAttribute("user")) {
    utils\HTTPUtils::setSessionValue("user", $request->getAttribute("user"));
}
$user = utils\HTTPUtils::getSessionValue("user");
//error_log(print_r($request, true));
if ($request->hasAttribute("Boton")) {

    $usuarios = new Usuarios();
    $usuarioVO = $usuarios->getUser($user);
    $usuarioVO->setPassword($request->getAttribute("nuevo"));
    $response = $usuarios->changePasswordUser($usuarioVO, 1);

    if ($response === Usuarios::RESPONSE_VALID) {
        BitacoraDAO::getInstance()->saveLog($usuarioVO, "ADM", "CAMBIO DE CONTRASEñA");
        header("Location: index.php?Msj=4");
    } else {
        $Msj = $response;
    }
}
?>
<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <title>Detisa-PLUS</title>
        <link rel="shortcut icon" href="favicondetisa.png" type="image/x-icon"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <link rel="stylesheet" type="text/css" media="screen" href="css/estilos_index.css?sum=<?= md5_file("css/estilos_index.css") ?>">
        <link rel="stylesheet" type="text/css" href="ficheros/css/ficherosStyles.css"/>
        <link rel="stylesheet" type="text/css" href="ficheros/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="ficheros/font-awesome/css/font-awesome.min.css"/>
        <script type="text/javascript" src="js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="js/js-usuarios.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#user").val("<?= $user ?>");
                $("#mensaje").html("<?= $Msj ?>");
                $("#Cambiar").val("Cambiar");

                $("#change_pass").submit(function (event) {

                    var password = $("#nuevo").val();
                    var password2 = $("#confirmar").val();

                    if (password !== password2) {
                        event.preventDefault();
                        $("#mensaje").html("Las contraseñas no coinciden.");
                        $("#nuevo").focus();
                        return false;
                    }

                    if (!validaPassword($("#nuevo"))) {
                        event.preventDefault();
                    }
                });

                $("#PasswordEye").mousedown(function () {
                    $(".toggle-password").toggleClass("fa-eye fa-eye-slash");
                    $("#nuevo").attr("type", "text");
                    $("#confirmar").attr("type", "text");
                }).mouseup(function () {
                    $(".toggle-password").toggleClass("fa-eye fa-eye-slash");
                    $("#nuevo").attr("type", "password");
                    $("#confirmar").attr("type", "password");
                });

                $("#nuevo").focus();
            });
        </script>
    </head>

    <body>
        <div id="inicio" class="vertical-center">
            <table id="firstTable" aria-hidden="true">               
                <tr>
                    <td style="vertical-align: top;">
                        <form id="change_pass" method="post" action="" autocomplete="off">
                            <div class="header">
                                <div class="logo"><img src="lib/detisa.png"/></div>
                                <div class="titulo">Sistema Administrativo Detisa</div>
                            </div>
                            
                            <p style="padding-bottom: 15px;">Cambio de contraseña</p>
                            
                            <div style="display: inline-block">
                                
                                <div class="body" style="width: 45%;display: inline-table;float: left">
                                    <div id="boxTable">
                                        <div style="text-align: center">Ingrese sus claves</div>
                                        <span>
                                            <label>Contraseña: <i class="icon fa fa-lg fa-key" aria-hidden="true"></i></label>                                    
                                            <input type="password" name="nuevo" id="nuevo" class="input-field" autocomplete="new-password" required/>
                                        </span>
                                        <span>
                                            <label>Confirmar: <i class="icon fa fa-lg fa-key" aria-hidden="true"></i></label>  
                                            <input type="password" name="confirmar" id="confirmar" class="input-field" autocomplete="new-password" required/>
                                            <span id="PasswordEye" toggle="#password-field" class="fa fa-fw fa-eye-slash field_icon toggle-password"></span>
                                        </span>
                                        <span style="margin-left: auto; margin-right: auto;">
                                            <button><i class="icon fa fa-lg fa-key" aria-hidden="true"></i> Continuar</button>
                                        </span>
                                    </div>  
                                </div>
                                
                                <div style="width: 45%;float: right;" class="texto_tablas" valign="top">
                                    <p style="padding-top: 30px;padding-bottom: 30px;color: #F63;">
                                        Debe cambiar su contraseña ya que ha expirado o esta entrando por primera vez.
                                    </p>
                                    <?= Usuarios::lineamientosPassword(); ?>
                                </div>
                                
                            </div>
                            <div id="mensaje" style="text-align: center; color: red"></div>
                            <input type="hidden" name="user" id="user">
                            <input type="hidden" name="Boton" id="Cambiar">
                        </form>
                    </td>
                </tr>                
            </table>
        </div>
        <?php include "./modal_window_ajax.php"; ?>
    </body>
</html>
