<?php

/**
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_formulario ei_filtro_ml
 * @wiki Referencia/Objetos/ei_filtro_ml
 */
class toba_ei_filtro_ml extends toba_ei
{
	protected $_columnas;
	protected $_columnas_datos;
	protected $_estilos = 'ei-base ei-filtro-ml-base';	
	protected $_colspan;
	protected $_etiquetas = array('columna' => 'Columna', 'condicion' => 'Condición', 'valor' => 'Valor');
	protected $_rango_tabs;					// Rango de números disponibles para asignar al taborder
	
	/**
	 * Método interno para iniciar el componente una vez construido
	 * @ignore 
	 */	
	function inicializar($parametros)
	{
		parent::inicializar($parametros);
		//$this->_nombre_formulario =	$parametros["nombre_formulario"];
		$this->crear_columnas();
		$this->set_grupo_eventos_activo('no_cargado');
	}	
	
	
	protected function crear_columnas()
	{
		$this->_columnas = array();
		foreach ($this->_info_filtro_col as $fila) {
			$clase = 'toba_filtro_columna_'.$fila['tipo'];
			$this->_columnas[$fila['nombre']] = new $clase($fila, $this);
		}
	}
	
	/**
	 * Consume un tabindex html del componente y lo retorna
	 * @return integer
	 */	
	function get_tab_index()
	{
		if (isset($this->_rango_tabs)) {
			return $this->_rango_tabs[0]++;
		}	
	}

	//---------------------------------------------------------------------------------
	//-------------------------- EVENTOS ----------------------------------------------
	//---------------------------------------------------------------------------------
	
	
	/**
	 * @ignore 
	 */
	function disparar_eventos()
	{
		$this->_log->debug( $this->get_txt() . " disparar_eventos", 'toba');		
		$validado = false;
		//Veo si se devolvio algun evento!
		if (isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if (isset($this->_memoria['eventos'][$evento])) {
				//Me fijo si el evento requiere validacion
				$maneja_datos = ($this->_memoria['eventos'][$evento] == apex_ei_evt_maneja_datos);
				if($maneja_datos) {
					$this->cargar_post();	
					$parametros = $this->get_datos();
				} else {
					$parametros = null;
				}
				//El evento es valido, lo reporto al contenedor
				$this->reportar_evento( $evento, $parametros );
			}
		}
	}
	
	/**
	 * @ignore 
	 */
	protected function cargar_post()
	{
		if (! isset($_POST[$this->objeto_js.'_listafilas'])) {
			return false;
		}
		$lista_filas = $_POST[$this->objeto_js.'_listafilas'];
		$filas_post = array();
		if ($lista_filas != '') {
			$filas_post = explode(apex_qs_separador, $lista_filas);
			$this->_columnas_datos = array();
			//Por cada fila
			foreach ($filas_post as $fila) {
				if (! isset($this->_columnas[$fila])) {
					throw new toba_error("Columna $fila inválida");
				}
				$this->_columnas[$fila]->resetear_estado();
				$this->_columnas[$fila]->cargar_estado_post();
				$validacion = $this->_columnas[$fila]->validar_estado();
				if ($validacion !== true) {
					$etiqueta = $columna->get_etiqueta();
					throw new toba_error_validacion($etiqueta.': '.$validacion, $columna);
				}
				$this->_columnas_datos[$fila] = $this->_columnas[$fila];
			}
		}
		return true;
	}
	
