<?php

class usersModel extends Model implements ISaver
{
	private $id;
	private $login;
	private $password;
	private $permissionId;
	private $hash;

	public function setId($id){$this->id = $id;}
	public function getId(){return $this->id;}
	public function getLogin(){return $this->login;}
	public function getPassword(){return $this->password;}
	public function getPermissionId(){return $this->permissionId;}
	public function setLogin($login){$this->login = $login;}
	public function setPassword($password){$this->password = $password;}
	public function setPermissionId($permissionId){$this->permissionId = $permissionId;}
	public function getHash(){return $this->hash;}
	public function setHash($hash){$this->hash = $hash;}

	public static function findById(int $id)
	{
		$query = App::$db->prepare('SELECT * FROM `users` WHERE `id` = ?;');
		$query->execute([$id]);
		$row = $query->fetch(PDO::FETCH_LAZY);
		if(count($row) != 0)
		{
			$pict = new self();
			$pict->setId($row['id']);
			$pict->setLogin($row['login']);
			$pict->setPassword($row['password']);
			$pict->setPermissionId($row['permission_id']);
			$pict->setHash($row['hash']);
			return $pict;
		}
		return null;
	}

	public static function findByLogin(string $login)
	{
		$query = App::$db->prepare('SELECT * FROM `users` WHERE `login` = ?;');
		$query->execute([$login]);
		$row = $query->fetch(PDO::FETCH_LAZY);
		if(count($row) != 0)
		{
			$user = new self();
			$user->setId($row['id']);
			$user->setLogin($row['login']);
			$user->setPassword($row['password']);
			$user->setPermissionId($row['permission_id']);
			return $user;
		}
		return null;
	}

	public function save()
	{
		if(!empty($this->id))
		{
			$query = App::$db->prepare('UPDATE users SET `id` = ?, `login` = ?, `password` = ?, `permission_id` = ?, `hash` = ? WHERE id = ?;');
			$query->execute([$this->id, $this->login, $this->password, $this->permissionId, $this->hash, $this->id]);
		}
		else
		{
			$query = App::$db->prepare('INSERT INTO users(`id`, `login`, `password`, `permission_id`, `hash`) VALUE(?,?,?,?,?)');
			$query->execute([$this->id, $this->login, $this->password, $this->permissionId, $this->hash]);
		}
	}
}