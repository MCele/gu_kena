<?php
class ci_rector extends toba_ci
{
    protected $s__votos_e;
    protected $s__votos_g;
    protected $s__votos_nd;
    protected $s__votos_d;
    
    protected $s__total_emp;
    
    //---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro_rector_e(gu_kena_ei_cuadro $cuadro)
	{
             
        //Obtengo todas las unidades menos los asentamientos y rectorado. 1:Administración central, 2:Facultad/Centro Regional/ Escuela 3:Asentamiento
            /*$unidades = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones_por_nivel(array(2));           
            
            //Cargar la cantidad de empadronados para el claustro estudiantes=3
            // en cada unidad
            $ar = $this->cargar_cant_empadronados($unidades, 3);
            
            //Ante ultima fila carga los votos totales de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] ="<span style='color:blue; font-weight:bold'>TOTAL</span>";
            $ar[$pos]['cant_empadronados'] = $this->s__total_emp;
            
            //Ultima fila carga los votos ponderados de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] = "<span style='color:red; font-weight:bold'>PONDERADOS</span>";
                        
            //Obtener las listas del claustro estudiantes=3
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(3); 
            
            //Agregar las etiquetas de todas las listas (columnas dinámicas)
            $i = 1;
            foreach($listas as $lista){
                $l['clave'] = $lista['id_nro_lista'];
                $l['titulo'] = substr($lista['nombre'], 0, 11). $lista['sigla'];
                $l['estilo'] = 'col-cuadro-resultados';
                $l['estilo_titulo'] = 'tit-cuadro-resultados';
                
                //$l['permitir_html'] = true;
                
                $grupo[$i] = $lista['id_nro_lista'];
                
                $columnas[$i] = $l;
                $this->dep('cuadro_superior_e')->agregar_columnas($columnas);
                
                //Cargar la cantidad de votos para cada lista de claustro estudiantes=3 
                //en cada unidad
                $ar = $this->cargar_cant_votos($lista['id_nro_lista'], $ar, 3);
                
                //Cargar los votos totales/ponderados para cada lista agregado como ante/última fila
                //para claustro estudiantes=3
                $ar[$pos-1][$lista['id_nro_lista']] = 0;
                $ar[$pos][$lista['id_nro_lista']] = 0;
                $ar = $this->cargar_votos_totales_ponderados($lista['id_nro_lista'], $ar, 3);
                
                $i++;
            }
            $this->dep('cuadro_superior_e')->set_grupo_columnas('Listas',$grupo);
           
                          
            $this->s__votos_e = $ar;//Guardar los votos para el calculo dhondt
            
            //Agregar datos totales de blancos, nulos y recurridos
            $b['clave'] = 'total_votos_blancos';
            $b['titulo'] = 'Blancos';
            $b['estilo'] = 'col-cuadro-resultados';
            $b['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[0] = $b;
            
            $n['clave'] = 'total_votos_nulos';
            $n['titulo'] = 'Nulos';
            $n['estilo'] = 'col-cuadro-resultados';
            $n['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[1] = $n;
            
            $r['clave'] = 'total_votos_recurridos';
            $r['titulo'] = 'Recurridos';
            $r['estilo'] = 'col-cuadro-resultados';
            $r['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[2] = $r;
            
            $this->dep('cuadro_superior_e')->agregar_columnas($bnr);
            
            
            $ar = $this->cargar_cant_b_n_r($ar, 3);
            $ar= $this->cargar_cant_votantes($ar,$listas);
            //$this->cambiar_estilo_total($ar); //ver porqué no agrega estilo
            
            return $ar;*/
        }
        
