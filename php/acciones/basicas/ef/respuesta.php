<?
require_once("nucleo/browser/interface/ef.php");
//Necesario para no eliminar los datos de los multietapa
toba::get_hilo()->desactivar_reciclado();

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
			if (count($valores) == 2) {
				$dependencias[$valores[0]] = $valores[1];
			}
		}
 		try{
			//---[ ]- Busco la definicion del EF
			$datos = array();
			$sql_es_nuevo = "
				SELECT clase FROM apex_objeto 
				WHERE proyecto = '{$referencia_ef[0]}' AND objeto ='{$referencia_ef[1]}'
			";
			$res = consultar_fuente($sql_es_nuevo, "instancia");
			$es_nuevo = in_array($res[0]['clase'], array('objeto_ei_formulario', 'objeto_ei_filtro', 'objeto_ei_formulario_ml'));
			//Desde 0.8.3 Dependiendo del tipo de objeto hay que buscar en los distintos datos
			if ($es_nuevo) {
				$sql = "SELECT 	o.fuente_datos_proyecto as fp, 
								o.fuente_datos as f,
								u.inicializacion as i,
								u.elemento_formulario as ef,
								u.columnas as col
						FROM 	apex_objeto o,
								apex_objeto_ei_formulario_ef u
						WHERE	u.objeto_ei_formulario = o.objeto
						AND		u.objeto_ei_formulario_proyecto = o.proyecto
						AND o.objeto = '{$referencia_ef[1]}' 
						AND o.proyecto = '{$referencia_ef[0]}'
						AND u.identificador = '{$referencia_ef[2]}';";
				$data = consultar_fuente($sql,"instancia"); //print_r($data);
			} else {
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
						AND u.identificador = '{$referencia_ef[2]}';";
				$data = consultar_fuente($sql,"instancia"); //print_r($data);
			}
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
			//$ef->cargar_datos_master_ok();
			$datos = $ef->obtener_valores();
			responder($datos);					

		}catch(excepcion_toba $e){
			echo $e->mensaje_web();
			responder( array( 	'x' => 'Excepcion!!!',
								'y' => addslashes($e->getMessage()) ) );			
		}
	}else{
		responder( array( '0' => 'No hay PARAMETROS') );
	}
?>
