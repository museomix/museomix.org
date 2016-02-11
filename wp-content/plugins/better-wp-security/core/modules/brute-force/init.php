<?php

class ITSEC_Brute_Force_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'brute_force';
	protected $_name = 'Brute Force';
	protected $_desc = 'Protect against brute force attempts.';
}
new ITSEC_Brute_Force_Module_Init();
