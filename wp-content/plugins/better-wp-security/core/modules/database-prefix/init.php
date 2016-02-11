<?php

class ITSEC_Database_Prefix_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'database-prefix';
	protected $_name = 'Database Prefix';
	protected $_desc = 'Change database prefix';
}
new ITSEC_Database_Prefix_Module_Init();
