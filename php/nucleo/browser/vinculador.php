<?php
/* 
* ARREGLAR: que no se necesiten permisos al autovinculo!
* agregar un nivel de vinculos globales para un OBJETO puntual
*/
class vinculador 
/*
 	@@acceso: interno
	@@desc: Esta clase maneja la VINCULACION entre ITEMS. Conoce todos los lugares a los que el 
	@@desc: ITEM actual puede acceder (considerando el USUARIO que lo solicito)
*/
{
	var $solicitud;			//interno | referencia | Referencia a la SOLICITUD 
	var $prefijo;			//interno | string | prefijo de cualquier URL
	var $info;				//interno | array | Vinculos a los que se puede acceder
	var $indices_objeto;	//interno | array | Vinculos ordenados por OBJETO
	var $indices_item;		//interno | array | Vinculos ordenados por ITEM
		
	function vinculador(&$solicitud)
/*
 	@@acceso: nucleo
	@@desc: Generacion directa de una URL que representa un posible futuro acceso a la infraestructura
	@@param: referencia | Refenrencia a la Solicitud
*/
	{
		global $ADODB_FETCH_MODE, $db, $cronometro, $hilo;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$this->solicitud =& $solicitud;
		$sql =	"-- Vinculos GLOBALES del TOBA y del PROYECTO Y PROPIOS del ITEM ------------------------------------
				SELECT	v.origen_item_proyecto as       	origen_item_proyecto,
						v.origen_item as					origen_item,
                        v.origen_objeto_proyecto as 		origen_objeto_proyecto,
                        v.origen_objeto as 					origen_objeto,
                        v.destino_item_proyecto as			destino_item_proyecto,
                        v.destino_item 			as			destino_item,
                        v.destino_objeto_proyecto as 		destino_objeto_proyecto,
                        v.destino_objeto as 				destino_objeto,
						v.indice as 						indice,
						v.vinculo_tipo as					tipo,
						v.inicializacion as 				inicializacion,
						v.frame as							frame,
						v.canal as							canal,
						v.texto as							texto,
						v.imagen_recurso_origen as			imagen_recurso_origen,
						v.imagen as							imagen
				FROM	apex_vinculo v, 
                        apex_usuario_grupo_acc_item ui,
                        apex_usuario_proyecto up
				WHERE	( (v.origen_item = '".$this->solicitud->info["item"]."' AND
							v.origen_item_proyecto= '".$this->solicitud->info["item_proyecto"]."'	) 			
							OR (v.origen_item = '/vinculos' 
							AND ( (v.origen_item_proyecto = '".$this->solicitud->hilo->obtener_proyecto()."')
									OR (v.origen_item_proyecto = 'toba') )	)
						)
				AND		(ui.item = v.destino_item) AND (ui.proyecto = v.destino_item_proyecto)
                AND     (ui.usuario_grupo_acc = up.usuario_grupo_acc)  AND (ui.proyecto = up.proyecto)
				AND		(up.usuario = '".$this->solicitud->hilo->obtener_usuario()."')
				UNION
				-- Vinculos GLOBALES del TOBA y del PROYECTO Y PROPIOS del ITEM (publicos) -------------------------
				SELECT	v.origen_item_proyecto as       	origen_item_proyecto,
						v.origen_item as					origen_item,
                        v.origen_objeto_proyecto as 		origen_objeto_proyecto,
                        v.origen_objeto as 					origen_objeto,
                        v.destino_item_proyecto as			destino_item_proyecto,
                        v.destino_item 			as			destino_item,
                        v.destino_objeto_proyecto as 		destino_objeto_proyecto,
                        v.destino_objeto as 				destino_objeto,
						v.indice as 						indice,
						v.vinculo_tipo as					tipo,
						v.inicializacion as 				inicializacion,
						v.frame as							frame,
						v.canal as							canal,
						v.texto as							texto,
						v.imagen_recurso_origen as			imagen_recurso_origen,
						v.imagen as							imagen
				FROM	apex_vinculo v, 
						apex_item i
				WHERE	( (v.origen_item = '".$this->solicitud->info["item"]."' AND
							v.origen_item_proyecto= '".$this->solicitud->info["item_proyecto"]."'	) 			
							OR (v.origen_item = '/vinculos' 
							AND ( (v.origen_item_proyecto = '".$this->solicitud->hilo->obtener_proyecto()."')
									OR (v.origen_item_proyecto = 'toba') )	)
						)
				AND		(	(i.item = v.destino_item) AND (i.proyecto = v.destino_item_proyecto)
							AND	(i.publico =1) )
				UNION
				-- Vinculos de los OBJETOS asociados ---------------------------------------
				SELECT	v.origen_item_proyecto as       	origen_item_proyecto,
						v.origen_item as					origen_item,
                        v.origen_objeto_proyecto as 		origen_objeto_proyecto,
                        v.origen_objeto as 					origen_objeto,
                        v.destino_item_proyecto as			destino_item_proyecto,
                        v.destino_item 			as			destino_item,
                        v.destino_objeto_proyecto as 		destino_objeto_proyecto,
                        v.destino_objeto as 				destino_objeto,
						v.indice as 						indice,
						v.vinculo_tipo as					tipo,
						v.inicializacion as 				inicializacion,
						v.frame as							frame,
						v.canal as							canal,
						v.texto as							texto,
						v.imagen_recurso_origen as			imagen_recurso_origen,
						v.imagen as							imagen
				FROM	apex_vinculo v, 
						apex_item_objeto o,
                        apex_usuario_grupo_acc_item ui,
                        apex_usuario_proyecto up
				WHERE	o.proyecto = v.origen_objeto_proyecto
				AND		o.objeto = v.origen_objeto
				AND		o.item = '".$this->solicitud->info["item"]."' AND
						o.proyecto= '".$this->solicitud->info["item_proyecto"]."'
				AND		(ui.item = v.destino_item) AND (ui.proyecto = v.destino_item_proyecto)
           		AND		(ui.usuario_grupo_acc = up.usuario_grupo_acc)  AND (ui.proyecto = up.proyecto)
				AND		(up.usuario = '".$this->solicitud->hilo->obtener_usuario()."')
				UNION
				-- Vinculos de los OBJETOS asociados con destino AUTOVINCULO ----------------------
				SELECT	v.origen_item_proyecto as       	origen_item_proyecto,
						v.origen_item as					origen_item,
                        v.origen_objeto_proyecto as 		origen_objeto_proyecto,
                        v.origen_objeto as 					origen_objeto,
                        v.destino_item_proyecto as			destino_item_proyecto,
                        v.destino_item 			as			destino_item,
                        v.destino_objeto_proyecto as 		destino_objeto_proyecto,
                        v.destino_objeto as 				destino_objeto,
						v.indice as 						indice,
						v.vinculo_tipo as					tipo,
						v.inicializacion as 				inicializacion,
						v.frame as							frame,
						v.canal as							canal,
						v.texto as							texto,
						v.imagen_recurso_origen as			imagen_recurso_origen,
						v.imagen as							imagen
				FROM	apex_vinculo v, 
						apex_item_objeto o
				WHERE	o.proyecto = v.origen_objeto_proyecto
				AND		o.objeto = v.origen_objeto
				AND		o.item = '".$this->solicitud->info["item"]."' AND
						o.proyecto= '".$this->solicitud->info["item_proyecto"]."'
				AND		(v.destino_item = '/autovinculo');";
//		echo "<pre> $sql </pre>";
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			monitor::evento("bug","VINCULADOR: No se genero el recordset. -- " . $db["instancia"][apex_db_con]->ErrorMsg()." -- SQL: $sql -- ");
		}
		if(!$rs->EOF){
			//Creo el array de vinculos
			$this->info = $rs->getArray();
			//Creo el array de indices para accederlos
			for($a=0;$a<count($this->info);$a++){
				//Llevo el ID a un string para buscarlos mas facil
				$obj = $this->info[$a]['origen_objeto_proyecto'].",".$this->info[$a]['origen_objeto'];
				$item = $this->info[$a]['destino_item_proyecto'].",".$this->info[$a]['destino_item'];
				$this->indices_objeto[$obj][$this->info[$a]['indice']]=$a;
				$this->indices_item[$item]=$a;
			}
		}
		$this->prefijo = $this->solicitud->hilo->prefijo_vinculo();
	}
