<?php
declare(strict_types=1);

namespace Unit\Database;

use Autoframe\Core\Database\Connection\AfrDbConnectionManagerClass;
use Autoframe\Core\Database\Connection\AfrDbConnectionManagerInterface;
use PDO;
use PHPUnit\Framework\TestCase;

class AfrDbConnectionManagerX extends AfrDbConnectionManagerClass{
    public function parseDSNInfoX(string $sDSN): array
    {
        return $this->parseDSNInfo($sDSN);
    }
}

class AfrDbConnectionManagerTest extends TestCase
{
    protected AfrDbConnectionManagerX $oManager;

	public static function insideProductionVendorDir(): bool
    {
        return strpos(__DIR__, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false;
    }

    protected function setUp(): void
    {
        $this->oManager = AfrDbConnectionManagerX::getInstance();
    }
	
	protected function tearDown(): void
    {
        //cleanup between tests for static
    }
    /*
  $pdo = new PDO('cubrid:host=$host;port=8001;dbname=platin',  $user, $pass); #cubrid

  $pdo = new PDO("sybase:host=$host;dbname=$dbname, $user, $pass");# PDO_DBLIB was linked against the Sybase ct-lib libraries
  $pdo = new PDO("mssql:host=$host;dbname=$dbname, $user, $pass"); # PDO_DBLIB was linked against the Microsoft SQL Server libraries
  $pdo = new PDO("dblib:host=$host;dbname=$dbname, $user, $pass"); # PDO_DBLIB was linked against the FreeTDS libraries.

  $pdo = new PDO("firebird:host=$host;dbname=$dbname", $user, $pass);# PDO_FIREBIRD without the port

  $pdo = new PDO("ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=testdb;HOSTNAME=11.22.33.444;PORT=56789;PROTOCOL=TCPIP;", $user, $pass);#  PDO_IBM DSN

  $pdo = new PDO("informix:host=host.domain.com; service=9800; database=common_db; server=ids_server; protocol=onsoctcp; EnableScrollableCursors=1", "testuser", "tespass"); #PDO_INFORMIX

  $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;sPdoFqcnClass=\PDO;charset=utf8mb4", $user, $pass);# MySQL with PDO_MYSQL
  $pdo = new PDO("mysql:unix_socket=/tmp/mysql.sock;dbname=testdb", $user, $pass);# MySQL with PDO_MYSQL

  $pdo = new PDO("oci:dbname=192.168.10.145:1521/mydb;charset=CL8MSWIN1251", "testuser", "tespass"); #Oracle Instant Client PDO_OCI

   #PDO_ODBC
  $pdo = new PDO("odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=localhost;PORT=50000;DATABASE=SAMPLE;PROTOCOL=TCPIP;UID=db2inst1;PWD=ibmdb2;", "testuser", "tespass"); #IBM DB2 uncataloged connection
  $pdo = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=C:\\db.mdb;Uid=Admin", "testuser", "tespass"); #Microsoft Access PDO_ODBC

  $pdo = new PDO('pgsql:host=$host;port=5432;dbname=platin',  $user, $pass); #postgre

  $pdo = new PDO("sqlite:my/database/path/database.db"); # SQLite Database
  $pdo = new PDO("sqlite::memory:"); # SQLite Database array(PDO::ATTR_PERSISTENT => true)
  $pdo = new PDO("sqlite:"); # SQLite Database
 */
    public static function parseDSNInfoProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $aReturn = [];
//        $aReturn[] = [ 'host','port','dbname','DSN' ];

        $aReturn[] =  self::seeder([ '33.33.33.33',8003,'platin','cubrid:host=33.33.33.33;port=8003;dbname=platin' ]);
        $aReturn[] =  self::seeder([ '','','','cubrid:host=$host;port=$port;dbname=$dbname' ]);
        $aReturn[] =  self::seeder([ '','','','cubrid:host=$host;' ]);

        $aReturn[] =  self::seeder([ '','','','sybase:host=$host;dbname=$dbname' ]);
        $aReturn[] =  self::seeder([ '',3333,'mydbx','sybase:host=$host;dbname=mydbx;port=3333' ]);

        $aReturn[] =  self::seeder(['11.22.33.44',56789, 'testdb','ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=testdb;HOSTNAME=11.22.33.44;PORT=56789;PROTOCOL=TCPIP;' ]);
        $aReturn[] =  self::seeder(['','', '','ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$dbname;HOSTNAME=$host;PORT=$port;PROTOCOL=TCPIP;' ]);
        $aReturn[] =  self::seeder(['','', '','ibm:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=$host;PORT=$port;PROTOCOL=TCPIP;' ]);

        $aReturn[] =  self::seeder(['host.domain.com',9801, 'common_db','informix:host=host.domain.com; service=9801; database=common_db; server=ids_server; protocol=onsoctcp; EnableScrollableCursors=1' ]);
        $aReturn[] =  self::seeder(['','', '','informix:host=$host; service=$port; database=$dbname; server=ids_server; protocol=onsoctcp; EnableScrollableCursors=1' ]);
        $aReturn[] =  self::seeder(['','', '','informix:host=$host; database=$dbname; server=ids_server; protocol=onsoctcp; EnableScrollableCursors=1' ]);
        $aReturn[] =  self::seeder(['','', '','informix:host=$host; service=$port; server=ids_server; protocol=onsoctcp; EnableScrollableCursors=1' ]);

        $aReturn[] =  self::seeder(['host.domain.com',3307, 'common_db','mysql:host=$host;port=3307;dbname=common_db' ]);//charset=utf8mb4
        $aReturn[] =  self::seeder(['',3306, 'common_db','mysql:host=$host;port=3306;dbname=common_db' ]);
        $aReturn[] =  self::seeder(['','', '','mysql:host=$host;port=$port' ]);

        $aReturn[] =  self::seeder(['/tmp/mysql.sock',null, 'testdb','mysql:unix_socket=/tmp/mysql.sock;dbname=testdb' ]);
        $aReturn[] =  self::seeder(['/tmp/mysql.sock',null, '','mysql:unix_socket=/tmp/mysql.sock' ]);

        $aReturn[] =  self::seeder(['192.168.10.145',1521, 'mydb','oci:dbname=192.168.10.145:1521/mydb' ]);
        $aReturn[] =  self::seeder(['','', '','oci:dbname=$host:$port/$dbname' ]);
        $aReturn[] =  self::seeder(['','', '','oci:dbname=$host/$dbname' ]);
        $aReturn[] =  self::seeder(['','', '','oci:dbname=$host:$port' ]);
        $aReturn[] =  self::seeder(['','', '','oci:dbname=$host;charset=CL8MSWIN1251' ]);

        $aReturn[] =  self::seeder(['localhost',50000, 'SAMPLE','odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=localhost;PORT=50000;DATABASE=SAMPLE;PROTOCOL=TCPIP;UID=db2inst1;PWD=ibmdb2;' ]);
        $aReturn[] =  self::seeder(['','', '','odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=$host;PORT=$port;DATABASE=$dbname;PROTOCOL=TCPIP;UID=db2inst1;PWD=ibmdb2;' ]);
        $aReturn[] =  self::seeder(['','', '','odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=$host;PROTOCOL=TCPIP;UID=db2inst1;PWD=ibmdb2;' ]);

        $aReturn[] =  self::seeder(['C:\\db.mdb',null, '','odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=C:\\db.mdb;Uid=Admin' ]);

        $aReturn[] =  self::seeder(['host.name',54322, 'common_db','pgsql:host=host.name;port=54322;dbname=common_db' ]);
        $aReturn[] =  self::seeder(['','', 'common_db','pgsql:host=$host;dbname=common_db' ]);
        $aReturn[] =  self::seeder(['','', '','pgsql:host=$host;port=$port' ]);

        $aReturn[] =  self::seeder(['my/database/path/database.db',null, 'database.db','sqlite:my/database/path/database.db' ]);
        $aReturn[] =  self::seeder([':memory:',null, 'memory','sqlite::memory:' ]); //array(PDO::ATTR_PERSISTENT => true)
        $aReturn[] =  self::seeder(['',null, 'sqlite','sqlite:' ]);


        return $aReturn;

    }

