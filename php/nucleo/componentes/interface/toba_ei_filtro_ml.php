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
	protected $_estilos = 'ei-base ei-filtro-ml-base';	
	protected $_colspan;
	protected $_etiquetas = array('columna' => 'Columna', 'condicion' => 'Condición', 'valor' => 'Valor');
	
	
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
			$this->_columnas[$fila['nombre']] = new toba_filtro_columna_cadena($fila);
		}
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
		//--- Si no se cargaron datos, se cargan ahora
		/*if (!isset($this->_datos)) {		
			$this->carga_inicial();
		}*/
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
		$this->generar_botonera_manejo_filas();
		echo "<table class='ei-filtro-ml-grilla' style='width: $ancho' >\n";
		$this->generar_formulario_encabezado();
		$this->generar_formulario_cuerpo();
		echo "\n</table>";
		$this->generar_botones();
	}
	
	/**
	 * Genera el HTML de la botonera de agregar/quitar/ordenar filas
	 */
	protected function generar_botonera_manejo_filas()
	{
		echo "<div class='ei-filtro-ml-botonera'>";
		$texto = toba_recurso::imagen_toba('nucleo/agregar.gif', true);
		echo toba_form::select("{$this->objeto_js}_nuevo", null, array());
		echo toba_form::button_html("{$this->objeto_js}_agregar", $texto, 
								"onclick='{$this->objeto_js}.crear_fila();'", $this->_rango_tabs[0]++, '+', 'Crea una nueva fila');
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
		$primera = true;
		foreach ($this->_etiquetas as $etiqueta){
			echo "<th class='ei-filtro-ml-columna'>\n";
			echo $etiqueta;
			echo "</th>\n";
		}
		//-- Columna para borrar en línea
		echo "<th class='ei-filtro-ml-columna'>&nbsp;\n";
		echo "</th>\n";				
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
				$estilo_fila = "";
			}
			echo "\n<!-- FILA $nombre_col -->\n\n";			
			echo "<tr $estilo_fila id='{$this->objeto_js}_fila$nombre_col' onclick='{$this->objeto_js}.seleccionar(\"$nombre_col\")'>";
			echo "<td class='ei-filtro-ml-col'>";
			echo $columna->get_etiqueta();
			echo "</td>\n";
			
			//-- Condición
			if ($columna->tiene_condicion()) {
				echo "<td class='ei-filtro-ml-cond'>";
				echo $columna->get_html_condicion();
				echo "</td>\n";
				echo "<td class='ei-filtro-ml-valor'>";
			} else {
				echo "<td class='ei-filtro-ml-valor' colspan=2>";
			}
			//-- Valor
			$columna->get_html_valor();
			echo "</td>\n";

			//-- Borrar a nivel de fila
			echo "<td class='$estilo_celda ei-filtro-ml-borrar'>";
			echo toba_form::button_html("{$this->objeto_js}_eliminar$nombre_col", toba_recurso::imagen_toba('borrar.gif', true), 
									"onclick='{$this->objeto_js}.seleccionar($nombre_col);{$this->objeto_js}.eliminar_seleccionada();'", 
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
			echo $identado."{$this->objeto_js}.agregar_ef({$columna->crear_objeto_js()}, '{$columna->get_nombre()}');\n";
		}
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