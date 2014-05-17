<?php

class UserDT
{
	public $id;		// bigint(20)
	public $username;	// varchar(30)
	public $first_name;	// varchar(30)
	public $last_name;	// varchar(30)
	public $email;		// varchar(30)
	public $password;	// varchar(30)
	public $role;		// smallint(1)
	public $register_date;	// timestamp
	public $last_login;	// timestamp
	public $gender;		// varchar(1)
	public $about_me;
	public $events_upcoming;
	public $events_interested_in;
	public $company_news;
	public $network_activity;
	public $confirmed;
	public $act_code;
}

?>
