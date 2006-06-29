<?

require_once("nucleo/componentes/interface/objeto_ei.php");	//Ancestro de todos los	OE
require_once("nucleo/obsoleto/efs_obsoletos/ef.php");//	Elementos de interface

class objeto_ei_formulario_obsoleto extends objeto_ei
{
	protected $elemento_formulario;			// interno | array |	Rererencias	a los	ELEMENTOS de FORMULARIO
	protected $nombre_formulario;			// interno | string | Nombre del	FORMULARIO en el cliente
	protected $prefijo;						// Prefijo de todos los objetos creados por este FORMs
	protected $lista_ef = array();			// interno | array |	Lista	completa	de	a los	EF
	protected $lista_ef_post = array();		// interno | array |	Lista	de	elementos que se reciben por POST
	protected $lista_ef_dao = array();
	protected $lista_ef_ocultos = array();
	protected $nombre_ef_cli = array(); 	// interno | array | ID html de los elementos
	protected $parametros;
	protected $modelo_eventos;
	protected $flag_out = false;			// indica si el formulario genero output
	protected $evento_mod_estricto;			// Solo dispara la modificacion si se apreto el boton procesar
	protected $rango_tabs;					// Rango de números disponibles para asignar al taborder
	protected $objeto_js;	

	protected $eventos_ext = null;			// Eventos seteados desde afuera
	protected $observadores;
	protected $id_en_padre;

	function __construct($id)
	{
		parent::objeto($id);
		//Elementos basicos del formulario
		$this->submit = "ei_form".$this->id[1];
		//Nombre de los botones de javascript
		$this->js_eliminar = "eliminar_ei_{$this->id[1]}";
		$this->js_agregar = "agregar_ei_{$this->id[1]}";
		$this->evento_mod_estricto = true;
		$this->objeto_js = "objeto_form_{$this->id[1]}";
		$this->rango_tabs = manejador_tabs::instancia()->reservar(50);
	}

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//Formulario
		$sql["info_formulario"]["sql"] = "SELECT	auto_reset as	auto_reset,						
										ancho 						as ancho,
										ancho_etiqueta				as ancho_etiqueta
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='".$this->id[0]."'
								AND		objeto_ut_formulario='".$this->id[1]."';";
		$sql["info_formulario"]["tipo"]="1";
		$sql["info_formulario"]["estricto"]="1";
		//EF
		$sql["info_formulario_ef"]["sql"] = "SELECT	identificador as identificador,
										columnas					as		columnas,
										obligatorio					as		obligatorio,
										elemento_formulario 		as		elemento_formulario,
										inicializacion				as		inicializacion,
										etiqueta					as		etiqueta,
										etiqueta_estilo				as		etiqueta_estilo,
										descripcion					as		descripcion,
										orden						as		orden,
										colapsado					as 		colapsado
								FROM	apex_objeto_ei_formulario_ef
								WHERE	objeto_ei_formulario_proyecto='".$this->id[0]."'
								AND	objeto_ei_formulario='".$this->id[1]."'
								AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql["info_formulario_ef"]["tipo"]="x";
		$sql["info_formulario_ef"]["estricto"]="1";
		return $sql;
	}
//--------------------------------------------------------------------------------------------

	function inicializar($parametros)
/*
	@@acceso: objeto
	@@desc: Dispara la creacion de los elementos	de	formulario (EF)
*/
	{
		$this->parametros = $parametros;
		$this->nombre_formulario =	$parametros["nombre_formulario"];
		if (isset($parametros['id']))
			$this->id_en_padre = $parametros['id'];
		$this->prefijo = $this->nombre_formulario . "_" . $this->id[1];
		//Creo el array de objetos EF (Elementos de Formulario) que conforman	el	ABM
		$this->crear_elementos_formulario();
		//Cargo IDs en el CLIENTE
		foreach ($this->lista_ef_post	as	$ef){
			$this->nombre_ef_cli[$ef] = $this->elemento_formulario[$ef]->obtener_id_form();
		}
		//Registar dependencias (SLAVE) en los MASTER
		$this->registrar_dependencias();
		//Inicializacion de especifica de cada tipo de formulario
		$this->inicializar_especifico();
	}
	//-------------------------------------------------------------------------------
	
	function crear_elementos_formulario()
