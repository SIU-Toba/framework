<?php

class RDIServicioRecurso 
{
	protected $conector;
	protected $sistema;
	protected $instalacion;
	protected $log;
	
	function __construct($conector, $sistema, $instalacion) 
	{
		$this->conector = $conector;
		$this->sistema = $sistema;
		$this->instalacion = $instalacion;
	}
	
	function asociarLog($log)
	{
		$this->log = $log;
	}
	
	protected function log($titulo, $texto)
	{
		if(isset($this->log)) {
			$this->log->add_debug($titulo, $texto);
		}
	}

	//-------------------------------------------------
	
	function getTipo()
	{
		return RDITipos::RECURSO;
	}
	
    protected function definirUbicacion($parametros) 
    {		
		$this->log('RECURSO',"Definir ubicacion ('/')");
		$idCarpeta = $this->conector->getIdCarpeta('/');
		return $idCarpeta;
    }	
	
	function getNombre($parametros)
	{
		return 'recurso-' . $parametros['aplicacionOrigen'] . $parametros['instalacionOrigen'];
	}
	
	//-------------------------------------------------
	// PARAMETROS / ATRIBUTOS
	//-------------------------------------------------		

	function getParametrosObligatorios()
	{
		return array();
	}	
	
	function getParametrosObligatoriosImplicitos()
	{
		$parametros['rdi:aplicacionOrigen'] = 'aplicacion';
		$parametros['rdi:instalacionOrigen'] = 'instalacion';
		return $parametros;
	}		
	
	function getParametrosOpcionales()
	{
		$parametros['rdi:publicado'] = 'publicado';
		return $parametros;
	}	
	
	function getAtributosInternos()
	{
		$parametros = array_flip($this->conector->atributos());
		return $parametros;
	}	
	
	protected function controlarParametrosObligatorios($parametros)
	{
		$definidos = array_merge($this->getParametrosObligatorios(), $this->getParametrosObligatoriosImplicitos());
		$no_incluidos = array();
		$ok = true;
		foreach($definidos as $def) {
			if (!isset($parametros[$def])) {
				$no_incluidos[] = $def;
				$ok = false;
			}
		}
		if (!$ok) {
			throw new RDIExcepcion("ERROR [".$this->getTipo()."] Parametros obligatorios no definidos: " . implode(',', $no_incluidos));
		} 
	}
	
	protected function procesarAtributos($parametros)
	{
		// Obligatorios
		$listaAtributos = array();
		$obligatorios = array_merge($this->getParametrosObligatorios(), $this->getParametrosObligatoriosImplicitos());
		foreach($obligatorios as $atributoTipo => $parametro) {
			if (isset($parametros[$parametro])) {
				$listaAtributos[$atributoTipo] = $parametros[$parametro];
			}
		}
		// Opcionales
		$opcionales = $this->getParametrosOpcionales();
		foreach($opcionales as $atributoTipo => $parametro) {
			if (isset($parametros[$parametro])) {
				$listaAtributos[$atributoTipo] = $parametros[$parametro];
			}
		}
		// Controlar parametros INVALIDOS
		$posibles = array_merge($opcionales, $obligatorios);
		$actuales = array_keys($parametros);
		
		$invalidos = array_diff($actuales, $posibles);
		if (! empty($invalidos)) {
			throw new RDIExcepcion("ERROR [".$this->getTipo()."] Parametros invalidos: " . implode(',', $invalidos));
		}
		return $listaAtributos;
	}
	
	//-------------------------------------------------
	// API RECURSO
	//-------------------------------------------------	

    function crear($parametros, $contenido, $mimeType="")
    {
        $parametros['aplicacion'] = $this->sistema;
        $parametros['instalacion'] = $this->instalacion;
		// Los documentos nuevos esta PUBLICADOS por defecto
		if(!isset($parametros['publicado'])){ $parametros['publicado'] = 1;}
		
		$this->controlarParametrosObligatorios($parametros);		
		$atributos = $this->procesarAtributos($parametros);
        $tipo = $this->getTipo();		
		
		$this->log('RECURSO',"Crear '$tipo'\n" . var_export($atributos,true));		
        $idCarpeta = $this->definirUbicacion($parametros);
        $nombre = $this->getNombre($parametros);
        $idRecurso = $this->conector->crearDocumento($idCarpeta, $nombre, $tipo, $atributos, $contenido, $mimeType);
		
        return $idRecurso;
    }	
	
