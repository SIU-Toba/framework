<?
require_once("nucleo/browser/interface/ef.php");
//Necesario para no eliminar los datos de los multietapa
$this->hilo->desactivar_reciclado();

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
			if ($valores[1] != apex_ef_valor_oculto)
				$dependencias[$valores[0]] = $valores[1];
			else {
				//Caso particular para los ocultos
				$clave_memoria = "obj_" .$referencia_ef[1]. "_ef_" . $valores[0];
				$valor = toba::get_solicitud()->hilo->recuperar_dato($clave_memoria);
				$dependencias[$valores[0]] = $valor;
			}
		}
 		try{
			//---[ 1 ]- Busco la definicion del EF
			$datos = array();
			$sql = "SELECT 	o.fuente_datos_proyecto as fp, 
							o.fuente_datos as f,
							u.inicializacion as i,
							u.elemento_formulario as ef,
							u.columnas as col
					FROM 	apex_objeto o,
							apex_objeto_ut_formulario_ef u
					WHERE	u.objeto_ut_formulario = o.objeto
					AND		u.objeto_ut_formulario_proyecto = o.proyecto
					AND o.objeto = '{$referencia_ef[1]}' 
					AND o.proyecto = '{$referencia_ef[0]}'
					AND u.identificador = '{$referencia_ef[2]}';"; //echo $sql;
			$data = consultar_fuente($sql); //print_r($data);
			//Abro la fuente de datos
			$fp = $data[0]['fp'];
			$f = $data[0]['f'];
			abrir_fuente_datos($f, $fp);
			$i = parsear_propiedades($data[0]['i']);
			if(isset($i["sql"]) && !isset($i["fuente"])){
				$i["fuente"]= $f;
			}			

			//---[ 2 ] -- Controlo casos aun no soportados
			if(($data[0]['ef'] == "ef_combo_dao") && !(isset($i['dao']))){
				throw new exception("Los DAOS dinamicos no estan soportados");
			}
			//---[ 3 ]- Creo el EF  ------------------
			//- a) Preparo Datos manejados
			if(ereg(",",$data[0]["col"])){
				$dato = explode(",", $data[0]["col"] );
				for($d=0;$d<count($dato);$d++){
					//Elimino espacios en las	claves
					$dato[$d]=trim($dato[$d]);
				}
			}else{
				 $dato = $data[0]["col"];
			}
			//- b) Creo la clase EF
			$sentencia_creacion_ef = "\$ef = new {$data[0]["ef"]}('id','form','identif','etiq','descrip',\$dato,'oblig',\$i);";
			//echo $sentencia_creacion_ef	. "<br>";
			eval($sentencia_creacion_ef);

			//---[ 4 ]- Devuelvo los datos del EF  ------------------
			$ef->cargar_datos_dependencias($dependencias);
			//ATENCION, le estoy diciendo al EF que todas sus dependencias estan seteadas...
			//	Esto es asi??? Hay que ver si en el caso de dependencias parciales se hace igual la llamada al server...
			$ef->cargar_datos_master_ok();
			$datos = $ef->obtener_valores();
			responder($datos);					

		}catch(excepcion_toba $e){
			echo "<pre>". $e->get_log_info();
			responder( array( 	'x' => 'Excepcion!!!',
								'y' => addslashes($e->getMessage()) ) );			
		}
	}else{
		responder( array( '0' => 'No hay PARAMETROS') );
	}
?>