//----------------------------------------------------------------

	function info()
/*
 	@@acceso: actividad
	@@desc: Imprime el estado del objeto
*/
	{
		$dump["indices_objeto"]=$this->indices_objeto;
		$dump["indices_item"]= $this->indices_item;
		$dump["info"]= $this->info;
		ei_arbol($dump,"VINCULADOR");
	}


//##################################################################################
//########################   Solicitud DIRECTA de URLS  ############################
//##################################################################################
	
	function generar_solicitud($item_proyecto="",$item="",$parametros=null,
								$zona=false,$cronometrar=false,$param_html=null,$menu=null,$celda_memoria=null)
/*
 	@@acceso: interno
	@@desc: Generacion directa de una URL que representa un posible futuro acceso a la infraestructura
	@@param: string | Id del ITEM (Proyecto) | Reproducion del ITEM actual
	@@param: string | Id del ITEM (Path) | Reproducion del ITEM actual
	@@param: array | Parametros pasados al ITEM siguente (Array asociativo de strings)| null
	@@param: boolean | Activa la propagacion automatica del editable de la ZONA | false
	@@param: boolean | Indica si la solicitud generada por este vinculo debe cronometrarse | false
	@@param: array | Parametros para la construccion del HTML. Las claves asociativas son: frame, clase_css, texto, tipo [normal,popup], inicializacion, imagen_recurso_origen, imagen
 	@@retorno: string | URL que implementa la llamada al ITEM solicitado | null
*/
 	{
		//-[1]- Determino ITEM
		//Por defecto se propaga el item actual, o un item del mismo proyecto
		if($item_proyecto=="") $item_proyecto = $this->solicitud->info["item_proyecto"];	
		if($item==""){
			$item_proyecto = $this->solicitud->info["item_proyecto"];
			$item = $this->solicitud->info["item"];
		}
		$item_a_llamar = $item_proyecto . apex_qs_separador . $item;
		//-[2]- Determino parametros
		$parametros_formateados = "";
		if($zona){//Hay que propagar la zona?
			if(isset($this->solicitud->zona)){//Existe una zona
				if($this->solicitud->zona->controlar_carga()){//Esta cargada?
					$parametros_formateados .= "&". apex_hilo_qs_zona 
						."=". implode(apex_qs_separador, $this->solicitud->zona->obtener_editable_cargado());
				}
			}
		}
		//Cual es la celda de memoria del proximo request?
		if(!isset($celda_memoria)){
			//Por defecto propago la celda actual del HILO
			$celda_memoria = toba::get_hilo()->get_celda_memoria_actual();
		}		
		$parametros_formateados .= "&". apex_hilo_qs_celda_memoria ."=". $celda_memoria;
		//La proxima pagina va a CRONOMETRARSE?
		if($cronometrar){
			$parametros_formateados .= "&". apex_hilo_qs_cronometro ."=1";
		}
		//Formateo paremetros directos
		if(isset($parametros)){
			foreach($parametros as $clave => $valor){
				$parametros_formateados .= "&$clave=$valor";
			}
		}
		//Genero la URL que invoca la solicitud
		$vinculo = $this->prefijo . "&" . apex_hilo_qs_item . "=" . $item_a_llamar;
		if(trim($parametros_formateados)!=""){
			if(apex_pa_encriptar_qs){
				$encriptador = toba::get_encriptador();
				//Le concateno un string unico al texto que quiero encriptar asi evito que conozca 
				//la clave alguien que ve los parametros encriptados y sin encriptar
				$parametros_formateados .= $parametros_formateados . "&jmb76=". uniqid("");
				$vinculo = $vinculo . "&". apex_hilo_qs_parametros ."=". $encriptador->cifrar($parametros_formateados);
			}else{
				$vinculo = $vinculo . $parametros_formateados;
			}
		}
		//El vinculo esta solicitado por el menu?
		//Esto se maneja directamente $_GET por performance (NO encriptar todo el menu)
		if($menu){
			$vinculo .= "&". apex_hilo_qs_menu ."=1";
		}
		//Genero HTML o devuelvo el VINCULO
		if(is_array($param_html)){
			return $this->generar_html($vinculo, $param_html);
		}else{
			return $vinculo;
		}
	}

