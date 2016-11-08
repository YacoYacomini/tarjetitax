<?php

namespace Poli\tarjetitax;

class TarjetaTest extends \PHPUnit_Framework_TestCase {

	//test tarjetas y bondi
	
	protected $tarjeta, $A, $B, $c;
	public function setup(){
		$this->tarjeta = new Tarjeta();
		$this->medio = new Medio();
		$this->A = new Colectivo("112","SEMTUR");
		$this->B = new Colectivo("144","Rosario Bus");
		$this->C = new Bicicleta("1");
	}
	
	public function testTarjeta350(){
		$this->tarjeta->recargar(350);
		$this->assertEquals($this->tarjeta->saldo(), 398, "Cuando cargo 350 deberia tener finalmente 398");
	}
	public function testTarjeta250(){
		$this->tarjeta->recargar(250);
		$this->assertEquals($this->tarjeta->saldo(), 250, "Cuando cargo 250 deberia tener finalmente 250");
	}
	public function testTarjeta600()
	{
		$this->tarjeta->recargar(600);
		$this->assertEquals($this->tarjeta->saldo(), 740, "Cuando cargo 600 deberia tener finalmente 740");
	}

	public function testPagarColectivo(){
		$this->tarjeta->recargar(20);
		$this->medio->recargar(20);
		$this->tarjeta->pagar($this->A,"2016/26/9 12:00");
		$this->tarjeta->pagar($this->A,"2016/26/9 12:02");
		$this->assertEquals($this->tarjeta->saldo(),4, "El saldo de la tarjeta deberia ser de $4");
		$this->medio->pagar($this->A,"2016/26/9 12:00");
		$this->medio->pagar($this->A,"2016/26/9 12:02");
		$this->assertEquals($this->medio->saldo(),12, "El saldo de la tarjeta deberia ser de $12 ya que uso medio boleto");
	}
	
	public function testNoSaldo(){
		$this->tarjeta->recargar(2.63);
		$this->tarjeta->pagar($this->C,"2016/02/1 12:00");
		$this->tarjeta->pagar($this->C,"2016/04/1 12:00"); 
		$this->assertEquals($this->tarjeta->pagar($this->C,"2016/06/1 12:00"),0,"No deberia poder pagar");
	}

	public function testPagarColectivosDistintos(){
		$this->tarjeta->recargar(20);
		$this->medio->recargar(20);
		$this->tarjeta->pagar($this->A,"2016/02/1 12:00");
		$this->tarjeta->pagar($this->B,"2016/03/1 12:02");
		$this->assertEquals($this->tarjeta->saldo(),4, "El saldo de la tarjeta deberia ser de $4");
		$this->medio->pagar($this->A,"2016/02/1 12:00");
		$this->medio->pagar($this->B,"2016/03/1 12:02");
		$this->assertEquals($this->medio->saldo(),12, "El saldo de la tarjeta deberia ser de $12 ya que use medio");
	}

	public function testPagarTransbordo(){
		$this->tarjeta->recargar(20);
		$this->medio->recargar(20);
		$this->tarjeta->pagar($this->A,"2016/02/1 12:00");
		$this->tarjeta->pagar($this->B,"2016/02/1 12:02");
		$this->assertEquals($this->tarjeta->saldo(),9.36, "El saldo de la tarjeta deberia ser de $9.36 por el trasbordo");
		$this->medio->pagar($this->A,"2016/02/1 12:00");
		$this->medio->pagar($this->B,"2016/02/1 12:02");
		$this->assertEquals($this->medio->saldo(),14.68, "El saldo de la tarjeta deberia ser de $14.68 por el trasbordo");
	}
	//test de boletos

	public function testPlus(){
		$this->tarjeta->pagar($this->A,"2016/02/1 12:00");
		$this->assertEquals($this->tarjeta->getBoleto()->getBoleto(),
			"TRANSPORTE PUBLICO ROSARIO \n2016/02/1 12:00 Linea:112\nPLUS $8\nSaldo: $0"," ");
	}
	public function testUltPlus(){
		$this->medio->pagar($this->A,"2016/01/1 12:00");
		$this->medio->pagar($this->A,"2016/02/1 12:00");
		$this->assertEquals($this->medio->getBoleto()->getBoleto(),
			"TRANSPORTE PUBLICO ROSARIO \n2016/02/1 12:00 Linea:112\nULT. PLUS $4\nSaldo: $0"," ");
	}
	public function testMedio(){
		$this->medio->recargar(20);
		$this->medio->pagar($this->A,"2016/02/1 12:00");
		$this->assertEquals($this->medio->getBoleto()->getBoleto(),
			"TRANSPORTE PUBLICO ROSARIO \n2016/02/1 12:00 Linea:112\nMEDIO $4\nSaldo: $16"," ");
	}
	public function testTransbordo(){
		$this->tarjeta->recargar(30);
		$this->tarjeta->pagar($this->B,"2016/02/1 11:55");
		$this->tarjeta->pagar($this->A,"2016/02/1 12:00");
		$this->assertEquals($this->tarjeta->getBoleto()->getBoleto(),
			"TRANSPORTE PUBLICO ROSARIO \n2016/02/1 12:00 Linea:112\nTRANSBORDO $2.64\nSaldo: $19.36"," ");
	}
	public function testTransbordoMedio(){
		$this->medio->recargar(30);
		$this->medio->pagar($this->B,"2016/02/1 11:55");
		$this->medio->pagar($this->A,"2016/02/1 12:00");
		$this->assertEquals($this->medio->getBoleto()->getBoleto(),
			"TRANSPORTE PUBLICO ROSARIO \n2016/02/1 12:00 Linea:112\nTRANSBORDO $1.32\nSaldo: $24.68"," ");
	}
	public function testNormal(){
		$this->tarjeta->recargar(8);
		$this->tarjeta->pagar($this->A,"2016/02/1 12:00");
		$this->assertEquals($this->tarjeta->getBoleto()->getBoleto(),
			"TRANSPORTE PUBLICO ROSARIO \n2016/02/1 12:00 Linea:112\nNORMAL $8\nSaldo: $0"," ");
	}

//test de bicis

		protected $boleto_bicicleta = 12; 
		public function testPagarBici(){
			$this->tarjeta->recargar(50);
			$this->tarjeta->pagar($this->C,"2016/02/1 12:00");
			$this->assertEquals($this->tarjeta->saldo(),50-$this->boleto_bicicleta, "Saldo deberia ser $".(50-$this->boleto_bicicleta));	
		}
		public function testPagarDia(){
			$this->tarjeta->recargar(30);
			$this->tarjeta->pagar($this->C,"2016/02/1 12:00");
			$this->tarjeta->pagar($this->C,"2016/02/1 15:00"); 
			$this->assertEquals($this->tarjeta->saldo(),30-$this->boleto_bicicleta, "Saldo deberia ser $".(30-$this->boleto_bicicleta));
			$this->tarjeta->pagar($this->C,"2016/02/3 12:00"); 
			$this->assertEquals($this->tarjeta->saldo(),30-2*$this->boleto_bicicleta, "Saldo deberia ser $".(30-2*$this->boleto_bicicleta));
		}
		public function testPagarPlus(){
			$this->tarjeta->recargar(2.63);
			$this->tarjeta->pagar($this->C,"2016/02/1 12:00"); 
			$this->assertEquals($this->tarjeta->pagar($this->C,"2016/04/1 12:00"),1, "Deberia poder pagar"); 
			$this->assertEquals($this->tarjeta->saldo(),2.63, "Saldo deberia ser $2.63");
		}
		
}
?>
