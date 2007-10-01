<?php

/**
 * Permite hacer reemplazos masivos en una cadena de texto
 * @package Varios
 */
class toba_editor_texto
{
	protected $sustituciones;
	protected $id_sustitucion;
	
	/**
	 * Ingresa criterios de reemplazo
	 * @param string $texto_buscado Exp. regular
	 * @param string $texto_reemplazo
	 */
	function agregar_sustitucion( $texto_buscado, $texto_reemplazo )
	{
		$this->sustituciones[ $this->id_sustitucion ]['buscado'] = $texto_buscado;
		$this->sustituciones[ $this->id_sustitucion ]['reemplazo'] = $texto_reemplazo;
		$this->id_sustitucion++;
	}	

	/**
	 * Procesa todos las sustituciones ingresadas
	 * @param string $texto
	 * @return string texto resultante
	 */
	function procesar($texto)
	{
		foreach( $this->sustituciones as $sustitucion ) {
			$texto = preg_replace( $sustitucion['buscado'], $sustitucion['reemplazo'], $texto );
		}
		return $texto;
	}
}

/**
 * Permite hacer reemplazos masivos en un archivo de texto
 * @package Varios
 */
class toba_editor_archivos extends toba_editor_texto
{
	/**
	 * Procesa todos las sustituciones ingresadas tomando un archivo de entrada y uno de salida
	 * @param string $archivo Path del archivo de entrada
	 * @param string $archivo_resultado Path del archivo resultante
	 */
	function procesar_archivo( $archivo, $archivo_resultado=null )
	{
		$archivo_resultado = isset($archivo_resultado) ? $archivo_resultado : $archivo;
		$texto = $this->procesar(file_get_contents( $archivo ));
		file_put_contents( $archivo_resultado, $texto );
	}
	
	/**
	 * Procesa las sustituciones en un conjunto de archivos 
	 * @param array $archivos Arreglo de paths de archivos
	 */
	function procesar_archivos( $archivos )
	{
		foreach( $archivos as $archivo ) {
			$this->procesar_archivo( $archivo );
		}
	}
}
?>