//##################################################################################
//#########  Solicitud INDIRECTA de URLs (Vinculacion a travez de la DB) ###########
//##################################################################################

	function obtener_vinculo_a_item($proyecto, $item, $parametros=null, $escribir_tag=false, $zona=false, 
										$cronometrar=false,$texto="",$param_html=null, $menu=null, $celda_memoria=null)
/*
 	@@acceso: actividad
	@@desc: Recupera un VINCULO explicitamente. Controla el los permisos de ACCESO
	@@param: string | Id del ITEM (Proyecto)
	@@param: string | Id del ITEM (Path)
	@@param: array | Parametros pasados al ITEM siguente (Array asociativo de strings)| null
	@@param: boolean | Indica si hay que generar el HTML del VINCULO | false
	@@param: boolean | Activa la propagacion automatica del editable de la ZONA | false
	@@param: boolean | Indica si la solicitud generada por este vinculo debe cronometrarse | false
	@@param: texto | Texto del vinculo | vacio
	@@retorno: string | URL que implementa la llamada o HTML del vinculo si el USUARIO posee permisi, NULL en el caso contrario
	@@pendiente: El PARAMETRO pasado por el OBJETO es un STRING, no deberia ser un array?
*/

	//Me parece que esta no necesita una inicializacion del canal...
	{
		$clave = $proyecto.",".$item;
		if(isset($this->indices_item[$clave])){
			$v = $this->indices_item[$clave];
			$url = $this->generar_solicitud($this->info[$v]['destino_item_proyecto'],
											$this->info[$v]['destino_item'],
											$parametros,$zona,$cronometrar,$param_html,$menu,$celda_memoria);
			if($escribir_tag){
				return $this->generar_html_vinculo($url,$v,'lista-link',$texto);
			}else{
				return $url;
			}
		}else{
			//No existe una referencia a ese ITEM.
			//El VINCULO no esta asociado, o el usuario actual no posee permisos
			return null;
		}
	}
