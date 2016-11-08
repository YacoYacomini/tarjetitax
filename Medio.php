<?php

namespace Poli\tarjetitax;



class Medio extends Tarjeta{
	public function __construct (){
		$this->saldo = 0;
		$this->porcentaje = 0.5;
		$this->medio = True;
	}
}


?>
