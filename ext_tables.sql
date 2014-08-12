#
# Table structure for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata (
	copyright_notice text NOT NULL,
	aperture_value float unsigned DEFAULT '0' NOT NULL,
	shutter_speed_value int(11) unsigned DEFAULT '0',
	iso_speed_ratings varchar(24) DEFAULT '' NOT NULL,
	camera_model varchar(255) DEFAULT '' NOT NULL,
	exposure_time int(11) DEFAULT '0' NOT NULL,
	flash varchar(24) DEFAULT '' NOT NULL,
	metering_mode varchar(24) DEFAULT '' NOT NULL,
	horizontal_resolution int(11) DEFAULT '0' NOT NULL,
	vertical_resolution int(11) DEFAULT '0' NOT NULL,
);