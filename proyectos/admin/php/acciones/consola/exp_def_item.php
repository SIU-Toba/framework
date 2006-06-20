<?

//Hay que tomar en cuenta como se comportan las subclases que no poseen
//Un archivo propio de definicion... (Prohibir las definiciones en ACTIVIDADES
//Seria lo IDEAL!!!)

// Tengo que emular el ambiente WEB
//	include_once("nucleo/consola/emular_web.php");
	
	$proyecto = 'admin';

	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	$sql = "SELECT 	i.*
				FROM 		apex_item i
				WHERE i.proyecto = '$proyecto'
				AND ((i.carpeta <> 1) OR (i.carpeta IS NULL))
				ORDER BY i.item";

	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if($rs) {
	if(!$rs->EOF){
		echo "\n\nExportacion de OBJETOS\n\n";
		//------------------------- Exportacion OBJETO
		while(!$rs->EOF)
		{
			$nombre_archivo = ereg_replace("/","_",$rs->fields["item"]);
			echo "<" . $nombre_archivo . "> ". $rs->fields["nombre"] . "\n";
			//Creo una solicitud

			include_once("nucleo/solicitud_especifica.php");
			$solicitud

			$rs->movenext();	
		}
	}else{
		echo "No hay ITEMS\n";
	}
}else{
	echo "ERROR: $sql\n";
}

?>