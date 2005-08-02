<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/db/toba_dbt.php");

class ci_principal extends objeto_ci
{
	protected $db_tablas;
	//efss
	private $id_intermedio_efs;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_dbt()->elemento('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		return $propiedades;
	}

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::objeto_db_registros();
			$this->db_tablas->cargar( array('proyecto'=>'toba', 'objeto'=>'1400') );
		}
		return $this->db_tablas;
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function evt__base__carga()
	{
		return $this->get_dbt()->elemento("base")->get();
	}

	function evt__base__modificacion($datos)
	{
		$this->get_dbt()->elemento("base")->set($datos);
	}

	function evt__prop_basicas__carga()
	{
		return $this->get_dbt()->elemento("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_dbt()->elemento("prop_basicas")->set($datos);
		
	}

	//*******************************************************************
	//**  COLUMNAS  *****************************************************
	//*******************************************************************
	
	function evt__post_cargar_datos_dependencias__2()
	{
		//Agrego el evento de cargar de la DB
		$evt = eventos::evento_estandar("leer_db","Cargar COLUMNAS tabla",true,null,null,true,false);
		$this->dependencias["columnas"]->agregar_evento( $evt );
	}
	
	function evt__columnas__carga()
	{
		return $this->get_dbt()->elemento('columnas')->get_registros(null,true);	
	}

	function evt__columnas__modificacion($datos)
	{
		$this->get_dbt()->elemento('columnas')->procesar_registros($datos);
	}

	//-- Generacion automatica de columnas!!
	
	function evt__columnas__leer_db()
	{
		$columnas = $this->obtener_definicion_columnas();
		//ei_arbol($columnas);		
		$dbr = $this->get_dbt()->elemento("columnas");
		for($a=0;$a<count($columnas);$a++){
			try{
				$dbr->agregar_registro($columnas[$a]);
			}catch(excepcion_toba $e){
				toba::get_cola_mensajes()->agregar("Error agregando la COLUMNA '{$columnas[$a]['columna']}'. " . $e->getMessage());
			}
		}
	}

	function obtener_definicion_columnas()
	//Utilizo ADODB para recuperar los metadatos
	{
		//-[ 1 ]- Obtengo datos
		$tabla = $this->get_dbt()->elemento("prop_basicas")->get_registro_valor(0,"tabla");
		$reg = $this->get_dbt()->elemento("base")->get();
		$proyecto = $reg['fuente_datos_proyecto'];
		$id_fuente = $reg['fuente_datos'];
		abrir_fuente_datos($id_fuente, $proyecto);
		$fuente = toba::get_fuente($id_fuente);
		$a=0;
		$columnas = $fuente[apex_db_con]->MetaColumns($tabla,false);
		if(!$columnas){
			toba::get_cola_mensajes()->agregar("La tabla '$tabla' no existe");
			return;
		}
		//echo "<pre>"; print_r($columnas);
		foreach( $columnas as $col ){
			$definicion[$a]['columna'] = $col->name;
			$definicion[$a]['tipo'] = $fuente[apex_db]->get_tipo_datos_generico($col->type);
			if(($definicion[$a]['tipo'])=="C")
				if(isset($col->max_length)) 
					$definicion[$a]['largo'] = $col->max_length;
			if(isset($col->not_null)) $definicion[$a]['no_nulo_db'] = $col->not_null;
			if(isset($col->primary_key)) $definicion[$a]['pk'] = $col->primary_key;
			//Secuencias
			if(isset($col->default_value)){
				if(preg_match("/nextval/",$col->default_value)){
					$seq = true;
					$temp = preg_split("|\"|", $col->default_value);
					$definicion[$a]['secuencia'] = $temp[1];
				}			
			}
			$a++;
		}
		return $definicion;
	}	

	//*******************************************************************
	//** PROCESAR  ******************************************************
	//*******************************************************************/

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase_proyecto", "toba" );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase", "objeto_db_registros" );
		//Sincronizo el DBT
		$this->get_dbt()->sincronizar();	
	}
	//-------------------------------------------------------------------
}
?>