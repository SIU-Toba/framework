<?php

class cuadro extends toba_testing_pers_ei_cuadro
{
    function obtener_clave_pura_fila($fila)
	//Genero la CLAVE
    {
        $id_fila = "";
        foreach($this->columnas_clave as $clave){
            $id_fila .= $this->datos[$fila][$clave] . apex_qs_separador;
        }
        $id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));   
		if(apex_pa_encriptar_qs)
		{
			$encriptador = toba::encriptador();
			//ATENCION: me faltaria ponerle un uniqid("") para que sea mas robusto;
			$id_fila = $encriptador->cifrar($id_fila);
		}
        return $id_fila;
    }

    function obtener_html($mostrar_cabecera=true, $titulo=null)
    //Genera el HTML del cuadro
    {
		//Reproduccion del titulo
		if(isset($titulo)){
			$this->memoria["titulo"] = $titulo;
			$this->memorizar();
		}else{
			if(isset($this->memoria["titulo"])){
				$titulo = $this->memoria["titulo"];
				$this->memorizar();
			}
		}
		//Manejo del EOF
        if($this->filas == 0){
            //La consulta no devolvio datos!
            if ($this->info_cuadro["eof_invisible"]!=1){
                if(trim($this->info_cuadro["eof_customizado"])!=""){
                    echo ei_mensaje($this->info_cuadro["eof_customizado"]);
                }else{
                    echo ei_mensaje("La consulta no devolvio datos!");
                }
            }
        }else{
            if(!($ancho=$this->info_cuadro["ancho"])) $ancho = "80%";
            //echo "<br>\n";
            
			//--Scroll       
	        if($this->info_cuadro["scroll"]){
				$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "500";
				$alto = isset($this->info_cuadro["alto"]) ? $this->info_cuadro["alto"] : "auto";
				echo "<div style='overflow: scroll; height: $alto; width: $ancho; border: 1px inset; padding: 0px;'>";
			//	echo "<table class='tabla-0'>\n";
			}else{
				$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "100";
			//	echo "<table width='$ancho' class='tabla-0'>\n";
			}
            
            echo "<table class='objeto-base' width='$ancho'>\n\n\n";

            if($mostrar_cabecera){
                echo "<tr><td>";
                $this->barra_superior(null, true,"objeto-ei-barra-superior");
                echo "</td></tr>\n";
            }
            if($this->info_cuadro["subtitulo"]<>""){
                echo"<tr><td class='lista-subtitulo'>". $this->info_cuadro["subtitulo"] ."</td></tr>\n";
            }

            echo "<tr><td>";
            echo "<TABLE width='100%' class='tabla-0'>";
            //------------------------ Genero los titulos
            echo "<tr>\n";
            for ($a=0;$a<$this->cantidad_columnas;$a++)
            {
                if(isset($this->info_cuadro_columna[$a]["ancho"])){
                    $ancho = " width='". $this->info_cuadro_columna[$a]["ancho"] . "'";
                }else{
                    $ancho = "";
                }
                echo "<td class='lista-col-titulo' $ancho>\n";
                $this->cabecera_columna(    $this->info_cuadro_columna[$a]["titulo"],
                                            $this->info_cuadro_columna[$a]["valor_sql"],
                                            $a );
                echo "</td>\n";
            }
            //-- Evento FIJO de seleccion
			echo "<td class='lista-col-titulo'>\n";
            echo "</td>\n";
            echo "</tr>\n";
			//-------------------------------------------------------------------------
            //----------------------- Genero VALORES del CUADRO -----------------------
			//-------------------------------------------------------------------------
            for ($f=0; $f< $this->filas; $f++)
            {
				$resaltado = "";
				$clave_fila = $this->get_clave_fila($f);
				//$this->clave_seleccionada
				//$resaltado = "_s";
				
                echo "<tr>\n";
                for ($a=0;$a< $this->cantidad_columnas;$a++)
                {
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
                        //Hay que hacer un formateo externo
                        if(trim($this->info_cuadro_columna[$a]["valor_proceso_parametros"])!=""){
                            $funcion = $this->info_cuadro_columna[$a]["valor_proceso_parametros"];
                            //Formateo el valor
                            $valor = $funcion($valor);
                        }
                    }elseif(isset($this->info_cuadro_columna[$a]["valor_fijo"])){
                        $valor = $this->info_cuadro_columna[$a]["valor_fijo"];
                    }else{
                        $valor = "";
                    }
                    //*** 2) PRoceso la columna
                    //Esto no se utiliza desde el instanciador
                    if(!$this->solicitud->hilo->entorno_instanciador()){
                        if(isset($this->info_cuadro_columna[$a]["valor_proceso"])){
                            $metodo_procesamiento = $this->info_cuadro_columna[$a]["valor_proceso"];
                            $valor = $this->$metodo_procesamiento($f, $valor);
                        }
                    }
                    //*** 3) Generacion de VINCULOS!
                    if(trim($this->info_cuadro_columna[$a]["vinculo_indice"])!=""){
                        $id_fila = $this->get_clave_fila($f);
                        //Genero el VINCULO
                        $vinculo = $this->solicitud->vinculador->get_vinculo_de_objeto( $this->id,
                                                                                $this->info_cuadro_columna[$a]["vinculo_indice"],
                                                                                $id_fila, true, $valor);
                        //El vinculador puede no devolver nada en dos casos: 
                        //No hay permisos o el indice no existe
                        if(isset($vinculo)){
                            $valor = $vinculo;
                        }
                    }
                    //*** 4) Genero el HTML
                    echo "<td class='".$this->info_cuadro_columna[$a]["estilo"]. $resaltado . "'>\n";
                    echo $valor;
                    echo "</td>\n";
                    //----------> Termino la CELDA!!
                }
	            //-- Evento FIJO de seleccion
					echo "<td class='lista-col-titulo'>\n";
					echo toba_form::image($this->submit.$clave_fila,toba_recurso::imagen_toba("doc.gif"), 
									"onClick='seleccionar(\"{$this->datos[$f]['id']}\", \"{$this->datos[$f]['descripcion']}\")';");
	            	echo "</td>\n";
				//----------------------------
                echo "</tr>\n";
            }
            //----------------------- Genero totales??
			$this->generar_html_totales();
            echo "</table>\n";
            echo "</td></tr>\n";
            echo "</table>\n";
            
			//Y por cierto......... si esto tenia scroll, cierro el div !!!
			if($this->info_cuadro["scroll"]){
				echo "</div>";
			}
		            
            //echo "<br>\n";
        }
    }

}


?>