//-------------------------------------------------------------------------------------

	function obtener_vinculo_a_item_cp($proyecto, $item, $parametros=null, $escribir_tag=false, $zona=false, 
										$cronometrar=false,$texto="",$param_html=null, $menu=null, $celda_memoria=null)
/*
 	@@acceso: nucleo
	@@desc: Recupera un VINCULO explicitamente, controlando que el ITEM actual pertenezca el proyecto activo. Controla el los permisos de ACCESO.
	@@param: string | Id del ITEM (Proyecto)
	@@param: string | Id del ITEM (Path)
	@@param: array | Parametros pasados al ITEM siguente (Array asociativo de strings)| null
	@@param: boolean | Indica si hay que generar el HTML del VINCULO | false
	@@param: boolean | Activa la propagacion automatica del editable de la ZONA | false
	@@param: boolean | Indica si la solicitud generada por este vinculo debe cronometrarse | false
	@@param: texto | Texto del vinculo | vacio
	@@retorno: string | URL que implementa la llamada o HTML del vinculo si el USUARIO posee permisi, NULL en el caso contrario
	@@pendiente: El PARAMETRO pasado por el OBJETO es un STRING, no deberia ser un array?
*/
	{
		if($this->solicitud->info['item_proyecto'] == $this->solicitud->hilo->obtener_proyecto() ){
			return $this->obtener_vinculo_a_item($proyecto,$item,$parametros,$escribir_tag,$zona,
													$cronometrar,$texto,$param_html,$menu,$celda_memoria);
		}else{
			return null;
		}
	}
//-------------------------------------------------------------------------------------


	function obtener_vinculo_de_objeto($objeto, $indice, $parametro=null, $escribir_tag=false, $texto="", $zona=true)
