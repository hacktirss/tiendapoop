<?php
#Librerias
include_once ("lib/lib.php");
include_once ("check.php");

use com\softcoatl\utils as utils;

$request = utils\HTTPUtils::getRequest();
$self = utils\HTTPUtils::self();

$Titulo = "Configuracion de permisos para usuario";

$busca = $request->hasAttribute("busca") ? $request->getAttribute("busca") : $request->getAttribute("id");
$editable = $request->hasAttribute("editable") ? $request->getAttribute("editable") : 1;

require_once "./service/UsuariosService.php";

$selectPermisos = "
                SELECT auth.id_menu,menus.tipo,auth.permisos,auth.editable  
                FROM authuser_conf auth,menus 
                WHERE auth.id_menu = menus.id AND auth.id_user = " . $busca . "
                ORDER BY auth.id_menu;";
$rows_ = utils\ConnectionUtils::getRowsFromQuery($selectPermisos);
$permisos = array();
foreach ($rows_ as $value) {
    $permisos[$value[id_menu]]["menu"] = $value["id_menu"];
    $permisos[$value[id_menu]]["permisos"] = $value["permisos"];
    $permisos[$value[id_menu]]["editable"] = $value["editable"];
}

$queryMenus = "SELECT * FROM menus ORDER BY id";
$rgMenus = utils\ConnectionUtils::getRowsFromQuery($queryMenus);
?>
<!DOCTYPE html>
<html lang="es" xml:lang="es">
    <head>
        <?php require_once "./config_main.php"; ?>
        <title><?= $Gcia ?></title>        
    </head>

    <body>

        <?php BordeSuperior(); ?>

        <div id="FormularioChecks">
            <form id="form1" name="form1" method="post" action="">
                <table>
                    <tbody>

                        <?php
                        $auxLine = 0;
                        foreach ($rgMenus as $menu) {
                            if ($auxLine == 0) {
                                echo "<tr valign='top'>";
                            }

                            echo "<td width='20%'>";
                            echo "<b>" . ucwords(strtolower($menu[nombre])) . "</b>";
                            echo "<hr>";

                            $i = 0;
                            //error_log(print_r($rows_[$menu[id]],true));
                            $Permisos = $permisos[$menu[id]][permisos];
                            $Editable = $permisos[$menu[id]][editable];

                            $query = "  SELECT menus.nombre,submenus.submenu,submenus.id,submenus.permisos
                                        FROM submenus 
                                        LEFT JOIN menus on menus.id=submenus.menu 
                                        WHERE menus.id = " . $menu[id] . "
                                        ORDER BY submenus.posicion";
                            $rgA = utils\ConnectionUtils::getRowsFromQuery($query);
                            foreach ($rgA as $rg) {
                                $submenu = str_replace(array(" ", ".", "-"), "", $rg[submenu]) . $rg[id];
                                $tachar = $rg[permisos] == 0 ? "class='tachar'" : "";
                                $disable = $rg[permisos] == 0 ? " disabled='disabled' " : "";
                                echo "<span $tachar>";
                                if ($Permisos[$i] == 1) {
                                    echo "<input class='micheck' type='checkbox' name='$submenu' value='1' checked $disable>";
                                } else {
                                    echo "<input class='micheck' type='checkbox' name='$submenu' $disable>";
                                }
                                $submenu .= "_0";
                                if ($Editable[$i] == 1) {
                                    echo "<input class='micheck' type='checkbox' name='$submenu' value='1' checked $disable>";
                                } else {
                                    echo "<input class='micheck' type='checkbox' name='$submenu' $disable>";
                                }
                                echo " $rg[submenu]</span></br>";
                                $i++;
                            }
                            echo "</td>";


                            $auxLine++;
                            if ($auxLine > 4) {
                                echo "</tr>";
                                $auxLine = 0;
                            }
                        }

                        if ($auxLine > 0) {
                            echo "</tr>";
                        }
                        ?>
                        <tr><td colspan="100%"><hr></td></tr>

                        <tr>
                            <td style="text-align: right">
                                Limpiar todo <input type="checkbox" id="Limpiar"
                            </td>
                            <td></td>
                            <td style="text-align: center">
                                <input type="submit" name="BotonD" id="Boton">
                            </td>
                            <td></td>
                            <td>
                                <input type="checkbox" id="Todo"> Seleccionar todo 
                            </td>
                        </tr>
                    </tbody>
<!--                    <tfoot>
                        <tr>
                            <td class="regresar"><a href="usuarios.php"><i class="fa fa-lg fa-arrow-circle-left"></i> Regresar</a></td>
                        </tr>
                    </tfoot>-->
                </table>
                <input type="hidden" name="busca" id="busca">
            </form>
            <?php regresar($Return) ?>
        </div>

        <?php BordeSuperiorCerrar(); ?>

        <script>
            $(document).ready(function () {
                var busca = "<?= $busca ?>";
                if (busca === "NUEVO") {
                    $("#Boton").val("Agregar");
                } else {
                    $("#Boton").val("Actualizar");
                    $("#busca").val(busca);
                }

                $("#Todo").click(function () {
                    var checkboxes = $(this).closest('form').find(':checkbox');
                    checkboxes.prop('checked', true);
                    $("#Limpiar").prop('checked', false);
                });

                $("#Limpiar").click(function () {
                    var checkboxes = $(this).closest('form').find(':checkbox');
                    checkboxes.prop('checked', false);
                });
            });
        </script>
    </body>
</html>
