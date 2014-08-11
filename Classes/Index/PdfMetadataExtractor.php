<?php
namespace Fab\Metadata\Index;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\ExtractorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Metadata\Utility\Unicode;

// Add auto-loader for Zend PDF library
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('metadata') . '/Resources/Private/ZendPdf/vendor/autoload.php');

/**
 * Service dealing with metadata extraction of images.
 */
class PdfMetadataExtractor implements ExtractorInterface {

	/**
	 * Returns an array of supported file types;
	 * An empty array indicates all filetypes
	 *
	 * @return array
	 */
	public function getFileTypeRestrictions() {
		return array();
	}

	/**
	 * Get all supported DriverClasses
	 * Since some extractors may only work for local files, and other extractors
	 * are especially made for grabbing data from remote.
	 * Returns array of string with driver names of Drivers which are supported,
	 * If the driver did not register a name, it's the classname.
	 * empty array indicates no restrictions
	 *
	 * @return array
	 */
	public function getDriverRestrictions() {
		return array();
	}

	/**
	 * Returns the data priority of the extraction Service.
	 * Defines the precedence of Data if several extractors
	 * extracted the same property.
	 * Should be between 1 and 100, 100 is more important than 1
	 *
	 * @return integer
	 */
	public function getPriority() {
		return 15;
	}

	/**
	 * Returns the execution priority of the extraction Service
	 * Should be between 1 and 100, 100 means runs as first service, 1 runs at last service
	 *
	 * @return integer
	 */
	public function getExecutionPriority() {
		return 15;
	}

	/**
	 * Checks if the given file can be processed by this Extractor
	 *
	 * @param File $file
	 * @return boolean
	 */
	public function canProcess(File $file) {
		return TRUE;
	}

	/**
	 * The actual processing TASK
	 * Should return an array with database properties for sys_file_metadata to write
	 *
	 * @param File $file
	 * @param array $previousExtractedData optional, contains the array of already extracted data
	 * @return array
	 */
	public function extractMetaData(File $file, array $previousExtractedData = array()) {
		$metadata = array();
		$title = $file->getProperty('title');
		if (empty($title)) {
			$metadata = array('title' => 'foo');
		}
		return $metadata;
	}


	////////////////////////////////////////////////////////
	// OLD CODE BELOW TO BE SORTED OUT
	////////////////////////////////////////////////////////

	/**
	 * Performs the service processing
	 *
	 * @return boolean
	 */
	public function process() {

		$this->out = array();

		if ($inputFile = $this->getInputFile()) {

			try {

				$pdf = \ZendPdf\PdfDocument::load($inputFile);

				if (is_object($pdf)) {

					$this->out['title'] = $pdf->properties['Title'];
					$this->out['creator'] = $pdf->properties['Author'];
					$this->out['description'] = $pdf->properties['Subject'];
					$this->out['keywords'] = $pdf->properties['Keywords'];
					$this->out['creator_tool'] = $pdf->properties['Creator'];
					$this->out['creation_date'] = $this->parsePdfDate($pdf->properties['CreationDate']);
					$this->out['modification_date'] = $this->parsePdfDate($pdf->properties['ModDate']);

					$this->out = Unicode::convert($this->out);
				}
			} catch (\Exception $e) {

				/** @var $loggerManager \TYPO3\CMS\Core\Log\LogManager */
				$loggerManager = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager');

				/** @var $logger \TYPO3\CMS\Core\Log\Logger */
				$message = sprintf('Metadata: PDF indexation raised an exception %s.', $e->getMessage());
				$loggerManager->getLogger(get_class($this))->warning($message);
			}

		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}

	/**
	 * Convert a PDF date string into a timestamp
	 * PDF date: D:YYYYMMDDHHmmSSOHH'mm'
	 *
	 * @param string $pdfDate
	 * @return int
	 */
	protected function parsePdfDate($pdfDate) {

		// Remove starting D: if exists
		$pdfDate = preg_replace("/D:/", "", $pdfDate);

		// Split the PDF Date into two parts if a timezone indication exists (Z = time is indicated in UTC)
		$pdfDateArray = preg_split("/(?=[-+Z]\d{2}'\d{2}')/", $pdfDate, -1);

		// Check if timezone indication exists
		if (isset($pdfDateArray[1])) {

			$timeOffset = preg_replace('[\D]', '', $pdfDateArray[1]);

			switch (substr($pdfDateArray[1], 0, 1)) {
				case '-':
					$timeOffset = '-' . $timeOffset;
					break;
				case '+':
					$timeOffset = '+' . $timeOffset;
					break;
			}
		}

		// Build an interpretable datetime
		if (isset($timeOffset)) {
			$pdfDate = $pdfDateArray[0] . $timeOffset;
			$pdfDateTimeFormat = \DateTime::createFromFormat('YmdGisO', $pdfDate);
		} else {
			$pdfDateTimeFormat = \DateTime::createFromFormat('YmdGis', $pdfDateArray[0]);
		}

		$pdfDateTime = NULL;
		if (is_object($pdfDateTimeFormat)) {
			// Form it to a UNIX timestamp
			$pdfDateTime = $pdfDateTimeFormat->format('U');
		}

		return $pdfDateTime;
	}
}
