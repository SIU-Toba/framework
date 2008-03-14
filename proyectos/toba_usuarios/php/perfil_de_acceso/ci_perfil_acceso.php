<?php 
require_once('lib/consultas_instancia.php');

class ci_perfil_acceso extends toba_ci
{
	protected $s__catalogador;
	protected $s__proyecto;
	protected $s__grupo_acceso;

	protected $s__arbol_cargado = false;

	function conf__arbol_perfiles($arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_items_perfil('toba_referencia', 'Usuario');
			$catalogador->cargar_todo();
			$raiz = $catalogador->buscar_carpeta_inicial();
			$arbol->set_datos(array($raiz), true);
			$this->s__arbol_cargado = true;
		}
	}
	
	function evt__guardar($datos)
	{
		$raices = $this->dep('arbol_perfiles')->get_datos();
		toba::db()->abrir_transaccion();
		/*
			$this->dep('datos')->sincronizar();
		
			Alta: se lepide el grupo de acceso al DT principal y se le pasa a los nodos
				$raiz->set_grupo_acceso( X );	
		*/
		
		foreach($raices as $raiz) {
			$raiz->sincronizar();	
		}
		unset($this->s__arbol_cargado);
		toba::db()->cerrar_transaccion();
	}

/*
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

		if (isset($this->s__opciones)) {
			//Cuando el catalogo carga todo los items es porque va a filtrar algo
			//entonces el resultado se debe mostrar completo, sin colapsados
			if ($this->s__catalogador->debe_cargar_todo($this->s__opciones)) {
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
		//return consultas_instancia::get_lista_grupos_acceso_proyecto();
	}
	
	function evt__volver()
	{
		$this->set_pantalla('seleccion_acceso');
	}
*/
	
}

?>