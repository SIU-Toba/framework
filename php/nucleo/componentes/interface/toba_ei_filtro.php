<?php

/**
 * Un filtro presenta una grilla donde es posible seleccionar criterios de búsqueda para las distintas columnas definidas.
 * Según el tipo de la columna se despliegan distintos criterios
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_filtro ei_filtro
 * @wiki Referencia/Objetos/ei_filtro
 */
class toba_ei_filtro extends toba_ei
{
	protected $_columnas;
	protected $_columnas_datos;
	protected $_estilos = 'ei-base ei-filtro-base';	
	protected $_colspan;
	protected $_etiquetas = array('columna' => 'Columna', 'condicion' => 'Condición', 'valor' => 'Valor');
	protected $_rango_tabs;					// Rango de números disponibles para asignar al taborder
	protected $_carga_opciones_ef;			//Encargado de cargar las opciones de los efs
	protected $_clase_formateo = 'toba_formateo';

	//Salida PDF
	protected $_pdf_letra_tabla = 8;
	protected $_pdf_tabla_ancho;
	protected $_pdf_tabla_opciones = array();
	
	
	final function __construct($definicion)
	{
		parent::__construct($definicion);
	}
	
	/**
	 * Método interno para iniciar el componente una vez construido
	 * @ignore 
	 */	
	function inicializar($parametros=array())
	{
		parent::inicializar($parametros);
		//$this->_nombre_formulario =	$parametros["nombre_formulario"];
		$this->crear_columnas();
		$this->set_grupo_eventos_activo('no_cargado');
	}	
	
	/**
	 * Crea los objetos columna necesarios
	 */
	protected function crear_columnas()
	{
		$this->_columnas = array();
		$efs = array();
		$parametros_efs = array();
		foreach ($this->_info_filtro_col as $fila) {
			$clase = 'toba_filtro_columna_'.$fila['tipo'];
			$this->_columnas[$fila['nombre']] = new $clase($fila, $this);
			$efs[$fila['nombre']] = $this->_columnas[$fila['nombre']]->get_ef();
			$parametros = $fila;
			if (isset($parametros['carga_sql']) && !isset($parametros['carga_fuente'])) {
				$parametros['carga_fuente']=$this->_info['fuente'];
			}			
			$parametros_efs[$fila['nombre']] = $parametros;
		}
		//--- Se registran las cascadas porque la validacion de efs puede hacer uso de la relacion maestro-esclavo
		$this->_carga_opciones_ef = new toba_carga_opciones_ef($this, $efs, $parametros_efs);
		$this->_carga_opciones_ef->registrar_cascadas();
	}

	/**
	 * Devuelve un arreglo de ids de columnas
	 * @return array
	 */
	function get_ids_columnas()
	{
		if (isset($this->_columnas)) {
			return array_keys($this->_columnas);
		}
		return array();
	}

	protected function existe_columna($id)
	{
		$ids_actuales = $this->get_ids_columnas();
		return (in_array($id, $ids_actuales));
	}

	/**
	 * Elimina una o varias columnas del filtro, las mismas no se enviaran al cliente ni participaran
	 *  del formado de las clausulas
	 * @param array $ids_columnas Arreglo de identificadores de columnas a eliminar
	 */
	function eliminar_columnas($ids_columnas = array())
	{
		foreach($ids_columnas as $id) {
			if (! isset($this->_columnas[$id])) {
				toba::logger()->error("Se intento eliminar la colunma $id pero esta no existe");
				throw new toba_error('Se intenta eliminar una columna del filtro que no existe');
			}
			//Si todo va bien elimino la columna y ademas quito el EF de las cascadas para que no quede el maestro pegado.
			$this->_carga_opciones_ef->quitar_ef($id);
			unset($this->_columnas[$id]);
		}
		$this->_carga_opciones_ef->registrar_cascadas();
	}

