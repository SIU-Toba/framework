        var ER_NUM = /^\-?\d+$/;
        function cal_error (str_message, mostrar_error) 
        {
			//Compatilidad hacia atras
			if (mostrar_error) {
	            alert (str_message);
				return false;
			} else {
	            return str_message;
			}
        }
        
        function validar_fecha (str_date, mostrar_error) 
        {
			if (mostrar_error == null) mostrar_error = true;
            var arr_date = str_date.split('/');
            if (str_date.length == 0)
                return true;
                
            if (arr_date.length != 3) return cal_error ("Formato de Fecha Invalido: '" + str_date + "'.\nEl Formato Aceptado es dd/mm/yyyy.", mostrar_error);
            if (!arr_date[0]) return cal_error ("Formato de Fecha Invalido: '" + str_date + "'.\nNo se Encuentra el Día para el Mes.", mostrar_error);
            if (!ER_NUM.exec(arr_date[0])) return cal_error ("Invalido Día del Mes: '" + arr_date[0] + "'.\nSon permitidos solo valores Numericos.", mostrar_error);
            if (!arr_date[1]) return cal_error ("Formato de Fecha Invalido: '" + str_date + "'.\nEl Mes no es Válido.", mostrar_error);
            if (!ER_NUM.exec(arr_date[1])) return cal_error ("Invalido Valor para el Mes: '" + arr_date[1] + "'.\nSon permitidos solo valores Numericos.", mostrar_error);
            if (!arr_date[2]) return cal_error ("Formato de Fecha Invalido: '" + str_date + "'.\nEl Año no es Válido.");
            if (!ER_NUM.exec(arr_date[2])) return cal_error ("Invalido Valor para el Año: '" + arr_date[2] + "'.\nSon permitidos solo valores Numericos.", mostrar_error);
        
            var dt_date = new Date();
            dt_date.setDate(1);
        
            if (arr_date[1] < 1 || arr_date[1] > 12) return cal_error ("Invalido Valor para el Mes: '" + arr_date[1] + "'.\nRago Permitido 01-12.", mostrar_error);
            dt_date.setMonth(arr_date[1]-1);
             
            if (arr_date[2] < 100) arr_date[2] = Number(arr_date[2]) + (arr_date[2] < NUM_CENTYEAR ? 2000 : 1900);
            dt_date.setFullYear(arr_date[2]);
        
            var dt_numdays = new Date(arr_date[2], arr_date[1], 0);
            dt_date.setDate(arr_date[0]);
            if (dt_date.getMonth() != (arr_date[1]-1)) return cal_error ("Invalido Día del Mes: '" + arr_date[0] + "'.\nRango Permitido 01-"+dt_numdays.getDate()+".", mostrar_error);
        
            return true;
        }