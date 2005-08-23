<?php
require_once('admin/objetos_toba/ci_editores_toba.php'); 
require_once("admin/db/toba_dbt.php");

/*
	Cosas faltantes:

		- Control de que existan dependencias
		- Borrado de dependencias
		- Validacion de relaciones
*/

class ci_principal extends ci_editores_toba
{
	protected $db_tablas;
	protected $seleccion_relacion;
	protected $seleccion_relacion_anterior;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		$propiedades[] = "seleccion_relacion";
		$propiedades[] = "seleccion_relacion_anterior";
		return $propiedades;
	}

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::objeto_db_tablas();
			//$this->db_tablas->cargar( array('proyecto'=>'toba', 'objeto'=>'1400') );
		}
		if($this->cambio_objeto){	
			$this->db_tablas->cargar( $this->id_objeto );
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
	//**  DEPENDENCIAS  *************************************************
	//*******************************************************************

	function evt__dependencias__carga()
	{
		return $this->get_dbt()->elemento('dependencias')->get_registros(null,true);	
	}

	function evt__dependencias__modificacion($datos)
	{
		/*
			ATENCION! si se borran dependencias hay que borrar tambien
			sus relaciones
		*/
		$this->get_dbt()->elemento('dependencias')->procesar_registros($datos);
	}

	//*******************************************************************
	//**  RELACIONEs  *************************************************
	//*******************************************************************

	/*
		Esta funcion y su contrapartida en DAO hacen cosas raras relacionadas
		con la idiosincracia actual del sistema cascadas ( no se soporta que
		los combos disparen parametros multiples )...
		
			La forma ideal de trabajar este tema seria con elementos toba
			que sepan como estan formados
	*/

	function get_lista_tablas()
	{
		$filas = $this->get_dbt()->elemento('dependencias')->get_registros();
		for($a=0;$a<count($filas);$a++){
			$datos[$a]['objeto'] = $filas[$a]['identificador']. "," .$filas[$a]['objeto_proveedor'];
			$datos[$a]['desc'] = $filas[$a]['descripcion'];
		}
		return $datos;
	}
	
	//-------------------------------------------------------------
	//-- FORM
	//-------------------------------------------------------------
	
	function rel_form_a_fila($datos)
	//Convierte el contenido del form a una fila
	{
		$fila['identificador'] = $datos['identificador'];
		$fila['cascada'] = $datos['cascada'];
		$fila['orden'] = $datos['orden'];
		//-- PADRE --
		$padre = explode(",",$datos['padre']);
		$fila['padre_id'] = $padre[0];
		$fila['padre_proyecto'] = toba::get_hilo()->obtener_proyecto();
		$fila['padre_objeto'] = $padre[1];
		$fila['padre_clave'] = implode(",",$datos['padre_columnas']);
		//-- HIJO --
		$hijo = explode(",",$datos['hija']);
		$fila['hijo_id'] = $hijo[0];
		$fila['hijo_proyecto'] = toba::get_hilo()->obtener_proyecto();
		$fila['hijo_objeto'] = $hijo[1];
		$fila['hijo_clave'] = implode(",",$datos['hija_columnas']);
		return $fila;
	}
	
	function rel_fila_a_form($fila)
	{
		$datos['padre'] = $fila['padre_id'] . "," . $fila['padre_objeto'];
		$datos['padre_columnas'] = explode(",", $fila['padre_clave']);
		$datos['hija'] = $fila['hijo_id'] . "," . $fila['hijo_objeto'];
		$datos['hija_columnas'] = explode(",", $fila['hijo_clave']);
		$datos['identificador'] = $fila['identificador'];
		$datos['cascada'] = $fila['cascada'];
		$datos['orden'] = $fila['orden'];
		return $datos;
	}


	function evt__rel_form__alta($datos)
	{
		$fila = $this->rel_form_a_fila($datos);
		//-- VALIDACIONES --
		//Cantidad de claves equivalente
		//Padre e hijo distinto
		//Estrella
		$this->get_dbt()->elemento("relaciones")->agregar_registro($fila);
	}
	
	function evt__rel_form__carga()
	{
		if(isset($this->seleccion_relacion)){
			$this->seleccion_relacion_anterior = $this->seleccion_relacion;
			$fila = $this->get_dbt()->elemento("relaciones")->get_registro($this->seleccion_relacion_anterior);
			return $this->rel_fila_a_form($fila);
		}
	}

	function evt__rel_form__baja()
	{
		$this->get_dbt()->elemento("relaciones")->eliminar_registro($this->seleccion_relacion_anterior);
		$this->evt__rel_form__cancelar();
	}
	
	function evt__rel_form__modificacion($datos)
	{
		$fila = $this->rel_form_a_fila($datos);
		$this->get_dbt()->elemento("relaciones")->modificar_registro($fila, $this->seleccion_relacion_anterior);
		$this->evt__rel_form__cancelar();
	}
	
	function evt__rel_form__cancelar()
	{
		unset($this->seleccion_relacion_anterior);
		unset($this->seleccion_relacion);
		$this->dependencias["rel_cuadro"]->deseleccionar();
	}

	function evt__salida__relacion()
	{	
		echo "HOLA";
		$this->evt__rel_form__cancelar();
	}

	//-------------------------------------------------------------
	//-- Cuadro
	//-------------------------------------------------------------

	function evt__rel_cuadro__seleccion($id)
	{
		$this->seleccion_relacion = $id;
	}

	function evt__rel_cuadro__carga()
	{
		return $this->get_dbt()->elemento("relaciones")->get_registros();
	}
	//-------------------------------------------------------------

	//*******************************************************************
	//** PROCESAR  ******************************************************
	//*******************************************************************

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase_proyecto", "toba" );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase", "objeto_datos_relacion" );
		//Sincronizo el DBT
		$this->get_dbt()->sincronizar();	
	}
	//-------------------------------------------------------------------
}
?>