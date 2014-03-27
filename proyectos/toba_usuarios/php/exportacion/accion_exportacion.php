<?php

echo "<pre style='background-color:white; text-align:left; border: 1px solid black; padding:5px;width: 600px;'>";
require_once('consola/consola.php');
$clase_menu = 'menu_toba';
$directorio_comandos = toba_dir().'/php/consola/comandos';
$consola = new consola( $directorio_comandos, $clase_menu );


$catalogo = toba_modelo_catalogo::instanciacion();
$id_instancia = toba::instancia()->get_id();
$id_proyecto = toba::proyecto()->get_id();
$instancia = $catalogo->get_proyecto($id_instancia, $id_proyecto, $consola);
$instancia->exportar_implementacion();
echo '</pre>';

?>