/*
 	@@acceso: objeto
	@@desc: Este metodo es llamado desde las clases que conforman objetos de la libreria. Recupera un VINCULO asociado a un OBJETO
	@@param: array | Id del OBJETO (proyecto/objeto)
	@@param: string | Indice del VINCULO dentro del OBJETO
	@@param: string | Parametro pasado al ITEM siguente| null
	@@param: boolean | Indica si hay que generar el HTML del VINCULO | false
	@@param: texto | Texto del vinculo | vacio
	@@retorno: string | URL que implementa la llamada o HTML del vinculo
	@@pendiente: El PARAMETRO pasado por el OBJETO es un STRING, no deberia ser un array?
*/
	{
		//Si el OBJETO esta en su INSTANCIADOR, no tiene acceso a su contexto de VINCULOS,
		//Por la ejecucion se considera de prueba y el LINK dumpea los parametros PASADOS
		if( $this->solicitud->hilo->entorno_instanciador() === true){
			//Esto es verdad para todos los objetos?
			if(trim($texto)=="") $texto = "Probar";
			return "<a href='#' class='lista-link' onclick=\"alert('PARAMETRO[ $parametro ]')\">$texto</a>";
		}
		//No se solicita desde el contexto de INSTANCIACION...
		$clave = $objeto[0].",".$objeto[1];
		if(isset($this->indices_objeto[$clave][$indice])){
			$v = $this->indices_objeto[$clave][$indice];
			//Veo cual es el canal que hay que utilizar
			if(isset($this->info[$v]['canal'])){
				$canal = $this->info[$v]['canal'];
			}elseif(isset($this->info[$v]['destino_objeto'])){
				$canal = apex_hilo_qs_canal_obj . $this->info[$v]['destino_objeto'];
			}else{
				//La ausensia de canal, puede usarse para pasar un array asociativo directo
				return "ERROR";
			}
			//Armo el paquete de parametros
			if(is_array($parametro)){
				$paquete = $parametro;		
			}else{
				if(isset($parametro)){
					$paquete = array( $canal=>$parametro);
				}else{
					$paquete = null;
				}
			}
			//genero el URL
			if($this->info[$v]['destino_item']=="/autovinculo"){
				$url = $this->generar_solicitud(null,null,$paquete,true);
			}else{
				$url = $this->generar_solicitud($this->info[$v]['destino_item_proyecto'],$this->info[$v]['destino_item'],$paquete,$zona);
			}
			//Escribo el TAG o devuelvo el URL pelado
			if($escribir_tag){
				return $this->generar_html_vinculo($url,$v,null,$texto);
			}else{
				return $url;
			}
		}else{
			return null;
		}
	}
//----------------------------------------------------------------

	function consultar_vinculo($proyecto, $item, $solo_proyecto_local=false)
