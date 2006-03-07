<?
	require_once("nucleo/lib/punto_acceso.php");
	
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		
		ei_arbol( dba::get_info_db_instancia(), "Parametros de conexion de la instancia");
		
		//---------------------------------//
			
	//Mostrar la revision utilizada
	echo "<pre>";
		$proyecto  = $this->hilo->obtener_proyecto();
		if( $proyecto != "toba" ){
			echo "		revision SVN toba: " . revision_svn(  $this->hilo->obtener_path() ) . "
		revision SVN $proyecto: " . revision_svn($this->hilo->obtener_proyecto_path() );
		}
   echo "</pre>";

//$path_img = recurso::path_apl()."/doc/wiki/trac/toba/chrome/common/trac_logo_mini.png";
$img = recurso::imagen_apl("admin/doc_wiki.gif", true, null, null, "Ver documentación WIKI offline");
$dest = recurso::path_apl()."/doc/wiki/trac/toba/wiki.html";
echo "<a href='$dest' target='_blank'>$img</a> ";

$img = recurso::imagen_apl("admin/doc_api.gif", true, null, null, "Ver documentación de la API offline");
$dest = recurso::path_apl()."/doc/api/index.html";
echo "<a href='$dest' target='_blank'>$img</a>";		
/*
	echo "<pre>";
	echo "

* Ver un resumen del ESTADO

- Nro. de Notas entrantes sin leer
- Nro. Notas Salientes leidas
- Cantidad de mensajes en cartelera

- Usuarios logueados al sistema

- Cantidad de OBJETOS en USO
- Cantidad de ITEMS en USO

- Cantidad de tareas pendientes

	
";	
	echo "<pre>";
*/

		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>