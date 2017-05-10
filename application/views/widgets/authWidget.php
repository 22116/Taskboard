<?php

class authWidget extends Widget
{
	public function __construct()
	{
		if(authModel::$isAuth)
		{
			$this->content = '<div id="welcome-block" class="label"><p> Hello, <strong>' . authModel::$user->getLogin() . '</strong></p>
						<form action="/login/out">
							<input type="submit" value="Log out" class="button btn-block"></div>
						</form>';
		}
		else
		{
			$this->content = '
				<form id="loginform-small" method="post" action="/login/in">
					<div class="input-group">
						<span class="input-group-addon" id="basic-addon1">@</span>
						<input name="login" type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
					</div>
					<div class="input-group">
						<input name="password" type="password" class="form-control" placeholder="password" aria-describedby="basic-addon1">
					</div>
					<div>
						<input type="submit" value="Sign in" class="button btn-block">
					</div>
				</form>
			';
		}
	}
}