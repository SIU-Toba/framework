<?php

class apdb_usuario_basicas extends toba_ap_tabla_db_s
{
	protected function evt__pre_update($id)
	{
		//Se trata de verificar que si la clave actual es una nueva o es similar a la anterior
		//Para encriptarla
		if ($this->datos[$id]['autentificacion'] != 'plano') {
			$usuario = quote($this->datos[$id]['usuario']);
			$sql = "SELECT clave, autentificacion FROM apex_usuario WHERE usuario = $usuario";
			$rs = toba::db($this->_fuente)->consultar($sql);
			
			$cambio_clave = ($rs[0]['clave'] != $this->datos[$id]['clave']);
			$cambio_metodo = ($rs[0]['autentificacion'] != $this->datos[$id]['autentificacion']);
			if ($cambio_clave || $cambio_metodo) {				
				//Antes de encriptar la clave verifico que no se esta usando una clave anterior
				toba_usuario::verificar_clave_no_utilizada($this->datos[$id]['clave'], $this->datos[$id]['usuario']);				
				$this->datos[$id]['autentificacion'] = apex_pa_algoritmo_hash;		//Fijo el algoritmo por defecto, de manera que se vayan migrando las claves
				$this->encriptar_clave($id, $this->datos[$id]['autentificacion']);
			}
		}
	}
	
	protected function evt__pre_insert($id)
	{
		if ($this->datos[$id]['autentificacion'] != 'plano') {
			$this->datos[$id]['autentificacion'] = apex_pa_algoritmo_hash;						//Fijo el algoritmo por defecto, de manera que se vaya actualizando
			$this->encriptar_clave($id, $this->datos[$id]['autentificacion']);
		}		
	}
	
	protected function encriptar_clave($id, $metodo)
	{
		$hasher = new toba_hash();
		$this->datos[$id]['clave'] = $hasher->hash($this->datos[$id]['clave']);
	}
}
?>