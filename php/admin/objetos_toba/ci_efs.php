<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/db/toba_dbt.php");
require_once("nucleo/browser/interface/ef.php");
/*
	El controlador tiene que implementar:

		- get_dbr_efs()
*/
class ci_efs extends objeto_ci
{
	protected $db_registros;
	protected $seleccion_efs;
	protected $seleccion_efs_anterior;
	private $id_intermedio_efs;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_dbr()->elemento('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		$propiedades[] = "seleccion_efs";
		$propiedades[] = "seleccion_efs_anterior";
		return $propiedades;
	}

	function get_dbr()
	//Acceso al db_tablas
	{
		if (! isset($this->db_registros)) {
			$this->db_registros = $this->controlador->get_dbr_efs();
		}
		return $this->db_registros;
	}

	function mostrar_efs_detalle()
	{
		if( isset($this->seleccion_efs) ){
			return true;	
		}
		return false;
	}

	function get_lista_ei()
	{
		$ei[] = "efs_lista";
		if( $this->mostrar_efs_detalle() ){
			$ei[] = "efs";
			$ei[] = "efs_ini";
		}
		return $ei;	
	}
	
	function evt__post_cargar_datos_dependencias()
	{
		if( $this->mostrar_efs_detalle() ){
			//Protejo la efs seleccionada de la eliminacion
			$this->dependencias["efs_lista"]->set_fila_protegida($this->seleccion_efs_anterior);
			//Agrego el evento "modificacion" y lo establezco como predeterminado
			$this->dependencias["efs"]->agregar_evento( eventos::modificacion(null, false), true );
		}
	}
	
	function limpiar_seleccion()
	{
		unset($this->seleccion_efs);
		unset($this->seleccion_efs_anterior);
	}

	//-------------------------------
	//---- EI: Lista de efss ----
	//-------------------------------
	
	function evt__efs_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una efs de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las efss NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		$orden = 1;
		foreach(array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			$registros[$id]['orden'] = $orden;
			$orden++;
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->id_intermedio_efs[$id] = $this->get_dbr()->agregar_registro($registros[$id]);
					break;	
				case "B":
					$this->get_dbr()->eliminar_registro($id);
					break;	
				case "M":
					$this->get_dbr()->modificar_registro($registros[$id], $id);
					break;	
			}
		}
	}
	
	function evt__efs_lista__carga()
	{
		if($datos_dbr = $this->get_dbr()->get_registros() )
		{
			//Ordeno los registros segun la 'posicion'
			//ei_arbol($datos_dbr,"Datos para el ML: PRE proceso");
			for($a=0;$a<count($datos_dbr);$a++){
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC , $datos_dbr);
			//EL formulario_ml necesita necesita que el ID sea la clave del array
			//No se solicita asi del DBR porque array_multisort no conserva claves numericas
			// y las claves internas del DBR lo son
			for($a=0;$a<count($datos_dbr);$a++){
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset( $datos_dbr[$a][apex_db_registros_clave] );
				$datos[ $id_dbr ] = $datos_dbr[$a];
			}
			//ei_arbol($datos,"Datos para el ML: POST proceso");
			return $datos;
		}
	}

	function evt__efs_lista__seleccion($id)
	{
		if(isset($this->id_intermedio_efs[$id])){
			$id = $this->id_intermedio_efs[$id];
		}
		$this->seleccion_efs = $id;
	}

	//-----------------------------------------
	//---- EI: Info detalla de un EF ----------
	//-----------------------------------------

	function evt__efs__modificacion($datos)
	{
		$this->get_dbr()->modificar_registro($datos, $this->seleccion_efs_anterior);
	}
	
	function evt__efs__carga()
	{
		$this->seleccion_efs_anterior = $this->seleccion_efs;
		return $this->get_dbr()->get_registro($this->seleccion_efs_anterior);
	}

	function evt__efs__cancelar()
	{
		unset($this->seleccion_efs);
		unset($this->seleccion_efs_anterior);
	}

	//-----------------------------------------
	//---- EI: Inicializacion del EF ----------
	//-----------------------------------------

	function evt__efs_ini__carga()
	{
		$this->seleccion_efs;
		$registro = $this->get_dbr()->get_registro($this->seleccion_efs_anterior);
		$ef = $registro['elemento_formulario'];
		$parametros = call_user_func(array($ef,"get_parametros"));
		$temp = array();
		$a=0;
		foreach($parametros as $clave => $desc){
			$temp[$a]['clave'] = $clave;
			$temp[$a]['valor'] = "111";
			$temp[$a]['ayuda'] = "";
			//$desc['descripcion'];
			$a++;	
		}
		return $temp;
	}
	//-------------------------------------------------------------------
}
?>