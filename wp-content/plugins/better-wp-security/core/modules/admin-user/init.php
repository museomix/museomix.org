<?php

class ITSEC_Admin_User_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'admin-user';
	protected $_name = 'Rename Admin User';
	protected $_desc = 'Rename the admin user or change user ID 1.';
}
new ITSEC_Admin_User_Module_Init();
