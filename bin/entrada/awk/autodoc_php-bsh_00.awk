# Busca definiciones de clases, propiedades y funciones

BEGIN {
#	print "----------------------------------------------------------------"
#	print ""
#	print FILENAME
	s = 0	
	k_pro = 0
	k_fun = 0
	k_cla = 0
}

$1 ~ /class/ {
	if( s == 0 ) {
		print "\n\033[1m CLASE \033[0m (" NR ") " $2
		k_cla ++
		k_fun = 0
		k_pro = 0
	}
}

$1 ~ /^ *var */ {
	if( s == 0 ) {
		if( k_pro == 0) print ""
		print "\033[1m   PROPIEDAD \033[0m (" NR ") " $2 ""
		k_pro ++
	}
}

$1 ~ /function/ {
	if( s == 0 ){
		if( k_fun == 0) print ""
		print "\033[1m   FUNCION\033[0m (" NR ") " $2 
		k_fun ++
	}
}

# Diferencia los contextos de JAVASCRIPT y PHP

/<script/ {
	s = 1
}

/<\/script/ {
	s = 0
}

#END {
#	print ""
#	print "   RESUTADOS"
#	print "   ---------"
#	print ""
#	if (k_cla > 0) print "      Clases: " k_cla
#	if (k_pro > 0) print "      Propiedades: " k_pro
#	if (k_fun > 0) print "      Funciones: " k_fun
#	print ""
#	print "----------------------------------------------------------------"
#}