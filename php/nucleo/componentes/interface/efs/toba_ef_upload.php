<?php

/**
 * Ef que selecciona un archivo de su sistema para que esté disponible en el servidor
 * Equivale a un <INPUT type='file'> y su estado equivale a una entrada del $_FILES de php
 * Ver el {http://www.php.net/manual/es/features.file-upload.php manejo de uploads} por parte de php,
 * para entender las restricciones y el manejo en general del upload de archivos
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_upload ef_upload
 */
class toba_ef_upload extends toba_ef
{
	protected $archivo_cargado = false;		//Se cargo un archivo en la etapa anterior?
	protected $archivo_subido = false;		//Se subio un archivo en esta etapa
	protected $extensiones_validas = null;
	protected $clase_css = 'ef-upload';
	
	static function get_lista_parametros()
	{
		$parametros[] = 'upload_extensiones';
		return $parametros;
	}	
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros)
	{ 
		// Controlar las extensiones válidas...
		if (isset($parametros['upload_extensiones']) && trim($parametros['upload_extensiones']) != '') {
			$this->extensiones_validas = array();
			foreach (explode(',', $parametros['upload_extensiones']) as $valor)
				$this->extensiones_validas[] = strtolower(trim($valor));
		}		
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio, $parametros);
	}	
	
	function get_input()
	{
		$tab = $this->padre->get_tab_index();
		$extra = " tabindex='$tab'";		
		$estado = $this->get_estado_input();
		//--- Se puede cargar con el nombre del archivo o el arreglo que php brinda
		//--- al hacer el upload
		if (is_array($estado)) {
			$nombre_archivo = isset($estado['name']) ? $estado['name'] : current($estado);
		} else {
			$nombre_archivo = $estado;
		}
		//-- Si hay un archivo lo deja marcado en sesion para la etapa siguiente
		if (isset($nombre_archivo) && trim($nombre_archivo) != '') {
			if (! $this->permitir_html) {
				$nombre_archivo = texto_plano($nombre_archivo);
			}
			toba::memoria()->set_dato_sincronizado($this->id_form."_cargado", true);
		}
		$salida = "";
		if (! $this->es_solo_lectura()) {
			if (isset($nombre_archivo) && $nombre_archivo != '') {
				$salida .= toba_form::archivo($this->id_form, null, $this->clase_css, "style='display:none'");
				$salida .= "<div id='{$this->id_form}_desicion' class='ef-upload-desc'>". $nombre_archivo . "</div>";
				$salida .= toba_form::checkbox("{$this->id_form}_check", null, 1, 'ef-checkbox', "$extra onclick=\"{$this->objeto_js()}.set_editable()\"");
				$salida .= "<label for='{$this->id_form}_check'>Cambiar el Archivo</label>";
			} else {
				$salida = toba_form::archivo($this->id_form, null, $this->clase_css, $extra);
				$salida .= toba_form::checkbox("{$this->id_form}_check", 1, 1, 'ef-checkbox', "style='display:none'");
			}
		} else { // En modo sólo lectura
			if (isset($nombre_archivo) && $nombre_archivo != '') {
				$salida = "<div class='ef-upload-desc'>". $nombre_archivo ."</div>";
			} else {
				$salida = toba_form::archivo($this->id_form, null, $this->clase_css, "disabled='disabled'");
			}
		}
		$salida .= $this->get_html_iconos_utilerias();
		return $salida;
	}
	
	function get_estado_input()
	{
		if (isset($this->estado)) {
			return $this->estado;
		}else{
			return null;
		}
	}
	
	function cargar_estado_post()
	{
		$this->archivo_cargado = toba::memoria()->get_dato_sincronizado($this->id_form."_cargado");
		$this->archivo_subido = false;
		if (isset($_FILES[$this->id_form])) {
			if (isset($_POST[$this->id_form."_check"])) {
				$this->archivo_subido = true;
				$this->estado = $_FILES[$this->id_form]; 
			}
		}
	}

	function es_archivo_vacio()
	{
		return $_FILES[$this->id_form]["error"] == UPLOAD_ERR_NO_FILE;
	}
	
	function tiene_estado()
	{
		return $this->archivo_cargado || 
				($this->archivo_subido && !$this->es_archivo_vacio());
	}
	
	/**
	 * Valida que cumpla con la lista de extensiones válidas definidas.
	 * También chequea los {@link http://www.php.net/manual/en/features.file-upload.errors.php mensajes de error de upload} de php
	 * @return unknown
	 */
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}
		if ($this->archivo_subido) {
			$id = $this->estado['error'];
			switch($id){
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_INI_SIZE:
					return "Se supero el tamaño máximo del archivo.";
				case UPLOAD_ERR_FORM_SIZE:
					return "Se supero el limite expresado en el FORM";
				case UPLOAD_ERR_NO_FILE:
					//Este caso lo maneja el obligatorio
					$this->archivo_subido = false;
					break;
				case UPLOAD_ERR_CANT_WRITE:
					return "No tiene permisos sobre la carpeta de upload";
				default:
					return "Ha ocurrido un error cargando el archivo ($id)";
			}
                        
			if (!$this->solo_lectura_modificacion && isset($this->extensiones_validas) && $this->archivo_subido && !$this->es_archivo_vacio()) {
				$rep = $_FILES[$this->id_form]['name'];
				$ext = substr($rep, strrpos($rep, '.') + 1);
				if (! in_array(strtolower($ext), $this->extensiones_validas)) {
					$extensiones = implode(', ', $this->extensiones_validas);
					$this->archivo_subido = false;
					$this->estado = null;
					return "No esta permitido subir este tipo de archivo. Solo se permiten extensiones $extensiones";
				}
			}
		}
		return true;
	}
	
	function get_consumo_javascript()
	{
		$consumos = array('efs/ef','efs/ef_upload');
		return $consumos;
	}
	
	function crear_objeto_js()
	{
		return "new ef_upload({$this->parametros_js()})";
	}
	
}

?>