    public static function seeder(array $aModel): array
    {
        //$aModel = [$driver, 'host','port','dbname','DSN' ];
        $sDriver = strtolower(trim(explode(':',$aModel[3])[0]));
        $aModel = array_merge([$sDriver],$aModel);
        $host = [
            '192.168.10.145',
            'localhost',
            '127.0.0.1',
            '11.22.33.44',
        ][rand(0,3)];
        if (strpos($aModel[4], '$host') !== false) {
            $aModel[1] = $host;
            $aModel[4] = str_replace('$host', $host, $aModel[4]);
        }

        if (//inline port
            strpos($aModel[4], '$port') === false && (
                strpos(strtolower($aModel[4]), 'port=') !== false ||
                strpos(strtolower($aModel[4]), 'service=') !== false
            )
        ) {
        //    $aModel[2] = intval(substr(explode('port=', strtolower($aModel[4]))[1], 0, 5));
        } elseif (strpos($aModel[4], '$port') !== false) {
            $aModel[2] = [8001, 2638, 1433, 1433, 3050, 56789, 9800, 3306, 1521, 50000, 5432,][rand(0, 10)];
            $aModel[4] = str_replace('$port', (string)$aModel[2], $aModel[4]);
        } else {
            $aModel[2] = AfrDbConnectionManagerClass::DRIVERS_PORTS[$sDriver];
        }

        $dbname = ['myDb','DaTa-Base'][rand(0, 1)];
        if (strpos($aModel[4], '$dbname') !== false) {
            $aModel[3] = $dbname;
            $aModel[4] = str_replace('$dbname', $dbname, $aModel[4]);
        }

        return $aModel;

    }
    /**
     * @test
     * @dataProvider parseDSNInfoProvider
     */
    public function parseDSNInfoTest(string $driver, string $host, $port, string $dbname, string $DSN): void
    {
        $aInfo = $this->oManager->parseDSNInfoX($DSN);
        $sInfo = json_encode($aInfo);

        $this->assertSame($driver, $aInfo[AfrDbConnectionManagerInterface::DRIVER],$sInfo);
        $this->assertSame($host, $aInfo[AfrDbConnectionManagerInterface::HOST],$sInfo);
        $this->assertSame($port, $aInfo[AfrDbConnectionManagerInterface::PORT],$sInfo);
        $this->assertSame($dbname, $aInfo[AfrDbConnectionManagerInterface::DBNAME],$sInfo);
    }

