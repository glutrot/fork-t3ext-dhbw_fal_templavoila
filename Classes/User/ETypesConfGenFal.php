<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009-2014 Jan Kristoffer Roth, <roth@dhbw-mosbach.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 *
 * @author Jan Kristoffer Roth <roth@dhbw-mosbach.de>
 */
class ETypesConfGenFal {

    function handleFal(&$params, &$tvObj) {
		$elArray = &$params['elArray'];
		$key = &$params['key'];
		
		switch($elArray[$key]['tx_templavoila']['eType']) {
			case 'fal_page':
				$data = 'page:';
				$table = 'pages';
				break;
			default:
				$data = 'register:tx_templavoila_pi1.parentRec.';
				$table = 'tt_content';
		}

		$elArray[$key]['TCEforms']['label'] = $elArray[$key]['tx_templavoila']['title'];
		$elArray[$key]['TCEforms']['config'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig($key);
		$elArray[$key]['tx_templavoila']['TypoScript'] = '
10 = FILES
10 {
	references {
		table = '.$table.'
		uid.cObject = COA
		uid.cObject {
			10 = TEXT
			10 {
				data = '.$data.'uid
				if.isTrue.data = '.$data.'_LOCALIZED_UID
				if.negate = 1
			}

			20 = TEXT
			20 {
				data = '.$data.'_LOCALIZED_UID
				if.isTrue.data = '.$data.'_LOCALIZED_UID
			}
		}
		fieldName = '.$key.'
	}

	renderObj = COA
	renderObj {
		10 = IMAGE
		10 {
			file {
				import.data = file:current:publicUrl
			}
			altText.data = file:current:alternative
			titleText.data = file:current:title
		}
	}
}
';
		$elArray[$key]['tx_templavoila']['proc']['HSC'] = 0;
	}
}

?>