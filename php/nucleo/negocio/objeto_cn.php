<?php
require_once("nucleo/browser/clases/objeto.php");	//Ancestro de todos los OE

class objeto_cn extends objeto
{
	protected $indice_reglas;				//Indice de reglas de negocio
	
	function __construct($id)
	{
		parent::objeto($id);
		$this->recuperar_estado_sesion();
		//Armo un indice de las reglas
		/*	
			for($a = 0; $a<count($this->info_negocio_regla); $a++){
				$this->indice_reglas[$this->info_negocio_regla[$a]["nombre"]] = $a;
			}
		*/
	}

	function destruir()
	{
		parent::destruir();
		$this->guardar_estado_sesion();		//GUARDO Memoria dessincronizada
	}

	function establecer_tiempo_maximo($tiempo="30")
	{
		ini_set("max_execution_time",$tiempo);
	}

/*
	function __call($metodo, $argumentos)
	{
		ei_arbol($argumentos, "Llamada al metodo no implementado: " . $metodo);
	}
*/

	//-------------------------------------------------------------------------------
	//------------------------------   Procesamiento   ------------------------------
	//-------------------------------------------------------------------------------

	function procesar()
	//Esto hay que redeclararlo en los HIJOS
	{
		$this->log->debug( $this->get_txt() . "[ procesar ] No definido!");
	}

	//-------------------------------------------------------------------------------
	//------  MANEJO de DERECHOS
	//-------------------------------------------------------------------------------

	/*
		Falta la carga de derechos
	*/
	
	function obtener_derechos()
	{
		return $this->derechos;
	}
	
	function posee_derecho($derecho)
	{
		//return true
		return in_array($derecho, $this->derechos);
	}

	//-------------------------------------------------------------------------------
	//------  MANEJO de CONTROLES configurables
	//-------------------------------------------------------------------------------



/*
	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//-- CABECERA ----------------------
		$sql["info_negocio"]["sql"] = "SELECT 	descripcion
										FROM	apex_objeto_negocio
										WHERE	objeto_negocio_proyecto='".$this->id[0]."'
									 	AND		objeto_negocio='".$this->id[1]."'";
		$sql["info_negocio"]["tipo"]="1";
		$sql["info_negocio"]["estricto"]="0";
		//-- Reglas de NEGOCIO --------------
		$sql["info_negocio_regla"]["sql"] = "SELECT	objeto_negocio_proyecto ,
												objeto_negocio          ,
												nombre			       	,
												descripcion             ,
												activada				,
												mensaje_a				,
												mensaje_b				
										FROM	apex_objeto_negocio_regla
										WHERE	objeto_negocio_proyecto ='".$this->id[0]."'
										AND		objeto_negocio ='".$this->id[1]."'
										AND		(activada = 1)
										ORDER	BY	nombre;";
		$sql["info_negocio_regla"]["tipo"]="x";
		$sql["info_negocio_regla"]["estricto"]="0";
		return $sql;
	}

	//-------------------------------------------------------------------------------
	//----------------------------   MANEJO de REGLAS   -----------------------------
	//-------------------------------------------------------------------------------

	function controlar_regla($regla, $parametros)
	//Ejecuta una regla
	{
		//Existe??
		if(isset($this->indice_reglas[$regla])){
			$pos = $this->indice_reglas[$regla];
			//Activada??
			if($this->info_negocio_reglas[$pos]['activada']==1){
				//Existe el metodo que implementa la regla?
				$metodo = "regla_" . $regla;
				if(method_exists($this, $metodo)){
					return $this->$metodo($parametros);
				}else{
					return array(997,"El metodo que implementa la regla no existe");
				}
			}else{
				return array(998,"La regla se encuentra desactivada");
			}
		}else{
			return array(999,"La regla no existe");
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_reglas()
	//Muesta las reglas existentes
	{
		$reglas = array();
		for($a = 0; $a<count($this->info_negocio_regla); $a++){
			if($this->info_negocio_regla[$a]['activada']==1){
				$reglas[] = $this->info_negocio_regla[$a]['nombre'];
			}
		}
		return $reglas;
	}
*/
	//-------------------------------------------------------------------------------
	//------------------------------------------   TESTEO   -------------------------
	//-------------------------------------------------------------------------------
	/*
		Estas funciones proveen un marco para registrar el testeo de los CN.
		Permiten generar ACCIONES de toba que instancien al objeto y disparen
		Sobre el mismo

	*/

	var $testeo_registrar;
	var $testeo_llamada = 0;
	var $testeo_sesion;
	var $testeo_archivo;
	
	function registrar_entrada($metodo, $parametros)
	//Registra la llamada a una funcion
	{
		if($this->testeo_registrar){
			$this->testeo_sesion[$testeo_llamada]['metodo'] = $metodo;
			$this->testeo_sesion[$testeo_llamada]['entrada'] = $parametros;
		}
	}
	//-------------------------------------------------------------------------------

	function registrar_salida()
	{
		if($this->testeo_registrar){
			$this->testeo_sesion[$testeo_llamada]['salida'] = $parametros;
			$this->testeo_llamada++;
		}
	}
	//-------------------------------------------------------------------------------

	function guardar_sesion()
	//Guardo la sesion de testeo en un archivo
	{
		$this->sesion_inicializacion();		
		for($a=0;$a<count();$a++)
		{
			$this->sesion_paso_testeo($a);	
		}
	}
	//-------------------------------------------------------------------------------

	function sesion_inicializacion()
	//Instancia el CN, deja registro de la fecha, etc.
	{
		
	}
	//-------------------------------------------------------------------------------

	function sesion_paso_testeo($paso)
	//Genera el PHP de un paso del testeo
	{
		
	}
	//-------------------------------------------------------------------------------

/*

		if($generar_php){
			//Genera el PHP que se usa para probar esta funcion
			echo "<pre>";
			//Dumpeo los BUFFERS
			foreach(array_keys($buffers) as $buffer){
				$registros = $buffers[$buffer]->get_registros();
				echo "//------   $buffer   -----\n\n";
				echo dump_array_php($registros, "\$".$buffer);
			}
			//Dumpeo el detalle de la cabecera
			echo "//------   DETALLE cabecera   -----\n\n";
			echo dump_array_php($cabecera_detalles, "\$cabecera_detalles");
			echo "<pre>";
		}

*/

}
?>