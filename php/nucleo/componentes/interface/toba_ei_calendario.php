<?php
/**
 * @package Componentes
 * @subpackage Eis
 */
require_once(toba_dir() . '/php/3ros/activecalendar/activecalendar.php');

/**
 * Calendario para visualizar contenidos diarios y seleccionar días o semanas.
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_calendario ei_calendario
 */
class toba_ei_calendario extends toba_ei
{
	protected $prefijo = 'cal';	
	protected $_calendario;
	protected $_semana_seleccionada;
	protected $_dia_seleccionado;
	protected $_mes_actual;
	protected $_ver_contenidos;

    function __construct($id)
    {
        parent::__construct($id);
		$dia = date("d");
		$mes = date("m");
		$anio = date("Y");
		$semana = date("W");
		$this->_semana_seleccionada = array("semana" => $semana, "anio" => $anio);
		$this->_dia_seleccionado = array("dia" => $dia, "mes" =>$mes, "anio" => $anio);
		$this->_mes_actual = array("mes" => $mes, "anio" => $anio);
		$this->_calendario = new calendario();
	}
	
	function destruir()
	{
		//Seleccionar Semana
		if (isset($this->_semana_seleccionada)) {
			$this->_memoria['semana_seleccionada'] = $this->_semana_seleccionada;
		} else {
			unset($this->_memoria['semana_seleccionada']);
		}
		//Seleccionar Día		
		if (isset($this->_dia_seleccionado)) {
			$this->_memoria['dia_seleccionado'] = $this->_dia_seleccionado;
		} else {
			unset($this->_memoria['dia_seleccionado']);
		}
		//Cambiar Mes 
		if (isset($this->_mes_actual)) {
			$this->_memoria['mes_actual'] = $this->_mes_actual;
		} else {
			unset($this->_memoria['mes_actual']);
		}
		parent::destruir();
	}

	/**
	 * Carga el calendario con información
	 * @param array $datos Arreglo en formato Recordset con columnas: dia, contenido
	 */
    function set_datos($datos=null)
    {
		if (isset($datos)) {
			foreach ($datos as $dato) {
				if (isset($dato["dia"])) {
					$this->_calendario->setEventContent($dato["dia"], $dato["contenido"]);
				}
			}
    	}
	}
	
	/**
	 * Habilita o deshabilita la posibilidad de ver los contenidos de los eventos
	 * @param boolean $ver
	 */
	function set_ver_contenidos($ver)
	{
		$this->_ver_contenidos = $ver;
		if ($ver) {
			$this->_calendario->viewEventContents();
		}
	}
	
	/**
	 * @ignore 
	 */	
	protected function cargar_seleccion_dia()
	{
		$this->_dia_seleccionado = null;
		if (isset($this->_memoria['dia_seleccionado']))
			$this->_dia_seleccionado = $this->_memoria['dia_seleccionado'];
		if(isset($_POST[$this->_submit."__seleccionar_dia"])) {
			$dia = $_POST[$this->_submit."__seleccionar_dia"];
			if ($dia != '') {
				$dia = explode(apex_qs_separador, $dia);
				$this->_dia_seleccionado["dia"] = $dia[0];
				$this->_dia_seleccionado["mes"] = $dia[1];				
				$this->_dia_seleccionado["anio"] = $dia[2];	
				$this->_calendario->setSelectedDay($dia[0]);
				$this->_calendario->setSelectedMonth($dia[1]);
				$this->_calendario->setSelectedYear($dia[2]);
			}
		}
	}
	
	/**
	 * @ignore 
	 */	
	protected function cargar_seleccion_semana()
	{
		$this->_semana_seleccionada = null;
		if (isset($this->_memoria['semana_seleccionada']))
			$this->_semana_seleccionada = $this->_memoria['semana_seleccionada'];
		if(isset($_POST[$this->_submit."__seleccionar_semana"])) {
			$semana = $_POST[$this->_submit."__seleccionar_semana"];
			if ($semana != '') {
				$semana = explode(apex_qs_separador, $semana);
				$this->_semana_seleccionada["semana"] = $semana[0];		
				$this->_semana_seleccionada["anio"] = $semana[1];
				$this->_calendario->setSelectedWeek($semana[0]);
				$this->_calendario->setSelectedYear($semana[1]);	
			}
		}
	}
	
	/**
	 * @ignore 
	 */	
	protected function cargar_cambio_mes()
	{
		if (isset($this->_memoria['mes_actual']))
			$this->_mes_actual = $this->_memoria['mes_actual'];
		if(isset($_POST[$this->_submit."__cambiar_mes"])) {
			$mes = $_POST[$this->_submit."__cambiar_mes"];
			if ($mes != '') {
				$mes = explode(apex_qs_separador, $mes);
				$this->_mes_actual["mes"] = $mes[0];		
				$this->_mes_actual["anio"] = $mes[1];		
			}
		}
	}
	
