<?php
require_once("toba_ei_formulario.php");

/**
 * Un formulario multilínea (ei_formulario_ml) presenta una grilla de campos repetidos una cantidad dada de filas permitiendo recrear la carga de distintos registros con la misma estructura. 
 * La definición y uso de la grilla de campos es similar al formulario simple con el agregado de lógica para manejar un número arbitrario de filas.
 * 
 * Como el formulario ML tiene la posibilidad de agregar nuevas filas completamente en el cliente, brinda un servicio que permite analizar lo acontecido con las filas enviadas al cliente.
 * Así si por ejemplo se envían 3 filas y el cliente modifica dos, la otra la borra y agrega una nueva el método de análisis evita que el programador tenga que comparar el estado de las filas enviadas con el recibido.
 * 
 * @package Componentes
 * @subpackage Eis
 */
class toba_ei_formulario_ml extends toba_ei_formulario
{
	protected $datos;
	protected $lista_ef_totales = array();
	protected $clave_seleccionada;				//Id de la fila seleccionada
	protected $siguiente_id_fila;				//Autoincremental que se usa para crear filas en la interface y asegurar que sean unicas
	protected $filas_recibidas;					//Lista de filas recibidas desde el ci
	protected $analizar_diferencias=false;		//¿Se analizan las diferencias entre lo enviado - recibido y se adjunta el resultado?
	protected $eventos_granulares=false;		//¿Se lanzan eventos a-b-m o uno solo modificacion?
	protected $ordenes = array();				//Ordenes de las claves de los datos recibidos
	protected $registro_nuevo=false;			//¿La proxima pantalla muestra una linea en blanco?
	protected $id_fila_actual;					//¿Que fila se esta procesando actualmente?
	protected $item_editor = '/admin/objetos_toba/editores/ei_formulario_ml';
	
	function __construct($id)
	{
		parent::__construct($id);
		$this->siguiente_id_fila = isset($this->memoria['siguiente_id_fila']) ? $this->memoria['siguiente_id_fila'] : 156;
		$this->filas_recibidas = isset($this->memoria['filas_recibidas']) ? $this->memoria['filas_recibidas'] : array();
	}

	function destruir()
	{
		$this->memoria['siguiente_id_fila'] = $this->siguiente_id_fila;
		$this->memoria['filas_recibidas'] = $this->filas_recibidas;
		parent::destruir();
	}	
		
	protected function inicializar_especifico()
	{
		//Se incluyen los totales
		for($a=0;$a<count($this->info_formulario_ef);$a++)
		{
			if($this->info_formulario_ef[$a]["total"]){
				$this->lista_ef_totales[] = $this->info_formulario_ef[$a]["identificador"];
			}
		}
		//Se determina el metodo de analisis de cambios
		$this->set_metodo_analisis($this->info_formulario['analisis_cambios']);
	}

	/**
	 * Cambia o desactiva el método de análisis del formulario.
	 * 
	 * Existen dos métodos de análisis:
	 * - En línea con los registros: incluye una columna apex_ei_analisis_fila a cada registro indicando si la fila es nueva (A), si es modificada (M) o si fue borrada (B)
	 * - A través de eventos: se dispara un evento por cambio (ej. evt__ml__registro_alta($id, $datos))
	 *
	 * @param string $metodo Puede ser (literal): LINEA, EVENTOS, o false (sin analisis)
	 */
	function set_metodo_analisis($metodo)
	{
		switch ($metodo) {
			case 'LINEA':
				$this->analizar_diferencias = true;
				$this->eventos_granulares = false;				
				break;
			case 'EVENTOS':
				$this->analizar_diferencias = true;
				$this->eventos_granulares = true;
				break;
			default:
				$this->analizar_diferencias = false;
				$this->eventos_granulares = false;
		}	
	}
	
	/**
	 * Deja al formulario sin selección alguna de fila
	 */
	function deseleccionar()
	{
		unset($this->clave_seleccionada);
	}

	/**
	*	Indica al formulario cual es la clave seleccionada. 
	*	A la hora de mostrar la grilla se crea un feedback gráfico sobre la fila que posea esta clave
	*	@param string $clave Identificador de la clave de la fila a seleccionar
	*/	
	function seleccionar($clave)
	{
		$this->clave_seleccionada = $clave;
	}
	

