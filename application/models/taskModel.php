<?php

class taskModel extends Model implements ISaver
{
	private $id;
	private $name;
	private $mail;
	private $content;
	private $pictureId;
	private $checked;

	public function setName($name){$this->name = $name;}
	public function setMail($mail){$this->mail = $mail;}
	public function setContent($content){$this->content = $content;}
	public function setPictureId($pictureId){$this->pictureId = $pictureId;}
	public function setId($id){$this->id = $id;}
	public function getId() : int{return $this->id;}
	public function getData(): mixed{return parent::getData();}
	public function getContent(){return $this->content;}
	public function getMail(){return $this->mail;}
	public function getName(){return $this->name;}
	public function getPictureId(){return $this->pictureId;}
	public function getChecked(){return $this->checked;}
	public function setChecked($checked){$this->checked = $checked;}

	public static function findById(int $id)
	{
		$query = App::$db->prepare('SELECT * FROM `tasks` WHERE `id` = ?;');
		$query->execute([$id]);
		$row = $query->fetch(PDO::FETCH_LAZY);
		if(count($row) != 0)
		{
			$task = new self();
			$task->setId($row['id']);
			$task->setName($row['name']);
			$task->setMail($row['mail']);
			$task->setContent($row['content']);
			$task->setPictureId($row['picture_id']);
			$task->setChecked($row['checked']);
			return $task;
		}
		return null;
	}

	public static function executeQuery($query)
	{
		$tasks = [];
		$query = App::$db->query($query);
		while($row = $query->fetch(PDO::FETCH_LAZY))
		{
				$task = new self();
				$task->setId($row['id']);
				$task->setName($row['name']);
				$task->setMail($row['mail']);
				$task->setContent($row['content']);
				$task->setPictureId($row['picture_id']);
				$task->setChecked($row['checked']);
				$tasks[] = $task;
		}
		return $tasks;
	}

	public static function count()
	{
		return App::$db->query('SELECT COUNT(`id`) as val FROM pictures;')->fetch()['val'];
	}

	public function save()
	{
		if(!empty($this->id))
		{
			$query = App::$db->prepare('UPDATE tasks SET `id` = ?, `name` = ?, `mail` = ?, `content` = ?, `picture_id` = ?, `checked` = ? WHERE `id` = ?;');
			$query->execute([$this->id, $this->name, $this->mail, $this->content, $this->pictureId, $this->checked, $this->id]);
		}
		else
		{
			$query = App::$db->prepare('INSERT INTO tasks(`id`, `name`, `mail`,`content`,`picture_id`,`checked`) VALUE(?,?,?,?,?,?)');
			$query->execute([$this->id, $this->name, $this->mail, $this->content, $this->pictureId, $this->checked]);
		}
	}
}