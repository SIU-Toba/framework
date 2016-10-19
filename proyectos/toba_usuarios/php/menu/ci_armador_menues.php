<?php
class ci_armador_menues extends toba_ci
{
	protected $s__arbol_cargado = false;
	protected $s__datos_basicos;
	protected $s__filtro;
	protected $s__proyecto;
	protected $s__ids_enviados;
	protected $raiz;
	
	function ini__operacion()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__proyecto = admin_instancia::get_proyecto_defecto();
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());			
		}		
	}
		
	function set_proyecto($proyecto)
	{
		$this->s__proyecto = $proyecto;
	}
	
	function get_proyecto()
	{
		return $this->s__proyecto;
	}
		
	function cortar_arbol()
	{
		unset($this->s__arbol_cargado);
	}	
	
	function es_edicion()
	{
		return $this->dep('datos')->esta_cargada();
	}
	
	function conf__arbol_origen($arbol) 
	{		
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_items_menu($this->s__filtro['proyecto']);
			$catalogador->cargar_todo();			
			$raiz = $catalogador->buscar_carpeta_inicial();	
			$arbol->set_datos(array($raiz), true);
			$this->s__arbol_cargado = true;
		}
	}
	
	function get_ids_enviados()
	{
		if (! isset($this->s__ids_enviados)) {			
			$this->s__ids_enviados = $this->dep('arbol_origen')->get_ids_nodos_enviados();
		}
		return $this->s__ids_enviados;
	}
	
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_final(toba_ei_pantalla $pantalla)
	{
		//Tengo que incorporar la clase del menu, esto lo recupero desde los datos
		$menu_tipo = $this->dep('datos')->tabla('menu')->get_columna('tipo_menu');		
		$datos_menu = consultas_instancia::get_menu_tipos(array('menu' => $menu_tipo));		
		if (! empty($datos_menu)) {
			$raiz = toba_proyecto_db::get_item_raiz($this->s__proyecto);
			$clase = basename($datos_menu[0]['archivo'], '.php');
			$menu = new $clase(false);						//Creo el menu correspondiente pero no hago carga inicial de items
			$menu->set_modo_prueba();
			
			//Se agregan las opciones que se pusieron en el menu y se pasa el objeto a la pantalla para que lo grafique
			$filas = $this->dep('datos')->tabla('operaciones')->get_filas();
			foreach ($filas as $item) {						
				$item['nombre'] = $item['descripcion'];
				$item['es_primer_nivel'] = ($item['padre'] == $raiz);
				$menu->agregar_opcion($item);					
			}
			
			$pantalla->set_menu($menu);
		}		
	}	
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		//$this->dep('datos')->dump_contenido();
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_inicial');
		$this->cortar_arbol();
		unset($this->s__ids_enviados);
	}

	function evt__cancelar()
	{
		unset($this->s__datos_basicos);
		$this->cortar_arbol();
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_inicial');
		unset($this->s__ids_enviados);
	}

	function evt__agregar()
	{
		$this->set_pantalla('pant_basica');
	}	
	
	function evt__armar_menu()
	{
		$this->set_pantalla('pant_armado');
	}
	
	function evt__cambiar_texto()
	{
		$this->set_pantalla('pant_descripciones');
	}

	function evt__previsualizar()
	{
		$this->set_pantalla('pant_final');
	}

	//-----------------------------------------------------------------------------------
	//---- filtro ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
		$this->set_proyecto($datos['proyecto']);
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
		unset($this->s__proyecto);
	}
	
	function conf__filtro($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		}		
	}
	
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__filtro)) {
			$datos = consultas_instancia::get_menus_existentes($this->s__filtro['proyecto']);
			$cuadro->set_datos($datos);
		}
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
		$this->set_pantalla('pant_basica');
	}
	
	//-----------------------------------------------------------------------------------
	//---- form_basico ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_basico(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->tabla('menu')->get_cantidad_filas() > 0) {
			$datos = $this->dep('datos')->tabla('menu')->get();
			$form->set_datos($datos);
		}
	}

	function evt__form_basico__modificacion($datos)
	{
		$datos['proyecto'] = $this->s__proyecto;
		$this->dep('datos')->tabla('menu')->set($datos);		
	}	
	
	//-----------------------------------------------------------------------------------
	//---- form_armado ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	function evt__form_armado__modificacion($datos)
	{
		if (isset($datos['nivel_inicial'])) {
			unset($datos['nivel_inicial']);
		}
		
		if (! empty($datos)) {
			$this->raiz = toba_proyecto_db::get_item_raiz($this->s__proyecto);

			//Recupero los ids enviados por el arbol (lado server)
			$ids_validos = $this->get_ids_enviados();			
			
			//Armo un arreglo con los ids recuperados en forma de padre/hijo
			$ids_recuperados = array();			
			foreach ($datos as $klave => $ids_enviados) {
				$aux = explode('^', $ids_enviados);
				$ids_recuperados = array_merge($ids_recuperados, $aux);
				$datos[$klave] = $aux;
			}
			
			$diff = array_diff($ids_recuperados, $ids_validos);
			if (! empty($diff)) {
				throw new toba_error_seguridad('Una de las opciones indicadas no es valida');
			}

			//Calculo el complemento para distinguir los elementos eliminados  (tambien estan los no seleccionados, asi que hay un poco mas.. pero bueno)			
			if ($this->es_edicion()) {
				$diffr = array_diff($ids_validos, $ids_recuperados);			
				$this->quitar_elementos_eliminados($diffr);
			}
						
			//Se inserta o actualizan los elementos del menu
			$this->actualizar_elementos($ids_recuperados, $datos);
		}		
	}
	
	protected function quitar_elementos_eliminados($elementos)
	{
		foreach ($elementos as $id) {
			$cursor = $this->dep('datos')->tabla('operaciones')->get_id_fila_condicion(array('item' => $id));
			if (! empty($cursor)) {
				$this->dep('datos')->tabla('operaciones')->eliminar_fila(current($cursor));
			}
		}
	}
	
	protected function actualizar_elementos($ids_recuperados, $datos)
	{
		//Aca se recupera la informacion de los items reales del proyecto
		$items_totales = $this->recuperar_info_items();
		foreach ($ids_recuperados as $id) {
			$linea = array('item' => $id);
			$linea['carpeta'] = (isset($items_totales[$id]['carpeta'])) ? $items_totales[$id]['carpeta']: 0;
			$linea['padre'] = $this->descubrir_padre($datos, $id);		
			if (isset($items_totales[$id])) {
				$linea['descripcion'] = $items_totales[$id]['descripcion'];			//Asigno la descripcion de la bd inicialmente
			}

			if (! $this->es_edicion()) {
				$this->dep('datos')->tabla('operaciones')->nueva_fila($linea);
			} else {
				$cursor = $this->dep('datos')->tabla('operaciones')->get_id_fila_condicion(array('item' => $id));
				if (! empty($cursor)) {
					unset($linea['descripcion']);
					$this->dep('datos')->tabla('operaciones')->modificar_fila(current($cursor), $linea);			//En la modificacion dejo la descripcion anterior
				} else {
					$this->dep('datos')->tabla('operaciones')->nueva_fila($linea);
				}
			}
			unset($linea);
		}
	}		
		
	//-----------------------------------------------------------------------------------
	//---- AUXILIARES -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function descubrir_padre($datos, $id) 
	{
		$padre = null;
		foreach ($datos as $klave => $grupo) {
			if (in_array($id, $grupo)) {
				$padre = $klave;
			}
		}
		//Si el valor del padre es igual al que se busca, estamos hablando de una carpeta o item de primer nivel
		$resultado = ($padre == $id) ? $this->raiz : $padre;
		return $resultado;		
	}
	
	function recuperar_info_items()
	{
		$resultado = array();
		$datos = toba_info_editores::get_lista_items($this->s__proyecto, false);
		foreach ($datos as $item) {
			$indx = $item['id'];
			$resultado[$indx] = $item;
		}
		return $resultado;
	}
		
	function buscar_datos_persistidos()
	{
		$buscador = $this->dep('datos')->tabla('operaciones')->nueva_busqueda();
		$buscador->set_columnas_orden(array('carpeta' => SORT_DESC));
		$datos = $buscador->buscar_filas();		
		return $datos;
	}
	
	function get_arreglo_js()
	{
		$arbol = array();
		$datos = $this->buscar_datos_persistidos();
		foreach ($datos as $fila) {
			$indx = $fila['item'];
			$padre = $fila['padre'];
			if (! is_null($padre) && isset($arbol[$padre])) {
				$arbol[$padre][] = $fila['item'];
			} else {
				$arbol[$indx] = array();
			}
		}		
		return $arbol;
	}
	
	
	//------------------------------------------------------------------------------------------------------------------
	//					SERVICIOS AJAX
	//------------------------------------------------------------------------------------------------------------------
	function ajax__get_estructura_arbol($parametros, toba_ajax_respuesta $respuesta)
	{
		//Se arma la estructura del arbol en json para enviarla al plugin JQuery de la anteultima pestaña.
		$arbol = array();
		$datos = $this->buscar_datos_persistidos();
		foreach ($datos as $fila) {
			$indx = $fila['item'];
			$padre = $fila['padre'];
			$aux = array('id' => $fila['item'], 'text' => $fila['descripcion']);
			if ($fila['carpeta']) {
				$aux['children'] = array();
			}
			if (! is_null($padre) && isset($arbol[$padre])) {
				$arbol[$padre]['children'][] = $aux;
			} else {
				$arbol[$indx] = $aux;
			}
			unset($aux);			
		}		
		$respuesta->set(array_values($arbol));
	}
	
	function ajax__set_descripcion_nodo($parametros, toba_ajax_respuesta $respuesta)
	{
		//Cambia la descripcion del nodo indicado
		$ids = $this->get_ids_enviados();		
		$id_nodo = $parametros['id_nodo'];
		//if (in_array($id_nodo, $ids)) {			
			$cursor = $this->dep('datos')->tabla('operaciones')->get_id_fila_condicion(array('item' => $id_nodo));
			$this->dep('datos')->tabla('operaciones')->modificar_fila(current($cursor), array('descripcion' => $parametros['descripcion']));
			$respuesta->set('OK');
		//}
	}
		
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		//Se encarga de crear los hiddens necesarios en el form y agregarle los ids de los nodos seleccionados de manera grafica..
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__cambiar_texto = function()
		{
			var id_hidden, ind;
			this.dep('form_armado').ef('nivel_inicial').set_estado(Object.keys(arbol).join('^'));
			for (ind in arbol) {
				id_hidden = ind + '__hidden';						
				if (arbol[ind].length > 0) {
					arbol[ind].push(ind);
					document.getElementById(id_hidden).value = arbol[ind].join('^');
				} else {
					document.getElementById(id_hidden).value = ind;
				}
			}
			return true;
		}
		";
	}	
}
?>