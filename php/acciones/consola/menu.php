<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	proyecto, item, nombre
			FROM 	apex_item
			WHERE (padre = '/consola')
			AND ( carpeta <> 1 OR carpeta IS NULL)
			AND (solicitud_tipo = 'consola')
			ORDER BY nombre";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
if($rs) {
	if(!$rs->EOF){
		echo "\n\nITEMs Disponibles\n\n";
		while(!$rs->EOF)
		{
			echo " " . $rs->fields["item"] . "\n";
			echo "      " . $rs->fields["nombre"] . "\n";
			echo "\n";
			$rs->movenext();	
		}
	}else{
		echo "No hay ITEMs\n";
	}
}else{
	echo "ERROR\n";
}
?>