<?
require_once("nucleo/browser/interface/ef.php");

//Necesario para no eliminar los datos de los multietapa
$this->hilo->desactivar_reciclado();

/*
	PENSADO para EFs que consultan datos a travez de SQL
*/
	if(isset($_POST['parametros']))
	{
		//---[ 0 ]- Recuperacion de parametros enviados por las DEPENDENCIAS
	
		$parametros = $_POST['parametros'];
		$temp = explode("|",$parametros);
		//Identificacion del EF: PROYECTO, OBJETO, EF
		$referencia_ef = explode(";", array_shift($temp) );
		//Busco los parametros
		$dependencias = array();
		foreach($temp as $dep){
			$valores = explode(";", $dep);
			$dependencias[$valores[0]] = trim($valores[1]);
		}
		//ei_arbol($dependencias);

 		try{
			//---[ 1 ]- Busco la definicion del EF
		
			$datos = array();
			$sql = "SELECT 	o.fuente_datos_proyecto as fp, 
							o.fuente_datos as f,
							u.inicializacion as i,
							u.elemento_formulario as ef
					FROM 	apex_objeto o,
							apex_objeto_ut_formulario_ef u
					WHERE	u.objeto_ut_formulario = o.objeto
					AND		u.objeto_ut_formulario_proyecto = o.proyecto
					AND o.objeto = '{$referencia_ef[1]}' 
					AND o.proyecto = '{$referencia_ef[0]}'
					AND u.identificador = '{$referencia_ef[2]}';"; //echo $sql;
			$data = consultar_fuente($sql); //print_r($data);
			$i = parsear_propiedades($data[0]['i']);

			//Abro la fuente de datos
			$fp = $data[0]['fp'];
			$f = $data[0]['f'];
			abrir_fuente_datos($f, $fp);

			//---[ 2 ]- BUSCO los VALORES
	
			if($data[0]['ef'] == "ef_combo_dao")		//-------------- DAO
			{
				if(isset($i["dao"])){
					$dao = $i["dao"];
				}
				if(isset($i["include"])){
					$include = $i["include"];
				}
				if(isset($i["clase"])){
					$clase = $i["clase"];
				}
				if(isset($include) && isset($clase) )
				{
					//Preparo los parametros recibidos
					$param = "'" . implode("', '",$dependencias) . "'";
					include_once($include);
					$sentencia = "\$datos = " .  $clase . "::" . $dao ."($param);";
					try{
						eval($sentencia);//echo $sentencia;
						//$datos = ppg::get_p_principales_cr('5833');
						//$datos = ppg::get_incisos();
						//$datos["llamada"] = $sentencia;
						//$datos["includeo"] = $include;
					}catch(excepcion_toba $e){
						$datos['1'] = "Excepcion: " . $e->getMessage();
					}
					
				}else{
					$datos['hola'] = "";
				}
				responder($datos);					

			}else 										//----------------- COMBOS comunes
			{
				$sql_ef = $i['sql']; //echo $sql_ef;
				if(isset($i['no_seteado'])){
					$datos[apex_ef_no_seteado]=$i['no_seteado'];	
				}
				$mascara = "%";
				foreach( $dependencias as $dep => $valor ){
					if ($valor != apex_ef_no_seteado)
						$sql_ef = ereg_replace( $mascara.$dep.$mascara, $valor, $sql_ef );
					else
						$sql_ef = ereg_replace( $mascara.$dep.$mascara, 'NULL', $sql_ef );
				}
				//echo $sql_ef;
				//---[ 3 ]- Busco los datos del EF, los organizo y los devuelvo
				
				$data = consultar_fuente( $sql_ef, $f, ADODB_FETCH_NUM ); //print_r($data);
				for($a=0;$a<count($data);$a++){
					$datos[$data[$a][0]] = $data[$a][1];
				}
				//$datos[] = "$parametros";
				responder($datos);
			}
		}catch(excepcion_toba $e){
			responder( array( 	'x' => 'Excepcion!!!',
								'y' => addslashes($e->getMessage()) ) );			
		}
		
	}else{
		responder( array( '0' => 'No hay PARAMETROS') );
	}
?>