	function get_datos()
	{
		$datos = array();
		foreach ($this->_columnas_datos as $fila => $columna) {
			$datos[$fila] = $columna->get_estado();
		}
		return $datos;
	}
	
	
	/**
	 * Borra los datos actuales y resetea el estado de los efs
	 */
	function limpiar_interface()
	{
		foreach ($this->_lista_ef as $ef) {
			$this->_elemento_formulario[$ef]->resetear_estado();
		}
	}	
	
	
	//-------------------------------------------------------------------------------
	//----------------------------	  MANEJO DE DATOS -------------------------------
	//-------------------------------------------------------------------------------
	
	
	/**
	 * Carga el filtro con un conjunto de datos
	 * @param array $datos Arreglo columna=>valor/es
	 * @param boolean $set_cargado Cambia el grupo activo al 'cargado', mostrando los botones de modificacion, eliminar y cancelar por defecto
	 */
	function set_datos($datos, $set_cargado=true)
	{
		if (isset($datos)){
			foreach ($this->_columnas as $id => $columna) {
				$columna->resetear_estado();
				if (isset($datos[$id])) {
					$columna->set_estado($datos[$id]);
					$columna->set_visible(true);
				} else {
					$columna->set_visible(false);
				}
			}
			if ($set_cargado && $this->_grupo_eventos_activo != 'cargado') {
				$this->set_grupo_eventos_activo('cargado');
			}
		}	
	}
	
	/**
	 * Retorna la clausula a incluir en el where de una sql, basada en el estado actual del filtro
	 */
	function get_where()
	{
		$where = array();
		foreach ($this->_columnas_datos as $columna) {
			$where[] = $columna->get_where();
		}
		return "\t\t".implode("\n\tAND\t", $where);
	}
	
