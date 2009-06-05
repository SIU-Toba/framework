<?php

class apdb_usuario_basicas extends toba_ap_tabla_db
{
	protected function evt__pre_update($id)
	{
		//Se trata de verificar que si la clave actual es una nueva o es similar a la anterior
		//Para encriptarla
		if ($this->datos[$id]['autentificacion'] == 'md5') {
			$usuario = $this->datos[$id]['usuario'];
			$sql = 'SELECT clave, autentificacion FROM apex_usuario WHERE usuario = '.quote($usuario);
			$rs = toba::db($this->fuente)->consultar($sql);
			if ($rs[0]['clave'] != $this->datos[$id]['clave']
					|| $rs[0]['autentificacion'] != 'md5') {
				$this->encriptar_clave($id);
			}
		}
	}
	
	protected function evt__pre_insert($id)
	{
		if ($this->datos[$id]['autentificacion'] == 'md5') {
			$this->encriptar_clave($id);
		}		
	}
	
	protected function encriptar_clave($id)
	{
		$this->datos[$id]['clave'] = md5($this->datos[$id]['clave']);
	}
}	
?>