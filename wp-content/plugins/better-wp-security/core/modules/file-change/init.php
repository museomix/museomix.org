<?php

class ITSEC_File_Change_Module_Init extends ITSEC_Module_Init {
	protected $_id   = 'file-change';
	protected $_name = 'File Change';
	protected $_desc = 'Detect File Changes.';
}
new ITSEC_File_Change_Module_Init();