	function recuperarAtributos($idRecurso)
	{
		$this->log('RECURSO',"Recuperar contenido $idRecurso");		
		$objeto = $this->conector->recuperarAtributos($idRecurso);
		$atributos = array();
		foreach($this->getListaColumnasPosibles() as $atrib => $param) {
			$atributos[$param] = $objeto->properties[$atrib];
		}
		return $atributos;
	}
	
	function recuperarContenido($idRecurso)
	{
		$this->log('RECURSO',"Recuperar contenido $idRecurso");		
		return $this->conector->recuperarContenido($idRecurso);
	}
	
	/**
	 * Atencion, el parametro versionar en false es SOLO para los casos
	 * en que se esta utilizando este metodo dentro de una "transaccion"
	 * manejada desde afuera. Si la modificacion no se completa con el 
	 * checkIN el objeto queda en el limbo de la no-version.
	 */
    function modificarAtributos($idRecurso, $parametros, $versionar=true)
    {
		$this->log('RECURSO',"modificar parametros: $idRecurso\n" . var_export($parametros,true));
		$atributos = $this->procesarAtributos($parametros);
		try {
			if($versionar){
				$checkedOut = false;
				$this->conector->checkOut($idRecurso);
				$checkedOut = true;
			}		
			$this->conector->modificarAtributos($idRecurso, $atributos);
			if($versionar){
				$this->conector->checkIn($idRecurso);
			}			
		} catch (RDIExcepcion $ex) {
			if($versionar && $checkedOut){
				$this->conector->cancelCheckOut($idRecurso);
			}
			throw $ex;
		}
    }
	
	/**
	 * Atencion, el parametro versionar en false es SOLO para los casos
	 * en que se esta utilizando este metodo dentro de una "transaccion"
	 * manejada desde afuera. Si la modificacion no se completa con el 
	 * checkIN el objeto queda en el limbo de la no-version.
	 */
    function modificarContenido($idRecurso, $contenido, $versionar=true, $mimeType="")
    {
		$this->log('RECURSO',"modificar CONTENIDO: $idRecurso");
		try {
			if($versionar){
				$checkedOut = false;
				$this->conector->checkOut($idRecurso);
				$checkedOut = true;
			}		
			$this->conector->modificarContenido($idRecurso, $contenido, $mimeType);
			if($versionar){
				$this->conector->checkIn($idRecurso);
			}			
		} catch (RDIExcepcion $ex) {
			if($versionar && $checkedOut){
				$this->conector->cancelCheckOut($idRecurso);
			}
			throw $ex;
		}		
    }
	
	function publicar($idRecurso, $publico=1, $versionar=true)
    {
		$desc = $publico ? "PUBLICAR" : "DESPUBLICAR";
		$this->log('RECURSO',"Publicar recurso: $idRecurso ($desc)");
		$this->modificarAtributos($idRecurso, array('publicado'=> $publico), $versionar);		
    }	
	
    function checkOut($idRecurso)
    {        
		$this->log('RECURSO','CheckOUT recurso: '. $idRecurso);
		$this->conector->checkOut($idRecurso);
    }
	
    function cancelCheckOut($idRecurso)
    {        
		$this->log('RECURSO','CANCEL CheckOUT recurso: ' . $idRecurso);
		$this->conector->cancelCheckOut($idRecurso);
    }
	
    function checkIn($idRecurso)
    {        
		$this->log('RECURSO','CheckIN recurso: ' . $idRecurso);
		$this->conector->checkIn($idRecurso);
    }	
	
    function versionar($idRecurso, $contenido, $parametros=array(), $mimeType="")
    {   
		$checkedOut = false;	
		$this->log('RECURSO',"Versionar recurso: $idRecurso\n" . var_export($parametros,true));
		if(! empty($parametros)){
			$atributos = $this->procesarAtributos($parametros);			
		} else {
			$atributos = array();
		}
		try {
			$this->conector->checkOut($idRecurso);
			$checkedOut = true;
			$this->conector->modificarContenido($idRecurso, $contenido, $mimeType);
			if(! empty($atributos)) {
				$this->conector->modificarAtributos($idRecurso, $atributos, false);
			}
			$this->conector->checkIn($idRecurso);
		} catch (RDIExcepcion $ex) {
			if($checkedOut){
				$this->conector->cancelCheckOut($idRecurso);
			}
			throw $ex;
		}
    }
	
