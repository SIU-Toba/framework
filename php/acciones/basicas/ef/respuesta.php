<?
require_once("nucleo/browser/interface/ef.php");
/*
	PENSADO para EFs que consultan datos a travez de SQL
*/
	if(isset($_POST['parametros']))
	{
		//---[ 0 ]- Recuperacion de parametros enviados por las DEPENDENCIAS
	
		$parametros = $_POST['parametros'];
		$temp = explode("|",$parametros);
		//Parametros de conexion: PROYECTO, OBJETO, EF
		$referencia_ef = explode(";", array_shift($temp) );
		//Desarmo los valores
		$dependencias = array();
		foreach($temp as $dep){
			$valores = explode(";", $dep);
			$dependencias[$valores[0]] = $valores[1];
		}
		//ei_arbol($dependencias);

 		try{
			//---[ 1 ]- Busco la definicion del EF
		
			$datos = array();
			$sql = "SELECT 	o.fuente_datos_proyecto as fp, 
							o.fuente_datos as f,
							u.inicializacion as i 
					FROM 	apex_objeto o,
							apex_objeto_ut_formulario_ef u
					WHERE	u.objeto_ut_formulario = o.objeto
					AND		u.objeto_ut_formulario_proyecto = o.proyecto
					AND o.objeto = '{$referencia_ef[1]}' 
					AND o.proyecto = '{$referencia_ef[0]}'
					AND u.identificador = '{$referencia_ef[2]}';"; //echo $sql;
			$data = consultar_fuente($sql); //print_r($data);
			$fp = $data[0]['fp'];
			$f = $data[0]['f'];
			$i = parsear_propiedades($data[0]['i']);
			$sql_ef = $i['sql']; //echo $sql_ef;
			if(isset($i['no_seteado'])){
				$datos[apex_ef_no_seteado]=$i['no_seteado'];	
			}
			
			//---[ 2 ]- Uso los valores de las dependencias para armar el SQL
	
			$mascara = "%";
			foreach( $dependencias as $dep => $valor ){
				if ($valor != apex_ef_no_seteado)
					$sql_ef = ereg_replace( $mascara.$dep.$mascara, $valor, $sql_ef );
				else
					$sql_ef = ereg_replace( $mascara.$dep.$mascara, 'NULL', $sql_ef );
			}
			//echo $sql_ef;
			//---[ 3 ]- Busco los datos del EF, los organizo y los devuelvo
			
			abrir_fuente_datos($f, $fp);
			$data = consultar_fuente( $sql_ef, $f, ADODB_FETCH_NUM ); //print_r($data);
			for($a=0;$a<count($data);$a++){
				$datos[$data[$a][0]] = $data[$a][1];
			}
			//$datos[] = "$parametros";
			responder($datos);

		}catch(excepcion_toba $e){
			responder( array( 	'x' => 'Excepcion!!!',
								'y' => addslashes($e->getMessage()) ) );			
		}
		
	}else{
		responder( array( '0' => 'No hay PARAMETROS') );
	}
?>