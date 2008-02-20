<?php 
class ci_perfil_acceso extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini()
	{
		/**
		 * Aca hay que ver la manera en que se le va a pasar el proyecto que se esta editando.
		 * Quizas modificando toba_editor::get_proyecto_cargado.
		 * @todo 
		 */
		
		$this->catalogador = new toba_catalogo_items_perfil('toba_referencia');		
	}

	function ini__operacion()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- Config. ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
	}

	//---- Configuracion de Pantallas ---------------------------------------------------

	function conf__pant_perfil_acceso($pantalla)
	{
	}
	
	//-------------------------------
	//---- Listado de items ----
	//-------------------------------

	function get_nodo_raiz($inicial=null, $con_excepciones=true)
	{
		$excepciones = array();
		//¿Hay apertura seleccionada?		
		if (isset($this->s__apertura) && $con_excepciones) {
			$apertura = (isset($this->apertura_selecc)) ? $this->apertura_selecc : $this->s__apertura;
			$this->dependencia('arbol_perfiles')->set_apertura_nodos($apertura);
			foreach ($apertura as $nodo => $incluido) {
				if ($incluido) {
					$excepciones[] = $nodo;	
				}	
			}
		}

		$opciones = isset($this->s__opciones) ? $this->s__opciones : array();
		$this->catalogador->cargar($opciones, $inicial, $excepciones);
		
		//$this->dependencia('arbol_perfiles')->set_frame_destino(apex_frame_centro);

		if (isset($this->s__opciones)) {
			//Cuando el catalogo carga todo los items es porque va a filtrar algo
			//entonces el resultado se debe mostrar completo, sin colapsados
			if ($this->catalogador->debe_cargar_todo($this->s__opciones)) {
				$this->dependencia('arbol_perfiles')->set_todos_abiertos();
			}
		}
		
		$nodo = $this->catalogador->buscar_carpeta_inicial();
		if ($nodo !== false) {
			$nodo->cargar_rama();
			//--- Cuando es un item directo y no una carpeta se aumenta la apertura
			if (!$nodo->es_carpeta()) {
				$this->dependencia('arbol_perfiles')->set_nivel_apertura(3);
			}
			return array($nodo);
		}		
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- arbol_perfiles ---------------------------------------------------------------

	//arreglo asociativo 'id_del_nodo' => 0|1 determinando si esta abierto o no
	function evt__arbol_perfiles__cambio_apertura($apertura)
	{
	}

	function evt__arbol_perfiles__ver_propiedades($nodo)
	{
	}
	
	function evt__arbol_perfiles__cargar_nodo($id)
	{
		return $this->get_nodo_raiz($id, false);
	}

	//
	function conf__arbol_perfiles(toba_ei_arbol $arbol)
	{
		if (isset($this->s__opciones['inicial'])) {
			return $this->get_nodo_raiz($this->s__opciones['inicial']);
		} else {
			return $this->get_nodo_raiz();	
		}
	}
}

?>