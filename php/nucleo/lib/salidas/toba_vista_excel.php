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
	protected $temp_salida;
	
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
		$this->crear_excel();
	}

	/**
	 * Retorna el objeto PHPExcel
	 * @return PHPExcel
	 */
	function get_excel()
	{
		return $this->excel;
	}

	function enviar_archivo()
	{
		$longitud = filesize($this->temp_salida);
		if (file_exists($this->temp_salida)) {
			$fp = fopen($this->temp_salida, 'r');
			$this->cabecera_http($longitud);
			fpassthru($fp);
			fclose($fp);
			unlink($this->temp_salida);
		}
	}

	protected function crear_excel()
	{
		$writer = 'PHPExcel_Writer_'.$this->writer;
		$archivo = explode('_', $writer);
		$archivo = implode('/', $archivo).'.php';
		require_once($archivo);
		$writer = new $writer($this->excel);
		$this->temp_salida = toba::proyecto()->get_path_temp().'/'.uniqid();
		$writer->save($this->temp_salida);
	}
	
	protected function cabecera_http($longitud)
	{
		toba_http::headers_download($this->tipo_descarga, $this->nombre_archivo, $longitud);
	}

	//------------------------------------------------------------------------
	//-- API de Creación
	//------------------------------------------------------------------------	
	
	function crear_hoja($nombre=null)
	{
		$hoja = $this->excel->createSheet();
		if (isset($nombre)) {
			$hoja->setTitle(utf8_encode(strval($nombre)));
		}
		$this->excel->setActiveSheetIndex($this->excel->getSheetCount()-1);
		$this->cursor = $this->cursor_base;
	}
	
	function set_hoja_nombre($nombre)
	{
		if (strlen($nombre) > 31) {
			$nombre = substr($nombre, 0, 30);
		}
		$this->excel->getActiveSheet()->setTitle(utf8_encode(strval($nombre)));
	}
	
	function get_hoja_nombre()
	{
		return $this->excel->getActiveSheet()->getTitle();
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
	
	function get_cursor()
	{
		return $this->cursor;
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
	function tabla($datos, $titulos=array(), $opciones=array(), $totales=array(), $origen=null)
	{
	 	//Determina la agrupacion
 		$agrupacion = array(); 		
 		foreach ($opciones as $clave => $atributos) {
 			if (isset($atributos['grupo']) && $atributos['grupo'] != '') {
 				$agrupacion[$atributos['grupo']][] = $clave;
	 		}
 		}		
		if (! isset($origen)) {
			$origen = $this->cursor;
			//Determina donde va a terminar la tabla
			$this->cursor[1] += count($datos);
			if (! empty($titulos)) {
				$this->cursor[1]++;
			}
			if (! empty($totales)) {
				$this->cursor[1]++;
			}			
			if (! empty($agrupacion)) {
				$this->cursor[1]++;
			}						
 		}
 		if (! empty($agrupacion)) {
 			$origen[1]++;	//Tiene que entrar una fila más
 		}
 		$borde = array('style' => PHPExcel_Style_Border::BORDER_THIN  );
 		$estilo_titulos = array(
 			'font' => array('bold' => true),
 			'borders' => array('bottom' => $borde, 'top' => $borde, 'left' => $borde, 'right' => $borde),
 			'fill' => array(
             		'type' => PHPExcel_Style_Fill::FILL_SOLID ,
		            'rotation'   => 0,
		            'startcolor' => array('rgb' => 'E6E6E6'),
             	),
 		);
		$hoja = $this->excel->getActiveSheet();
		//--- Titulos
		if (! empty($titulos)) {
			$x=0;
			$ultimo_grupo = '';
			foreach($titulos as $clave => $valor) {
				$inicio = $origen[0] + $x;	//Desplazado X columnas a derecha horizontalmente
				//-- Pone el titulo de la columna
				if (isset($valor) || !isset($opciones[$clave]['borrar_estilos_nulos'])) {
					$hoja->setCellValueByColumnAndRow($inicio, $origen[1],utf8_encode(strval($valor)));
					$hoja->getStyleByColumnAndRow($inicio, $origen[1])->applyFromArray($estilo_titulos);
				}
				//-- Maneja la agrupacion
				if (! empty($agrupacion)) {
					if (isset($opciones[$clave]['grupo']) && $opciones[$clave]['grupo'] != '') {
						$grupo_actual = $opciones[$clave]['grupo'];
					} else {
					 	$grupo_actual = '';
					}
					$hoja->setCellValueByColumnAndRow($inicio, $origen[1]-1, utf8_encode(strval($grupo_actual)));					
					$hoja->getStyleByColumnAndRow($inicio, $origen[1]-1)->applyFromArray($estilo_titulos);
					if ($ultimo_grupo != $grupo_actual) {
						if (isset($agrupacion[$grupo_actual]) && count($agrupacion[$grupo_actual]) > 1) {
							//Hay que mergear horizontalmente, segun la cantidad de columnas en el grupo
							$fin = $inicio + (count($agrupacion[$grupo_actual]) - 1);
							$hoja->mergeCellsByColumnAndRow($inicio, $origen[1]-1, $fin, $origen[1]-1);
						}
					}
					if ($grupo_actual == '') {
						//El grupo es vacio o no tiene grupo, hay que mergear verticalmente esta unica fila
						$hoja->setCellValueByColumnAndRow($inicio, $origen[1]-1, utf8_encode(strval($valor)));
						$hoja->mergeCellsByColumnAndRow($inicio, $origen[1]-1, $inicio, $origen[1]);
					}
					$ultimo_grupo = $grupo_actual;					
				}
				$x++;
			}
			$origen[1]++;
		}
		//--- Datos
		$columnas = array();
		$y = 0;		
		$x = 0;
		foreach($datos as $filas) {
			$x = 0;
			foreach($filas as $clave => $valor) {
				$columnas[$clave] = $x;
				$hoja->setCellValueByColumnAndRow($origen[0] + $x, $origen[1] + $y, utf8_encode(strval($valor)));
				if (! isset($opciones[$clave]['estilo']['borders'])) {
					$opciones[$clave]['estilo']['borders']= array('bottom' => $borde, 'top' => $borde, 'left' => $borde, 'right' => $borde);
				}
				//--- Se borran los estilos si no tiene valor (opcional)				
				if (!isset($valor) && isset($opciones[$clave]['borrar_estilos_nulos'])) {
					$opciones[$clave]['estilo'] = array();
				}
				$hoja->getStyleByColumnAndRow($origen[0] + $x, $origen[1] + $y)->applyFromArray($opciones[$clave]['estilo']);
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
		//--- Totales
		foreach($totales as $clave) {
			$col = PHPExcel_Cell::stringFromColumnIndex($columnas[$clave]);
			$desde = $col.(($origen[1]));
			$hasta = $col.(($origen[1]+$y)-1);
			$total = "=SUM($desde:$hasta)";
			$destino_x = $origen[0]+$columnas[$clave];
			$destino_y =  $origen[1] + $y;
			$hoja->setCellValueByColumnAndRow($destino_x, $destino_y, utf8_encode(strval($total)) );
			$estilo = $hoja->getStyleByColumnAndRow($destino_x, $destino_y);
			unset($opciones[$clave]['estilo']['borders']);
			$estilo->applyFromArray($opciones[$clave]['estilo']);			
			$estilo->getFont()->setBold(true);
			$estilo->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK );
			$estilo->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK );
		}
		return array($origen, array($origen[0]+($x-1), $origen[1]+($y-1)));
	}
	
	function separacion($cant_filas, $cant_columnas=0)
	{
		$this->cursor[1] += $cant_filas;
		$this->cursor[0] += $cant_columnas;
	}
	
	function titulo($titulo, $celdas_ancho=1, $origen=null)
	{
		$estilos = array(
			'font' => array('
				bold' => true, 
				'size' => 14, 
				'color' => array('argb' => PHPExcel_Style_Color::COLOR_WHITE)),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID, 
				'startcolor' => array('argb' => 'FF808080')
			),
			'borders' => array(
				'top' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
				'bottom' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
				'left' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
				'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
			)
		);
		$altura = 20;
		$this->texto($titulo, $estilos, $celdas_ancho, $altura, $origen);

	}
	
	function texto($texto, $estilos=array(), $celdas_ancho=1, $altura=null, $origen=null)
	{
		if (! isset($origen)) {
			$origen = $this->cursor;
			$this->cursor[1]++;
 		}
		$hoja = $this->excel->getActiveSheet();		
 		$hoja->setCellValueByColumnAndRow($origen[0], $origen[1],utf8_encode(strval($texto)) );
 		$hoja->setBreak('A1', PHPExcel_Worksheet::BREAK_COLUMN   );
 		$estilo = $hoja->getStyleByColumnAndRow($origen[0], $origen[1]);
 		$estilo->applyFromArray($estilos);
 		if (isset($altura)) {
 			$hoja->getRowDimension($origen[1])->setRowHeight($altura);
 		}
		if ($celdas_ancho > 1) {
			$fin = ($origen[0] + $celdas_ancho) -1;
			$hoja->mergeCellsByColumnAndRow($origen[0], $origen[1], $fin, $origen[1]);
	 		for($i=$origen[0]+1; $i<=$fin; $i++) {
	 			$estilo = $hoja->getStyleByColumnAndRow($i, $origen[1]);
	 			$estilo->applyFromArray($estilos);		
	 		}
		}
	}
	
}
?>