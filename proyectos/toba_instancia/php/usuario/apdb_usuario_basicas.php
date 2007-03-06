<?
require_once("nucleo/componentes/persistencia/toba_ap_tabla_db.php");

class apdb_usuario_basicas extends toba_ap_tabla_db
{
	protected function evt__pre_update($id)
	{
		//Se trata de verificar que si la clave actual es una nueva o es similar a la anterior
		//Para encriptarla
		if ($this->datos[$id]['autentificacion'] != 'plano') {
			$usuario = $this->datos[$id]['usuario'];
			$sql = "SELECT clave, autentificacion FROM apex_usuario WHERE usuario = '$usuario'";
			$rs = toba::db($this->fuente)->consultar($sql);
			if ($rs[0]['clave'] != $this->datos[$id]['clave']
					|| $rs[0]['autentificacion'] != $this->datos[$id]['autentificacion']) {
				$this->encriptar_clave($id, $this->datos[$id]['autentificacion']);
			}
		}
	}
	
	protected function evt__pre_insert($id)
	{
		if ($this->datos[$id]['autentificacion'] != 'plano') {
			$this->encriptar_clave($id, $this->datos[$id]['autentificacion']);
		}		
	}
	
	protected function encriptar_clave($id, $metodo)
	{
		if ($metodo != 'md5') {
			$this->datos[$id]['clave'] = encriptar_con_sal($this->datos[$id]['clave'], $metodo);
		} else {
			$this->datos[$id]['clave'] = hash($metodo, $id);			
		}
	}
}	
?>