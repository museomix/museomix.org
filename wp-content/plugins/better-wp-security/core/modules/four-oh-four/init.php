<?php

class ITSEC_404_Detection_Module_Init extends ITSEC_Module_Init {
	protected $_id   = '404-detection';
	protected $_name = '404 Detection';
	protected $_desc = 'Detect 404s and take action on them.';
}
new ITSEC_404_Detection_Module_Init();
