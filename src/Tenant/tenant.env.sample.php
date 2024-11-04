<?php

use Autoframe\Core\Tenant\AfrTenant;

/**
 * HTTP:
 * $_SERVER['HTTP_HOST'] match inside AfrTenant::setHostList
 *
 * CLI:
 * set using argv params; eg: php index.php
 *      -T tenantName
 *      -T="tenantName"
 *      --tenant 'domain.com'
 *      --tenant='sub.domain.tld'
 *
 *      $_ENV['AFR_TENANT_CLI'] ?? getenv('AFR_TENANT_CLI') ?: null;
 *      !! set using ENV, but this is not recommended for multi tenant in CLI calling
 *
 * FALLBACK:
 * If no tenant match found, then we render the first tenant key
 *
 */

AfrTenant::getBaseDirPath() || AfrTenant::setBaseDirPath(__DIR__);

(new AfrTenant('b2b-app-www'))->setProtocolDomainName([
	'www.b2b-app.ro',
	'www.b2b-app.test',
	'b2b-app.ro',
	'b2b-app.test',
	'localhost',
	'127.0.0.1',
])
	->setEnv('dev')
	->setDebug(true)
	->setRoot('/')
	->setTempDir()
	->setHtmlDir()
	->setAssetsDir()
	->autoSetupAndPushTenantConfig();

(new AfrTenant('online-b2b-app'))->setProtocolDomainName([
	'online',
	'online.b2b-app.ro',
	'online.b2b-app.test',
])
	->setEnv('dev')
	->setDebug(true)
	->autoSetupAndPushTenantConfig();
