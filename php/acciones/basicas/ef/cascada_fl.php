<?
	//sleep(2);
	
	//Recuperacion de parametros
	$parametros = $_POST['parametros'];

	//Genero los datos que quiero devolver
	global $ADODB_FETCH_MODE, $db, $cronometro;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT patron, descripcion_corta FROM apex_patron;";
	$rs = $db["instancia"][apex_db_con]->Execute($sql);
	if((!$rs)||($rs->EOF)){
		$datos[0] = "Error";
	}else{
		$temp = $rs->getArray();
		foreach($temp as $dato){
			$datos[$dato['patron']] = $dato['descripcion_corta'];
		}
	}
	
	//Devuelvo los valores
	responder($datos);

?>