	/**
	 * @ignore 
	 */	
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		$this->_eventos['seleccionar_dia'] = array('maneja_datos'=>true, 'ayuda'=> 'Seleccionar el día');
		$this->_eventos['seleccionar_semana'] = array('maneja_datos'=>true, 'ayuda'=> 'Seleccionar la semana');
		$this->_eventos['cambiar_mes'] = array('maneja_datos'=>true, 'ayuda'=> 'Cambiar de mes');
	}

	/**
	 * @ignore 
	 */	
	function disparar_eventos()
	{
		$this->cargar_seleccion_dia();
		$this->cargar_seleccion_semana();
		$this->cargar_cambio_mes();
		if(isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];	
			//El evento estaba entre los ofrecidos?
			if (isset($this->_memoria['eventos'][$evento]) ) {
				if ($evento == 'seleccionar_dia')
					$parametros = $this->_dia_seleccionado;
				elseif ($evento == 'seleccionar_semana')
					$parametros = $this->_semana_seleccionada;
				elseif ($evento == 'cambiar_mes')
					$parametros = $this->_mes_actual;

				$this->reportar_evento( $evento, $parametros );
			}
		}
		$this->borrar_memoria_eventos_enviados();
	}
	
	function generar_html()
	{
		//Campos de comunicación con J
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit."__seleccionar_semana", '');
		echo toba_form::hidden($this->_submit."__seleccionar_dia", '');
		echo toba_form::hidden($this->_submit."__cambiar_mes", '');

		$this->_calendario->updateCalendar($this->_mes_actual["mes"], $this->_mes_actual["anio"]);
		$this->_calendario->enableDatePicker(2000,2010);
		$this->_calendario->enableDayLinks();
		$this->_calendario->enableWeekLinks();
		echo "<div class='ei-base ei-calendario-base'>\n";
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-calendario-barra-sup");
		echo "<div id='cuerpo_{$this->objeto_js}'>\n";
		echo $this->_calendario->showMonth($this->objeto_js, $this->_eventos, $this->get_html_barra_editor() );
		echo "</div></div>\n";
	}


	/**
	 * @ignore 
	 */	
	function getActYear()
	{
		return $this->_calendario->actyear;
	}
	
	/**
	 * @ignore 
	 */	
	function getActMonth()
	{
		return $this->_calendario->actmonth;
	}
	
	/**
	 * Retorna el contenido extra asociado a un día
	 * @param timestamp $dia
	 */
	function get_contenido($dia)
	{
		$datos = $this->_calendario->getEventContent($dia);
		return $datos;
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		echo $identado."window.{$this->objeto_js} = new ei_calendario('{$this->objeto_js}', '{$this->_submit}');\n";
	}

	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */	
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_calendario';
		return $consumo;
	}	

}


/**
 * Clase interna de calendario que utiliza activecalendar
 * @package Varios
 * @ignore 
 * 
 */
class calendario extends activecalendar
{
	
	function __construct($week=false,$year=false,$month=false,$day=false,$GMTDiff="none")
	{
        parent::__construct($week,$year,$month,$day,$GMTDiff);
	}
	
	function updateCalendar($mes, $anio)
	{
		$this->setActMonth($mes);
		$this->setActYear($anio);
		$this->setSelectedMonth($mes);
		$this->setSelectedYear($anio);
		
		$this->has31days = checkdate($this->actmonth,31,$this->actyear);
		$this->isSchalt = checkdate(2,29,$this->actyear);

		if ($this->isSchalt == 1 && $this->actmonth == 2)
			$this->maxdays = 29;
		elseif ($this->isSchalt != 1 && $this->actmonth == 2)
			$this->maxdays = 28;
		elseif ($this->has31days == 1)
			$this->maxdays = 31;
		else $this->maxdays = 30;
	
		$this->firstday = $this->mkActiveDate("w", $this->mkActiveTime(0,0,1,$this->actmonth,1,$this->actyear)); 
		$this->firstdate = $this->mkActiveTime(0,0,1,$this->actmonth,1,$this->actyear);
	}
	
	function setEventContent($day, $content)
	{
		$eventContent[$day] = $content;
		$this->calEventContent[] = $eventContent;
	}

	function getEventContent($day)
	{
		return $this->content($day);
	}
	
	function mkEventContent($var)
	{
		$day = $this->mkActiveDate("Y-m-d", $this->mkActiveTime(0,0,1,$this->actmonth,$var,$this->actyear));
		$hasContent = $this->content($day);
		$out="";
		if ($hasContent)
		{
			foreach($hasContent as $content)
			{
				$out.="<table class=\"".$this->cssEventContent."\">";
				$out.="<tr><td>".$content."</td></tr></table>";
			}
		}
		return $out;
	}
	
