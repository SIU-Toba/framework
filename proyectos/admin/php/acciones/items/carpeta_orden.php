<?php
require_once("nucleo/lib/interface/form.php");

	//------------------------------------------------------------
	interface ordenamiento_items
	{
		function ordenar($proyecto, $rama);
		function descripcion();
	}
	//------------------------------------------------------------
	class ordenamiento_alfabetico implements ordenamiento_items
	{
		function descripcion()
		{
			return "Ordenamiento Alfabético (items primero, carpetas después).";
		}
		
		public function ordenar($proyecto, $rama)
		{
			global $db, $ADODB_FETCH_MODE;
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
			$ordenes = array();
		
			$sql = "
					SELECT	nombre, item_id, orden
					FROM apex_item
					WHERE
						proyecto = '$proyecto' AND
						padre = '$rama'
					ORDER BY carpeta, nombre
				";
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if ($rs)
			{
				$numero = 0;
				$incremento = 5;
				while (!$rs->EOF)
				{
					$id = $rs->fields['item_id'];
					$sql_update = " UPDATE apex_item SET orden = '$numero' WHERE item_id='$id' AND proyecto='$proyecto' ";
					$rs_update =& $db["instancia"][apex_db_con]->Execute($sql_update);
					if(!$rs_update)
					{
						echo ei_mensaje("Ordenamiento de ITEMS - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql_update", "error");
						return false;
					}
					
					$ordenes[] = array('nombre' => $rs->fields['nombre'], 'nuevo_orden' => $numero);
					$numero = $numero + $incremento;
					$rs->MoveNext();
				}
			}
			return $ordenes;			
		}
	}
	//------------------------------------------------------------	
	class ordenamiento_alfabetico_recursivo implements ordenamiento_items
	{
		function descripcion()
		{
			return "Ordenamiento Recursivo Alfabético (items primero, carpetas después).";
		}
		
		public function ordenar($proyecto, $rama)
		{
			echo ei_mensaje("En construcción");
		}
	}
	//------------------------------------------------------------	

	$clases = array('ordenamiento_alfabetico', 'ordenamiento_alfabetico_recursivo');

	if($editable = $this->zona->get_editable())
	{
		$this->zona->obtener_html_barra_superior();

		$proyecto = $editable[0];
		$item = $editable[1];
	} 
	else
	{
		$parametros = toba::get_hilo()->obtener_parametros();
		//Seteo de parametros a la fuerza
		if( (isset($parametros['padre_i'])) && 	(isset($parametros['padre_p'])) ){
			$proyecto = $parametros['padre_p'];
			$item = $parametros['padre_i'];
		}

		if($this->zona->cargar_editable(array('proyecto' => $proyecto, 'item' => $item))){
			$this->zona->obtener_html_barra_superior();
		}
		
	}


	echo "<div align='center'>";
	echo form::abrir('ordenar_items', '');
	enter();
	foreach ($clases as $clase)
	{
		$metodo = new $clase;
		echo form::submit($clase, $metodo->descripcion());
		enter();enter();
	}
	echo form::cerrar();		
	echo "</div>";
	
	foreach ($clases as $clase)
	{
		if (isset($_POST[$clase]))
		{
			$metodo = new $clase;
			$ordenes = $metodo->ordenar($proyecto, $item);
			ei_arbol($ordenes);
		}
	}

	
?>