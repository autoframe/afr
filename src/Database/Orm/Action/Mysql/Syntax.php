<?php

namespace Autoframe\Core\Database\Orm\Action\Mysql;

trait Syntax
{
	protected static array $__aSpace = [' ', "\t", "\n", "\r"];
	protected static array $__aEol = ["\n", "\r\n", "\r"];
	protected static array $__aQuotes = ['`', "'", '"'];

	protected static string $__sTildeQuot = '`';
	protected static string $__sSingleQuot = "'";
	protected static string $__sDoubleQuot = '"';
	protected static string $__sBackSlash = '\\';
	protected static string $__sComma = ',';
	protected static string $__sEndStatement = ';';
	protected static string $__sGlueDbTblCol = '.';

	protected static array $__aKeyWordsTbl = [
		0 => [
			'PRIMARY KEY',
			'UNIQUE KEY', //UNIQUE KEY `dddd` (`id`,`t`)
			'FULLTEXT KEY', //FULLTEXT KEY `iii` (`t`)
			'KEY',  //    'INDEX', //TODO test for index parsing!!!!
			//   'SPATIAL',
			//'CONSTRAINT',
			//'FOREIGN KEY',
			//'REFERENCES',
		],
		1 => [
			'CONSTRAINT',// CONSTRAINT `AFR5A9FCD1F9EF7E20F0CD5BE539234675F FOREIGN KEY (`AFR24AA0458D543F3E317E7D257DD50B796) REFERENCES `AFR9627F67FB2652C1FFFE7D34A89038AF0 (`AFRD2C23D1B4FEEEC456D72CDC94CC44595) ON DELETE RESTRICT ON UPDATE NO ACTION
			'FOREIGN KEY',
			'REFERENCES',
			'ON DELETE',
			'ON UPDATE',
		],
		2 => [
			'RESTRICT',
			'CASCADE',
			'NO ACTION',
			'SET NULL',
		]
	];
	public static array $__aDataTypeMap = [

		//(4) A 1-byte integer, signed range is -128 to 127, unsigned range is 0 to 255
		'TINYINT' => [
			self::D_CAST => self::D_INT,
			self::D_MIN => -128,
			self::D_MAX => 127,
			self::D_UMIN => 0,
			self::D_UMAX => 255,
			self::D_PARENTHESIS => '3',
			self::D_UPARENTHESIS => '4',
		],

		//(6) A 2-byte integer, signed range is -32,768 to 32,767, unsigned range is 0 to 65,535
		'SMALLINT' => [
			self::D_CAST => self::D_INT,
			self::D_MIN => -32768,
			self::D_MAX => 32767,
			self::D_UMIN => 0,
			self::D_UMAX => 65535,
			self::D_PARENTHESIS => '5',
			self::D_UPARENTHESIS => '6',
		],

		//(9) A 3-byte integer, signed range is -8,388,608 to 8,388,607, unsigned range is 0 to 16,777,215
		'MEDIUMINT' => [
			self::D_CAST => self::D_INT,
			self::D_MIN => -8388608,
			self::D_MAX => 8388607,
			self::D_UMIN => 0,
			self::D_UMAX => 16777215,
			self::D_PARENTHESIS => '8',
			self::D_UPARENTHESIS => '9',
		],

		//(11) A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295
		'INT' => [
			self::D_CAST => self::D_INT,
			self::D_MIN => -2147483648,
			self::D_MAX => 2147483647,
			self::D_UMIN => 0,
			self::D_UMAX => 4294967295,
			self::D_PARENTHESIS => '10',
			self::D_UPARENTHESIS => '11',
		],

		//(20) An 8-byte integer, signed range is -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807, unsigned range is 0 to 18,446,744,073,709,551,615
		'BIGINT' => [
			self::D_CAST => self::D_INT,
			self::D_MIN => -9223372036854775808,
			self::D_MAX => 9223372036854775807,
			self::D_UMIN => 0,
			self::D_UMAX => 18446744073709551615,
			self::D_PARENTHESIS => '20',
			self::D_UPARENTHESIS => '20',
		],

		//An alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE
		'SERIAL' => [
			self::D_CAST => self::D_INT,
			self::D_MIN => 0,
			self::D_MAX => 18446744073709551615,
			self::D_UMIN => 0,
			self::D_UMAX => 18446744073709551615,
			self::D_PARENTHESIS => '20',
			self::D_UPARENTHESIS => '20',
		],

		//A fixed-point number (M, D) - the maximum number of digits (M) is 65 (default 10), the maximum number of decimals (D) is 30 (default 0)
		'DECIMAL' => [
			//TODO: refactor to correct values
			self::D_CAST => self::D_FLOAT,
			self::D_DECIMALS => 30,
			self::D_ROUNDED => 65,
			self::D_PARENTHESIS => ['15,2', '12,4'],
			self::D_UPARENTHESIS => ['16,2', '20,4']
		],

		//A small floating-point number, allowable values are -3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38
		'FLOAT' => [
			self::D_CAST => self::D_FLOAT,
			self::D_DECIMALS => 8,
			self::D_ROUNDED => 18,
			self::D_PARENTHESIS => ['32,8'], //TODO: refactor to correct values
			self::D_UPARENTHESIS => ['32,8'],
		],


		//A double-precision floating-point number, allowable values are -1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308
		'DOUBLE' => [
			self::D_CAST => self::D_DOUBLE,
			self::D_DECIMALS => 16,
			self::D_ROUNDED => 18,
			self::D_PARENTHESIS => ['40,16'], //TODO: refactor to correct values
			self::D_UPARENTHESIS => ['40,16'],

		],

		//Synonym for DOUBLE (exception: in REAL_AS_FLOAT SQL mode it is a synonym for FLOAT)
		'REAL' => [
			//TODO: refactor to correct values
			self::D_CAST => self::D_DOUBLE,
			self::D_DECIMALS => 16,
			self::D_ROUNDED => 18,
			self::D_PARENTHESIS => ['40,16'], //TODO: refactor to correct values
			self::D_UPARENTHESIS => ['40,16'],
		],

		//A bit-field type (M), storing M of bits per value (default is 1, maximum is 64)
		'BIT' => [
			self::D_CAST => self::D_BIT,
			self::D_PARENTHESIS => '1',
		],

		//A synonym for TINYINT(1), a value of zero is considered false, nonzero values are considered true
		'BOOLEAN' => [
			self::D_CAST => self::D_BOOL,
			self::D_PARENTHESIS => '1',
		],

		//A date, supported range is 1000-01-01 to 9999-12-31
		'DATE' => [
			self::D_CAST => self::D_STR,
			self::D_FIXEDLEN => 10,
		],

		//A date and time combination, supported range is 1000-01-01 00:00:00 to 9999-12-31 23:59:59
		'DATETIME' => [
			self::D_CAST => self::D_STR,
			self::D_FIXEDLEN => 19,
		],

		//A timestamp, range is 1970-01-01 00:00:01 UTC to 2038-01-09 03:14:07 UTC, stored as the number of seconds since the epoch (1970-01-01 00:00:00 UTC)
		'TIMESTAMP' => [
			self::D_CAST => self::D_STR,
			self::D_FIXEDLEN => 19,
		],

		//A time, range is -838:59:59 to 838:59:59
		'TIME' => [
			self::D_CAST => self::D_STR,
			self::D_FIXEDLEN => [9, 10],
		],

		//A year in four-digit (4, default) or two-digit (2) format, the allowable values are 70 (1970) to 69 (2069) or 1901 to 2155 and 0000
		'YEAR' => [
			self::D_CAST => self::D_STR,
			self::D_FIXEDLEN => [2, 4],
		],

		//A fixed-length (0-255, default 1) string that is always right-padded with spaces to the specified length when stored
		'CHAR' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 255,
			self::D_PARENTHESIS => '1',

		],

