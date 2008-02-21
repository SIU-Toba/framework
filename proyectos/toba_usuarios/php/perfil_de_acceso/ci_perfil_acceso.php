<?php 
require_once('lib/consultas_instancia.php');

class ci_perfil_acceso extends toba_ci
{
	protected $s__catalogador;
	protected $s__proyecto;
	protected $s__grupo_acceso;

	function ini()
	{
		/**
		 * Aca hay que ver la manera en que se le va a pasar el proyecto que se esta editando.
		 * Quizas modificando toba_editor::get_proyecto_cargado.
		 * @todo 
		 */
	}
	
	 function evt__grupos_acceso__seleccion($seleccion)
	 {
	 	$this->s__grupo_acceso = $seleccion['usuario_grupo_acc'];
	 	$this->s__proyecto = $seleccion['proyecto'];
	 	$this->s__catalogador = new toba_catalogo_items_perfil($this->s__proyecto);		
	 	$this->s__catalogador->set_grupo_acceso($this->s__grupo_acceso);
	 	$this->set_pantalla('edicion_acceso');
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
		
		$this->s__catalogador->cargar($opciones, $inicial, $excepciones);
		
		//$this->dependencia('arbol_perfiles')->set_frame_destino(apex_frame_centro);

		if (isset($this->s__opciones)) {
			//Cuando el catalogo carga todo los items es porque va a filtrar algo
			//entonces el resultado se debe mostrar completo, sin colapsados
			if ($this->catalogador->debe_cargar_todo($this->s__opciones)) {
				$this->dependencia('arbol_perfiles')->set_todos_abiertos();
			}
		}
		
		$nodo = $this->s__catalogador->buscar_carpeta_inicial();
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
		$arbol->set_mostrar_utilerias(false);
		if (isset($this->s__opciones['inicial'])) {
			return $this->get_nodo_raiz($this->s__opciones['inicial']);
		} else {
			return $this->get_nodo_raiz();	
		}
	}
	
	function conf__grupos_acceso($cuadro)
	{
		return consultas_instancia::get_lista_grupos_acceso_proyecto();
	}
	
	function evt__volver()
	{
		unset($this->s__catalogador);
		$this->set_pantalla('seleccion_acceso');
	}
	
}

?>