        //Metodo responsable de cargar los votos blancos, nulos y recurridos de cada unidad electoral
        function cargar_cant_b_n_r($unidades, $id_claustro){
            $p = sizeof($unidades)-2;
            //Inicializo para realizar la sumatoria
            $unidades[$p]['total_votos_blancos'] = 0;
            $unidades[$p]['total_votos_nulos'] = 0;
            $unidades[$p]['total_votos_recurridos'] = 0;            
            for($i=0; $i<$p; $i++){//Recorro las unidades
              //Agrega la cantidad de votos blancos,nulos y recurridos calculado en acta para cada unidad con claustro y tipo superior=1            
                $ar = $this->dep('datos')->tabla('acta')->cant_b_n_r($unidades[$i]['id_nro_ue'], $id_claustro, 1);
                if(sizeof($ar)>0){
                    $unidades[$i]['total_votos_blancos'] = $ar[0]['blancos'];
                    $unidades[$i]['total_votos_nulos'] = $ar[0]['nulos'];
                    $unidades[$i]['total_votos_recurridos'] = $ar[0]['recurridos'];
                    
                    //Agrego en la anteultima fila la sumatoria total
                    
                    $unidades[$p]['total_votos_blancos'] += $ar[0]['blancos'];
                    $unidades[$p]['total_votos_nulos'] += $ar[0]['nulos'];
                    $unidades[$p]['total_votos_recurridos'] += $ar[0]['recurridos'];
                }
            }    
            return $unidades;
        }
        
        //Metodo responsable de cargar la segunda columna con la cantidad de empadronados
        // en cada unidad electoral
        function cargar_cant_empadronados($unidades, $id_claustro){
            $this->s__total_emp = 0;
            for($i=0; $i<sizeof($unidades); $i++){//Recorro las unidades
                //Agrega la cantidad de empadronados calculado en acta para cada unidad con claustro
                $unidades[$i]['cant_empadronados'] = $this->dep('datos')->tabla('mesa')->cant_empadronados($unidades[$i]['id_nro_ue'], $id_claustro);
                $this->s__total_emp += $unidades[$i]['cant_empadronados'];   
            }
            return $unidades;
        }
        
        function cargar_cant_votos($id_lista, $unidades, $id_claustro){
            for($i=0; $i<sizeof($unidades)-2; $i++){//Recorro las unidades
                //Agrega la cantidad de empadronados calculado en acta para cada unidad con claustro  y tipo 'superior'
                $unidades[$i][$id_lista] = $this->dep('datos')->tabla('voto_lista_csuperior')->cant_votos($id_lista, $unidades[$i]['id_nro_ue'], $id_claustro);
            }
            return $unidades;
        }    
        
        function cargar_cant_votantes($unidades, $listas){ 
        //realiza la suma de los votos que hay en las distintas columnas
            $p = sizeof($unidades)-2;
            $cant_columnas= sizeof($listas);
            $unidades[$p]['cant_votantes'] = 0;
            //Inicializo para realizar la sumatoria
            $cant_total = 0;
            for($i=0; $i<$p; $i++){ //Recorre las unidades
                $votantes_ue = 0;
                for($c=0; $c<$cant_columnas; $c++){
                    $votantes_ue += $unidades[$i][($listas[$c]['id_nro_lista'])] ;  
               }
               $votantes_ue += $unidades[$i]['total_votos_blancos'];
               $votantes_ue += $unidades[$i]['total_votos_nulos'];
               $votantes_ue += $unidades[$i]['total_votos_recurridos'];
                $unidades[$i]['cant_votantes'] = $votantes_ue; 
                $cant_total += $votantes_ue;
            }
            $unidades[$p]['cant_votantes'] = $cant_total;
            return $unidades;
        }
        
        
        function cargar_votos_totales_ponderados($id_lista, $unidades, $id_claustro){
            $pos_total = sizeof($unidades) -2;//Fila que contiene los votos totales
            $pos_pond = sizeof($unidades)-1;//Fila que contiene los votos ponderados
            
            //Recorro las unidades exluyendo las dos últimas filas que tiene los votos totales y ponderados
            for($i=0; $i<$pos_total; $i++){
                if(isset($unidades[$i][$id_lista]) && isset($unidades[$i]['cant_empadronados'])){
                    //Suma el cociente entre cant de votos de la 
                    //lista en la UEn / cant empadronados del claustro en la UEn
                    $cociente = $unidades[$i][$id_lista]/$unidades[$i]['cant_empadronados'];
                    $unidades[$pos_pond][$id_lista] += $cociente;
                }
                
                if(isset($unidades[$i][$id_lista])){
                    //Suma los votos 
                    $unidades[$pos_total][$id_lista] += $unidades[$i][$id_lista];        
                }
            }
            $unidades[$pos_pond][$id_lista] = round($unidades[$pos_pond][$id_lista],6);

            
            //$aux = "<span style='color:red'>".$unidades[$pos_total][$id_lista].""."</span>";
            //$unidades[$pos_total][$id_lista] = $aux;
            
            //print_r($unidades);
            //print_r($pos_total);
            return $unidades;
        }

