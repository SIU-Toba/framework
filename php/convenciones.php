<?php
/**
 * Recomendaciones Generales:
 * 		- Usar tabs en lugar de espacios cuando se entran a nuevos scopes.
 * 		- Los nombres de archivos, clases, atributos, métodos, etc. son 
 * 				en minúsculas y separados por guiones bajos. Ej. objeto_ei_formulario
 */

require_once('nucleo/...');			//Sección de includes. Usar paths relativos a mi_proyecto/php

define('apex_xx_nombre', 'valor');	//Sección de defines, xx es la abreviacion de la zona donde se utiliza, Por ej. pa=Punto de Acceso

/**
 * Lugar de explicación general de la clase (en formato javadoc)
 * 
 * Tratar de respetar (cant_clases($archivo) == 1), salvo que sean clases muy chicas o muy cohesivas
 * Tratar de respetar ($nombre_archivo == $nombre_clase . '.php');
 * 
 * @package Paquete al que pertenece la clase (Objetos, Librerias, Nucleo, Recursos, Varios, etc.)
 * @subpackage Subpaquete (Persistencia, Ei, Navegación, Negocio, etc.)
 * @todo Cosas pendientes por hacer, si es groso tratar de relacionarlo con algún ticket
 */
class nombre_clase extends clase_padre
{
	/**
	 * Tratar de que los atributos sea protegidos
	 * Cuando sea posible, brindar valores por defecto en lugar de inicializarlos en el constructor
	 * No usar javadoc a este nivel (ocupa mucho espacio y en general acceder a las propiedades es algo de bajo nivel
	 */
	protected $atributo1 = 'valor';				//Tratar de que los comentarios de los atributos
	protected $atributo2;						//queden alineados
	
	
	function __construct()	//Usar el constructor de PHP5
	{		
	}
	
	/**
	 * Descripción javadoc del método
	 * @param array $nombre_significativo Descripción del parámetro
	 * Los parametros obvios no se documentan
	 * @return boolean Descripción del retorno
	 */
	protected function prototipo_de_metodo($nombre_significativo, $incluir_todo=true)
	{
	}
	
	function es_publica()  //Solo explicitar el tipo de acceso (protected, private) cuando no es publico
	{
	}
	
	protected function es_obvio() //Los métodos obvios no se documentan!
	{
	}
	
	/**
	 * @deprecated Desde tal versión
	 */
	function no_me_llames()
	{
		toba::logger()->obsoleto(__CLASS__, __FUNCTION__, 'Explicar qué cosa usar');	
	}
	
	
	//------------------------------------------------------------------------
	//------ SINTAXIS (esta es una sección!) (crear una macro!)---------------
	//------------------------------------------------------------------------

	function espacios()
	{
		//Para llamar a un método:
		$objeto->metodo($par1, $par2);	//Los parametros se separan por comas.. y despues de cada coma un espacio!
		
		//Para asignar algo
		$algo = $otra_cosa;				//Espacio antes y despues del igual para separar bien las aguas
		
		//Para usar palabras reservadas
		if ($tal_cosa) {				//Espacio despues de if, for, while, etc., 
										//espacio antes y despues de llave
		}
	}
	
	function variables()
	{
		/**
		 * Nomenclatura de variables:
		 * 		- Usar $i,$j o cosas así para contadores
		 * 		- En general, evitar usar $temp, $pepe, $x o cosas por el estilo!
		 * 		- Llamar 'Las cosas por su nombre' :P
		 */
		
		//Porqué inicializar los arreglos???
		$arreglo = array();
		if ($condicion_que_casi_siempre_se_da) {
			$arreglo[] = 'valor';
		}
		return $arreglo;	//Para que el consumo de lo que se retorna sea uniforme
	}						//por ejemplo si alguien quiere hacer un foreach con el resultado
		

	function bloques()
	{
		/**
		 * El objetivo es mejorar la legibilidad del código. 
		 * Para esto se buscan un balance entre
		 * 	- Cantidad de cosas concretas por linea (si es ~1, mejor)
		 * 	- Entendimiento de donde inicia y termina un bloque
		 */
		
		//Se prefiere esto:
		//		+ Cantidad de cosas concretas por linea (2 lineas que solo contienen delimitadores)
		if ($condicion) {	
			foreach ($elemento as $id) {
				echo $id;
			}
		} else {
			echo "Otra cosa";
		}

		
		//En lugar de esto:
		//		+ Entendimiento de los bloques
		//		- Cantidad de cosas concretas por linea (6 lineas que solo contienen delimitadores)
		if ($condicion) 
		{	
			foreach ($elemento as $id) 
			{
				echo $id;
			}
		} 
		else 
		{
			echo "Otra cosa";
		}
		
		//Se exceptuan los bloques de clases y metodos (son solo contenedores)
	}
	
	function strings()
	{
		//Usar comillas simples cuando el contenido es estatico
		$simples = 'contenido';
		$dobles = "$simples dinámico";
		
		//Aprovechar los strings multilínea
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
		";	//Cerrar el string aca facilita agregar algo al final del sql sin preocuparse por las comillas
		
		//Con ESTO otro:
		$sql =  '	SELECT'.
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