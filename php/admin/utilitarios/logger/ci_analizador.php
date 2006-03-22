<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//--------------------------------------------------------------------
class ci_analizador extends objeto_ci
{
	protected $opciones;
	
	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = 'opciones';
		return $estado;	
	}
	
	/**
	 * @todo Se desactiva el logger porque no corre como proyecto toba sino como el de la aplicacion
	 * 		Cuando el admin sea un proyecto hay que sacar la desactivación
	 */
	function evt__inicializar()
	{
		toba::get_logger()->desactivar();	
		if (!isset($this->opciones)) {
			$this->opciones['proyecto'] = toba::get_hilo()->obtener_proyecto();	
			$this->opciones['fuente'] = 'fs';
		}
	}
	
	function obtener_html_dependencias()
	{
		parent::obtener_html_dependencias();
?>
		<style type="text/css">
		.cuerpo, .ci-cuerpo {
			margin-top: 0px;
			margin-bottom: 0px;
		}
		</style>
<?php
		if (isset($this->opciones['proyecto']) && isset($this->opciones['fuente'])) {
			if ($this->opciones['fuente'] == 'db') {
				$this->obtener_html_db();
			} elseif ($this->opciones['fuente'] == 'fs') {
				$this->obtener_html_fs();
			}
		}
	}
	
	function obtener_html_db()
	{
		echo ei_mensaje("El logger por base de datos no está implementado");
	}
	
	function obtener_html_fs()
	{
		$archivo = $this->get_logger()->directorio_logs()."/sistema.log";
		if (file_exists($archivo)) {
			echo "<pre style='width:100%;height:100%;overflow:auto;'>";
			echo file_get_contents($archivo);
			echo "</pre>";
		} else {
			echo ei_mensaje("No hay logs registrados para el proyecto ".
							"<strong>{$this->opciones['proyecto']}</strong>");
		}
	}
	
	function get_logger()
	{
		return logger::instancia($this->opciones['proyecto']);
	}
	

	function evt__pre_cargar_datos_dependencias()
	{
		if (isset($this->opciones)) {
			$this->dependencia('filtro')->colapsar();
		}
	}
	
	//---- Eventos CI -------------------------------------------------------
	
	function evt__refrescar()
	{
	}
	
	function evt__borrar()
	{
		$this->get_logger()->borrar_archivos_logs();	
	}
	
	//---- Eventos Filtro -------------------------------------------------------
	
	function evt__filtro__filtrar($opciones)
	{
		$this->opciones = $opciones;		
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->opciones);	
	}
	
	function evt__filtro__carga()
	{
		if (isset($this->opciones)) {
			return $this->opciones;	
		}
	}
}

?>