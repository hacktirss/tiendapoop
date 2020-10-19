<?php
#Librerias
include_once ("lib/lib.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$connection = utils\IConnection::getConnection();

$Response = urldecode(utils\HTTPUtils::getRequest()->getAttribute("Msj"));
$htmlResponse = "";
if ($Response == 2) {
    $htmlResponse = "<div style='color:red'>Tu clave ha sido cambiada con <font size='+1'>exito!!!</font></div>";
    $htmlResponse .= "<div><font color='red'>FAVOR DE INGRESAR AHORA CON TU NUEVA CLAVE</div>";
} elseif ($Response == 3) {
    $htmlResponse = "<div style='color:red'><b>La Session ha sido cerrada por inactividad o ha expirado</b></div>";
    $htmlResponse .= "<div><b>VUELVE A INGRESAR TUS DATOS</b></div>";
} elseif ($Response == 4) {
    $htmlResponse = "<div style='color:red'>Clave actualizada con <font size='+1'>exito!!!</font></div>";
    $htmlResponse .= "<div><b>VUELVE A INGRESAR TUS DATOS</b></div>";
} elseif ($Response == 5) {
    $htmlResponse = "<div style='color:red'>La Session ha sido cerrada con <font size='+1'>exito!!!</font></div>";
    $htmlResponse .= "<div>AHORA PUEDES CERRAR EL NAVEGADOR</div>";
} elseif ($Response == 1) {
    $htmlResponse .= "<div></div>";
} else {
    $htmlResponse = "<div><b>$Response</b></div>";
}
?>

<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <title>PoopCorn Store</title>
        <link rel="shortcut icon" href="lib/iconoPoop.jpeg" type="image/x-icon"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <link rel="stylesheet" type="text/css" media="screen" href="css/estilos_colores.css?sum=<?= md5_file("css/estilos_colores.css") ?>">
        <link rel="stylesheet" type="text/css" media="screen" href="css/estilos_index.css?sum=<?= md5_file("css/estilos_index.css") ?>">
        <link rel="stylesheet" type="text/css" href="ficheros/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" href="fonts-awesome/css/font-awesome.min.css"/>
        <script type="text/javascript" src="js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="js/js-usuarios.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#spiner").hide();
                $("#Msj").html("<?= $htmlResponse ?>");
                $("#Usuario").focus();

                $("#Login").submit(function (event) {
                    event.preventDefault();
                    jQuery.ajax({
                        type: "POST",
                        url: "auth_ajax.php",
                        dataType: "json",
                        cache: false,
                        data: {"username": $("#Usuario").val(), "password": $("#Contrasenia").val()},
                        beforeSend: function (xhr) {
                            $("#Msj").hide();
                            $("#Fail").hide();
                            $("#myLoader").modal("toggle");
                        },
                        success: function (data) {
                            console.log(data);
                            if (data.success) {
                                window.location = data.redirect;
                            } else {
                                var count = parseInt(data.count) + 1;
                                if (data.count < 5) {
                                    $("#Msj").html(data.message);
                                    $("#Fail").html("Intento fallido " + count);
                                    $("#Usuario").focus();
                                    $("#myLoader").modal("toggle");
                                    $("#Msj").show();
                                    $("#Fail").show();
                                } else {
                                    window.location = "locked.php?Msj=" + data.message;
                                }
                            }
                        },
                        error: function (jqXHR, textStatus) {
                            console.log(jqXHR);
                            $("#Msj").html(textStatus);
                        }
                    });

                });
            });
        </script>
    </head>

    <body>

        <div id="inicio" class="vertical-center">
            <table aria-hidden="true" id="firstTable">
                <tr>
                    <td style="vertical-align: top;">
                        <form id="Login" method="post" action="" autocomplete="off">
                            <div class="header">
                                <div class="logo"><img src="lib/logoPoop.png" alt="Logo" onclick="location.reload();"/></div>
                            </div>
                            <div class="body">
                                <div id="boxTable">Ingrese sus datos &nbsp;<i aria-hidden="true" class="icon fa fa-lg fa-user"></i><br/>
                                    <span>
                                        <input type="text" name="username" id="Usuario" placeholder="Usuario" required="required"/>
                                    </span>
                                    <span>
                                        <input type="password" name="password" id="Contrasenia" placeholder="Contraseña" required="required" autocomplete="new-password"/>
                                    </span>
                                    <span>
                                        <button name="Login" value="Continuar"><i class="fa fa-sign-in"></i> Continuar</button>
                                    </span>
                                </div>
                                <div style="width: 100%;text-align: center;color: red;" id="Msj"></div>
                                <div style="width: 100%;text-align: center;color: red;" id="Fail"></div>
                            </div>                            
                        </form>
                    </td>
                </tr>

            </table>
        </div>

        <?php include "./modal_window_ajax.php"; ?>
    </body>
</html>
