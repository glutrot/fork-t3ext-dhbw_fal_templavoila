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
namespace DHBW\DhbwFalTemplavoila;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('templavoila', 'mod1/index.php'));
 
 /**
 *
 * @author Jan Kristoffer Roth <roth@dhbw-mosbach.de>
 */
class TemplavoilaMod1IndexXClass extends \tx_templavoila_module1 {
	/**
	 * Rendering the preview of content for Page module.
	 *
	 * @param    array $previewData : Array with data from which a preview can be rendered.
	 * @param    array $elData : Element data
	 * @param    array $ds_meta : Data Structure Meta data
	 * @param    string $languageKey : Current language key (so localized content can be shown)
	 * @param    string $sheet : Sheet key
	 *
	 * @return    string        HTML content
	 */
	function render_previewData($previewData, $elData, $ds_meta, $languageKey, $sheet) {
		global $LANG;

		$this->currentElementBelongsToCurrentPage = $elData['table'] == 'pages' || $elData['pid'] == $this->rootElementUid_pidForContent;

		// General preview of the row:
		$previewContent = is_array($previewData['fullRow']) && $elData['table'] == 'tt_content' ? $this->render_previewContent($previewData['fullRow']) : '';

		// Preview of FlexForm content if any:
		if (is_array($previewData['sheets'][$sheet])) {

			// Define l/v keys for current language:
			$langChildren = intval($ds_meta['langChildren']);
			$langDisable = intval($ds_meta['langDisable']);
			$lKey = $langDisable ? 'lDEF' : ($langChildren ? 'lDEF' : 'l' . $languageKey);
			$vKey = $langDisable ? 'vDEF' : ($langChildren ? 'v' . $languageKey : 'vDEF');

			foreach ($previewData['sheets'][$sheet] as $fieldData) {

				if (isset($fieldData['tx_templavoila']['preview']) && $fieldData['tx_templavoila']['preview'] == 'disable') {
					continue;
				}

				$TCEformsConfiguration = $fieldData['TCEforms']['config'];
				$TCEformsLabel = $this->localizedFFLabel($fieldData['TCEforms']['label'], 1); // title for non-section elements

				if ($fieldData['type'] == 'array') { // Making preview for array/section parts of a FlexForm structure:;
					if (is_array($fieldData['childElements'][$lKey])) {
						$subData = $this->render_previewSubData($fieldData['childElements'][$lKey], $elData['table'], $previewData['fullRow']['uid'], $vKey);
						$previewContent .= $this->link_edit($subData, $elData['table'], $previewData['fullRow']['uid']);
					} else {
						// no child elements found here
					}
				} else { // Preview of flexform fields on top-level:
					$fieldValue = $fieldData['data'][$lKey][$vKey];

					if ($TCEformsConfiguration['type'] == 'group') {
						if ($TCEformsConfiguration['internal_type'] == 'file') {
							// Render preview for images:
							$thumbnail = \t3lib_BEfunc::thumbCode(array('dummyFieldName' => $fieldValue), '', 'dummyFieldName', $this->doc->backPath, '', $TCEformsConfiguration['uploadfolder']);
							$previewContent .= '<strong>' . $TCEformsLabel . '</strong> ' . $thumbnail . '<br />';
						} elseif ($TCEformsConfiguration['internal_type'] === 'db') {
							if (!$this->renderPreviewDataObjects) {
								$this->renderPreviewDataObjects = $this->hooks_prepareObjectsArray('renderPreviewDataClass');
							}
							if (isset($this->renderPreviewDataObjects[$TCEformsConfiguration['allowed']])
								&& method_exists($this->renderPreviewDataObjects[$TCEformsConfiguration['allowed']], 'render_previewData_typeDb')
							) {
								$previewContent .= $this->renderPreviewDataObjects[$TCEformsConfiguration['allowed']]->render_previewData_typeDb($fieldValue, $fieldData, $previewData['fullRow']['uid'], $elData['table'], $this);
							}
						}
					//added
					} elseif ($TCEformsConfiguration['type'] == 'inline' && $TCEformsConfiguration['foreign_table'] == 'sys_file_reference' && $TCEformsConfiguration['foreign_match_fields']['fieldname'])	{
						// Render preview for FAL images:
						$table = $elData['table'];
						$fieldName = $TCEformsConfiguration['foreign_match_fields']['fieldname'];

						$tempTCAConfig = $GLOBALS['TCA'][$table]['columns'][$fieldName]['config'];

						// simulate TCA for FAL-field
						$GLOBALS['TCA'][$table]['columns'][$fieldName]['config'] = $TCEformsConfiguration;

						$thumbnail = \TYPO3\CMS\Backend\Utility\BackendUtility::thumbCode(
							array('uid' => $elData['uid']),
							$table,
							$fieldName,
							$this->doc->backPath,
							'',
							null,
							0,
							'',
							'128x64'
						);

						$GLOBALS['TCA'][$table]['columns'][$fieldName]['config'] = $tempTCAConfig;

						$previewContent .= '<strong>' . $TCEformsLabel . '</strong> ' . $thumbnail . '<br />';
					//END added
					} else {
						if ($TCEformsConfiguration['type'] != '') {
							// Render for everything else:
							$previewContent .= '<strong>' . $TCEformsLabel . '</strong> ' . (!$fieldValue ? '' : $this->link_edit(htmlspecialchars(\t3lib_div::fixed_lgd_cs(strip_tags($fieldValue), 200)), $elData['table'], $previewData['fullRow']['uid'])) . '<br />';
						}
					}
				}
			}
		}

		return $previewContent;
	}
}
?>