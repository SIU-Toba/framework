<?php
/**
 * @package Componentes
 * @subpackage Eis
 */
require_once("toba_ei.php");
require_once('3ros/activecalendar/activecalendar.php');

/**
 * Calendario para visualizar contenidos diarios y seleccionar días o semanas.
 * @package Componentes
 * @subpackage Eis
 */
class toba_ei_calendario extends toba_ei
{
	protected $prefijo = 'cal';	
	var $calendario;
	var $semana_seleccionada;
	var $dia_seleccionado;
	var $mes_actual;
	var $ver_contenidos;

    function __construct($id)
    {
        parent::__construct($id);
		$dia = date("d");
		$mes = date("m");
		$anio = date("Y");
		$semana = date("W");
		$this->semana_seleccionada = array("semana" => $semana, "anio" => $anio);
		$this->dia_seleccionado = array("dia" => $dia, "mes" =>$mes, "anio" => $anio);
		$this->mes_actual = array("mes" => $mes, "anio" => $anio);
		$this->calendario = new calendario();
	}
	
	function destruir()
	{
		//Seleccionar Semana
		if (isset($this->semana_seleccionada)) {
			$this->memoria['semana_seleccionada'] = $this->semana_seleccionada;
		} else {
			unset($this->memoria['semana_seleccionada']);
		}
		//Seleccionar Día		
		if (isset($this->dia_seleccionado)) {
			$this->memoria['dia_seleccionado'] = $this->dia_seleccionado;
		} else {
			unset($this->memoria['dia_seleccionado']);
		}
		//Cambiar Mes 
		if (isset($this->mes_actual)) {
			$this->memoria['mes_actual'] = $this->mes_actual;
		} else {
			unset($this->memoria['mes_actual']);
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
					$this->calendario->setEventContent($dato["dia"], $dato["contenido"]);
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
		$this->ver_contenidos = $ver;
		if ($ver) {
			$this->calendario->viewEventContents();
		}
	}
	
	protected function cargar_seleccion_dia()
	{
		$this->dia_seleccionado = null;
		if (isset($this->memoria['dia_seleccionado']))
			$this->dia_seleccionado = $this->memoria['dia_seleccionado'];
		if(isset($_POST[$this->submit."__seleccionar_dia"])) {
			$dia = $_POST[$this->submit."__seleccionar_dia"];
			if ($dia != '') {
				$dia = explode(apex_qs_separador, $dia);
				$this->dia_seleccionado["dia"] = $dia[0];
				$this->dia_seleccionado["mes"] = $dia[1];				
				$this->dia_seleccionado["anio"] = $dia[2];	
				$this->calendario->setSelectedDay($dia[0]);
				$this->calendario->setSelectedMonth($dia[1]);
				$this->calendario->setSelectedYear($dia[2]);
			}
		}
	}
	
	protected function cargar_seleccion_semana()
	{
		$this->semana_seleccionada = null;
		if (isset($this->memoria['semana_seleccionada']))
			$this->semana_seleccionada = $this->memoria['semana_seleccionada'];
		if(isset($_POST[$this->submit."__seleccionar_semana"])) {
			$semana = $_POST[$this->submit."__seleccionar_semana"];
			if ($semana != '') {
				$semana = explode(apex_qs_separador, $semana);
				$this->semana_seleccionada["semana"] = $semana[0];		
				$this->semana_seleccionada["anio"] = $semana[1];
				$this->calendario->setSelectedWeek($semana[0]);
				$this->calendario->setSelectedYear($semana[1]);	
			}
		}
	}
	
	protected function cargar_cambio_mes()
	{
		if (isset($this->memoria['mes_actual']))
			$this->mes_actual = $this->memoria['mes_actual'];
		if(isset($_POST[$this->submit."__cambiar_mes"])) {
			$mes = $_POST[$this->submit."__cambiar_mes"];
			if ($mes != '') {
				$mes = explode(apex_qs_separador, $mes);
				$this->mes_actual["mes"] = $mes[0];		
				$this->mes_actual["anio"] = $mes[1];		
			}
		}
	}
	
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		$this->eventos['seleccionar_dia'] = array('maneja_datos'=>true, 'ayuda'=> 'Seleccionar el día');
		$this->eventos['seleccionar_semana'] = array('maneja_datos'=>true, 'ayuda'=> 'Seleccionar la semana');
		$this->eventos['cambiar_mes'] = array('maneja_datos'=>true, 'ayuda'=> 'Cambiar de mes');
	}
	
	function disparar_eventos()
	{
		$this->cargar_seleccion_dia();
		$this->cargar_seleccion_semana();
		$this->cargar_cambio_mes();
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];	
			//El evento estaba entre los ofrecidos?
			if (isset($this->memoria['eventos'][$evento]) ) {
				if ($evento == 'seleccionar_dia')
					$parametros = $this->dia_seleccionado;
				elseif ($evento == 'seleccionar_semana')
					$parametros = $this->semana_seleccionada;
				elseif ($evento == 'cambiar_mes')
					$parametros = $this->mes_actual;

				$this->reportar_evento( $evento, $parametros );
			}
		}
	}
	
	function generar_html()
	{
		//Campos de comunicación con JS
		echo toba_form::hidden($this->submit, '');
		echo toba_form::hidden($this->submit."__seleccionar_semana", '');
		echo toba_form::hidden($this->submit."__seleccionar_dia", '');
		echo toba_form::hidden($this->submit."__cambiar_mes", '');

		$this->calendario->updateCalendar($this->mes_actual["mes"], $this->mes_actual["anio"]);
		$this->calendario->enableDatePicker(2000,2010);
		$this->calendario->enableDayLinks();
		$this->calendario->enableWeekLinks();

		echo $this->calendario->showMonth($this->objeto_js, $this->eventos, $this->get_html_barra_editor() );
	}
	
	function getActYear()
	{
		return $this->calendario->actyear;
	}
	
	function getActMonth()
	{
		return $this->calendario->actmonth;
	}
	
	/**
	 * Retorna el contenido extra asociado a un día
	 * @param timestamp $dia
	 */
	function get_contenido($dia)
	{
		$datos = $this->calendario->getEventContent($dia);
		return $datos;
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		echo $identado."window.{$this->objeto_js} = new ei_calendario('{$this->objeto_js}', '{$this->submit}');\n";
	}

	//-------------------------------------------------------------------------------

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
			$out="<td class=\"".$this->cssSunday."\">".$var."</td>";		
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