        /*function cambiar_estilo_total($unidades){ 
//deberia ingresar las listas para recorrerlas FALTA HACER!!!
            $pos_total = sizeof($unidades) -2;
            $artotal= $unidades[$pos_total];
            $cant = sizeof($artotal);
            //print_r("cant:".$cant." Artotal: ");
            //print_r($artotal);
            //print_r("\n");
            $aux = "<span style='color:red'>".$artotal['cant_empadronados']."</span>";
            //print_r($aux);
            $artotal['cant_empadronados']= $aux;
        }
         * 
         */
        
	function evt__cuadro_superior_e__seleccion($datos)
	{
		
	}
 
        /*function truncate($val, $f="0")
        {
               if(($p = strpos($val, '.')) !== false) {
                    $val = floatval(substr($val, 0, $p + 1 + $f));
                }
            return $val;
        }*/
        
	//-----------------------------------------------------------------------------------
	//---- cuadro_superior_g ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_superior_g(gu_kena_ei_cuadro $cuadro)
	{
            /*$this->dep('cuadro_superior_g')->colapsar();//No se muestra el cuadro en un principio
            
            //Obtengo todas las unidades menos los asentamientos y rectorado. 
            //1:Administración central, 2:Facultad/Centro Regional/ Escuela 3:Asentamiento
            $unidades = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones_por_nivel(array(2)); 
            
            //Cargar la cantidad de empadronados para el claustro graduados=4 en cada unidad
            $ar = $this->cargar_cant_empadronados($unidades, 4);
            
            //Ante ultima fila carga los votos totales de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] ="<span style='color:blue;font-weight:bold'>TOTAL</span>";
            $ar[$pos]['cant_empadronados'] = $this->s__total_emp;
            
            //Ultima fila carga los votos ponderados de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] = "<span style='color:red;font-weight:bold'>PONDERADOS</span>";
                          
            //Obtener las listas del claustro graduados=4
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(4); 
            
            //Agregar las etiquetas de todas las listas
            $i = 1;
            foreach($listas as $lista){
                $l['clave'] = $lista['id_nro_lista'];
                $l['titulo'] = substr($lista['nombre'], 0, 11). $lista['sigla'];
                $l['estilo'] = 'col-cuadro-resultados';
                $l['estilo_titulo'] = 'tit-cuadro-resultados';
                //$l['permitir_html'] = true;
                
                $grupo[$i] = $lista['id_nro_lista'];
                
                $columnas[$i] = $l;
                $this->dep('cuadro_superior_g')->agregar_columnas($columnas);
                
                //Cargar la cantidad de votos para cada lista de claustro graduados=4 en cada unidad
                $ar = $this->cargar_cant_votos($lista['id_nro_lista'], $ar, 4);
                
                //Cargar los votos totales/ponderados para cada lista agregado como ante/última fila
                //para claustro graduados=4
                $ar[$pos-1][$lista['id_nro_lista']] = 0;
                $ar[$pos][$lista['id_nro_lista']] = 0;
                $ar = $this->cargar_votos_totales_ponderados($lista['id_nro_lista'], $ar, 4);
                
                $i++;
            }
            
            if(isset($grupo))
                $this->dep('cuadro_superior_g')->set_grupo_columnas('Listas',$grupo);
              
            $this->s__votos_g = $ar;//Guardar los votos para el calculo dhondt
            
            //Agregar datos totales de blancos, nulos y recurridos
            $b['clave'] = 'total_votos_blancos';
            $b['titulo'] = 'Blancos';
            $b['estilo'] = 'col-cuadro-resultados';
            $b['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[0] = $b;
            
            $n['clave'] = 'total_votos_nulos';
            $n['titulo'] = 'Nulos';
            $n['estilo'] = 'col-cuadro-resultados';
            $n['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[1] = $n;
            
            $r['clave'] = 'total_votos_recurridos';
            $r['titulo'] = 'Recurridos';
            $r['estilo'] = 'col-cuadro-resultados';
            $r['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[2] = $r;
            
            $this->dep('cuadro_superior_g')->agregar_columnas($bnr);
            
            $p = sizeof($ar)-2;
            //Inicializo para realizar la sumatoria
            $ar[$p]['total_votos_blancos'] = 0;
            $ar[$p]['total_votos_nulos'] = 0;
            $ar[$p]['total_votos_recurridos'] = 0;
            
            $ar = $this->cargar_cant_b_n_r($ar, 4);
            $ar= $this->cargar_cant_votantes($ar,$listas);
            
            return $ar;*/
        }

