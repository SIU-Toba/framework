<?
require_once("nucleo/persistencia/db_tablas.php");
/*
*	Relacion simple entre una cabecera y n detalles 
*/
class db_tablas_cd extends db_tablas
{
	protected $cabecera;
	protected $detalles;
	
	public function cargar($id)
	{
		$this->elemento[$this->cabecera]->cargar_datos_clave($id);
		foreach( array_keys($this->detalles) as $detalle ) {
			$this->elemento[$detalle]->cargar_datos_clave($id);
		}
		parent::cargar($id);
	}

	public function sincronizar_plan()
	{
		$this->elemento[$this->cabecera]->sincronizar();
		//Se obtiene el id de la cabecera
		$valores = $this->elemento[$this->cabecera]->get_clave_valor();
		//Se asigna cada valor al detalle
		foreach( $this->detalles as $id => $columna_clave ){
			$i = 0;
			foreach ($valores as $valor){
				$this->elemento[$id]->establecer_valor_columna( $columna_clave[$i] , $valor);
				$i++;
			}
			$this->elemento[$id]->sincronizar();
		}
	}
	
	public function eliminar_plan()
	{
		$detalles = array_reverse(array_keys($this->detalles));
		foreach( $detalles as $detalle ) {
			$this->elemento[$detalle]->eliminar_registros();
			$this->elemento[$detalle]->sincronizar();
		}
		$this->elemento[$this->cabecera]->eliminar_registros();
		$this->elemento[$this->cabecera]->sincronizar();		
	}
}
?>