	function content($var)
	{
		$hasContent = false;
	
		if ($this->calEventContent)
		{
			for ($x=0; $x<count($this->calEventContent); $x++)
			{
				$eventContent = $this->calEventContent[$x];
				foreach($eventContent as $eventTime => $eventContent)
				{
					if ($eventTime == $var)
						$hasContent[] = $eventContent;
				}
			}
		}
		
		return $hasContent;
	}

	function showMonth($objeto_js, $eventos, $editor)
	{
		$out = $this->mkMonthHead();
		$out .= $this->barra_editor($editor);
		$out .= $this->mkMonthTitle();
		$out .= $this->mkDatePicker($objeto_js, $eventos);
		$out .= $this->mkWeekDays();
		$out .= $this->mkMonthBody($objeto_js, $eventos);
		$out .= $this->mkMonthFoot();
		return $out;
	}
	
	function barra_editor($html)
	{
		$pickerSpan = 8;
		$out = '';
		if($html) {
			$out.="<tr><td class=\"".$this->cssPicker."\" colspan=\"".$pickerSpan."\">\n";
			$out.=$html;
			$out.="</td></tr>\n";
		}
		return $out;
	}
	
	function mkDatePicker($objeto_js, $eventos)
	{
		$pickerSpan = 8;
		if ($this->datePicker)
		{
			$out="<tr><td class=\"".$this->cssPicker."\" colspan=\"".$pickerSpan."\">\n";
			$out.="<select name=\"".$this->monthID."\" id=\"".$this->monthID."\" class=\"".$this->cssPickerMonth."\">\n";
			for ($z=1;$z<=12;$z++)
			{
				if ($z <= 9)
					$z = "0$z";
				if ($z==$this->actmonth)
					$out.="<option value=\"".$z."\" selected=\"selected\">".$this->getMonthName($z)."</option>\n";
				else
					$out.="<option value=\"".$z."\">".$this->getMonthName($z)."</option>\n";
			}
			$out.="</select>\n";
			$out.="<select name=\"".$this->yearID."\" id=\"".$this->yearID."\" class=\"".$this->cssPickerYear."\">\n";
			for ($z=$this->startYear;$z<=$this->endYear;$z++)
			{
				if ($z==$this->actyear)
					$out.="<option value=\"".$z."\" selected=\"selected\">".$z."</option>\n";
				else
					$out.="<option value=\"".$z."\">".$z."</option>\n";
			}
			$out.="</select>\n";
			$evento_js = toba_js::evento('cambiar_mes', $eventos["cambiar_mes"]);
			$js = "{$objeto_js}.set_evento($evento_js);";
			$out.="<input type=\"submit\" value=\"".$this->selBtn."\" class=\"".$this->cssPickerButton."\" style='cursor: pointer;;cursor:hand;' onclick=\"$js\"></input>\n";
			$out.="</td></tr>\n";
		}
		return $out;
	}

	function mkMonthBody($objeto_js, $eventos)
	{
		$out="<tr>";
		$monthday=0;
		$out.=$this->mkWeek($this->firstdate, $objeto_js, $eventos);
		for ($x=0; $x<=6; $x++)
		{
			if ($x>=$this->firstday)
			{
				$monthday++;
				$out.=$this->mkDay($monthday, $objeto_js, $eventos);
			}
			else 
				$out .= "<td class=\"".$this->cssNoMonthDay."\"></td>";
		}
		$out.="</tr>\n";
		$goon = $monthday + 1;
		$stop=0;
		for ($x=0; $x<=6; $x++)
		{
			if ($goon>$this->maxdays)
				break;
			if ($stop==1)
				break;
			$out.="<tr>";
			$date = $this->mkActiveTime(0,0,1,$this->actmonth,$goon,$this->actyear);
			$out.=$this->mkWeek($date, $objeto_js, $eventos);
			for ($i=$goon; $i<=$goon+6; $i++)
			{
				if ($i>$this->maxdays)
				{
					$out.="<td class=\"".$this->cssNoMonthDay."\"></td>";
					$stop=1;
				}
				else
					$out.=$this->mkDay($i, $objeto_js, $eventos);
			}
			$goon=$goon+7;
			$out.="</tr>\n";
		}
		return $out;
	}
	
