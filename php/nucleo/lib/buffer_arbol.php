<?php

class buffer
/*
	- Pensar la persistencia en la sesion

	-- TEMAS --

 - Relaciones padre - hijo
 - Obtencion y updateo de informacion de cosmetica
 - Metodos para consultar y modificar lod buffers
 - calcular SQL de sincronizacion con la base (TIMESTAMP o comparacion?)
 - Metodo para controlar la perdida de sincronizacion ()
 - Componedor GENERICO de SQL?
*/
{
	var $definicion;				//Definicion que indica la construccion del BUFFER
	var $fuente;					//Fuente de datos utilizada
	var $identificador;				//Identificador del registro
	var $control;					//Estructura de control
	var $datos = array();			//Datos cargados en el BUFFER
	//var $datos_db = array();		//Datos tal cual salieron de la DB (Control de SINCRO)

	function buffer($definicion, $fuente)
	{
		$this->definicion = $definicion;
		$this->fuente = $fuente;
		//Si existi antes en la sesion me recupero de ahi
	}

	//-------------------------------------------------------------------------------
	//-------------------------  Cargar DATOS de la BASE  ---------------------------
	//-------------------------------------------------------------------------------

	function cargar_datos_db($identificador)
	//Cargo los BUFFERS con datos de la DB
	{
		$this->identificador = $this->formatear_clave($identificador);
		$this->cargar_db( $this->definicion['plan'] );
		$this->generar_estructuras_control();
	}
	//-------------------------------------------------------------------------------

	function cargar_db($plan)
	//Cargo los BUFFERS con datos de la DB
	//Los datos son 
	{
		global $db, $ADODB_FETCH_MODE, $cronometro;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		for($a=0;$a<count($plan);$a++)
		{
			$seccion = $plan[$a]["nombre"];
			$sql = $this->generar_sql_select($seccion);
			//echo $sql . "<br>";
			//-- Intento cargar el BUFFER
			$rs = $db[$this->fuente][apex_db_con]->Execute($sql);
			if((!$rs)){
				monitor::evento("bug","[BUFFER:$seccion] Error cargando DATOS' ".$db["instancia"][apex_db_con]->ErrorMsg());
			}
			if($rs->EOF){
				if(isset($this->definicion['seccion'][$seccion]["carga_obligatoria"])){
					if($this->definicion['seccion'][$seccion]["carga_obligatoria"]=="1"){
						//ERROR! Fallo una carga obligatoria
					}
				}else{
					//El parametro no es estricto, lo inicializo como ARRAY vacio
					$this->datos[$seccion] = array();
				}
			}else{
				$temp = $rs->getArray();
				//Registro UNICO o GRUPO de REGISTROS
				if($this->definicion['seccion'][$seccion]["registros"]=="1"){	
					$this->datos[$seccion] = $temp[0];
				}else{
					$this->datos[$seccion] = $temp;
				}
				//Se solicita control de SINCRONIA a la DB?
				if(isset($this->definicion['seccion'][$seccion]["control_sincro"])){
					if($this->definicion['seccion'][$seccion]["control_sincro"]=="1"){	
						$this->datos_db[$seccion] = $this->datos[$seccion];
					}
				}
				//Cargo los datos de los hijos RECURSIVAMENTE?
				if(isset($plan[$a]["hijo"])){
					if(is_array($plan[$a]["hijo"])){
						$this->cargar_db( $plan[$a]["hijo"] );
					}
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	function generar_estructuras_control()
	//Genera la estructuras de control del estado interno del buffer
	{
		//Por SECCION
			//- Proximo registro
			//- Claves de los registros
			//- Estado de sincronizacion (db, I, E, M)
		//ei_arbol($this->datos,"DATOS");
	}

	//-------------------------------------------------------------------------------
	//------------  Acceder/Modificar DATOS   ---------------------------------------
	//-------------------------------------------------------------------------------
	//Primitivas de acceso y modificacion

	function obtener_seccion($seccion)
	{
		return $this->datos[$seccion];
	}
	
	function obtener_seccion_registro($seccion, $registro)
	{
		return $this->datos[$seccion][$registro];
	}

	//-------------------------------------------------------------------------------
	//La funcionalidad tiene que estar orientada a un registro activo?
	function set_registro_activo($seccion, $clave){}
	function get_registro_activo($seccion){}
	function unset_registro_activo($seccion, $clave){}
	function eliminar_registro_activo($seccion, $clave){}
	function modificar_registro_activo($seccion, $clave){}
	//-------------------------------------------------------------------------------

	function agregar_elemento($seccion, $datos)
	{
		
	}
	//-------------------------------------------------------------------------------

	function modificar_elemento($seccion, $clave, $datos)
	{
		
	}
	//-------------------------------------------------------------------------------

	function eliminar_elemento($seccion, $clave)
	{
		
	}

	//-------------------------------------------------------------------------------
	//------------  GENERADORES de SQL  ---------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_sql_select($seccion)
	{
		foreach($this->identificador as $columna => $valor){
			$sql_where[] =	"( $columna = '$valor')";
		}
		$sql =	" SELECT	" . implode(",	",$this->definicion['seccion'][$seccion]["clave"]) . "," . 
						implode(",	",$this->definicion['seccion'][$seccion]["columna"])	. 
				" FROM "	. $this->definicion['seccion'][$seccion]["tabla"] .
				" WHERE " .	implode(" AND ",$sql_where) .";";
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function generar_sql_insert($seccion, $registro)
	{
		
	}
	//-------------------------------------------------------------------------------

	function generar_sql_update($seccion, $registro)
	{
		
	}
	//-------------------------------------------------------------------------------

	function generar_sql_delete($seccion, $registro)
	{
		
	}

	//-------------------------------------------------------------------------------
	//---------------  Control de SINCRO con la DB   --------------------------------
	//-------------------------------------------------------------------------------
	//Herramienta para manejo transaccional OPTIMISTA

	function controlar_sincro_db()
	{
		$status = $this->control_sincro_seccion($this->buffer_info_plan);
		return $status;
	}
	//-------------------------------------------------------------------------------

	function control_sincro_seccion($plan)
	//Esto existe en el caso de hacer el control por comparacion
	//Ejecuta los SQL iniciales y determina si cambio algun dato en la DB
	{
		return false;
	}

	//-------------------------------------------------------------------------------
	//---------------  VARIOS   -----------------------------------------------------
	//-------------------------------------------------------------------------------

	function formatear_clave($clave_pos)
	{
		if(count($clave_pos)!=count($this->definicion['clave_maestra'])){
			//ERROR!!!
		}
		$indice = 0;
		//Se supone que el orden es el esperado
		foreach($clave_pos as $clave){
			$clave_asoc[$this->definicion['clave_maestra'][$indice]]=$clave;
			$indice++;
		}
		return $clave_asoc;		
	}
	//-------------------------------------------------------------------------------

	function formatear_clave_seccion($seccion, $clave_pos)
	{
		if(count($clave_pos)!=count($this->definicion['seccion'][$seccion]["clave"])){
			//ERROR!!!
		}
		$indice = 0;
		//Se supone que el orden es el esperado
		foreach($clave_pos as $clave){
			$clave_asoc[$this->definicion['seccion'][$seccion]["clave"][$indice]]=$clave;
			$indice++;
		}
		return $clave_asoc;		
	}
	//-------------------------------------------------------------------------------
}
?>
