<?php
require_once('admin/db/dao_editores.php');
require_once('nucleo/componentes/info/interfaces.php');

class catalogo_objetos
{
	protected $proyecto;
	protected $objetos;
	
	protected $explicaciones = array();		//Explicaciones de porque entro este objeto
	
	function __construct($proyecto)
	{
		$this->proyecto = $proyecto;
	}
	
	function get_objetos($opciones)
	{
		//---Metodo de Consulta (DAO)
		$filtro_dao = "";
		if (isset($opciones['dao']) && $opciones['dao'] != '') {
			$filtro_dao = $this->formar_filtro_dao($opciones['dao']);	
		}
				
		//---Clase
		if (isset($opciones['clase'])) {
			$clases = array($opciones['clase']);	
		} else {
			$clases = dao_editores::get_clases_validas();
		}
		
		//---ID
		$filtro_id = isset($opciones['id']) ? "AND	o.objeto = '{$opciones['id']}'" : '';
		
		//----Extensiones
		$filtro_ext = "";
		if (isset($opciones['extendidos'])) {
			if ($opciones['extendidos'] == 'SI') {
				$filtro_ext = "AND		o.subclase IS NOT NULL";
			} else {
				$filtro_ext = "AND		o.subclase IS NULL";
			}
		}
		
		//---Huerfanos
		$filtro_huerfano = "";
		if (isset($opciones['huerfanos']) && $opciones['huerfanos'] == 1) {
			$filtro_huerfano = "AND		o.objeto NOT IN (SELECT objeto FROM apex_item_objeto WHERE proyecto = '{$this->proyecto}')";
			$filtro_huerfano .= "AND	o.objeto NOT IN (SELECT objeto_proveedor FROM apex_objeto_dependencias WHERE proyecto = '{$this->proyecto}')";
		}
		
		//---Nombre
		$filtro_nombre = isset($opciones['nombre']) ? "AND		o.nombre ILIKE '%{$opciones['nombre']}%'" : '';
		

		
		//-- Se utiliza como sql básica aquella que brinda la definición de un componente
		$sql_base = componente_toba::get_vista_extendida($this->proyecto);
		$sql = $sql_base['info']['sql'];
		$sql .= "
				AND		o.clase IN ('" . implode("', '", $clases) . "')
				AND 	o.proyecto = '$this->proyecto'
				$filtro_dao				
				$filtro_id
				$filtro_ext
				$filtro_huerfano
				$filtro_nombre
	            ORDER BY o.nombre
		";

		//--- Recorrido
		$datos = toba::get_db('instancia')->consultar($sql);
		foreach ($datos as $dato) {
			$agregar = true;
			if (isset($opciones['extensiones_rotas']) && $opciones['extensiones_rotas'] == 1) {
				$archivo = $dato['subclase_archivo'];
				$path_proy = toba::get_hilo()->obtener_proyecto_path()."/php/".$archivo;
				$path_toba = toba_dir()."/php/".$archivo;
				if (file_exists($path_proy) || file_exists($path_toba)) {
					//Si se encuentra el archivo la extension no esta rota
					$agregar = false;
				}
			}
			if ($agregar) {
				$clave = array('componente' =>$dato['objeto'], 'proyecto' => $this->proyecto);			
				$info = constructor_toba::get_info($clave, $dato['clase'], false, array('info' =>$dato));
				if (isset($this->explicaciones[$dato['objeto']] )) {
					$explicacion = implode("<hr>", $this->explicaciones[$dato['objeto']]);
					$info->set_info_extra($explicacion);
				}
				$this->objetos[] = $info;
			}
		}
		
		return $this->objetos;
	}
	
	
	/**
	 * Consulta antes los objetos porque tiene que formar la 'explicacion' 
	 * de porque se filtran estos registros. Con un subselect esto no sería posible
	 */
	protected function formar_filtro_dao($busca)
	{
		$sql = "
			SELECT 
				o.objeto,
				ef.inicializacion,
				c.dao_metodo,
				c.dao_nucleo
			FROM apex_objeto o
				LEFT OUTER JOIN apex_objeto_cuadro c ON 
					(c.objeto_cuadro = o.objeto 
					AND c.objeto_cuadro_proyecto = o.proyecto)
				LEFT OUTER JOIN apex_objeto_ei_formulario_ef ef ON
					(ef.objeto_ei_formulario = o.objeto
					AND ef.objeto_ei_formulario_proyecto = o.proyecto)
			WHERE
				o.proyecto = '{$this->proyecto}'
				AND (
					(c.dao_nucleo ILIKE '%$busca%'  OR	c.dao_metodo ILIKE '%$busca%')
					OR 		(ef.inicializacion ILIKE '%clase:%$busca%;'
							OR	ef.inicializacion ILIKE '%dao:%$busca%;'
							OR	ef.inicializacion ILIKE '%include:%$busca%;')
					)
		";
		$rs = consultar_fuente($sql, 'instancia');
		if (!empty($rs)) {
			$sql = "AND		o.objeto IN (";
			foreach ($rs as $obj) {
				$this->explicaciones[$obj['objeto']][] = $this->formar_explicacion_dao($busca, $obj);
				$sql .= $obj['objeto'].", ";
			}
			$sql = substr($sql, 0, -2);
			$sql .= ")";
			return $sql;
		}
	}
	
	protected function formar_explicacion_dao($buscado, $datos)
	{
		if (isset($datos['inicializacion'])) {

		}
		if (isset($datos['dao_metodo'])) {
			if (preg_match("/$buscado/", $datos['dao_metodo'])) {
				return "<b>$buscado</b> se encontró como método <b>{$datos['dao_metodo']}</b> de la consulta_php de este cuadro.";
			}
		}
		if (isset($datos['dao_nucleo'])) {
			if (eregi("$buscado", $datos['dao_nucleo'])) {
				return "<b>$buscado</b> se encontró en el nombre <b>{$datos['dao_nucleo']}</b> de la consulta_php de este cuadro.";
			}
		}		
	}
	
}


?>