<form id="formeditor" method="post">
	<div class="input-group">
		<div>
			<label>Name:</label>
			<input disabled="disabled" value="<?php echo $name ?>" name="name" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
		</div>
		<div>
			<label>Email:</label>
			<input disabled="disabled" value="<?php echo $mail ?>" name="mail" class="form-control" placeholder="example@com.ua" aria-describedby="basic-addon1">
		</div>
		<textarea name="content" class="form-control" placeholder="Very important task..." aria-describedby="basic-addon1"><?php echo $text ?></textarea>
		<input type="hidden" name="task_id" value="<?php echo $id ?>">
		<div>
			<input class="btn btn-primary btn-md" type="submit" value="Publish">
			<input type="checkbox" value="<?php echo $checked ?>" name="checked">
		</div>
	</div>
</form>