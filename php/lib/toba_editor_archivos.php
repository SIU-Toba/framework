<?php

class toba_editor_texto
{
	protected $sustituciones;
	protected $id_sustitucion;
	
	function agregar_sustitucion( $texto_buscado, $texto_reemplazo )
	{
		$this->sustituciones[ $this->id_sustitucion ]['buscado'] = $texto_buscado;
		$this->sustituciones[ $this->id_sustitucion ]['reemplazo'] = $texto_reemplazo;
		$this->id_sustitucion++;
	}	
	
	function procesar($texto)
	{
		foreach( $this->sustituciones as $sustitucion ) {
			$texto = preg_replace( $sustitucion['buscado'], $sustitucion['reemplazo'], $texto );
		}
		return $texto;
	}
}

class toba_editor_archivos extends toba_editor_texto
{
	function procesar_archivo( $archivo )
	{
		$texto = $this->procesar(file_get_contents( $archivo ));
		file_put_contents( $archivo, $texto );
	}
	
	function procesar_archivos( $archivos )
	{
		foreach( $archivos as $archivo ) {
			$this->procesar_archivo( $archivo );
		}
	}
}
?>