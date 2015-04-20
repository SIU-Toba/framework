<?php
/**
 * La idea de esta rama de recursos es compartir informacion entre sistemas
 *	asumiendo que todos identifican a las personas con los atributos:
 *		- TIPO DOCUMENTO
 *		- NUMERO DOCUMENTO
 *  Supone que se usan tipos consistentes entre sistemas (ej: CUIT != DNI)
 *	Esta funcionalidad queda expuesta en el metodo:
 *		- buscarCompartidos($parametros) 
 */
class RDIServicioRecursoPersonal extends RDIServicioRecurso
{   
	const CARPETA_RECURSOS_PERSONALES = 'recursosPersonales';
	
	function getTipo()
	{
		return RDITipos::RECURSOPERSONAL;
	}
	
	function getParametrosObligatorios()
	{
		$parametros = parent::getParametrosObligatorios();
		$parametros['rdirp:tipoIdentificacion'] = 'tipoIdentificacion';
		$parametros['rdirp:numeroIdentificacion'] = 'numeroIdentificacion';
		return $parametros;
	}
	
    protected function definirUbicacion($parametros) 
    {		
		// Devuelve el ID de carpeta sobre la que se va a crear el recurso
		$tipoIdentificacion = $parametros['tipoIdentificacion'];
		$nroIdentificacion = $parametros['numeroIdentificacion']; 
        $dir_raiz = '/' . self::CARPETA_RECURSOS_PERSONALES;
        $dir_personal =  $tipoIdentificacion . $nroIdentificacion;
		$this->log('RECURSO PERSONAL',"Definir ubicacion ({$dir_raiz}/{$dir_personal})");
		try{
			// La carpeta de la persona ya existe
			$idCarpeta = $this->conector->getIdCarpeta($dir_raiz.'/'.$dir_personal);
			return $idCarpeta;
		} catch (RDIExcepcionObjetoNoEncontrado $ex) {
			try {
				// La carpeta raiz ya existe (es el primer recurso asociado la persona)
	            $idPadre = $this->conector->getIdCarpeta($dir_raiz);
				$idCarpeta = $this->conector->crearCarpeta($idPadre, $dir_personal);
				return $idCarpeta;
			} catch (RDIExcepcionObjetoNoEncontrado $ex) {
				// No existe ninguna carpeta (es el primer recurso personal guardado)
				$idBase = $this->conector->getIdCarpeta('/');
				$idPadre = $this->conector->crearCarpeta($idBase, self::CARPETA_RECURSOS_PERSONALES);
				$idCarpeta = $this->conector->crearCarpeta($idPadre, $dir_personal);
				return $idCarpeta;
			}	
        }
    }
	
	//--------------------------------------------------------------
	//-- Compartir recursos entre aplicaciones
	//--------------------------------------------------------------
	
	protected function definirColumnasRecordsetBusquedaCompartidos()
	{
		return array(
			RDIConector::ATRIBUTO_ID =>	$this->conector->atributo(RDIConector::ATRIBUTO_ID),
			'aplicacion' =>			'rdi:aplicacionOrigen',
			'instalacion' =>		'rdi:instalacionOrigen',
			'publicado' =>			'rdi:publicado',
			RDIConector::ATRIBUTO_CREACION => $this->conector->atributo(RDIConector::ATRIBUTO_CREACION),
		);		
	}	
	
    function buscarCompartidos($parametros) 
    {
		//Por definicion la busqueda de compartidos 
		// es por tipo y numero de identificacion
		if (!isset($parametros['tipoIdentificacion']) || 
			!isset($parametros['numeroIdentificacion'])	) {
			throw new RDIExcepcion("BuscarCompartidos: Es necesario definir los parametros 'tipoIdentificacion' y 'numeroIdentificacion'");
		}
		$tipo = $this->getTipo();
		$this->log('RECURSO PERSONAL',"Buscar recursos compartidos de tipo: $tipo");		
		$query = "	SELECT	{$this->conector->atributo(RDIConector::ATRIBUTO_ID)},
							rdi:aplicacionOrigen,
							rdi:instalacionOrigen,
							rdi:publicado,
							{$this->conector->atributo(RDIConector::ATRIBUTO_CREACION)}
					FROM	$tipo
					WHERE	rdirp:tipoIdentificacion = '{$parametros['tipoIdentificacion']}'
					AND		rdirp:numeroIdentificacion = '{$parametros['numeroIdentificacion']}'
					AND		(rdi:publicado = '1' 
								OR	(rdi:aplicacionOrigen = '{$this->sistema}' 
								AND rdi:instalacionOrigen = '{$this->instalacion}'))
					ORDER BY {$this->conector->atributo(RDIConector::ATRIBUTO_CREACION)} DESC";
		$cols = $this->definirColumnasRecordsetBusquedaCompartidos();
		return $this->conector->consultaDirecta($query, $cols);
    }
}
