<?
require_once("objeto_ei.php");	//Ancestro de todos los	OE
require_once("nucleo/browser/interface/ef.php");//	Elementos de interface

/**
 * Un formulario simple presenta una grilla de campos editables. 
 * A cada uno de estos campos se los denomina Elementos de Formulario (efs).
 * @todo Los EF deberian cargar su estado en el momento de obtener la interface, no en su creacion.
 * @package Objetos
 * @subpackage Ei
 */
class objeto_ei_formulario extends objeto_ei
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
	
	function deshabilitar_efs($efs=null)
	//Establece el grupo de EFs especificados como SOLO LECTURA
	{
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		foreach ($efs as $ef){
			if(isset($this->elemento_formulario[$ef])){
				$this->elemento_formulario[$ef]->establecer_solo_lectura();						
			}else{
				$log = toba::get_logger();
				$log->error("DESABILITAR EF: el EF '$ef' no existe");
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
				$this->elemento_formulario[$ef]->establecer_obligatorio();						
			}else{
				$log = toba::get_logger();
				$log->error("DESABILITAR EF: el EF '$ef' no existe");
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
				$colapsado = "style='cursor: hand; cursor: pointer;' onclick=\"{$this->objeto_js}.cambiar_expansion();\" title='Mostrar / Ocultar'";
				echo "<tr><td class='abm-fila' style='text-align:center'>";
				echo "<img id='{$this->objeto_js}_cambiar_expansion' src='$img' $colapsado>";
				echo "</td></tr>\n";
			}
			echo "<tr><td class='ei-base'>\n";
			$this->obtener_botones();
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
		echo $identado."var {$this->objeto_js} = new objeto_ei_formulario('{$this->objeto_js}', $rango_tabs, '{$this->submit}');\n";
		foreach ($this->lista_ef_post as $ef) {
			echo $identado."{$this->objeto_js}.agregar_ef({$this->elemento_formulario[$ef]->crear_objeto_js()}, '$ef');\n";
		}
	}

	//-------------------------------------------------------------------------------

	public function consumo_javascript_global()
	{
		$consumo = parent::consumo_javascript_global();
		$consumo[] = 'clases/objeto_ei_formulario';
		//Busco las	dependencias
		foreach ($this->lista_ef_post	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->obtener_consumo_javascript();
			if(isset($temp)) $consumo = array_merge($consumo, $temp);
		}
		$consumo = array_unique($consumo);//Elimino los	duplicados
		return $consumo;
	}	
}
?>