/*
 	@@acceso: actividad
	@@desc: Consulta si un USUARIO tiene acceso a un ITEM
	@@param: string | Id del ITEM (Proyecto)
	@@param: string | Id del ITEM (Path)
	@@param: boolean | Controla si el ITEM actual es del proyecto ACTIVO | false
	@@retorno: boolean | true si tiene acceso y false en el caso contrario
*/	
	{
		$clave = $proyecto.",".$item;
		if(isset($this->indices_item[$clave])){
			//Controlar tambien que el ITEM se va a cargar en su propio proyecto
			if($solo_proyecto_local){
				if($this->solicitud->info['item_proyecto'] 
						== $this->solicitud->hilo->obtener_proyecto() ){
					return true;
				}else{
					return false;
				}
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	
//-------------------------------------------------------------------------------------
//------------------------------ HTML  -------------------------------
//-------------------------------------------------------------------------------------

	function generar_html_vinculo($url, $posicion_vinculo, $clase_css='lista-link', $forzar_texto="")
/*
 	@@acceso: interno
	@@desc: Inicializa la generacion de HTML del vinculo interno
	@@param: string | URL
	@@param: int | Posicion del vinculo en el array $this->info
	@@param: string | Estilo CSS que hay que aplicarle al link | 'lista-link'
	@@param: string | Forzar el texto del vinculo | vacio
	@@retorno: string | HTML del vinculo generado
*/
	{
		if($forzar_texto==""){
			if(trim($this->info[$posicion_vinculo]['texto']=="")){
				//MODIFICAR!!! El texto por defecto tiene que salir del proyecto
				$texto = "TEXTO no especificado";
			}else{
				$texto = $this->info[$posicion_vinculo]['texto'];
			}
		}else{
			$this->info[$posicion_vinculo]['texto'] = $forzar_texto;
		}
		$parametros = $this->info[$posicion_vinculo];
		$parametros['clase_css'] = $clase_css;


		return $this->generar_html($url, $parametros);
	}
//----------------------------------------------------------------

	function generar_html($url, $parametros)
/*
 	@@acceso: interno
	@@desc: Genera un VINCULO
	@@param: string | URL
	@@param: array | Parametros para la construccion del HTML. Las claves asociativas son: frame, clase_css, texto, tipo [normal,popup], inicializacion, imagen_recurso_origen, imagen
 	@@retorno: string | HTML del vinculo generado
*/
	{
		//El vinculo corresponde a un FRAME
		if(isset($parametros['frame'])){
			if(trim($parametros['frame']!="")){
				$frame = " target='" . $parametros['frame'] . "' ";
			}else{
				$frame = "";
			}
		}else{
			$frame = "";
		}
		if(isset($parametros['clase_css'])){
			if(trim($parametros['clase_css']!="")){
				$clase = " class='" . $parametros['frame'] . "' ";
			}else{
				$clase = " class='lista-link'";
			}
		}else{
			$clase = " class='lista-link'";
		}
		//La llamada depende del tipo de vinculo (normal, popup, etc.)
		if( $parametros['tipo']=="normal" ){	//	*** Ventana NORMAL ***
			//El vinculo es normal
			$html = "<a href='$url' $clase $frame>";
		}elseif( $parametros['tipo']=="popup" )	//	*** POPUP javascript ***
		{
			$init = explode(",",$parametros['inicializacion']);
			$init = array_map("trim",$init);
			//ei_arbol($init);
			$tx = (isset($init[0])) ? $init[0] : 400;
			$ty = (isset($init[1])) ? $init[1] : 400;
			$scroll = (isset($init[2])) ? $init[2] : "yes";
			$resizable = (isset($init[3])) ? $init[3] : "yes";
			$html = "<a href='#' $clase onclick=\"javascript:return solicitar_item_popup('$url', $tx, $ty, '$scroll', '$resizable')\">";
		}

		if( isset($parametros['imagen']) && 
				isset($parametros['imagen_recurso_origen'])){
			if($parametros['imagen_recurso_origen']=="apex"){
				$html.= recurso::imagen_apl($parametros['imagen'],true,null,null,$parametros['texto']);
			}elseif($parametros['imagen_recurso_origen']=="proyecto"){
				$html.= recurso::imagen_pro($parametros['imagen'],true,null,null,$parametros['texto']);
			}else{
				$html.= $parametros['texto'];
			}
		}else{
			$html.= $parametros['texto'];
		}
		$html.= "</a>";
		return $html;
	}
//----------------------------------------------------------------

	function navegar_a($item_proyecto="",$item="",$parametros=null,
								$zona=false,$cronometrar=false)
/*
 	@@acceso: actividad
	@@desc: Genera un salto de javascript directo a una pagina
	@@param: string | Id del ITEM (Proyecto) | Reproducion del ITEM actual
	@@param: string | Id del ITEM (Path) | Reproducion del ITEM actual
	@@param: array | Parametros pasados al ITEM siguente (Array asociativo de strings)| null
	@@param: boolean | Activa la propagacion automatica del editable de la ZONA | false
	@@param: boolean | Indica si la solicitud generada por este vinculo debe cronometrarse | false
	@@param: array | Parametros para la construccion del HTML. Las claves asociativas son: frame, clase_css, texto, tipo [normal,popup], inicializacion, imagen_recurso_origen, imagen
 	@@retorno: string | URL que implementa la llamada al ITEM solicitado | null
*/
	{
		echo "<script language'javascript'>\n";
		echo "document.location.href='".
				$this->generar_solicitud($item_proyecto,$item,$parametros,$zona,$cronometrar)."'\n";
		echo "</script>\n";
		
	}

//----------------------------------------------------------------

	function javascript_head()
	//JAvascript necesario en el HEAD segun los vinculos que se van consumir
	//en esta solicitud
	{
		//Evalua que cosas se van a llamar y crear todo el javascript
		//necesario	
	}
//----------------------------------------------------------------
	
	function javascript_popup()
	//Devuelve la funcion de popup que hay que poner en el HEADER
	//Con la que se llaman a todos los POPUP
	{
	
	}
//----------------------------------------------------------------
}
?>