<?
require_once("nucleo/browser/interface/form.php");// Elementos STANDART de formulario
require_once("objeto_cuadro.php");                     //Ancestro de todos los OE

class objeto_ei_cuadro extends objeto_cuadro
/*
    @@acceso: publico
    @@desc: Esta clase implementa un listado ordenable y paginable.
    
	Cosas para una interface: obtener_consumo_dao, set_dao, obtener_evento
	Falta un metodo que devuelva el ID del registro que se eligio...
    
*/
{
 	var $submit;
	var $clave_seleccionada;
	var $id_en_padre;
 
    function objeto_ei_cuadro($id)
/*
    @@acceso: constructor
    @@desc: 
*/
    {
        parent::objeto_cuadro($id);
        $this->submit = "ei_cuadro_" . $this->id[1] ."_";
		$this->clave_seleccionada = null;
		if(!isset($this->columnas_clave)){
			$this->columnas_clave = array( apex_buffer_clave );
		}
		//El cuadro posee un evento de seleccion por defecto?
		if(isset($this->info_cuadro["ev_seleccion"])){
			if($this->info_cuadro["ev_seleccion"]=="1"){
				$this->ev_seleccion = true;
			}else{
				$this->ev_seleccion = false;
			}
		}else{
			$this->ev_seleccion = false;
		}
		$this->objeto_js = "objeto_cuadro_{$id[1]}";		
    }

	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//------------- Cuadro ----------------
		$sql["info_cuadro"]["sql"] = "SELECT	titulo as titulo,		
								c.subtitulo						as	subtitulo,		
								c.sql							as	sql,			
								c.columnas_clave				as	columnas_clave,		 
								c.archivos_callbacks			as	archivos_callbacks,		
								c.ancho							as	ancho,			
								c.ordenar						as	ordenar,			
								c.exportar						as	exportar_xls,		 
								c.exportar_rtf					as	exportar_pdf,		 
								c.paginar						as	paginar,			
								c.tamano_pagina					as	tamano_pagina,
								c.scroll						as	scroll,
								c.scroll_alto					as	alto,
								c.eof_invisible					as	eof_invisible,		 
								c.eof_customizado				as	eof_customizado,
								c.pdf_respetar_paginacion		as	pdf_respetar_paginacion,	
								c.pdf_propiedades				as	pdf_propiedades,
								c.asociacion_columnas			as	asociacion_columnas,
								c.ev_seleccion					as	ev_seleccion,
								c.dao_nucleo_proyecto			as  dao_nucleo_proyecto,	
								c.dao_nucleo					as  dao_clase,			
								c.dao_metodo					as  dao_metodo,
								c.dao_parametros				as  dao_parametros,
								n.archivo 						as	dao_archivo
					 FROM		apex_objeto_cuadro c
					 			LEFT OUTER JOIN	apex_nucleo n
					 			ON c.dao_nucleo_proyecto = n.proyecto
					 			AND c.dao_nucleo = n.nucleo
					 WHERE	objeto_cuadro_proyecto='".$this->id[0]."'	
					 AND		objeto_cuadro='".$this->id[1]."';";
		return $sql;
	}
  
//################################################################################
//###########################                         ############################
//###########################         UTILERIA        ############################
//###########################                         ############################
//################################################################################
    

	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];		
	}
//--------------------------------------------------------------------------

	public function agregar_observador($observador)
	{
		$this->observadores[] = $observador;
	}

	function eliminar_observador($observador){}

	function definir_eventos()
	{
	}

	function disparar_eventos()
	{
		$this->recuperar_interaccion();
		if( $evento = $this->obtener_evento() ){
			foreach(array_keys($this->observadores) as $id){
				$this->observadores[$id]->registrar_evento( $this->id_en_padre, $evento, $this->obtener_clave() );
			}
		}
	}
//--------------------------------------------------------------------------

	function obtener_evento()
	{
		if(isset($this->clave_seleccionada)){
			return "seleccion";
		}else{
			return null;
		}
	}
//--------------------------------------------------------------------------

	function obtener_clave()
	{
		return $this->clave_seleccionada;
	}
