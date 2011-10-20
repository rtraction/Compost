<?php

/**
 * Validate an image upload from the $_FILES variable
 * @param string $fileName the name of the submitted file
 * @return boolean
 */
function validate_image_upload($fileName){
	if (isset($_FILES[$fileName]) && strlen($_FILES[$fileName]['name']) > 0) {
		if ($_FILES[$fileName]['error']) {
			return false;
		}
		if (!in_array($_FILES[$fileName]['type'],array('image/jpeg', 'image/png', 'image/gif'))) {
			return false;
		}
	}
	return true;
}

/**
 * Get the corrisponding error message to the upload error number
 * @param int $errorNumber
 * @return string
 */
function get_file_upload_error($errorNumber){
	switch ($errorNumber) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
		case 0:
			return '';
        default:
            return 'Unknown upload error ' . $errorNumber;
    }
}
