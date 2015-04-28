<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE === 'BE')	{

// add the new field presets to the user prefs
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
templavoila.eTypes {
	eType {
		fal_page {
			label = FAL Reference (Page)
		}
		fal_fce {
			label = FAL Reference (FCE)
		}
	}
	defaultTypes_misc := addToList(custom,fal_page,fal_fce)
}
');
}
?>