/*
	@@acceso: interno
	@@desc: Genera	el	array	de	objetos EF que	constituye la columna vertebral del	ABM
*/
	{
		for($a=0;$a<count($this->info_formulario_ef);$a++)
		{
			//-[1]- Armo las listas	que determinan	el	plan de accion	del ABM
			$this->lista_ef[]	= $this->info_formulario_ef[$a]["identificador"];
			switch ($this->info_formulario_ef[$a]["elemento_formulario"]) {
				case	"ef_oculto":
				case	"ef_oculto_secuencia":
				case	"ef_oculto_proyecto":
				case	"ef_oculto_usuario":
					$this->lista_ef_ocultos[] = $this->info_formulario_ef[$a]["identificador"];
					break;
				default:
					  $this->lista_ef_post[] =	$this->info_formulario_ef[$a]["identificador"];
			}
			$parametros	= parsear_propiedades($this->info_formulario_ef[$a]["inicializacion"]);
			if(isset($parametros["sql"]) && !isset($parametros["fuente"])){
				$parametros["fuente"]=$this->info["fuente"];
			}

			//Preparo el identificador	del dato	que maneja el EF.
			//Esta parametro puede ser	un	ARRAY	o un string: exiten EF complejos	que manejan	mas de una
			//Columna de la tabla a	la	que esta	asociada	el	ABM
			if(ereg(",",$this->info_formulario_ef[$a]["columnas"])){
				 $dato =	explode(",",$this->info_formulario_ef[$a]["columnas"]);
				for($d=0;$d<count($dato);$d++){//Elimino espacios en las	claves
					$dato[$d]=trim($dato[$d]);
				}
			}else{
				 $dato =	$this->info_formulario_ef[$a]["columnas"];
			}
			//Nombre	del formulario.
			$id_ef = $this->info_formulario_ef[$a]["identificador"];
			$this->elemento_formulario[$id_ef] = new $this->info_formulario_ef[$a]["elemento_formulario"](
																		$this->id, 
																		$this->nombre_formulario,
																		$this->info_formulario_ef[$a]["identificador"],
																		$this->info_formulario_ef[$a]["etiqueta"],
																		addslashes($this->info_formulario_ef[$a]["descripcion"]),
																		$dato,
																		$this->info_formulario_ef[$a]["obligatorio"],
																		$parametros);
			if ($this->elemento_formulario[$id_ef]->tiene_carga_dao()) {
					  $this->lista_ef_dao[] =	$this->info_formulario_ef[$a]["identificador"];
			}			
			$this->elemento_formulario[$id_ef]->set_expandido(! $this->info_formulario_ef[$a]['colapsado']);
			if (isset( $this->info_formulario['ancho_etiqueta'])) {
				$this->elemento_formulario[$id_ef]->set_ancho_etiqueta($this->info_formulario['ancho_etiqueta']);
			}
			if (isset($this->info_formulario_ef[$a]['etiqueta_estilo'])) {
				$this->elemento_formulario[$id_ef]->set_estilo_etiqueta( $this->info_formulario_ef[$a]['etiqueta_estilo'] );
			}
		}	
	}
	//-------------------------------------------------------------------------------

	function inicializar_especifico()
	{
		$this->set_grupo_eventos_activo('no_cargado');
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------	INFORMACION	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function info_estado_ef()
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
		ei_arbol($temp,"Estado actual	de	los ELEMENTOS de FORMULARIO");
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	DEPENDENCIAS  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function procesar_dependencias()
	{
		foreach ($this->lista_ef as $ef){
			$dependencias = $this->elemento_formulario[$ef]->obtener_dependencias();
			if(is_array($dependencias)){
				//echo "entre $ef<br>";
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

	function registrar_dependencias()
	{
		foreach ($this->lista_ef as $ef)
		{
			if($dependencias = $this->elemento_formulario[$ef]->obtener_dependencias())
			{
				foreach( $dependencias as $dep )
				{
					if(is_object($this->elemento_formulario[$dep])){
						//Se le notifican a un maestro sus slaves
						$id_form_dep = $this->elemento_formulario[$ef]->obtener_id_form();
						$this->elemento_formulario[$dep]->registrar_ef_dependiente($ef, $id_form_dep);
						
						//Se le notifican a un slave todos sus maestros
						$id_form_master = $this->elemento_formulario[$dep]->obtener_id_form();
						$this->elemento_formulario[$ef]->registrar_ef_maestro($dep, $id_form_master);
					}else{
						echo ei_mensaje("La dependencia '$dep' es invalida");
					}
				}
			}
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function disparar_eventos()
	{
		$this->recuperar_interaccion();
		//Veo si se devolvio algun evento!
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!=""){
			$evento = $_POST[$this->submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if(isset($this->memoria['eventos'][$evento]) ){
				//Me fijo si el evento requiere validacion
				$maneja_datos = $this->memoria['eventos'][$evento];
				if($maneja_datos) {
					$this->validar_estado();
					$parametros = $this->obtener_datos();
				} else {
					$parametros = null;
				}
				//El evento es valido, lo reporto al contenedor
				$this->reportar_evento( $evento, $parametros );
			}
		}
		$this->limpiar_interface();
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function recuperar_interaccion()
	{
		if($this->cargar_post()==true){
			//$this->validar_estado();	
			//Se modificaron los datos?
		}else{
			echo ei_mensaje("No se cargo el POST");		
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_post()
/*
	@@acceso: interno
	@@desc: Carga el estado	de	cada EF a partir del	POST!
*/
	{
		$estado = true;
		foreach ($this->lista_ef as $ef){
			$x	= $this->elemento_formulario[$ef]->cargar_estado();
			if	(!$x){
				//$estado = false;
				//echo "ERROR en $ef <br>";
			}
		}
		return $estado;
	}
	//-------------------------------------------------------------------------------

	function validar_estado()
	{
		$status =	true;
		//Valida	el	estado de los ELEMENTOS	de	FORMULARIO
		foreach ($this->lista_ef as $ef)
		{
			//En la refactorizacion de EFs, el EF directamente dispara una excepcion
			$temp = $this->elemento_formulario[$ef]->validar_estado();
			if(!$temp[0]){
				$mensaje = "Error en el elemento de formulario '" . $this->elemento_formulario[$ef]->obtener_etiqueta() ."' - ". $temp[1];
				throw new excepcion_toba($mensaje);
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
		foreach ($this->lista_ef as $ef) {
			$this->elemento_formulario[$ef]->resetear_estado();
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_estado_ef($array_ef)
/*
	@@acceso: actividad
	@@desc: Esta funcion permite establecer el valor de un elemento del FORMULARIO (Visible u	Oculto)
	@@param:	array	| una	entrada por	EF	(id->valor)	que quiera cargar. los EF compuestos tienen que	recibir como valor un ARRAY con la forma que	espera el EF destino
*/
	{
		if(is_array($array_ef)){
			foreach($array_ef	as	$ef => $valor){
				if(isset($this->elemento_formulario[$ef])){
					$this->elemento_formulario[$ef]->cargar_estado($valor);
				}else{
					$this->registrar_info_proceso("[cargar_estado_ef] No existe	un	elemento	de	formulario identificado	'$ef'","error");
				}
			}
		}else{
			$this->registrar_info_proceso("[cargar_estado_ef] Los	EF	se	cargan a	travez de un array asociativo	(\"clave\"=>\"dato a	cargar\")!","error");
		}
	}
	//-------------------------------------------------------------------------------

	function	ejecutar_metodo_ef($ef,	$metodo, $parametro=null)
/*
	@@acceso: actividad
	@@desc: Esto sirve para	comunicarse	con EF que pueden	cambiar en tiempo	de	ejecucion
	@@desc: EJ:	un	combo	que necesita cambiar	una propiedad del	WHERE	segun	la	solicitud
	@@param:	string |	elemento	de	formulario a llamar
	@@param:	string |	metodo a	llamar en el EF
	@@param:	array	| Argumentos de la funcion
*/
	{
		if(isset($this->elemento_formulario[$ef])){
			return $this->elemento_formulario[$ef]->$metodo($parametro);
		}else{
			echo ei_mensaje("El EF identificado	'$ef'	no	existe.");
		}
	}
	//-------------------------------------------------------------------------------

	function	obtener_nombres_ef()
/*
	@@acceso: actividad
	@@desc: Recupera la lista de nombres de EF
	@@retorno: array | Listado	de	cada elemento de formulario
*/
	{
		foreach ($this->lista_ef_post	as	$ef){
			$nombres_ef[$ef] = $this->elemento_formulario[$ef]->obtener_id_form();		}
		return $nombres_ef;
	}
	//-------------------------------------------------------------------------------

	function	obtener_consumo_dao()
/*
	@@acceso: actividad
	@@desc: Recupera la lista de consumo de DAOs
	@@retorno: array | Asociativo (nombre/dao)
*/
	{
		$dao = null;
		foreach ($this->lista_ef_dao as $ef){
			if($temp = $this->elemento_formulario[$ef]->obtener_dao()){
				$dao[$ef] = $temp;
			}
		}
		return $dao;
	}
	//-------------------------------------------------------------------------------
	
	/**
	 * @deprecated Desde 0.8.4, usar set_solo_lectura
	 */
	function deshabilitar_efs($efs=null)
	{
		toba::get_logger()->obsoleto(__CLASS__, __FUNCTION__, "0.8.4", "Usar set_solo_lectura");
		$this->set_solo_lectura($efs);
	}
	
	/**
	 * Deshabilita o habilita la edición de un conjunto de efs de este formulario
	 *
	 * @param array $efs Conjunto de efs a deshabilitar, si es nulo se asume todos
	 * @param boolean $readonly Deshabilita la edición (por defecto true)
	 */
	function set_solo_lectura($efs=null, $readonly=true)
	{
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		foreach ($efs as $ef){
			if(isset($this->elemento_formulario[$ef])){
				if ($readonly) {
					$this->elemento_formulario[$ef]->establecer_solo_lectura();
				} else {
					$this->elemento_formulario[$ef]->establecer_lectura();
				}
			}else{
				throw new excepcion_toba("Deshabilitar EF: El ef '$ef' no existe");
			}
		}		
	}
	//-------------------------------------------------------------------------------
	
	function set_efs_obligatorios($efs=null)
	//Establece el grupo de EFs especificados como OBLIGATORIOS
	{
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		foreach ($efs as $ef){
			if(isset($this->elemento_formulario[$ef])){
				$this->elemento_formulario[$ef]->set_obligatorio();						
			}else{
				throw new excepcion_toba("El ef '$ef' no existe");
			}
		}
	}
	
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	obtener_datos()
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario. Genera un array asociativo de una dimension
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef)
		{
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					echo ei_mensaje("obtener_datos: Error de consistencia	interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$registro[$dato[$x]]	= $estado[$dato[$x]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$registro[$dato] = $estado;
			}
		}
		//ATENCION, esta truchada es para evitar el comportamiento de los EF de retornar NULL
		foreach(array_keys($registro) as $columna){
			if($registro[$columna]=="NULL"){
				$registro[$columna]=null;
			}	
		}
		return $registro;
	}
	//-------------------------------------------------------------------------------

	function cargar_datos($datos)
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		if(isset($datos)){
			//ei_arbol($datos,"DATOS para llenar el EI_FORM");
			//Seteo los	EF	con el valor recuperado
			foreach ($this->lista_ef as $ef){	//Tengo que	recorrer	todos	los EF...
				$temp = null;
				$dato = $this->elemento_formulario[$ef]->obtener_dato();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					$temp = null;
					for($x=0;$x<count($dato);$x++){
						if(isset($datos[$dato[$x]])){
							$temp[$dato[$x]]=stripslashes($datos[$dato[$x]]);
						}
					}
				}else{					//El EF maneja	un	DATO SIMPLE
					if(isset($datos[$dato])){
						if (!is_array($datos[$dato]))
							$temp = stripslashes($datos[$dato]);
						elseif (is_array($datos[$dato])) { //ATENCION: Este es el caso para el multi-seleccion, hay que mejorarlo
							$temp = array();
							foreach ($datos[$dato] as $string) {
								$temp[] = stripslashes($string);
							}
						}
					}
				}
				if(isset($temp)){
					$this->elemento_formulario[$ef]->cargar_estado($temp);
				}
			}
			//Memorizo que clave cargue de la base
			//guardo los datos en la memoria
			//para compararlos y saber si se modificaron
			//$this->memoria["datos"] = $datos;
			$this->procesar_dependencias();
			$this->set_grupo_eventos_activo('cargado');
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Genero la interface
		if($this->estado_proceso!="INFRACCION")
		{
			echo "\n\n<!-- ***************** Inicio EI FORMULARIO (	".	$this->id[1] ." )	***********	-->\n\n";
			//Campo de sincroniacion con JS
			echo form::hidden($this->submit, '');
			//A los ocultos se les deja incluir javascript
			foreach ($this->lista_ef_ocultos as $ef) {
				echo $this->elemento_formulario[$ef]->obtener_javascript_general();
			}
			$ancho = isset($this->info_formulario["ancho"]) ? $this->info_formulario["ancho"] : "";
			echo "<table class='objeto-base' width='$ancho' id='{$this->objeto_js}_cont'>";
			echo "<tr><td>";
			$this->barra_superior(null, true,"objeto-ei-barra-superior");
			echo "</td></tr>\n";
			$colapsado = (isset($this->colapsado) && $this->colapsado) ? "style='display:none'" : "";
			echo "<tr><td><div $colapsado id='cuerpo_{$this->objeto_js}'>";
			$this->generar_formulario();	
			echo "</div></td></tr>\n";
			echo "</table>\n";
			$this->flag_out = true;
		}
	}
	//-------------------------------------------------------------------------------

	function generar_formulario()
/*
	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Genero	la	interface
		if($this->estado_proceso!="INFRACCION") 
		{
			echo "<table class='tabla-0' width='{$this->info_formulario['ancho']}'>";
			$hay_colapsado = false;
			foreach ($this->lista_ef_post	as	$ef){
				$clase = 'abm-fila';
				$estilo_nodo = "";
				$id_ef = $this->elemento_formulario[$ef]->obtener_id_form();
				if (! $this->elemento_formulario[$ef]->esta_expandido()) {
					$hay_colapsado = true;
					$clase = 'abm-fila-oculta';
					$estilo_nodo = "display:none";
				}
				echo "<tr><td class='$clase' style='text-align: left; $estilo_nodo' id='nodo_$id_ef'>\n";
				$this->elemento_formulario[$ef]->obtener_interface_ei();
				echo "</td></tr>\n";
			}
			if ($hay_colapsado) {
				$img = recurso::imagen_apl('expandir_vert.gif', false);
				$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_expansion();\" title='Mostrar / Ocultar'";
				echo "<tr><td class='abm-fila' style='text-align:center'>";
				echo "<img id='{$this->objeto_js}_cambiar_expansion' src='$img' $colapsado>";
				echo "</td></tr>\n";
			}
			echo "<tr><td class='ei-base'>\n";
			$this->generar_botones();
			echo "</td></tr>\n";
			echo "</table>\n";
		}
	}

	//-------------------------------------------------------------------------------
	//---- EVENTOS ------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function get_lista_eventos()
	/*
		Los eventos standard estan relacionados con el consumo del formulario en un ABM
	*/
	{
		$eventos = parent::get_lista_eventos();
		return $eventos;     
		/*
		
		CAMBIO_EVT
		
		if($this->etapa != "agregar") {
			unset($eventos['alta']);
		} elseif($this->etapa !="modificar") {
			unset($eventos['baja']);
			unset($eventos['cancelar']);
			unset($eventos['modificacion']);
		}
		//En caso que no se definan eventos, modificacion es el por defecto y no se incluye como botón
		if (count($eventos) == 0) {
			$eventos += eventos::modificacion(null, false);		
			$this->set_evento_defecto('modificacion');
		}
		*/
	}
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		$rango_tabs = "new Array({$this->rango_tabs[0]}, {$this->rango_tabs[1]})";
		echo $identado."window.{$this->objeto_js} = new objeto_ei_formulario('{$this->objeto_js}', $rango_tabs, '{$this->submit}');\n";
		foreach ($this->lista_ef_post as $ef) {
			echo $identado."{$this->objeto_js}.agregar_ef({$this->elemento_formulario[$ef]->crear_objeto_js()}, '$ef');\n";
		}
	}

	//-------------------------------------------------------------------------------

	public function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'clases/objeto_ei_formulario';
		//Busco las	dependencias
		foreach ($this->lista_ef_post	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->obtener_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo = array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}

	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------
		
	function vista_impresion_html( $salida )
	{
		$salida->subtitulo( $this->get_titulo() );
		echo "<table class='tabla-0' width='{$this->info_formulario['ancho']}'>";
		foreach ( $this->lista_ef_post as $ef){
			$clase = 'abm-fila';
			echo "<tr><td class='lista-col-titulo' style='text-align: left'>\n";
			echo $this->elemento_formulario[$ef]->obtener_etiqueta();
			$temp = $this->get_valor_imprimible_ef( $ef );
			echo "</td><td class='". $temp['css'] ."'>\n";
			echo $temp['valor'];
			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}
	
	function get_valor_imprimible_ef( $id_ef ) 
	{
		require_once('nucleo/lib/formateo.php');
		$ef = $this->elemento_formulario[$id_ef];
		$valor = $ef->obtener_descripcion_estado();
		if ( $ef instanceof ef_editable_moneda ) {
			$temp = array( 'css' => 'col-num-p1', 'valor'=> formato_moneda($valor) );
		} elseif ( $ef instanceof ef_editable_numero ) {
			$temp = array( 'css' => 'col-num-p1', 'valor'=> $valor );
		} elseif ( $ef instanceof ef_editable_fecha ) {
			if ($valor!='') {
				$temp = array( 'css' => 'col-tex-p1', 'valor'=> formato_fecha($valor) );
			} else {
				$temp = array( 'css' => 'col-tex-p1', 'valor'=> '' );
			}
		} else {
			$temp = array( 'css' => 'col-tex-p1', 'valor'=> $valor );
		}
		return $temp;
	}
}


class	objeto_ut_formulario	extends objeto_ei_formulario_obsoleto
/*
	@@acceso: actividad
	@@desc: Esta clase contruye la Interface Grafica de un registro de una tabla
*/
{
	var $etapa_actual;
	var $lista_ef_clave;				//	interno | array |	Lista	de	elementos que forman	parte	de	la	CLAVE	(PK)
	var $lista_ef_secuencia;		//	interno | array |	Lista	de	elementos que representan secuencias
	var $lista_ef_no_sql = array();
	var $flag_no_propagacion;		//	interno | string | Flag	que indica si hay	que dejar de reproducir	el	estado de la MEMORIA
	var $clave;							//	interno | array |	Clave	que esta	procesando el formulario
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------	INICIALIZACION	 --------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
		
	function	objeto_ut_formulario($id)
/*
	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::__construct($id);
		$this->etapa_actual = "";
		$this->lista_ef_clave =	array();
		$this->lista_ef_secuencia = array();
		$this->flag_no_propagacion	= "no_prop";
	}

	function inicializar_especifico()
	{
		for($a=0;$a<count($this->info_formulario_ef);$a++)
		{
				//Lista de Secuencias
				if($this->info_formulario_ef[$a]["elemento_formulario"]=="ef_oculto_secuencia"){
					  $this->lista_ef_secuencia[]	= $this->info_formulario_ef[$a]["identificador"];
				}
				//Lista de CLAVES	del ABM
				if($this->info_formulario_ef[$a]["clave_primaria"]==1){
					 $this->lista_ef_clave[] =	$this->info_formulario_ef[$a]["identificador"];
				}
				//Columnas que no hay que utilizar para generar los SQL
				if($this->info_formulario_ef[$a]["no_sql"]==1){
					 $this->lista_ef_no_sql[] = $this->info_formulario_ef[$a]["identificador"];
				}
		}		
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------------------	INFORMACION	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	info()
/*
	@@acceso: actividad
	@@desc: Muestra es la informacion COMPLETA
*/
	{
		parent::info();
		ei_arbol($this->info_estado_ef());
	}	
	//-------------------------------------------------------------------------------

	function	permitir_eliminar()
/*
	@@acceso: objeto
	@@desc: Responde la interface	de	modificacion permite	eliminar	registros
*/
	{
		if($this->info_formulario["ev_mod_eliminar"]==1){
			return true;
		}else{
			return false;		
		}
	}	

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------	PROCESOS	 -----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function cargar_db($clave)
/*
	@@acceso: interno
	@@desc: Busca un registro de la base y	lo	carga	en	los EF.
*/
	{
		if(!isset($clave)) return false;
		//Busco las	columnas	que tengo que recuperar
		foreach ($this->lista_ef as $ef){	//Tengo que	recorrer todos los EF...
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				for($x=0;$x<count($dato);$x++){
					$sql_col[] = $dato[$x];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$sql_col[] = $dato;
			}
		}
		$sql_col = array_diff($sql_col, $this->lista_ef_no_sql);
		//Armo la porcion	de	SQL que corresponde al WHERE
		$clave_ok =	$this->formatear_clave($clave);
		foreach($clave_ok	as	$columna	=>	$valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		$sql =	" SELECT	" . implode(",	",$sql_col)	. 
				" FROM "	. $this->info_formulario["tabla"] .
				" WHERE " .	implode(" AND ",$sql_where) .";";
		//Busco el registro en la base
		$rs = toba::get_db($this->info["fuente"])->consultar($sql);
		if(empty($rs)) {//NO existe el registro
			return false;
		}
		$datos_db =	current($rs);//Siempre va a ser un solo registro
		//ei_arbol($datos_db,"DATOS DB");
		//Seteo los	EF	con el valor recuperado
		foreach ($this->lista_ef as $ef){	//Tengo que	recorrer	todos	los EF...
			if(!in_array($ef,$this->lista_ef_no_sql)){
				$dato	= $this->elemento_formulario[$ef]->obtener_dato();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					$temp	= array();
					for($x=0;$x<count($dato);$x++){
						$temp[$dato[$x]]=	stripslashes($datos_db[$dato[$x]]);
					}
				}else{					//El EF maneja	un	DATO SIMPLE
					$temp	= stripslashes($datos_db[$dato]);
				}
				$this->elemento_formulario[$ef]->cargar_estado($temp);
			}
		}
		//Memorizo que clave cargue de la base
		$this->memoria["clave"] = $clave_ok;
		$this->memorizar();
		$this->procesar_dependencias();
		return true;
	}
	//-------------------------------------------------------------------------------

	function	actualizacion_post_insert()
/*
	@@acceso: interno
	@@desc: Recupera el valor de las	secuencias de la base
*/
	{
		//ATENCION: Hay que mejorar la forma de recuperar una secuencia!!!
		if(is_array($this->lista_ef_secuencia)){//Hay secuencias?
			global $db,	$ADODB_FETCH_MODE;
			//Itero las	secuencias y les cargo su estado
			foreach($this->lista_ef_secuencia as $secuencia){
				$columna	= $this->elemento_formulario[$secuencia]->obtener_dato();//Una	secuencia no puede tener un dato	compuesto.
				$sql = "SELECT	MAX($columna) FROM {$this->info_formulario['tabla']};";
				$ADODB_FETCH_MODE	= ADODB_FETCH_NUM;
				$rs =	$db[$this->info["fuente"]][apex_db_con]->Execute($sql);
				if(!$rs){//SQL	mal formado
					$this->observar("error","OBJETO UT - Formulario	[actualizacion_post_insert] -	error	buscando	el	valor	de	la	secuencia: $secuencia 
								[SQL]	$sql - [ERROR]	" . $db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),false,true,true);
				}
				if($rs->EOF){//NO	existe el registro
					$this->observar("error","OBJETO UT - Formulario	[actualizacion_post_insert] -	error	buscando	el	valor	de	la	secuencia (NULL) : $secuencia",false,true,true);
				}
				if(trim($rs->fields[0])==""){
					$this->observar("error","OBJETO UT - Formulario	[actualizacion_post_insert] -	error	buscando	el	valor	de	la	secuencia (\"\") : $secuencia",false,true,true);
				}
				//Cargo el valor de la secuencia	en	el	EF
				$this->elemento_formulario[$secuencia]->cargar_estado($rs->fields[0]);	
			}
		}
	}
	//-------------------------------------------------------------------------------

	function	obtener_datos()
/*
	@@acceso: actividad
	@@desc: Recupera el estado	actual del formulario
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef)
		{
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					echo ei_mensaje("obtener_datos: Error de consistencia	interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$registro[$dato[$x]]	= $estado[$dato[$x]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$registro[$dato] = $estado;
			}
		}
		return $registro;
	}
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
	@@acceso: interno
	@@desc: Valida	el	registro
	@@pendiente: grados:	EJ	un	ef_oculto_proyecto no la deberia	dejar	pasar...
*/
	{
		//Valida	el	estado de los ELEMENTOS	de	FORMULARIO
		  $status =	true;
		foreach ($this->lista_ef as $ef){
			$temp	= $this->elemento_formulario[$ef]->validar_estado();
				if(!$temp[0]){
					 $status	= false;
					$this->registrar_info_proceso("[". $this->elemento_formulario[$ef]->obtener_etiqueta(). 
													"]	- ". $temp[1],"error");
				}
		}
		//Validacion ESPECIFICA
		//Carga los	datos	donde	el	VALIDADOR del HIJO los busca (ahorrar?)
		if(!$temp){
			$status = false;
		}
		  return	$status;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------------	 Generacion	de	SQL	------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_sql($tipo="insert")
/*
	@@acceso: objeto
	@@desc: Devuelve el SQL	de	esta UT
*/
	{
		switch($tipo){
			case "insert":
				return $this->generar_sql_insert();
				break;
			case "delete":
				return $this->generar_sql_delete();
				break;
			case "update":
				return $this->generar_sql_update();
				break;
		}
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_insert()
/*
	@@acceso: objeto
	@@desc: GENERA	un	SQL de insersion para el registro cargado	en	la	INTERFACE
*/
	{
		global $db;
		foreach ($this->lista_ef as $ef){					//Tengo que	recorrer	todos	los EF...
			if(!(in_array($ef,$this->lista_ef_secuencia)))	//...	Menos	las secuencias
			{
				$dato	= $this->elemento_formulario[$ef]->obtener_dato();
				$estado = $this->elemento_formulario[$ef]->obtener_estado();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
						return array(false,"procesar_insert: Error de consistencia interna en el EF etiquetado: ".
											$this->elemento_formulario[$ef]->obtener_etiqueta() );
					}
					for($x=0;$x<count($dato);$x++){
						$sql_col[] = $dato[$x];
						$sql_val[] = $estado[$dato[$x]];
					}
				}else{					//El EF maneja	un	DATO SIMPLE
					$sql_col[] = $dato;
					$sql_val[] = $estado;
				}
			}
		}
		//Reduzco repeticiones en los	ARRAYS y ESCAPO caracteres
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		for($a=0;$a<count($sql_col);$a++){
			//El campo tiene que manejarse como SQL?
			if(!in_array($sql_col[$a],$this->lista_ef_no_sql) ){
				$columnas[$sql_col[$a]]	= addslashes($sql_val[$a]);
			}
		}
		$sql_col = array_keys($columnas);
		$sql_val = array_values($columnas);
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");

		//Genero	el	SQL de INSERCION
		$sql = "INSERT	INTO ". $this->info_formulario["tabla"] ." (". implode(",",$sql_col)	.") 
				VALUES ('".	implode("','",$sql_val)	."');";
		//ATENCION!!: esto implica	que nunca se va a	poder	grabar la palabra	"NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		//return	array(false,"SQL:	".$sql);
		return array($sql);
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_update()
/*
	@@acceso: interno
	@@desc: Realizo un UPDATE del	registro	en	la	base.
*/
	{
		global $db;
		//Recupero los	valores de los	EF, para	generar el SQL	de	UPDATE
		foreach ($this->lista_ef_post	as	$ef){		//Recorro SOLO	los EF que vienen	del POST
			$dato	= $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error	de	consistencia interna	del EF
					//No tengo que	dejar	una observacion tambien???
					ei_arbol($dato,"DATOS manejados");
					ei_arbol($estado,"ESTADO interno");
					//return	array(false,"procesar_update:	Error	de	consistencia interna	en	el	EF	etiquetado:	".
						//				$this->elemento_formulario[$ef]->obtener_etiqueta() );
					return "";
				}
				for($a=0;$a<count($dato);$a++){
					$sql_col[] = $dato[$a];
					$sql_val[] = $estado[$dato[$a]];
				}
			}else{					//El EF maneja	un	DATO SIMPLE
				$sql_col[] = $dato;
				$sql_val[] = $estado;
			}
		}
		//Reduzco repeticiones en los	ARRAYS y ESCAPO CARACTERES
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		for($a=0;$a<count($sql_col);$a++){
			//El campo tiene que manejarse como SQL?
			if(!in_array($sql_col[$a],$this->lista_ef_no_sql) ){
				$columnas[$sql_col[$a]]	= addslashes($sql_val[$a]);
			}
		}
		//Tengo que sacar las columnas que no van en el SQL
		

		//Si no se pueden	modificar las claves, las elimino de la sentencia
		if($this->info_formulario["ev_mod_clave"]!="1"){
			foreach(	array_keys($this->obtener_clave()) as $columna){
				unset($columnas[$columna]);
			}
		}
		//Sero los nombres de las columnas de los	valores
		$sql_col	= array_keys($columnas);
		$sql_val	= array_values($columnas);
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		//Armo la porcion	de	SQL que corresponde a las COLUMNAS
		for($a=0;$a<count($sql_col);$a++){
			$sql_update[] = $sql_col[$a] . "	= '" . $sql_val[$a] . "'";
		}
		//Armo la porcion	de	SQL que corresponde al WHERE
		$clave_ok = $this->formatear_clave($this->memoria["clave"]);
		foreach(	$clave_ok as $columna => $valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		//Armo el SQL completo
		$sql =	" UPDATE	" . $this->info_formulario["tabla"]	. 
				" SET	" . implode(",	",$sql_update)	. 
				" WHERE " .	implode(" AND ",$sql_where) .";";
		//ATENCION!!: esto implica	que nunca se va a	poder	grabar la palabra	"NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		return array($sql);
	}
	//-------------------------------------------------------------------------------

	function	generar_sql_delete()
/*
	@@acceso: interno
	@@desc: ELIMINA el registro en la BASE
*/
	{
		global $db;
		//Grabo el contenido	de	la	interface en la base
		//Armo la porcion	de	SQL que corresponde al WHERE
		$clave_ok = $this->formatear_clave($this->memoria["clave"]);
		foreach(	$clave_ok as $columna => $valor){
			$sql_where[] =	"(	$columna	= '$valor')";
		}
		$sql =	" DELETE	FROM " .	$this->info_formulario["tabla"] . 
				" WHERE " .	implode(" AND ",$sql_where) .";";
		return array($sql);
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------------	  MANEJO	de	la	PK	  ----------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	obtener_clave($refrescar_memoria = true)
/*
	@@acceso: actividad
	@@desc: Devuelve la clave que	se	esta procesando como	un	array	asociativo (dato/valor)
	@@desc: (El	dato representa la columna	de	la	tabla).La clave de los EFs
	@@desc: Atencion:	SI	el	ABM esta	configurado	en	AUTO-RESET,	es	probable	que este	metodo devuelva un NULL
*/
	{
		$clave_actual = array();//Preparo un array para	cargar claves
		foreach($this->lista_ef_clave	as	$clave)
		{
			$temp	= $this->elemento_formulario[$clave]->obtener_estado();
			if(is_array($temp)){//Los EF compuestos ya devuelven un array dato/valor
				$clave_actual = array_merge($clave_actual,$temp);
			}else{//Los	EF	simples devuelven	un	string, tengo que	armar	el	par dato/valor	a mano
				$dato	= $this->elemento_formulario[$clave]->obtener_dato();
				$clave_actual[$dato]	= $temp;
			}
		}
		if($refrescar_memoria){
			//Refresco la clave de la memoria, si es que se puede modificar...
			if($this->info_formulario["ev_mod_clave"]=="1"){
				$this->memoria["clave"] = $clave_actual;
				$this->memorizar();
			}
		}
		return $clave_actual;
	}
	//-------------------------------------------------------------------------------

	function	formatear_clave($clave_pos)
/*
	@@acceso: interno
	@@desc: Le da formato a	un	clave	definida	en	forma	posicional,	transformandola en formato	asociativo
	@@param:	
*/
	{
		//ei_arbol($clave_pos,"Clave RECIBIDA");
		//Obtengo los nombres de los indices (datos manejados	por los EF clave)
		foreach($this->lista_ef_clave	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->obtener_dato();
			//Si se maneja	un	dato complejo lo DESARMO...				
			if(is_array($temp)){
				//ei_arbol($temp,"Dato recibido de un EF");
				for($a=0;$a<count($temp);$a++){
					$dato[] = $temp[$a];
				}
			}else{
				$dato[] = $temp;
			}
		}
		//Si la cantidad de indices no coincide con la cantidad de valores, algo esta	mal
		if(count($clave_pos)!=count($dato)){
			echo ei_mensaje("UT - FORMULARIO	[ " .	$this->id[1] .	" ] -	La	clave	especificada no corresponde con la definida","error");
			ei_arbol($clave_pos,"CLAVE	recibida");
			ei_arbol($dato,"Estructura	esperada");
			//No puedo seguir	el	procesamiento si las	cosas	estan	asi
			$this->observar("error","[formatear_clave] -	La	clave	especificada esta	mal formada",true,false,true);
		}
		//Armo la definicion	asociativa.	El	criterio	es: el orden de los valores pasados
		//tiene que	corresponder al orden de los EF,	y dentro	de	estos	al	orden	de	las
		//columnas definidas.
		$indice = 0;
		//Itero de esta manera para dar la posibilidad de pasar una	clave	no	numerica	(aunque ordenada)
		foreach($clave_pos as $clave){
			$clave_asoc[$dato[$indice]]=$clave;
			$indice++;
		}
		return $clave_asoc;
	}
	//-------------------------------------------------------------------------------

	function inhabilitar_modificacion_claves()
/*
	@@acceso: actividad
	@@desc: Inhabilita la modificacion de claves.
*/
	{
		if($this->info_formulario["ev_mod_clave"]!="1"){
			foreach($this->lista_ef_clave	as	$ef){
				if(in_array($ef, $this->lista_ef_post)){
					$this->elemento_formulario[$ef]->establecer_solo_lectura();
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

    function lista_claves()
/*
	@@acceso: mt_mds
	@@desc: Devuelve una lista con las claves del formulario.
*/

    {
    	if (isset($this->lista_ef_clave)) 
    	{
    	    return $this->lista_ef_clave;
    	}
        else
        {
            return array();
        }
    }
    

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function	get_consumo_javascript()
	{
		//Busco las	dependencias
		$consumo	= array();
		foreach ($this->lista_ef_post	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->obtener_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo	= array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}
	//-------------------------------------------------------------------------------

	function	obtener_javascript()
/*
	@@acceso: interno
	@@desc: devuelve el javascript del formulario
*/
	{
		$javascript	= "";
		//Obtengo el javascript	de	validacion de cada EF
		foreach ($this->lista_ef_post	as	$ef){
			$javascript	.=	$this->elemento_formulario[$ef]->obtener_javascript();
		}
		$javascript	.=	"\n\n";
		return $javascript;
	}
	//-------------------------------------------------------------------------------

	function	obtener_html()
/*
	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Genero	la	interface
		if($this->estado_proceso!="INFRACCION")
		{
			echo "\n<!-- ***************** Inicio UT FORMULARIO (	".	$this->id[1] ." )	***********	-->\n\n";
			//A los ocultos se les deja incluir javascript
			foreach ($this->lista_ef_ocultos as $ef) {
				echo $this->elemento_formulario[$ef]->obtener_javascript_general();
			}
			echo "<table width='100%' class='tabla-0'>";
			foreach ($this->lista_ef_post	as	$ef){
				echo "<tr><td class='abm-fila'>\n";
				$this->elemento_formulario[$ef]->obtener_interface_ut();
				echo "</td></tr>\n";
			}
			echo "</table>\n";
			echo "\n<!--	Fin UT FORMULARIO	(". $this->id[1] .")	-->\n\n";
		}
	}
	//-------------------------------------------------------------------------------

}
?>