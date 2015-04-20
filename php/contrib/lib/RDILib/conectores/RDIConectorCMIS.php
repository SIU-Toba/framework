<?php

abstract class RDIConectorCMIS extends RDIConector
{	
	function establecerAtributosBasicos()
	{
		$this->atributos[RDIConector::ATRIBUTO_ID]			= 'cmis:versionSeriesId';
		$this->atributos[RDIConector::ATRIBUTO_NOMBRE]		= 'cmis:name';
		$this->atributos[RDIConector::ATRIBUTO_CREACION]	= 'cmis:creationDate';
		$this->atributos[RDIConector::ATRIBUTO_ID_VERSION]	= 'cmis:objectId';
		$this->atributos[RDIConector::ATRIBUTO_VERSION]		= 'cmis:versionLabel';
	}
	
	function infoConexion() 
    {
        return $this->conexion->getRepositoryInfo();
    }
	
	function controlServidorPoseeTipos($tiposBasicos)			
    {
        return true;
		/**
		 * @todo
		 *	Dejar la respuesta en un cache local asociado a los parametros del server.
		 *  Mejorar mensaje de la excepcion lanzada cuando no encuentra el tipo ancestro
		 */
		$ancestro = RDITipos::getAncestroTipos();
		try {
			$descendientes = $this->conexion->getTypeDescendants($ancestro, -1);
		} catch(CmisObjectNotFoundException $ex) {									//No existe ni el ancestro de todos los tipos SIU-RDI.
			//$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
			//throw new RDIExcepcion('Faltan los tipos basicos para los elementos '.$txtCMIS); 
			return false;															//El llamador se encarga del msg por ahora
		}
		return $this->hayTiposBasicos($descendientes, $tiposBasicos);
    }
	
	protected function hayTiposBasicos($descendientes, $basicos)
	{
		if (empty($descendientes->objectsById)) {
			return false;
		}
		$tipos = array_keys($descendientes->objectsById);
		$faltantes = array_diff($tipos, $basicos);
		return empty($faltantes);
	}
	
    //--------------------------------------------------
    //-- Documentos
    //--------------------------------------------------        

    function crearDocumento($idCarpeta, $nombre, $tipo, $props, $contenido, $mimeType) 
	{
		$this->log('CONECTOR','Crear documento: '. $nombre);
		try {
			//$options = array('versioningState'=>'major');
			$options=array();
			$obs = $this->conexion->createDocumentWithType($idCarpeta, $nombre, $tipo, $props, $contenido, $mimeType, $options);
			if(!isset($obs->id))  {
				throw CmisException('El server no devolvio un ID');
			}
			return $obs->id;
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error crear documento: $txtCMIS");
        }
	}	
	
	function recuperarAtributos($idRecurso)
	{
		$this->log('CONECTOR','getObject: ' . $idRecurso);		
		try {
			return $this->conexion->getObject($idRecurso);
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error getObject: $txtCMIS");
        }		
	}	
	
	function recuperarContenido($idRecurso)
	{
		$this->log('CONECTOR','Recuperar contenido documento: ' . $idRecurso);		
		try {
			return $this->conexion->getContentStream($idRecurso);
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error recuperar contenido: $txtCMIS");
        }			
	}
	
    function modificarAtributos($idRecurso, $atributos)
    {
		$this->log('CONECTOR',"Modificar atributos: . $idRecurso\n" . var_export($atributos,true));		
		try {
			$this->conexion->updateProperties($idRecurso, $atributos);
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error modificar atributos: $txtCMIS");
        }
    }	
	
    function modificarContenido($idRecurso, $contenido, $mimeType)
    { 
		$this->log('CONECTOR','Modificar contenido documento: ' . $idRecurso);	
		try {
			$this->conexion->setContentStream($idRecurso, $contenido, $mimeType);
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error Modificar contenido: $txtCMIS");
        }
    }	
	
    function checkOut($idRecurso)
    {     
		$this->log('CONECTOR','CheckOUT documento: ' . $idRecurso);
		try {
			$this->conexion->checkOut($idRecurso);
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error checkOut: $txtCMIS");
        }		
    }
	
    function cancelCheckOut($idRecurso)
    {     
		$this->log('CONECTOR','CANCEL CheckOUT documento: ' . $idRecurso);
		try {
			/*
			 * Atencion! la implementacion actual del checkout elimina el recurso
			 * si se hace un cancel checkout de un objeto que no tenia un checkout hecho.
			 * por ese motivo se hace una consulta antes confirmando que este chequeado
			 * para que no haya problemas.
			 */
			$elemento = $this->recuperarAtributos($idRecurso);
			//Si, el booleano a controlar viene como un string!
			if($elemento->properties['cmis:isVersionSeriesCheckedOut'] === 'true') {
				$this->conexion->cancelCheckOut($idRecurso);
			}
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error CANCEL checkOut: $txtCMIS");
        }		
    }
	
    function checkIn($idRecurso)
    {
		$this->log('CONECTOR','CheckIN documento: ' . $idRecurso);		
		try {
			$propiedades = array();
			$this->conexion->checkIn($idRecurso, array());
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error checkIn: $txtCMIS");
        }	
    }		
	