	/**
	 * No permite que el usuario pueda agregar nuevas filas en el cliente
	 */
	function desactivar_agregado_filas()
	{
		$this->info_formulario['filas_agregar'] = false;
	}
	

	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------

	function disparar_eventos()
	{
		//Veo si se devolvio algun evento!
		if (isset($_POST[$this->submit]) && $_POST[$this->submit]!=""){
			//La opcion seleccionada estaba entre las ofrecidas?		
			if (isset($this->memoria['eventos'][$_POST[$this->submit]]) ) {
				//--- Caso particular: Manejo de 2 eventos (uno implicito)
				$implicito = null;
				if (isset($_POST[$this->submit.'_implicito']) && 
							$_POST[$this->submit].'_implicito' !=""){
					$evt = $_POST[$this->submit.'_implicito'];
					if (isset($this->memoria['eventos'][$evt])) {	
						$implicito = $evt;
					}
				}
				$this->disparar_eventos_especifico($_POST[$this->submit], $implicito);
			}
		}
		$this->limpiar_interface();
	}	
	
	protected function disparar_eventos_especifico($evento, $implicito=null)
	{
		$maneja_datos = ($this->memoria['eventos'][$evento] == apex_ei_evt_maneja_datos);
		$parametros = isset($_POST[$this->objeto_js."__parametros"]) ? $_POST[$this->objeto_js."__parametros"] : '';
		
		//--- Si el evento maneja datos, se validan y cargan
		if ($maneja_datos) {
			$this->cargar_post();
			$this->validar_estado();
		}
		//--- Caso particular, manejo de un evento implicito que se dispara junto a uno principal
		if ($implicito) {
			$this->reportar_evento( $implicito, $this->get_datos($this->analizar_diferencias) );
		}		
		//--- ¿Se lanzan los eventos granulares (registro_alta, baja y modificacion) ?
		if ($this->eventos_granulares && $maneja_datos) {
			$this->disparar_eventos_granulares();
		}
		
		//--- Se reporta el pedido de nuevo registro, si no se atrapa se asume SI		
		if (! $this->info_formulario['filas_agregar_online'] && $evento == 'pedido_registro_nuevo') {
			if ($this->reportar_evento("pedido_registro_nuevo", null) === apex_ei_evt_sin_rpta) {
				$this->set_registro_nuevo();
			}
		//--- Si Tiene parametros, es uno a nivel de fila			
		} else if ($parametros != '') {
			//Reporto el evento a nivel de fila
			$this->clave_seleccionada = $this->get_clave_fila($parametros);
			$this->reportar_evento( $evento, $this->clave_seleccionada);
		//-- Si no tiene es un evento comun			
		} else {
			$this->reportar_evento( $evento, $this->get_datos($this->analizar_diferencias));
		}
	}
		
	protected function disparar_eventos_granulares()
	{
		$this->validar_estado();
		$datos = $this->get_datos(true);
		foreach ($datos as $fila => $dato) {
			$analisis = $dato[apex_ei_analisis_fila];
			unset($dato[apex_ei_analisis_fila]);			
			switch ($analisis)
			{
				case 'A': 
					$this->reportar_evento( 'registro_alta', $dato, $fila);
					break;
				case 'M':
					$this->reportar_evento( 'registro_modificacion', $dato, $fila);
					break;				
				case 'B':
					$this->reportar_evento( 'registro_baja', $fila );
					break;			
			}
		}	
	}

	/**
	 * Crea la cantidad de filas vacías definidas en el editor
	 */
	function carga_inicial()
	{
		$this->datos = array();
		if ($this->info_formulario["filas"] > 0 ) {
			for ($i = 0; $i < $this->info_formulario["filas"]; $i++) {
				$this->agregar_registro();
			}
		}
	}
		
	/**
	 * Carga en $this->datos los valores recibidos del POST
	 */
	protected function cargar_post()
	{
		if (! isset($_POST[$this->objeto_js.'_listafilas']))
			return false;

		$this->datos = array();			
		$lista_filas = $_POST[$this->objeto_js.'_listafilas'];
		$filas_post = array();
		if ($lista_filas != '') {
			$filas_post = explode('_', $lista_filas);
			//Por cada fila
			foreach ($filas_post as $fila) {
				//1) Cargo los EFs
				foreach ($this->lista_ef as $ef){
					$this->elemento_formulario[$ef]->ir_a_fila($fila);
					$this->elemento_formulario[$ef]->resetear_estado();
					$this->elemento_formulario[$ef]->cargar_estado_post();
					//La validación del estado no se hace aquí porque interrumpiría la carga
				}
				//2) Seteo el registro
				$this->cargar_ef_a_registro($fila);
			}
		}
		return true;
	}

