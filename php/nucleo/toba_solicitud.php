<?php
require_once("nucleo/lib/toba_hilo.php");
require_once("nucleo/lib/toba_vinculador.php");

/**
 * Una solicitud es la representación de una operación o item en runtime
 * Contiene e instancia a los componentes de la operación
 */
abstract class toba_solicitud
{
	protected $id;							//ID de la solicitud	
	protected $info;							//Propiedeades	de	la	solicitud extraidas de la base
	protected $info_objetos;					//Informacion sobre los	objetos asociados	al	item
	protected $indice_objetos;				//Indice	de	objetos asociados	por CLASE
	protected $objetos = array();				//Objetos standarts asociados	al	ITEM
	protected $objetos_indice_actual = 0;		//Posicion actual	del array de objetos	
	protected $observaciones;					//Array de observaciones realizadas	durante la solicitud	
	protected $observaciones_objeto;			//Observaciones realizadas	por objetos	STANDART	
	protected $registrar_db;					//Indica	si	se	va	a registrar	la	solicitud
	protected $cronometrar;					//Indica	si	se	va	a registrar	el	cronometro de la solicitud	
	protected $log;							//Objeto que mantiene el log de la ejecucion

	function __construct($item, $usuario)	
	{
		//Le pregunto al HILO si se solicito cronometrar la PAGINA
		if(toba::get_hilo()->usuario_solicita_cronometrar()){
			$this->registrar_db = true;
			$this->cronometrar = true;
		}		
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		$this->item = $item;
		$this->usuario = $usuario;

		for($a=0;$a<count($this->info['objetos']);$a++){
			$indice = $this->info['objetos'][$a]["clase"];
			$this->indice_objetos[$indice][]=$a;
			$objetos[] = $this->info['objetos'][$a]["objeto"];	
		}

		$this->id =	toba_instancia::get_id_solicitud();

		//--- Identifico si la solicitud tiene que	realizar	observaciones
		if(isset($this->info['basica']['item_solic_obs_tipo'])){
			$tipo	= array($this->info['basica']['item_solic_obs_tipo_proyecto'],$this->info['basica']['item_solic_obs_tipo']);
			$this->observar($tipo,$this->info['basica']['item_solic_observacion'],false,false);
		}

		//--- Cargo los OBJETOS que se encuentran asociados
		$this->log = toba::get_logger();
		toba::get_cronometro()->marcar('SOLICITUD: Cargar	info ITEM',apex_nivel_nucleo);
	}
	

	/**
	 * Construye un componente y lo mantiene en un slot interno
	 *
	 * @param string $clase Nombre de la clase de componente
	 * @param int $posicion Posición del objeto en el item
	 * @param mixed $parametros
	 * @return int Indice o slot interno donde se almaceno el componente
	 */
	function cargar_objeto($clase,$posicion,$parametros=null)
	{
		toba::get_cronometro()->marcar('basura',apex_nivel_nucleo);
		//-[1]- El indice	es	valido?
		if(!isset($this->indice_objetos[$clase][$posicion])){	
			$this->observar(array("toba","error"),"SOLICITUD [obtener_id_objeto]: No EXISTE un OBJETO	asociado	al	indice [$clase][$posicion].",false,true,true);
			return -1;
		}
		$posicion =	$this->indice_objetos[$clase][$posicion];	
		$indice = $this->objetos_indice_actual;

		$clave['proyecto'] = $this->info['objetos'][$posicion]['objeto_proyecto'];
		$clave['componente'] = $this->info['objetos'][$posicion]['objeto'];
		$this->objetos[$indice] = toba_constructor::get_runtime( $clave, $clase );

		toba::get_cronometro()->marcar('SOLICITUD: Crear OBJETO	['. $this->info['objetos'][$posicion]['objeto']	.']',apex_nivel_nucleo);
		$this->objetos_indice_actual++;
		return $indice;
	}	
	
	/**
	 * Destruye los componentes asociados a la operación y el hilo
	 */
	function finalizar_objetos()
	{
		//--- Finalizo objetos TOBA ----------
		//echo "Empiezo a finalizar los objetos...<br>";
		for($a=0;$a<count($this->objetos);$a++){
			$this->objetos[$a]->destruir();
		}
		toba_cargador::instancia()->destruir();
		
		//--- Finalizo objetos BASICOS -------
		toba::get_hilo()->destruir();
		//dump_session();
	}	

	abstract function procesar();