	function evt__cuadro_superior_g__seleccion($seleccion)
	{
	}
        
	//-----------------------------------------------------------------------------------
	//---- cuadro_superior_nd -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_superior_nd(gu_kena_ei_cuadro $cuadro)
	{
            /*$this->dep('cuadro_superior_nd')->colapsar();//No se muestra el cuadro en un principio
            
            //Obtengo todas las unidades menos los asentamientos. 
            //1:Administración central, 2:Facultad/Centro Regional/ Escuela 3:Asentamiento
            $unidades = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones_por_nivel(array(1,2)); 
            
            //Cargar la cantidad de empadronados para el claustro no docente = 1 en cada unidad
            $ar = $this->cargar_cant_empadronados($unidades, 1);
            
            //Ante ultima fila carga los votos totales de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] ="<span style='color:blue;font-weight:bold '>TOTAL</span>";
            $ar[$pos]['cant_empadronados'] = $this->s__total_emp;
            
            //Ultima fila carga los votos ponderados de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] = "<span style='color:red;font-weight:bold '>PONDERADOS</span>";
                       
            //Obtener las listas del claustro no docente = 1
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(1); 
            
            //Agregar las etiquetas de todas las listas
            $i = 1;
            foreach($listas as $lista){
                $l['clave'] = $lista['id_nro_lista'];
                $l['titulo'] = substr($lista['nombre'], 0, 11). $lista['sigla'];
                $l['estilo'] = 'col-cuadro-resultados';
                $l['estilo_titulo'] = 'tit-cuadro-resultados';
                //$l['permitir_html'] = true;
                
                $grupo[$i] = $lista['id_nro_lista'];
                
                $columnas[$i] = $l;
                $this->dep('cuadro_superior_nd')->agregar_columnas($columnas);
                
                //Cargar la cantidad de votos para cada lista de claustro no docente = 1 en cada unidad
                $ar = $this->cargar_cant_votos($lista['id_nro_lista'], $ar, 1);
                
                //Cargar los votos totales/ponderados para cada lista agregado como ante/última fila
                //para claustro no docente = 1
                $ar[$pos-1][$lista['id_nro_lista']] = 0;
                $ar[$pos][$lista['id_nro_lista']] = 0;
                $ar = $this->cargar_votos_totales_ponderados($lista['id_nro_lista'], $ar, 1);
                
                $i++;
            }
            if(isset($grupo))
                $this->dep('cuadro_superior_nd')->set_grupo_columnas('Listas',$grupo);
              
            $this->s__votos_nd = $ar;//Guardar los votos para el calculo dhondt
            
            //Agregar datos totales de blancos, nulos y recurridos
            $b['clave'] = 'total_votos_blancos';
            $b['titulo'] = 'Blancos';
            $b['estilo_titulo'] = 'tit-cuadro-resultados';
            $b['estilo'] = 'col-cuadro-resultados';
            $bnr[0] = $b;
            
            $n['clave'] = 'total_votos_nulos';
            $n['titulo'] = 'Nulos';
            $n['estilo'] = 'col-cuadro-resultados';
            $n['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[1] = $n;
            
            $r['clave'] = 'total_votos_recurridos';
            $r['titulo'] = 'Recurridos';
            $r['estilo'] = 'col-cuadro-resultados';
            $r['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[2] = $r;
            
            $this->dep('cuadro_superior_nd')->agregar_columnas($bnr);
            
            $p = sizeof($ar)-2;
            //Inicializo para realizar la sumatoria
            $ar[$p]['total_votos_blancos'] = 0;
            $ar[$p]['total_votos_nulos'] = 0;
            $ar[$p]['total_votos_recurridos'] = 0;
            
            $ar = $this->cargar_cant_b_n_r($ar, 1);
            $ar= $this->cargar_cant_votantes($ar,$listas);
            
            return $ar;*/
	}

