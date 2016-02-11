<?php

class ITSEC_Strong_Passwords_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'strong_passwords';
	protected $_name = 'Strong Passwords';
	protected $_desc = 'Force users to use strong passwords as rated by the WordPress password meter.';
}
new ITSEC_Strong_Passwords_Module_Init();
