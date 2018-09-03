<?php

require_once('fibonachi.php');

use PHPUnit\Framework\TestCase;
 
class FibonachiTest extends TestCase
{

	public $fibon;

	public function setUp()
    {
        $this->fibon = new Fibonachi;
    }

    /**
    *	Если числа были заданные не корректно
    */

    public function testValidWrongNum()
	{
		$this->assertEquals('{"error":"1"}',$this->fibon->getValidate(array('from'=>'1q','to'=>'5')));
	}


    /**
    *	Если числа не заданые
    */

    public function testValidNull()
	{
		$this->assertEquals('{"error":"1"}',$this->fibon->getValidate(array('from'=>null, 'to'=>null)));
	}

	/**
    *	Если число одно from
    */

    public function testValidOneNumFrom()
	{
		$this->assertEquals('{"1":1}',$this->fibon->getValidate(array('from'=>1, 'to'=>null)));
	}


	/**
    *	Если число одно to
    */

    public function testValidOneNumTo()
	{
		$this->assertEquals('{"3":2}',$this->fibon->getValidate(array('from'=>null, 'to'=>3)));
	}

	/**
    *	from = to
    */

    public function testValidOneFromEquTo()
	{
		$this->assertEquals('{"3":2}',$this->fibon->getValidate(array('from'=>3, 'to'=>3)));
	}

	/**
    *	Если числа были заданные не корректно
    */

    public function testValidErrorNum()
	{
		$this->assertEquals('{"error":"2"}',$this->fibon->getValidate(array('from'=>'5', 'to'=>'1')));
	}



	/**
    *	Корректная работа
    */

	public function testValidTwoNum()
	{
		$this->assertEquals('{"1":1,"2":1,"3":2,"4":3,"5":5}',$this->fibon->getValidate(array('from'=>'1','to'=>'5')));
		
	}

	/**
    *	Проверка на целочисленное число
    */

	public function testIntCheckOk(){
		$this->assertFalse($this->fibon->intCheck('1q'));
	}

	/**
    *	Проверка ошибок
    */

    public function testErrorMessage()
	{
		$this->assertEquals('{"error":"1"}',$this->fibon->error('1'));
	}

	/**
    *	Формирование json
    */

    public function testData()
	{
		$this->assertEquals('{"3":2}',$this->fibon->returnData(array('3'=>2)));
	}

	/**
    *	Получения не обработанного ряда
    */

	public function testGetManyValue()
	{
		$data = array(
			'1' => 1,
			'2' => 1,
			'3' => 2,
			'4' => 3,
			'5' => 5	
		);

		$this->assertEquals($data,$this->fibon->getManyValue(1,5));
	}

	/**
    *	Строим ряд фибоначи
    */

	public function testBuildFibonachi()
	{
		$data = array(
			'4' => 3,
			'5' => 5	
		);

		$this->assertEquals($data,$this->fibon->buildFibonachi(5));
	}

	/**
    *	Проверяем на существование в редисе значений
    */

	public function testGetCheckPreValue()
	{
		$data = array(
			'3' => 2,
			'4' => 3,
			'5' => 5	
		);

		$this->assertEquals($data,$this->fibon->getCheckPreValue(array(3,4)));
	}

	/**
    *	Проверяем на существование значения в редисе
    */

	public function testGetCheckOneValue()
	{
		$this->assertEquals(3,$this->fibon->getCheckOneValue(4));
	}

}
