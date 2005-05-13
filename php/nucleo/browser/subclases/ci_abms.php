<?
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_abms extends objeto_ci
{
	protected $filtro;
	protected $seleccion;
	protected $nueva_entidad=false;
	protected $dbr;
		
	function __construct($id)
	{
		parent::__construct($id);
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "filtro";
		$estado[] = "seleccion";
		$estado[] = "nueva_entidad";
		return $estado;
	}

	function obtener_dbr()
	{
		if (! isset($this->dbr)) {
			include_once( $this->info["parametro_a"]);
			$clase = $this->info['parametro_b'];
			//ATENCION: No se para que sirve el primer parametro de dbr
			$this->dbr = new $clase("dbr_".$this->id, $this->info['fuente']);
		}
		return $this->dbr;
	}
	
	
	function evt__limpieza_memoria()
	{
		parent::evt__limpieza_memoria(array("filtro"));
	}

	//--------------------------------------------------------------
	//--  EVENTOS Filtro
	//--------------------------------------------------------------
	function evt__filtro__filtrar($datos)
	{
		$this->filtro = $datos;
	}
	
	function evt__filtro__limpiar($datos)
	{
		unset( $this->filtro );
	}

	function evt__filtro__carga()
	{
		if(isset($this->filtro)){
			return $this->filtro;
		}
	}

	//--------------------------------------------------------------
	//--  EVENTOS Cuadro
	//--------------------------------------------------------------
/*		
	function evt__cuadro__carga()
	{

	}
*/	
	
	function evt__cuadro__seleccion($id)
	{
		$this->seleccion = $id;
	}

	//--------------------------------------------------------------
	//--  EVENTOS Formulario
	//--------------------------------------------------------------
	
		
	function evt__formulario__cancelar()
	{
		unset($this->seleccion);
	}	
	
	function evt__formulario__carga()
	{
		if (isset($this->seleccion)) {
			$dbr = $this->obtener_dbr();
			$dbr->cargar_registro_por_clave($this->seleccion);
			return $dbr->obtener_registro(0);
		}
	}
	
	
	function evt__formulario__alta($registro)
	{
		$dbr = $this->obtener_dbr();
		try {
			abrir_transaccion();
			$dbr->agregar_registro($registro);
			$dbr->sincronizar_db();
			cerrar_transaccion();
		}catch(excepcion_toba $e){
			abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}		
	}
	
	function evt__formulario__modificacion($registro)
	{
		$dbr = $this->obtener_dbr();
		try {
			abrir_transaccion();
			$dbr->modificar_registro($registro, 0);
			$dbr->sincronizar_db();
			cerrar_transaccion();
		} catch (excepcion_toba $e){
			abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}			
	}

	function evt__formulario__baja()
	{
		$dbr = $this->obtener_dbr();
		try {
			abrir_transaccion();
			$dbr->eliminar_registro(0);
			$dbr->sincronizar_db();
			cerrar_transaccion();
		} catch (excepcion_toba $e){
			abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}			
	}	
	
}
?>