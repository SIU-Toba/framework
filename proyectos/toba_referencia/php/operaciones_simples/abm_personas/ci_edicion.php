<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_edicion extends toba_ci
{
	function get_relacion()	
	{
		return $this->controlador->get_relacion();
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'persona'
	//-------------------------------------------------------------------


	function conf__form_persona()
	{
		$datos = $this->get_relacion()->tabla('persona')->get();
		$fp_imagen = $this->get_relacion()->tabla('persona')->get_blob('imagen');
		if (isset($fp_imagen)) {
		  	//-- Se necesita el path fisico y la url de una archivo temporal que va a contener la imagen
		  	$temp_nombre = md5(uniqid(time()));
		  	$temp_archivo = toba::proyecto()->get_www_temp($temp_nombre);
	
		  	//-- Se pasa el contenido al archivo temporal
		  	$temp_fp = fopen($temp_archivo['path'], 'w');
		  	stream_copy_to_stream($fp_imagen, $temp_fp);
		  	fclose($temp_fp);
		  	$tamaño = round(filesize($temp_archivo['path']) / 1024);
		  	
		  	//-- Se muestra la imagen temporal
		  	$datos['imagen_vista_previa'] = "<img src='{$temp_archivo['url']}' alt=''>";
		  	$datos['imagen'] = 'Tamaño: '.$tamaño. ' KB';
		} else {
			$datos['imagen'] = null;
		}
		return $datos;
	}

	function evt__form_persona__modificacion($registro)
	{
		$this->get_relacion()->tabla('persona')->set($registro);
		if (is_array($registro['imagen'])) {
			//Se subio una imagen
			$fp = fopen($registro['imagen']['tmp_name'], 'rb');
			$this->get_relacion()->tabla('persona')->set_blob('imagen', $fp);
		}		
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'juegos'
	//-------------------------------------------------------------------

	function conf__form_juegos()	
	{
		return $this->get_relacion()->tabla('juegos')->get_filas(null, true);
	}

	function evt__form_juegos__modificacion($datos)
	{
		$this->get_relacion()->tabla('juegos')->procesar_filas($datos);	
	}

	//-------------------------------------------------------------------
	//--- Pantalla 'deportes'
	//-------------------------------------------------------------------

	//-- Cuadro --

	function conf__cuadro_deportes()	
	{
		$buscador = $this->get_relacion()->tabla('deportes')->nueva_busqueda();
		$buscador->	set_columnas_orden(array('hora_fin' => SORT_DESC,'desc_deporte' => SORT_ASC));
		return $buscador->buscar_filas();
	}

	function evt__cuadro_deportes__seleccion($seleccion)
	{
		$this->get_relacion()->tabla('deportes')->set_cursor($seleccion);
	}
	
	//-- Formulario --

	function conf__form_deportes()
	{
		if ($this->get_relacion()->tabla('deportes')->hay_cursor()) {
			return $this->get_relacion()->tabla('deportes')->get();
		}
	}

	function evt__form_deportes__modificacion($registro)
	{
		$this->get_relacion()->tabla('deportes')->set($registro);
		$this->evt__form_deportes__cancelar();
	}

	function evt__form_deportes__baja()
	{
		$this->get_relacion()->tabla('deportes')->set(null);
		$this->evt__form_deportes__cancelar();
	}

	function evt__form_deportes__alta($registro)
	{
		$this->get_relacion()->tabla('deportes')->nueva_fila($registro);
	}

	function evt__form_deportes__cancelar()
	{
		$this->get_relacion()->tabla('deportes')->resetear_cursor();
	}
	
}
?>