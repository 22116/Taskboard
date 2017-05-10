<?php

interface ISaver
{
	public function save();
	public static function findById(int $id);
}