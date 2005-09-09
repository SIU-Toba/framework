<?
require_once("objeto_ei_formulario.php");	//Ancestro de todos los	OE
require_once("nucleo/browser/interface/ef.php");//	Elementos de interface

class	objeto_ei_formulario_ml	extends objeto_ei_formulario
/*
	@@acceso: actividad
	@@desc: Esta clase contruye la Interface Grafica de un registro de una tabla

	Un formulario tiene que saber que si viene del post, para dejar cargase datos o no???
	Cual es la solucion para esta competencia??
*/
{
	protected $datos;
	protected $lista_ef_totales = array();
	protected $clave_seleccionada;					//Id de la fila seleccionada
	protected $siguiente_id_fila;				//Autoincremental que se va a asociar al ef que identifica una fila
	protected $filas_recibidas;					//Lista de filas recibidas desde el ci
	protected $analizar_diferencias=false;		//¿Se analizan las diferencias entre lo enviado - recibido y se adjunta el resultado?
	protected $eventos_granulares=false;		//¿Se lanzan eventos a-b-m o uno solo modificacion?
	protected $ordenes;							//Ordenes de las claves de los datos recibidos
	protected $hay_registro_nuevo=false;		//¿La proxima pantalla muestra una linea en blanco?

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
	//-------------------------------------------------------------------------------

	function elemento_toba()
	{
		require_once('api/elemento_objeto_ei_formulario_ml.php');
		return new elemento_objeto_ei_formulario_ml();
	}	
	
	function destruir()
	{
		$this->memoria['siguiente_id_fila'] = $this->siguiente_id_fila;
		$this->memoria['filas_recibidas'] = $this->filas_recibidas;
		parent::destruir();
	}	
	//-------------------------------------------------------------------------------
		
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
	//-------------------------------------------------------------------------------

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//Formulario
		$sql["info_formulario"]["sql"] = "SELECT	auto_reset as	auto_reset,
										scroll as 					scroll,					
										ancho as					ancho,
										alto as						alto,
										filas as					filas,
										filas_agregar as			filas_agregar,
										filas_agregar_online as 	filas_agregar_online,
										filas_ordenar as			filas_ordenar,
										filas_numerar as 			filas_numerar,
										columna_orden as 			columna_orden,
										analisis_cambios		as	analisis_cambios
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND		objeto_ut_formulario='".$this->id[1]."';";
		$sql["info_formulario"]["tipo"]="1";
		$sql["info_formulario"]["estricto"]="1";
		//EF
		$sql["info_formulario_ef"]["sql"] = "SELECT	identificador as identificador,
										columnas	as				columnas,
										obligatorio	as				obligatorio,
										elemento_formulario as		elemento_formulario,
										inicializacion	as			inicializacion,
										etiqueta	as				etiqueta,
										descripcion	as				descripcion,
										orden	as					orden,
										total as 					total,
										estilo as					columna_estilo,
										colapsado as 				colapsado
								FROM	apex_objeto_ei_formulario_ef
								WHERE	objeto_ei_formulario_proyecto='".$this->id[0]."'
								AND	objeto_ei_formulario='".$this->id[1]."'
								AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql["info_formulario_ef"]["tipo"]="x";
		$sql["info_formulario_ef"]["estricto"]="1";
		return $sql;
	}
	
//--------------------------------------------------------------------------	
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
	
//--------------------------------------------------------------------------
	function deseleccionar()
	{
		unset($this->clave_seleccionada);
	}

