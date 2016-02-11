<?php

class ITSEC_Away_Mode_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'away-mode';
	protected $_name = 'Away Mode';
	protected $_desc = 'Turn off logins while you are away.';
}
new ITSEC_Away_Mode_Module_Init();
