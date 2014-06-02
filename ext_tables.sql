#
# Table structure for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata (
	horizontal_resolution int(11) DEFAULT "",
	vertical_resolution int(11) DEFAULT "",

	# Images
	aperture_value varchar(10) DEFAULT "",
	shutter_speed_value varchar(10) DEFAULT "",
	flash tinyint(4) DEFAULT "",
	metering_mode tinyint(4) DEFAULT "",
	camera_model varchar(64) DEFAULT "",
	exposure_time int(11) DEFAULT '0',
	iso_speed_ratings int(11) DEFAULT "",
);