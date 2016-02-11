<?php

class ITSEC_Backup_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'backup';
	protected $_name = 'Backup';
	protected $_desc = 'Backup your database.';
}
new ITSEC_Backup_Module_Init();
