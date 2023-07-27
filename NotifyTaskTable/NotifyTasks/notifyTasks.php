<?php
namespace Chelnynet\Warranty\NotifyTasks;

use Bitrix\Main\Localization\Loc;
use \Chelnynet\Warranty\NotifyTaskTable;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Type\DateTime;

abstract class NotifyTasks extends NotifyTaskTable
{
	protected $resume=true;
	protected $reclamation=0;
	protected $users=[];
	protected $start=null;
	protected $nextExec=null;
	protected $filter=[];

	abstract public function setVariables($attr=null):self;
	abstract public function removeTaskFilter($attr=null):self;
	abstract public function getTemplate():self;
	abstract public function getNextExec(array $item):self;

	/**
	 * @param string $modifier
	 * @return string
	 * Set $modifier in datetime format '+1 day' as example
	 */
	protected function nineFourteen($modifier=''):string{
		$timesArray=[
			(new \DateTime())->setTime(9,00,00),
			(new \DateTime())->setTime(14,00,00),
		];
		if($modifier!=='')
		{
			$result=$timesArray[0]->modify($modifier);
		}
		else
		{
			if((new \DateTime())<$timesArray[0])
			{
				$result=$timesArray[0];
			}
			else{
				if((new \DateTime())<$timesArray[1])
				{
					$result=$timesArray[1];
				}
				else
				{
					$result=$timesArray[0]->modify('+1 day');
				}
			}
		}
		return $result->format("d.m.Y H:i:s");
	}

	protected function getManagerGroup():array
	{
		$options=Option::get(
			'chelnynet_rorder',
			'chelnynet_rorderReclamation'
		);
		$optionsUnserialized=\unserialize($options);
		if(!\is_array($optionsUnserialized)){
			return [-1];
		}
		$result=$optionsUnserialized['manager'];
		if(!\is_array($result)) {
			return [-1];
		}
		else{
			return $result;
		}
	}

	protected function managersList():array
	{
		$result=\Bitrix\Main\UserTable::getList([
			'select'=>['ID'],
			'filter'=>[
				'=ACTIVE'=>'Y',
				'Bitrix\Main\UserGroupTable:USER.GROUP_ID'=>$this->getManagerGroup()
			],
			'group'=>['ID'],
		])->fetchAll();
		if(\is_array($result)){
			return \array_column($result,'ID');
		}
		else
		{
			return[];
		}
	}

	protected function authorID():int{
		$res=\Bitrix\Hlbd\WarrantyTable::getRow([
			'select'=>['UF_OWNER'],
			'filter'=>['=ID'=>$this->reclamation]
		]);
		if($res['UF_OWNER']>0){
			return $res['UF_OWNER'];
		}
		else{
			throw new \Exception(__CLASS__.': reclamation without authorID');
		}
	}

	public function setReclamation(int $id):self
	{
		$this->reclamation=$id;
		return $this;
	}

	public function addTasks():self
	{
		if($this->resume===false)
		{
			return $this;
		}
		if(!\is_array($this->users))
		{
			throw new \Exception(__CLASS__.': users array is not speciifed');
		}
		if(!($this->start instanceof DateTime))
		{
			throw new \Exception(__CLASS__.': start time not in instance of datetime');
		}
		if(!($this->nextExec instanceof DateTime))
		{
			throw new \Exception(__CLASS__.': next execute time not in instance of datetime');
		}
		if(!isset($this->reclamation)||$this->reclamation===0)
		{
			throw new \Exception(__CLASS__.': reclamation id is not speciifed');
		}
		foreach($this->users as $user)
		{
			$array=[
				'UF_TYPE'=>get_called_class(),
				'UF_USER'=>$user,
				'UF_RECLAMATION'=>$this->reclamation,
				'UF_START'=>$this->start,
				'UF_NEXT_EXEC'=>$this->nextExec,
				'UF_ACTIVE'=>true
			];
			$result=NotifyTaskTable::add($array);
			NotifyTaskTable::logErrors($result);
		}
		return $this;
	}

	public function removeTasks(): self
	{
		if(!\is_array($this->filter))
		{
			throw new \Exception(__CLASS__.': you need to specified this->filter before query');
		}
		$items=NotifyTaskTable::getList([
			'select'=>['ID'],
			'filter'=>$this->filter
		])->fetchAll();

		if(!\is_array($items)){
			return $this;
			}

		foreach($items as $item)
		{
			$result=NotifyTaskTable::delete($item);
			NotifyTaskTable::logErrors($result);
		}
		return $this;
	}

	public function getMessage(&$aggregate,$item): self
	{
		if(!\is_array($aggregate[$item['UF_USER']]))
		{
			$aggregate[$item['UF_USER']]=[];
		}
		$mess=Loc::getMessage($this->messageTemplate,['#ID#'=>$item['UF_RECLAMATION']]);
		$aggregate[$item['UF_USER']]['tag'].='_'.$item['UF_RECLAMATION'].'-'.$item['ID'];
		$aggregate[$item['UF_USER']]['messages'].=$mess.'[br]';
		$aggregate[$item['UF_USER']]['mailMessages'].=$mess.'\n';
		return $this;
	}

	public function updateTask(int $id): self
	{
		if($id===0||$this->resume===false)
		{
			return $this;
		}
		$arr=[
			'UF_NEXT_EXEC'=>$this->nextExec,
			'UF_LAST_EXEC'=>new DateTime()
		];
		$result=NotifyTaskTable::update($id,$arr);
		NotifyTaskTable::logErrors($result);
		return $this;
	}
}