<?php
namespace Chelnynet\Warranty;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\ORM\Data\DataManager,
	\Bitrix\Main\ORM\Fields\DatetimeField,
	\Bitrix\Main\Type\DateTime,
	\Bitrix\Main\ORM\Fields\IntegerField,
	\Bitrix\Main\ORM\Fields\TextField,
	\Bitrix\Main\Event,
	\Chelnynet\Warranty\NotifyTasks;

Loc::loadMessages(__FILE__);

class NotifyTaskTable extends DataManager
{
	const STATUS_AUTOMATISATION=[
		[
			'in'=>[12,40],
			'out'=>[41],
			'type'=>'checkCustomer'
		],
	];

	//static $
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'cro_warranty_notify_task';
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
					'autocomplete' => true,
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_ID_FIELD')
				]
			),
			new TextField(
				'UF_TYPE',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_TYPE_FIELD')
				]
			),
			new IntegerField(
				'UF_USER',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_USER_FIELD')
				]
			),
			new IntegerField(
				'UF_RECLAMATION',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_RECLAMATION_FIELD')
				]
			),
			new IntegerField(
				'UF_ACTIVE',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_ACTIVE_FIELD')
				]
			),
			new DatetimeField(
				'UF_START',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_START_FIELD')
				]
			),
			new DatetimeField(
				'UF_NEXT_EXEC',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_NEXT_EXEC_FIELD')
				]
			),
			new DatetimeField(
				'UF_LAST_EXEC',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_LAST_EXEC_FIELD')
				]
			),
			new DatetimeField(
				'UF_FINISH',
				[
					'title' => Loc::getMessage('NOTIFY_TASK_ENTITY_UF_FINISH_FIELD')
				]
			),
		];
	}

	public static function onAfterReclamationAdd(Event $event):void{
		$id=$event->getParameter('RECLAMATION_ID');

		(new NotifyTasks\checkBuyerInfo())
			->setReclamation($id)
			->setVariables()
			->addTasks();

		(new NotifyTasks\draftReminder())
			->setReclamation($id)
			->removeTaskFilter()
			->removeTasks();
		}

	public static function onAfterRemoveDraft(Event $event):void{
		$id=$event->getParameter('RECLAMATION_ID');

		(new NotifyTasks\draftReminder())
			->setReclamation($id)
			->removeTaskFilter()
			->removeTasks();
		}

	public static function onAfterReturnToDraft(Event $event):void{
		$id=$event->getParameter('RECLAMATION_ID');

		(new NotifyTasks\checkBuyerInfo())
			->setReclamation($id)
			->removeTaskFilter()
			->removeTasks();

		(new NotifyTasks\draftReminder())
			->setReclamation($id)
			->setVariables()
			->addTasks();
		}

	public static function onAfterReclamationChangeNomStatus(Event $event){
		$array=$event->getParameter(0);
		$list=self::STATUS_AUTOMATISATION;
		for($i=0;$i<count($list);$i++)
		{
			if(\in_array($array['UF_STATUS'],$list[$i]['in']))
			{
				$cname="\\Chelnynet\\Warranty\\NotifyTasks\\".$list[$i]['type'];
				(new $cname())
					->setReclamation($array['UF_RECLAMATION'])
					->setVariables($array)
					->addTasks();
			}
			elseif(\in_array($array['UF_STATUS'],$list[$i]['out']))
			{
				$cname="\\Chelnynet\\Warranty\\NotifyTasks\\".$list[$i]['type'];
				(new $cname())
					->setReclamation($array['UF_RECLAMATION'])
					->removeTaskFilter($array)
					->removeTasks();
			}
			else{}
		}
	}

	public static function toWork(int $id):void
	{
		(new NotifyTasks\checkBuyerInfo())
			->setReclamation($id)
			->removeTaskFilter()
			->removeTasks();
	}

	protected static function logErrors($result)
	{
		if(!$result->isSuccess())
		{
			\rostarError::logError(print_r($result->getErrorMessages(),true),__CLASS__);
		}
	}

	public static function execAgent()
	{
		(new self)->send();
		return '\\'.__CLASS__.'::'.__FUNCTION__.'();';
	}

	private function senderItems():array
	{
		$result=self::getList([
			'select'=>['ID','UF_TYPE','UF_RECLAMATION','UF_USER'],
			'filter'=>[
				'=UF_ACTIVE'=>true,
				'<=UF_NEXT_EXEC'=>(new Datetime()),
			]
		])->fetchAll();
		if(\is_array($result))
		{
			return $result;
		}
		else
		{
			return [];
		}
	}

	private function send():void{
		$aggregate=[];
		$items=$this->senderItems();
		for($i=0;$i<\count($items);$i++)
		{
			$cname=$items[$i]['UF_TYPE'];
			if(!class_exists($cname)||!preg_match('#^Chelnynet\\\\Warranty\\\\NotifyTasks#',$cname))
			{
				continue;
			}
			(new $cname())
				->getTemplate()
				->getMessage($aggregate,$items[$i])
				->getNextExec($items[$i])
				->updateTask($items[$i]['ID']);
		}
		$this->notify($aggregate);
		}

	private function notify(array $array): self {
		foreach($array as $userID=>$notify){
			$notificator=new \Chelnynet\Utils\Notificator;
			$notificator->setUser($userID);
			$notificator->setTags('NOTIFY_TASK'.$notify['tag']);
			$notificator->setSubject(Loc::getMessage('NOTIFY_TASK_SUBJECT'));
			$notificator->setMessage($notify['messages']);
			$notificator->setMailMessage($notify['mailMessages']);
			$notificator->send();
			}
		return $this;
		}
	}
/*
Set agent every minute
\Chelnynet\Warranty\NotifyTaskTable::execAgent();

Set events
foreach([
'onAfterReclamationChangeNomStatus',
'onAfterRemoveDraft',
'onAfterReclamationAdd',
'onAfterReturnToDraft'
] as $event)
{
(\Bitrix\Main\EventManager::getInstance())->registerEventHandler(
	"rostarReclamations",
	$event,
	CHELNYNET_RORDER_MODULE_NAME,
	"\Chelnynet\Warranty\NotifyTaskTable",
	$event,
	100
	);
}
*/