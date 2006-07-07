<?
require_once("objeto_ei_formulario.php");	//Ancestro de todos los	OE

/**
 * Un formulario multilínea (ei_formulario_ml) presenta una grilla de campos repetidos una cantidad dada de filas permitiendo recrear la carga de distintos registros con la misma estructura. 
 * La definición y uso de la grilla de campos es similar al formulario simple con el agregado de lógica para manejar un número arbitrario de filas.
 * @package Objetos
 * @subpackage Ei
 */
class objeto_ei_formulario_ml extends objeto_ei_formulario
{
	protected $datos;
	protected $lista_ef_totales = array();
	protected $clave_seleccionada;				//Id de la fila seleccionada
	protected $siguiente_id_fila;				//Autoincremental que se va a asociar al ef que identifica una fila
	protected $filas_recibidas;					//Lista de filas recibidas desde el ci
	protected $analizar_diferencias=false;		//¿Se analizan las diferencias entre lo enviado - recibido y se adjunta el resultado?
	protected $eventos_granulares=false;		//¿Se lanzan eventos a-b-m o uno solo modificacion?
	protected $ordenes = array();				//Ordenes de las claves de los datos recibidos
	protected $hay_registro_nuevo=false;		//¿La proxima pantalla muestra una linea en blanco?
	protected $id_fila_actual;					//¿Que fila se esta procesando actualmente?
	protected $item_editor = '/admin/objetos_toba/editores/ei_formulario_ml';
	
	function __construct($id)
/*
	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::__construct($id);
		$this->rango_tabs = manejador_tabs::instancia()->reservar(5000);
		$this->siguiente_id_fila = isset($this->memoria['siguiente_id_fila']) ? $this->memoria['siguiente_id_fila'] : 156000;
		$this->filas_recibidas = isset($this->memoria['filas_recibidas']) ? $this->memoria['filas_recibidas'] : array();
	}

	function destruir()
	{
		$this->memoria['siguiente_id_fila'] = $this->siguiente_id_fila;
		$this->memoria['filas_recibidas'] = $this->filas_recibidas;
		parent::destruir();
	}	
		
	function inicializar_especifico()
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

	function set_metodo_analisis($metodo)
	{
		switch ($metodo)
		{
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
	
	function deseleccionar()
	{
		unset($this->clave_seleccionada);
	}

	function desactivar_agregado_filas()
	{
		$this->info_formulario['filas_agregar'] = false;
	}
	
	function seleccionar($clave)
	{
		$this->clave_seleccionada = $clave;
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
			
				$this->disparar_eventos_especifico($_POST[$this->submit]);
			}
		} else {	//Es la primera carga
			$this->carga_inicial();
		}
		$this->limpiar_interface();
	}	
	
	protected function disparar_eventos_especifico($evento)
	{
		$maneja_datos = $this->memoria['eventos'][$evento];
		$parametros = isset($_POST[$this->objeto_js."__parametros"]) ? $_POST[$this->objeto_js."__parametros"] : '';
		
		//Me fijo si el evento envia datos modificados
		if ($maneja_datos) {
			$this->cargar_post();
			$this->validar_estado();
		}
		
		//Si agregar no es online y viene un pedido de agregar, si hay o no registro nuevo y su forma se preguntan al ci
		//En caso que no responda se asume que si y es vacio
		//Para no complicar con el resto de la logica se sale del metodo
		if (! $this->info_formulario['filas_agregar_online'] && $evento == 'pedido_registro_nuevo') {
			//¿Se lanzan los eventos granulares (registro_alta, baja y modificacion)?
			if ($this->eventos_granulares && $maneja_datos) {
				$this->disparar_eventos_granulares();
			} else {
				$this->reportar_evento( 'modificacion', $this->get_datos($this->analizar_diferencias) );				
			}
			$this->hay_registro_nuevo = $this->reportar_evento( $evento, null );
			return;
		}

		//¿Se lanzan los eventos granulares (registro_alta, baja y modificacion) ?
		if ($this->eventos_granulares && $maneja_datos) {
			$this->disparar_eventos_granulares();
		}
		
		//Si Tiene parametros, es uno a nivel de fila
		if ($parametros != '') {
			//Si maneja datos, disparar una modificacion antes del evento a nivel de fila
			if ($maneja_datos && !$this->eventos_granulares) {
				$this->reportar_evento( 'modificacion', $this->get_datos($this->analizar_diferencias) );
			}
			//Reporto el evento a nivel de fila
			$this->clave_seleccionada = $this->get_clave_fila($parametros);
			$this->reportar_evento( $evento, $this->clave_seleccionada);
		}
		
		//Si no tiene parametros particulares, ellos son los valores de las filas
		if ($parametros == '' && !$this->eventos_granulares) {
			if ($maneja_datos)
				$this->reportar_evento( $evento, $this->get_datos($this->analizar_diferencias) );
			elseif ($evento != 'pedido_registro_nuevo')
				$this->reportar_evento( $evento, null );
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

	function carga_inicial()
/*
	@@acceso: interno
	@@desc: Carga los datos a partir de la definición
*/
	{
		if (!isset($this->datos) || $this->datos === null) {
			$this->datos = array();
			if ($this->info_formulario["filas"] > 0 ) {
				for ($i = 0; $i < $this->info_formulario["filas"]; $i++) {
					//A cada fila se le brinda un id único
					$this->datos[$this->siguiente_id_fila] = array();
					$this->siguiente_id_fila++;	
				}
			}
		}
	}
		
