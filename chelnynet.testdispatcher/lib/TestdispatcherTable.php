<?php
namespace Chelnynet\TestDispatcher;

use Bitrix\Main\Entity\FieldError;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

Loc::loadMessages(__FILE__);

/**
 * Class TestdispatcherTable
 *
 * Fields:
 * <ul>
 * <li> UF_CREATED datetime optional default current datetime
 * <li> UF_ACTIVE int optional default 0
 * <li> UF_ACTIVE_TO datetime optional
 * <li> UF_USER int mandatory
 * <li> UF_COMMENT text mandatory
 * <li> UF_RIGHTS int optional
 * </ul>
 *
 * @package Bitrix\Testdispatcher
 **/

class TestdispatcherTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'ch_testdispatcher';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new DatetimeField(
				'UF_CREATED',
				[
					'default' => function()
					{
						return new DateTime();
					},
					'title' => 'Дата и время создания записи'
				]
			),
			new IntegerField(
				'UF_ACTIVE',
				[
					'default' => 0,
					'title' => 'Активность'
				]
			),
			new DateField(
				'UF_ACTIVE_TO',
				[
					'title' => 'Дата окончания активности'
				]
			),
			new IntegerField(
				'UF_USER',
				[
					'primary' => true,
					'title' => 'Пользователь'
				]
			),
			new TextField(
				'UF_COMMENT',
				[
					'required' => true,
					'title' => 'Комментарий'
				]
			),
			new IntegerField(
				'UF_RIGHTS',
				[
					'title' => 'Уровень доступа',
					'validation' => function(){
						return array(
							function ($value) {
								if ($value>=1&&$value<=12)
								{
									return true;
								}
								else
								{
									return 'Должен содержать значение 1-12';
								}
							}
						);
					}
				]
			),
			(new Reference(
				'USER',
				UserTable::class,
				Join::on('this.UF_USER', 'ref.ID')
			))
				->configureJoinType('inner')
		];
	}

	public static function OnAfterUserUpdate($fields){
		$update=false;
		if($fields['ACTIVE']==='Y'){
			$update=true;
			}
		$res=self::update($fields['ID'],['UF_ACTIVE'=>$update]);
		if(!$res->isSuccess()){
			//die("<pre>".print_r($res->getErrorMessages(),true)."</pre>");
			}
		}


	public static function OnBeforeAdd(\Bitrix\Main\ORM\Event $event){
		$result=new \Bitrix\Main\Entity\EventResult;
		$arFields=$event->getParameter("fields");
		if(self::getCount(['=UF_USER'=>$arFields['UF_USER']])!==0){
			$result->addError(new FieldError(
				$event->getEntity()->getField('UF_USER'),
				'Этот диспетчер уже заведён в систему'
			));
		}
		return $result;
	}
}