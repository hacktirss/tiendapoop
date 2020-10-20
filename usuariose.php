<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");
include_once ("service/ListasCatalogo.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$sanitize = SanitizeUtil::getInstance();

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

$Return = "usuarios.php";
$Titulo = "Detalle de usuario";

require_once "./service/UsuariosService.php";

$objectVO = new UsuarioVO();
if (is_numeric($busca)) {
    $objectVO = $usuarios->getUser($busca);
} else {
    $objectVO->setTeam(3);
}
$btnGenerar = " <span class='generar' id='Generar'>Generar contraseña</span>";

?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
        <script type="text/javascript" src="js/js-usuarios.js"></script>
    </head>
    
    <body>

        <?php BordeSuperior(); ?> 

        <form name="form1" id="form1" method="post" action="" autocomplete="off">
            <div id="Formularios">
                <table>
                    <tbody>
                        <tr>
                            <td>        
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos del usuario</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Id:</div>
                                                        <div class="col-9"><span id="Id"></span></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Nombre:</div>
                                                        <div class="col-9"><input type="text" name="Name" id="Name" placeholder="Nombre completo"></div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Correo:</div>
                                                        <div class="col-9"><input type="text" name="Mail" id="Mail" placeholder="correo@dominio.com"></div>
                                                    </div>                                                                                                
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Usuario:</div>
                                                        <div class="col-3"><input type="text" name="Uname" id="Uname" placeholder="Nombre de usuario"></div>
                                                    </div>
                                                    <?php if ($busca === "NUEVO") { ?>
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Contraseña:</div>
                                                            <div class="col-3"><input type="text" name="Passwd" id="Passwd"></div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Status:</div>
                                                        <div class="col-3">
                                                            <select name="Status" id="Status">
                                                                <option value="active">Activo</option>
                                                                <option value="inactive">Inactivo</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4 align-center"><input type="submit" name="Boton" id="Boton"></div>
                                                        <div class="col-md-4"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php if (is_numeric($busca)) { ?>
                                            <tr>
                                                <td class="titulos" colspan="100%">Cambiar contraseña</td>
                                            </tr>
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="container">
                                                        <div class="row no-gutters">
                                                            <div class="col-3 align-right">Clave:</div>
                                                            <div class="col-3"><input type="password" name="Passwd" id="Passwd" placeholder="Ingresar nueva contraseña" autocomplete="new-password"></div>
                                                        </div>
                                                        <div class="row no-gutters">
                                                            <div class="col-md-4"></div>
                                                            <div class="col-md-4 align-center"><input type="submit" name="Boton" Id="BotonD" value="Cambiar"></div>
                                                            <div class="col-md-4"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                            <tr>
                                                <td colspan="100%"><div id="mensaje"></div></td>
                                            </tr>
                                    </tbody>
                                </table>

                                <?php regresar($Return) ?>
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="titulos" colspan="100%">Datos auxiliares</td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%">
                                                <div class="container">
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Vigencia:</div>
                                                        <div class="col-9">
                                                            <span id="FeclaveSpan"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Última Actividad:</div>
                                                        <div class="col-9">
                                                            <span id="LastActivitySpan"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right">Última Sesión:</div>
                                                        <div class="col-9">
                                                            <span id="LastLoginSpan"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row no-gutters">
                                                        <div class="col-3 align-right"># Accesos:</div>
                                                        <div class="col-9">
                                                            <span id="LoginCountSpan"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                </table>
            </div>
            <input type="hidden" name="busca" id="busca">
            <input type="hidden" name="Level" id="Level" value="9">
        </form>

        <?php BordeSuperiorCerrar(); ?>

        <script>
            $(document).ready(function () {
                let busca = "<?= $busca ?>";
                $("#busca").val("<?= $busca ?>");

                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                }

                $("#Id").html("<?= $busca ?>");
                $("#Name").val("<?= $objectVO->getNombre() ?>").prop("required", "required");
                $("#Mail").val("<?= $objectVO->getMail() ?>");
                $("#Uname").val("<?= $objectVO->getUsername() ?>").prop("required", "required");
                $("#Team").val("<?= $objectVO->getTeam() ?>").prop("required", "required");

                $("#FeclaveSpan").html("<?= $objectVO->getCreation() ?>");
                $("#LastActivitySpan").html("<?= $objectVO->getLastactivity() ?>");
                $("#LastLoginSpan").html("<?= $objectVO->getLastlogin() ?>");
                $("#LoginCountSpan").html("<?= $objectVO->getCount() ?>");

                $("#Name").focus();

                $("#Generar").click(function (event) {
                    event.preventDefault();
                    $("#Passwd").val(generatePassword());
                    $("#Passwd").focus();
                });

                $("#form1").submit(function (event) {
                    if (busca === "NUEVO") {
                        bntFormSubmit = 0;
                        if (!validaPassword($("#Passwd"))) {
                            event.preventDefault();
                            $("#Passwd").focus();
                        }
                    }
                });

                $("#BotonD").click(function (event) {
                    bntFormSubmit = 0;
                    if (!validaPassword($("#Passwd"))) {
                        event.preventDefault();
                        $("#Passwd").focus();
                        bntFormSubmit = 0;
                    }
                });
            });
        </script>
    </body>
</html>