//--------------------------------------------------------------------------
	function seleccionar($clave)
	{
		$this->clave_seleccionada = $clave;
	}
		
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------	INFORMACION	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	info_estado_ef()
/*
	@@acceso: actividad
	@@desc: Muestra el estado de los	EF
*/
	{
		foreach ($this->lista_ef as $ef){
			$temp1[$ef]	= $this->elemento_formulario[$ef]->obtener_estado();
			$temp2[$ef]	= $this->elemento_formulario[$ef]->obtener_dato();
		}
		$temp["DATOS"]=$temp2;
		$temp["ESTADO"]=$temp1;
		//ei_arbol($temp,"Estado actual	de	los ELEMENTOS de FORMULARIO");
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------
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
				$this->reportar_evento( 'modificacion', $this->obtener_datos($this->analizar_diferencias) );				
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
				$this->reportar_evento( 'modificacion', $this->obtener_datos($this->analizar_diferencias) );
			}
			//Reporto el evento a nivel de fila
			$this->clave_seleccionada = $this->obtener_clave_fila($parametros);
			$this->reportar_evento( $evento, $this->clave_seleccionada);
		}
		
		//Si no tiene parametros particulares, ellos son los valores de las filas
		if ($parametros == '' && !$this->eventos_granulares) {
			if ($maneja_datos)
				$this->reportar_evento( $evento, $this->obtener_datos($this->analizar_diferencias) );
			elseif ($evento != 'pedido_registro_nuevo')
				$this->reportar_evento( $evento, null );
		}
		

	}
	//-------------------------------------------------------------------------------
		
	function disparar_eventos_granulares()
	{
		$this->validar_estado();
		$datos = $this->obtener_datos(true);
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

	//-------------------------------------------------------------------------------

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
	//-------------------------------------------------------------------------------
		
	function cargar_post()
	{
/*
	@@acceso: interno
	@@desc: Carga los datos a partir del POST
*/
		if (! isset($_POST[$this->objeto_js.'_listafilas']))
			return false;

		$this->datos = array();			
		$lista_filas = $_POST[$this->objeto_js.'_listafilas'];
		$filas_recibidas = array();
		if ($lista_filas != '') {
			$filas_recibidas = explode('_', $lista_filas);
			//Por cada fila
			foreach ($filas_recibidas as $fila)
			{
				if ($fila >= $this->siguiente_id_fila)
					$this->siguiente_id_fila = $fila + 1;
				//1) Cargo los EFs
				foreach ($this->lista_ef as $ef){
					$this->elemento_formulario[$ef]->establecer_id_form($fila);
					$this->elemento_formulario[$ef]->resetear_estado();
					$x	= $this->elemento_formulario[$ef]->cargar_estado();
					//La validación del estado no se hace aquí porque interrumpiría la carga
				}
				//2) Seteo el registro
				$this->cargar_ef_a_registro($fila);
			}
		}
		return true;
	}
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
	@@acceso: interno
	@@desc: Valida	cada fila
	@@pendiente: grados:	EJ	un	ef_oculto_proyecto no la deberia	dejar	pasar...
*/
	{
		//Esta validación se podría hacer más eficiente en el cargar_post, pero se prefiere acá por si se cambia el manejo actual
		//de validaciones. Por ejemplo ahora se están desechando los cambios que origina el error y por lo tanto no se pueden
		//ver las modificaciones hechas, sería deseable poder verlos.
		foreach ($this->datos as $id_fila => $datos_registro) {
			$this->cargar_registro_a_ef($id_fila, $datos_registro);
			foreach ($this->lista_ef as $ef){
				$this->elemento_formulario[$ef]->establecer_id_form($id_fila);
				$temp = $this->elemento_formulario[$ef]->validar_estado();
				if(!$temp[0]){
					$mensaje = "Error en el elemento de formulario '" . $this->elemento_formulario[$ef]->obtener_etiqueta() ."' - ". $temp[1];
					throw new excepcion_toba($mensaje);
				}
			}			
		}
 	}
	//-------------------------------------------------------------------------------

	function limpiar_interface()
/*
	@@acceso: actividad
	@@desc: Resetea los elementos	de	formulario
*/
	{
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->resetear_estado();
		}
		unset($this->datos);
		unset($this->ordenes);
	}
	//-------------------------------------------------------------------------------

	function cargar_estado_ef($array_ef)
	{
		throw new excepcion_toba("No esta implementado en el multilínea");
		//ATENCION: En un multilinea esto es distinto. FALTA
	}
	//-------------------------------------------------------------------------------

	function obtener_nombres_ef()