//--------------------------------------------------------------------------

	function recuperar_interaccion()
	{
		//Si se presiono un boton, busco la clave
		foreach (array_keys($_POST) as $post)
        {
			if(preg_match("/".$this->submit.".*_x/", $post)){
				$sobra = strlen($this->submit);
				$clave = substr($post, $sobra, (strlen($post) - $sobra - 2 ));
				//PHP convierte los "." es "_" en los botones de submit (Es raro, pero es asi)
				//$clave = ereg_replace("_",".",$clave);//echo $partida;
				if(apex_pa_encriptar_qs){
					$encriptador = toba::get_encriptador();
					$clave = $encriptador->descifrar( $clave );
				}
				if(count($this->columnas_clave) > 1 )
				{
					//La clave es un array, devuelvo un array asociativo con el nombre de las claves
					$clave = explode(apex_qs_separador, $clave);
					for($a=0;$a<count($clave);$a++){
						$this->clave_seleccionada[$this->columnas_clave[$a]] = $clave[$a];		
					}
				}else{
					$this->clave_seleccionada = $clave;			
				}
			}
		}
	}
//--------------------------------------------------------------------------

    function obtener_clave_fila($fila)
	//Genero la CLAVE
    {
        $id_fila = "";
        foreach($this->columnas_clave as $clave){
            $id_fila .= $this->datos[$fila][$clave] . apex_qs_separador;
        }
        $id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));   
		if(apex_pa_encriptar_qs)
		{
			$encriptador = toba::get_encriptador();
			//ATENCION: me faltaria ponerle un uniqid("") para que sea mas robusto;
			$id_fila = $encriptador->cifrar($id_fila);
		}
        return $this->submit . $id_fila;
    }
//--------------------------------------------------------------------------

    function cargar_datos($datos=null,$memorizar=true)
/*
    @@acceso: publico
    @@desc: Carga los datos del cuadro desde la base
    @@param: array | sentencias WHERE a acoplar
    @@param: array | Sentencias FROM a acoplar
    @@param: boolean | Desactivar la paginacion
    @@retorno: boolean | Estado resultante de la operacion
*/
    {
        $this->sql = "";
		if(isset($datos)){
	        $this->datos = $datos;
		}else{
			if(trim($this->info_cuadro['dao_metodo'])!=""){
				include_once($this->info_cuadro['dao_archivo']);
				$sentencia = "\$this->datos = " . $this->info_cuadro['dao_clase'] 
											. "::" .  $this->info_cuadro['dao_metodo']
											. "(".$this->info_cuadro['dao_parametros'].");";
				eval($sentencia);//echo $sentencia;
			}
		}
        //ei_arbol($this->datos,"DATOS");
        if($this->ordenar_datos){
            $this->ordenar();
        }
		$this->filas = count($this->datos);
        return true;
    }
//--------------------------------------------------------------------------

    function obtener_html($mostrar_cabecera=true, $titulo=null)
    //Genera el HTML del cuadro
    {
		//Reproduccion del titulo
		if(isset($titulo)){
			$this->memoria["titulo"] = $titulo;
		}else{
			if(isset($this->memoria["titulo"])){
				$titulo = $this->memoria["titulo"];
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
            $this->generar_html_barra_paginacion();

            echo "<tr><td>";
            echo "<TABLE width='100%' class='tabla-0'  id='cuerpo_{$this->objeto_js}'>";
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
			if($this->ev_seleccion){
				echo "<td class='lista-col-titulo'>\n";
	            echo "</td>\n";
	            echo "</tr>\n";
			}
			//-------------------------------------------------------------------------
            //----------------------- Genero VALORES del CUADRO -----------------------
			//-------------------------------------------------------------------------
            for ($f=0; $f< $this->filas; $f++)
            {
				$resaltado = "";
				$clave_fila = $this->obtener_clave_fila($f);
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
                        $id_fila = $this->obtener_clave_fila($f);
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
                    echo "<td class='".$this->info_cuadro_columna[$a]["estilo"]. $resaltado . "'>\n";
                    echo $valor;
                    echo "</td>\n";
                    //----------> Termino la CELDA!!
                }
	            //-- Evento FIJO de seleccion
				if($this->ev_seleccion){
					echo "<td class='lista-col-titulo'>\n";
					echo form::image( $this->obtener_clave_fila($f) ,recurso::imagen_apl("doc.gif"));
	            	echo "</td>\n";
	            }
				//----------------------------
                echo "</tr>\n";
            }
            //----------------------- Genero totales??
			$this->generar_html_totales();
            echo "</table>\n";
            echo "</td></tr>\n";
            $this->generar_html_barra_paginacion();
            echo "</table>\n";
            
			//Y por cierto......... si esto tenia scroll, cierro el div !!!
			if($this->info_cuadro["scroll"]){
				echo "</div>";
			}
		            
            //echo "<br>\n";
        }
    }
//--------------------------------------------------------------------------

}