	function evt__cuadro_superior_nd__seleccion($seleccion)
	{
	}
	
        //-----------------------------------------------------------------------------------
	//---- cuadro_superior_d ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_superior_d(gu_kena_ei_cuadro $cuadro)
	{
            /*$this->dep('cuadro_superior_d')->colapsar();//No se muestra el cuadro en un principio
            //$cuadro->set_datos($this->dep('datos')->tabla('unidad_electoral')->get_descripciones());
         
            
            //Obtengo todas las unidades menos los asentamientos y rectorado. 
            //1:Administración central, 2:Facultad/Centro Regional/ Escuela 3:Asentamiento
            $unidades = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones_por_nivel(array(2)); 
            
            //Cargar la cantidad de empadronados para el claustro docente = 2 en cada unidad
            $ar = $this->cargar_cant_empadronados($unidades, 2);
            
            //Ante ultima fila carga los votos totales de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] ="<span style='color:blue;font-weight:bold '>TOTAL</span>";
            $ar[$pos]['cant_empadronados'] = $this->s__total_emp;
            
            //Ultima fila carga los votos ponderados de cada lista
            $pos = sizeof($ar);
            $ar[$pos]['sigla'] = "<span style='color:red;font-weight:bold '>PONDERADOS</span>";
                       
            //Obtener las listas del claustro docente = 2
            $listas = $this->dep('datos')->tabla('lista_csuperior')->get_listas_actuales(2); 
            
            //Agregar las etiquetas de todas las listas
            $i = 1;
            foreach($listas as $lista){
                $l['clave'] = $lista['id_nro_lista'];
                $l['titulo'] = substr($lista['nombre'], 0, 11). $lista['sigla'];
                $l['estilo'] = 'col-cuadro-resultados';
                $l['estilo_titulo'] = 'tit-cuadro-resultados';
                //$l['permitir_html'] = true;
                
                $grupo[$i] = $lista['id_nro_lista'];
                
                $columnas[$i] = $l;
                $this->dep('cuadro_superior_d')->agregar_columnas($columnas);
                
                //Cargar la cantidad de votos para cada lista de claustro docente = 2 en cada unidad
                $ar = $this->cargar_cant_votos($lista['id_nro_lista'], $ar, 2);
                
                //Cargar los votos totales/ponderados para cada lista agregado como ante/última fila
                //para claustro docente = 2
                $ar[$pos-1][$lista['id_nro_lista']] = 0;
                $ar[$pos][$lista['id_nro_lista']] = 0;
                $ar = $this->cargar_votos_totales_ponderados($lista['id_nro_lista'], $ar, 2);
                
                $i++;
            }
            if(isset($grupo))
                $this->dep('cuadro_superior_d')->set_grupo_columnas('Listas',$grupo);
              
            $this->s__votos_d = $ar;//Guardar los votos para el calculo dhondt
            
            //Agregar datos totales de blancos, nulos y recurridos
            $b['clave'] = 'total_votos_blancos';
            $b['titulo'] = 'Blancos';
            $b['estilo_titulo'] = 'tit-cuadro-resultados';
            $b['estilo'] = 'col-cuadro-resultados';
            $bnr[0] = $b;
            
            $n['clave'] = 'total_votos_nulos';
            $n['titulo'] = 'Nulos';
            $n['estilo'] = 'col-cuadro-resultados';
            $n['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[1] = $n;
            
            $r['clave'] = 'total_votos_recurridos';
            $r['titulo'] = 'Recurridos';
            $r['estilo'] = 'col-cuadro-resultados';
            $r['estilo_titulo'] = 'tit-cuadro-resultados';
            $bnr[2] = $r;
            
            $this->dep('cuadro_superior_d')->agregar_columnas($bnr);
            
            $p = sizeof($ar)-2;
            //Inicializo para realizar la sumatoria
            $ar[$p]['total_votos_blancos'] = 0;
            $ar[$p]['total_votos_nulos'] = 0;
            $ar[$p]['total_votos_recurridos'] = 0;
            
            $ar = $this->cargar_cant_b_n_r($ar, 2);
            $ar= $this->cargar_cant_votantes($ar,$listas);
            
            return $ar;*/
	}

	function evt__cuadro_superior_d__seleccion($seleccion)
	{
	}

