<?php
/**
 * Este componente permite unificar la carga y entrega de datos y servicios a una jerarquia completa de componentes de interface (especialmente a los cis)
 * 
 * Separar la carga y utilización de los datos (inicio y fin de una transaccion de negocios)
 * permite:
 *  -Lograr una maxima independencia entre la logica de pantalla y de la de negocio, 
 *  -Tener un lugar centralizado para brindar servicios comunes a una jerarquia de componentes
 * Estas flexibilidad se consigue a expensas de una mayor burocracia y complejidad en el manejo de datos.
 * @package Componentes
 * @subpackage Negocio
 */
class toba_cn extends toba_componente
{
	protected $_transaccion_abierta;			//Indica si la transaccion se encuentra en proceso

	function __construct($id)
	{
		parent::__construct($id);
		$this->_transaccion_abierta = false;
		$this->ini();
	}

	/**
	 * Ventana de extensión que se ejecuta al iniciar el componente en todos los pedidos en los que participa.
	 * @ventana
	 */
	function ini()
	{
	}	

	/**
	 * Evento que se dispara cuando se limpia la memoria
	 */
	function evt__limpieza_memoria($no_borrar=null)
	{
		$this->_log->debug( $this->get_txt() . "[ evt__limpieza_memoria ]", 'toba');
		$this->eliminar_estado_sesion($no_borrar);
		$this->ini();
	}


	//-------------------------------------------------------------------------------
	//------------------  PROCESAMIENTO  --------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Limpia la memoria propia
	 */
	function cancelar()
	{
		$this->_log->debug( $this->get_txt() . "[ cancelar ]", 'toba');
		$this->evt__limpieza_memoria();
	}

	/**
	 * El procesamiento se dispara cuando la entrega de datos ci->cn ha finalizado
	 * Se inicia una transaccion de base de datos y dentro de ella se llama a :
	 *  - {@link evt__validar_datos() evt__validar_datos} 
	 *  - {@link evt__procesar_especifico() evt__procesar_especifico}
	 *  Una vez terminada la transacción se invoca a la limpieza de memoria
	 * 
	 * @todo Ver la posibilidad de usar ignore_user_abort() para evitar problemas con medios no transaccionales
	 */
	function procesar($parametros=null)
	{
		$resultado = null;
		$this->_log->debug( $this->get_txt() . "[ procesar ]", 'toba');
		try {
			//ignore_user_abort();				//------> ?????
			$this->iniciar_transaccion();
			$this->evt__validar_datos();
			$resultado = $this->evt__procesar_especifico($parametros);
			$this->finalizar_transaccion();
			$this->evt__limpieza_memoria();
			return $resultado;
		}
		catch(toba_error $e){
			$this->abortar_transaccion();
			$this->_log->debug($e, 'toba');	
			throw new toba_error( $e->getMessage() );
		}
	}

	/**
	 * Ventana de validacion que se ejecuta al inicio del procesamiento final
	 * En caso de querer abortar el procesamiento lanzar una excepcion que herede de toba_error
	 * @ventana
	 */
	function evt__validar_datos()
	{}

	/**
	 * Ventana para incluir el procesamiento de negocio
	 * En caso de querer abortar el procesamiento lanzar una excepcion que herede de toba_error
	 * @ventana
	 */
	function evt__procesar_especifico()
	{}

	//-------------------------------------------------------------------------------
	//------------------  Manejo de TRANSACCIONES  ----------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @see toba_db::abrir_transaccion()
	 */
	function iniciar_transaccion()
	{
		$this->_transaccion_abierta = true;
		return toba::db($this->_info['fuente'])->abrir_transaccion();
	}
	
	/**
	 * @see toba_db::cerrar_transaccion()
	 */	
	function finalizar_transaccion()
	{
		$this->_transaccion_abierta = false;
		return toba::db($this->_info['fuente'])->cerrar_transaccion();
	}

	/**
	 * @see toba_db::abortar_transaccion()
	 */		
	function abortar_transaccion()
	{
		$this->_transaccion_abierta = false;
		return toba::db($this->_info['fuente'])->abortar_transaccion($sql);
	}

	/**
	 * @see toba_db::ejecutar()
	 */			
	function ejecutar_sql($sentencias_sql)
	{
		if($this->_transaccion_abierta){
			if(!is_array($sentencias_sql)){
				$sentencias_sql = array($sentencias_sql);
			}
			foreach($sentencias_sql as $sql){
				toba::db($this->_info['fuente'])->ejecutar($sql);
			}
		}else{
			throw new toba_error('La transaccion no se encuentra abierta');
		}
	}
}
?>