	function cargar_post()
	{
		if (! isset($_POST[$this->objeto_js.'_listafilas']))
			return false;

		$this->datos = array();			
		$lista_filas = $_POST[$this->objeto_js.'_listafilas'];
		$filas_post = array();
		if ($lista_filas != '') {
			$filas_post = explode('_', $lista_filas);
			//Por cada fila
			foreach ($filas_post as $fila)
			{
				if ($fila >= $this->siguiente_id_fila)
					$this->siguiente_id_fila = $fila + 1;
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

	function validar_estado()
	{
		//Esta validación se podría hacer más eficiente en el cargar_post, pero se prefiere acá por si se cambia el manejo actual
		//de validaciones. Por ejemplo ahora se están desechando los cambios que origina el error y por lo tanto no se pueden
		//ver las modificaciones hechas, sería deseable poder verlos.
		foreach ($this->datos as $id_fila => $datos_registro) {
			$this->cargar_registro_a_ef($id_fila, $datos_registro);
			foreach ($this->lista_ef_post as $ef){
				$this->elemento_formulario[$ef]->ir_a_fila($id_fila);
				$validacion = $this->elemento_formulario[$ef]->validar_estado();
				if ($validacion !== true) {
					$this->efs_invalidos[$id_fila][$ef] = $validacion;
					$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta();
					throw new excepcion_toba_validacion($etiqueta.': '.$validacion, $this->ef($ef));
				}
			}
		}
	} 	
	
	function limpiar_interface()
	{
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->resetear_estado();
		}
		unset($this->datos);
		unset($this->ordenes);
	}

	function cargar_estado_ef($array_ef)
	{
		throw new excepcion_toba("No esta implementado en el multilínea");
		//ATENCION: En un multilinea esto es distinto. FALTA
	}

	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	---------------------------------
	//-------------------------------------------------------------------------------

	function get_cantidad_lineas()
	{
		return count($this->datos);
	}
	
	function get_datos($analizar_diferencias = false)
	{
		//Envia el ordenamiento como una columna aparte
		if ($this->info_formulario['columna_orden']) {
			$orden = 1;
			foreach (array_keys($this->datos) as $id) {
				$this->datos[$id][$this->info_formulario['columna_orden']] = $orden;
				$orden++;
			}
		}
		if ($analizar_diferencias) {
			//Analizo la procedencia del registro: es alta o modificación
			$datos = $this->datos;
			foreach (array_keys($datos) as $id_fila) {
				//Si la fila que viene desde el POST estaba entra las recibidas del CI en el request anterior
				//es una fila modificada, sino para el CI es una nueva 
				if (in_array($id_fila, $this->filas_recibidas))
					$datos[$id_fila][apex_ei_analisis_fila] = 'M';
				else
					$datos[$id_fila][apex_ei_analisis_fila] = 'A';
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
	

	function cargar_datos($datos = null)
	{
		if ($datos !== null) {
			//Para dar un analisis preciso de la accion del ML, es necesario discriminar cuales
			//filas son a dar de alta y cuales son a modificar
			$this->filas_recibidas = array();
			foreach ($datos as $id => $fila) {
				if (! isset($fila[apex_ei_analisis_fila]) || $fila[apex_ei_analisis_fila] != 'A') {
					$this->filas_recibidas[] = $id;
				}
			}
			$this->datos = $datos;
		} else {
			$this->filas_recibidas = array();
			$this->carga_inicial();
		}
		//Ordenar por la columna que se establece
		if ($this->datos != null) {
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
		} else {
			$this->ordenes = array();	
		}
	}

	/**
	*	Agrega un registro nuevo a la matriz
	*/
	protected function agregar_registro_nuevo()
	{	
		$template = (is_array($this->hay_registro_nuevo)) ? $this->hay_registro_nuevo : array();
		$this->datos[$this->siguiente_id_fila] = $template;
		$this->ordenes[] = $this->siguiente_id_fila;
		$this->siguiente_id_fila++;
	}

	function existen_datos_cargados()
	{
		if(isset($this->datos)){
			if(count($this->datos) > 0){
				//ei_arbol($this->datos,"DATOS");
				return true;
			}else{
				return false;
			}
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
	function cargar_ef_a_registro($id_registro)
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
		//ei_arbol($this->datos,"CArga de registros");
	}

	/**
	 * Carga los efs en base a los datos de una fila específica
	 */
	function cargar_registro_a_ef($id_fila, $datos_registro)
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
					} elseif (is_array($datos[$dato])) { //ATENCION: Este es el caso para el multi-seleccion, hay que mejorarlo
						$temp = array();
						foreach ($datos[$dato] as $string) {
							$temp[] = stripslashes($string);
						}
					}
				} else {
					$temp = null;	
				}
			}
			if ($temp !== null)
				$this->elemento_formulario[$ef]->set_estado($temp);
		}
	}

	//-------------------------------------------------------------------------------
	//----------------------------	  SALIDA	  -----------------------------------
	//-------------------------------------------------------------------------------

	function generar_formulario()
	{
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
		$this->botonera_manejo_filas();

		//Defino la cantidad de columnas
		$colspan = count($this->lista_ef_post);
		if ($this->info_formulario['filas_numerar']) {
			$colspan++;
		}			
		//Campo de comunicacion con JS
		echo form::hidden("{$this->objeto_js}_listafilas",'');
		echo form::hidden("{$this->objeto_js}__parametros", '');
		
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
	
	function generar_formulario_encabezado()
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
		$cant_sobre_fila = $this->cant_eventos_sobre_fila();
		if($cant_sobre_fila > 0){
			echo "<th class='ei-ml-columna' colspan='$cant_sobre_fila'>\n";
            echo "</th>\n";
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
			$desc = recurso::imagen_apl("descripcion.gif",true,null,null,$desc);
		}
		$id_ef = $this->elemento_formulario[$ef]->get_id_form();			
		$editor = $this->generar_vinculo_editor($ef);
		$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta().$marca;
		echo "<span class='$estilo'>$etiqueta $editor $desc</span>\n";
	}	
	
	function generar_formulario_pie($colspan)
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
				echo "<td id='$id_form_total'>&nbsp\n";
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
	
	function generar_formulario_cuerpo()
	{
		if ($this->hay_registro_nuevo !== false) {
			$this->agregar_registro_nuevo();
		}
		//------ FILAS ------
		$this->filas_enviadas = array();

		//Se recorre una fila más para insertar una nueva fila 'modelo' para agregar en js
		if ( $this->info_formulario['filas_agregar_online']) {
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
			foreach ($this->eventos as $id => $evento) {
				if ($evento['sobre_fila']) {
					//Filtrado de eventos por fila
					$metodo_filtro = 'filtrar_evt__' . $id;
					if(method_exists($this, $metodo_filtro)){
						if(! $this->$metodo_filtro ) 
							continue;
					}
					//HTML del EVENTO
					$tip = '';
					if (isset($evento['ayuda']))
						$tip = $evento['ayuda'];
					$clase = ( isset($evento['estilo']) && (trim( $evento['estilo'] ) != "")) ? $evento['estilo'] : 'ei-boton-fila';
					$tab_order = null;
					$acceso = tecla_acceso( $evento["etiqueta"] );
					$html = '';
					if (isset($evento['imagen_recurso_origen']) && $evento['imagen']) {
						if (isset($evento['imagen_recurso_origen']))
							$img = recurso::imagen_de_origen($evento['imagen'], $evento['imagen_recurso_origen']);
						else
							$img = $evento['imagen'];
						$html = recurso::imagen($img, null, null, null, null, null, 'vertical-align: middle;').' ';
					}
					$html .= $acceso[0];
					$tecla = $acceso[1];
					//Creo JS del EVENTO
					$evento_js = eventos::a_javascript($id, $evento, $fila);
					$js = "onclick=\"{$this->objeto_js}.set_evento($evento_js);\"";
					echo "<td class='$estilo_fila'>\n";
					echo form::button_html( $this->submit."_".$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase, false);
	            	echo "</td>\n";
				}	
			}
			//----------------------------			
			echo "</tr>\n";
			$a++;
		}
	}

	function botonera_manejo_filas()
	{
		$agregar = $this->info_formulario['filas_agregar'];
		$ordenar = $this->info_formulario['filas_ordenar'];
		if ($agregar || $ordenar) {
			echo "<div style='text-align: left'>";
			$tab = ($this->rango_tabs[1] - 10);
			if ($agregar) {
				echo form::button_html("{$this->objeto_js}_agregar", recurso::imagen_apl('ml/agregar.gif', true), 
										"onclick='{$this->objeto_js}.crear_fila();'", $tab++, '+', 'Crea una nueva fila');
				echo form::button_html("{$this->objeto_js}_eliminar", recurso::imagen_apl('ml/borrar.gif', true), 
										"onclick='{$this->objeto_js}.eliminar_seleccionada();' disabled", $tab++, '-', 'Elimina la fila seleccionada');
				$html = recurso::imagen_apl('ml/deshacer.gif', true)."<span id='{$this->objeto_js}_deshacer_cant'  style='font-size: 8px;'></span>";
				echo form::button_html("{$this->objeto_js}_deshacer", $html, 
										" onclick='{$this->objeto_js}.deshacer();' disabled", $tab++, 'z', 'Deshace la última eliminación');
				echo "&nbsp;";
			}
			if ($ordenar) {
				echo form::button_html("{$this->objeto_js}_subir", recurso::imagen_apl('ml/subir.gif', true), 
										"onclick='{$this->objeto_js}.subir_seleccionada();' disabled", $tab++, '<', 'Sube una posición la fila seleccionada');
				echo form::button_html("{$this->objeto_js}_bajar", recurso::imagen_apl('ml/bajar.gif', true),
                                        "onclick='{$this->objeto_js}.bajar_seleccionada();' disabled", $tab++, '>', 'Baja una posición la fila seleccionada');
			}
			echo "</div>\n";
		}
	}

	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -------------------------------
	//-------------------------------------------------------------------------------

	function get_lista_eventos()
	/*
		Los eventos standard estan relacionados con el consumo del formulario en un ABM
	*/
	{
		$eventos = parent::get_lista_eventos();
		if (! $this->info_formulario['filas_agregar_online']) {
			$eventos +=eventos::ml_registro_nuevo();
		}
		return $eventos;
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		//Creación de los objetos javascript de los objetos
		$rango_tabs = "new Array({$this->rango_tabs[0]}, {$this->rango_tabs[1]})";
		$filas = js::arreglo($this->filas_enviadas);
		$en_linea = js::bool($this->info_formulario['filas_agregar_online']);
		$seleccionada = (isset($this->clave_seleccionada)) ? $this->clave_seleccionada : "null";
		$esclavos = js::arreglo($this->cascadas_esclavos, true, false);
		$maestros = js::arreglo($this->cascadas_maestros, true, false);		
		$id = js::arreglo($this->id, false);
		$invalidos = js::arreglo($this->efs_invalidos, true);
		echo $identado."window.{$this->objeto_js} = new objeto_ei_formulario_ml";
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
		$consumos[] = 'clases/objeto_ei_formulario_ml';
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