<?php
require_once('admin/objetos_toba/ci_editores_toba.php');

class ci_editor extends ci_editores_toba
{

	protected $seleccion_pantalla;
	protected $seleccion_pantalla_anterior;
	protected $pantalla_dep_asoc;
	protected $pantalla_evt_asoc;
	protected $cambio_objeto = false;		//Se esta editando un nuevo objeto?
	private $id_intermedio_pantalla;
	protected $seleccion_externa_pantalla;

	function __construct($id)
	{
		parent::__construct($id);
		$pantalla = toba::get_hilo()->obtener_parametro('pantalla');
		//¿Se selecciono una pantalla desde afuera?
		if (isset($pantalla)) {
			$this->seleccion_externa_pantalla = true;
			//Se busca cual es el id interno del ML para enviarselo
			$datos = $this->evt__pantallas_lista__carga();
			foreach ($datos as $id => $dato) {
				if ($dato['identificador'] == $pantalla) {
					$this->evt__pantallas_lista__seleccion($id);
				}
			}
		}
	}
	
	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_entidad()->tabla('pantallas')->info(true),"PANTALLAS");
		//ei_arbol($this->get_entidad()->tabla('eventos')->info(true),"PANTALLAS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_pantalla";
		$propiedades[] = "seleccion_pantalla_anterior";
		$propiedades[] = "pantalla_dep_asoc";
		$propiedades[] = "pantalla_evt_asoc";
		return $propiedades;
	}

	function get_etapa_actual()
	{
		$pantalla = parent::get_etapa_actual();
		if (isset($this->seleccion_externa_pantalla)) {
			return "2";	//Si se selecciono una pantalla desde afuera va hacia ella
		} 
		return $pantalla;
	}

	// *******************************************************************
	// ******************* tab PROPIEDADES BASICAS  **********************
	// *******************************************************************

	function evt__base__carga()
	{
		return $this->get_entidad()->tabla("base")->get();
	}

	function evt__base__modificacion($datos)
	{
		$this->get_entidad()->tabla("base")->set($datos);
	}

	function evt__prop_basicas__carga()
	{
		return $this->get_entidad()->tabla("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
	}

	// *******************************************************************
	// *******************  tab DEPENDENCIAS  ****************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/
	function evt__salida__1()
	{
		$this->dependencias['dependencias']->limpiar_seleccion();
	}

	function get_dbr_dependencias()
	{
		return $this->get_entidad()->tabla('dependencias');
	}
	
	function evt__dependencias__del_dep($id)
	{
		//El ci de dependencias avisa que se borro la dependencias $id
		$this->get_entidad()->tabla('pantallas')->eliminar_dependencia($id);
	}
	
	/**
	*	El ci de dependencias avisa que una dependencia cambio su identificacion
	*/
	function evt__dependencias__mod_id($anterior, $nuevo)
	{
		//Este cambio se le notifica a las pantallas
		$this->get_entidad()->tabla('pantallas')->cambiar_id_dependencia($anterior, $nuevo);
	}	
	
	
	// *******************************************************************
	// ******************* tab PANTALLAS  ********************************
	// *******************************************************************
	
	function evt__entrada__2()
	{
		//--- Armo la lista de DEPENDENCIAS disponibles
		$this->pantalla_dep_asoc = array();
		if($registros = $this->get_entidad()->tabla('dependencias')->get_filas())
		{
			foreach($registros as $reg){
				$this->pantalla_dep_asoc[ $reg['identificador'] ] = $reg['identificador'];
			}
		}
		//--- Armo la lista de EVENTOS disponibles
		$this->pantalla_evt_asoc = array();
		if($registros = $this->get_entidad()->tabla('eventos')->get_filas())
		{
			foreach($registros as $reg){
				$this->pantalla_evt_asoc[ $reg['identificador'] ] = $reg['identificador'];
			}
		}
	}

	function get_lista_ei__2()
	{
		$ei[] = "pantallas_lista";
		if( isset($this->seleccion_pantalla) ){
			$ei[] = "pantallas";
			if( count($this->pantalla_dep_asoc) > 0 ){
				$ei[] = "pantallas_ei";			
			}
			if( count($this->pantalla_evt_asoc) > 0 ){
				$ei[] = "pantallas_evt";			
			}
		}
		return $ei;	
	}

	/*
		Hay que mejorar el layout
	*/
	function obtener_html_contenido__2()
	{
		$this->dependencias['pantallas_lista']->obtener_html();			
		if( isset($this->seleccion_pantalla) ){
			$datos = $this->get_entidad()->tabla('pantallas')->get_fila($this->seleccion_pantalla);
			$nombre_pantalla = "Propiedades de la pantalla '{$datos['etiqueta']}' [{$datos['identificador']}]";
			echo "<fieldset style='margin-top: 10px; padding: 7px; FONT-WEIGHT: bold; FONT-SIZE: 12px; COLOR: #333333;'><legend>$nombre_pantalla</legend>";			
			$this->dependencias['pantallas']->obtener_html();			
			echo "<table class='tabla-0'  width='100%'>";
			echo "<tr>\n";
			if( count($this->pantalla_dep_asoc) > 0 ){
				echo "<td width='50%' style='vertical-align: top'>\n";
				echo "<fieldset style='margin: 5px;padding: 5px; FONT-SIZE: 10px; COLOR: #990000;'><legend>Dependencias Asociadas</legend>";			
				$this->dependencias['pantallas_ei']->obtener_html();			
				echo "</fieldset>";
				echo "</td>\n";
			}
			if( count($this->pantalla_evt_asoc) > 0 ){
				echo "<td style='vertical-align: top'>\n";
				echo "<fieldset style='margin: 5px; padding: 5px; FONT-SIZE: 10px; COLOR: #990000;'><legend>Eventos Asociados</legend>";			
				$this->dependencias['pantallas_evt']->obtener_html();			
				echo "</fieldset>";
				echo "</td>\n";
			}
			echo "</tr>\n";
			echo "</table>";
			$this->generar_boton_evento('aceptar_pantalla');
			$this->generar_boton_evento('cancelar_pantalla');
			echo "</fieldset>";
		}
	}

	//Antes de cargar los datos de las pantallas, ver si alguno particular se selecciono desde afuera
	function evt__pre_cargar_datos_dependencias__2()
	{
		if (isset($this->seleccion_pantalla)) {
			$this->dependencias['pantallas_lista']->seleccionar($this->seleccion_pantalla);
		}
	}
			
	function evt__post_cargar_datos_dependencias__2()
	{
		if( isset($this->seleccion_pantalla) ){
			//Protejo la evento seleccionada de la eliminacion
			$this->dependencias["pantallas_lista"]->set_fila_protegida( $this->seleccion_pantalla );
		}
	}

	function evt__salida__2()
	{
		unset($this->seleccion_pantalla_anterior);
		unset($this->seleccion_pantalla);
	}

	function evt__cancelar_pantalla()
	{
		unset($this->seleccion_pantalla_anterior);
		unset($this->seleccion_pantalla);
	}

	function evt__aceptar_pantalla()
	{
		unset($this->seleccion_pantalla_anterior);
		unset($this->seleccion_pantalla);
	}

	//----------------------------------------------------------
	//-- Lista -------------------------------------------------
	//----------------------------------------------------------
	
	function evt__pantallas_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una columna de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las columnas NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		$dbr = $this->get_entidad()->tabla("pantallas");
		$orden = 1;
		foreach(array_keys($registros) as $id)
		{
			//Creo el campo orden basado en el orden real de las filas
			$registros[$id]['orden'] = $orden;
			$orden++;
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch($accion){
				case "A":
					$this->id_intermedio_pantalla[$id] = $dbr->nueva_fila($registros[$id]);
					break;	
				case "B":
					$dbr->eliminar_fila($id);
					break;	
				case "M":
					$dbr->modificar_fila( $id, $registros[$id]);
					break;	
			}
		}		
	}
	
	function evt__pantallas_lista__carga()
	{
		if($datos_dbr = $this->get_entidad()->tabla('pantallas')->get_filas() )
		{
			//Ordeno los registros segun la 'posicion'
			//ei_arbol($datos_dbr,"Datos para el ML: PRE proceso");
			for($a=0;$a<count($datos_dbr);$a++){
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC , $datos_dbr);
			//EL formulario_ml necesita necesita que el ID sea la clave del array
			// No se solicita asi del DBR porque array_multisort no conserva claves numericas
			// y las claves internas del DBR lo son
			for($a=0;$a<count($datos_dbr);$a++){
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset( $datos_dbr[$a][apex_db_registros_clave] );
				$datos[ $id_dbr ] = $datos_dbr[$a];
			}
			//ei_arbol($datos,"Datos para el ML: POST proceso");
			return $datos;
		}
	}

	function evt__pantallas_lista__seleccion($id)
	{
		if(isset($this->id_intermedio_pantalla[$id])){
			$id = $this->id_intermedio_pantalla[$id];
		}
		$this->seleccion_pantalla = $id;
	}

	//------------------------------------------------------
	//-- Informacion extendida de la pantalla  -------------
	//------------------------------------------------------

	function evt__pantallas__modificacion($datos)
	{
		$this->get_entidad()->tabla('pantallas')->modificar_fila($this->seleccion_pantalla_anterior, $datos);
	}
	
	function evt__pantallas__carga()
	{
		$this->seleccion_pantalla_anterior = $this->seleccion_pantalla;
		return $this->get_entidad()->tabla('pantallas')->get_fila($this->seleccion_pantalla_anterior);
	}

	//------------------------------------------------------
	//--- Asociacion de DEPENDENCIAS a pantallas  ----------
	//------------------------------------------------------

	function evt__pantallas_ei__carga()
	{
		if( $deps = $this->get_entidad()->tabla('pantallas')->get_dependencias_pantalla($this->seleccion_pantalla_anterior) )
		{
			$a=0;
			$datos = null;
			foreach($deps as $dep){
				if(in_array($dep, $this->pantalla_dep_asoc)){
					$datos[$a]['dependencia'] = $dep;
					$a++;	
				}
			}
			return $datos;
		}
		//return array();
	}

	function evt__pantallas_ei__modificacion($datos)
	{
		$deps = array();
		foreach($datos as $dato){
			$deps[] = $dato['dependencia'];
		}
		$this->get_entidad()->tabla('pantallas')->set_dependencias_pantalla($this->seleccion_pantalla_anterior, $deps);
	}

	function combo_dependencias()
	{
		$datos = null;
		$a=0;
		foreach( $this->pantalla_dep_asoc as $dep => $info){
			$datos[$a]['id'] = $dep; 
			$datos[$a]['desc'] = $info; 
			$a++;
		}
		return $datos;
	}

	//------------------------------------------------------
	//--- Asociacion de EVENTOS a pantallas  ---------------
	//------------------------------------------------------

	function evt__pantallas_evt__carga()
	{
		$eventos_asociados = $this->get_entidad()->tabla('pantallas')->get_eventos_pantalla($this->seleccion_pantalla_anterior);
		$datos = null;
		$a=0;
		foreach( $this->pantalla_evt_asoc as $dep){
			$datos[$a]['evento'] = $dep; 
			if(is_array($eventos_asociados)){
				if(in_array($dep, $eventos_asociados)){
					$datos[$a]['asociar'] = 1;
				}else{
					$datos[$a]['asociar'] = 0;
				}
			}else{
				$datos[$a]['asociar'] = 0;
			}
			$a++;
		}
		return $datos;
	}

	function evt__pantallas_evt__modificacion($datos)
	{
		$eventos = array();
		foreach($datos as $dato){
			if($dato['asociar'] == "1")	$eventos[] = $dato['evento'];
		}
		$this->get_entidad()->tabla('pantallas')->set_eventos_pantalla($this->seleccion_pantalla_anterior, $eventos);
	}
	
	/**
	*	Se modifica el identificador de un evento, esto afecta a todas las pantallas en la que esta incluído
	*/
	function evt__eventos__mod_id($id_anterior, $id_nuevo)
	{
		$this->get_entidad()->tabla('pantallas')->cambiar_id_evento($id_anterior, $id_nuevo);
	}

	// *******************************************************************
	// *******************  tab EVENTOS  *********************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/

	function evt__salida__3()
	{
		$this->dependencias['eventos']->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}

	function get_modelos_evento()
	{
		require_once('api/elemento_objeto_ci.php');
		return elemento_objeto_ci::get_modelos_evento();
	}

	function get_eventos_estandar($modelo)
	{
		require_once('api/elemento_objeto_ci.php');
		return elemento_objeto_ci::get_lista_eventos_estandar($modelo);
	}

	function evt__eventos__del_evento($id)
	{
		//El ci de EVENTOS avisa que se borro el evento $id
		$this->get_entidad()->tabla('pantallas')->eliminar_evento($id);
	}
	
	/**
	 * Se actualiza la aparicion de un evento en todas las pantallas
	 *
	 * @param array $pant_presentes Pantallas en las que el evento aparece
	 * @param string $evento Identificador del evento (ej: procesar)
	 */
	function set_pantallas_evento($pant_presentes, $evento)
	{
		$this->get_entidad()->tabla('pantallas')->set_pantallas_evento($pant_presentes, $evento);
	}
	
	/**
	 * Retorna las pantallas en las que esta incluido el evento
	 */
	function get_pantallas_evento($evento)
	{
		return $this->get_entidad()->tabla('pantallas')->get_pantallas_evento($evento);
	}
	
	function get_pantallas_posibles()
	{
		$pantallas = $this->get_entidad()->tabla('pantallas')->get_filas();
		//Se contruye un nombre mas completo
		foreach (array_keys($pantallas) as $pant) {
			$pantallas[$pant]['nombre'] = '('.$pantallas[$pant]['identificador'].') '.
											$pantallas[$pant]['etiqueta'];
		}
		return $pantallas;
	}
	
	// *******************************************************************
	// *******************  tab PHP  *************************************
	// *******************************************************************

	function obtener_html_contenido__4()
	{	
		require_once('nucleo/lib/reflexion/archivo_php.php');
		require_once('nucleo/lib/reflexion/clase_php.php');
		require_once('admin/db/dao_editores.php');

		$registro = $this->get_entidad()->tabla('base')->get();
		
		$subclase = $registro['subclase'];
		$subclase_archivo = $registro['subclase_archivo'];
		$clase = $registro['clase'];
		$clase_archivo = dao_editores::get_archivo_de_clase($registro['clase_proyecto'], $clase );
		$proyecto = $this->id_objeto['proyecto'];

	    $path = toba::get_hilo()->obtener_proyecto_path() . "/php/" . $subclase_archivo;
		//Manejo de archivos            
		$archivo_php = new archivo_php($path);
		//Manejo de clases
		$clase_php = new clase_php($subclase, $archivo_php, $clase, $clase_archivo);
		$clase_php->set_objeto( $this->id_objeto['proyecto'], $this->id_objeto['objeto']);
		if($archivo_php->existe()){	
			ei_separador("ARCHIVO: ".	$archivo_php->nombre());
			$archivo_php->incluir();
			$clase_php->analizar();	
		}	
	}		

	// *******************************************************************
	// *******************  tab COMPOSICION  *****************************
	// *******************************************************************
	
	
	// *******************************************************************
	// *******************  PROCESAMIENTO  *******************************
	// *******************************************************************
	
	function evt__procesar()
	{
		if (!$this->cargado) {
			//Seteo los datos asociados al uso de este editor
			$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
			$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase_proyecto", "toba" );
			$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase", "objeto_ci" );
		}
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();
	}
	// *******************************************************************
}
?>