	function viernes($semana, $anio)
	{
		$ts_semana  = strtotime('+' . $semana . ' weeks', strtotime($anio . '0101'));
		$ajuste = 5 - date('w', $ts_semana);
		$ts_viernes = strtotime($ajuste . ' days', $ts_semana);
		
		if (date('W', $ts_viernes) == $semana)
			return $ts_viernes;
		else // se pasó a la semana siguiente
			return strtotime('-7 days', $ts_viernes);
	}

	
	function compare_week($week, $year)
	{
		$viernes = $this->viernes($week, $year);
		return $this->compare_date($viernes);
	}

	function mkWeek($date, $objeto_js, $eventos)
	{
		$week = $this->weekNumber($date);
		$year = $this->mkActiveDate("Y",$date);
		
		if (!$this->weekLinks) {
			if ($week == $this->getSelectedWeek() && $year == $this->getSelectedYear())
				$out = "<td class=\"".$this->cssSelecDay."\">".$this->weekNumber($date)."</td>\n";
			else
				$out = "<td class=\"".$this->cssWeek."\">".$this->weekNumber($date)."</td>\n";
		} else {
			if ($this->compare_week($this->weekNumber($date),$this->actyear) == 1) 
				$out = "<td class=\"".$this->cssWeekNoSelec."\">".$this->weekNumber($date)."</td>\n";	
			else {	
				$evento_js = toba_js::evento('seleccionar_semana', $eventos["seleccionar_semana"], "{$this->weekNumber($date)}||{$this->mkActiveDate('Y',$date)}");
				$js = "{$objeto_js}.set_evento($evento_js);";
				
				if ($week == $this->getSelectedWeek() && $year == $this->getSelectedYear())
					$out = "<td class=\"".$this->cssSelecDay."\" style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$this->weekNumber($date)."</td>\n";	
				else
					$out = "<td class=\"".$this->cssWeek."\" style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$this->weekNumber($date)."</td>\n";	
			}		
		}	
		return $out;
	}
	
	function compare_date($day)
	{
		$fecha_hoy = $this->mkActiveTime(0,0,1,$this->monthtoday,$this->daytoday,$this->yeartoday);
	
		if ($day < $fecha_hoy)
			return -1;
		elseif ($day > $fecha_hoy)
			return 1;
		else
			return 0;	
	}
	
	function mkDay($var, $objeto_js, $eventos)
	{
		if ($var <= 9)
			$day = "0$var";
		else
			$day = $var;	

		$eventContent = $this->mkEventContent($var);
		$content = ($this->showEvents) ? $eventContent : "";
		
		$evento_js = toba_js::evento('seleccionar_dia', $eventos["seleccionar_dia"], "{$day}||{$this->actmonth}||{$this->actyear}");
		$js = "{$objeto_js}.set_evento($evento_js);";
		$day = $this->mkActiveTime(0,0,1,$this->actmonth,$var,$this->actyear);
		if ($this->compare_date($day) == 1)
			$out="<td class=\"".$this->cssSunday."\">".$var.$content."</td>";		
		elseif (($this->dayLinks) && ((!$this->enableSatSelection && ($this->getWeekday($var) == 0)) || ((!$this->enableSunSelection && $this->getWeekday($var) == 6))))
			$out="<td class=\"".$this->cssSunday."\">".$var."</td>";
		elseif ($var==$this->getSelectedDay() && $this->actmonth==$this->getSelectedMonth() && $this->actyear==$this->getSelectedYear()) {
			if (!$this->dayLinks)
				$out="<td class=\"".$this->cssSelecDay."\">".$var.$content."</td>";
			else
				$out="<td class=\"".$this->cssSelecDay."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content."</td>";
		} elseif ($var==$this->daytoday && $this->actmonth==$this->monthtoday && $this->actyear==$this->yeartoday && $this->getSelectedDay() < 0 && $this->getSelectedMonth()==$this->monthtoday && $this->getSelectedWeek()<0) {
			if (!$this->dayLinks)
				$out="<td class=\"".$this->cssToday."\">".$var.$content."</td>";
			else
				$out="<td class=\"".$this->cssToday."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content."</td>";
		} elseif ($this->getWeekday($var) == 0 && $this->crSunClass){
			if (!$this->dayLinks)
				$out="<td class=\"".$this->cssSunday."\">".$var.$content."</td>";
			else
				$out="<td class=\"".$this->cssSunday."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content."</td>";
		} elseif ($this->getWeekday($var) == 6 && $this->crSatClass) {
			if (!$this->dayLinks)
				$out="<td class=\"".$this->cssSaturday."\">".$var.$content."</td>";
			else	
				$out="<td class=\"".$this->cssSaturday."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content."</td>";
		} else {
			if (!$this->dayLinks)
				$out="<td class=\"".$this->cssMonthDay."\">".$var.$content."</td>";
			else
				$out="<td class=\"".$this->cssMonthDay."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content."</td>";
		}		

		return $out;
	}
}

?>