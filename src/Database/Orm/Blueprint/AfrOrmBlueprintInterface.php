<?php

namespace Autoframe\Core\Database\Orm\Blueprint;

interface AfrOrmBlueprintInterface
{
	//todo setari coloane
	//fillable = editable = !readonly
	//lazy_load la get
	//belongs to many => autogenerare tabel legatura referinta celalalt tabel


	//https://www.youtube.com/watch?v=V5xINbA-z9o
	//https://medium.com/@magenta2127/how-to-design-mysql-database-model-for-1-to-1-1-to-n-and-m-to-n-relationship-fbedd434aeab
	//junction table N-N https://stackoverflow.com/questions/13533260/nn-relation-mysql
	const TBL_META_PIVOTS = 'aMetaPivots';  //['colName'=>['1-1','1-N','N-1','N-N']]// NS\__CLASS__?? connAlias, dbN,tbl, col +
	// functie nume coloana return obj/arr todo reverse map din connAlias.tbl.col in ns.class/modul???
	//foreignIdFor(CLASS)
	//col->foreignIdFor(CLASS)
	//col->foreignId('col')->constrained(othertbl.col)
	//classObj->foreign('col')->refferences(othertbl.col)
	//classObj->foreign('col')->refferences(col)->on(othertbl)

	const QUOTED = 'sQuoted';
	const END_OFFSET = 'iEndOffset';

	const FQCN = 'sFQCN'; // = NS\ENTITY_NAME
	const ENTITY_NAME = 'sEntityName';

	const CON_ALIAS = 'sConnAlias';
	const PDO_CONNECTION = 'rPDO';

	const DB_LIST = 'aDbList';
	const DB_NAME = 'sDbName';
	const DB_TABLES = 'aDbTables';
	const DB_VIEWS = 'aDbViews';

	const PREFER_DB_NAME = 'sPreferredDbName'; //on parse sql from db or actual fallback / setter

	const TBL_NAME = 'sTblName';
	const COLUMNS = 'aColumns';
	const RAW = 'aRAW';
	const COL_NAME = 'sColName';

	const FK_FQCN = 'FK_FQCN';
	const FK_METHOD = 'FK_MethodAccessor';
	const FK_RELATIONSHIP = 'FK_Relationship';
	const FK_DB_NAME = 'FK_DbName';
	const FK_TBL_NAME = 'FK_Tbl';
	const FK_COL = 'FK_Col';
	const PIVOT_TABLE_NAME = 'Pvt_Tbl_Name';
	const PIVOT_COL_FOREIGN_SELF_KEY = 'Pvt_Col_Self';
	const PIVOT_COL_OWNER_TARGET_KEY = 'Pvt_Col_Target';
	const PIVOT_FQCN = 'Pvt_FQCN';
	const PIVOT_METHOD = 'sMethod';

	const M_LAZY_HYDRATE = 'bLazyHydrate'; //fetched on access / demand, but this will be cached
	const M_USE_CACHE = 'bUseCache'; //table cache flag
	const M_HIDDEN = 'bHidden'; //not fetch-able, not shown as property
	const M_UPDATE_READONLY = 'bUpdateReadonly'; //not updatable
	const M_RAM_BOX_ACCESS_PERSISTENCE = 'iRamBoxAccessPersistence';

	const ENGINE = 'sEngine';
	const TBL_TYPE = 'sTblType'; // 'BASE TABLE', 'VIEW' ,'SYSTEM VIEW' (INFORMATION_SCHEMA).
	const TBL_TYPE_TEMPORARY = 'bTmpTbl'; // 'BASE TABLE', 'VIEW' ,'SYSTEM VIEW' (INFORMATION_SCHEMA).
	const AUTOINCREMENT = 'sAutoIncrement';


	const PRIMARY_KEY = 'sPrimaryKey';
	const UNIQUE_KEYS = 'aUniqueKeys';
	const FULLTEXT_KEYS = 'aTextKeys';
	const CONSTRAINTS = 'aConstraintsKeys';

	const KEY = 'sTblKey';
	const KEYS = 'aTblKeys';
	const COL_TYPE = 'sType';
	const COL_TYPE_ATTRIBUTES = 'aTypeAttributes';
	const COL_ATTR = 'aColumnAttributes';
	const COL_UNSIGNED = 'sUnsigned';
	const COL_DEFAULT = 'sDefault';
	const COL_EXTRA = 'sExtra';
	const FLAGS = 'aFlags';

	const AFR_META = 'aAfrMeta';
	const BLUEPRINT_VERSION = 'sBPVersion';
	const BLUEPRINT_SOURCE = 'sBPSource';

	const JSON_FLAG = 'aJsonFlag';
	const KEY_TREE = 'sTree'; //  KEY `2b_smallint` (`2b_smallint`,`date`) USING BTREE,

	const IF_NOT_EXIST = 'bfNotExists';
	const CHARSET = 'sCharset';
	const COLLATION = 'sCollation';
	const COMMENT = 'sComment';
	const LEFT_OVER_PARSE = 'aLeftOver';

	const SPECS = 'aSpecs';
	const TIMEZONE = 'sTimezone';


	// Doctrine\DBAL\Types\Types;
	/*
		 public const ARRAY = 'array';
		public const ASCII_STRING         = 'ascii_string';
		public const BIGINT               = 'bigint';
		public const BINARY               = 'binary';
		public const BLOB                 = 'blob';
		public const BOOLEAN              = 'boolean';
		public const DATE_MUTABLE         = 'date';
		public const DATE_IMMUTABLE       = 'date_immutable';
		public const DATEINTERVAL         = 'dateinterval';
		public const DATETIME_MUTABLE     = 'datetime';
		public const DATETIME_IMMUTABLE   = 'datetime_immutable';
		public const DATETIMETZ_MUTABLE   = 'datetimetz';
		public const DATETIMETZ_IMMUTABLE = 'datetimetz_immutable';
		public const DECIMAL              = 'decimal';
		public const FLOAT                = 'float';
		public const GUID                 = 'guid';
		public const INTEGER              = 'integer';
		public const JSON                 = 'json';
		public const OBJECT = 'object'; /// @deprecated Use {@link Types::JSON} instead.
		public const SIMPLE_ARRAY   = 'simple_array';
		public const SMALLINT       = 'smallint';
		public const STRING         = 'string';
		public const TEXT           = 'text';
		public const TIME_MUTABLE   = 'time';
		public const TIME_IMMUTABLE = 'time_immutable';
	 * */
	const D_INT = 'int';
	const D_FLOAT = 'float';
	const D_DOUBLE = 'double';
	const D_STR = 'string';
	const D_BIT = 'int';
	const D_BOOL = 'bool';
	const D_ARR = 'array';
	const D_OBJ = 'object'; //serialize??
	const D_JSON = 'json'; //json encode
	const D_NULL = 'null';

	const D_CAST = 'cast';
	const D_MIN = 'min';
	const D_MAX = 'max';
	const D_UMIN = 'uMin';
	const D_UMAX = 'uMax';
	const D_FIXEDLEN = 'fixedLen';
	const D_MAXLEN = 'maxLen';
	const D_DECIMALS = 'decimals';
	const D_ROUNDED = 'roundNumber';
	const D_PARENTHESIS = 'mParenthesis';
	const D_UPARENTHESIS = 'mUParenthesis';

}