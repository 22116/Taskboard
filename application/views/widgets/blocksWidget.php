<?php

class blocksWidget extends Widget
{
	private $rowCount;
	private $page;
	private $sort;

	const SORT_NAME = 0;
	const SORT_MAIL = 1;
	const SORT_STATUS = 2;
	const SORT_NONE = 3;

	public function __construct(int $selectedPage, int $sortType = self::SORT_NONE)
	{
		$this->content = '';

		$min = $selectedPage * 3 - 3;
		$max = 3;

		$query = "SELECT * FROM tasks";
		switch ($sortType)
		{
			case self::SORT_NAME: $query .= ' ORDER BY `name` '; break;
			case self::SORT_MAIL: $query .= ' ORDER BY `mail` '; break;
			case self::SORT_STATUS: $query .= ' ORDER BY `checked` '; break;
		}
		$query .= " LIMIT ". $min .",". $max;
		$tasks = taskModel::executeQuery($query);
		$this->rowCount = taskModel::count();

		$this->page = $selectedPage;
		$this->sort = $sortType;

		$this->constructSortButtons();
		$this->content .= '<div class="row">';
		foreach ($tasks as $task)
		{
			$pict = pictureModel::findById($task->getPictureId());

			$this->content .= '<div class="col-sm-6 col-md-4">
			<div class="thumbnail">
				<img src="/'.$pict->getPath().'" alt="">
				<div class="caption">
					<h3>'.$task->getName().'</h3>
					<h3>'.$task->getMail().'</h3>
					<p>'.$task->getContent().'</p>
					<p>'
				.(authModel::$isAuth && authModel::$user->getPermissionId() == 2 ? '<a href="/main/editor?task_id='.$task->getId().'" class="btn btn-primary" role="button">Edit</a>' : '')
				.($task->getChecked() ? '<div class="label-success">Checked</div>' : '').
					'</p>
				</div>
			</div>
			</div>';
		}
		$this->content .= '</div>';
		if ($this->rowCount / 3 > 1) $this->constructPagination();
	}

	private function constructPagination()
	{
		$this->content .= '<nav id="pagination" aria-label="Page navigation">
							  <ul class="pagination">
								<li>
								  <a href="'.($this->page > 1 ? '?page='.($this->page - 1) : '#').'" aria-label="Previous">
									<span aria-hidden="true">&laquo;</span>
								  </a>
								</li>';
		for($i = 1; $i < $this->rowCount / 3; $i++)
		{
			$this->content .= '<li class="'.($this->page == $i ? 'active' : '').'"><a href="?page='.$i.'&sort='.$this->sort.'">'.$i.'</a></li>';
		}
		$this->content .= '		<li>
								  <a href="'.'?page='.($this->page + 1).'" aria-label="Next">
									<span aria-hidden="true">&raquo;</span>
								  </a>
								</li>
							  </ul>
							</nav>';
	}

	private function constructSortButtons()
	{
		$this->content .= '
		<div id="sorts">
		<label>Sort by:</label>
		<a href="?page='.$this->page.'&sort='.self::SORT_NAME.'" class="btn btn-primary" role="button">Name</a>
		<a href="?page='.$this->page.'&sort='.self::SORT_MAIL.'" class="btn btn-primary" role="button">Mail</a>
		<a href="?page='.$this->page.'&sort='.self::SORT_STATUS.'" class="btn btn-primary" role="button">Status</a>
		<a href="?page='.$this->page.'&sort='.self::SORT_NONE.'" class="btn btn-primary" role="button">None</a>
		</div>
		';
	}
}