<?php
	class ChatRoom {
		public $id;
		public $dateCreation;
		public $dateLastNewMessage;
		public $users = array();
		public $messages = array();
		public $dateEnd;
		public $noMoreThanOneVisitor;
		public $userId;
	}
	class ChatMessage {
		public $message;
		public $hash;
		public $userId;
		public $date;
	}
?>