	//------------------------------------------------------------------------
	//--------------------------  AUDITORIA Y	LOG --------------------------
	//------------------------------------------------------------------------

	/**
	 * @deprecated Esperando una refactorización en versiones futuras
	 */
	function registrar()	
	{
		if($this->registrar_db) {
			toba::get_cronometro()->marcar('SOLICITUD: Fin	del registro','nucleo');
			// Solicitud
			toba_instancia::registrar_solicitud(	$this->id, $this->info['basica']['item_proyecto'], 
												$this->info['basica']['item'], $this->get_tipo());
			// Cronometro
			if($this->cronometrar){	
				toba::get_cronometro()->registrar($this->id);
			}
			// Observaciones
			if(count($this->observaciones)>0) {
				for($i=0;$i<count($this->observaciones);$i++) {
					$tipo[0] = $this->observaciones[$i][0][0];
					$tipo[1] = $this->observaciones[$i][0][1];
					toba_instancia::registrar_solicitud_observaciones($this->id, $tipo, $this->observaciones[$i][1]);
				}
			}
		}
	}

	/**
	 * @deprecated Esperando una refactorización en versiones futuras
	 */
	function observar($tipo,$observacion,$forzar_registro=true,$mostrar=true,$cortar_ejecucion=false)
	{
		if(!is_array($tipo)){
			$tipo	= array("toba","error");
		}
		if($forzar_registro)	$this->registrar_db=true;
		if($mostrar){
			if( $this->get_tipo() =="consola"){
				echo $observacion	."\n";	
			}else {	
				echo ei_mensaje($observacion,$tipo);
			}
		}	
		$this->observaciones[] = array($tipo,$observacion);
		//ei_arbol($this->observaciones);
		if($cortar_ejecucion){
			//Corto la ejecucion	de	la	solicitud
			$this->registrar_db();
			exit();
		}
	}

	/**
	 * @deprecated Esperando una refactorización en versiones futuras
	 */
	function observar_objeto($objeto, $tipo, $observacion, $forzar_registro=true,	$mostrar=true,	$cortar_ejecucion=false)
	{
		if($forzar_registro)	$this->registrar_db=true;
		if($mostrar) echo	ei_mensaje($observacion,$tipo);
		$this->observaciones_objeto[]	= array($objeto,$tipo,$observacion);
		if($cortar_ejecucion){
			//Corto la ejecucion	de	la	solicitud
			$this->registrar_db();
			exit();
		}
	}

	//----------------------------------------------------------
	//-------------------------- Consultas --------------------------
	//----------------------------------------------------------

	function get_tipo()
	{
		return $this->info['basica']['item_solic_tipo'];
	}
	
	function es_item_publico()
	{
		return $this->info['basica']['item_publico'];	
	}
	
	function existe_ayuda()	
	{
		return (trim($this->info['basica']['item_existe_ayuda'])!="");	
	}
	
	/**
	* Retorna un arreglo de datos básicos del item que se esta ejecutando
	* @param string $prop Propiedad a obtener (opcional)
	*/
	function get_datos_item($prop=null)
	{
		if (isset($prop)) {
			return $this->info['basica'][$prop];	
		}
		return $this->info['basica'];	
	}
	
	/**
	 * @return array(proyecto, item)
	 */
	function get_id()
	{
		return $this->id;	
	}
	
	//----------------------------------------------------------
	//-------------------------- ZONA --------------------------
	//----------------------------------------------------------

	protected function crear_zona()
	{
		$clase = 'zona';
		if (trim($this->info['basica']['item_zona'])!="") {
			//--- Tiene subclase?
			if (isset($this->info['basica']['item_zona_archivo'])) {
				require_once($this->info['basica']['item_zona_archivo']);
				$clase = $this->info['basica']['item_zona'];
			}
			//--- Tiene consulta?
			$consulta = array(
					'archivo' => $this->info['basica']['zona_cons_archivo'],
					'clase' => $this->info['basica']['zona_cons_clase'],
					'metodo' => $this->info['basica']['zona_cons_metodo']
			);
			$this->zona = new $clase($this->info['basica']['item_zona'], $consulta);
		}
	}	
	
	/**
	 * @return zona
	 */
	function zona()
	{
		return $this->zona;
	}
	
	/**
	 * Hay una zona asignada y creada?
	 * @return boolean
	 */
	function hay_zona()
	{
		return isset($this->zona);	
	}

}
?>