    function eliminarDocumento($idRecurso)
    {        
		$this->log('CONECTOR','ELIMINAR documento: ' . $idRecurso);
		try {
			$this->conexion->deleteObject($idRecurso);
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error borrar: $txtCMIS");
        }
    }		
	
    //--------------------------------------------------
    //-- Carpetas
    //--------------------------------------------------            
    
    function getIdCarpeta($path)
    {
		$this->log('CONECTOR','getID carpeta: ' . $path);				
		try {
			$recurso = $this->conexion->getObjectByPath($path);
			return $recurso->id;
		}  catch (CmisObjectNotFoundException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcionObjetoNoEncontrado("Carpeta '$path' no existe: $txtCMIS");
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error recuperardo la carpeta: $path");
        }	
	}
    
    function crearCarpeta($idPadre, $nombreHijo) 
    {
		$this->log('CONECTOR',"Crear carpeta: '$nombreHijo' en idCarpeta: '$idPadre'");
		try {
			$recurso = $this->conexion->createFolder($idPadre, $nombreHijo);
			return $recurso->id;
		}  catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error crear carpeta: $txtCMIS");
        }
    }

    function getHijosCarpeta($idPadre) 
    {        
		$this->log('CONECTOR','getChildren de idCarpeta: ' . $pathPadre);
		try {
			$objs = $this->conexion->getChildren($idPadre);
			$lista = array();
			foreach ($objs->objectList as $obj) {
				$lista[$obj->properties['cmis:objectId']] = $obj->properties['cmis:name'];
			} 
			return $lista;
		}  catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error buscar elementos hijos: $txtCMIS");
        }
    }

	//--------------------------------------------------
    //-- Tipos
    //--------------------------------------------------    
	function crearTipo($contenido) 
	{
		$this->log('CONECTOR','Crear Tipo: ');
		try {
			//Aca se supone que deberia armar el xml con el que doy de alta el tipo? No es conocer demasiado de la plataforma subyacente?
			
			$obs = $this->conexion->createType($contenido);
			if(!isset($obs->id))  {
				throw CmisException('El server no devolvio un ID');
			}
			return $obs->id;
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error crear el tipo: $txtCMIS");
        }
	}
	
	function eliminarTipo($tipo)
	{
		$this->log('CONECTOR','Eliminar Tipo: ');
		try {
			$this->conexion->deleteType($tipo);
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error eliminar el tipo: $txtCMIS");
        }
	}
	
	function getTipo($tipo)
	{
		$this->log('CONECTOR','Get Tipo: ');
		try {
			$obj = $this->conexion->getTypeDefinition($tipo);
			return $obj;
		} catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error recuperar el tipo: $txtCMIS");
        }
		
	}
	
    //--------------------------------------------------
    //-- CONSULTAS
    //--------------------------------------------------            
    	
	function consultaDirecta($CMISQL, $mapeoColumnas, $opciones=array())
	{	
		// Manejo de opciones
		$opciones_a_aplicar=array(	"searchAllVersions" => "false",
									"maxItems" => 0,
									"skipCount" => 0);
		/*foreach($opciones as $id => $valor) {
			$opciones_a_aplicar[$id] = $valor;
		}*/
		$opciones_a_aplicar = array_merge($opciones_a_aplicar, $opciones);
		$this->log('CONECTOR',"CONSULTA CMISQL: \n" . $CMISQL . "\n" . var_export($opciones_a_aplicar,true));
		if (self::$debugEchoQuery) {
			echo("CONSULTA CMISQL: \n" . $CMISQL . "\n" . var_export($opciones_a_aplicar,true) ."\n\n");
		}
		try {
			$objs = $this->conexion->query($CMISQL, $opciones_a_aplicar);
			// Convertir la respuesta en listado en base al mapeo
			$i = 0;
			$lista = array();
			foreach ($objs->objectList as $obj) {
				foreach($mapeoColumnas as $destino => $origen) {
					$lista[$i][$destino] = $obj->properties[$origen];
				}
				$i++;
			}
			return $lista;			
		}  catch (CmisException $ex) {
			$txtCMIS = $this->extraerTextoExcepcion($ex->getMessage());
			$this->log_error($ex);
            throw new RDIExcepcion("Error consulta: $txtCMIS");
        }
    }		
	
	//--------------------------------------------------

	function extraerTextoExcepcion($texto) 
	{
		$msg_inicio = "<!--message-->";
		$msg_fin = "<!--/message-->";
		$pattern = "#" . preg_quote($msg_inicio) . "(.*?)" .  preg_quote($msg_fin) . "#m";
		$resultado = array();
		preg_match_all($pattern, $texto, $resultado, PREG_SET_ORDER);
		//print_r($resultado);
		if(!isset($resultado[0][1])) {
			$this->log('ERROR REGEX', $resultado);
			return '(REGEX: texto no extraido, ver log)';
		} else {
			return $resultado[0][1];
		}
	}
	
}

