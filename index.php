<?php

namespace Aranea;

error_reporting(-1);

chdir(dirname(__FILE__));

require 'vendor/autoload.php';

$help = <<<EOF
Usage: php index.php -u [url] [options]...

Arguments:
  -d
  --debug
      Print debug messages.

  -h
  --help
      Print this help message.

  --ignore-nofollow
      Ignore robots.txt and rel="nofollow" on links

  -H
  --span-hosts
      Enable spanning across hosts when doing recursive retrieving.

  -l <depth>
  --level <depth>
      Specify maximum recursion depth level.

	--max-redirects <number>
      Follow no more than <number> redirects per page.

	--max-urls <number>
      Terminate after having found <number> URLs.

  -o <directory>
  --output-directory <directory>
      Log retrieved data to files in a directory.

  -q
  --quiet
      Turn off regular output.

  -r
  --recursive
      Turn on recursive retrieving. The default maximum depth is 5.

  -T <seconds>
  --timeout <seconds>
      Set the network timeout to <seconds> seconds.

  --connect-timeout <seconds>
      Set the connect timeout to <seconds> seconds.

  -u <url>
  --url <url>
      Retrieve a URL.

  -v
  --verbose
      Turn on verbose output.

  -w <seconds>
  --wait <seconds>
      Wait the specified number of seconds between the retrievals.
EOF;

$opts = getopt('dhHl:o:qrT:u:vw:', array(
	'connect-timeout:',
	'debug',
	'help',
	'level:',
	'ignore-nofollow',
	'output-directory:',
	'max-redirect:',
	'max-urls:',
	'recursive',
	'quiet',
	'span-hosts',
	'timeout:',
	'url:',
	'verbose',
	'wait:',
	));

$url = isset($opts['url']) ? $opts['url'] : ( isset($opts['u']) ? $opts['u'] : '' );

if ( isset($opts['help']) || isset($opts['h']) || !$url ) {
	fwrite(STDERR, $help . "\n");

	exit(1);
}

Fetcher::$ignoreNoFollow = isset($opts['ignore-nofollow']);
Fetcher::$debug          = isset($opts['debug'])      || isset($opts['d']);
Fetcher::$spanHosts      = isset($opts['span-hosts']) || isset($opts['H']);
Fetcher::$quiet          = isset($opts['quiet'])      || isset($opts['q']);
Fetcher::$recursive      = isset($opts['recursive'])  || isset($opts['r']);
Fetcher::$verbose        = isset($opts['verbose'])    || isset($opts['v']);

if ( isset($opts['connect-timeout']) ) {
	Fetcher::$connectTimeout = $opts['connect-timeout'];
}

if ( isset($opts['max-redirect']) ) {
	Fetcher::$maxRedirect = $opts['max-redirect'];
}

if ( isset($opts['max-urls']) ) {
	Fetcher::$maxUrls = $opts['max-urls'];
}

if ( isset($opts['level']) || isset($opts['l']) ) {
	Fetcher::$maxDepth = isset($opts['level']) ? $opts['level'] : $opts['l'];
}

if ( isset($opts['output-directory']) || isset($opts['o']) ) {
	Fetcher::$outputDirectory = isset($opts['output-directory']) ? $opts['output-directory'] : $opts['o'];
}

if ( isset($opts['timeout']) || isset($opts['T']) ) {
	Fetcher::$timeout = isset($opts['timeout']) ? $opts['timeout'] : $opts['T'];
}

if ( isset($opts['wait']) || isset($opts['w']) ) {
	Fetcher::$wait = isset($opts['wait']) ? $opts['wait'] : $opts['w'];
}

try {
	Fetcher::fetch($url);
} catch ( Exception $e ) {
	if ( !Fetcher::$quiet ) {
		fwrite(STDERR, $e->getMessage() . "\n");
	}

	exit(1);
}

exit(0);
