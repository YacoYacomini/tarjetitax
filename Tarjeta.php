<?php

namespace Poli\tarjetitax;


class Tarjeta implements Int_Tarjeta{
	protected $saldo,$porcentaje,$plus=0,$valorPlus=0, $transbordo = False, $medio;
	protected $viajes,$boleto,$ultimafecha=0,$ultimabicipaga=0;
	public function __construct (){
		$this->saldo = 0;
		$this->porcentaje = 1;
		$this->medio = False;
	}
	public function pagar(Transporte $transporte, $fecha_y_hora){
		if($transporte->getTipo()==1){ 
			$aux1 = strtotime($fecha_y_hora);
			$aux2 = strtotime($this->ultimafecha);
			if($this->ultimafecha == 0 || ($aux1-$aux2>3600) || $this->viajes[$this->ultimafecha]->getTransporte()->getId() == $transporte->getid()){ 
				$costo = $transporte->getCosto()*$this->porcentaje;
				$this->transbordo = False;
			} else {
				$costo = $transporte->getCostoTrans()*$this->porcentaje;
				$this->transbordo = True;
			}
			if($costo+$this->valorPlus <= $this->saldo && $this->plus>0){
				$this->saldo -= $this->valorPlus;
				$this->plus = 0;
				$this->valorPlus = 0;
			}
			if($costo<=$this->saldo || $this->plus<2){
				if($costo>$this->saldo && $this->plus<2){
					$this->plus++;
					$this->valorPlus += $costo;
				}
				else{
					$this->saldo -= $costo;
				}

				$this->viajes[$fecha_y_hora] = new Viaje($fecha_y_hora,$transporte,$costo);
				$this->boleto = new Boleto ($this,$this->viajes[$fecha_y_hora]);
				$this->ultimafecha = $fecha_y_hora;
				return 1;
			} 
			else{
				return 0;
			}
		} 
		else{ 
			$aux1 = strtotime($fecha_y_hora);
			$aux2 = strtotime($this->ultimabicipaga);
			if($this->ultimabicipaga == 0 || ($aux1-$aux2>86400)){
				$costo = $transporte->getCosto();
			} else {
				$costo = 0;
			}
			if(($this->saldo >= $costo+$this->valorPlus)&& $this->plus>0){
				$this->saldo -= $this->valorPlus;
				$this->plus = 0;
				$this->valorPlus = 0;
			}
	
			if($costo<=$this->saldo || $this->plus<2){
				if($costo>$this->saldo && $this->plus<2){
					$this->plus++;
					$this->valorPlus += $costo;
				}
				else{
					$this->saldo -= $costo;
					$this->ultimabicipaga = $fecha_y_hora;
				}

				$this->viajes[$fecha_y_hora] = new Viaje($fecha_y_hora,$transporte,$costo);
				return 1;
			}
			else{
				return 0;
			}
		}
	}
	public function recargar($monto){
		if($monto>=500){
			$monto+=140;
		} else if($monto>=272){
			$monto+=48;
		}
		$this->saldo+=$monto;
	}
	public function getTipo(){
		if($this->plus == 1){
			return "PLUS";
		}
		elseif($this->plus == 2){
			return "ULT. PLUS";
		}
		elseif($this->transbordo){
			return "TRANSBORDO";
		}
		elseif($this->medio){
			return "MEDIO";
		}
		else return "NORMAL";
	}
	public function getBoleto(){
		return $this->boleto;
	}
	public function saldo(){
		return $this->saldo;
	}
	public function viajesRealizados(){
		return $this->viajes;
	}
}



?>
