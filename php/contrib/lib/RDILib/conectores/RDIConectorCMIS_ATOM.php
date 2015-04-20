<?php
require_once('CMIS_ATOM/cmis_service.php');

class RDIConectorCMIS_ATOM extends RDIConectorCMIS
{
    function crearConexion()
    {
		$this->log('CONECTOR','Crear conexion');
		try {
			return new CMISService($this->url, $this->usuario, $this->clave);
		} catch (CmisException $ex) {
            throw new RDIExcepcion("Error conectandose al Repositorio (URL: $this->url USUARIO: $this->usuario CLAVE: ******)");
        }		
    }    
}
