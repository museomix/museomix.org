<?php

class ITSEC_Hide_Backend_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'hide-backend';
	protected $_name = 'Hide Backend';
	protected $_desc = 'Move the WordPress admin location (not recommended).';
}
new ITSEC_Hide_Backend_Module_Init();
