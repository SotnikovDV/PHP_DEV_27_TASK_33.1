<?php
class Model_Services extends Model
{
	public function get_data()
	{	
		// Здесь мы просто симулируем реальные данные.
		return array(
			array(
				'id' => '1',
				'link' => 'http://DunkelBeer.ru',
				'description' => 'Ловко обмануть избирателей'
			),
			array(
				'id' => '2',
				'link' => 'http://ZopoMobile.ru',
				'description' => 'Смухлевать результаты выборов.'
			),
			array(
				'id' => '3',
				'link' => 'http://ZopoMobile.ru',
				'description' => 'Настроить репресивный аппарат'
			),
			array(
				'id' => '4',
				'link' => 'http://ZopoMobile.ru',
				'description' => 'Ограбить население'
			),
			array(
				'id' => '5',
				'link' => 'http://ZopoMobile.ru',
				'description' => 'Оккупировать соседнюю страну'
			),
		);
	}
}