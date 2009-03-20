<?php
/**
 * Recomendaciones Generales:
 * 		- Usar tabs en lugar de espacios cuando se entran a nuevos scopes.
 * 		- Los nombres de archivos, clases, atributos, m�todos, etc. son 
 * 				en min�sculas y separados por guiones bajos. Ej. objeto_ei_formulario
 */

require_once('nucleo/...');			//Secci�n de includes. Usar paths relativos a mi_proyecto/php

define('apex_xx_nombre', 'valor');	//Secci�n de defines, xx es la abreviacion de la zona donde se utiliza, Por ej. pa=Punto de Acceso

/**
 * Lugar de explicaci�n general de la clase (en formato javadoc)
 * 
 * Tratar de respetar (cant_clases($archivo) == 1), salvo que sean clases muy chicas o muy cohesivas
 * Tratar de respetar ($nombre_archivo == $nombre_clase . '.php');
 * 
 * @package Paquete al que pertenece la clase (Objetos, Librerias, Nucleo, Recursos, Varios, etc.)
 * @subpackage Subpaquete (Persistencia, Ei, Navegaci�n, Negocio, etc.)
 * @todo Cosas pendientes por hacer, si es groso tratar de relacionarlo con alg�n ticket
 */
class convenciones extends clase_padre
{
	/**
	 * Tratar de que los atributos sea protegidos
	 * Cuando sea posible, brindar valores por defecto en lugar de inicializarlos en el constructor
	 * No es necesario usar phpdoc a este nivel (ocupa mucho espacio y en general acceder a las propiedades es algo de bajo nivel
	 */
	protected $atributo1 = 'valor';				//Tratar de que los comentarios de los atributos
	protected $atributo2;						//queden alineados
	

	/**
	 * Usar el constructor de php5
	 */
	function __construct()
	{		
	}
	
	/**
	 * Ingresar descripci�n phpdoc del m�todo
	 * 
	 * @param array   $nombre_significativo Descripci�n del par�metro
	 * @param boolean $incluir_todo Todos los par�metros se documentan
	 * @return boolean Descripci�n del retorno
	 */
	protected function prototipo_de_metodo($nombre_significativo, $incluir_todo=true)
	{
	}

	/**
	 * Solo explicitar el tipo de acceso (protected, private) cuando no es publico
	 * Un buen signo de documentaci�n de un m�todo es si su nombre ya documenta su uso
	 */
	function es_publica()
	{
	}
	
	/**
	 * Usar @deprecated para marcar los m�todos a los que se le quitar� soporte en pr�ximas versiones
	 * @deprecated Desde version x.y.z
	 */
	function no_me_llames()
	{
		toba::logger()->obsoleto(__CLASS__, __FUNCTION__, 'Explicar qu� cosa usar');	
	}
	
	
	//------------------------------------------------------------------------
	//------ SINTAXIS (esta es una secci�n!) (crear una macro!)---------------
	//------------------------------------------------------------------------

	function espacios()
	{
		//Asignaci�n:
		$algo = $otra_cosa;				//Espacio antes y despues del igual para separar bien las aguas
										//Una asignaci�n por l�nea

		//Espacio despues de if, for, while, etc.,
		if ($tal_cosa) {
			//Para llamar a un m�todo:
			$objeto->metodo($par1, $par2);	//Los parametros se separan por comas.. y despues de cada coma un espacio!
		}

		//Expresiones
		$a = $b + $c;					//Espacio entre operadores
		$a = $b && $c;					//Usar && y || en lugar de AND y OR (php hereda de C)
	}
	
	function variables()
	{
		/**
		 * Nomenclatura de variables:
		 * 		- Usar $i,$j o cosas as� para contadores
		 * 		- En general, evitar usar $temp, $pepe, $x o cosas por el estilo!
		 * 		- Llamar 'Las cosas por su nombre' :P
		 */
		
		//Porqu� inicializar los arreglos???
		$arreglo = array();
		if ($condicion_que_casi_siempre_se_da) {
			$arreglo[] = 'valor';
		}
		return $arreglo;	//Para que el consumo de lo que se retorna sea uniforme
	}						//por ejemplo si alguien quiere hacer un foreach con el resultado
		

	/**
	 * Los bloques de clases y metodos tienen llaves {} en l�neas propias (son solo contenedores)
	 */
	function bloques()
	{
		//En cambio el resto de las estructuras de control abren su llave en la misma l�nea
		//La idea es aumentar la cantidad de cosas concretas por linea y no usar dos lineas que solo contienen delimitadores)
		if ($condicion) {	
			foreach ($elemento as $id) {
				echo $id;
			}
		} else {
			echo 'Otra cosa';
		}
	}
	
	function strings()
	{
		//Usar comillas simples cuando el contenido es estatico
		$simples = 'contenido';

		//Usar comillas dobles s�lo cuando dentro hay una variable/llamada
		$dobles = "$simples din�mico";
		
		//Aprovechar los strings multil�nea
		//Comparar la legibilidad, flexibilidad a cambios, 
		//facilidad de traer o llevar a un editor de sql y agilidad de escribir ESTO:
		$sql = "	SELECT	
						campo1 as mi_campo,
						campo2
					FROM
						tabla1 t1,
						tabl2 t2
					WHERE
							t1.condicion = t2.condicion
						AND	t1.nombre = $variable
		";	//Cerrar el string aca facilita agregar algo al final del sql sin preocuparse por las comillas
		
		//Con ESTO otro:
		$sql = '	SELECT'.
				'		campo1 as mi_campo,'.
				'		campo2'.
				' 		FROM'.
				'		tabla1 t1,'.
				'		tabl2 t2'.
				'	WHERE'.
				'		t1.condicion = t2.condicion';
	}
}

?>