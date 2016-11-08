<?php

namespace Poli\tarjetitax;

class TarjetaTest extends \PHPUnit_Framework_TestCase {

	protected $tarjeta,$A,$B;
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

	public function testPagarBici(){
		$this->tarjeta->recargar(30);
		$this->tarjeta->pagar($this->C,"2016/02/1 12:00");
		$this->assertEquals($this->tarjeta->saldo(),18, "El saldo de la tarjeta deberia ser de $18");
		$this->tarjeta->pagar($this->C,"2016/02/1 15:00");
		$this->assertEquals($this->tarjeta->saldo(),18, "El saldo de la tarjeta deberia ser de $18");
		$this->tarjeta->pagar($this->C,"2016/02/3 12:00");
		$this->assertEquals($this->tarjeta->saldo(),6, "El saldo de la tarjeta deberia ser de $6");
	}
}
?>
