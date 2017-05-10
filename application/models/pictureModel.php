<?php

class pictureModel extends Model implements ISaver
{
	private $id;
	private $path;

	public function setId($id){$this->id = $id;}
	public function getData(): mixed{return parent::getData();}
	public function setPath($path){$this->path = $path;}
	public function getPath(){return $this->path;}
	public function getId(){return $this->id;}

	public static function findById(int $id)
	{
		$query = App::$db->prepare('SELECT * FROM `pictures` WHERE `id` = ?;');
		$query->execute([$id]);
		$row = $query->fetch(PDO::FETCH_LAZY);
		if(count($row) != 0)
		{
			$pict = new self();
			$pict->setId($row['id']);
			$pict->setPath($row['path']);
			return $pict;
		}
		return null;
	}

	public static function findByPath(string $path)
	{
		$query = App::$db->prepare('SELECT * FROM `pictures` WHERE `path` = ?;');
		$query->execute([$path]);
		$row = $query->fetch(PDO::FETCH_LAZY);
		if(count($row) != 0)
		{
			$pict = new self();
			$pict->setId($row['id']);
			$pict->setPath($row['path']);
			return $pict;
		}
		return null;
	}

	public static function count()
	{
		return App::$db->query('SELECT MAX(`id`) as val FROM pictures;')->fetch()['val'] + 1;
	}

	public function save()
	{
		if(!empty($this->id))
		{
			$query = App::$db->prepare('UPDATE pictures SET `id` = ?, `path` = ? WHERE id = ?;');
			$query->execute([$this->id, $this->path, $this->id]);
		}
		else
		{
			$query = App::$db->prepare('INSERT INTO pictures(`id`, `path`) VALUE(?,?)');
			$query->execute([$this->id, $this->path]);
		}
	}
}