/*
	@@acceso: actividad
	@@desc: Recupera la lista de nombres de EF
	@@retorno: array | Listado	de	cada elemento de formulario
*/
	{
		foreach ($this->lista_ef_post	as	$ef){
			$nombres_ef[$ef] = $this->elemento_formulario[$ef]->obtener_id_form_orig();		
		}
		return $nombres_ef;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	---------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cantidad_lineas()
	{
		return count($this->datos);
	}
	
	//-------------------------------------------------------------------------------	
	function obtener_datos($analizar_diferencias = false)
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
	protected function obtener_clave_fila($fila)
	{
		if ($this->analizar_diferencias) {
			if (isset($this->datos[$fila]))
				return $fila;
		} else {
			$i = 0;
			foreach (array_keys($this->datos) as $id_fila) {
				if ($fila == $id_fila)
					return $i;
				$i++;
			}
			return $fila;
		}
		
	}
	
	//-------------------------------------------------------------------------------
	function cargar_datos($datos = null)
	{
		if ($datos !== null) {
			$this->filas_recibidas = array_keys($datos);
			$this->datos = $datos;
		} else {
			$this->carga_inicial();
		}
		//Ordenar por la columna que se establece
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
	//-------------------------------------------------------------------------------
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

	//-------------------------------------------------------------------------------

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
	//-------------------------------------------------------------------------------
	//------------------------  Multiplexacion de EFs  ------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_ef_a_registro($id_registro)
/*
	@@acceso: actividad
	@@desc: Carga el estado del array de EFs en un registro
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef)
		{
			//Aplano el estado del EF en un array
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if (is_array($dato)) {	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					echo ei_mensaje("obtener_datos: Error de consistencia	interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$this->datos[$id_registro][$dato[$x]]	= $estado[$dato[$x]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				//ATENCION, esta truchada es para evitar el comportamiento de los EF de retornar NULL
				if ($estado == 'NULL')
					$estado = null;
				$this->datos[$id_registro][$dato] = $estado;
			}
		}
		//ei_arbol($this->datos,"CArga de registros");
	}
	//-------------------------------------------------------------------------------

	function cargar_registro_a_ef($id_fila, $datos_registro)
/*
	@@acceso: actividad
	@@desc: Carga un REGISTRO en el array de EFs
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		$datos = $datos_registro;
		foreach ($this->lista_ef as $ef) {
			//Seteo el ID-formulario del EF para que referencie al registro actual
			$this->elemento_formulario[$ef]->establecer_id_form($id_fila);
			$this->elemento_formulario[$ef]->resetear_estado();
			$dato = $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja	 *** DATO COMPUESTO
				$temp = array();
				for($x=0;$x<count($dato);$x++){
					if(isset($datos[$dato[$x]])){
						$temp[$dato[$x]]=stripslashes($datos[$dato[$x]]);
					}else{
						$temp[$dato[$x]] = null;
					}
				}
			}else{					//El EF maneja	un	*** DATO SIMPLE
				$temp = (isset($datos[$dato])) ? stripslashes($datos[$dato]) : null;
			}
			if ($temp !== null)
				$this->elemento_formulario[$ef]->cargar_estado($temp);
		}
		$this->procesar_dependencias();
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//----------------------------	  SALIDA	  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function generar_formulario()
	{
		//Botonera de agregar y ordenar
		$this->botonera_manejo_filas();
		//Ancho y Scroll
		$ancho = isset($this->info_formulario["ancho"]) ? $this->info_formulario["ancho"] : "auto";
		if($this->info_formulario["scroll"]){
			$alto_maximo = isset($this->info_formulario["alto"]) ? $this->info_formulario["alto"] : "auto";
			if ($ancho != 'auto')
				echo "<div style='overflow: auto; width: $ancho; border: 1px inset; margin: 0px; padding: 0px;'>";
			else
				echo "<div>";
		}else{
			$alto_maximo = "auto";
			echo "<div>";
		}
		//Defino la cantidad de columnas
		$colspan = count($this->lista_ef_post);
		if ($this->info_formulario['filas_numerar']) {
			$colspan++;
		}			
		//Campo de comunicacion con JS
		echo form::hidden("{$this->objeto_js}_listafilas",'');
		echo form::hidden("{$this->objeto_js}__parametros", '');
		
		echo "<table class='tabla-0' style='width: $ancho'>\n";
		echo "<thead>\n";
		$this->generar_formulario_encabezado();
		echo "</thead>\n";
		echo "<tfoot>\n";
		$this->generar_formulario_pie($colspan);
		echo "</tfoot>\n";	
		echo "<tbody class='tabla-con-scroll' style='max-height: $alto_maximo'>";		
		$this->generar_formulario_cuerpo();
		echo "</tbody>\n";		
		echo "\n</table>\n</div>";
		
	}
	
	function generar_formulario_encabezado()
	{
		//------ TITULOS -----	
		echo "<tr>\n";
		if ($this->info_formulario['filas_numerar']) {
			echo "<th class='abm-columna'>&nbsp;</th>\n";
		}
		foreach ($this->lista_ef_post	as	$ef){
			echo "<th class='abm-columna'>\n";
			echo $this->elemento_formulario[$ef]->envoltura_ei_ml();
			echo "</th>\n";
		}
        //-- Eventos sobre fila
		$cant_sobre_fila = $this->cant_eventos_sobre_fila();
		if($cant_sobre_fila > 0){
			echo "<th class='abm-columna' colspan='$cant_sobre_fila'>\n";
            echo "</th>\n";
		}		
		echo "</tr>\n";
	}
	
	function generar_formulario_pie($colspan)
	{
		//------ Totales y Eventos------
		echo "\n<!-- TOTALES -->\n";
		if(count($this->lista_ef_totales)>0){
			echo "\n<tr>\n";
			if ($this->info_formulario['filas_numerar']) {
				echo "<td class='abm-total'>&nbsp;</td>\n";
			}
			foreach ($this->lista_ef_post as $ef){
				echo "<td  class='abm-total'>\n";
					$this->elemento_formulario[$ef]->establecer_id_form("s");
					$id_form_total = $this->elemento_formulario[$ef]->obtener_id_form();
					echo "<div id='$id_form_total' class='abm-total'>&nbsp;</div>";
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
		echo "<tr><td class='ei-base' colspan='$colspan'>\n";
		$this->obtener_botones();
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
				$estilo_fila = "abm-fila-ml-selec";				
			} else {
				$estilo_fila = "abm-fila-ml";
			}
			$this->cargar_registro_a_ef($fila, $dato);
			
			//Aca va el codigo que modifica el estado de cada EF segun los datos...
			echo "\n<!-- FILA $fila -->\n\n";
			echo "<tr $estilo id='{$this->objeto_js}_fila$fila' onFocus='{$this->objeto_js}.seleccionar($fila)' onClick='{$this->objeto_js}.seleccionar($fila)'>";
			if ($this->info_formulario['filas_numerar']) {
				echo "<td class='$estilo_fila'>\n<span id='{$this->objeto_js}_numerofila$fila'>".($a + 1);
				echo "</span></td>\n";
			}
			foreach ($this->lista_ef_post as $ef){
				$this->elemento_formulario[$ef]->establecer_id_form($fila);
				$id_form = $this->elemento_formulario[$ef]->obtener_id_form();
				echo "<td  class='$estilo_fila' id='cont_$id_form'>\n";
				echo $this->elemento_formulario[$ef]->obtener_input();
				echo "</td>\n";
			}
            //-- Eventos aplicados a una fila
			//Para el caso particular del ML, aquellos que manejan datos disparan un modificacion tambien (si es que lo hay)
			foreach ($this->eventos as $id => $evento) {
				if ($evento['sobre_fila']) {
					echo "<td class='$estilo_fila'>\n";
					$evento_js = eventos::a_javascript($id, $evento, $fila);
					$js = "{$this->objeto_js}.set_evento($evento_js);";
					if (isset($evento['imagen_recurso_origen']))
						$img = recurso::imagen_de_origen($evento['imagen'], $evento['imagen_recurso_origen']);
					else
						$img = $evento['imagen'];
					echo recurso::imagen($img, null, null, $evento['ayuda'], '', 
										"onclick=\"$js\"", 'cursor: pointer');
	            	echo "</td>\n";
				}
			}			
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
										" onclick='{$this->objeto_js}.deshacer();' disabled", $tab++, 'z', 'Deshace la última acción');
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
	//--------------------------------	DEPENDENCIAS  -------------------------------
	//-------------------------------------------------------------------------------

	function procesar_dependencias()
	{
		foreach ($this->lista_ef as $ef){
			$dependencias = $this->elemento_formulario[$ef]->obtener_dependencias();
			if(is_array($dependencias)){
				$estado = array();
				foreach( $dependencias as $dep ){
					//echo "entre $dep<br>";
					if(is_object($this->elemento_formulario[$dep])){
						if($temp = $this->elemento_formulario[$dep]->obtener_estado()){
							if($temp != "NULL") $estado[$dep] = $temp;
						}
					}else{
						echo ei_mensaje("La dependencia '$dep' es invalida");
					}
				}
				$this->elemento_formulario[$ef]->cargar_datos_dependencias($estado);
			}
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
		$hay_botonera = false;
		foreach ($eventos as $evento) {
			if ($evento['en_botonera'])
				$hay_botonera = true;
		}
		if(! $hay_botonera) {
				//En caso que no se definan eventos, modificacion es el por defecto y no se incluye como botón
			$eventos += eventos::modificacion(null, false);
			$this->set_evento_defecto('modificacion');
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
		echo $identado."var {$this->objeto_js} = new objeto_ei_formulario_ml";
		echo "('{$this->objeto_js}', $rango_tabs, '{$this->submit}', $filas, {$this->siguiente_id_fila}, $seleccionada, $en_linea);\n";
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
	
	function consumo_javascript_global()
	{
		$consumos = parent::consumo_javascript_global();
		$consumos[] = 'clases/objeto_ei_formulario_ml';
		return $consumos;
	}

}
?>
