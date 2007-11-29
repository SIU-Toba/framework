<?php
agregar_dir_include_path(toba_dir().'/php/3ros/phpExcel');
require_once('PHPExcel.php');

/**
 * Genera un pdf a través de una api básica
 * @package SalidaGrafica
 * @todo La numeración de páginas no funcionará si se cambia la orientación de la misma. Habría que 
 * implementar un método que en base al tipo de papel y orientación de la página, devuelva las 
 * coordenadas para una correcta visualización de la numeración de páginas.
 * @todo El método insertar_imagen esta implementado con un método en estado beta de la api ezpdf. Usar
 * con discreción.
 */
class toba_vista_excel
{
	/**
	 * @var PHPExcel
	 */
	protected $excel;	
	protected $objetos = array();	
	protected $nombre_archivo = 'salida.xls';
	protected $tipo_descarga = 'attachment';
	protected $writer = 'Excel5';
	protected $cursor_base = array(0,1);
	protected $cursor = array(0,1);
	
	function __construct()
	{
		$this->excel = new PHPExcel();
		$this->inicializar();
	}
	
	function inicializar()
	{
	}
	
	/**
	 * @ignore 
	 */
	function asignar_objetos( $objetos )
	{
		$this->objetos = $objetos;
	}
	
	//------------------------------------------------------------------------
	//-- Configuracion
	//------------------------------------------------------------------------

	/**
	 * Cambia el formato de salida del excel
	 * @param string $tipo es Excel5, CSV, Excel2007, HTML o Serialized, por defecto es Excel5 (office97)
	 */
	function set_tipo_salida($tipo)
	{
		$this->writer = $tipo;
	}
	
	/**
	 * Cambia el nombre del archivo que el usuario visualiza al descargar
	 * @param string $nombre
	 */
	function set_nombre_archivo($nombre)
	{
		$this->nombre_archivo = $nombre;
	}

	//------------------------------------------------------------------------
	//-- Generacion del excel
	//------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function generar_salida()
	{	
		foreach( $this->objetos as $objeto ) {
			$objeto->vista_excel( $this );	
		}
		$this->stream_excel();
	}

	/**
	 * Retorna el objeto PHPExcel
	 * @return PHPExcel
	 */
	function get_excel()
	{
		return $this->excel;
	}

	protected function stream_excel()
	{
		$writer = 'PHPExcel_Writer_'.$this->writer;
		$archivo = explode('_', $writer);
		$archivo = implode('/', $archivo).'.php';
		require_once($archivo);
		$writer = new $writer($this->excel);
		$path = toba::proyecto()->get_path_temp().'/'.uniqid();
		$writer->save($path);
		$longitud = filesize($path);
		if (file_exists($path)) {
			$fp = fopen($path, 'r');
			$this->cabecera_http($longitud);
			fpassthru($fp);
			fclose($fp);
			unlink($path);
		}		
	}
	
	protected function cabecera_http($longitud)
	{
		header("Cache-Control: private");
  		header('Content-type: application/vnd.ms-excel');
  		header("Content-Length: $longitud");	
   		header("Content-Disposition: {$this->tipo_descarga}; filename={$this->nombre_archivo}");
  		header("Pragma: no-cache");
		header("Expires: 0");
	}

	//------------------------------------------------------------------------
	//-- API de Creación
	//------------------------------------------------------------------------	

	
	function crear_hoja($nombre)
	{
		$hoja = $this->excel->createSheet();
		$hoja->setTitle($nombre);
		$this->excel->setActiveSheetIndex($this->excel->getSheetCount()-1);
		$this->cursor = $this->cursor_base;
	}
	
	/**
	 * Cambia el cursor de inserción en el flujo del excel generado
	 * @param int $columna Número de columna, base 0
	 * @param int $fila, Número de fila, base 1
	 */
	function set_cursor($columna, $fila)
	{
		$this->cursor = array($columna, $fila);
	}
	
	/**
	 * 
	 * @param array $datos
	 * @param array $titulos
	 * @param array opciones => array(
	 * 'columna' => 
	 * 	 'ancho' => (auto|numero)
	 * 	 'estilo' => array( 
	 *		'font' => array('name' => 'Arial', 'bold' => true, 'italic' => false, 'underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE, 'strike' => false, 'color' => array('rgb' => '808080')),
	 *		'borders' => array(
	 *			'bottom' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOT,'color' => array('rgb' => '808080')), 
	 *			'top' => array( 'style' => PHPExcel_Style_Border::BORDER_DASHDOT, 'color' => array('rgb' => '808080'))
	 *			)
	 *		)
	 * 	)		
	 * @param array $origen Arreglo [columna,fila], sino se toma el cursor actual
	 * 
	 */
	function tabla($datos, $titulos=array(), $opciones=array(), $origen=null)
	{
		if (! isset($origen)) {
			$origen = $this->cursor;
			$this->cursor[1] += count($datos);
 		}
		$hoja = $this->excel->getActiveSheet();
		$y = 0;
		foreach($datos as $filas) {
			$x = 0;
			foreach($filas as $clave => $valor) {
				$hoja->setCellValueByColumnAndRow($origen[0] + $x, $origen[1] + $y, $valor);
				if (isset($opciones[$clave]['estilo'])) {
					$hoja->getStyleByColumnAndRow($origen[0] + $x, $origen[1] + $y)->applyFromArray($opciones[$clave]['estilo']);
				}
				if (isset($opciones[$clave]['ancho'])) {
					if ($opciones[$clave]['ancho'] == 'auto') {
						$hoja->getColumnDimensionByColumn($origen[0] + $x)->setAutoSize(true);
					} else {
						$hoja->getColumnDimensionByColumn($origen[0] + $x)->setWidth($opciones[$clave]['ancho']);
					}
				}
				$x++;
			}
			$y++;
		}
	}
	
	function separacion($cant_filas)
	{
		$hoja = $this->excel->getActiveSheet();		
		$this->cursor[1] += $cant_filas;
	}
	
	function titulo($titulo, $origen=null)
	{
		if (! isset($origen)) {
			$origen = $this->cursor;
			$this->cursor[1]++;
 		}
		$hoja = $this->excel->getActiveSheet();		
 		$hoja->setCellValueByColumnAndRow($origen[0], $origen[1], $titulo);
 		$fuente = $hoja->getStyleByColumnAndRow($origen[0], $origen[1])->getFont();
		$fuente->setBold(true);		
		$fuente->setSize(14);
	}
	
}
?>