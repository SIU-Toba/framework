<?php
/**
 * Clase que devuelve un cliente para conectarse a un ECM
 * @package Centrales
 */
class toba_cliente_rdi
{
	const nombre_archivo = '/rdi.ini';

	protected $proyecto;
	protected $clienteRdi;
	protected $instalacion;

	function __construct()
	{
	}

	/**
	 * Permite cambiar el proyecto del cliente RDI
	 * @param toba_proyecto $obj_proyecto
	 */
	function set_proyecto(toba_proyecto $obj_proyecto)
	{
		//Si hay cambio de proyecto, elimino la instancia de RDICliente
		if (isset($this->proyecto) && ($this->proyecto->get_id() != $obj_proyecto->get_id())) {
			unset($this->clienteRdi);
		}
		$this->proyecto = $obj_proyecto;
	}

	function set_instalacion($obj_instalacion)
	{
		$this->instalacion = $obj_instalacion;
	}

	/**
	 * Devuelve una instancia de RDICliente para poder trabajar contra el ECM
	 * @throws toba_error
	 * @return \RDICliente
	 */
	function get_cliente()
	{
		if (! isset($this->clienteRdi)) {
			$this->clienteRdi = $this->instanciar_cliente();
		}
		return $this->clienteRdi;
	}

    //------------------------------------------------------------------------//
    //						METODOS INTERNOS
    //------------------------------------------------------------------------//
	/**
	 * Instancia el cliente RDI
	 * @return \RDICliente
	 * @throws toba_error
	 * @ignore
	 */
	protected function instanciar_cliente()
	{
		$id_proyecto = $this->proyecto->get_id();

                if (! \class_exists('\RDICliente')) {
                    throw new toba_error("Falta incorporar el paquete 'siu/rdi' al proyecto");
                }

		if (! toba::config()->existe_valor('rdi', null, $id_proyecto)) {
			throw new toba_error('Falta el archivo de configuración rdi.ini o no se encuentra la seccion del proyecto');
		}

		$parametros = toba::config()->get_subseccion('rdi', $id_proyecto);
		$nombre = $this->instalacion->get_nombre();
		if ((trim($nombre) == '') && (! isset($parametros['instalacion']))) {
			throw new toba_error('Falta especificar el nombre de la instalacion en el archivo instalacion.ini');
		}
		$nombre_inst = (trim($nombre) != '') ? $nombre : $parametros['instalacion'];
		$rdi = new \RDICliente($parametros['conector'],
							$parametros['repositorio'],
							$parametros['usuario'],
							$parametros['clave'],
							$id_proyecto,
							$nombre_inst);

		//Agrego un log para desarrollo
		if (! $this->instalacion->es_produccion()) {
			$log = new toba_logger_rdi($id_proyecto);
			$log->set_activo(true);
			$rdi->asociarLog($log);
		}

		//Reviso si existen servicios redefinidos y los asigno
		$serv_personalizados = toba::proyecto()->get_parametro('servicios_rdi', null, false);
		if (! is_null($serv_personalizados)) {
			foreach($serv_personalizados as $servicio => $clase) {
				try {
					$rdi->mapeoServicios()->redefinir($servicio, $clase);
				} catch (RDIExcepcion $ex) {
					$rdi->mapeoServicios()->agregar($servicio, $clase);
				}
			}
		}

		return $rdi;
	}
}
?>