    /**
     * @test
     */
    public function connectAliasTest(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers())) {
            $this->assertSame(1, 1);
            return;
        }

        $sLocalDB = __DIR__.DIRECTORY_SEPARATOR.'sqlite.db';
        $sAlias = 'sql.lite';
        $sAlias2 = 'sql.lite2';
        $this->oManager->defineConnectionAlias( $sAlias,  'sqlite:'.$sLocalDB );
        $this->oManager->defineConnectionAlias( $sAlias2,  'sqlite:'.$sLocalDB );

        $aAliasInfo = $this->oManager->getAliasInfo($sAlias2);
        //$this->assertSame($this->oManager->aAliases, $aAliasInfo, print_r($aAliasInfo, true));
        $this->assertSame($aAliasInfo[AfrDbConnectionManagerInterface::INFO][AfrDbConnectionManagerInterface::DRIVER], 'sqlite', print_r($aAliasInfo, true));
        $this->assertSame($aAliasInfo[AfrDbConnectionManagerInterface::INFO][AfrDbConnectionManagerInterface::HOST], $sLocalDB, print_r($aAliasInfo, true));


        $oConn1 = $this->oManager->getConnectionByAlias($sAlias);
        $this->assertSame( $oConn1,  $this->oManager->getConnectionByAlias($sAlias),print_r($this->oManager,true));

        $oConn2 = $this->oManager->getConnectionByAlias($sAlias2);
        $this->assertSame($oConn1, $oConn2,print_r($this->oManager,true));
        $this->assertSame($oConn1, $oConn2,print_r($this->oManager,true));


        $this->oManager->flushConnection($sAlias2);
        $this->assertSame($this->oManager->isConnected($sAlias2), false,print_r($this->oManager,true));
        $this->assertSame($this->oManager->isConnected($sAlias), false,print_r($this->oManager,true));

    }

}