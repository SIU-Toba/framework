<?php
$this->registrar_parametros();

if (!isset($this->parametros['-orig_proy'])) {
	throw new toba_error('El parámetro -orig_proy es obligatorio');
}
if (!isset($this->parametros['-orig_item'])) {
	throw new toba_error('El parámetro -orig_item es obligatorio');
}
if (!isset($this->parametros['-dest_proy'])) {
	throw new toba_error('El parámetro -dest_proy es obligatorio');
}
if (!isset($this->parametros['-dest_padre'])) {
	throw new toba_error('El parámetro -dest_padre es obligatorio');
}
if (!isset($this->parametros['-dest_fuente'])) {
	throw new toba_error('El parámetro -dest_fuente es obligatorio');
}

$id = array(    'proyecto' => $this->parametros['-orig_proy'],
					'componente' =>  $this->parametros['-orig_item']);
$info_item = toba_constructor::get_info($id, 'toba_item');

$nuevos_datos = array();
$nuevos_datos['proyecto'] = $this->parametros['-dest_proy'];
$nuevos_datos['padre_proyecto'] = $nuevos_datos['proyecto'];
$nuevos_datos['padre'] = $this->parametros['-dest_padre'];
if (isset($this->parametros['-dest_anexo'])) {
	$nuevos_datos['anexo_nombre'] = $this->parametros['-dest_anexo'];    
}
$nuevos_datos['fuente_datos'] = $this->parametros['-dest_fuente'];
$nuevos_datos['fuente_datos_proyecto'] = $nuevos_datos['proyecto'];
$directorio = (isset($this->parametros['-dest_dir'])) ? $this->parametros['-dest_dir'] : false;
$clave = $info_item->clonar($nuevos_datos, $directorio);

echo $clave['componente'];


?>