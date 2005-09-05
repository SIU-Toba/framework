<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/db/dao_editores.php');

class ci_relaciones extends objeto_ci
{
	protected $tabla;
	protected $seleccion_relacion;
	protected $seleccion_relacion_anterior;
	private $id_intermedio_relaciones;
	private $rel_activa_padre;
	private $rel_activa_hijo;

	function destruir()
	{
		parent::destruir();
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_relacion";
		$propiedades[] = "seleccion_relacion_anterior";
		return $propiedades;
	}

	function get_tabla()
	{
		if (! isset($this->tabla)) {
			$this->tabla = $this->controlador->get_tabla_relaciones();
		}
		return $this->tabla;
	}

	function mostrar_detalle_relacion()
	{
		if( isset($this->seleccion_relacion) ){
			return true;	
		}
		return false;
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if( !$this->mostrar_detalle_relacion() ){
			unset($eventos['cancelar']);
		}		
		return $eventos;
	}

	function evt__cancelar()
	{
		$this->limpiar_seleccion();	
	}

	function get_lista_ei()
	{
		$ei[] = "relaciones_lista";
		if( $this->mostrar_detalle_relacion() ){
			$ei[] = "relaciones_columnas";
		}
		return $ei;	
	}

	function evt__post_cargar_datos_dependencias()
	{
		if( $this->mostrar_detalle_relacion() ){
			//Protejo la efs seleccionada de la eliminacion
			//ATENCION! - $this->dependencias["relaciones_lista"]->set_fila_protegida($this->seleccion_relacion_anterior);
			//Agrego el evento "modificacion" y lo establezco como predeterminado
			$this->dependencias["relaciones_columnas"]->agregar_evento( eventos::modificacion(null, false), true );
		}
		if (isset($this->seleccion_relacion)) {
			$this->dependencias["relaciones_lista"]->seleccionar($this->seleccion_relacion);
		}
	}
	
	function limpiar_seleccion()
	{
		unset($this->seleccion_relacion);
		unset($this->seleccion_relacion_anterior);
	}

	//--------------------------------------------------------------------------------------
	//---- Lista de RELACIONES
	//--------------------------------------------------------------------------------------

	function get_lista_tablas()
	{
		return $this->controlador->get_lista_tablas();
	}

	function conversion_form_a_fila($datos)
	//Adapta el contenido del form a una fila
	{
		//-- PADRE --
		$padre = explode(",",$datos['padre']);
		$datos['padre_id'] = $padre[0];
		$datos['padre_proyecto'] = toba::get_hilo()->obtener_proyecto();
		$datos['padre_objeto'] = $padre[1];
		unset($datos['padre']);
		//-- HIJO --
		$hijo = explode(",",$datos['hija']);
		$datos['hijo_id'] = $hijo[0];
		$datos['hijo_proyecto'] = toba::get_hilo()->obtener_proyecto();
		$datos['hijo_objeto'] = $hijo[1];
		unset($datos['hija']);
		return $datos;
	}
	
	function conversion_fila_a_form($fila)
	//Adapta el contenido de una fila al form
	{
		$fila['padre'] = $fila['padre_id'] . "," . $fila['padre_objeto'];
		$fila['hija'] = $fila['hijo_id'] . "," . $fila['hijo_objeto'];
		unset($fila['padre_id']);
		unset($fila['padre_objeto']);
		unset($fila['hijo_id']);
		unset($fila['hijo_objeto']);
		return $fila;
	}
	
	function evt__relaciones_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una efs de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las RELACIONES NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		//$orden = 1;
		$tabla = $this->get_tabla();
		foreach(array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			//$registros[$id]['orden'] = $orden;
			//$orden++;
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			$fila = $this->conversion_form_a_fila($registros[$id]);
			switch($accion){
				case "A":
					$this->id_intermedio_relaciones[$id] = $tabla->nueva_fila($fila);
					break;	
				case "B":
					$tabla->eliminar_fila($id);
					break;	
				case "M":
					$tabla->modificar_fila($id, $fila);
					break;	
			}
		}
		//ei_arbol($tabla->get_filas(),"FILAS");
	}
	
	function evt__relaciones_lista__carga()
	{
		if($datos_tabla = $this->get_tabla()->get_filas() )
		{
			for($a=0;$a<count($datos_tabla);$a++){
				//Planifico el ORDEN
				$orden[] = $datos_tabla[$a]['orden'];
				//ADAPTO los datos al FORM
				$datos_tabla[$a] = $this->conversion_fila_a_form($datos_tabla[$a]);
			}
			array_multisort($orden, SORT_ASC , $datos_tabla);
			// EL formulario_ml necesita necesita que el ID sea la clave del array
			// No se solicita asi del DBR porque array_multisort no conserva claves numericas
			// y las claves internas del DBR lo son
			for($a=0;$a<count($datos_tabla);$a++){
				$id_interno = $datos_tabla[$a][apex_datos_clave_fila];
				unset( $datos_tabla[$a][apex_db_registros_clave] );
				$datos[ $id_interno ] = $datos_tabla[$a];
			}
			//ei_arbol($datos,"Datos para el ML: POST proceso");
			return $datos;
		}
	}

	function evt__relaciones_lista__seleccion($id)
	{
		if(isset($this->id_intermedio_relaciones[$id])){
			$id = $this->id_intermedio_relaciones[$id];
		}
		$this->seleccion_relacion = $id;
	}
	
	//-------------------------------------------------------------------------------------
	//---- DETALLE de la RELACION
	//-------------------------------------------------------------------------------------

 /*
		$fila['padre_clave'] = implode(",",$datos['padre_columnas']);
		$fila['hijo_clave'] = implode(",",$datos['hija_columnas']);

		$datos['padre_columnas'] = explode(",", $fila['padre_clave']);
		$datos['hija_columnas'] = explode(",", $fila['hijo_clave']);
 */
	function evt__pre_cargar_datos_dependencias()
	{
		if( $this->mostrar_detalle_relacion() ){
			//Precargo la informacion que necesita el ML inferior
			$relacion_activa = $this->get_tabla()->get_fila($this->seleccion_relacion);
			$this->rel_activa_padre = $relacion_activa['padre_objeto'];
			$this->rel_activa_hijo = $relacion_activa['hijo_objeto'];
		}
	}

	function get_columnas_padre()
	{
		return dao_editores::get_lista_dt_columnas( $this->rel_activa_padre );
	}
	
	function get_columnas_hija()
	{
		return dao_editores::get_lista_dt_columnas( $this->rel_activa_hijo );
	}

	function evt__relaciones_columnas__modificacion($datos)
	{
		//for($a=0;$a<count($datos);$a++){
			
		//}
		//$this->get_tabla()->modificar_fila($this->seleccion_relacion_anterior, $datos);
	}
	
	function evt__relaciones_columnas__carga()
	{
		//$this->seleccion_relacion_anterior = $this->seleccion_relacion;
		//return $this->get_tabla()->get_fila($this->seleccion_relacion_anterior);
	}
	//-------------------------------------------------------------------------------------
}
?>