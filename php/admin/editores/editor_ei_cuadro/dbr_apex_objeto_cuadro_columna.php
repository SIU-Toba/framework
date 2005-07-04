<?
//Generacion: 4-07-2005 00:43:11
//Fuente de datos: 'instancia'
require_once('nucleo/persistencia/db_registros_s.php');

class dbr_apex_objeto_cuadro_columna extends db_registros_s
//db_registros especifico de la tabla 'apex_objeto_cuadro_columna'
{
	function __construct($id, $fuente, $tope_registros=0, $utilizar_transaccion=false, $memoria_autonoma=false)
	{
		$definicion['tabla']='apex_objeto_cuadro_columna';
		$definicion['clave'][0]='objeto_cuadro_proyecto';
		$definicion['clave'][1]='objeto_cuadro';
		$definicion['clave'][2]='orden';
		$definicion['no_nulo'][0]='objeto_cuadro_proyecto';
		$definicion['no_nulo'][1]='objeto_cuadro';
		$definicion['no_nulo'][2]='orden';
		$definicion['no_nulo'][3]='titulo';
		$definicion['no_nulo'][4]='columna_estilo';
		$definicion['columna'][0]='titulo';
		$definicion['columna'][1]='columna_estilo';
		$definicion['columna'][2]='columna_ancho';
		$definicion['columna'][3]='ancho_html';
		$definicion['columna'][4]='total';
		$definicion['columna'][5]='valor_sql';
		$definicion['columna'][6]='valor_sql_formato';
		$definicion['columna'][7]='valor_fijo';
		$definicion['columna'][8]='valor_proceso';
		$definicion['columna'][9]='valor_proceso_esp';
		$definicion['columna'][10]='valor_proceso_parametros';
		$definicion['columna'][11]='vinculo_indice';
		$definicion['columna'][12]='par_dimension_proyecto';
		$definicion['columna'][13]='par_dimension';
		$definicion['columna'][14]='par_tabla';
		$definicion['columna'][15]='par_columna';
		$definicion['columna'][16]='no_ordenar';
		$definicion['columna'][17]='mostrar_xls';
		$definicion['columna'][18]='mostrar_pdf';
		$definicion['columna'][19]='pdf_propiedades';
		$definicion['columna'][20]='desabilitado';
		parent::__construct($id, $definicion, $fuente, $tope_registros, $utilizar_transaccion, $memoria_autonoma);
	}	
	
	function cargar_datos_clave($id)
	{
		$where[] = "objeto_cuadro = '{$id['objeto']}'";
		$where[] = "objeto_cuadro_proyecto = '{$id['proyecto']}'";
		$this->cargar_datos($where);
	}
}
?>