	/**
	 * Recorre todos los datos del formulario, cargandolos en los efs y validando estos el estado
	 * 
	 * @throws toba_error_validacion En caso de que la validación de algún ef falle
	 * @todo Esta validación se podría hacer más eficiente en el cargar_post, pero se prefiere acá por si se cambia el manejo actual
	 * 		de validaciones. Por ejemplo ahora se están desechando los cambios que origina el error y por lo tanto no se pueden
	 * 		ver las modificaciones hechas, sería deseable poder verlos.
	 */
	function validar_estado()
	{
		foreach ($this->datos as $id_fila => $datos_registro) {
			$this->cargar_registro_a_ef($id_fila, $datos_registro);
			foreach ($this->lista_ef_post as $ef){
				$this->elemento_formulario[$ef]->ir_a_fila($id_fila);
				$validacion = $this->elemento_formulario[$ef]->validar_estado();
				if ($validacion !== true) {
					$this->efs_invalidos[$id_fila][$ef] = $validacion;
					$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta();
					throw new toba_error_validacion($etiqueta.': '.$validacion, $this->ef($ef));
				}
			}
		}
	} 	

	/**
	 * Borra los datos actuales y resetea el estado de los efs
	 */	
	function limpiar_interface()
	{
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->resetear_estado();
		}
		unset($this->datos);
		unset($this->ordenes);
	}

	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	---------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna la cantidad de filas, registros o líneas que el formulario tiene cargados
	 * @return integer
	 */
	function get_cantidad_lineas()
	{
		return count($this->datos);
	}

	/**
	 * Retorna el set de datos que maneja actualmente el componente
	 * Si se llama en la etapa de eventos contiene los datos recibidos del POST
	 * Si se llama en la etapa de servicio contiene los datos cargados con set_datos
	 * @param boolean $analizar_diferencias Debe utilizar algún metodo de analisis para compararlos con los datos del pedido anterior?
	 * @return array Formato recordset, la clave de la fila se envia como clave asociativa y como columna. Ej. array(clave_fila=> array(apex_datos_clave_fila =>clave_fila, columna=>valor, ...), ..)
	 */
	function get_datos($analizar_diferencias = false)
	{
		//Envia el ordenamiento como una columna aparte
		$orden = 1;
		foreach (array_keys($this->datos) as $id) {
			if (isset($this->info_formulario['columna_orden'])) {
				$this->datos[$id][$this->info_formulario['columna_orden']] = $orden;
			}
			$this->datos[$id][apex_datos_clave_fila] = $id;
			$orden++;
		}
		if ($analizar_diferencias) {
			//Analizo la procedencia del registro: es alta o modificación
			$datos = $this->datos;
			foreach (array_keys($datos) as $id_fila) {
				//Si la fila que viene desde el POST estaba entra las recibidas del CI en el request anterior
				//es una fila modificada, sino para el CI es una nueva 
				if (in_array($id_fila, $this->filas_recibidas)) {
					$datos[$id_fila][apex_ei_analisis_fila] = 'M';
				} else {
					$datos[$id_fila][apex_ei_analisis_fila] = 'A';
				}
			}
			
			//Se buscan los registros borrados
			foreach ($this->filas_recibidas as $recibida) {
				//Si la recibida en el request anterior no vino junto a los datos se borro
				if (! in_array($recibida, array_keys($datos))) {
					$datos[$recibida] = array(apex_ei_analisis_fila => 'B');
				}
			}
		} else {	//Hay que sacar la información extra
			$datos = array_values($this->datos);
		}
		return $datos;
	}

	/*
	*	Retorna la posicion en el arreglo de datos donde se ubica un id interno de fila
	*   Esta posicion puede ser el mismo id interno en caso de que las diferencias se analizen online
	*   o puede ser el posicionamiento simple si no hay analisis
	*/
	protected function get_clave_fila($fila)
	{
		if ($this->analizar_diferencias) {
			if (isset($this->datos[$fila]))
				return $fila;
		} else {
			if (isset($this->datos)) {
				$i = 0;
				foreach (array_keys($this->datos) as $id_fila) {
					if ($fila == $id_fila)
						return $i;
					$i++;
				}
			}
			return $fila;
		}
		
	}
	
	function pre_configurar()
	{
		parent::pre_configurar();
		$this->filas_recibidas = array();
	}
	
	/**
	 * Carga el formulario con un conjunto de datos.
	 * Si el formulario tiene definido un ordenamiento, aqui es donde se lleva a cabo
	 *
	 * @param array $datos Formato recordset, cada registro puede enviar su clave como clave asociativa o como columna apex_datos_clave_fila,
	 * 						también se puede especificar una columna conteniendo el orden del registro (cual columna usar se define en el editor)
	 */
	function set_datos($datos)
	{
		if (!is_array($datos)) {
			throw new toba_error_def( $this->get_txt() . 
					" El parametro para cargar el cuadro posee un formato incorrecto:" .
						"Se esperaba un arreglo de dos dimensiones con formato recordset.");
		}		
		
		$this->filas_recibidas = array();
		$this->datos = array();
		foreach ($datos as $id => $fila) {
			//--- Se determina la clave real de la fila
			if (isset($fila[apex_datos_clave_fila])) {
				$id = $fila[apex_datos_clave_fila];
			}
			//--- Se actualiza la secuencia autoincremental
			if (is_numeric($id) && $id >= $this->siguiente_id_fila) {
				$this->siguiente_id_fila = $id + 1;
			}
			$this->datos[$id] = $fila;
			
			//Para dar un analisis preciso de la accion del ML, es necesario discriminar cuales
			//filas son a dar de alta y cuales son a modificar
			if (! isset($fila[apex_ei_analisis_fila]) || $fila[apex_ei_analisis_fila] != 'A') {
				$this->filas_recibidas[] = $id;
			}
		}

		//---Ordenar por la columna que se establece
		//El ML no ordena el arreglo, porque esto cambiaria las claves asociativas
		//por eso mantiene la variable $this->ordenes
		if ($this->info_formulario['columna_orden']) {
			$ordenes = array();
			foreach ($this->datos as $id => $dato) {
				$ordenes[$id] = $dato[$this->info_formulario['columna_orden']];
			}
			asort($ordenes);
			$this->ordenes = array_keys($ordenes);
		} else {
			$this->ordenes = array_keys($this->datos);
		}
	}

	/**
	* Agrega un registro nuevo a la matriz de datos
	* 
    * La diferencia entre este método y agregar una fila vacía en el set_datos es que en este último
	* los registros en el próximo pedido de página serán analizados como 'modificados' ya que no sabe diferenciarlos
	* de los datos que ya existen en el medio de almacenamiento.
	* 
	* Para cumplir con su objetivo este metodo tiene que se invocado en la etapa de configuración cuando
	* ya se ha cargado al componente con datos, de lo contrario se perdera su efecto.
	* @see set_registro_nuevo, set_datos
	*/
	function agregar_registro($valores=array())
	{	
		$this->datos[$this->siguiente_id_fila] = $valores;
		$this->ordenes[] = $this->siguiente_id_fila;
		$this->siguiente_id_fila++;
	}
	
	/**
	 * Inserta un registro nuevo en la proxima generación de HTML. 
	 * Solo permite agregar un único registro, llamadas consecutivas a este método sólo variarán el contenido de la nueva fila
	 * La diferencia con agregar_registro es que este último no puede ser invocado antes del set_datos (configuración o carga), 
	 * set_registro_nuevo deja una marca interna que fuerza la creación de una nueva fila independientemente de la carga de datos.
	 * @param array $template Valores por defecto de la nueva fila, false si se quiere cancelar el alta del registro
	 * @see agregar_registro
	 */
	function set_registro_nuevo($template=array())
	{
		$this->registro_nuevo = $template;
	}	

	/**
	 * Cambia la clave o id a utilizar para la siguiente fila creada en este formulario
	 * 
	 * Como el formulario crea la fila antes que existan en el medio de almacenamiento (típicamente una base de datos)
	 * necesita brindar a la fila un identificador únivoco, entonces maneja internamente una secuencia, con este método
	 * es posible modificar el valor que toma el siguiente número de esa secuencia.
	 * @param integer $id
	 */
	function set_proximo_id($id)
	{
		$this->siguiente_id_fila = $id;	
	}


	/**
	 * El formulario posee datos?
	 * @return boolean
	 */
	function datos_cargados()
	{
		if(isset($this->datos)){
			return count($this->datos) > 0;
		}else{
			return false;
		}
	}

	//-------------------------------------------------------------------------------
	//------------------------  Multiplexacion de EFs  ------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Carga los datos de una fila específica a partir de los valores de los efs de esa fila
	 */
	protected function cargar_ef_a_registro($id_registro)
	{
		$this->id_fila_actual = $id_registro;
		foreach ($this->lista_ef as $ef)
		{
			//Aplano el estado del EF en un array
			$dato	= $this->elemento_formulario[$ef]->get_dato();
			$estado = $this->elemento_formulario[$ef]->get_estado();
			if (is_array($dato)) {	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					echo ei_mensaje("Error de consistencia	interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->get_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$this->datos[$id_registro][$dato[$x]]	= $estado[$dato[$x]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$this->datos[$id_registro][$dato] = $estado;
			}
		}
	}

	/**
	 * Carga los efs en base a los datos de una fila específica
	 */
	protected function cargar_registro_a_ef($id_fila, $datos_registro)
	{
		$this->id_fila_actual = $id_fila;
		$datos = $datos_registro;
		foreach ($this->lista_ef as $ef) {
			//Seteo el ID-formulario del EF para que referencie al registro actual
			$this->elemento_formulario[$ef]->ir_a_fila($id_fila);
			$this->elemento_formulario[$ef]->resetear_estado();
			$dato = $this->elemento_formulario[$ef]->get_dato();
			if(is_array($dato)){	//El EF maneja	 *** DATO COMPUESTO
				$temp = array();
				for($x=0;$x<count($dato);$x++){
					if(isset($datos[$dato[$x]])){
						$temp[$dato[$x]]=stripslashes($datos[$dato[$x]]);
					}else{
						$temp[$dato[$x]] = null;
					}
				}
			} else {					//El EF maneja	un	*** DATO SIMPLE
				if (isset($datos[$dato])) {
					if (!is_array($datos[$dato])) {
						$temp = stripslashes($datos[$dato]);
					} elseif (is_array($datos[$dato])) {
						//--- Caso para multi-seleccion
						$temp = array();
						foreach ($datos[$dato] as $string) {
							$temp[] = stripslashes($string);
						}
					}
				} else {
					$temp = null;	
				}
			}
			if ($temp !== null) {
				$this->elemento_formulario[$ef]->set_estado($temp);
			}
		}
	}

	//-------------------------------------------------------------------------------
	//----------------------------	  SALIDA	  -----------------------------------
	//-------------------------------------------------------------------------------

	protected function generar_formulario()
	{
		$this->rango_tabs = manejador_tabs::instancia()->reservar(1000);		
		//--- Si no se cargaron datos, se cargan ahora
		if (!isset($this->datos)) {		
			$this->carga_inicial();
		}
		
		//Ancho y Scroll
		$estilo = '';
		$ancho = isset($this->info_formulario["ancho"]) ? $this->info_formulario["ancho"] : "auto";
		if($this->info_formulario["scroll"]){
			$alto_maximo = isset($this->info_formulario["alto"]) ? $this->info_formulario["alto"] : "auto";
			if ($ancho != 'auto' || $alto_maximo != 'auto') {
				$estilo .= "overflow: auto; width: $ancho; height: $alto_maximo; border: 1px inset; margin: 0px; padding: 0px;";
			} 
		}else{
			$alto_maximo = "auto";
		}		
		if (isset($this->colapsado) && $this->colapsado) {
			$estilo .= "display:none;";
		}
		echo "<div class='ei-cuerpo ei-ml' id='cuerpo_{$this->objeto_js}' style='$estilo'>";
		//Botonera de agregar y ordenar
		$this->generar_botonera_manejo_filas();

		//Defino la cantidad de columnas
		$colspan = count($this->lista_ef_post);
		if ($this->info_formulario['filas_numerar']) {
			$colspan++;
		}			
		//Campo de comunicacion con JS
		echo toba_form::hidden("{$this->objeto_js}_listafilas",'');
		echo toba_form::hidden("{$this->objeto_js}__parametros", '');
		
		echo "<table class='ei-ml-grilla' style='width: $ancho' >\n";
		echo "<thead>\n";
		$this->generar_formulario_encabezado();
		echo "</thead>\n";
		echo "<tfoot>\n";
		$this->generar_formulario_pie($colspan);
		echo "</tfoot>\n";	
		echo "<tbody>";		
		$this->generar_formulario_cuerpo();
		echo "</tbody>\n";		
		echo "\n</table>";
		echo "\n</div>";
	}
	
	protected function generar_formulario_encabezado()
	{
		//------ TITULOS -----	
		echo "<tr>\n";
		if ($this->info_formulario['filas_numerar']) {
			echo "<th class='ei-ml-columna'>&nbsp;</th>\n";
		}
		foreach ($this->lista_ef_post	as	$ef){
			echo "<th class='ei-ml-columna'>\n";
			$this->generar_etiqueta_ef($ef);
			echo "</th>\n";
		}
        //-- Eventos sobre fila
		if($this->cant_eventos_sobre_fila() > 0){
			foreach ($this->get_eventos_sobre_fila() as $evento) {
				echo "<th class='ei-ml-columna'>&nbsp;\n";
				if (toba_editor::modo_prueba()) {
					echo toba_editor::get_vinculo_evento($this->id, $this->info['clase_editor_item'], $evento->get_id())."\n";
				}
	            echo "</th>\n";
			}
		}		
		echo "</tr>\n";
	}
	
	protected function generar_etiqueta_ef($ef)
	{
		$estilo = $this->elemento_formulario[$ef]->get_estilo_etiqueta();
		if ($estilo == '') {
	        if ($this->elemento_formulario[$ef]->es_obligatorio()) {
	    	        $estilo = 'ei-ml-etiq-oblig';
					$marca = '(*)';
        	} else {
	            $estilo = 'ei-ml-etiq';
				$marca ='';
    	    }
		}
		$desc = $this->elemento_formulario[$ef]->get_descripcion();
		if ($desc !=""){
			$desc = toba_recurso::imagen_toba("descripcion.gif",true,null,null,$desc);
		}
		$id_ef = $this->elemento_formulario[$ef]->get_id_form();			
		$editor = $this->generar_vinculo_editor($ef);
		$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta().$marca;
		echo "<span class='$estilo'>$etiqueta $editor $desc</span>\n";
	}	
	
	protected function generar_formulario_pie($colspan)
	{
		//------ Totales y Eventos------
		echo "\n<!-- TOTALES -->\n";
		if(count($this->lista_ef_totales)>0){
			echo "\n<tr  class='ei-ml-fila-total'>\n";
			if ($this->info_formulario['filas_numerar']) {
				echo "<td>&nbsp;</td>\n";
			}
			foreach ($this->lista_ef_post as $ef){
				$this->elemento_formulario[$ef]->ir_a_fila("s");
				$id_form_total = $this->elemento_formulario[$ef]->get_id_form();
				echo "<td id='$id_form_total'>&nbsp;\n";
				echo "</td>\n";
			}
	        //-- Eventos sobre fila
			$cant_sobre_fila = $this->cant_eventos_sobre_fila();
			if($cant_sobre_fila > 0){
				echo "<td colspan='$cant_sobre_fila'>\n";
	            echo "</td>\n";
			}
			echo "</tr>\n";
		}		
		echo "<tr><td colspan='$colspan'>\n";
		$this->generar_botones();
		echo "</td></tr>\n";
	}
	
	protected function generar_formulario_cuerpo()
	{
		if ($this->registro_nuevo !== false) {
			$template = (is_array($this->registro_nuevo)) ? $this->registro_nuevo : array();
			$this->agregar_registro($template);
		}
		//------ FILAS ------
		$this->filas_enviadas = array();
		if (!isset($this->ordenes)) {
			$this->ordenes = array();
		}
		//Se recorre una fila más para insertar una nueva fila 'modelo' para agregar en js
		if ( $this->info_formulario['filas_agregar'] && $this->info_formulario['filas_agregar_online']) {
			$this->datos["__fila__"] = array();
			$this->ordenes[] = "__fila__";
		}
		$a = 0;
		foreach ($this->ordenes as $fila) {
			$dato = $this->datos[$fila];
			//Si la fila es el template ocultarla
			if ($fila !== "__fila__") {
				$this->filas_enviadas[] = $fila;
				$estilo = "";
			} else {
				$estilo = "style='display:none;'";
			}
			//Determinar el estilo de la fila
			if (isset($this->clave_seleccionada) && $fila == $this->clave_seleccionada) {
				$estilo_fila = "ei-ml-fila-selec";				
			} else {
				$estilo_fila = "ei-ml-fila";
			}
			$this->cargar_registro_a_ef($fila, $dato);
			
			//--- Se cargan las opciones de los efs de esta fila
			$this->cargar_opciones_efs();			
			
			//Aca va el codigo que modifica el estado de cada EF segun los datos...
			echo "\n<!-- FILA $fila -->\n\n";
			echo "<tr $estilo id='{$this->objeto_js}_fila$fila' onClick='{$this->objeto_js}.seleccionar($fila)'>";
			if ($this->info_formulario['filas_numerar']) {
				echo "<td class='$estilo_fila'>\n<span id='{$this->objeto_js}_numerofila$fila'>".($a + 1);
				echo "</span></td>\n";
			}
			foreach ($this->lista_ef_post as $ef){
				$this->elemento_formulario[$ef]->ir_a_fila($fila);
				$id_form = $this->elemento_formulario[$ef]->get_id_form();
				echo "<td  class='$estilo_fila' id='cont_$id_form'>\n";
				echo $this->elemento_formulario[$ef]->get_input();
				echo "</td>\n";
			}
 			//---> Creo los EVENTOS de la FILA <---
 			
			foreach ($this->get_eventos_sobre_fila() as $id => $evento) {
				echo "<td class='$estilo_fila'>\n";
				if( ! $evento->esta_anulado() ) { //Si el evento viene desactivado de la conf, no lo utilizo
					//1: Posiciono al evento en la fila
					$evento->set_parametros($fila);
					if($evento->posee_accion_vincular()){
						$evento->vinculo()->set_parametros($fila);	
					}
					//2: Ventana de modificacion del evento por fila
					//- a - ¿Existe una callback de modificacion en el CONTROLADOR?
					$callback_modificacion_eventos_contenedor = 'conf_evt__' . $this->parametros['id'] . '__' . $id;
					if (method_exists($this->controlador, $callback_modificacion_eventos_contenedor)) {
						$this->controlador->$callback_modificacion_eventos_contenedor($evento, $fila);
					} else {
						//- b - ¿Existe una callback de modificacion una subclase?
						$callback_modificacion_eventos = 'conf_evt__' . $id;
						if (method_exists($this, $callback_modificacion_eventos)) {
							$this->$callback_modificacion_eventos($evento, $fila);
						}
					}
					//3: Genero el boton
					if( ! $evento->esta_anulado() ) {
						echo $evento->get_html($this->submit, $this->objeto_js);
					} else {
						$evento->restituir();	//Lo activo para la proxima fila
					}
				}
            	echo "</td>\n";
			}
			//----------------------------			
			echo "</tr>\n";
			$a++;
		}
	}

	/**
	 * Genera el HTML de la botonera de agregar/quitar/ordenar filas
	 */
	protected function generar_botonera_manejo_filas()
	{
		$agregar = $this->info_formulario['filas_agregar'];
		$ordenar = $this->info_formulario['filas_ordenar'];
		if ($agregar || $ordenar) {
			echo "<div style='text-align: left'>";
			if ($agregar) {
				echo toba_form::button_html("{$this->objeto_js}_agregar", toba_recurso::imagen_toba('ml/agregar.gif', true), 
										"onclick='{$this->objeto_js}.crear_fila();'", $this->rango_tabs[0]++, '+', 'Crea una nueva fila');
				echo toba_form::button_html("{$this->objeto_js}_eliminar", toba_recurso::imagen_toba('ml/borrar.gif', true), 
										"onclick='{$this->objeto_js}.eliminar_seleccionada();' disabled", $this->rango_tabs[0]++, '-', 'Elimina la fila seleccionada');
				$html = toba_recurso::imagen_toba('ml/deshacer.gif', true)."<span id='{$this->objeto_js}_deshacer_cant'  style='font-size: 8px;'></span>";
				echo toba_form::button_html("{$this->objeto_js}_deshacer", $html, 
										" onclick='{$this->objeto_js}.deshacer();' disabled", $this->rango_tabs[0]++, 'z', 'Deshace la última eliminación');
				echo "&nbsp;";
			}
			if ($ordenar) {
				echo toba_form::button_html("{$this->objeto_js}_subir", toba_recurso::imagen_toba('ml/subir.gif', true), 
										"onclick='{$this->objeto_js}.subir_seleccionada();' disabled", $this->rango_tabs[0]++, '<', 'Sube una posición la fila seleccionada');
				echo toba_form::button_html("{$this->objeto_js}_bajar", toba_recurso::imagen_toba('ml/bajar.gif', true),
                                        "onclick='{$this->objeto_js}.bajar_seleccionada();' disabled", $this->rango_tabs[0]++, '>', 'Baja una posición la fila seleccionada');
			}
			echo "</div>\n";
		}
	}

	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -------------------------------
	//-------------------------------------------------------------------------------

	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		if (! $this->info_formulario['filas_agregar_online']) {
			$this->eventos['pedido_registro_nuevo'] = array('maneja_datos' => true);
		}
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		//Creación de los objetos javascript de los objetos
		$rango_tabs = "new Array({$this->rango_tabs[0]}, {$this->rango_tabs[1]})";
		$filas = toba_js::arreglo($this->filas_enviadas);
		$en_linea = toba_js::bool($this->info_formulario['filas_agregar_online']);
		$seleccionada = (isset($this->clave_seleccionada)) ? $this->clave_seleccionada : "null";
		$esclavos = toba_js::arreglo($this->cascadas_esclavos, true, false);
		$maestros = toba_js::arreglo($this->cascadas_maestros, true, false);		
		$id = toba_js::arreglo($this->id, false);
		$invalidos = toba_js::arreglo($this->efs_invalidos, true);
		echo $identado."window.{$this->objeto_js} = new ei_formulario_ml";
		echo "($id, '{$this->objeto_js}', $rango_tabs, '{$this->submit}', $filas, {$this->siguiente_id_fila}, $seleccionada, $en_linea, $maestros, $esclavos, $invalidos);\n";
		foreach ($this->lista_ef_post as $ef) {
			echo $identado."{$this->objeto_js}.agregar_ef({$this->elemento_formulario[$ef]->crear_objeto_js()}, '$ef');\n";
		}
		//Agregado de callbacks para calculo de totales
		if(count($this->lista_ef_totales)>0) {
			foreach ($this->lista_ef_post as $ef) {
				if(in_array($ef, $this->lista_ef_totales)) {
					echo $identado."{$this->objeto_js}.agregar_total('$ef');\n";
				}
			}
		}
	}
	
	function get_objeto_js_ef($id)
	{
		return "{$this->objeto_js}.ef('$id').ir_a_fila('{$this->id_fila_actual}')";
	}
	
	function get_consumo_javascript()
	{
		$consumos = parent::get_consumo_javascript();
		$consumos[] = 'componentes/ei_formulario_ml';
		return $consumos;
	}

	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------
		
	function vista_impresion_html( $salida )
	{
		$salida->subtitulo( $this->get_titulo() );
		$ancho = isset($this->info_formulario["ancho"]) ? $this->info_formulario["ancho"] : "auto";
		echo "<table class='tabla-0' style='width: $ancho'>\n";
		//-- Encabezado
		echo "<tr>\n";
		if ($this->info_formulario['filas_numerar']) {
			echo "<th class='ei-ml-col-tit'>&nbsp;</th>\n";
		}
		foreach ($this->lista_ef_post	as	$ef){
			echo "<th class='ei-cuadro-col-tit'>\n";
			echo $this->elemento_formulario[$ef]->get_etiqueta();
			echo "</th>\n";
		}
		echo "</tr>\n";
		//-- Cuerpo
		$a = 0;
		if( isset( $this->ordenes ) ) {
			foreach ($this->ordenes as $fila) {
				$dato = $this->datos[$fila];
				$this->cargar_registro_a_ef($fila, $dato);
				$this->cargar_opciones_efs();
				echo "<tr class='col-tex-p1'>";
				if ($this->info_formulario['filas_numerar']) {
					echo "<td class='col-tex-p1'>\n".($a + 1)."</td>\n";
				}
				foreach ($this->lista_ef_post as $ef){
					$this->elemento_formulario[$ef]->ir_a_fila($fila);
					$temp = $this->get_valor_imprimible_ef( $ef );
					echo "</td><td class='". $temp['css'] ."'>\n";
					echo $temp['valor'];
					echo "</td>\n";
				}
				echo "</tr>\n";
				$a++;
			}
		}
		echo "\n</table>\n";
	}
}
?>