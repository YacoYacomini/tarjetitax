<?php

namespace Poli\tarjetitax

class Boleto {
		protected $viaje,$trasbordo,$tarjeta;
		public function __construct( Tarjeta $tarjeta, Viaje $viaje){
			$this->viaje = $viaje;
			$this->tarjeta = $tarjeta;
		}
		public function getBoleto(){
			return "SEMTUR - ".$this->viaje->getTransporte()->getNombreEmpresa()."\n".
			$this->viaje->getHorario()." Linea:".$this->viaje->getTransporte()->getId()."\n".$this->tarjeta->getTipo()." $".$this->viaje->getCosto()."\nSaldo: $".$this->tarjeta->saldo();
		}
}

?>