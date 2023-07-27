<?php
namespace Chelnynet\Warranty\NotifyTasks;

use Bitrix\Main\Type\DateTime;
use Chelnynet\Warranty\NotifyTaskTable;

/**
 * Class draftReminder
 * @package Chelnynet\Warranty\NotifyTasks
 */

class draftReminder extends NotifyTasks
{
	public function removeTaskFilter($attr=null):self
	{
		$this->filter=[
			'=UF_RECLAMATION'=>$this->reclamation,
			'=UF_TYPE'=>__CLASS__
		];
		return $this;
	}

	public function setVariables($attr=null):self
	{
		$this->users=$this->managersList();
		$this->users[]=$this->authorID();

		$time=(new \DateTime())->modify('+ 5 days');
		$this->start=DateTime::createFromPhp($time);
		$this->nextExec=DateTime::createFromPhp($time);
		return $this;
	}

	public function getTemplate(): self
	{
		$this->messageTemplate='NOTIFY_TASK_ENTITY_DRAFT_REMINDER_MESSAGE';
		return $this;
	}

	public function getNextExec(array $item): self
	{
		if(\array_intersect($this->getManagerGroup(),\CUser::GetUserGroup($item['UF_USER']))){
			$this->resume=false;
			NotifyTaskTable::delete($item['ID']);
		}
		else{
			$this->nextExec=DateTime::createFromPhp((new \DateTime())->modify('+ 1 day'));
		}
		return $this;
	}
}