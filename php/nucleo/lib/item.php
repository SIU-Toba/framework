<?php

class item
{
	protected $datos;			//Datos básicos
	protected $nivel;			//Nivel del item en el arbol de items
	protected $grupos_acceso;	//Grupos que pueden acceder al item
	protected $camino;			//Arreglo de carpetas que componen la rama en donde pertenece el item
	
	function __construct($datos = array())
	{
		$this->datos = $datos;
	}
	//------------------------------------PROPIEDADES --------------------------------------------------------			
	function id() {	return $this->datos['item']; }
	
	function id_padre() {	return $this->datos['padre']; }	

	function nivel() {	return $this->nivel; }
	
	function camino() { return $this->camino; }
	
	function nombre() { return $this->datos['nombre']; }
	
	function proyecto() { return $this->datos['proyecto']; }
	
	function tipo_solicitud() { return $this->datos["solicitud_tipo"]; }
	
	function crono() { return $this->datos['crono'] == 1; }
	
	function objetos() { return $this->datos["objetos"]; }
	
	function registra_solicitud() { return $this->datos["registrar"]; }
	
	function propietario() { return $this->datos['usuario']; }

	function grupos_acceso()
	{
		if (!isset($this->grupos_acceso)) {
			$sql = "
				SELECT g.usuario_grupo_acc
				FROM
					apex_usuario_grupo_acc_item g
				WHERE
					g.item = '{$this->id()}' AND
					g.proyecto = '{$this->proyecto()}'
			";
			$rs = toba::get_db('instancia')->Execute($sql);
			if (!$rs)
				throw new excepcion_toba("INFO DEL ITEM - [error] " . toba::get_db('instancia')->ErrorMsg()." - [sql] $sql");
			if ($rs->EOF)
				$this->grupos_acceso = array();
			else
				$this->grupos_acceso =  aplanar_matriz($rs->GetArray());
		}
		return $this->grupos_acceso;
	}
		
	//------------------------------------PREGUNTAS --------------------------------------------------------
	function grupo_tiene_permiso($grupo)
	{
		return in_array($grupo, $this->grupos_acceso());
	}
	
	function es_carpeta() { return $this->datos['carpeta']; }
	
	function es_de_menu() {	return $this->datos["menu"]; }
	
	function es_publico() { return $this->datos['publico']; } 
	
	function es_buffer() 
	{ 
		return !($this->datos['act_buf']== 0 && $this->datos['act_buf_p']=="toba");
	}
	
	function es_patron()
	//--- Es un PATRON?? El patron <toba,especifico> representa la ausencia de PATRON	
	{
		return !($this->datos['act_pat']=="especifico" && $this->datos['act_pat_p']=="toba");
	}
	
	function es_accion()
	{
		return !$this->es_buffer() && !$this->es_patron();
	}

	function es_hijo_de($carpeta)
	{
		if ($this->id() == '')
			return false;
		return $this->datos['padre'] == $carpeta->id();
	}

	//------------------------------------ CAMBIO DE ESTADO --------------------------------------------------------
	
	function set_nivel($nivel) { $this->nivel = $nivel; }	
	
	function set_camino($camino) {
		$this->camino = $camino;
	}
	
	function otorgar_permiso($grupo)
	{
		$sql = "INSERT INTO apex_usuario_grupo_acc_item (usuario_grupo_acc, proyecto, item) 
				VALUES ('$grupo', '{$this->proyecto()}', '{$this->id()}')";
		if(toba::get_db('instancia')->Execute($sql) === false)
			throw new excepcion_toba("Ha ocurrido un error CREANDO los permisos - " .toba::get_db('instancia')->ErrorMsg());
	}

	
	//------------------------------------DEFINICION ESTATICA--------------------------------------------------------	
	static function definicion_campos()
	{
		return "
				i.orden							as orden,
				i.proyecto						as proyecto,
				i.item		 					as item,
				i.padre		 					as padre,
				i.nombre	 					as nombre,
				i.carpeta						as carpeta,
				i.menu							as menu,
				i.usuario						as usuario,
				i.actividad_buffer_proyecto 	as act_buf_p,
				i.actividad_buffer				as act_buf,
				i.actividad_patron_proyecto		as act_pat_p,
				i.actividad_patron				as act_pat,
				i.actividad_accion				as act_acc,
				i.publico						as publico,
				i.solicitud_registrar			as registrar,
				i.solicitud_registrar_cron		as crono,
				i.solicitud_tipo				as solicitud_tipo";
	}
	
	static function definicion_tabla()
	{
		return "apex_item i";
	}
	
	//------------------------------------CARGAS PARTICULARES--------------------------------------------------------		
	function cargar_por_id($proyecto, $id)
	{
		$sql = "SELECT {$this->definicion_campos()} FROM {$this->definicion_tabla()} WHERE 
				i.item = '$id' AND i.proyecto = '$proyecto'";
		$rs = toba::get_db('instancia')->Execute($sql);
		if (!$rs || $rs->EOF)
			throw new excepcion_toba("ITEM Carga - [error] " . toba::get_db('instancia')->ErrorMsg()." - [sql] $sql");
		else
			$this->datos = $rs->fields;
	}	
	
}



?>