	function eliminar($idRecurso)
	{
		$this->log('RECURSO',"ELIMINAR $idRecurso");		
		return $this->conector->eliminarDocumento($idRecurso);
	}	
	
	//-------------------------------------------------
	// BUSQUEDA
	//-------------------------------------------------		
	/**
	 * Esto a futuro se podria convertir en un motorcito de consultas
	 * (sobre todo para proveer mejores posibilidades de filtrado, lo demas esta OK)
	 */
	function buscar($columnas=array(), $criterioFiltrado=array(), $orden=array(), $opciones=array())
	{
		$this->log('RECURSO',"Buscar -- \nCOLUMNAS: \n" . var_export($columnas, true) .
				"\nFILTRO: \n" . var_export($criterioFiltrado, true) .
				"\nORDEN: \n" . var_export($orden, true));
		
		//----- COLUMNAS ----------------
		$cols_predeterminadas = array('id','version','nombre','creacion','idVersion');
		if(empty($columnas)) {$columnas = $cols_predeterminadas;}
		$definicionColumnas = $this->getListaColumnasPosibles();
		
		//1)-- Verificar validez
		$listaValidas = array_values($definicionColumnas);
		$invalidos = array_diff($columnas, $listaValidas);		
		if (! empty($invalidos)) {
			throw new RDIExcepcion('Buscar: existen columnas no definidas: ' . implode(',', $invalidos));
		}		
		//2)-- resolver parametros externos a nombre de atributos
		$columnasQuery = array();
		$claves = array_flip($definicionColumnas);
		foreach($columnas as $etiqueta) {
			$columnasQuery[$etiqueta] = $claves[$etiqueta];
		}
		if(empty($columnasQuery)){
			throw new RDIExcepcion('La busqueda no tiene columnas!');
		}
		
		//----- FILTRADO ----------------
		$where = "";
		if(! empty($criterioFiltrado)) {
			$wh = array();
			foreach($criterioFiltrado as $col => $valor) {
				if(! isset($claves[$col])){
					throw new RDIExcepcion('BUSCAR - CRITERIO FILTRADO: No existe la columna: ' . $col);
				}
				if(trim($col) == 'id'){
					throw new RDIExcepcion('BUSCAR - CRITERIO FILTRADO: CMIS no permite utilizar la columna "id". Utilice  $servicio->recuperarAtributos($id)');
				}
				$wh[] = $claves[$col] ." = '$valor'";		//Doesn't have or need a quote?
			}
			$where .= ' WHERE ' . implode(' AND ', $wh);			
		}
		
		//----- ORDEN -----------------
		$orderby = "";
		if(! empty($orden)) {
			$or = array();
			foreach($orden as $col => $valor) {
				if(!isset($claves[$col])){
					throw new RDIExcepcion('BUSCAR - ORDEN: No existe la columna: ' . $col);
				}
				if(trim($col) == 'id'){
					throw new RDIExcepcion('BUSCAR - ORDEN: CMIS no permite utilizar la columna "id".');
				}
				$or[] = $claves[$col] .' '. $valor;
			}
			$orderby .= ' ORDER BY ' . implode(', ', $or);
		}
		
		//----- Armado CMISQL -------
		$CMISQL = 'SELECT ' . implode(', ', $columnasQuery) . "\n" .
					' FROM ' . $this->getTipo() . "\n" .
					" $where \n" .
					" $orderby;\n";
		$datos = $this->conector->consultaDirecta($CMISQL, $columnasQuery, $opciones);
		return $datos;
	}

	function getListaColumnasPosibles()
	{
		$posibles = array_merge($this->getAtributosInternos(),
								$this->getParametrosObligatoriosImplicitos(),
								$this->getParametrosObligatorios(), 
								$this->getParametrosOpcionales());
		return $posibles;
	}	
}
