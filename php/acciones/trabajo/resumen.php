<?
	require_once("nucleo/lib/punto_acceso.php");
	
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();

		$param = print_r( toba::get_info_instancia(apex_pa_instancia), true);

		//------ Cambio de instancia --------// 	
		$punto_acceso = new punto_acceso();
		if (isset($_POST['nombre_instancia']))
		{
			$nueva = $_POST['nombre_instancia'];
			if ($punto_acceso->cambiar_instancia_actual($nueva))
			{
				$centro = $this->hilo->obtener_item_inicial();
				echo "<script language'javascript'>\n";
				echo "document.location.href='";
			    echo $this->vinculador->generar_solicitud($centro[0], $centro[1], $centro[2], true);
				echo "'\n</script>\n";
			}
		}

?>
		<form name="cambiar_instancia" method="post" action="">
		Instancia: 
		<select name="nombre_instancia" id="nombre_instancia" onChange="document.cambiar_instancia.submit();">
		<?
		foreach ($punto_acceso->get_instancias_posibles() as $nombre => $detalles) {
			$seleccionado = ($nombre == apex_pa_instancia) ? 'selected' : '';
		?>
		    <option value="<?=$nombre?>" <?=$seleccionado?>><?=$nombre?></option>	
		<? } ?>
		</select>
		</form>
<?php
		//---------------------------------//
			
	//Mostrar la revision utilizada
	echo "<pre>
$param";
		$proyecto  = $this->hilo->obtener_proyecto();
		if( $proyecto != "toba" ){
			echo "		revision SVN toba: " . revision_svn(  $this->hilo->obtener_path() ) . "
		revision SVN $proyecto: " . revision_svn($this->hilo->obtener_proyecto_path() );
		}

		
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