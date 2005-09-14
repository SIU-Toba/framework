<?
require_once("objeto_cuadro.php");						//Ancestro de todos los OE

class objeto_cuadro_reg extends objeto_cuadro
/*
	@@acceso: publico
	@@desc: Esta clase implementa un listado ordenable y paginable.
*/
{
	
//################################################################################
//###########################                         ############################
//###########################      INICIALIZACION     ############################
//###########################                         ############################
//################################################################################
    	
	function objeto_cuadro_reg($id)
/*
	@@acceso: constructor
	@@desc: 
*/
	{
		parent::objeto_cuadro($id);
	}

//--------------------
//------- HTML -------
//--------------------

	function obtener_html($mostrar_cabecera=true)
	//Genera el HTML del cuadro
	{
		$filas = count($this->datos);
		if($filas == 0){
			//La consulta no devolvio datos!
			if ($this->info_cuadro["eof_invisible"]!=1){
				if(trim($this->info_cuadro["eof_customizado"])!=""){
					echo ei_mensaje($this->info_cuadro["eof_customizado"]);
				}else{
					echo ei_mensaje("La consulta no devolvio datos!");
				}
			}
		}else
		{
			if(!($ancho=$this->info_cuadro["ancho"])) $ancho = "80%";
			//echo "<br>\n";
			echo "\n\n<div align='center'>\n";
			echo "<table class='objeto-base' width='$ancho'>\n\n\n";

			if($mostrar_cabecera){
				echo "<tr><td>";
				$this->barra_superior();
				echo "</td></tr>\n";
			}
			if($this->info_cuadro["subtitulo"]<>""){
				echo"<tr><td class='lista-subtitulo'>". $this->info_cuadro["subtitulo"] ."</td></tr>\n";
			}

			echo "<tr><td>";

			//----------------------- Genero las filas
			for ($f=0; $f< $filas; $f++)
			{

			echo "<TABLE width='100%' class='tabla-0'>";
			for ($a=0;$a<$this->cantidad_columnas;$a++)
			{
				echo "<tr>\n";
				echo "<td class='lista-fila-titulo'>\n";
				//------------------------ Genero los titulos
				$this->cabecera_columna(	$this->info_cuadro_columna[$a]["titulo"],
											$this->info_cuadro_columna[$a]["valor_sql"],
											$a );
				echo "</td>\n";

					//----------> Comienzo una CELDA!!
					//*** 1) Recupero el VALOR
					if(isset($this->info_cuadro_columna[$a]["valor_sql"])){
						$valor = $this->datos[$f][$this->info_cuadro_columna[$a]["valor_sql"]];
						//Hay que formatear?
						if(isset($this->info_cuadro_columna[$a]["valor_sql_formato"])){
							$funcion = "formato_" . $this->info_cuadro_columna[$a]["valor_sql_formato"];
							//Formateo el valor
							$valor = $funcion($valor);
						}
						//Hay que procesar?
						//Esto no se utiliza desde el instanciador
						if(!$this->solicitud->hilo->entorno_instanciador()){
							if(isset($this->info_cuadro_columna[$a]["valor_proceso"])){
								$metodo_procesamiento = $this->info_cuadro_columna[$a]["valor_proceso"];
								$valor = $this->$metodo_procesamiento($f, $valor);
							}
						}
					}elseif(isset($this->info_cuadro_columna[$a]["valor_fijo"])){
						$valor = $this->info_cuadro_columna[$a]["valor_fijo"];
					}else{
						$valor = "";
					}
					//*** 3) Generacion de vinculos
					if( (trim($this->info_cuadro_columna[$a]["vinculo_indice"])!="") 
					&& (is_array($this->columnas_clave)) )
					{
						//Genero el ID de la fila (el dato que quiero prop24agar)
						$id_fila = "";
						foreach($this->columnas_clave as $clave){
							$id_fila .= $this->datos[$f][$clave] . apex_qs_separador;
						}
						$id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));	
						//Genero el VINCULO
						$vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto( $this->id,
																				$this->info_cuadro_columna[$a]["vinculo_indice"],
																				$id_fila, true, $valor);
						//El vinculador puede no devolver nada en dos casos: 
						//No hay permisos o el indice no existe
						if(isset($vinculo)){
							$valor = $vinculo;
						}
					}

				//*** 4) Genero el HTML
				echo "<td width='99%' class='".$this->info_cuadro_columna[$a]["estilo"]."'>\n";
				echo $valor;
				echo "</td>\n";
				echo "</tr>\n";
			}
			//Linea de 2px que separa registros
			echo "<tr><td>" . recurso::imagen_apl('nulo.gif',true,null,3) . "</td></tr>";
			echo "</table>\n";
			}
			echo "</td></tr>\n";
			$this->generar_html_barra_paginacion();
			echo "</table>\n";
			echo "</div>";
			//echo "<br>\n";
		}
	}
}
?>