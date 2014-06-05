<?php

//Github webhooks should come from a range within 192.30.252.0/22
$github_public_cidrs = array('192.30.252.0/22');

$remoteIp = $_SERVER['REMOTE_ADDR'];
$payload = json_decode(stripslashes($_POST['payload']));

if (ip_in_cidrs($remoteIp, $github_public_cidrs)) {
  if ($payload->ref === 'refs/heads/staging') {
      $output = `cd /var/www/staging-listen360 && git pull`;
  } elseif ($payload->ref === 'refs/heads/master') {
      $output = `cd /var/www/listen360 && git pull`;
  }
}


//returns true when a specified IP address ($ip) falls within a defined range ($cidrs)
function ip_in_cidrs($ip, $cidrs) {
	$ipu = explode('.', $ip);

	foreach ($ipu as &$v) {
		$v = str_pad(decbin($v), 8, '0', STR_PAD_LEFT);
	}

	$ipu = join('', $ipu);
	$result = FALSE;

	foreach ($cidrs as $cidr) {
		$parts = explode('/', $cidr);
		$ipc = explode('.', $parts[0]);

		foreach ($ipc as &$v) $v = str_pad(decbin($v), 8, '0', STR_PAD_LEFT); {
			$ipc = substr(join('', $ipc), 0, $parts[1]);
			$ipux = substr($ipu, 0, $parts[1]);
			$result = ($ipc === $ipux);
		}

		if ($result) break;
	}

return $result;
}

?>