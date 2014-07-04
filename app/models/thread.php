<?php
class Thread extends AppModel
{
	public $validation = array(
		'title' => array(
			'length' => array(
				'validate_between', MIN_LENGTH, MAX_LENGTH,
			),
		)
	);

	public static function getAll()
	{
		$threads = array();
		$db = DB::conn();
		$rows = $db->rows('SELECT * FROM thread');
		foreach ($rows as $row) {
			$threads[] = new Thread($row);
		}
		return $threads;
	}
	public static function get($id)
	{
		$db = DB::conn();
		$row = $db->row('SELECT * FROM thread WHERE id = ?', array($id));
		return new self($row);
	}
	public function create(Comment $comment)
	{
		$this->validate();
		$comment->validate();
		if ($this->hasError() || $comment->hasError()) {
			throw new ValidationException('invalid thread or comment');
		}
		$db = DB::conn();
		$db->begin();
		$db->query('INSERT INTO thread SET title = ?, created = NOW()', array($this->title));
		$newID = $db->lastInsertId();
		$this->id = $newID;
		$comment->id = $newID;
		// write first comment at the same time
		$comment->write($comment);
		$db->commit();
	}

}