	/**
	 *  Se aplican las restricciones funcionales necesarias a cada columna.
	 * @ignore
	 */
	function aplicar_restricciones_funcionales()
	{
		parent::aplicar_restricciones_funcionales();

		//-- Restricción funcional columnas no-visibles ------
		$no_visibles = toba::perfil_funcional()->get_rf_filtro_cols_no_visibles($this->_id[1]);
		if (! empty($no_visibles)) {
			foreach ($this->_columnas as $id => $columna) {
				if (in_array($columna->get_id_metadato(), $no_visibles)) {
					unset($this->_columnas[$id]);
				}
			}
		}
		//----------------

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
		//$this->_log->debug( $this->get_txt() . " disparar_eventos", 'toba');
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
		$this->borrar_memoria_eventos_atendidos();
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
			foreach ($this->_columnas as $id => $columna) {
				if (in_array($id, $filas_post)) {
					$this->_columnas[$id]->resetear_estado();
					$this->_columnas[$id]->cargar_estado_post();
					$validacion = $this->_columnas[$id]->validar_estado();
					if ($validacion !== true) {
						$etiqueta = $this->_columnas[$id]->get_etiqueta();
						throw new toba_error_validacion($etiqueta.': '.$validacion, $this->_columnas[$id]);
					}
					$this->_columnas_datos[$id] = $this->_columnas[$id];
				} else {
					if ($columna->es_obligatorio()) {
						throw new toba_error_validacion("La columna $id es obligatoria");
					}
				}
			}
		}
		return true;
	}

	/**
	 * Obtiene los datos del filtro
	 * @return array
	 */
	function get_datos()
	{
		$datos = array();
		if (isset($this->_columnas_datos)) {
			foreach ($this->_columnas_datos as $fila => $columna) {
				$datos[$fila] = $columna->get_estado();
			}
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
		$this->_columnas_datos = array();
		if (isset($datos)){
			foreach ($this->_columnas as $id => $columna) {
				$columna->resetear_estado();
				if (isset($datos[$id])) {
					$columna->set_estado($datos[$id]);
					$columna->set_visible(true);
					$this->_columnas_datos[$id] = $columna;
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
	 * Retorna la clausula a incluir en el where de una sql, basada en el estado actual del filtro o las condiciones que se le pasen
	 * @param string $separador Separador a utilizar para separar las clausulas
	 * @param array $clausulas Clausulas a utilizar, por defecto se toman las del estado actual del filtro
	 */
	function get_sql_where($separador = 'AND', $clausulas=null)
	{
		if (! isset($clausulas)) {
			$clausulas = $this->get_sql_clausulas();
		}
		if (! empty($clausulas)) {
			return "\t\t".implode("\n\t$separador\t", $clausulas);
		} else {
			return '1=1';
		}
	}

	/**
	 * Devuelve un arreglo de clausulas SQL basado en los valores de las columnas del filtro
	 * @return array
	 */
	function get_sql_clausulas()
	{
		$where = array();
		if (isset($this->_columnas_datos)) {
			foreach ($this->_columnas_datos as $columna) {
				if ($columna->tiene_estado()) {
					$where[$columna->get_nombre()] = $columna->get_sql_where();
				}
			}
		}
		return $where;		
	}

	/**
	 * Retorna la referencia a un objeto columna perteneciente al filtro
	 * @return toba_filtro_columna
	 */
	function columna($nombre)
	{
		if (! isset($this->_columnas[$nombre])) {
			toba::logger()->error("Se intento acceder la colunma $nombre pero esta no existe");
			throw new toba_error('Se intenta acceder una columna del filtro que no existe');
		}
		return $this->_columnas[$nombre];
	}
	
	
	//-------------------------------------------------------------------------------
	//----------------------------	  SALIDA	  -----------------------------------
	//-------------------------------------------------------------------------------
		
	function generar_html()
	{
		//Genero la interface
		echo "\n\n<!-- ***************** Inicio EI filtro (	".	$this->_id[1] ." )	***********	-->\n\n";
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
		$this->generar_html_barra_sup(null, true,"ei-filtro-barra-sup");
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
		$this->_carga_opciones_ef->cargar();		
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
		echo "<div class='ei-cuerpo ei-filtro-base' id='cuerpo_{$this->objeto_js}' style='$estilo'>";
		$this->generar_layout($ancho);
		echo "\n</div>";
	}	

	/**
	 * Genera los componentes que conforman la disposicion del filtro en pantalla
	 * @param string $ancho
	 */
	protected function generar_layout($ancho)
	{
		//Botonera de agregar y ordenar
		echo "<table id='{$this->objeto_js}_grilla' class='ei-filtro-grilla' style='width: $ancho' >\n";
		$this->generar_formulario_encabezado();
		$this->generar_formulario_cuerpo();
		echo "\n</table>";
		if ($this->botonera_abajo()) {
			$this->generar_botones();
		}
	}
		
	/**
	 * Genera el HTML de la botonera de agregar/quitar/ordenar filas
	 */
	protected function get_botonera_manejo_filas()
	{
		$salida = '';
		$salida = "<div class='ei-filtro-botonera' id='botonera_{$this->objeto_js}'>";
		$texto = toba_recurso::imagen_toba('nucleo/agregar.gif', true);
		$opciones = array(apex_ef_no_seteado => '');
		foreach ($this->_columnas as $columna) {
			$opciones[$columna->get_nombre()] = $columna->get_etiqueta();
		}
		$salida .= 'Agregar filtro ';
		$onchange = "onchange='{$this->objeto_js}.crear_fila()'";
		$salida .= toba_form::select("{$this->objeto_js}_nuevo", null, $opciones, 'ef-combo', $onchange);
		$salida .="</div>\n";
		return $salida;
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
			echo "<th class='ei-filtro-columna' $colspan>\n";
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
		$estilo_celda = "ei-filtro-fila";
		foreach ($this->_columnas as $nombre_col => $columna) {
			$this->analizar_visualizacion_columna ($columna);			
			if ($columna->es_visible()) {
				$estilo_fila = "";
			} else {
				$estilo_fila = "style='display:none;'";
			}
			echo "\n<!-- FILA $nombre_col -->\n\n";			
			echo "<tr $estilo_fila id='{$this->objeto_js}_fila$nombre_col' onclick='{$this->objeto_js}.seleccionar(\"$nombre_col\")'>";
			echo "<td class='$estilo_celda ei-filtro-col'>";
			echo $this->generar_vinculo_editor($nombre_col);
			echo $columna->get_html_etiqueta();
			echo "</td>\n";
			
			//-- Condición
			echo "<td class='$estilo_celda ei-filtro-cond'>";
			echo $columna->get_html_condicion();
			echo "</td>\n";
			
			//-- Valor			
			echo "<td class='$estilo_celda ei-filtro-valor'>";
			$columna->get_html_valor();
			echo "</td>\n";

			//-- Borrar a nivel de fila
			echo "<td class='$estilo_celda ei-filtro-borrar'>";
			//Si es obligatoria no se puede borrar
			if (!$columna->es_solo_lectura() && !$columna->es_obligatorio()) {
				echo toba_form::button_html("{$this->objeto_js}_eliminar$nombre_col", toba_recurso::imagen_toba('borrar.gif', true), 
									"onclick='{$this->objeto_js}.seleccionar(\"$nombre_col\");{$this->objeto_js}.eliminar_seleccionada();'", 
									$this->_rango_tabs[0]++, null, 'Elimina la fila');
			} else {
				echo '&nbsp;';
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</tbody>\n";		
	}	
	
	/**
	 * Genera la botonera del componente
	 * @param string $clase Clase css con el que se muestra la botonera
	 */
	function generar_botones($clase = '', $extra='')
	{
		$extra .= $this->get_botonera_manejo_filas();			//Lo coloco aca porque sino debo redefinir toda la ventana superior

		//----------- Generacion
		if ($this->hay_botones()) {
			echo "<div class='ei-botonera $clase'>";
			echo $extra;
			$this->generar_botones_eventos();
			echo "</div>";
		} elseif ($extra != '') {
			echo $extra;
		}
	}

	/**
	 * @ignore 
	 */
	protected function generar_vinculo_editor($id_ef)
	{
		if (toba_editor::modo_prueba()) {
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->_id),
									'col' => $id_ef );
			$item_editor = '1000254';
			return toba_editor::get_vinculo_subcomponente($item_editor, $param_editor);			
		}
		return null;
	}	
	
	protected function analizar_visualizacion_columna($columna)
	{
		$cascadas_maestros = $this->_carga_opciones_ef->get_cascadas_maestros();		//Obtengo todos los maestros
		//Si alguno de los maestros es visible, la columna en si misma se vuelve visible inicialmente.
		foreach($cascadas_maestros[$columna->get_nombre()] as $maestro) {
			if (isset($this->_columnas[$maestro]) && $this->_columnas[$maestro]->es_visible()) {
					$this->_columnas[$columna->get_nombre()]->set_visible(true);				//Seteo como visible la columna
			}
		}
	}
	
	//-----------------------------------------------------------
	// Cascadas
	//-----------------------------------------------------------
	
	function servicio__cascadas_columnas()
	{
		require_once(toba_dir() . '/php/3ros/JSON.php');
		if (! isset($_GET['cascadas-col']) || ! isset($_GET['cascadas-maestros'])) {
			throw new toba_error_seguridad("Cascadas: Invocación incorrecta");
		}
		toba::memoria()->desactivar_reciclado();
		$id_columna = trim(toba::memoria()->get_parametro('cascadas-col'));		
		if (! $this->existe_columna($id_columna)) {
			throw new toba_error_seguridad($this->get_txt()." No existe la columna  '$id_columna'");
		}
		$maestros = array();
		$cascadas_maestros = $this->_carga_opciones_ef->get_cascadas_maestros();
		$ids_maestros = $cascadas_maestros[$id_columna];
		foreach (explode('-|-', toba::memoria()->get_parametro('cascadas-maestros')) as $par) {
			if (trim($par) != '') {
				$param = explode("-;-", trim($par));
				if (count($param) != 2) {
					throw new toba_error_seguridad("Cascadas: Cantidad incorrecta de parametros ($par).");
				}
				$id_col_maestro = $param[0];

				//--- Verifique que este entre los maestros y lo elimina de la lista
				if (!in_array($id_col_maestro, $ids_maestros)) {
					throw new toba_error_seguridad("Cascadas: El ef '$id_col_maestro' no esta entre los maestros de '$id_columna'");
				}
				array_borrar_valor($ids_maestros, $id_col_maestro);

				$campos =  $this->columna($id_col_maestro)->get_nombre(); 
				$valores = explode(apex_qs_separador, $param[1]);
				if (!is_array($campos)) {
					$maestros[$id_col_maestro] = $this->columna($id_col_maestro)->get_ef()->normalizar_parametro_cascada($param[1]);
				} else {
					//--- Manejo de claves múltiples
					if (count($valores) != count($campos)) {
						throw new toba_error("Cascadas: El ef $id_col_maestro maneja distinta cantidad de datos que los campos pasados");
					}
					$valores_clave = array();
					for ($i=0; $i < count($campos) ; $i++) {
						$valores_clave[$campos[$i]] = $valores[$i];
					}
					$maestros[$id_col_maestro] = $valores_clave;
				}
			}
		}
		//--- Recorro la lista de maestros para ver si falta alguno. Permite tener ocultos como maestros
		foreach ($ids_maestros as $id_col_maestro) {
			if (is_null($this->columna($id_col_maestro)->get_estado())) {
				throw new toba_error_seguridad("Cascadas: El ef maestro '$id_col_maestro' no tiene estado cargado");
			}
			$maestros[$id_col_maestro] = $this->columna($id_col_maestro)->get_estado();
		}
		toba::logger()->debug("Cascadas '$id_columna', Estado de los maestros: ".var_export($maestros, true));
		$valores = $this->_carga_opciones_ef->ejecutar_metodo_carga_ef($id_columna, $maestros);
		toba::logger()->debug("Cascadas '$id_columna', Respuesta: ".var_export($valores, true));

		//--Guarda los datos en sesion para que los controle a la vuelta PHP		
		$sesion = null;									//No hay claves para resguardar
		if (isset($valores) && is_array($valores)) {			//Si lo que se recupero es un arreglo de valores
			if ($this->columna($id_columna)->get_ef()->es_seleccionable()) {		//Si es un ef seleccionable
				$sesion = array_keys($valores);
			}/* else {									//No es seleccionable pero se envia clave / valor.. (aun no se chequea), ej: popup
				$sesion = current($valores);
			}*/
		}
		$this->columna($id_columna)->get_ef()->guardar_dato_sesion($sesion, true);

		//--- Se arma la respuesta en formato JSON
		$json = new Services_JSON();
		if (! is_null($sesion)) {
			$resultado = array();
			foreach($valores as $klave => $valor) {						//Lo transformo en recordset para mantener el ordenamiento en Chrome
				$resultado[] = array($klave, $valor);
			}
			echo $json->encode($resultado);
		} else {
			echo $json->encode($valores);
		}
	}
	
	/**
	 * Método que se utiliza en la respuesta del filtro del combo editable usando AJAX
	 */
	function servicio__filtrado_ef_ce()
	{
		require_once(toba_dir() . '/php/3ros/JSON.php');				
		if (! isset($_GET['filtrado-ce-ef']) || ! isset($_GET['filtrado-ce-valor'])) {
			throw new toba_error_seguridad("Filtrado de combo editable: Invocación incorrecta");	
		}
		toba::memoria()->desactivar_reciclado();
		$id_ef = trim(toba::memoria()->get_parametro('filtrado-ce-ef'));
		$filtro = trim(toba::memoria()->get_parametro('filtrado-ce-valor'));
		$fila_actual = trim(toba::memoria()->get_parametro('filtrado-ce-fila'));
		
		//--- Resuelve la cascada
		$maestros = array($id_ef => $filtro);
		$cascadas_maestros = $this->_carga_opciones_ef->get_cascadas_maestros();
		$ids_maestros = $cascadas_maestros[$id_ef];
		foreach (explode('-|-', toba::memoria()->get_parametro('cascadas-maestros')) as $par) {
			if (trim($par) != '') {
				$param = explode("-;-", trim($par));
				if (count($param) != 2) {
					throw new toba_error_seguridad("Filtrado de combo editable: Cantidad incorrecta de parametros ($par).");
				}
				$id_ef_maestro = $param[0];

				//--- Verifique que este entre los maestros y lo elimina de la lista
				if (!in_array($id_ef_maestro, $ids_maestros)) {
					throw new toba_error_seguridad("Filtrado de combo editable: El ef '$id_ef_maestro' no esta entre los maestros de '$id_ef'");
				}
				array_borrar_valor($ids_maestros, $id_ef_maestro);

				$campos = $this->columna($id_ef_maestro)->get_nombre();
				$valores = explode(apex_qs_separador, $param[1]);
				if (!is_array($campos)) {
					$maestros[$id_ef_maestro] = $this->columna($id_ef_maestro)->get_ef()->normalizar_parametro_cascada($param[1]);
				} else {
					//--- Manejo de claves múltiples
					if (count($valores) != count($campos)) {
						throw new toba_error_def("Filtrado de combo editable: El ef $id_ef_maestro maneja distinta cantidad de datos que los campos pasados");
					}
					$valores_clave = array();
					for ($i=0; $i < count($campos) ; $i++) {
						$valores_clave[$campos[$i]] = $valores[$i];
					}
					$maestros[$id_ef_maestro] = $valores_clave;
				}
			}
		}
		//--- Recorro la lista de maestros para ver si falta alguno. Permite tener ocultos como maestros
		foreach ($ids_maestros as $id_ef_maestro) {
			$this->columna($id_ef_maestro)->cargar_estado_post();
			if (! $this->columna($id_ef_maestro)->tiene_estado()) {
				throw new toba_error_seguridad("Filtrado de combo editable: El ef maestro '$id_ef_maestro' no tiene estado cargado");
			}
			$maestros[$id_ef_maestro] = $this->columna($id_ef_maestro)->get_estado();
		}

		
		toba::logger()->debug("Filtrado combo_editable '$id_ef', Cadena: '$filtro', Estado de los maestros: ".var_export($maestros, true));		
		$valores = $this->_carga_opciones_ef->ejecutar_metodo_carga_ef($id_ef, $maestros);
		toba::logger()->debug("Filtrado combo_editable '$id_ef', Respuesta: ".var_export($valores, true));				
		
		//--- Se arma la respuesta en formato JSON
		$json = new Services_JSON();
		if (is_array($valores)) {
			$resultado = array();
			foreach($valores as $klave => $valor) {						//Lo transformo en recordset para mantener el ordenamiento en Chrome
				$resultado[] = array($klave, $valor);
			}
			echo $json->encode($resultado);
		} else {
			echo $json->encode($valores);
		}
	}

	/**
	 * Método que se utiliza en la respuesta del filtro del combo editable cuando se quiere validar un id seleccionado
	 */
	function servicio__filtrado_ef_ce_validar()
	{
		require_once(toba_dir() . '/php/3ros/JSON.php');				
		if (! isset($_GET['filtrado-ce-ef']) || ! isset($_GET['filtrado-ce-valor'])) {
			throw new toba_error_seguridad("Validación de combo editable: Invocación incorrecta");	
		}
		$id_ef = trim(toba::memoria()->get_parametro('filtrado-ce-ef'));
		$valor = trim(toba::memoria()->get_parametro('filtrado-ce-valor'));
		//$fila_actual = trim(toba::memoria()->get_parametro('filtrado-ce-fila'));

		$descripcion = $this->_carga_opciones_ef->ejecutar_metodo_carga_descripcion_ef($id_ef, $valor);
		$estado = array($valor => $descripcion);
		
		//--- Se arma la respuesta en formato JSON
		$json = new Services_JSON();
		echo $json->encode($estado);
	}	

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	protected function crear_objeto_js()
	{
		$efs_esclavos = $this->_carga_opciones_ef->get_cascadas_esclavos();
		$identado = toba_js::instancia()->identado();
		$id = toba_js::arreglo($this->_id, false);
		$esclavos = toba_js::arreglo($this->_carga_opciones_ef->get_cascadas_esclavos(), true, false);
		$maestros = toba_js::arreglo($this->_carga_opciones_ef->get_cascadas_maestros(), true, false);
		echo $identado."window.{$this->objeto_js} = new ei_filtro($id, '{$this->objeto_js}', '{$this->_submit}', $maestros, $esclavos);\n";
		foreach ($this->_columnas as $columna) {
			$visible = $columna->es_visible() ? 'true' : 'false';
			$compuesto = $columna->es_compuesto() ? 'true' : 'false';
			echo $identado."{$this->objeto_js}.agregar_ef({$columna->crear_objeto_js()}, '{$columna->get_nombre()}', $visible, $compuesto);\n";
		}

		//Ciclo por los eventos para definir el comportamiento que lance el predeterminado
		foreach (array_keys($this->_eventos_usuario_utilizados) as $id_evento) {
			if ($this->evento($id_evento)->es_predeterminado()) {
				$excluidos = array();
				foreach ($this->_columnas as $columna) {		//Aca tengo que ciclar por las columnas
					if ($columna->es_solo_lectura()) {
						$excluidos[] = $columna->get_ef()->get_id();
					}
				}
				$excluidos = toba_js::arreglo($excluidos);
				echo $identado."{$this->objeto_js}.set_procesar_cambios(true, '$id_evento', $excluidos);\n";
			}
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
		$consumo[] = 'componentes/ei_filtro';
		//Busco las	dependencias
		foreach ($this->_columnas as $columna){
			$temp	= $columna->get_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo = array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}	
	
	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------
		
	function vista_impresion_html( toba_impresion $salida )
	{
		$this->_carga_opciones_ef->cargar();
		$ancho = isset($this->_info_filtro["ancho"]) ? $this->_info_filtro["ancho"] : "auto";
		$salida->subtitulo( $this->get_titulo() );
		$this->generar_layout_impresion($ancho);
	}	

	/**
	 * Genera los componentes que se visualizaran en la vista impresion
	 * @param string $ancho
	 */
	protected function generar_layout_impresion($ancho)
	{
		echo "<table class='ei-filtro-grilla' width='$ancho'>";
		$this->generar_encabezado_impresion();
		$this->generar_cuerpo_impresion();
		echo "\n</table>";		
	}

	/**
	 * @ignore
	 */
	protected function generar_encabezado_impresion()
	{
		echo "<thead>\n <tr>\n";
		echo "<th class='imp-mensaje ei-form-etiq'>\n";
		echo "<strong> Búsqueda </strong>";
		echo "</th>\n";
		echo "</tr>\n";
		echo "</thead>\n";
	}

	/**
	 * @ignore
	 */
	protected function generar_cuerpo_impresion()
	{
		echo "<tbody>";			
		$estilo_celda = "ei-filtro-fila";
		foreach ($this->_columnas as $nombre_col => $columna) {
			if (! $columna->es_visible()) {
				continue;
			} 
			
			$estado_col = $columna->get_estado();
			if (!$columna->get_ef()->tiene_estado()){
				continue;
			}
			
			echo "\n<!-- FILA $nombre_col -->\n\n";			
			echo "<tr >";
			echo "<td class='$estilo_celda ei-filtro-col'>";			
			echo $columna->get_ef()->get_etiqueta();
			echo "</td>\n";
			
			//-- Condición
			echo "<td class='$estilo_celda ei-filtro-cond'>";			
			if (! is_null($estado_col)){				
			 	echo $columna->condicion()->get_etiqueta();
			}
			echo "</td>\n";

			//-- Valor			
			$fn_formateo = $columna->get_formateo();
			if (! is_null($fn_formateo)){
				$formateo = new $this->_clase_formateo('impresion_html');				
				$funcion = "formato_" . $fn_formateo;
				$valor_real = $columna->get_ef()->get_estado();
				$valor = $formateo->$funcion($valor_real);				
			}else{
				$valor = $columna->get_ef()->get_descripcion_estado('impresion_html');
			}			
			
			echo "<td class='$estilo_celda ei-filtro-valor'>";
			echo $valor;
			echo "</td>\n";
			echo "</tr>\n";
		}
		echo "</tbody>\n";		
	}
		
	//---------------------------------------------------------------
	//----------------------  SALIDA PDF  ---------------------------
	//---------------------------------------------------------------
		
	/**
	 * Permite setear el ancho del formulario.
	 * @param unknown_type $ancho Es posible pasarle valores enteros o porcentajes (por ejemplo 85%).
	 */
	function set_pdf_tabla_ancho($ancho)
	{
		$this->_pdf_tabla_ancho = $ancho;
	}
	
	/**
	 * Permite setear el tamaño de la tabla que representa el formulario.
	 * @param integer $tamanio Tamaño de la letra.
	 */
	function set_pdf_letra_tabla($tamanio)
	{
		$this->_pdf_letra_tabla = $tamanio;
	}
	
	/**
	 * Permite setear el estilo que llevara la tabla en la salida pdf.
	 * @param array $opciones Arreglo asociativo con las opciones para la tabla de salida.
	 * @see toba_vista_pdf::tabla, ezpdf::ezTable
	 */
	function set_pdf_tabla_opciones($opciones)
	{
		$this->_pdf_tabla_opciones = $opciones;
	}
	
	function vista_pdf( $salida )
	{
		$this->_carga_opciones_ef->cargar();		
		$formateo = new $this->_clase_formateo('pdf');
		$datos = array();
		$datos['datos_tabla'] = array();
		foreach ( $this->_columnas as $columna ){
			if (!$columna->es_visible()){
				continue;
			}
				        
			if ($columna->get_ef()->tiene_estado()) {
				$etiqueta = $columna->get_ef()->get_etiqueta();
				$condicion = $columna->condicion()->get_etiqueta();
								
				$fn_formateo = $columna->get_formateo();
				if (! is_null($fn_formateo)){
					$funcion = "formato_" . $fn_formateo;
                	$valor_real = $columna->get_ef()->get_estado();
                	$valor = $formateo->$funcion($valor_real);
				}else{
					$valor = $columna->get_ef()->get_descripcion_estado('pdf');
				}
				$datos['datos_tabla'][] = array('Columna' => $etiqueta, 'Condicion' => $condicion, 'Valor' => $valor);
			}
		}
		//-- Genera la tabla
        $ancho = null;
        if (strpos($this->_pdf_tabla_ancho, '%') !== false) {
        	$ancho = $salida->get_ancho(str_replace('%', '', $this->_pdf_tabla_ancho));	
        } elseif (isset($this->_pdf_tabla_ancho)) {
        		$ancho = $this->_pdf_tabla_ancho;
        }
        $opciones = $this->_pdf_tabla_opciones;
        if (isset($ancho)) {
        	$opciones['width'] = $ancho;		
        }        
		$datos['titulo_tabla'] =  $this->get_titulo();
		$salida->tabla($datos, false, $this->_pdf_letra_tabla, $opciones);
	}

	//---------------------------------------------------------------
	//----------------------  SALIDA EXCEL --------------------------
	//---------------------------------------------------------------
		
	function vista_excel(toba_vista_excel $salida)
	{
		$this->_carga_opciones_ef->cargar();		
		$formateo = new $this->_clase_formateo('excel');
		$datos = array();
		foreach ( $this->_columnas as $columna ){
			if (!$columna->es_visible()){
				continue;
			}
			
			if ($columna->get_ef()->tiene_estado()) {
				$opciones = array();
				$etiqueta = $columna->get_ef()->get_etiqueta();			
				//Hay que formatear?
				$estilo = array();
				$fn_formateo = $columna->get_formateo();
				if (! is_null($fn_formateo)){
					$funcion = "formato_" . $fn_formateo;
	                $valor_real = $columna->get_ef()->get_estado();
	                list($valor, $estilo) = $formateo->$funcion($valor_real);
				}else{
					list($valor, $estilo) = $columna->get_ef()->get_descripcion_estado('excel');
				}
				
				$condicion = $columna->condicion()->get_etiqueta();				
				if (isset($estilo)) {
					$opciones['valor']['estilo'] = $estilo;
				}	
				$opciones['etiqueta']['estilo']['font']['bold'] = true;
				$opciones['etiqueta']['ancho'] = 'auto';
				$opciones['condicion']['ancho'] = 'auto';
				$opciones['valor']['ancho'] = 'auto';				
				$datos = array(array('etiqueta' => $etiqueta, 'condicion' => $condicion, 'valor' => $valor));
				$salida->tabla($datos, array(), $opciones);
			}
		}		
	}
	
	//---------------------------------------------------------------
	//----------------------  API BASICA ----------------------------
	//---------------------------------------------------------------

	/**
	 * Cambia la forma en que se le da formato a un ef en las salidas pdf, excel y html
	 * @param string $id_ef
	 * @param string $funcion Nombre de la función de formateo, sin el prefijo 'formato_'
	 * @param string $clase Nombre de la clase que contiene la funcion, por defecto toba_formateo
	 */
	function set_formateo_ef($id_ef, $funcion, $clase=null)
	{
		$columna = $this->_columnas[$id_ef];
		$columna->set_formateo($funcion);
		if (isset($clase)) {
			$this->_clase_formateo = $clase;
		}
	}	
	
	//---------------------------------------------------------------
	//------------------------- SALIDA XML --------------------------
	//---------------------------------------------------------------
	/**
	 * Genera el xml del componente
	 * @param boolean $inicial Si es el primer elemento llamado desde vista_xml
	 * @param string $xmlns Namespace para el componente
	 * @return string XML del componente
	 */
	function vista_xml($inicial=false, $xmlns=null)
	{
		if ($xmlns) {
			$this->xml_set_ns($xmlns);
		}
		$this->_carga_opciones_ef->cargar();		
		$formateo = new $this->_clase_formateo('xml');
		$tmpxml = null;
		foreach ( $this->_columnas as $columna ){
			if (!$columna->es_visible()){
				continue;
			}
				        
			if ($columna->get_ef()->tiene_estado()) {
				$etiqueta = $columna->get_ef()->get_etiqueta();
				$condicion = $columna->condicion()->get_etiqueta();
								
				$fn_formateo = $columna->get_formateo();
				if (! is_null($fn_formateo)){
					$funcion = "formato_" . $fn_formateo;
                	$valor_real = $columna->get_ef()->get_estado();
                	$valor = $formateo->$funcion($valor_real);
				}else{
					$valor = $columna->get_ef()->get_descripcion_estado('xml');
				}
				$tmpxml .= '<'.$this->xml_ns.'fila><'.$this->xml_ns.'dato valor="'.$etiqueta.'"/><'.$this->xml_ns.'dato valor="'.$condicion.'"/><'.$this->xml_ns.'dato valor="'.$valor.'"/></'.$this->xml_ns.'fila>';
			}
		}
		if($tmpxml) {
			$xml = '<'.$this->xml_ns.'tabla'.$this->xml_ns_url;
			if (trim($this->_info["titulo"])=="" && (!isset($this->xml_titulo) || $this->xml_titulo == '')) {
				$this->xml_set_titulo('Filtro');
			} 
			$xml .= $this->xml_get_att_comunes();
			$xml .= '>';
			$xml .= $this->xml_get_elem_comunes();
			$xml .= '<'.$this->xml_ns.'datos><'.$this->xml_ns.'col titulo="Columna"/><'.$this->xml_ns.'col titulo="Condición"/><'.$this->xml_ns.'col titulo="Valor"/>'.$tmpxml.'</'.$this->xml_ns.'datos></'.$this->xml_ns.'tabla>';
			return $xml;
		}
	}
}
?>
