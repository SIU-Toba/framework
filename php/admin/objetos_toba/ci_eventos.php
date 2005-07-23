<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/db/autoload.php");

class ci_editor extends objeto_ci
{
	//Eventos
	private $db_registros;
	protected $seleccion_evento;
	protected $seleccion_evento_anterior;
	private $id_intermedio_evento;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_dbt()->elemento('columnas')->info(true),"COLUMNAS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_evento";
		$propiedades[] = "seleccion_evento_anterior";
		return $propiedades;
	}

	function get_dbr()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = new dbt_objeto_ei_cuadro($this->info['fuente']);
		}
		return $this->db_tablas;
	}

	function get_eventos_estandar()
	{
		$evento[0]['identificador'] = "seleccion";
		$evento[0]['etiqueta'] = "";
		$evento[0]['imagen_recurso_origen'] = "apex";
		$evento[0]['imagen'] = "doc.gif";	
		return $evento;
	}

	//*******************************************************************
	//*******************  EVENTOS  ************************************
	//*******************************************************************

	function mostrar_evento_detalle()
	{
		if( isset($this->seleccion_evento) ){
			return true;	
		}
		return false;
	}

	function get_lista_ei__3()
	{
		$ei[] = "eventos_lista";
		if( $this->mostrar_evento_detalle() ){
			$ei[] = "eventos";
		}
		return $ei;	
	}
	
	function evt__salida__3()
	{
		unset($this->seleccion_evento);
		unset($this->seleccion_evento_anterior);
	}

	function evt__post_cargar_datos_dependencias__3()
	{
		$evt = eventos::evento_estandar("init","Cargar EVENTOS estandar",true,null,null,true);
		$this->dependencias["eventos_lista"]->agregar_evento( $evt );
		if( $this->mostrar_evento_detalle() ){
			//Protejo la evento seleccionada de la eliminacion
			$this->dependencias["eventos_lista"]->set_fila_protegida($this->seleccion_evento_anterior);
			//Agrego el evento "modificacion" y lo establezco como predeterminado
			$this->dependencias["eventos"]->agregar_evento( eventos::modificacion(null, false), true );
		}
	}

	//-------------------------------
	//---- EI: Lista de eventos ----
	//-------------------------------
	
	function evt__eventos_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una evento de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las eventos NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/

		//FALT CONTROL : (Etiqueta o imagen) completa

		$dbr = $this->get_dbt()->elemento("eventos");
		foreach(array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->id_intermedio_evento[$id] = $dbr->agregar_registro($registros[$id]);
					break;	
				case "B":
					$dbr->eliminar_registro($id);
					break;	
				case "M":
					$dbr->modificar_registro($registros[$id], $id);
					break;	
			}
		}
	}
	
	function evt__eventos_lista__carga()
	{
		return $this->get_dbt()->elemento('eventos')->get_registros(null, true);
	}

	function evt__eventos_lista__seleccion($id)
	{
		if(isset($this->id_intermedio_evento[$id])){
			$id = $this->id_intermedio_evento[$id];
		}
		$this->seleccion_evento = $id;
	}
	
	function evt__eventos_lista__init()
	{
		foreach($this->get_eventos_estandar() as $evento)
		{
			try{
				$this->get_dbt()->elemento("eventos")->agregar_registro($evento);
			}catch(excepcion_toba $e){
				toba::get_cola_mensajes()->agregar("Error agregando el evento '{$evento['identificador']}'. " . $e->getMessage());
			}
		}
	}

	//-----------------------------------------
	//---- EI: Info detalla de un EVENTO ------
	//-----------------------------------------

	function evt__eventos__modificacion($datos)
	{
		$this->get_dbt()->elemento('eventos')->modificar_registro($datos, $this->seleccion_evento_anterior);
	}
	
	function evt__eventos__carga()
	{
		$this->seleccion_evento_anterior = $this->seleccion_evento;
		return $this->get_dbt()->elemento('eventos')->get_registro($this->seleccion_evento_anterior);
	}

	function evt__eventos__cancelar()
	{
		unset($this->seleccion_evento);
		unset($this->seleccion_evento_anterior);
	}
}
?>