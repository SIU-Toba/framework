<?php
/*
	MODIFICADORES
 
 	@@acceso: 
	@@desc: 
	@@param: 
	@@retorno:
	@@pendiente:

	------
	Niveles de acceso para tabular los elementos que conforman una clase: 
	* (el nivel de mas arriba puede acceder a los de mas abajo)
	* interno 		: Elemento accedido solo desde la clase
	* nucleo		: Elemento accedido por el nucleo
	* objeto		: Elemento accedido por una clase standart del sistema (hija de 'objeto')
	* actividad		: Elemento accedio
	-----

* 	Nota: el modificador pendiente no se escribe
*/

class documentador
/*
	@@acceso: actividad
	@@desc: Esta clase implementa generador de documentacion
*/
{
	var $archivo;			// interno | string | Archivo que se va a parsear
	var $tipo_archivo;		// interno | string | Tipo de unidad de CODIGO a la que pertenece el archivo
	var $acceso;			// interno | array | Tipo de acceso
	var $arbol;				// interno | array | Arbol de codigo deducido
	var $funcion_actual;	// interno | string | FUNCION que se esta procesando actualmente
	var $clase_actual;		// interno | string | CLASE que se esta procesando actualemente
	var $estructura_actual;	// interno | string | Estructura actual (funcion o clase)
	var $javascript;		// interno | int | Define si el contexto es de PHP o JAVASCRIPT
	var $pepe;
	
	function documentador($archivo,$acceso=null)
/*
	@@acceso: actividad
	@@desc: Constructor de la clase
	@@param: string | nombre de archivo
	@@param: array | Lista de 'accesos' que se van a mostrar | array("actividad")
*/
	{
		$this->archivo = $archivo;
		$this->javascript = 0;
		if(is_array($acceso)){
			$this->acceso = $acceso;
		}else{
			//Accesos visibles PREDETERMINADOS
//			$this->acceso = array("actividad");
			$this->acceso = array("actividad","interno");
		}
	}
//-------------------------------------------------------------------------

	function procesar()
/*
	@@acceso: actividad
	@@desc: Parsea el archivo y crea el arbol de codigo
*/
	{
		$fd = @fopen ($this->archivo, "r");
		if(!is_resource($fd)){
			echo ei_mensaje("ERROR: '$archivo' no es un archivo valido\n" );
		}
		else
		{
			$linea = 1;
		    while (!feof ($fd))
	    	{
	        	$buffer = fgets($fd, 4096);
				//---------------------------------------------------
				//------- Exclusion de codigo Javascript ------------
				//---------------------------------------------------
				if(preg_match("/^\s*<script/i",$buffer)){	
				//Comienza una seccion de JAVASCRIPT
					if(!preg_match("/<\/script/i",$buffer)){
						//Esto hay que hacerlo con un LOOKAHEAD assertion
						//Comprueba que el tag no se habra y despues se cierre
						$this->javascript = 1;
					}
				}
				if(preg_match("/^\s*<\/script/i",$buffer)){	
					//Termina una seccion de JAVASCRIPT
					$this->javascript = 0;
				}
				//-------------------------------
				//------- CLASS -----------------
				//-------------------------------
				if(preg_match("/^\s*class[\s]*([^\{\s]*)/i",$buffer,$resultado))
	        	{
					$this->clase_actual = $resultado[1];
					$this->estructura_actual = "c";
					//Creo una rama para la nueva clase
					//$this->arbol["clases"][ $this->clase_actual ] = array();
					$this->arbol["clases"][ $this->clase_actual ]['linea'] = $linea;
	    		}
				//-------------------------------
	    		//------- FUNCTION --------------
				//-------------------------------
				if(preg_match("/^\s*function[\s]*([^(\s]*).*/i",$buffer,$resultado))
	        	{
					if(!$this->javascript)
					{
						$this->funcion_actual = $resultado[1];
						$this->estructura_actual = "f";
						if( isset($this->clase_actual) && (array_key_exists($this->clase_actual,$this->arbol["clases"])) )
						{
							//La funcion es el metodo de una clase
							//$this->arbol["clases"][ $this->clase_actual ]["metodos"][ $this->funcion_actual ] = array();
							$this->arbol["clases"][ $this->clase_actual ]["metodos"][ $this->funcion_actual ]['linea'] = $linea;
						}else{
							//$this->arbol["funciones"][ $this->funcion_actual ] = array();
							$this->arbol["funciones"][ $this->funcion_actual ]['linea'] = $linea;
						}
					}
	    		}
				//-------------------------------
				//-------- VAR ------------------
				//-------------------------------
		    	if(preg_match("/^\s*var[\s]*[$]([^\s]*);.*?\/\/(.*)$/i",$buffer,$resultado))
	        	{
					if( isset($this->clase_actual) && (array_key_exists($this->clase_actual,$this->arbol["clases"])) )
					{
						//Nueva PROPIEDAD para la CLASE actual
						$doc = preg_split("/\s*\|\s*/",$resultado[2]);
						//Modificadores por defento
						$doc_ok = array(	"acceso" => (isset($doc[0])? trim($doc[0]) : 'interno'),
											"tipo" => (isset($doc[1])? trim($doc[1]) : 'mixto'),
											"desc" => (isset($doc[2])? trim($doc[2]) : '') );	
						$this->arbol["clases"][ $this->clase_actual ]["propiedades"][ $resultado[1] ] = $doc_ok;
					}else{
						//Javascript?
					}
				}
				//-------------------------------
				//-------- MODIFICADORES --------
				//-------------------------------
				//Modificadores a capturar.
				$modificadores = array("acceso","desc","retorno","param","pendiente");
				foreach( $modificadores as $modificador)
	        	{
			    	if(preg_match("/@@".$modificador."[\s]*:[\s]*(.*)$/i",$buffer,$resultado)){
						//1)**********  Determino el VALOR del modificador  ***************
						if(trim($resultado[1])!=""){
							if( $modificador == "retorno" )
							{
								//Modificadores que poseen TIPO de DATO y Descripcion
								$temp = preg_split("/\s*\|\s*/",$resultado[1]);
								$temp_ok = array( 	"tipo" => (isset($temp[0])? $temp[0] : 'mixto'),
													"desc" => (isset($temp[1])? $temp[1] : '') );
								$valor = $temp_ok;
							}elseif($modificador == "param")
							{
								//Modificadores que poseen TIPO de DATO y Descripcion
								$temp = preg_split("/\s*\|\s*/",$resultado[1]);
								$temp_ok = array( 	"tipo" => (isset($temp[0])? $temp[0] : 'mixto'),
													"desc" => (isset($temp[1])? $temp[1] : null ),
													"defecto" => (isset($temp[2])? $temp[2] : null) );
								$valor = $temp_ok;
							}else{
								//Por defecto los modificadores tienen solo texto
								$valor = $resultado[1];
							}
						}else{
							$valor = null;
						}
						//2)**********  Posiciono el MODIFICADOR dentro del ARBOL  ********
						if( isset($this->clase_actual) && 
							(array_key_exists($this->clase_actual,$this->arbol["clases"])) )
						{
							switch($this->estructura_actual)
							{
								case "c": 	//El modificador es general de la clase
									if(isset($this->arbol["clases"][$this->clase_actual][$modificador])){
										//Si el modificador ya existe, concatena en valor.
										$this->arbol["clases"][$this->clase_actual][$modificador] .= $valor;	
									}else{
										$this->arbol["clases"][$this->clase_actual][$modificador] = $valor;	
									}
									break;	
								case "f":	//El modificador pertenede a un METODO
									if($modificador == "param"){
										//El modificador 'param' cuelga del arbol de otra manera...
										$this->arbol["clases"][$this->clase_actual]["metodos"]
											[$this->funcion_actual][$modificador][] = $valor;
									}else{
										if(isset($this->arbol["clases"][$this->clase_actual]["metodos"][$this->funcion_actual][$modificador]))
										{
											//Si el modificador ya existe, concatena en valor.
											$this->arbol["clases"][$this->clase_actual]["metodos"]
												[$this->funcion_actual][$modificador] .= $valor;
										}else{
											$this->arbol["clases"][$this->clase_actual]["metodos"]
												[$this->funcion_actual][$modificador] = $valor;
										}
									}
									break;
							}
						}elseif( isset($this->funcion_actual) && 
							(array_key_exists($this->funcion_actual,$this->arbol["funciones"])) )
						{
							//Las propiedades son de una funcion suelta
							if($modificador == "param"){
								//El modificador 'param' cuelga del arbol de otra manera...
								$this->arbol["funciones"][ $this->funcion_actual ][$modificador][] = $valor;
							}else{

								if(isset($this->arbol["funciones"][ $this->funcion_actual ][$modificador]))
								{
									//Si el modificador ya existe, concatena en valor.
									$this->arbol["funciones"][ $this->funcion_actual ][$modificador] .= $valor;
								}else{
									$this->arbol["funciones"][ $this->funcion_actual ][$modificador] = $valor;
								}
							}
						}else{
							//El modificador se reconocio por fuera de las estructuras conocidas
							//---> ERROR??
						}
					}
				}
				//-------------------------------
				//-------------------------------
				$linea ++;
			}
		}
		//Defino que tipo de archivo se parseo
		$this->definir_tipo_archivo();
	}
//-------------------------------------------------------------------------

	function info_arbol()
/*
	@@acceso: actividad
	@@desc: hace un DUMP del arbol de codigo generado
*/
	{
		ei_arbol( $this->arbol);
	}
//-------------------------------------------------------------------------

	function definir_tipo_archivo()
/*
	@@acceso: interno
	@@desc: Determina que tipo de archivo se documentando
*/
	{
		if(isset($this->arbol["funciones"])){
			$this->tipo_archivo = "funciones";
		}elseif(isset($this->arbol["clases"])){
			$this->tipo_archivo = "clases";
		}else
		{
			$this->tipo_archivo = "actividad";
		}
	}

//-------------------------------------------------------------------------
//------------------------  SALIDA  ---------------------------------------
//-------------------------------------------------------------------------
	
	function obtener_html()
/*
	@@acceso: actividad
	@@desc: Genera HTML. Muestra la informacion existente en el arbol generado
*/
	{
		echo "<div align='center'><table class='tabla-0' width='90%'>\n";
		//Escribo el cuerpo de la documentacion segun el tipo de archivo
		switch($this->tipo_archivo)
		{
			case "clases":
			//Achivo de CLASES
//				echo "<tr>\n";
//				echo "<td class='doc-archivo-tipo'>&nbsp;(Archivo de declaracion de CLASES)</td>\n";
//				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='doc-archivo'>"; 
				$this->generar_autodoc_clases();
				echo "</td>\n";
				echo "</tr>\n";
				break;
			case "funciones":
//				echo "<tr>\n";
//				echo "<td class='doc-archivo-tipo'>&nbsp;(Archivo de declaracion de FUNCIONES GLOBALES)</td>\n";
//				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='doc-archivo'>"; 
				$this->generar_autodoc_funciones();
				echo "</td>\n";
				echo "</tr>\n";
				break;
			default:
				echo ei_mensaje("No existe un plan de documentacion para el archivo definido");
		}
//		echo "<tr>\n";
//		echo "<td class='doc-archivo'>". $this->archivo."</td>\n";
//		echo "</tr>\n";
//		echo "</table></div>\n";	
		enter();
//		$this->info_arbol();
	}

//----------------------------------------------------------------------------
//------------------   ESQUEMAS segun el TIPO de ARCHIVO   -------------------
//----------------------------------------------------------------------------

	function generar_autodoc_clases()
/*
	@@acceso: interno
	@@desc: Esquema de documentacion de archivos de CLASES
*/
	{	
		foreach( array_keys($this->arbol["clases"]) as $clase)
		{
			//Nombre de la CLASE
			echo "<div class='doc-clase-nombre' align='left'>$clase</div>";
			//Descripcion de la CLASE			
			if(isset($this->arbol["clases"][$clase]["desc"])){
				echo "<div class='doc-clase-desc' align='left'>".
					$this->arbol["clases"][$clase]["desc"]."</div>";
				enter();
			}
			//Presentacion de PROPIEDADES
			if(isset($this->arbol["clases"][$clase]["propiedades"])){
				$cantidad = count($this->arbol["clases"][$clase]["propiedades"]);
				echo "<div class='doc-clase-seccion' align='left'>Propiedades ($cantidad)</div>";
				$this->obtener_html_propiedades($clase);
			}else{
				$cantidad = 0;
				echo "<div class='doc-clase-seccion' align='left'>Propiedades ($cantidad)</div>";
			}
			//Presentacion de METODOS
			$cantidad = count($this->arbol["clases"][$clase]["metodos"]);
			echo "<div class='doc-clase-seccion' align='left'>Metodos ($cantidad)</div>";
			//Listado de metodos disponibles
			$this->obtener_html_lista_metodos($clase);
			foreach( array_keys($this->arbol["clases"][$clase]["metodos"]) as $metodo)
			{
				$this->obtener_html_funcion($metodo, $this->arbol["clases"][$clase]["metodos"][$metodo] );
			}
			ei_linea();
			enter();
		}
	}

	function generar_autodoc_funciones()
/*
	@@acceso: interno
	@@desc: Esquema de documentacion de archivos de FUNCIONES
*/
	{	
		foreach( array_keys($this->arbol["funciones"]) as $funcion)
		{
			$this->obtener_html_funcion($funcion, $this->arbol["funciones"][$funcion] );
		}
	}

//-------------------------------------------------------------------------	
//------------------------  Bloques ---------------------------------------	
//-------------------------------------------------------------------------	

	function obtener_html_propiedades($clase)
/*
	@@acceso: interno
	@@desc: Genera la documentacion de las propiedades de una clase
 	@@param: string | nombre de la clase
*/
	{	
		if(isset($this->arbol["clases"][$clase]["propiedades"])){
			echo "<table class='tabla-0'>\n";
			foreach( array_keys($this->arbol["clases"][$clase]["propiedades"]) as $prop)
			{
				$propiedad =& $this->arbol["clases"][$clase]["propiedades"][$prop];
				if( in_array(trim($propiedad["acceso"]), $this->acceso)) 
				{
					echo "<tr>\n";
					echo "<td class='doc-func-acceso' colspan='2'>".$propiedad['acceso']."</td>\n";
					echo "<td class='doc-func-valor' colspan='2'><i>[".$propiedad['tipo']."]</i> </td>\n";
					echo "<td class='doc-func-valor' colspan='2'>".$propiedad['desc']."</td>\n";
					echo "</tr>\n";
				}
			}
			echo "</table>\n";
			enter();
		}
	}
//-------------------------------------------------------------------------	

	function obtener_html_lista_metodos($clase)
/*
	@@acceso: interno
	@@desc: Genera la Lista de metodos
 	@@param: string | nombre de la clase
*/
	{	
		echo "<table class='tabla-0' width='400'>\n";
		foreach( array_keys($this->arbol["clases"][$clase]["metodos"]) as $metodo)
		{
			if(isset($this->arbol["clases"][$clase]["metodos"][$metodo]["acceso"])){
				if( in_array(trim($this->arbol["clases"][$clase]["metodos"][$metodo]["acceso"]),$this->acceso )) 
				//SI el nivel de acceso se encuentra entre los solicitado
				{
					echo "<tr>\n";
					echo "<td class='doc-func-acceso' colspan='2'>".
					$this->arbol["clases"][$clase]["metodos"][$metodo]['acceso']."</td>\n";
					echo "<td class='doc-func-valor' colspan='2'><b><a href='#$metodo' class='lista-link'>$metodo</b></a>&nbsp;(".
					$this->arbol["clases"][$clase]["metodos"][$metodo]['linea'] .")</td>\n";
					echo "</tr>\n";
				}
			}
		}
		echo "</table>\n";
		enter();
	}
//-------------------------------------------------------------------------	

	function obtener_html_funcion($nombre, &$funcion)
/*
	@@acceso: interno
	@@desc: Genera la documentacion de una funcion
	@@param: string | nombre de la funcion/metodo
	@@param: string | referencia a la funcion dentro del arbol de codigo
*/
	{
		//Si el ACCESO esta definido, y coincide con los accesos definidos, se muestra
		if( isset($funcion["acceso"]) &&
			in_array(trim($funcion["acceso"]),$this->acceso) )
		{
			echo "<table class='tabla-0' width='550'>\n";
			//-[1]- NOMBRE
			echo "<tr>\n";
			echo "<td class='doc-func-nombre' colspan='2'><a name='".$nombre."'>".$nombre."</a></td>\n";
			echo "</tr>\n";
			//-[2]- ACCESO 
			if(isset($funcion['acceso']))
			{
				echo "<tr>\n";				
				echo "<td width='110' class='doc-func-etiqueta'>Acceso</td>\n";
				echo "<td class='doc-func-acceso'>";
				echo $funcion['acceso'];
				echo "</td>\n";
				echo "</tr>\n";				
			}
			//-[3]- DESCRIPCION
			if(isset($funcion['desc']))
			{
				echo "<tr>\n";				
				echo "<td width='110' class='doc-func-etiqueta'>Descripcion</td>\n";
				echo "<td class='doc-func-valor'>";
				echo $funcion['desc'];
				echo "</td>\n";
				echo "</tr>\n";				
			}
			//-[4]- PARAMETROS
			if(isset($funcion['param']))
			{
				echo "<tr>\n";
				echo "<td width='110' class='doc-func-etiqueta'>Parametros</td>\n";
				echo "<td class='doc-func-valor'>";
				echo "<ol class='doc-func-lista'>\n";
				foreach($funcion['param'] as $parametro){
					$tipo = (trim($parametro['tipo'])!="")? $parametro['tipo'] : 'mixed';
					$desc = (trim($parametro['desc'])!="")? $parametro['desc'] : 'NO DEFINIDO';
					//El parametro es opcional
					if(isset($parametro['defecto'])){
						$defecto = (trim($parametro['defecto'])!="")? $parametro['defecto'] : 'NO DEFINIDO';
						echo "<li ><div class='doc-func-param-opcional'>";
						echo "<i>[$tipo]</i><br><b>$desc</b><br>Valor pred.: $defecto";
						echo "</div></li>\n";
					}else{
						echo "<li ><div class='doc-func-param-obligatorio'>";
						echo "<i>[$tipo]</i> $desc";
						echo "</div></li>\n";
					}
				}
				echo "</ol>";
				echo "</td>\n";
				echo "</tr>\n";				
			}
			//-[5]- RETORNO
			if(isset($funcion['retorno']))
			{
				echo "<tr>\n";				
				echo "<td width='110' class='doc-func-etiqueta'>Retorno</td>\n";
				echo "<td class='doc-func-valor'>";
				echo "<i>[" . $funcion['retorno']['tipo'] ."]</i> " . 
							$funcion['retorno']['desc'];
				echo "</td>\n";
				echo "</tr>\n";				
			}
			echo "</table>\n";
			enter();
		}
	}
//-------------------------------------------------------------------------	
}
?>