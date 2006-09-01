<?
require_once('migracion_toba.php');
require_once('lib/parseo.php');
require_once('lib/manejador_archivos.php');
require_once('lib/editor_archivos.php');

class migracion_0_10_0 extends migracion_toba
{

	//--------------------------------------------------------------
	//-------------------------- PROYECTO --------------------------
	//--------------------------------------------------------------
	
	/**
	 * La estructura interna para separar los parámetros de los ef cambia así es posible contener
	 * los caracteres ';' y ':' entre ellos.
	 */
	function proyecto__parametros_efs()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'], '');
			if (is_array($param)) {
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * Los parametros del combo lista cambiaron, para brindar mayor ortogonalidad a la definición
	 * Ahora los valores en la lista se separan siempre por coma y los clave-valor (si clave != valor) se separan por /
	 *
	 */
	function proyecto__parametros_combo_lista_c()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				elemento_formulario = 'ef_combo_lista_c' AND
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['lista'])) {
				$lista = str_replace("/", "^", $param['lista']);
				$lista = str_replace("," ,"/", $lista);
				$param['lista'] = str_replace("^", ",", $lista);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * El parametro 'valores' del multiseleccion pasa a llamarse 'lista' para unificarlo con los combos
	 */
	function proyecto__parametros_multiseleccion()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
					elemento_formulario IN ('ef_multi_seleccion', 'ef_multi_seleccion_lista', 'ef_multi_seleccion_check')
				AND objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['valores'])) {
				$param['lista'] = $param['valores'];
				unset($param['valores']);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * Los parametros de inicializacion de la ventana pasan de ser 3 e implicitos a n y explicitos,
	 * asi se pueden definir cualquiera y ademas pueden ser modificados como los parametros de los 
	 * eventos
	 */
	function proyecto__parametros_ef_popup()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				elemento_formulario = 'ef_popup' AND
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['ventana'])) {
				$ventana = explode(',', $param['ventana']);
				if (isset($ventana[0])) {
					$ventana[0] = "width: {$ventana[0]}";	
				}
				if (isset($ventana[1])) {
					$ventana[1] = "height: {$ventana[1]}";	
				}
				if (isset($ventana[2])) {
					$ventana[2] = "scroll: {$ventana[2]}";	
				}
				$param['ventana'] = implode(',', $ventana);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$cant += $this->elemento->get_db()->ejecutar($sql);
			}
		}
		return $cant;		
	}
	
	/**
	 * Las clases de combo se unificaron en una unica
	 */
	function proyecto__arbol_herencia_efs()
	{
		$sql = "
			UPDATE 
				apex_objeto_ei_formulario_ef
			SET
				elemento_formulario = 'ef_combo'
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}' AND
				elemento_formulario IN ('ef_combo_dao', 'ef_combo_db', 'ef_combo_db_proyecto', 
										'ef_combo_lista', 'ef_combo_lista_c')
		";		
		$cant = $this->elemento->get_db()->ejecutar($sql);
		return $cant;		
	}
	
	/**
	 * El ef_editable_multilinea pasa a ser ef_editable_textarea
	 */
	function proyecto__ef_textarea()
	{
		$sql = "
			UPDATE 
				apex_objeto_ei_formulario_ef
			SET
				elemento_formulario = 'ef_editable_textarea'
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}' AND
				elemento_formulario = 'ef_editable_multilinea'
		";		
		$cant = $this->elemento->get_db()->ejecutar($sql);	
		return $cant;	
	}
	
	/**
	 * El editable numero migra su parametro CIFRAS por TAMANO, que es en realidad lo que es!
	 */
	function proyecto__ef_parametros_editable_numero()
	{
		$cant = 0;
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
					elemento_formulario IN ('ef_editable_numero', 'ef_editable_numero_porcentaje', 'ef_editable_moneda')
				AND objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			if (isset($param['cifras'])) {
				$param['tamano'] = $param['cifras'];
				unset($param['cifras']);
				$prop = addslashes(empaquetar_propiedades($param,'_'));
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef SET inicializacion = '$prop'
					WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				$rs = $this->elemento->get_db()->ejecutar($sql);
				$cant++;
			}
		}
		return $cant;
	}

	function proyecto__ef_normalizacion_parametros()
	{
		$cant = 0;
		$correlacion = array(
			'estado' =>'estado_defecto',
			'predeterminado' => 'estado_defecto',
			'solo_lectura' => 'solo_lectura',
			'dao' =>'carga_metodo',
			'clase' =>'carga_clase',
			'include' =>'carga_include',
			'clave' =>'carga_col_clave',
			'sql' =>'carga_sql',
			'fuente' =>'carga_fuente',
			'lista' =>'carga_lista',
			'dependencias' =>'carga_maestros',
			'cascada_relajada' =>'carga_cascada_relaj',
			'no_seteado' =>'carga_no_seteado',
			'tamano' =>'edit_tamano',
			'cifras' =>'edit_tamano',
			'maximo' =>'edit_maximo',
			'mascara' =>'edit_mascara',
			'unidad' =>'edit_unidad',
			'rango' =>'edit_rango',
			'filas' =>'edit_filas',
			'columnas' =>'edit_columnas',
			'wrap' =>'edit_wrap',
			'resaltar' =>'edit_resaltar',
			'ajustable' =>'edit_ajustable',
			'ventana' =>'popup_ventana',
			'editable' =>'popup_editable',
			'tipo' =>'fieldset_fin',
			'valor_no_seteado' =>'check_valor_no',
			'valor_info' =>'check_desc_si',
			'valor_info_no_seteado' =>'check_desc_no',
			'sin_datos' =>'fijo_sin_estado',
			'ancho' =>'editor_ancho',
			'alto' =>'editor_alto',
			'botonera' =>'editor_botonera',
			'cant_minima'=>'selec_cant_minima',
			'cant_maxima'=>'selec_cant_maxima',
			'mostrar_utilidades'=>'selec_utilidades',
			'tamanio'=>'selec_tamano',
			//'item_destino' Migrar manualmente
			//'valor':carga_col_desc o check_valor_si
		);
		
		$sql = "
			SELECT 
				objeto_ei_formulario_proyecto,
				objeto_ei_formulario,
				objeto_ei_formulario_fila,
				elemento_formulario,
				inicializacion
			FROM
				apex_objeto_ei_formulario_ef
			WHERE 
				objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";
		$rs = $this->elemento->get_db()->consultar($sql);
		foreach ($rs as $ef) {
			$param = parsear_propiedades($ef['inicializacion'],'_');
			$nuevos = array();
			foreach ($param as $clave => $valor) {
				if (isset($correlacion[$clave])) {
					$nuevos[$correlacion[$clave]] = $valor;
				} elseif ($clave == 'valor') {
					if ($ef['elemento_formulario'] == 'ef_checkbox') {
						$nuevos['check_valor_si'] = $valor;
					} else {
						$nuevos['carga_col_desc'] = $valor;
					}
				} elseif ($clave == 'item_destino') {
					$partes = explode(',', $valor);
					$p_item = $partes[0];
					if (count($partes)==2) {
						$p_proyecto = $partes[1];
					} else {
						$p_proyecto = $this->elemento->get_id();
					}
					$nuevos['popup_item'] = $p_item;
					$nuevos['popup_proyecto'] = $p_proyecto;
				} else  {
					echo "Warning: El parametro $clave no tiene correlacion en la migracion.\n";	
				}
			}
			if (! empty($nuevos)) {
				$sql = "
					UPDATE apex_objeto_ei_formulario_ef 
					SET 
						inicializacion = NULL,
				";
				foreach($nuevos as $id => $valor) {
					$valor = addslashes($valor);
					$sql .= "$id = '$valor', ";
				}
				$sql = substr($sql, 0, -2);
				$sql .="	WHERE
							objeto_ei_formulario_proyecto = '{$ef['objeto_ei_formulario_proyecto']}' 
						AND	objeto_ei_formulario = '{$ef['objeto_ei_formulario']}'  
						AND objeto_ei_formulario_fila = '{$ef['objeto_ei_formulario_fila']}'
					";
				//echo $sql."\n";
				$rs = $this->elemento->get_db()->ejecutar($sql);
				$cant++;
			}
		}	
		
		
		//--- Pone las inicializaciones en NULL para no volver a migrarlas sin querer
		$sql =  "UPDATE apex_objeto_ei_formulario_ef SET inicializacion = NULL
					WHERE objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'";
		$this->elemento->get_db()->ejecutar($sql);
		return $cant;
	}
	
	function proyecto__ef_clave_nuevo_parametro()
	{
		$sql = " 
			UPDATE apex_objeto_ei_formulario_ef SET edit_confirmar_clave=1
			WHERE
					elemento_formulario IN ('ef_editable_clave')
				AND objeto_ei_formulario_proyecto='{$this->elemento->get_id()}'
		";	
		$cant = $this->elemento->get_db()->ejecutar($sql);
		return $cant;
	}

	function proyecto__path_includes()
	{
		$editor = new editor_archivos();
		$editor->agregar_sustitucion('|nucleo/browser/zona/zona.php|'					,'nucleo/lib/zona.php');
		$editor->agregar_sustitucion('|nucleo/lib/asercion.php|'						,'lib/asercion.php');          
		$editor->agregar_sustitucion('|nucleo/lib/cache_db.php|'              			,'lib/cache_db.php');               
		$editor->agregar_sustitucion('|nucleo/lib/db.php|'                    			,'lib/db.php');                     
		$editor->agregar_sustitucion('|nucleo/lib/editor_archivos.php|'       			,'lib/editor_archivos.php');
		$editor->agregar_sustitucion('|nucleo/lib/encriptador.php|'           			,'lib/encriptador.php');      
		$editor->agregar_sustitucion('|nucleo/lib/ini.php|'                   			,'lib/ini.php');          
		$editor->agregar_sustitucion('|nucleo/lib/manejador_archivos.php|'	   			,'lib/manejador_archivos.php');
		$editor->agregar_sustitucion('|nucleo/lib/parseo.php|'                			,'lib/parseo.php');   
		$editor->agregar_sustitucion('|nucleo/lib/sincronizador_archivos.php|'			,'lib/sincronizador_archivos.php');
		$editor->agregar_sustitucion('|nucleo/lib/sql.php|'                   			,'lib/sql.php');
		$editor->agregar_sustitucion('|nucleo/lib/texto.php|'                 			,'lib/texto.php');
		$editor->agregar_sustitucion('|nucleo/lib/varios.php|'							,'lib/varios.php');
		$editor->agregar_sustitucion('|nucleo/lib/reflexion/archivo_php.php|'			,'lib/reflexion/archivo_php.php');
		$editor->agregar_sustitucion('|nucleo/lib/reflexion/clase_datos.php|' 			,'lib/reflexion/clase_datos.php');
		$editor->agregar_sustitucion('|nucleo/lib/reflexion/clase_php.php|'   			,'lib/reflexion/clase_php.php');
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto.php|'               ,'nucleo/componentes/objeto.php'                 			);		
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ci.php|'            ,'nucleo/componentes/interface/objeto_ci.php'              );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ci_abm.php|'          ,'nucleo/componentes/interface/objeto_ci_abm.php'          );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei.php|'              ,'nucleo/componentes/interface/objeto_ei.php'              );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei_arbol.php|'        ,'nucleo/componentes/interface/objeto_ei_arbol.php'        );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei_archivos.php|'     ,'nucleo/componentes/interface/objeto_ei_archivos.php'     );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei_calendario.php|'   ,'nucleo/componentes/interface/objeto_ei_calendario.php'   );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei_cuadro.php|'       ,'nucleo/componentes/interface/objeto_ei_cuadro.php'       );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei_filtro.php|'       ,'nucleo/componentes/interface/objeto_ei_filtro.php'       );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei_formulario.php|'   ,'nucleo/componentes/interface/objeto_ei_formulario.php'   );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_ei_formulario_ml.php|','nucleo/componentes/interface/objeto_ei_formulario_ml.php');
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_cuadro.php|'          ,'nucleo/componentes/transversales/objeto_cuadro.php'      );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_cuadro_reg.php|'      ,'nucleo/componentes/transversales/objeto_cuadro_reg.php'  );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_filtro.php|'          ,'nucleo/componentes/transversales/objeto_filtro.php'      );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_grafico.php|'         ,'nucleo/componentes/transversales/objeto_grafico.php'     );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_hoja.php|'            ,'nucleo/componentes/transversales/objeto_hoja.php'        );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_html.php|'            ,'nucleo/componentes/transversales/objeto_html.php'        );
		$editor->agregar_sustitucion('|nucleo/browser/clases/objeto_lista.php|'           ,'nucleo/componentes/transversales/objeto_lista.php'       );
		$archivos = manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);
	}
	
	/**
	 * La clase lista-col-titulo paso a ser ei-cuadro-col-tit
	 *
	 */
	function proyecto__migracion_css_cuadro()
	{
		$sql = " 
			UPDATE apex_objeto_ei_cuadro_columna
			SET 
				estilo_titulo = 'ei-cuadro-col-tit'
			WHERE
					objeto_cuadro_proyecto = '{$this->elemento->get_id()}'
				AND	estilo_titulo = 'lista-col-titulo'
		";
		$cant = $this->elemento->get_db()->ejecutar($sql);	
		return $cant;	
	}
	
	/**
	 * Los estilos de los botones de los eventos cambiaron
	 */
	function proyecto__migracion_css_botones_evt()
	{
		$cant = 0;		
		$sql = " 
			UPDATE apex_objeto_eventos
			SET 
				estilo = 'ei-boton'
			WHERE
					proyecto = '{$this->elemento->get_id()}'
				AND	estilo = 'abm-input'
		";
		$cant += $this->elemento->get_db()->ejecutar($sql);	
		
		//--- BAJA
		$sql = " 
			UPDATE apex_objeto_eventos
			SET 
				estilo = 'ei-boton-baja'
			WHERE
					proyecto = '{$this->elemento->get_id()}'
				AND	estilo = 'abm-input-eliminar'
		";
		$cant += $this->elemento->get_db()->ejecutar($sql);		
		return $cant;
	}
	
	function proyecto__cambios_tipo_pagina()
	{
		$cant = 0;
		//--Consumidor_html, lista_admin y com_js migran a vacio
		$sql = "UPDATE apex_item
				SET 
					pagina_tipo = 'vacio'
				WHERE 
						proyecto = '{$this->elemento->get_id()}'
					AND	pagina_tipo IN ('com_js', 'consumidor_html', 'lista_admin')
			";
		$cant+= $this->elemento->get_db()->ejecutar($sql);
		
		//--Popup pasa a ef_popup		
		$sql = "UPDATE apex_item
				SET 
					pagina_tipo = 'popup_ef'
				WHERE 
						proyecto = '{$this->elemento->get_id()}'
					AND	pagina_tipo IN ('com_js', 'consumidor_html', 'popup')
			";
		$cant+= $this->elemento->get_db()->ejecutar($sql);
		return $cant;
	}

	function proyecto__eliminar_objetos_obsoletos()
	{
		$clases_obsoletas[] = 'objeto_cuadro';
		$clases_obsoletas[] = 'objeto_cuadro_reg';
		$clases_obsoletas[] = 'objeto_filtro';
		$clases_obsoletas[] = 'objeto_hoja';
		$clases_obsoletas[] = 'objeto_mt_abms';
		$clases_obsoletas[] = 'objeto_html';
		$clases_obsoletas[] = 'objeto_lista';
		$clases_obsoletas[] = 'objeto_mt';
		$clases_obsoletas[] = 'objeto_mt_mds';
		$clases_obsoletas[] = 'objeto_mt_s';
		$clases_obsoletas[] = 'objeto_ut_formulario';
		$clases = implode("','",$clases_obsoletas);
		$sql = "DELETE FROM apex_objeto WHERE 						
						proyecto = '{$this->elemento->get_id()}' AND 
						clase IN ('$clases');";
		return $this->elemento->get_db()->ejecutar($sql);
	}	

	function proyecto__migrar_solicitud_browser()
	{
		$sql = "UPDATE apex_item SET solicitud_tipo = 'web' 
				WHERE 
						proyecto = '{$this->elemento->get_id()}'
					AND	solicitud_tipo = 'browser' 
					AND carpeta <> 1 OR carpeta IS NULL;";
		return $this->elemento->get_db()->ejecutar($sql);
	}	

	function proyecto__migrar_tipo_solicitud_carpetas()
	{
		$sql = "UPDATE apex_item SET solicitud_tipo = NULL WHERE 
						carpeta = 1;
					AND	proyecto = '{$this->elemento->get_id()}'";
		return $this->elemento->get_db()->ejecutar($sql);
	}
	
	/**
	 * Se cambia:
	 *	evt__id__carga por conf__id
	 * 	evt__entrada__id por evt__id__entrada	
	 *  evt__inicializar por ini
	 */
	function proyecto__cambio_api_ci()
	{
		$editor = new editor_archivos();
		$editor->agregar_sustitucion('/evt__(\w+)__carga\(/', 'conf__${1}(');		
		$editor->agregar_sustitucion('/evt__entrada__(\w+)\(/', 'evt__${1}__entrada(');
		$editor->agregar_sustitucion('/evt__salida__(\w+)\(/', 'evt__${1}__salida(');
		$editor->agregar_sustitucion('/evt__inicializar\(/', 'ini(');
		$archivos = manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);
	}
	
	function proyecto__fuente_no_obligatoria()
	{
		$sql = "
			UPDATE apex_objeto SET fuente_datos=NULL, fuente_datos_proyecto=NULL
			WHERE 
					proyecto = '{$this->elemento->get_id()}'
				AND clase NOT IN ('objeto_ei_formulario', 
								'objeto_ei_formulario_ml',
								'objeto_ei_filtro',
								'objeto_datos_tabla',
								'objeto_datos_relacion',
								'objeto_cn');
			";
		return $this->elemento->get_db()->ejecutar($sql);
	}
	
	/**
	 * Todas las clases del nucleo deben estar precedidas por toba_
	 */
	function proyecto__namespace_toba()
	{
		$editor = new editor_archivos();
		$editor->agregar_sustitucion('/objeto_ci/', 			'toba_ci');
		$editor->agregar_sustitucion('/objeto_ei/', 			'toba_ei');
		$editor->agregar_sustitucion('/objeto_cn/', 			'toba_cn');
		$editor->agregar_sustitucion('/objeto_datos_tabla/',	'toba_datos_tabla');
		$editor->agregar_sustitucion('/objeto_datos_relacion/',	'toba_datos_relacion');
		$editor->agregar_sustitucion('/nucleo_toba/',			'toba_nucleo');
		$editor->agregar_sustitucion('/form::/',				'toba_form::');
		$editor->agregar_sustitucion('/recurso::/',				'toba_toba_recurso::');
		$editor->agregar_sustitucion('/fuente_de_datos/',		'toba_fuente_datos');
		$editor->agregar_sustitucion('/info_instalacion/',		'toba_instalacion');
		$editor->agregar_sustitucion('/info_instancia/',		'toba_instancia');
		$editor->agregar_sustitucion('/info_proyecto/',			'toba_proyecto');
		$editor->agregar_sustitucion('/parser_ayuda/',			'toba_parser_ayuda');
		$editor->agregar_sustitucion('/excepcion_toba/',		'toba_error');
		$editor->agregar_sustitucion('/js::/',					'toba_js::');
		$editor->agregar_sustitucion('/zona.php/',				'toba_zona.php');
		$editor->agregar_sustitucion('/extends zona/',			'extends toba_zona');
		$editor->agregar_sustitucion('/ap_relacion_db/',		'toba_ap_relacion_db');
		$editor->agregar_sustitucion('/ap_tabla_db/',			'toba_ap_tabla_db');
		$archivos = manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);
	}
	
	/**
	 * Debido a las nuevas convenciones, se prefiere que los metodos que no retornan tipos simples
	 * no se prefijen con get_, por ej. se usa toba::logger()
	 */
	function proyecto__cambio_convenciones_clase_toba()
	{
		$editor = new editor_archivos();
		$editor->agregar_sustitucion('/toba::get_logger/', 			'toba::logger');
		$editor->agregar_sustitucion('/toba::get_nucleo/', 			'toba::nucleo');
		$editor->agregar_sustitucion('/toba::get_solicitud/', 		'toba::solicitud');
		$editor->agregar_sustitucion('/toba::get_zona/', 			'toba::zona');
		$editor->agregar_sustitucion('/toba::get_vinculador/', 		'toba::vinculador');
		$editor->agregar_sustitucion('/toba::get_hilo/', 			'toba::hilo');
		$editor->agregar_sustitucion('/toba::get_permisos/', 		'toba::permisos');
		$editor->agregar_sustitucion('/toba::get_cola_mensajes/',	'toba::notificacion');
		$editor->agregar_sustitucion('/toba::get_fuente/',			'toba::fuente');
		$editor->agregar_sustitucion('/toba::get_db/',				'toba::db');
		$editor->agregar_sustitucion('/toba::get_encriptador/',		'toba::encriptador');
		$editor->agregar_sustitucion('/toba::get_cronometro/',		'toba::cronometro');
		$editor->agregar_sustitucion('/toba::get_sesion/',			'toba::sesion');
		$editor->agregar_sustitucion('/toba::get_usuario/',			'toba::usuario');
		$archivos = manejador_archivos::get_archivos_directorio($this->elemento->get_dir(), '/.php$/', true);
		$editor->procesar_archivos($archivos);
	}
}
?>