<?php
require_once('modelo/consultas/dao_editores.php');
require_once("nucleo/componentes/constructor_toba.php");

class clonador_objetos
{
	protected $datos_relacion;
	
	function cargar_db($id_origen)
	{
		//Se busca la clase del objeto
		$clase = dao_editores::get_clase_de_objeto($id_origen);
		
		//Se busca el id del datos_relacion de la clase
		$id_dr = dao_editores::get_dr_de_clase($clase);
		
		//Se construye el objeto datos_relacion
		$componente = array('proyecto' => $id_dr[0], 'componente' => $id_dr[1]);
		$this->datos_relacion = constructor_toba::get_runtime($componente);
		$this->datos_relacion->conectar_fuente();
		$this->datos_relacion->configuracion();
		
		//Se carga con el id_origen
		$this->datos_relacion->cargar(array('proyecto' => $id_origen[0], 'objeto' => $id_origen[1]));
	}
	
	
	/**
	*	Hace una replica exacta del objeto cargado, no se propaga por las dependencias
	*	@param string Nombre a asignar al clon
	*	@return array Clave del clon
	*/
	function clonar($nuevo_nombre, $en_transaccion=true)
	{
		if (!$en_transaccion) {
			$this->datos_relacion->get_persistidor()->desactivar_transaccion();	
		}
		$this->datos_relacion->tabla('base')->set_fila_columna_valor(0, 'nombre', $nuevo_nombre);
				
		//Se le fuerza una inserción a los datos_tabla
		//Como la clave de los objetos son secuencias, esto garantiza claves nuevas
		$this->datos_relacion->forzar_insercion();
		$this->datos_relacion->sincronizar();
		
		//Se busca la clave del nuevo objeto
		$clave = $this->datos_relacion->tabla('base')->get_clave_valor(0);
		return $clave;
	}
}

?>