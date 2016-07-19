<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Utility\Text;
use Cake\ORM\TableRegistry;

class QueryComponent extends Component
{

	private $condition;

	public function makeQuery() {
	if(!empty($this->condition)){

	//debug($this->condition);
	$images = TableRegistry::get('Images');
	$query = $images->find();
	//$query->select(['id', 'photo' '']);
	$query->where($this->condition); // Return the same query object
	$query->order(['id' => 'DESC']); // Still same object, no SQL executed

	/*foreach ($query as $image) {
	    debug($image->photo);
	}*/
	$vysledok = $query->toArray();
	return $vysledok;
	}
	}

	public function parseAndValidate($data) {

	//pomocne premenne
	$vyskyt_zatvoriek_prvych = 0;
	$vyskyt_zatvoriek_druchych = 0;
	$counter = 0;
	$position = 0;
	$text = "";
	$text2 = "";
	$value2= 0;
	$pomocne_pocitadlo = 0;
	
	//vysledne pole - inicializacia	
	$after_parsing = array();
	$arr_medzi_vysledok = array();
	
	//pomocna pre urcenie pozicie operatorov v poli po tokenizovani textarea vstupu
	$stack_positions_operatorov = array();
	$stack_positions_operatorov_v_zatvorke = array();
	$stack_positions_pouzitich_zatvoriek = array();
	$stack_positions_podmienky = array();
	
	//povolene polia, operatory a podmienky....
	$arr_polia = array('Šírka', 'Výška', 'photo');
	$arr_povolene_operatory = array('Šírka' => array('=', '<'), 'Výška' => array('=', '<', '>'), 'photo' => array('=','CONTAINS'));
	$arr_operatory = array('=', '<', '>','CONTAINS');
	$arr_podmienky = array('AND', 'OR');
	
	//pomocne polia
	$arr_zatvorky = array('(', ')');
	$arr_specialne_znaky = array('(', ')', "\'", "\"");
	
	//tokenizacia textarealu
	$result = Text::tokenize($data, ' ', "\"", "\"");
	
	//najdenie pozicie operatorov a podmienok a zatvoriek
	$stack_positions_operatorov_v_zatvorke['prva'] = array();
	$stack_positions_operatorov_v_zatvorke['druha'] = array();
        
	foreach ($result as $key => $value){
	  if(in_array($value, $arr_podmienky)){
             array_push($stack_positions_podmienky, $key);
	  } else if(in_array($value, $arr_operatory)){
	     array_push($stack_positions_operatorov, $key);
	  //ratanie poctu zatvoriek a evedovanie zatvoriek
	  }else if(strpos($value, $arr_zatvorky[0]) !== false){
		$stack_positions_operatorov_v_zatvorke['prva'][] = $key;
		$vyskyt_zatvoriek_prvych++;
	  }else if(strpos($value, $arr_zatvorky[1]) !== false){
		$vyskyt_zatvoriek_druchych++;
		$stack_positions_operatorov_v_zatvorke['druha'][] = $key;
	}
	}
	
	//overenie zatvoriek	
	if($vyskyt_zatvoriek_prvych != $vyskyt_zatvoriek_druchych){
           exit("Error: zle urcene zatvorky");
	}

	//overenie AND a OR operatorov oproti operatorov pomienkovych - vyskyt
	if(count($stack_positions_podmienky)+1 != count($stack_positions_operatorov)){
	   exit("Error: AND a OR operatorov oproti operatorov pomienkovych");
	}

	//tokenizacia
	$result = str_replace($arr_specialne_znaky, ['','','',''], $result);

	//parsovanie podmienok (tvorenie) + testovanie poli a podporovanych operatorov
	foreach ($stack_positions_operatorov as $key => $value){

	//inicializacia premennych
	$text = "";
	$text2 = "";
	$testovane_pole = "";

	//skladanie poli
	if(0 == $key){
	for($j=0;$j<$value;$j++){
		//testovanie podporovanych poli
		if(!in_array($result[$j], $arr_polia)){
			exit("Error: nepodporovane pole " . $result[$j]);		
		}
		$testovane_pole = $result[$j];
		$text .= $result[$j] . " ";
	}
	} else{
	for($j=$stack_positions_podmienky[$key-1]+1;$j<$value;$j++){
		//testovanie podporovanych poli		
		if(!in_array($result[$j], $arr_polia)){
			exit("Error: nepodporovane pole "  . $result[$j]);		
		}
		$testovane_pole = $result[$j];
		$text .= $result[$j] . " ";
	}
	}

	//testovanie vhodnoti operatorov pre dane polia
	if(!in_array($result[$value], $arr_povolene_operatory[$testovane_pole])){
			exit("Error: novhodny operator " . $result[$value] . " pre pole: " . $testovane_pole);		
	}
	
	//premena CONTAINS na LIKE v poli
	if(strcmp("CONTAINS",$result[$value]) == 0){
	$text .= 'LIKE';
	$text2 .= '%';
	} else if(strcmp("=",$result[$value]) == 0){
	$text = substr_replace($text, "", -1);
	} else{
	$text .= $result[$value];
	}
	
	//skladanie podmienky
	if(count($stack_positions_podmienky) > $key){
	for($i=$value+1;$i<$stack_positions_podmienky[$key];$i++){
		$text2 .= $result[$i] . " ";
	}  
	} else{
	for($i=$value+1;$i<count($result);$i++){
		$text2 .= $result[$i] . " ";
	} 
	}
	$text2 = substr_replace($text2, "", -1);

	if(strcmp("CONTAINS",$result[$value]) == 0){
	$text2 .= '%';
	}
	
	//testovanie ci je podmienka v spravnom formate
	if(strcmp("",$text) == 0 || strcmp("",$text2) == 0 || strcmp("",$result[$value]) == 0){
		exit("ERRO: zly format: podmienka: pole, operator a hodnota podmienky");
	}
	//pridanie podmienky
	$arr_medzi_vysledok[] = array($text, $text2);
	}

	$stack_pouzite = array();

	for($j=count($stack_positions_operatorov_v_zatvorke['prva'])-1;$j>=0;$j--){
		$prva = $stack_positions_operatorov_v_zatvorke['prva'][$j];
		$druha = $stack_positions_operatorov_v_zatvorke['druha'][$j];
		foreach ($stack_positions_podmienky as $key => $value){
			if($prva < $value &&  $druha > $value){
				$stack_positions_pouzitich_zatvoriek[] = $value;
		if(strcmp("OR",$result[$value]) == 0){	
		 if(!array_key_exists('OR', $after_parsing)){
			$after_parsing['OR'] = array();
		 }
			$after_parsing['OR'][$arr_medzi_vysledok[$key][0]] = $arr_medzi_vysledok[$key][1];
			$after_parsing['OR'][$arr_medzi_vysledok[$key+1][0]] = $arr_medzi_vysledok[$key+1][1];
			$stack_pouzite[] = $key;
			$stack_pouzite[] = $key+1;
	  	 } else{ 
			
			$after_parsing[$arr_medzi_vysledok[$key][0]] = $arr_medzi_vysledok[$key][1];
			$after_parsing[$arr_medzi_vysledok[$key+1][0]] = $arr_medzi_vysledok[$key+1][1];
			$stack_pouzite[] = $key;
			$stack_pouzite[] = $key+1;
		 }
		}
	       }
	      }

	//vytvorenie konecneho polia urceneho pre objekt query
	foreach ($stack_positions_podmienky as $key => $value){
	  if(!in_array($value, $stack_positions_pouzitich_zatvoriek)){
		if(strcmp("OR",$result[$value]) == 0){	
			if(!array_key_exists('OR', $after_parsing)){
			  $after_parsing['OR'] = array();
			}
			if(!in_array($pomocne_pocitadlo, $stack_pouzite)){
			  $after_parsing['OR'][$arr_medzi_vysledok[$pomocne_pocitadlo][0]] = $arr_medzi_vysledok[$pomocne_pocitadlo][1];
			  $stack_pouzite[] = $pomocne_pocitadlo;
			}
			$pomocne_pocitadlo++;
			if(!in_array($pomocne_pocitadlo, $stack_pouzite)){
			  $after_parsing['OR'][$arr_medzi_vysledok[$pomocne_pocitadlo][0]] = $arr_medzi_vysledok[$pomocne_pocitadlo][1];
			  $stack_pouzite[] = $pomocne_pocitadlo;
			}
	  	} else{ 
		     if(count($stack_positions_podmienky) > $key+1){
			
			if(strcmp("AND",$stack_positions_podmienky[$key+1]) == 0){
			   if(!in_array($pomocne_pocitadlo, $stack_pouzite)){
			        $after_parsing[$arr_medzi_vysledok[$pomocne_pocitadlo][0]] = $arr_medzi_vysledok[$pomocne_pocitadlo][1];
				$stack_pouzite[] = $pomocne_pocitadlo;
			   }
			   $pomocne_pocitadlo++;	
			   if(!in_array($pomocne_pocitadlo, $stack_pouzite)){
				$after_parsing[$arr_medzi_vysledok[$pomocne_pocitadlo][0]] = $arr_medzi_vysledok[$pomocne_pocitadlo][1];
				$stack_pouzite[] = $pomocne_pocitadlo;
			   }
			} else{
	
			  if(!in_array($pomocne_pocitadlo, $stack_pouzite)){
			    $after_parsing[$arr_medzi_vysledok[$pomocne_pocitadlo][0]] = $arr_medzi_vysledok[$pomocne_pocitadlo][1];
			    $stack_pouzite[] = $pomocne_pocitadlo;
			  }
			  $pomocne_pocitadlo++;
			  }
			} else{
			if(!in_array($pomocne_pocitadlo, $stack_pouzite)){
			        $after_parsing[$arr_medzi_vysledok[$pomocne_pocitadlo][0]] = $arr_medzi_vysledok[$pomocne_pocitadlo][1];
				$stack_pouzite[] = $pomocne_pocitadlo;
			   }
			   $pomocne_pocitadlo++;	
			   if(!in_array($pomocne_pocitadlo, $stack_pouzite)){
				$after_parsing[$arr_medzi_vysledok[$pomocne_pocitadlo][0]] = $arr_medzi_vysledok[$pomocne_pocitadlo][1];
				$stack_pouzite[] = $pomocne_pocitadlo;
			   }
			}
		}
	 }
	}
	

	//debug($after_parsing);		
	$this->condition = $after_parsing;

	}
}
