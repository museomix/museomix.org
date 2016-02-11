<?php

class ITSEC_SSL_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'ssl';
	protected $_name = 'SSL';
	protected $_desc = 'Use SSL.';
}
new ITSEC_SSL_Module_Init();
