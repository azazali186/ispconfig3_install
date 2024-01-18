<?php

require __DIR__ . '/class.ISPConfigDebian11OS.inc.php';

/**
 * Description of class
 *
 * @author croydon
 */
class ISPConfigDebian12OS extends ISPConfigDebian11OS {
	protected function addBusterBackportsRepo() {
		// not in bullseye
	}

	protected function getRoundcubePackages() {
		return array(
			'roundcube',
			'roundcube-core',
			'roundcube-mysql',
			'roundcube-plugins'
		);
	}

	protected function getPackagesToInstall($section) {
		$packages = parent::getPackagesToInstall($section);

		if($section === 'first') {
			$key = array_search('getmail', $packages, true);
			if($key !== false) {
				unset($packages[$key]);
			}
			$packages[] = 'getmail6';
			$packages[] = 'rsyslog';
		} elseif($section === 'mail') {
			$key = array_search('rar', $packages, true);
			if($key !== false) {
				unset($packages[$key]);
			}
		}

		return $packages;
	}

	protected function isStableSupported() {
		return true;
	}

	protected function shallCompileJailkit() {
		return false;
	}

	protected function getMySQLUserQueries($mysql_root_pw) {
		$escaped_pw = preg_replace('/[\'\\\\]/', '\\$1', $mysql_root_pw);
		$queries = array(
			'DELETE FROM mysql.user WHERE User=\'\';',
			'DELETE FROM mysql.user WHERE User=\'root\' AND Host NOT IN (\'localhost\', \'127.0.0.1\', \'::1\');',
			'DROP DATABASE IF EXISTS test;',
			'DELETE FROM mysql.db WHERE Db=\'test\' OR Db=\'test\\_%\';',
			'SET PASSWORD FOR \'root\'@\'localhost\' = PASSWORD(\'' . $escaped_pw . '\');',
			'FLUSH PRIVILEGES;'
		);

		return $queries;
	}

	protected function installMailman($host_name) {
		ISPConfigLog::info('ISPConfig does not yet support mailman3 and mailman2 is no longer available in Debian 12.', true);
		return;
	}

	protected function getSystemPHPVersion() {
		return '8.2';
	}

	protected function installRoundcube($mysql_root_pw) {
		parent::installRoundcube($mysql_root_pw);
		$this->replaceLine('/etc/roundcube/config.inc.php', "\$config['smtp_host']", "\$config['smtp_host'] = 'localhost:25';");
	}
}