		//A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size
		'VARCHAR' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 65535,
			self::D_PARENTHESIS => '255',

		],

		//A TEXT column with a maximum length of 255 (2^8 - 1) characters, stored with a one-byte prefix indicating the length of the value in bytes
		'TINYTEXT' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 255,
		],

		//A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes
		'TEXT' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 65535,
		],

		//A TEXT column with a maximum length of 16,777,215 (2^24 - 1) characters, stored with a three-byte prefix indicating the length of the value in bytes
		'MEDIUMTEXT' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 16777215,
		],

		//A TEXT column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) characters, stored with a four-byte prefix indicating the length of the value in bytes
		'LONGTEXT' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 4294967295,
		],

		//Similar to the CHAR type, but stores binary byte strings rather than non-binary character strings
		'BINARY' => [
			self::D_CAST => self::D_BIT,
		],

		//Similar to the VARCHAR type, but stores binary byte strings rather than non-binary character strings
		'VARBINARY' => [
			self::D_CAST => self::D_BIT,
		],

		//A BLOB column with a maximum length of 255 (2^8 - 1) bytes, stored with a one-byte prefix indicating the length of the value
		'TINYBLOB' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 255,
		],

		//A BLOB column with a maximum length of 65,535 (2^16 - 1) bytes, stored with a two-byte prefix indicating the length of the value
		'BLOB' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 65535,
		],

		//A BLOB column with a maximum length of 16,777,215 (2^24 - 1) bytes, stored with a three-byte prefix indicating the length of the value
		'MEDIUMBLOB' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 16777215,
		],

		//A BLOB column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) bytes, stored with a four-byte prefix indicating the length of the value
		'LONGBLOB' => [
			self::D_CAST => self::D_STR,
			self::D_MAXLEN => 4294967295,
		],

		//An enumeration, chosen from the list of up to 65,535 values or the special '' error value
		'ENUM' => [
			self::D_CAST => self::D_STR,
			self::D_PARENTHESIS => "'a','b','c',''",

		],

		//A single value chosen from a set of up to 64 members; Comma ',' values are forbidden from the set
		'SET' => [
			self::D_CAST => self::D_ARR,
			self::D_PARENTHESIS => "'d','e','f',''",
		],

		//`json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`json`)),
		//Stores and enables efficient access to data in JSON (JavaScript Object Notation) documents
		'JSON' => [
			self::D_CAST => self::D_JSON,//TODO: encapsulateCellValue(
			//    self::D_CAST => self::D_STR,
			self::D_MAXLEN => 4294967295,
		],


		//    'GEOMETRY',//A type that can store a geometry of any type
		//    'POINT',//A point in 2-dimensional space
		//    'LINESTRING',//A curve with linear interpolation between points
		//    'POLYGON',//A polygon
		//    'MULTIPOINT',//A collection of points
		//    'MULTILINESTRING',//A collection of curves with linear interpolation between points
		//    'MULTIPOLYGON',//A collection of polygons
		//    'GEOMETRYCOLLECTION',//A collection of geometry objects of any type


	];

	protected static array $__aDataFlags = [
		'NOT NULL',
		'UNSIGNED',
		'AUTO_INCREMENT',
		'ZEROFILL',
	];
	protected static array $__aDataAttributes = [

		'CHARACTER SET',
		'COLLATE',
		'DEFAULT',
		'COMMENT',
		'ON UPDATE', //'ON UPDATE CURRENT_TIMESTAMP()',
		'CHECK',

	];

	protected static array $__aDbAttributes = [

		self::DB_NAME => 'CREATE DATABASE',
		self::CHARSET => 'CHARACTER SET',
		self::COLLATION => 'COLLATE',
		self::COMMENT => 'COMMENT',
	];

	/*
	 *  #1075 - Incorrect table definition; there can be only one auto column and it must be defined as a key
	 *  #3719 'utf8' is currently an alias for the character set UTF8MB3, but will be an alias for UTF8MB4 in a future release. Please consider using UTF8MB4 in order to be unambiguous.
	 *  #1681 Integer display width is deprecated and will be removed in a future release.   ADICA INT fara paranteze. se merge pe auto
	 CREATE TABLE IF NOT EXISTS `muta#ble` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `fkid` int(11) NOT NULL,
	  `int_defa``ult'_none_unsigned` int(10) unsigned NOT NULL,
	#  `int_default_none_signed` int(10) unsigned zerofill NOT NULL,
	  `int_default_none_null` int(11) DEFAULT NULL,
	  `int_default_null_null` int(11) DEFAULT NULL,
	  `1b_tinyint` TINYINT(4) NOT NULL DEFAULT '22',
	  `2b_smallint` smallint(6) NOT NULL,
	  `3b_mediumint` mediumint(9) NOT NULL,
	  `8b_bigint` bigint(20) NOT NULL,
	  `decimalX` decimal(10,2) NOT NULL,
	  `floatX` float NOT NULL,
	  `double_floatX2` double NOT NULL,
	  `date` date NOT NULL,
	  `dt` datetime NOT NULL,
	  `t` time NOT NULL,
	  `ts` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	  `char_0_255_padded_with_spaces` char(2) NOT NULL,
	  `varchar_0-65535` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	  `tinytxt_2_1` tinytext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	  `txt_2_2` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	  `medtxt_2_3` mediumtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	  `longtxt_2_4` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	  `binary_as_chr_but_01` binary(4) NOT NULL,
	  `varbinary_as_varchr_but_01` varbinary(6) NOT NULL,
	  `tinyblob_2_1` tinyblob DEFAULT NULL COMMENT 'defau''lt tr"ebui`e s)a fi(e null',
	  `blob_2_16` blob DEFAULT NULL COMMENT 'default trebuie sa fie null',
	  `medblob_2_24` mediumblob DEFAULT NULL COMMENT 'default trebuie sa fie null',
	  `longblob_2_32` longblob DEFAULT NULL COMMENT 'default trebuie sa fie null',
	  `enum_64k` enum('a','b','c','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	  `set_max_64_vals` set('d','e','f','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	  `json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`json`)),
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `1b_tinyint_key` (`1b_tinyint`,t),
	  KEY `fkmut1` (`fkid`),
	  KEY `2b_smallint` (`2b_smallint`),
	  CONSTRAINT `fkmut1` FOREIGN KEY (`fkid`) REFERENCES `mutable` (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=UTF8MB4 COLLATE=UTF8MB4_general_ci COMMENT='yha-comment!';

	 * */


}