        //-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
            //$this->pantalla()->tab('pant_docente')->ocultar();
            $this->generar_json('2016-05-17');
	}
        
        function generar_json($fecha){
            $columns = [0 => ['field' => 'lista', 'title' => 'Listas'],
                        1 => ['field' => 'sede1', 'title' => 'Sede 1'],
                        2 => ['field' => 'total', 'title' => 'Total'],
                        3 => ['field' => 'ponderados', 'title' => 'Ponderados'],
                        ];
            
            
            $this->consulta($fecha);
            
        }
        
        function consulta($fecha){
            //Obtener todas las categorias (rector, decano,...)
            $categorias = $this->dep('datos')->tabla('tipo')->get_descripciones();
            
            foreach($categorias as $una_categoria){
                if($una_categoria['id_tipo'] == 1){//=RECTOR - ESTE IF NO IRIA
                    $ue = $this->dep('datos')->tabla('unidad_electoral')->get_descripciones();
                    switch($una_categoria['id_tipo']){
                        case 1: $nom_cat = 'CS'; break;
                        case 2: $nom_cat = 'CD'; break;
                        case 3: $nom_cat = 'CD'; break;
                        case 4: $nom_cat = 'R'; break;
                        case 5: $nom_cat = 'D'; break;
                        case 6: $nom_cat = 'D'; break;
                        default: $nom_cat = ''; break;
                    }
                    
                    foreach($ue as $una_ue){
                        if($una_ue['id_nro_ue'] == 12){//=FAIF - ESTE IF NO IRIA
                            
                            $claustros = $this->dep('datos')->tabla('claustro')->get_descripciones();
                            $nom_ue = strtoupper($una_ue['sigla']);    
                            foreach($claustros as $un_claustro){//RECORRE CLAUSTROS
                                $nom_archivo = $nom_cat.'_'.$nom_ue.'_'.strtoupper($un_claustro['descripcion'][0]);
                                
                                $filtro['id_claustro'] = $un_claustro['id'];
                                $filtro['id_tipo'] = $una_categoria['id_tipo'];
                                $filtro['id_ue'] = $una_ue['id_nro_ue'];
                                $this->cuadro_a_json($nom_archivo, $filtro, $fecha);
                            }
                          
                        }
                    }
                  
                }
            }
            
        }
        
        function cuadro_a_json($nom_archivo, $filtro, $fecha){
            switch ($filtro['id_tipo']){
                case 1: $tabla_voto = 'voto_lista_csuperior'; 
                        $tabla_lista = 'lista_csuperior'; break;
                case 2: $tabla_voto = 'voto_lista_cdirectivo'; 
                        $tabla_lista = 'lista_cdirectivo'; break;
                case 3: $tabla_voto = 'voto_lista_cdirectivo'; 
                        $tabla_lista = 'lista_cdirectivo'; break;
                case 4: $tabla_voto = 'voto_lista_rector'; 
                        $tabla_lista = 'lista_rector'; break;
                case 5: $tabla_voto = 'voto_lista_decano'; 
                        $tabla_lista = 'lista_decano'; break;
                case 6: $tabla_voto = 'voto_lista_decano'; 
                        $tabla_lista = 'lista_decano'; break;
            }
            $sql = "select l.id_nro_lista, "
                    . "l.nombre as lista, "
                    . "l.sigla as sigla_lista, "
                    . "vl.cant_votos as total, "
                    . "a.id_acta, "
                    . "s.id_sede, "
                    . "s.nombre as sede, "
                    . "s.sigla as sigla_sede "
                    . "from acta a "
                    . "inner join mesa m on m.id_mesa = a.de "
                    . "inner join sede s on s.id_sede = a.id_sede "
                    . "inner join $tabla_voto vl on vl.id_acta = a.id_acta "
                    . "inner join $tabla_lista l on l.id_nro_lista = vl.id_lista "
                    . "where m.fecha = '$fecha' "
                    . " and a.id_tipo= ".$filtro['id_tipo']
                    . " and s.id_ue = ".$filtro['id_ue']
                    . " and m.id_claustro = ".$filtro['id_claustro'];
            $res = toba::db('gu_kena')->consultar($sql);
            
            $bnr = $this->dep('datos')->tabla('acta')->cant_bnr_acta($filtro, $fecha);
            //print_r($bnr);
            //ARMAR JSONSSSS!!!!!!!!!!!!!!!!!
            foreach($res as $una_lista){
                $un_registro['lista'] = utf8_encode($una_lista['lista']);
                $un_registro['sede1'] = $una_lista['total'];
                $un_registro['total'] = $una_lista['total'];
                $un_registro['ponderados'] = 0;
                
                $json['data'][] = $un_registro;
            }
            
            $un_registro = array();
            //foreach($bnr as $un_bnr){
                $un_registro['lista'] = 'blancos';
                $un_registro['sede1'] = $bnr[0]['blancos'];
                $un_registro['total'] = $bnr[0]['blancos'];
                $un_registro['ponderados'] = 0;
                
                $json['data'][] = $un_registro;
                $un_registro = array();
                $un_registro['lista'] = 'nulos';
                $un_registro['sede1'] = $bnr[0]['nulos'];
                $un_registro['total'] = $bnr[0]['nulos'];
                $un_registro['ponderados'] = 0;
                
                $json['data'][] = $un_registro;
                $un_registro = array();
                $un_registro['lista'] = 'recurridos';
                $un_registro['sede1'] = $bnr[0]['recurridos'];
                $un_registro['total'] = $bnr[0]['recurridos'];
                $un_registro['ponderados'] = 0;
                
                $json['data'][] = $un_registro;
            //}
            
            $string_json = json_encode($json);
            
            file_put_contents('resultados_json/'.$nom_archivo.'.json', $string_json);
            print_r($nom_archivo);print_r($json);print_r($string_json);
            
        }

        //-----------------------------------------------------------------------------------
	//---- formulario que muestra datos de mesas enviadas, confirmadas y definitivas -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	//---- form_mesas_e -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_mesas_e(gu_kena_ei_formulario $form)
	{
            /*$cargadas = $this->dep('datos')->tabla('mesa')->get_cant_cargadas(3);
            $confirmadas = $this->dep('datos')->tabla('mesa')->get_cant_confirmadas(3);
            $definitivas = $this->dep('datos')->tabla('mesa')->get_cant_definitivas(3);
            
            $total = $this->dep('datos')->tabla('mesa')->get_total_mesas(3);
            if ($total != 0){
                $datos['cargadas'] = ($cargadas * 100 / $total);
                $datos['cargadas'] = round($datos['cargadas'], 2). " % ($cargadas de $total)";
                $datos['confirmadas'] = ($confirmadas * 100 / $total);
                $datos['confirmadas'] = round($datos['confirmadas'],2). " % ($confirmadas de $total)";
                $datos['definitivas'] = ($definitivas * 100 / $total);
                $datos['definitivas'] = round($datos['definitivas'],2). " % ($definitivas de $total)";
            }
            else {
                $datos['cargadas'] = $cargadas . " % ($cargadas de $total)";
                $datos['confirmadas'] = $confirmadas . " % ($confirmadas de $total)";
                $datos['definitivas'] = $definitivas . " % ($definitivas de $total)";
             }
            return $datos;*/
	}

	//-----------------------------------------------------------------------------------
	//---- form_mesas_g -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_mesas_g(gu_kena_ei_formulario $form)
	{
            /*$cargadas = $this->dep('datos')->tabla('mesa')->get_cant_cargadas(4);
            $confirmadas = $this->dep('datos')->tabla('mesa')->get_cant_confirmadas(4);
            $definitivas = $this->dep('datos')->tabla('mesa')->get_cant_definitivas(4);
            
            $total = $this->dep('datos')->tabla('mesa')->get_total_mesas(4);
            if ($total != 0) {
                    $datos['cargadas'] = ($cargadas * 100 / $total);
                    $datos['cargadas'] = round($datos['cargadas'], 2) . " % ($cargadas de $total)";
                    $datos['confirmadas'] = ($confirmadas * 100 / $total);
                    $datos['confirmadas'] = round($datos['confirmadas'], 2) . " % ($confirmadas de $total)";
                    $datos['definitivas'] = ($definitivas * 100 / $total);
                    $datos['definitivas'] = round($datos['definitivas'], 2) . " % ($definitivas de $total)";
            }
            else{
                    $datos['cargadas'] = $cargadas . " % ($cargadas de $total)";
                    $datos['confirmadas'] = $confirmadas . " % ($confirmadas de $total)";
                    $datos['definitivas'] = $definitivas .  " % ($definitivas de $total)";
            }
            
        return $datos;*/
	}

	//-----------------------------------------------------------------------------------
	//---- form_mesas_nd ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_mesas_nd(gu_kena_ei_formulario $form)
	{
            /*$cargadas = $this->dep('datos')->tabla('mesa')->get_cant_cargadas(1);
            $confirmadas = $this->dep('datos')->tabla('mesa')->get_cant_confirmadas(1);
            $definitivas = $this->dep('datos')->tabla('mesa')->get_cant_definitivas(1);
            
            $total = $this->dep('datos')->tabla('mesa')->get_total_mesas(1);
            
            if ($total != 0) {
                $datos['cargadas'] = ($cargadas * 100 / $total);
                $datos['cargadas'] = round($datos['cargadas'], 2) . " % ($cargadas de $total)";
                $datos['confirmadas'] = ($confirmadas * 100 / $total);
                $datos['confirmadas'] = round($datos['confirmadas'], 2) . " % ($confirmadas de $total)";
                $datos['definitivas'] = ($definitivas * 100 / $total);
                $datos['definitivas'] = round($datos['definitivas'], 2) . " % ($definitivas de $total)";
            } 
            else {
                $datos['cargadas'] = $cargadas . " % ($cargadas de $total)";
                $datos['confirmadas'] = $confirmadas . " % ($confirmadas de $total)";
                $datos['definitivas'] = $definitivas . " % ($definitivas de $total)";
             }
        return $datos;*/
	}
        
        //-----------------------------------------------------------------------------------
	//---- form_mesas_d ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_mesas_d(gu_kena_ei_formulario $form)
	{
            /*$cargadas = $this->dep('datos')->tabla('mesa')->get_cant_cargadas(2);
            $confirmadas = $this->dep('datos')->tabla('mesa')->get_cant_confirmadas(2);
            $definitivas = $this->dep('datos')->tabla('mesa')->get_cant_definitivas(2);
            $total = $this->dep('datos')->tabla('mesa')->get_total_mesas(2);
            
            if ($total != 0) {
                $datos['cargadas'] = ($cargadas * 100 / $total);
                $datos['cargadas'] = round($datos['cargadas'], 2) . " % ($cargadas de $total)";
                $datos['confirmadas'] = ($confirmadas * 100 / $total);
                $datos['confirmadas'] = round($datos['confirmadas'], 2) . " % ($confirmadas de $total)";
                $datos['definitivas'] = ($definitivas * 100 / $total);
                $datos['definitivas'] = round($datos['definitivas'], 2) . " % ($definitivas de $total)";
            } 
            else {
                $datos['cargadas'] = $cargadas . " % ($cargadas de $total)";
                $datos['confirmadas'] = $confirmadas . " % ($confirmadas de $total)";
                $datos['definitivas'] = $definitivas . " % ($definitivas de $total)";
             }
        return $datos;*/
	}
        
        
        //-----------------------------------------------------------------------------------
	//---- EXPORTACION EXCEL ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function vista_excel(toba_vista_excel $salida){
            $salida->set_nombre_archivo("EscrutinioSuperior.xls");
            $excel = $salida->get_excel();
            
            
            $this->dependencia('cuadro_superior_e')->vista_excel($salida);
            $salida->separacion(3);
            $this->dependencia('cuadro_dhondt_e')->vista_excel($salida);
            $salida->set_hoja_nombre("Estudiantes");
            
            $salida->crear_hoja();
            $this->dependencia('cuadro_superior_g')->vista_excel($salida);
            $salida->separacion(3);
            $this->dependencia('cuadro_dhondt_g')->vista_excel($salida);
            $salida->set_hoja_nombre("Graduados");
            
            $salida->crear_hoja();
            $this->dependencia('cuadro_superior_nd')->vista_excel($salida);
            $salida->separacion(3);
            $this->dependencia('cuadro_dhondt_nd')->vista_excel($salida);
            $salida->set_hoja_nombre("No Docente");
//            $excel->getActiveSheet()->setTitle('Parte de Novedades');
//            $excel->getActiveSheet()->getStyle('A5')->getFill()->applyFromArray(array(
//        'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array( 'rgb' => 'F28A8C' ) ));
        }
}
?>