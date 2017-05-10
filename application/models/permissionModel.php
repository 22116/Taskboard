<?php

class permissionModel extends Model implements ISaver
{
	private $id;
	private $name;

	public function setId($id){$this->id = $id;}
	public function getName(){return $this->name;}
	public function setName($name){$this->name = $name;}
	public function getId(){return $this->id;}
	public function getData(): mixed{return parent::getData();}

	public static function findById(int $id)
	{
		$query = App::$db->prepare('SELECT * FROM `permissions` WHERE `id` = ?;');
		$query->execute([$id]);
		$row = $query->fetch(PDO::FETCH_LAZY);
		if(count($row) != 0)
		{
			$perm = new self();
			$perm->setId($row['id']);
			$perm->setName($row['name']);
			return $perm;
		}
		return null;
	}
	public function save()
	{
		if(!empty($this->id))
		{
			$query = App::$db->prepare('UPDATE permissions SET `id` = ?, `name` = ? WHERE id = ?;');
			$query->execute([$this->id, $this->name, $this->id]);
		}
		else
		{
			$query = App::$db->prepare('INSERT INTO permissions(`id`, `name`) VALUE(?,?)');
			$query->execute([$this->id, $this->name]);
		}
	}
}