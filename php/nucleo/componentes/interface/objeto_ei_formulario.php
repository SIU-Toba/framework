<?
require_once("objeto_ei.php");	//Ancestro de todos los	OE
require_once("nucleo/componentes/interface/efs/ef.php");

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
	protected $lista_ef_ocultos = array();
	protected $nombre_ef_cli = array(); 	// interno | array | ID html de los elementos
	protected $parametros_carga_efs;		// Parámetros que se utilizan para cargar de valores a los efs
	protected $parametros;
	protected $modelo_eventos;
	protected $flag_out = false;			// indica si el formulario genero output
	protected $evento_mod_estricto;			// Solo dispara la modificacion si se apreto el boton procesar
	protected $rango_tabs;					// Rango de números disponibles para asignar al taborder
	protected $objeto_js;	
	protected $ancho_etiqueta = '150px';
	protected $efs_invalidos = array();

	protected $eventos_ext = null;			// Eventos seteados desde afuera
	protected $observadores;
	protected $id_en_padre;
	protected $item_editor = '/admin/objetos_toba/editores/ei_formulario';
		
	//---Cascadas
	protected $cascadas_maestros = array();		//Arreglo de maestros indexados por esclavo
	protected $cascadas_esclavos = array();		//Arreglo de esclavos indexados por maestro

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

	function inicializar($parametros)
	{
		$this->parametros = $parametros;
		$this->nombre_formulario =	$parametros["nombre_formulario"];
		if (isset($this->info_formulario['ancho_etiqueta']) && $this->info_formulario['ancho_etiqueta'] != '') {
			$this->ancho_etiqueta = $this->info_formulario['ancho_etiqueta'];
		}	
		if (isset($parametros['id']))
			$this->id_en_padre = $parametros['id'];
		$this->prefijo = $this->nombre_formulario . "_" . $this->id[1];
		//Creo el array de objetos EF (Elementos de Formulario) que conforman	el	ABM
		$this->crear_elementos_formulario();
		//Cargo IDs en el CLIENTE
		foreach ($this->lista_ef_post	as	$ef){
			$this->nombre_ef_cli[$ef] = $this->elemento_formulario[$ef]->get_id_form();
		}
		//Registar esclavos en los maestro
		$this->registrar_cascadas();
		//Inicializacion de especifica de cada tipo de formulario
		$this->inicializar_especifico();
	}
	
	protected function crear_elementos_formulario()
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
			$parametros	= parsear_propiedades($this->info_formulario_ef[$a]["inicializacion"], '_');
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
			$this->parametros_carga_efs[$id_ef] = $parametros;
			$this->elemento_formulario[$id_ef] = new $this->info_formulario_ef[$a]["elemento_formulario"](
																		$this, 
																		$this->nombre_formulario,
																		$this->info_formulario_ef[$a]["identificador"],
																		$this->info_formulario_ef[$a]["etiqueta"],
																		addslashes($this->info_formulario_ef[$a]["descripcion"]),
																		$dato,
																		$this->info_formulario_ef[$a]["obligatorio"],
																		$parametros);
			$this->elemento_formulario[$id_ef]->set_expandido(! $this->info_formulario_ef[$a]['colapsado']);
			if (isset($this->info_formulario_ef[$a]['etiqueta_estilo'])) {
				$this->elemento_formulario[$id_ef]->set_estilo_etiqueta( $this->info_formulario_ef[$a]['etiqueta_estilo'] );
			}
		}
	}
	
	function inicializar_especifico()
	{
		$this->set_grupo_eventos_activo('no_cargado');
	}

	//-------------------------------------------------------------------------------
	//--------------------------------	EVENTOS  -----------------------------------
	//-------------------------------------------------------------------------------

	function disparar_eventos()
	{
		$this->recuperar_interaccion();
		$datos = $this->get_datos();
		$validado = false;
		//Veo si se devolvio algun evento!
		if (isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];
			//La opcion seleccionada estaba entre las ofrecidas?
			if (isset($this->memoria['eventos'][$evento])) {
				//Me fijo si el evento requiere validacion
				$maneja_datos = $this->memoria['eventos'][$evento];
				if($maneja_datos) {
					if (! $validado) {
						$this->validar_estado();
						$validado = true;
					}
					$parametros = $datos;
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
	//--------------------------------	PROCESOS  -----------------------------------
	//-------------------------------------------------------------------------------

	function recuperar_interaccion()
	{
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->cargar_estado_post();
		}
	}

	function validar_estado()
	{
		//Valida el	estado de los ELEMENTOS	de	FORMULARIO
		foreach ($this->lista_ef as $ef) {
			$validacion = $this->elemento_formulario[$ef]->validar_estado();
			if ($validacion !== true) {
				$this->efs_invalidos[$ef] = $validacion;
				$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta();
				throw new excepcion_toba($etiqueta.': '.$validacion);
			}
		}
	}

	//-------------------------------------------------------------------------------
	//--------------------------------	CASCADAS  -------------------------------
	//-------------------------------------------------------------------------------

	function registrar_cascadas()
	{
		$this->cascadas_maestros = array();
		$this->cascadas_esclavos = array();
		foreach ($this->lista_ef as $esclavo) {
			$this->cascadas_maestros[$esclavo] = $this->elemento_formulario[$esclavo]->get_maestros();
			foreach ($this->cascadas_maestros[$esclavo] as $maestro) {
				if (! isset($this->elemento_formulario[$maestro])) {
					throw new excepcion_toba_def("El ef '$maestro' no esta definido");
				}
				$this->cascadas_esclavos[$maestro][] = $esclavo;

				$id_form_dep = $this->elemento_formulario[$esclavo]->get_id_form();
				$js = "{$this->objeto_js}.cascadas_cambio_maestro('$maestro')";
				$this->elemento_formulario[$maestro]->set_cuando_cambia_valor($js);
			}
		}
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------------	Manejos de EFS ------------------------------
	//-------------------------------------------------------------------------------

	function limpiar_interface()
	{
		foreach ($this->lista_ef as $ef) {
			$this->elemento_formulario[$ef]->resetear_estado();
		}
	}

	function get_nombres_ef()
	{
		return $this->lista_ef_post;
	}
	
	/**
	 * Retorna la referencia a un ef contenido
	 * @return ef
	 */
	function ef($id) 
	{
		return $this->elemento_formulario[$id];
	}

	
	/**
	 * Permite o no la edición de un conjunto de efs de este formulario, pero sus valores se muestran al usuario
	 *
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 * @param boolean $readonly ¿Hacer solo_lectura? (true por defecto)
	 */
	function set_solo_lectura($efs=null, $readonly=true)
	{
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);	
		}
		foreach ($efs as $ef){
			if(isset($this->elemento_formulario[$ef])){
				$this->elemento_formulario[$ef]->set_solo_lectura($readonly);
			}else{
				throw new excepcion_toba("Deshabilitar EF: El ef '$ef' no existe");
			}
		}
	}

	/**
	 * Establece que un conjunto de efs serán o no obligatorios durante una interacción
	 *
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 * @param boolean $obligatorios ¿Hacer obligatorio? (true por defecto)
	 */
	function set_efs_obligatorios($efs=null, $obligatorios=true) {
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);	
		}
		foreach ($efs as $ef){
			if(isset($this->elemento_formulario[$ef])){
				$this->elemento_formulario[$ef]->set_obligatorio($obligatorios);						
			}else{
				throw new excepcion_toba("El ef '$ef' no existe");
			}
		}
	}
	
	/**
	 * Establece que un conjunto de efs NO seran enviados al cliente durante una interacción
	 *
	 * @param array $efs Uno o mas efs, si es nulo se asume todos
	 * @param boolean $desactivar ¿Desactivarlos? (true por defecto)
	 */
	function desactivar_efs($efs=null, $desactivar=true)
	{
		if(!isset($efs)){
			$efs = $this->lista_ef_post;
		}
		if (! is_array($efs)) {
			$efs = array($efs);	
		}
		
	}
	
	function cargar_estado_ef($array_ef)
	{
		if(is_array($array_ef)){
			foreach($array_ef	as	$ef => $valor){
				if(isset($this->elemento_formulario[$ef])){
					$this->elemento_formulario[$ef]->set_estado($valor);
				}else{
					$this->registrar_info_proceso("[cargar_estado_ef] No existe	un	elemento	de	formulario identificado	'$ef'","error");
				}
			}
		}else{
			$this->registrar_info_proceso("[cargar_estado_ef] Los	EF	se	cargan a	travez de un array asociativo	(\"clave\"=>\"dato a	cargar\")!","error");
		}
	}

	
	//-------------------------------------------------------------------------------
	//-------------------------	  MANEJO de DATOS	  -------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Recupera el estado actual del formulario. Genera un array asociativo de una dimension
	 */
	function get_datos()
	{
		foreach ($this->lista_ef as $ef) {
			$dato	= $this->elemento_formulario[$ef]->get_dato();
			$estado = $this->elemento_formulario[$ef]->get_estado();
			if (is_array($dato)){	//El EF maneja	DATO COMPUESTO
				if ($this->elemento_formulario[$ef]->es_estado_unico()) {
					if ((count($dato))!=(count($estado))) {//Error	de	consistencia interna	del EF
						throw new excepcion_toba_def("Error de consistencia	interna en el EF etiquetado: ".
											$this->elemento_formulario[$ef]->get_etiqueta().
											"\nRecibido: ".var_export($estado, true));
					}
					for($x=0;$x<count($dato);$x++){
						$registro[$dato[$x]] = $estado[$dato[$x]];
					}
				} else {
					//--- Es multi-estado y multi-dato!! Caso particular, no es posible normalizar el arreglo					
					$salida = array();
					$registro[$ef] = array();
					foreach ($estado as $sub_estado) {
						if (count($dato) != count($sub_estado)) {
							//Error	de	consistencia interna	del EF
							throw new excepcion_toba_def("Error de consistencia	interna en el EF etiquetado: ".
												$this->elemento_formulario[$ef]->get_etiqueta().
												"\nRecibido: ".var_export($sub_estado, true));
						}
						for ($x=0;$x<count($dato);$x++) {
							$salida[$dato[$x]] = $sub_estado[$dato[$x]];
						}
						$registro[$ef][] = $salida;						
					}
				}
			} else {
				$registro[$dato] = $estado;
			}
		}
		return $registro;
	}

	function cargar_datos($datos)
	{
		if (isset($datos)){
			//ei_arbol($datos,"DATOS para llenar el EI_FORM");
			//Seteo los	EF	con el valor recuperado
			foreach ($this->lista_ef as $ef) {	//Tengo que	recorrer	todos	los EF...
				$temp = null;
				$dato = $this->elemento_formulario[$ef]->get_dato();
				if(is_array($dato)){	//El EF maneja	DATO COMPUESTO
					if ($this->elemento_formulario[$ef]->es_estado_unico()) {
						$temp = null;
						for($x=0;$x<count($dato);$x++) {
							if(isset($datos[$dato[$x]])) {
								$temp[$dato[$x]]=stripslashes($datos[$dato[$x]]);
							}
						}
					} else {
						//--- Es multi-estado y multi-dato!! Caso particular, no es posible normalizar el arreglo
						$temp = $datos[$ef];
					}
				} else {					//El EF maneja	un	DATO SIMPLE
					if (isset($datos[$dato])){
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
					$this->elemento_formulario[$ef]->set_estado($temp);
				}
			}
			//Memorizo que clave cargue de la base
			//guardo los datos en la memoria
			//para compararlos y saber si se modificaron
			//$this->memoria["datos"] = $datos;
			//$this->procesar_dependencias();
			$this->set_grupo_eventos_activo('cargado');
		}
	}
	
	//---------------------------------------------------------------------------
	//-------------------------	  CARGA DE efs	  -------------------------------
	//---------------------------------------------------------------------------

	function ef_tiene_maestros_seteados($id_ef)
	{
		foreach ($this->cascadas_maestros[$id_ef] as $maestro) {
			if (! $this->elemento_formulario[$maestro]->activado()) {
				return false;
			}
		}
		return true;			
	}
	
	protected function cargar_valores_efs()
	{
		foreach ($this->lista_ef as $id_ef) {
			if ($this->ef_requiere_carga($id_ef)) {
				$param = array();
				//-- Tiene maestros el ef?
				$cargar = true;
				if (isset($this->cascadas_maestros[$id_ef]) && !empty($this->cascadas_maestros[$id_ef])) {
					foreach ($this->cascadas_maestros[$id_ef] as $maestro) {
						if ($this->elemento_formulario[$maestro]->activado()) {
							$estado = $this->elemento_formulario[$maestro]->get_estado();
							$param[$maestro] = $estado;
						} else {
							$cargar = false;
						}
					}
				}
				if ($cargar) {
					if ($this->elemento_formulario[$id_ef]->carga_depende_de_estado()) {
						$param[$id_ef] = $this->elemento_formulario[$id_ef]->get_estado();
					}
					$datos = $this->ejecutar_metodo_carga_ef($id_ef, $param);
				} else {
					$datos = null;	
				}
				$this->elemento_formulario[$id_ef]->cargar_valores($datos);				
			}
		}
	}
	
	protected function ef_requiere_carga($id_ef)
	{
		return 
			isset($this->parametros_carga_efs[$id_ef]['dao'])
			|| isset($this->parametros_carga_efs[$id_ef]['lista'])
			|| isset($this->parametros_carga_efs[$id_ef]['sql']);
	}
	
	protected function ejecutar_metodo_carga_ef($id_ef, $maestros = array())
	{
		$parametros = $this->parametros_carga_efs[$id_ef];
		$seleccionable = $this->elemento_formulario[$id_ef]->es_seleccionable();
		
		$es_posicional = true;
		if ($seleccionable) {
			//--- Se determinan cuales son los campos claves y el campo de valor
			$campos_clave = $this->elemento_formulario[$id_ef]->get_campos_clave();
			$campo_valor = $this->elemento_formulario[$id_ef]->get_campo_valor();
			$es_posicional = $this->elemento_formulario[$id_ef]->son_campos_posicionales();
	
			$valores = array();
			if (isset($parametros['no_seteado']) && ! isset($valores[apex_ef_no_seteado])) {
				$valores[apex_ef_no_seteado] = $parametros['no_seteado'];
			}
		}
		if (isset($parametros['lista'])) {
			//--- Carga a partir de una lista de valores
			$nuevos = $this->ef_metodo_carga_lista($id_ef, $parametros, $maestros);
			return $valores + $nuevos;
		} elseif (isset($parametros['sql'])) {
			//--- Carga a partir de un SQL
			$nuevos = $this->ef_metodo_carga_sql($id_ef, $parametros, $maestros, $es_posicional);
			if ($seleccionable) {
				return $valores + rs_convertir_asociativo($nuevos, $campos_clave, $campo_valor);
			} else {
				if (! empty($nuevos)) {
					return $nuevos[0][0];					
				}
			}
		} elseif (isset($parametros['dao'])) {
			//--- Carga a partir de un Método PHP
			$nuevos = $this->ef_metodo_carga_php($id_ef, $parametros, $maestros);
			if ($seleccionable) {
				$val = $valores + rs_convertir_asociativo($nuevos, $campos_clave, $campo_valor);
				return $val;
			} else {
				return $nuevos;	
			}
		}
	}

	protected function ef_metodo_carga_lista($id_ef, $parametros, $maestros)
	{
		$elementos = explode(",", $parametros["lista"]);
		$valores = array();
		foreach ($elementos as $elemento) {
			$campos = explode("/", $elemento);
			if (count($campos) == 1) {
				$valores[trim($campos[0])] = trim($campos[0]);
			} elseif (count($campos) == 2) {
				$valores[trim($campos[0])] = trim($campos[1]);
			} else {
				throw new excepcion_toba_def("La lista de valores del ef $id_ef es incorrecta.");
			}
		}		
		return $valores;
	}
	
	protected function ef_metodo_carga_sql($id_ef, $parametros, $maestros, $es_posicional)
	{
		//--- Si la SQL contenia comillas fueron quoteadas cuando se guardaron en la base
		$parametros['sql'] = stripslashes($parametros['sql']);
        //Armo la sentencia que limita al proyecto
        $sql_where = "";
        if (isset($parametros['columna_proyecto'])) {
    		$sql_where .= $parametros["columna_proyecto"] . " = '".toba::get_hilo()->obtener_proyecto()."' ";
			if (isset($parametros["incluir_toba"]) && $parametros["incluir_toba"]) {
		        $sql_where .= " OR ".$parametros["columna_proyecto"]." = 'toba'";
			}
        }
		if ($sql_where != '') {
	        $where[] = "(" . $sql_where .")";
        	$parametros["sql"] =  stripslashes(sql_agregar_clausulas_where($parametros["sql"],$where));
		}
		foreach ($maestros as $id_maestro => $valor_maestro) {
			$parametros["sql"] = ereg_replace(apex_ef_cascada.$id_maestro.apex_ef_cascada, $valor_maestro,
												$parametros["sql"]);
		}
		$modo = ($es_posicional) ? apex_db_numerico : apex_db_asociativo;
		return toba::get_db($parametros['fuente'])->consultar($parametros['sql'], $modo);
	}
	
	protected function ef_metodo_carga_php($id_ef, $parametros, $maestros)
	{
		if (isset($parametros['include'])) {
			$instanciable = (isset($parametros['instanciable']) && $parametros['instanciable']=='1');
			require_once($parametros['include']);
			if ($instanciable) {
				$obj = new $parametros['clase']();
				$metodo = array($obj, $parametros['dao']);
			} else {
				$metodo = array($parametros['clase'], $parametros['dao']);
			}
			return call_user_func_array($metodo, $maestros);
		} else {
			//--- Es un metodo del CI contenedor
			return call_user_func_array( array($this->controlador, $parametros['dao']), $maestros);
		}
	}
	
	//-------------------------------------------------------------------------------
	//------------------------------	  SALIDA	  -------------------------------
	//-------------------------------------------------------------------------------

	function servicio__cascadas_efs()
	{
		require_once('3ros/JSON.php');				
		if (! isset($_GET['cascadas-ef']) || ! isset($_GET['cascadas-maestros'])) {
			throw new excepcion_toba("Cascadas: Invocación incorrecta");	
		}
		
		$id_ef = trim(toba::get_hilo()->obtener_parametro('cascadas-ef'));
		$maestros = array();
		foreach (explode('-|-', toba::get_hilo()->obtener_parametro('cascadas-maestros')) as $par) {
			if (trim($par) != '') {
				$param = explode("-;-", trim($par));
				if (count($param) != 2) {
					throw new excepcion_toba("Cascadas: Cantidad incorrecta de parametros ($par).");						
				}
				$id_ef_maestro = $param[0];
				$campos = $this->elemento_formulario[$id_ef_maestro]->get_dato();
				$valores = explode(apex_ef_separador, $param[1]);
				if (!is_array($campos)) {
					$maestros[$id_ef_maestro] = $param[1];
				} else {
					//--- Manejo de claves múltiples					
					if (count($valores) != count($campos)) {
						throw new excepction_toba("Cascadas: El ef $id_ef_maestro maneja distinta cantidad de datos que los campos pasados");
					}
					$valores_clave = array();
					for ($i=0; $i < count($campos) ; $i++) {
						$valores_clave[$campos[$i]] = $valores[$i];
					}
					$maestros[$id_ef_maestro] = $valores_clave;
				}
			}
		}
		toba::get_logger()->debug("Cascadas '$id_ef', Valores de los maestros: ".var_export($maestros, true));		
		$valores = $this->ejecutar_metodo_carga_ef($id_ef, $maestros);
		toba::get_logger()->debug("Cascadas '$id_ef', Respuesta: ".var_export($valores, true));				
		
		//--- Se arma la respuesta en formato JSON
		$json = new Services_JSON();
		echo $json->encode($valores);
	}
	
	function obtener_html()
	{
		//--- La carga de efs se realiza aqui para que sea contextual al servicio
		//--- ya que hay algunos que no lo necesitan (ej. cascadas)
		$this->cargar_valores_efs();		
		//Genero la interface
		echo "\n\n<!-- ***************** Inicio EI FORMULARIO (	".	$this->id[1] ." )	***********	-->\n\n";
		//Campo de sincroniacion con JS
		echo form::hidden($this->submit, '');
		$ancho = '';
		if (isset($this->info_formulario["ancho"])) {
			$ancho = convertir_a_medida_tabla($this->info_formulario["ancho"]);
		}
		echo "<table class='objeto-base' $ancho id='{$this->objeto_js}_cont'>";
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

	protected function generar_formulario()
	{
		if (editor::modo_prueba()) {
			$this->ancho_etiqueta = sumar_medida($this->ancho_etiqueta, 18);
		}
		$ancho = ($this->info_formulario['ancho'] != '') ? "style='width: {$this->info_formulario['ancho']}'" : '';
		echo "<div class='ei-formulario' $ancho>";
		$hay_colapsado = false;
		foreach ($this->lista_ef_post as $ef){
			if (! $this->elemento_formulario[$ef]->esta_expandido()) {
				$hay_colapsado = true;
			}
			$this->generar_envoltura_ef($ef);
		}
		if ($hay_colapsado) {
			$img = recurso::imagen_apl('expandir_vert.gif', false);
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_expansion();\" title='Mostrar / Ocultar'";
			echo "<div class='abm-fila abm-expansion'>";
			echo "<img id='{$this->objeto_js}_cambiar_expansion' src='$img' $colapsado>";
			echo "</div>";
		}
		echo "<div class='ei-base'>\n";
		$this->generar_botones();
		echo "</div>";
		echo "</div>\n";
	}
	
	protected function generar_envoltura_ef($ef)
	{
		$clase = 'abm-fila';
		$estilo_nodo = "";
		$id_ef = $this->elemento_formulario[$ef]->get_id_form();
		if (! $this->elemento_formulario[$ef]->esta_expandido()) {
			$clase = 'abm-fila-oculta';
			$estilo_nodo = "display:none";
		}
		
		if ($this->elemento_formulario[$ef]->tiene_etiqueta()) {
			echo "<div class='$clase' style='$estilo_nodo' id='nodo_$id_ef'>\n";
			$this->generar_etiqueta_ef($ef);
			//--- El margin-left de 0 y el heigth de 1% es para evitar el 'bug de los 3px'  del IE
			echo "<div id='cont_$id_ef' style='margin-left:{$this->ancho_etiqueta};_margin-left:0;_height:1%;'>\n";
			echo $this->elemento_formulario[$ef]->get_input();
			echo "</div>";
			echo "</div>\n";
		} else {		
			echo $this->elemento_formulario[$ef]->get_input();
		}
	}

	protected function generar_etiqueta_ef($ef)
	{
		$estilo = $this->elemento_formulario[$ef]->get_estilo_etiqueta();
		if ($estilo == '') {
	        if ($this->elemento_formulario[$ef]->es_obligatorio()) {
	    	        $estilo = 'ef-etiqueta-obligatorio';
					$marca = '(*)';
        	} else {
	            $estilo = 'ef-etiqueta';
				$marca ='';
    	    }
		}
		$desc = $this->elemento_formulario[$ef]->get_descripcion();
		if ($desc !=""){
			$desc = recurso::imagen_apl("descripcion.gif",true,null,null,$desc);
		}
		$id_ef = $this->elemento_formulario[$ef]->get_id_form();					
		$editor = $this->generar_vinculo_editor($ef);
		$etiqueta = $this->elemento_formulario[$ef]->get_etiqueta();
		//--- El _width es para evitar el 'bug de los 3px'  del IE
		echo "<label style='_width:{$this->ancho_etiqueta};' for='$id_ef' class='$estilo'>$editor $desc $etiqueta $marca</label>\n";
	}
	
	protected function generar_vinculo_editor($id_ef)
	{
		if (editor::modo_prueba()) {
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->id),
									'ef' => $id_ef );
			return editor::get_vinculo_subcomponente($this->item_editor, $param_editor);			
		}
		return null;
	}
		
	//-------------------------------------------------------------------------------
	//------------------------------ JAVASCRIPT  ------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		$rango_tabs = "new Array({$this->rango_tabs[0]}, {$this->rango_tabs[1]})";
		$esclavos = js::arreglo($this->cascadas_esclavos, true, false);
		$maestros = js::arreglo($this->cascadas_maestros, true, false);
		$id = js::arreglo($this->id, false);
		$invalidos = js::arreglo($this->efs_invalidos, true);
		echo $identado."window.{$this->objeto_js} = new objeto_ei_formulario($id, '{$this->objeto_js}', $rango_tabs, '{$this->submit}', $maestros, $esclavos, $invalidos);\n";
		foreach ($this->lista_ef_post as $ef) {
			echo $identado."{$this->objeto_js}.agregar_ef({$this->elemento_formulario[$ef]->crear_objeto_js()}, '$ef');\n";
		}
	}

	function get_objeto_js_ef($id)
	{
		return "{$this->objeto_js}.ef('$id')";
	}
	
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'clases/objeto_ei_formulario';
		//Busco las	dependencias
		foreach ($this->lista_ef_post	as	$ef){
			$temp	= $this->elemento_formulario[$ef]->get_consumo_javascript();
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
		$this->cargar_valores_efs();		
		$salida->subtitulo( $this->get_titulo() );
		echo "<table class='tabla-0' width='{$this->info_formulario['ancho']}'>";
		foreach ( $this->lista_ef_post as $ef){
			echo "<tr><td class='ef-etiqueta'>\n";
			echo $this->elemento_formulario[$ef]->get_etiqueta();
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
		$valor = $ef->get_descripcion_estado();
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
?>