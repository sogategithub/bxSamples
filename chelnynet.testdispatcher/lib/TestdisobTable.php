<?php
namespace Chelnynet\TestDispatcher;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\Entity\FieldError;
use Bitrix\Main\Entity\Event;


Loc::loadMessages(__FILE__);

/**
 * Class TestdisobTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_USER int mandatory
 * <li> UF_OBJECT int mandatory
 * </ul>
 *
 * @package Bitrix\Testdisob
 **/

class TestdisobTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ch_testdisob';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'title' => 'ID'
				]
			),
			new IntegerField(
				'UF_USER',
				[
					'required' => true,
					'title' => 'Пользователь'
				]
			),
			new IntegerField(
				'UF_OBJECT',
				[
					'required' => true,
					'title' => 'Объект'
				]
			),
		];
	}

	public static function OnBeforeAdd(\Bitrix\Main\ORM\Event $event){
		$result=new \Bitrix\Main\Entity\EventResult;
		$arFields=$event->getParameter("fields");
		// проверить наличие диспетчера,
		if(TestdispatcherTable::getCount(['=UF_USER'=>$arFields['UF_USER']])!==1){
			$result->addError(new FieldError(
				$event->getEntity()->getField('UF_USER'),
				'Нет такого диспетчера'
				));
			}
		// наличие объекта,
		if(TestobjectTable::getCount(['=ID'=>$arFields['UF_OBJECT']])!==1){
			$result->addError(new FieldError(
				$event->getEntity()->getField('UF_OBJECT'),
				'Нет такого объекта'
			));
		}
		// работает ли диспетчер на других объектах
		if(self::getCount(['=UF_USER'=>$arFields['UF_USER']])!==0){
			$result->addError(new FieldError(
				$event->getEntity()->getField('UF_OBJECT'),
				'Этот диспетчер уже работает на другом обьекте'
			));
		}
		return $result;
	}
}