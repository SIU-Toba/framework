<?php
/**
 * @package Componentes
 * @subpackage Eis
 */

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
	protected $_rango_anios = array(2000, 2020);

	final function __construct($id)
	{
		parent::__construct($id);
		$dia = date('d');
		$mes = date('m');
		$anio = date('Y');
		$semana = date('W');
		$this->_semana_seleccionada = array('semana' => $semana, 'anio' => $anio);
		$this->_dia_seleccionado = array('dia' => $dia, 'mes' =>$mes, 'anio' => $anio);
		$this->_mes_actual = array('mes' => $mes, 'anio' => $anio);
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
				if (isset($dato['dia'])) {
					$this->_calendario->setEventContent($dato['dia'], $dato['contenido']);
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
	 * Selecciona una fecha en particular para operar sobre ella
	 * @param integer $dia
	 * @param integer $mes
	 * @param integer $anio
	 */
	function set_dia_seleccionado($dia, $mes, $anio)
	{
		$this->_memoria['dia_seleccionado'] = array('dia' => $dia, 'mes' => $mes, 'anio' => $anio);
		$this->_dia_seleccionado = $this->_memoria['dia_seleccionado'];
		$this->_calendario->setSelectedDay($dia);
		$this->_calendario->setSelectedMonth($mes);
		$this->_calendario->setSelectedYear($anio);		
	}

	/**
	 * Setea un rango de años finito para la operacion del calendario
	 * @param integer $inicio
	 * @param integer $fin
	 */
	function set_rango_anios($inicio, $fin)
	{
		$this->_rango_anios = array($inicio, $fin);
	}
	
	/**
	 * Habilita o deshabilita iniciar en domingo la semana
	 * @param boolean $valor
	 */
	function set_iniciar_en_domingo($valor)
	{
		$this->_calendario->set_startOnSun($valor);
	}
	
	/**
	 * Habilita o deshabilita la seleccion en los dias sabado
	 * @param boolean $valor
	 */
	function set_sab_seleccionable($valor)
	{
		$this->_calendario->set_enableSatSelection($valor);
	}
	
	/**
	 * Habilita o deshabilita la seleccion en los dias domingo
	 * @param boolean $valor
	 */
	function set_dom_seleccionable($valor)
	{
		$this->_calendario->set_enableSunSelection($valor);
	}

	/**
	 * Habilita o deshabilita seleccionar solo dias pasados
	 * @param boolean $valor
	 */
	function set_seleccionar_solo_dias_pasados($valor)
	{
		$this->_calendario->set_seleccionar_solo_dias_pasados($valor);
	}

	/**
	 * Habilita o deshabilita el número de semana en el calendario
	 * @param boolean $valor
	 */
	function set_mostrar_semanas($valor)
	{
		$this->_calendario->set_mostrar_semanas($valor);
	}

	/**
	 * Habilita o deshabilita el resaltado del día actual
	 * @param boolean $valor
	 */
	function set_resaltar_siempre_dia_actual($valor)
	{
		$this->_calendario->set_resaltar_siempre_dia_actual($valor);
	}
	
	/**
	 * @ignore 
	 */	
	protected function cargar_seleccion_dia()
	{
		$this->_dia_seleccionado = null;
		if (isset($this->_memoria['dia_seleccionado'])) {
			$this->_dia_seleccionado = $this->_memoria['dia_seleccionado'];
		}
		if(isset($_POST[$this->_submit.'__seleccionar_dia'])) {
			$dia = $_POST[$this->_submit.'__seleccionar_dia'];
			if ($dia != '') {
				$dia = explode(apex_qs_separador, $dia);
				$this->_dia_seleccionado['dia'] = $dia[0];
				$this->_dia_seleccionado['mes'] = $dia[1];				
				$this->_dia_seleccionado['anio'] = $dia[2];	
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
		if (isset($this->_memoria['semana_seleccionada'])) {
			$this->_semana_seleccionada = $this->_memoria['semana_seleccionada'];
		}
		if(isset($_POST[$this->_submit.'__seleccionar_semana'])) {
			$semana = $_POST[$this->_submit.'__seleccionar_semana'];
			if ($semana != '') {
				$semana = explode(apex_qs_separador, $semana);
				$this->_semana_seleccionada['semana'] = $semana[0];		
				$this->_semana_seleccionada['anio'] = $semana[1];
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
		if (isset($this->_memoria['mes_actual'])) {
			$this->_mes_actual = $this->_memoria['mes_actual'];
		}
		if(isset($_POST[$this->_submit.'__cambiar_mes'])) {
			$mes = $_POST[$this->_submit.'__cambiar_mes'];
			if ($mes != '') {
				$mes = explode(apex_qs_separador, $mes);
				$this->_mes_actual['mes'] = $mes[0];		
				$this->_mes_actual['anio'] = $mes[1];		
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
		if(isset($_POST[$this->_submit]) && $_POST[$this->_submit]!='') {
			$evento = $_POST[$this->_submit];	
			//El evento estaba entre los ofrecidos?
			if (isset($this->_memoria['eventos'][$evento]) ) {
				if ($evento == 'seleccionar_dia') {
					$parametros = $this->_dia_seleccionado;
				} elseif ($evento == 'seleccionar_semana') {
					$parametros = $this->_semana_seleccionada;
				} elseif ($evento == 'cambiar_mes') {
					$parametros = $this->_mes_actual;
				}
				$this->reportar_evento( $evento, $parametros );
			}
		}
		$this->borrar_memoria_eventos_atendidos();
	}
	
	function generar_html()
	{
		//Campos de comunicación con J
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit.'__seleccionar_semana', '');
		echo toba_form::hidden($this->_submit.'__seleccionar_dia', '');
		echo toba_form::hidden($this->_submit.'__cambiar_mes', '');

		$this->_calendario->updateCalendar($this->_mes_actual['mes'], $this->_mes_actual['anio']);
		$this->_calendario->enableDatePicker($this->_rango_anios[0], $this->_rango_anios[1]);
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
	 * @return array
	 */
	function get_contenido($dia)
	{
		$datos = $this->_calendario->getEventContent($dia);
		return $datos;
	}
	
	/**
	 * @return calendario
	 */
	function get_calendario()
	{
		return $this->_calendario;
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
 * Clase interna de calendario que se mergeo con activecalendar
 * @package Varios
 * @ignore 
 * 
 * @class: activeCalendar
 * @project: Active Calendar Class
 * @version: 1.0.4 (stable);
 * @author: Giorgos Tsiledakis;
 * @date: 2005-3-2;
 * @copyright: Giorgos Tsiledakis;
 * @license: GNU LESSER GENERAL PUBLIC LICENSE;
 * Support, feature requests and bug reports please at : http://www.micronetwork.de/activecalendar/
 * Special thanks to Corissia S.A (http://www.corissia.com) for the permission to publish the source code
 * Thanks to Maik Lindner (http://nifox.com) for his help developing this class
 */
class calendario //extends activecalendar
{
	/*
	********************************************************************************
	You can change below the month and day names, according to your language
	This is just the default configuration. You may set the month and day names by calling setMonthNames() and setDayNames()
	********************************************************************************
	*/
	protected $jan='Enero';
	protected $feb='Febrero';
	protected $mar='Marzo';
	protected $apr='Abril';
	protected $may='Mayo';
	protected $jun='Junio';
	protected $jul='Julio';
	protected $aug='Agosto';
	protected $sep='Septiembre';
	protected $oct='Octubre';
	protected $nov='Noviembre';
	protected $dec='Diciembre';
	protected $sun='Dom';
	protected $mon='Lun';
	protected $tue='Mar';
	protected $wed='Mie';
	protected $thu='Jue';
	protected $fri='Vie';
	protected $sat='Sab';
	/*
	********************************************************************************
	You can change below the default year's and month's view navigation controls
	********************************************************************************
	*/
	protected $yearNavBack=" &lt;&lt; "; // Previous year, this could be an image link
	protected $yearNavForw=" &gt;&gt; "; // Next year, this could be an image link
	protected $monthNavBack=" &lt;&lt; "; // Previous month, this could be an image link
	protected $monthNavForw=" &gt;&gt; "; // Next month, this could be an image link
	protected $selBtn='Ir'; // value of the date picker button (if enabled)
	protected $monthYearDivider=' '; // the divider between month and year in the month`s title
	/*
	********************************************************************************
	$startOnSun = false: first day of week is Monday
	$startOnSun = true: first day of week is Sunday
	********************************************************************************
	*/
	protected $startOnSun=false;
	/*
	********************************************************************************
	$rowCount : defines the number of months in a row in yearview ( can be also set by the method showYear() )
	********************************************************************************
	*/
	protected $rowCount=4;
	/*
	********************************************************************************
	Names of the generated html classes. You may change them to avoid any conflicts with your existing CSS
	********************************************************************************
	*/
	protected $cssYearTable='year'; // table tag: calendar year
	protected $cssYearTitle='yearname'; // td tag: calendar year title
	protected $cssYearNav='yearnavigation'; // td tag: calendar year navigation
	protected $cssMonthTable='month'; // table tag: calendar month
	protected $cssMonthTitle='monthname'; // td tag: calendar month title
	protected $cssMonthNav='monthnavigation'; // td tag: calendar month navigation
	protected $cssWeekDay='dayname'; // tr tag: calendar weekdays
	protected $cssPicker='datepicker'; // td tag: date picker
	protected $cssPickerForm='datepickerform'; // form tag: date picker form
	protected $cssPickerMonth='monthpicker'; // select tag: month picker
	protected $cssPickerYear='yearpicker'; // select tag: year picker
	protected $cssPickerButton='pickerbutton'; // input (submit) tag: date picker button
	protected $cssMonthDay='monthday'; // td tag: days, that belong to the current month
	protected $cssWeek='weeknumber'; // td tag: weeks, that belong to the current month
	protected $cssWeekNoSelec = 'weeknoselec';
	protected $cssNoMonthDay='monthday'; // td tag: days, that do not belong to the current month
	protected $cssToday='today'; // td tag: the current day
	protected $cssSelecDay='selectedday'; // td tag: the selected day
	protected $cssSunday='sunday'; // td tag: all Sundays (can be disabled, see below)
	protected $cssSaturday='saturday'; // td tag: all Saturdays (can be disabled, see below)
	protected $cssEvent='event'; // td tag: event day set by setEvent(). Multiple class names can be generated
	protected $cssPrefixSelecEvent='selected'; // prefix for the event class name if the event is selected
	protected $cssPrefixTodayEvent='today'; //  prefix for the event class name if the event is the current day
	protected $cssEventContent='eventcontent'; // table tag: calendar event content. Multiple class names can be generated
	protected $crSunClass=true; // true: creates a td class on every Sunday (set above)
	protected $crSatClass=true; // true: creates a td class on every Saturday (set above)
	/*
	********************************************************************************
	You can change below the GET VARS NAMES (navigation + day links)
	You should modify the private method mkUrl(), if you want to change the structure of the generated links
	********************************************************************************
	*/
	protected $yearID='yearID';
	protected $monthID='monthID';
	protected $dayID='dayID';
	protected $weekID='weekID';
	/*
	********************************************************************************
	Default start and end year for the date picker (can be changed, if using the ADOdb Date Library)
	********************************************************************************
	*/
	protected $startYear=1971;
	protected $endYear=2100;
	
	protected $mostrar_semanas = true;
	protected $mostrar_mes = true;
	protected $solo_pasados = true;
	protected $siempre_resalta_dia_actual = false;
	
	/*
	********************************************************************************
	Permitir la selección de los días sábado y domingo
	********************************************************************************
	*/
	protected $enableSunSelection=false;
	protected $enableSatSelection=false;
	
	/*
	********************************************************************************
	PUBLIC activeCalendar() -> class constructor, does the initial date calculation
	$GMTDiff: GMT Zone for current day calculation, do not set to use local server time
	********************************************************************************
	*/
	function semana($semana, $anio)
	{
		$anio_actual = $this->mkActiveTime(0, 0, 0, 1, 1, $anio); 
		$sabado = $anio_actual + (60*60*24*7*$semana);
		$lunes = $sabado - (60*60*24*5);

		return $lunes; 
	}
	
	function __construct($week=false,$year=false,$month=false,$day=false,$GMTDiff='none')
	{
		$this->timetoday = time();
		$this->selectedday = -2;
		$this->selectedyear = $year;
		if ($week)	{
			$this->selectedweek = $week;
			$semana = $this->semana($week, $year);
			$day = $this->mkActiveGMDate('d', $semana);
			$month = $this->mkActiveGMDate('m', $semana);
		} else {
			$this->selectedweek = -1;
		}
		$this->selectedmonth = $month;
		if (!$month) {
			$month = 1;
		}
		if (!$day) {
			$day = 1;
		} else {
			$this->selectedday=$day;
		}
		
		$h = $this->mkActiveGMDate('H');
		$m = $this->mkActiveGMDate('i');
		$s = $this->mkActiveGMDate('s');
		$d = $this->mkActiveGMDate('d');
		$W = $this->mkActiveGMDate('W');
		$mo = $this->mkActiveGMDate('m');
		$y = $this->mkActiveGMDate('Y');
		$is_dst = $this->mkActiveDate('I');
		if ($GMTDiff != 'none') {
			$this->timetoday = $this->mkActiveTime($h,$m,$s,$mo,$d,$y) + (3600*($GMTDiff+$is_dst));
		}
		
		$this->unixtime=$this->mkActiveTime($h,$m,$s,$month,$day,$year);
		if ($this->unixtime == -1 || !$year) {
			$this->unixtime = $this->timetoday;
		}
		$this->daytoday = $this->mkActiveDate('d');
		$this->monthtoday = $this->mkActiveDate('m');
		$this->yeartoday = $this->mkActiveDate('Y');
		$this->weektoday = $this->mkActiveDate('W');

		if (!$day) {
			$this->actday = $this->daytoday;
		} else {
			$this->actday = $this->mkActiveDate('d',$this->unixtime);
		}
		if (!$month) {
			$this->actmonth = $this->monthtoday;
		} else {
			$this->actmonth = $this->mkActiveDate('m',$this->unixtime);
		}
		if (!$year) {
			$this->actyear = $this->yeartoday;
		} else {
			$this->actyear = $this->mkActiveDate('Y',$this->unixtime);
		}
		if (!$week) {
			$this->actweek = $this->weektoday;
		} else {
			$this->actweek = $this->mkActiveDate('W',$this->unixtime);
		}
		$this->has31days = checkdate($this->actmonth,31,$this->actyear);
		$this->isSchalt = checkdate(2,29,$this->actyear);

		if ($this->isSchalt == 1 && $this->actmonth == 2) {
			$this->maxdays = 29;
		} elseif ($this->isSchalt != 1 && $this->actmonth == 2) {
			$this->maxdays = 28;
		} elseif ($this->has31days == 1) {
			$this->maxdays = 31;
		} else { $this->maxdays = 30; }

		// el número de día de la semana del primer día del mes actual: 0 (para domingo)...6 (para sábado)
		$this->firstday = $this->mkActiveDate('w', $this->mkActiveTime(0,0,1,$this->actmonth,1,$this->actyear)); 
		// la fecha del primer día del mes actual medida en número de segundos (Unix)
		$this->firstdate = $this->mkActiveTime(0,0,1,$this->actmonth,1,$this->actyear);
		$this->GMTDiff = $GMTDiff;
	}
	
	/**
	 * Determina si se mostraran los numeros de semana en la presentacion del calendario
	 * @param boolean $mostrar
	 */
	function set_mostrar_semanas($mostrar)
	{
		$this->mostrar_semanas = $mostrar;
	}

	/**
	 * Determina si se muestra el mes actual
	 * @param boolean $mostrar
	 */
	function set_mostrar_mes_actual($mostrar)
	{
		$this->mostrar_mes = $mostrar;
	}

	/**
	 * Determina si puede seleccionar fechas a futuro o no
	 * @param boolean $seleccionar
	 */
	function set_seleccionar_solo_dias_pasados($seleccionar)
	{
		$this->solo_pasados = $seleccionar;
	}

	/**
	 * Determina si el dia seleccionado se mostrara con un estilo resaltado
	 * @param boolean $resaltar
	 */
	function set_resaltar_siempre_dia_actual($resaltar)
	{
		$this->siempre_resalta_dia_actual = $resaltar;
	}
	
	/**
	 * Determina si el sábado es dia seleccionable
	 * @return boolean 
	 */
	function get_enableSatSelection()
	{
		return $this->enableSatSelection;
	}

	/**
	 * Marca / desmarca el sábado como dia seleccionable
	 * @param boolean $valor
	 */
	function set_enableSatSelection($valor)
	{
		$this->enableSatSelection = $valor;
	}

	/**
	 *  Determina si el domingo es dia seleccionable
	 * @return boolean
	 */
	function get_enableSunSelection()
	{
		return $this->enableSunSelection;
	}

	/**
	 * Marca /  desmarca el domingo como dia seleccionable
	 * @param boolean $valor
	 */
	function set_enableSunSelection($valor)
	{
		$this->enableSunSelection = $valor;
	}
	
	/*
	********************************************************************************
	PUBLIC enableYearNav() -> enables the year's navigation controls
	********************************************************************************
	*/
	function enableYearNav($link=false,$arrowBack=false,$arrowForw=false)
	{
		if ($link) {
			$this->urlNav = $link;
		} else {
			$this->urlNav = $_SERVER['PHP_SELF'];
		}
		if ($arrowBack) {
			$this->yearNavBack = $arrowBack;
		}
		if ($arrowForw) {
			$this->yearNavForw = $arrowForw;
		}
		$this->yearNav=true;
	}
	
	/*
	********************************************************************************
	PUBLIC enableMonthNav() -> enables the month's navigation controls
	********************************************************************************
	*/
	function enableMonthNav($link=false,$arrowBack=false,$arrowForw=false)
	{
		if ($link) {
			$this->urlNav=$link;
		} else {
			$this->urlNav=$_SERVER['PHP_SELF'];
		}
		if ($arrowBack) {
			$this->monthNavBack=$arrowBack;
		}
		if ($arrowForw) {
			$this->monthNavForw=$arrowForw;
		}
		$this->monthNav=true;
	}
	
	/*
	********************************************************************************
	PUBLIC enableDayLinks() -> enables the day links
	param javaScript: sets a Javascript function on each day link
	********************************************************************************
	*/
	function enableDayLinks($link=false,$javaScript=false)
	{
		if ($link) {
			$this->url=$link;
		} else {
			$this->url=$_SERVER['PHP_SELF'];
		}
		if ($javaScript) {
			$this-> $this->javaScriptDay=$javaScript;
		}
		$this->dayLinks=true;
	}
	
	/*
	********************************************************************************
	PUBLIC enableDayLinks() -> enables the day links
	param javaScript: sets a Javascript function on each day link
	********************************************************************************
	*/
	function enableWeekLinks($link=false,$javaScript=false)
	{
		if ($link) {
			$this->url=$link;
		} else {
			$this->url=$_SERVER['PHP_SELF'];
		}
		if ($javaScript) {
			$this-> $this->javaScriptDay=$javaScript;
		}
		$this->weekLinks=true;
	}

	/*
	********************************************************************************
	PUBLIC enableDatePicker() -> enables the day picker control
	********************************************************************************
	*/
	function enableDatePicker($startYear=false,$endYear=false,$link=false,$button=false)
	{
		if ($link) {
			$this->urlPicker=$link;
		} else {
			$this->urlPicker=$_SERVER['PHP_SELF'];
		}
		if ($startYear && $endYear) {
			if ($startYear>=$this->startYear && $startYear<$this->endYear) {
				$this->startYear=$startYear;
			}
			if ($endYear>$this->startYear && $endYear<=$this->endYear) {
				$this->endYear=$endYear;
			}
		}
		if ($button) {
			$this->selBtn=$button;
		}
		$this->datePicker=true;
	}
	
	/*
	********************************************************************************
	PUBLIC setEvent() -> sets a calendar event, $id: the HTML class (css layout)
	********************************************************************************
	*/
	function setEvent($year,$month,$day,$id=false,$url=false)
	{
		$eventTime=$this->mkActiveTime(0,0,1,$month,$day,$year);
		if (!$id) {
			$id=$this->cssEvent;
		}
		$this->calEvents[$eventTime]=$id;
		$this->calEventsUrl[$eventTime]=$url;
	}

	/*
	********************************************************************************
	PUBLIC setMonthNames() -> sets the month names, $namesArray must be an array of 12 months starting with January
	********************************************************************************
	*/
	function setMonthNames($namesArray)
	{
		if (!is_array($namesArray) || count($namesArray)!=12) {
			return false;
		} else {
			$this->monthNames=$namesArray;
		}
	}
	/*
	********************************************************************************
	PUBLIC setDayNames() -> sets the week day names, $namesArray must be an array of 7 days starting with Sunday
	********************************************************************************
	*/
	function setDayNames($namesArray)
	{
		if (!is_array($namesArray) || count($namesArray)!=7) {
			return false;
		} else {
			$this->dayNames=$namesArray;
		}
	}
	/*
	********************************************************************************
	PUBLIC view_event_contents()
	********************************************************************************
	*/
	function viewEventContents()
	{
		$this->showEvents = true;
		$this->cssMonthDay = 'monthdayevents';
		$this->cssWeek = 'weeknumberevents';
		$this->cssWeekNoSelec = 'weeknoselecevents';
		$this->cssNoMonthDay = 'nomonthdayevents';
		$this->cssToday = 'todayevents';
		$this->cssSelecDay = 'selecteddayevents';
		$this->cssSunday = 'sundayevents';
		$this->cssSaturday = 'saturdayevents';
		$this->cssEvent = 'eventevents';
		$this->cssPrefixSelecEvent = 'selectedeventevents';
		$this->cssPrefixTodayEvent = 'todayevents';
	}
	/*
	********************************************************************************
	PUBLIC getSelectedDay() -> returns the actually selected day 
	********************************************************************************
	*/
	function getSelectedDay()
	{
		return $this->selectedday;
	}
	/*
	********************************************************************************
	PUBLIC getSelectedMonth() -> returns the actually selected month
	********************************************************************************
	*/
	function getSelectedMonth()
	{
		return $this->selectedmonth;
	}
	/*
	********************************************************************************
	PUBLIC getSelectedYear() -> returns the actually selected year
	********************************************************************************
	*/
	function getSelectedYear()
	{
		return $this->selectedyear;
	}
	/*
	********************************************************************************
	PUBLIC getSelectedWeek() -> returns the actually selected week
	********************************************************************************
	*/
	function getSelectedWeek()
	{
		return $this->selectedweek;
	}
	/*
	********************************************************************************
	PUBLIC setSelectedDay()
	********************************************************************************
	*/
	function setSelectedDay($day)
	{
		$this->selectedday = $day;
	}
	/*
	********************************************************************************
	PUBLIC setSelectedMonth()
	********************************************************************************
	*/
	function setSelectedMonth($month)
	{
		$this->selectedmonth = $month;
	}
	/*
	********************************************************************************
	PUBLIC setSelectedYear()
	********************************************************************************
	*/
	function setSelectedYear($year)
	{
		$this->selectedyear = $year;
	}
	/*
	********************************************************************************
	PUBLIC setSelectedWeek()
	********************************************************************************
	*/
	function setSelectedWeek($week)
	{
		$this->selectedweek = $week;
	}
	/*
	********************************************************************************
	PUBLIC getActMonth() -> returns the actual month
	********************************************************************************
	*/
	function setActMonth($month)
	{
		$this->actmonth = $month;
	}
	/*
	********************************************************************************
	PUBLIC getActYear() -> returns the actual year
	********************************************************************************
	*/
	function setActYear($year)
	{
		$this->actyear = $year;
	}
	
	/**
	 * @ignore
	 */
	function updateCalendar($mes, $anio)
	{
		$this->setActMonth($mes);
		$this->setActYear($anio);
		$this->setSelectedMonth($mes);
		$this->setSelectedYear($anio);
		
		$this->has31days = checkdate($this->actmonth,31,$this->actyear);
		$this->isSchalt = checkdate(2,29,$this->actyear);

		if ($this->isSchalt == 1 && $this->actmonth == 2) {
			$this->maxdays = 29;
		} elseif ($this->isSchalt != 1 && $this->actmonth == 2) {
			$this->maxdays = 28;
		} elseif ($this->has31days == 1) {
			$this->maxdays = 31;
		} else { $this->maxdays = 30; }		
	
		$this->firstday = $this->mkActiveDate('w', $this->mkActiveTime(0,0,1,$this->actmonth,1,$this->actyear)); 
		$this->firstdate = $this->mkActiveTime(0,0,1,$this->actmonth,1,$this->actyear);
	}

	/**
	 * @ignore
	 */
	function setEventContent($day, $content)
	{
		$eventContent[$day] = $content;
		$this->calEventContent[] = $eventContent;
	}

	/**
	 * @ignore
	 */
	function getEventContent($day)
	{
		return $this->content($day);
	}

	/**
	 * @ignore
	 */
	function mkEventContent($var)
	{
		$day = $this->mkActiveDate('Y-m-d', $this->mkActiveTime(0,0,1,$this->actmonth,$var,$this->actyear));
		$hasContent = $this->content($day);
		$out='';
		if ($hasContent) {
			foreach($hasContent as $content) {
				$out.="<table class=\"".$this->cssEventContent."\">";
				$out.="<tr><td>".$content."</td></tr></table>";
			}
		}
		return $out;
	}

	/**
	 * @ignore
	 */
	function content($var)
	{
		$hasContent = false;	
		if ($this->calEventContent) {
			for ($x=0; $x<count($this->calEventContent); $x++) {
				$eventContent = $this->calEventContent[$x];
				foreach($eventContent as $eventTime => $eventContent) {
					if ($eventTime == $var) {
						$hasContent[] = $eventContent;
					}
				}
			}
		}
		
		return $hasContent;
	}

	/**
	 * @ignore
	 */
	function showMonth($objeto_js=null, $eventos=array(), $editor=null)
	{
		$out = $this->mkMonthHead();
		$out .= $this->barra_editor($editor);
		if ($this->mostrar_mes) {
			$out .= $this->mkMonthTitle();
		}
		$out .= $this->mkDatePicker($objeto_js, $eventos);
		$out .= $this->mkWeekDays();
		$out .= $this->mkMonthBody($objeto_js, $eventos);
		$out .= $this->mkMonthFoot();
		return $out;
	}

	/**
	 * @ignore
	 */
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

	/*
	----------------------
	@START PRIVATE METHODS
	----------------------
	*/
	/*
	********************************************************************************
	THE FOLLOWING METHODS AND VARIABLES ARE PRIVATE. PLEASE DO NOT CALL OR MODIFY THEM
	********************************************************************************
	*/
	private $timezone=false;
	private $yearNav=false;
	private $monthNav=false;
	private $dayLinks=false;
	private $weekLinks=false;
	private $datePicker=false;
	private $url=false;
	private $urlNav=false;
	private $urlPicker=false;
	private $calEvents=false;
	private $calEventsUrl=false;
	private $javaScriptDay=false;
	private $monthNames=false;
	private $dayNames=false;
	private $calEventContent=false;
	private $calEventContentUrl=false;
	private $calEventContentId=false;
	private $calInit=0;
	
	/*
	********************************************************************************
	Día, Mes, Año y Semana actualmente seleccionados
	********************************************************************************
	*/
	private $selectedday=-1;
	private $selectedmonth=-1;
	private $selectedyear=-1;
	private $selectedweek=-1;
	/*
	********************************************************************************
	Indica si se deben mostrar los eventos para un día o semana en la interface del calendario
	********************************************************************************
	*/
	private $showEvents;
	
	/*
	********************************************************************************
	PRIVATE weekNumber($day) -> make and return week number for certain day
	********************************************************************************
	*/
	function weekNumber($date)
	{
		if ($date) {
			$week = $this->mkActiveDate('W', $date);
		} else {
			$week = $this->mkActiveDate('W', $this->mkActiveTime(0,0,1,$this->selectedmonth,1,$this->selectedyear));
		}
		if($week > 53) {
			return 1;
		} else {
			return $week;
		}
	}
	/*
	********************************************************************************
	PRIVATE mkMonthHead() -> creates the month table tag
	********************************************************************************
	*/
	function mkMonthHead()
	{
		$out = "<div align='center'>";
		$out .= "<table class=\"".$this->cssMonthTable."\">\n";
		return $out;
	}
	/*
	********************************************************************************
	PRIVATE mkMonthTitle() -> creates the tile and navigation tr tag of the month table
	********************************************************************************
	*/
	function mkMonthTitle()
	{
		if (!$this->monthNav) {
			$out="<tr><td class=\"".$this->cssMonthTitle."\" colspan=\"8\">";
			$out.=$this->getMonthName().$this->monthYearDivider.$this->actyear;
			$out.="</td></tr>\n";
		} else {
			$out="<tr><td class=\"".$this->cssMonthNav."\" colspan=\"2\">";
			if ($this->actmonth==1) {
				$out.=$this->mkUrl($this->actyear-1,'12');
			} else {
				$out.=$this->mkUrl($this->actyear,$this->actmonth-1);
			}
			$out.=$this->monthNavBack.'</a></td>';
			$out.="<td class=\"".$this->cssMonthTitle."\" colspan=\"3\">";
			$out.=$this->getMonthName().$this->monthYearDivider.$this->actyear.'</td>';
			$out.="<td class=\"".$this->cssMonthNav."\" colspan=\"2\">";
			if ($this->actmonth==12) {
				$out.=$this->mkUrl($this->actyear+1,'1');
			} else {
				$out.=$this->mkUrl($this->actyear,$this->actmonth+1);
			}
			$out.=$this->monthNavForw."</a></td></tr>\n";
		}
		return $out;
	}

	/**
	 * @ignore
	 */
	function mkDatePicker($objeto_js, $eventos=array())
	{
		$pickerSpan = 8;
		if ($this->datePicker) {
			$evento_js = toba_js::evento('cambiar_mes', $eventos['cambiar_mes']);
			$js = "{$objeto_js}.set_evento($evento_js);";
						
			$out="<tr><td class=\"".$this->cssPicker."\" colspan=\"".$pickerSpan."\">\n";
			$out.="<select name=\"".$this->monthID."\" id=\"".$this->monthID."\" class=\"".$this->cssPickerMonth."\" onchange=\"$js\">\n";
			for ($z=1;$z<=12;$z++) {
				if ($z <= 9) {
					$z = "0$z";
				}
				if ($z==$this->actmonth) {
					$out.="<option value=\"".$z."\" selected=\"selected\" >".$this->getMonthName($z)."</option>\n";
				} else {
					$out.="<option value=\"".$z."\">".$this->getMonthName($z)."</option>\n";
				}
			}
			$out.="</select>\n";
			$out.="<select name=\"".$this->yearID."\" id=\"".$this->yearID."\" class=\"".$this->cssPickerYear."\" onchange=\"$js\">\n";
			for ($z=$this->startYear;$z<=$this->endYear;$z++) {
				if ($z==$this->actyear) {
					$out.="<option value=\"".$z."\" selected=\"selected\">".$z."</option>\n";
				} else {
					$out.="<option value=\"".$z."\">".$z."</option>\n";
				}
			}
			$out.="</select>\n";
			$out.="</td></tr>\n";
		}
		return $out;
	}

	/**
	 * @ignore
	 */
	function mkMonthBody($objeto_js=null, $eventos=array())
	{
		$out='<tr>';
		$monthday=0;
		if ($this->mostrar_semanas) {
			$out.=$this->mkWeek($this->firstdate, $objeto_js, $eventos);
		}
		for ($x=0; $x<=6; $x++) {
			if ($x>=$this->firstday) {
				$monthday++;
				$out.=$this->mkDay($monthday, $objeto_js, $eventos);
			}
			else {
				$out .= "<td class=\"".$this->cssNoMonthDay."\"></td>";
			}
		}
		$out.="</tr>\n";
		$goon = $monthday + 1;
		$stop=0;
		for ($x=0; $x<=6; $x++) {
			if ($goon>$this->maxdays) break;
			if ($stop==1) break;
			$out.='<tr>';
			$date = $this->mkActiveTime(0,0,1,$this->actmonth,$goon,$this->actyear);
			if ($this->mostrar_semanas) {
				$out.=$this->mkWeek($date, $objeto_js, $eventos);
			}
			for ($i=$goon; $i<=$goon+6; $i++) {
				if ($i>$this->maxdays) {
					$out.="<td class=\"".$this->cssNoMonthDay."\"></td>";
					$stop=1;
				} else {
					$out.=$this->mkDay($i, $objeto_js, $eventos);
				}
			}
			$goon=$goon+7;
			$out.="</tr>\n";
		}
		return $out;
	}

	/**
	 * @ignore
	 */
	function mkWeekDays()
	{
		$out = '';
		if ($this->startOnSun) {
			if ($this->mostrar_semanas) {
				$out .="<tr class=\"".$this->cssWeekDay."\"><td>"."Sem"."</td>";
			}
			$out.='<td>'.$this->getDayName(0).'</td>';
			$out.='<td>'.$this->getDayName(1).'</td>';
			$out.='<td>'.$this->getDayName(2).'</td>';
			$out.='<td>'.$this->getDayName(3).'</td>';
			$out.='<td>'.$this->getDayName(4).'</td>';
			$out.='<td>'.$this->getDayName(5).'</td>';
			$out.='<td>'.$this->getDayName(6)."</td></tr>\n";
		} else {
			if ($this->mostrar_semanas) {
				$out .="<tr class=\"".$this->cssWeekDay."\"><td>".'Sem'.'</td>';
			}
			$out.='<td>'.$this->getDayName(1).'</td>';
			$out.='<td>'.$this->getDayName(2).'</td>';
			$out.='<td>'.$this->getDayName(3).'</td>';
			$out.='<td>'.$this->getDayName(4).'</td>';
			$out.='<td>'.$this->getDayName(5).'</td>';
			$out.='<td>'.$this->getDayName(6).'</td>';
			$out.='<td>'.$this->getDayName(0)."</td></tr>\n";
			$this->firstday=$this->firstday-1;
			if ($this->firstday<0) {
				$this->firstday=6;
			}
		}
		return $out;
	}

	/**
	 * @ignore
	 */
	function viernes($semana, $anio)
	{
		$ts_semana  = strtotime('+' . $semana . ' weeks', strtotime($anio . '0101'));
		$ajuste = 5 - date('w', $ts_semana);
		$ts_viernes = strtotime($ajuste . ' days', $ts_semana);
		
		if (date('W', $ts_viernes) == $semana) {
			return $ts_viernes;
		} else {// se pasó a la semana siguiente
			return strtotime('-7 days', $ts_viernes);
		}
	}

	/**
	 * @ignore
	 */
	function compare_week($week, $year)
	{
		$viernes = $this->viernes($week, $year);
		return $this->compare_date($viernes);
	}

	/**
	 * @ignore
	 */
	function mkWeek($date, $objeto_js=null, $eventos=array())
	{
		$week = $this->weekNumber($date);
		$year = $this->mkActiveDate('Y',$date);
		
		if (!$this->get_weekLinks()) {
			if ($week == $this->getSelectedWeek() && $year == $this->getSelectedYear()) {
				$out = "<td class=\"".$this->cssSelecDay."\">".$this->weekNumber($date)."</td>\n";
			} else {
				$out = "<td class=\"".$this->cssWeek."\">".$this->weekNumber($date)."</td>\n";
			}
		} else {
			if ($this->compare_week($this->weekNumber($date),$this->actyear) == 1) {
				$out = "<td class=\"".$this->cssWeekNoSelec."\">".$this->weekNumber($date)."</td>\n";	
			} else {	
				$evento_js = toba_js::evento('seleccionar_semana', $eventos['seleccionar_semana'], "{$this->weekNumber($date)}||{$this->mkActiveDate('Y',$date)}");
				$js = "{$objeto_js}.set_evento($evento_js);";
				
				if ($week == $this->getSelectedWeek() && $year == $this->getSelectedYear()) {
					$out = "<td class=\"".$this->cssSelecDay."\" style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$this->weekNumber($date)."</td>\n";	
				} else {
					$out = "<td class=\"".$this->cssWeek."\" style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$this->weekNumber($date)."</td>\n";	
				}
			}		
		}	
		return $out;
	}

	/**
	 * @ignore
	 */
	function compare_date($day)
	{
		$fecha_hoy = $this->mkActiveTime(0,0,1,$this->monthtoday,$this->daytoday,$this->yeartoday);
		if ($day < $fecha_hoy) {
			return -1;
		} elseif ($day > $fecha_hoy) {
			return 1;
		} else {
			return 0;	
		}
	}

	/**
	 * @ignore
	 */
	function mkDay($var, $objeto_js=null, $eventos=array())
	{
		if ($var <= 9) {
			$day = "0$var";
		} else {
			$day = $var;	
		}
		$eventContent = $this->mkEventContent($var);
		$content = ($this->get_showEvents()) ? $eventContent : '';
		
		if (is_null($objeto_js)) {
			$objeto_js = $this->get_id_objeto_js();
		}		
		
		$evento_js = toba_js::evento('seleccionar_dia', $eventos['seleccionar_dia'], "{$day}||{$this->actmonth}||{$this->actyear}");
		$js = "{$objeto_js}.set_evento($evento_js);";
		$day = $this->mkActiveTime(0,0,1,$this->actmonth,$var,$this->actyear);
		
		$resalta_hoy = ($this->siempre_resalta_dia_actual || $this->getSelectedDay() < 0);

		if ($this->solo_pasados && $this->compare_date($day) == 1) {
			//Es una fecha futura y no se permite clickearla
			$out="<td class=\"".$this->cssSunday."\">".$var.$content.'</td>';		
		} elseif (($this->get_dayLinks()) && ((!$this->get_enableSatSelection() && ($this->getWeekday($var) == 0)) || ((!$this->get_enableSunSelection() && $this->getWeekday($var) == 6)))) {
			$out="<td class=\"".$this->cssSunday."\">".$var.'</td>';			
		} elseif ($var==$this->getSelectedDay() && $this->actmonth==$this->getSelectedMonth() && $this->actyear==$this->getSelectedYear()) {
			if (!$this->get_dayLinks()) {
				$out="<td class=\"".$this->cssSelecDay."\">".$var.$content.'</td>';
			} else {
				$out="<td class=\"".$this->cssSelecDay."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content.'</td>';
			}
		} elseif ($var==$this->daytoday && $this->actmonth==$this->monthtoday && $this->actyear==$this->yeartoday && $resalta_hoy && $this->getSelectedMonth()==$this->monthtoday && $this->getSelectedWeek()<0) {
			if (!$this->get_dayLinks()) {
				$out="<td class=\"".$this->cssToday."\">".$var.$content.'</td>';
			} else {
				$out="<td class=\"".$this->cssToday."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content.'</td>';
			}
		} elseif ($this->getWeekday($var) == 0 && $this->crSunClass){
			if (!$this->get_dayLinks()) {
				$out="<td class=\"".$this->cssSunday."\">".$var.$content.'</td>';
			} else {
				$out="<td class=\"".$this->cssSunday."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content.'</td>';
			}
		} elseif ($this->getWeekday($var) == 6 && $this->crSatClass) {
			if (!$this->get_dayLinks()) {
				$out="<td class=\"".$this->cssSaturday."\">".$var.$content.'</td>';
			} else {
				$out="<td class=\"".$this->cssSaturday."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content.'</td>';
			}
		} else {
			if (!$this->get_dayLinks()) {
				$out="<td class=\"".$this->cssMonthDay."\">".$var.$content.'</td>';
			} else {
				$out="<td class=\"".$this->cssMonthDay."\"style='cursor: pointer;cursor:hand;' onclick=\"$js\">".$var.$content.'</td>';
			}
		}		

		return $out;
	}
	
	/*
	********************************************************************************
	PRIVATE mkMonthFoot() -> closes the month table
	********************************************************************************
	*/
	function mkMonthFoot()
	{
		return "</table>\n</div>";
	}
	/*
	********************************************************************************
	PRIVATE mkUrl() -> creates the day and navigation link structure
	********************************************************************************
	*/
	function mkUrl($year,$month=false,$day=false)
	{
		if (strpos($this->url,'?')) {
			$glue = "&amp;";
		} else {
			$glue = '?';
		}
		if (strpos($this->urlNav,'?')) {
			$glueNav="&amp;";
		} else {
			$glueNav='?';
		}
		$yearNavLink="<a href=\"".$this->urlNav.$glueNav.$this->yearID."=".$year."\">";
		$monthNavLink="<a href=\"".$this->urlNav.$glueNav.$this->yearID."=".$year."&amp;".$this->monthID."=".$month."\">";
		$dayLink="<a href=\"".$this->url.$glue.$this->yearID."=".$year."&amp;".$this->monthID."=".$month."&amp;".$this->dayID."=".$day."\">".$day.'</a>';
		if ($year && $month && $day) return $dayLink;
		if ($year && !$month && !$day) return $yearNavLink;
		if ($year && $month && !$day) return $monthNavLink;
	}
	/*
	********************************************************************************
	PRIVATE mkWeekUrl() -> creates the week and navigation link structure
	********************************************************************************
	*/
	function mkWeekUrl($week, $year)
	{
		if (strpos($this->url,'?')) {
			$glue = "&amp;";
		} else {
			$glue = '?';
		}
		if (strpos($this->urlNav,'?')) {
			$glueNav="&amp;";
		} else {
			$glueNav='?';
		}
		$weekNavLink = "<a href=\"".$this->url.$glue.$this->weekID."=".$week."&amp;".$this->yearID."=".$year."\">".$week.'</a>';
		return $weekNavLink;
	}
	/*
	********************************************************************************
	PRIVATE getMonthName() -> returns the month's name, according to the configuration
	********************************************************************************
	*/
	function getMonthName($var=false)
	{
		if (!$var) {
			$var=@$this->actmonth;
		}
		if ($this->monthNames) {
			return $this->monthNames[$var-1];
		}
		switch($var) {
			case 1: return $this->jan;
			case 2: return $this->feb;
			case 3: return $this->mar;
			case 4: return $this->apr;
			case 5: return $this->may;
			case 6: return $this->jun;
			case 7: return $this->jul;
			case 8: return $this->aug;
			case 9: return $this->sep;
			case 10: return $this->oct;
			case 11: return $this->nov;
			case 12: return $this->dec;
		}
	}
	/*
	********************************************************************************
	PRIVATE getDayName() -> returns the day's name, according to the configuration
	********************************************************************************
	*/
	function getDayName($var=false)
	{
		if ($this->dayNames) {
			return $this->dayNames[$var];
		}
		switch($var) {
			case 0: return $this->sun;
			case 1: return $this->mon;
			case 2: return $this->tue;
			case 3: return $this->wed;
			case 4: return $this->thu;
			case 5: return $this->fri;
			case 6: return $this->sat;
		}
	}
	/*
	********************************************************************************
	PRIVATE getWeekday() -> returns the weekday's number, 0 = Sunday ... 6 = Saturday
	********************************************************************************
	*/
	function getWeekday($var)
	{
		return $this->mkActiveDate('w', $this->mkActiveTime(0,0,1,$this->actmonth,$var,$this->actyear));
	}
	/*
	********************************************************************************
	PRIVATE isEvent() -> checks if a date was set as an event and creates the eventID (css layout) and eventUrl
	********************************************************************************
	*/
	function isEvent($var)
	{
		if ($this->calEvents)	{
			$checkTime=$this->mkActiveTime(0,0,1,$this->actmonth,$var,$this->actyear);
			$selectedTime=$this->mkActiveTime(0,0,1,$this->selectedmonth,$this->selectedday,$this->selectedyear);
			$todayTime=$this->mkActiveTime(0,0,1,$this->monthtoday,$this->daytoday,$this->yeartoday);
			foreach($this->calEvents as $eventTime => $eventID) {
				if ($eventTime==$checkTime) {
					if ($eventTime==$selectedTime) {
						$this->eventID=$this->cssPrefixSelecEvent.$eventID;
					} elseif ($eventTime==$todayTime) {
						$this->eventID=$this->cssPrefixTodayEvent.$eventID;
					} else {
						$this->eventID=$eventID;
					}
					if ($this->calEventsUrl[$eventTime]) {
						$this->eventUrl=$this->calEventsUrl[$eventTime];
					}
					return true;
				}
			}
		return false;
		}
	}
	/*
	********************************************************************************
	PRIVATE hasEventContent() -> checks if an event content was set
	********************************************************************************
	*/
	function hasEventContent($var)
	{
		$hasContent = false;

		if ($this->calEventContent) {
			$checkTime = $this->mkActiveTime(0,0,1,$this->actmonth,$var,$this->actyear);

			for ($x=0;$x<count($this->calEventContent);$x++) {
				$eventContent=$this->calEventContent[$x];
				$eventContentUrl=$this->calEventContentUrl[$x];
				$eventContentId=$this->calEventContentId[$x];
				foreach($eventContent as $eventTime => $eventContent) {
					if ($eventTime==$checkTime) {
						$hasContent[][$eventContentId][$eventContentUrl]=$eventContent;
					}
				}
			}
		}

		return $hasContent;
	}
	
	/*
	********************************************************************************
	PRIVATE mkActiveDate() -> checks if ADOdb Date Library is loaded and calls the date function
	********************************************************************************
	*/
	function mkActiveDate($param,$acttime=false)
	{
		if (!$acttime) {
			$acttime=$this->timetoday;
		}
		if (function_exists('adodb_date')) {
			return adodb_date($param,$acttime);
		} else {
			return date($param,$acttime);
		}
	}
	/*
	********************************************************************************
	PRIVATE mkActiveGMDate() -> checks if ADOdb Date Library is loaded and calls the gmdate function
	********************************************************************************
	*/
	function mkActiveGMDate($param,$acttime=false)
	{
		if (!$acttime) {
			$acttime=time();
		}
		if (function_exists('adodb_gmdate')) {
			return adodb_gmdate($param,$acttime);
		} else {
			return gmdate($param,$acttime);
		}
	}
	/*
	********************************************************************************
	PRIVATE mkActiveTime() -> checks if ADOdb Date Library is loaded and calls the mktime function
	********************************************************************************
	*/
	function mkActiveTime($hr,$min,$sec,$month=false,$day=false,$year=false)
	{
		if (function_exists('adodb_mktime')) {
			return adodb_mktime($hr,$min,$sec,$month,$day,$year);
		} else {
			return mktime($hr,$min,$sec,$month,$day,$year);
		}
	}
	
	/**
	 * @ignore
	 */
	function get_startOnSun()
	{
		return $this->startOnSun;
	}
	
	/**
	 * @ignore
	 */
	function set_startOnSun($valor)
	{
		$this->startOnSun = $valor;
	}
	
	/**
	 * @ignore
	 */
	function get_mon()
	{
		return $this->mon;
	}
	
	/**
	 * @ignore
	 */
	function set_mon($valor)
	{
		$this->mon = $valor;
	}
	
	/**
	 * @ignore
	 */
	function get_tue()
	{
		return $this->tue;
	}
	
	/**
	 * @ignore
	 */
	function set_tue($valor)
	{
		$this->tue = $valor;
	}
	
	/**
	 * @ignore
	 */
	function get_wed()
	{
		return $this->wed;
	}
	
	/**
	 * @ignore
	 */
	function set_wed($valor)
	{
		$this->wed = $valor;
	}
	
	/**
	 * @ignore
	 */
	function get_thu()
	{
		return $this->thu;
	}
	
	/**
	 * @ignore
	 */
	function set_thu($valor)
	{
		$this->thu = $valor;
	}
	
	/**
	 * @ignore
	 */
	function get_fri()
	{
		return $this->fri;
	}
	
	/**
	 * @ignore
	 */
	function set_fri($valor)
	{
		$this->fri = $valor;
	}
	
	/**
	 * @ignore
	 */
	function get_sat()
	{
		return $this->sat;
	}
	
	/**
	 * @ignore
	 */
	function set_sat($valor)
	{
		$this->sat = $valor;
	}
	
	/**
	 * @ignore
	 */
	function get_sun()
	{
		return $this->sun;
	}
	
	/**
	 * @ignore
	 */
	function set_sun($valor)
	{
		$this->sun = $valor;
	}
	
	/**
	 * @ignore
	 */
	protected function get_weekLinks()
	{
		return $this->weekLinks;
	}
	
	/**
	 * @ignore
	 */
	protected function get_showEvents()
	{
		return $this->showEvents;
	}
	
	/**
	 * @ignore
	 */
	protected function get_dayLinks()
	{
		return $this->dayLinks;
	}
	
}

?>