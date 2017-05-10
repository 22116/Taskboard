<?php

class mainController extends Controller
{
	public function actionIndex()
	{
		$this->view->generate('index', [
			'pageIndex' => isset($_REQUEST['page']) ? $_REQUEST['page'] : 1,
			'sortType' => isset($_REQUEST['sort']) ? $_REQUEST['sort'] : blocksWidget::SORT_NONE
		]);
	}

	public function actionEditor()
	{
		if(!authModel::$isAuth || !isset($_REQUEST['task_id'])) header('location: /');

		if(isset($_REQUEST['task_id']) && !empty($_REQUEST['task_id']) &&
			isset($_REQUEST['content']) && !empty($_REQUEST['content']))
		{
			$task = taskModel::findById($_REQUEST['task_id']);
			$task->setContent(trim($_REQUEST['content']));
			$task->setChecked(isset($_REQUEST['checked']) ? 1 : 0);
			$task->save();
			header('location: /');
		}

		$user = taskModel::findById($_REQUEST['task_id']);
		$this->view->generate('editor', [
			'name' => $user->getName(),
			'mail' => $user->getMail(),
			'text' => $user->getContent(),
			'checked' => $user->getChecked(),
			'id' => $user->getId()
		]);
	}

	public function actionCreator()
	{
		if(isset($_REQUEST['name']) && !empty($_REQUEST['name']) &&
			isset($_REQUEST['mail']) && !empty($_REQUEST['mail']) &&
			isset($_REQUEST['content']) && !empty($_REQUEST['content']) &&
			isset($_FILES['image']))
		{
			$pictureId = pictureModel::findByPath($this->uploadPicture('image'))->getId();
			$task = new taskModel();
			$task->setName(trim($_REQUEST['name']));
			$task->setMail(trim($_REQUEST['mail']));
			$task->setContent(trim($_REQUEST['content']));
			$task->setPictureId($pictureId);
			$task->setChecked(0);
			$task->save();
			header('location: /');
		}
		$this->view->generate('creator');
	}

	private function uploadPicture($name)
	{
		$uploadfile = "cash/" . pictureModel::count() . '.' . (new SplFileInfo($_FILES[$name]['name']))->getExtension();
		$name = $this->resizeImage($_FILES[$name], $uploadfile);

		if($name != 'cash/default.png')
		{
			$pict = new pictureModel();
			$pict->setPath($name);
			$pict->save();
		}

		return $name;
	}

	private function resizeImage($file, $path)
	{
		if ($file['type'] == 'image/jpeg')
			$src = imagecreatefromjpeg($file['tmp_name']);
		else if ($file['type'] == 'image/png')
			$src = imagecreatefrompng($file['tmp_name']);
		else if ($file['type'] == 'image/gif')
			$src = imagecreatefromgif($file['tmp_name']);
		else return 'cash/default.png';

		$w_src = imagesx($src);
		$h_src = imagesy($src);

		$maxWidth = 320;

		if ($w_src > $maxWidth)
		{
			$ratio = $w_src / $maxWidth;
			$w_dest = round($w_src/$ratio);
			$h_dest = round($h_src/$ratio);

			$dest = imagecreatetruecolor($w_dest, $h_dest);

			imagecopyresampled($dest, $src, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src);

			imagejpeg($dest, $path, 75);
			imagedestroy($dest);
			imagedestroy($src);

			return $path;
		}
		else
		{
			imagejpeg($src, $path, 75);
			imagedestroy($src);

			return $path;
		}
	}

	public function behaviorBefore()
	{
		authModel::checkCurrentUser();
	}
}