	//-------------------------------------------------------------------------------
	//----------------------------	  SALIDA	  -----------------------------------
	//-------------------------------------------------------------------------------
	
	
	function generar_html()
	{
		//Genero la interface
		echo "\n\n<!-- ***************** Inicio EI FILTRO ML (	".	$this->_id[1] ." )	***********	-->\n\n";
		//Campo de sincroniacion con JS
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit.'_implicito', '');
		$ancho = '';
		if (isset($this->_info_filtro["ancho"])) {
			$ancho = convertir_a_medida_tabla($this->_info_filtro["ancho"]);
		}
		echo "<table class='{$this->_estilos}' $ancho>";
		echo "<tr><td style='padding:0'>";
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-filtro-ml-barra-sup");
		$this->generar_formulario();
		echo "</td></tr>\n";
		echo "</table>\n";
		$this->_flag_out = true;
	}	
	

	/**
	 * @ignore 
	 */
	protected function generar_formulario()
	{
		$this->_rango_tabs = toba_manejador_tabs::instancia()->reservar(100);		
		$this->_colspan = 0;
	
		//Ancho y Scroll
		$estilo = '';
		$ancho = isset($this->_info_filtro["ancho"]) ? $this->_info_filtro["ancho"] : "auto";
		$alto_maximo = "auto";
		if (isset($this->_colapsado) && $this->_colapsado) {
			$estilo .= "display:none;";
		}
		//Campo de comunicacion con JS
		echo toba_form::hidden("{$this->objeto_js}_listafilas",'');
		echo toba_form::hidden("{$this->objeto_js}__parametros", '');		
		echo "<div class='ei-cuerpo ei-filtro-ml-base' id='cuerpo_{$this->objeto_js}' style='$estilo'>";
		$this->generar_layout($ancho);
		echo "\n</div>";
	}	
	
	protected function generar_layout($ancho)
	{
		//Botonera de agregar y ordenar
		echo "<table id='{$this->objeto_js}_grilla' class='ei-filtro-ml-grilla' style='width: $ancho' >\n";
		$this->generar_formulario_encabezado();
		$this->generar_formulario_cuerpo();
		echo "\n</table>";
		$this->generar_botonera_manejo_filas();		
		$this->generar_botones();
	}
	
	/**
	 * Genera el HTML de la botonera de agregar/quitar/ordenar filas
	 */
	protected function generar_botonera_manejo_filas()
	{
		echo "<div class='ei-filtro-ml-botonera'>";
		$texto = toba_recurso::imagen_toba('nucleo/agregar.gif', true);
		$opciones = array(apex_ef_no_seteado => '');
		foreach ($this->_columnas as $columna) {
			$opciones[$columna->get_nombre()] = $columna->get_etiqueta();
		}
		echo 'Agregar filtro ';
		$onchange = "onchange='{$this->objeto_js}.crear_fila()'";
		echo toba_form::select("{$this->objeto_js}_nuevo", null, $opciones, 'ef-combo', $onchange);
		echo "</div>\n";
	}	
	
	/**
	 * @ignore 
	 */
	protected function generar_formulario_encabezado()
	{
		echo "<thead id='cabecera_{$this->objeto_js}'>\n";		
		//------ TITULOS -----	
		echo "<tr>\n";
		$i = 1;
		foreach ($this->_etiquetas as $id => $etiqueta){
			$colspan = '';
			if ($i == count($this->_etiquetas)) {
				$colspan = 'colspan=2';
			}
			echo "<th class='ei-filtro-ml-columna' $colspan>\n";
			echo $etiqueta;
			echo "</th>\n";
			$i++;
		}
		echo "</tr>\n";
		echo "</thead>\n";
	}
	
	/**
	 * @ignore 
	 */
	protected function generar_formulario_cuerpo()
	{
		echo "<tbody>";			
		$estilo_celda = "ei-filtro-ml-fila";
		foreach ($this->_columnas as $nombre_col => $columna) {
			if ($columna->es_visible()) {
				$estilo_fila = "";
			} else {
				$estilo_fila = "style='display:none;'";
			}
			echo "\n<!-- FILA $nombre_col -->\n\n";			
			echo "<tr $estilo_fila id='{$this->objeto_js}_fila$nombre_col' onclick='{$this->objeto_js}.seleccionar(\"$nombre_col\")'>";
			echo "<td class='$estilo_celda ei-filtro-ml-col'>";
			echo $columna->get_etiqueta();
			echo "</td>\n";
			
			//-- Condición
			echo "<td class='$estilo_celda ei-filtro-ml-cond'>";
			if ($columna->tiene_condicion()) {				
				echo $columna->get_html_condicion();
			} else {
				echo '&nbsp;';
			}
			
			echo "</td>\n";
			
			//-- Valor			
			echo "<td class='$estilo_celda ei-filtro-ml-valor'>";
			$columna->get_html_valor();
			echo "</td>\n";

			//-- Borrar a nivel de fila
			echo "<td class='$estilo_celda ei-filtro-ml-borrar'>";
			echo toba_form::button_html("{$this->objeto_js}_eliminar$nombre_col", toba_recurso::imagen_toba('borrar.gif', true), 
									"onclick='{$this->objeto_js}.seleccionar(\"$nombre_col\");{$this->objeto_js}.eliminar_seleccionada();'", 
									$this->_rango_tabs[0]++, null, 'Elimina la fila');
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</tbody>\n";		
	}	
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$id = toba_js::arreglo($this->_id, false);
		echo $identado."window.{$this->objeto_js} = new ei_filtro_ml($id, '{$this->objeto_js}', '{$this->_submit}');\n";
		foreach ($this->_columnas as $columna) {
			$visible = $columna->es_visible() ? 'true' : 'false';
			$compuesto = $columna->es_compuesto() ? 'true' : 'false';
			echo $identado."{$this->objeto_js}.agregar_ef({$columna->crear_objeto_js()}, '{$columna->get_nombre()}', $visible, $compuesto);\n";
		}
	}
	
	/**
	 * Retorna una referencia al ef en javascript
	 * @param string $id Id. del ef
	 * @return string
	 */
	function get_objeto_js_ef($id)
	{
		return "{$this->objeto_js}.ef('$id')";
	}	
	
	function get_objeto_js()
	{
		return $this->objeto_js;
	}
	
	
	/**
	 * @ignore 
	 */
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_filtro_ml';
		//Busco las	dependencias
		foreach ($this->_columnas as $columna){
			$temp	= $columna->get_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo = array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}	
	
}

?>