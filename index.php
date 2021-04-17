<?php

/**
 * This is terrible. But it is simple and works. So it shall be.
 */

# Environment variables.
$hostname = getenv('SHELLY_HOSTNAME');
$username = getenv('SHELLY_HTTP_USERNAME');
$password = getenv('SHELLY_HTTP_PASSWORD');

$url = 'http://' . $hostname . '/meter/0';

$curl = curl_init();

# Basic HTTP Authentication (if set).
if (!empty($username) && !empty($password)) {
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
}

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

# Call the URL with curl.
$result = curl_exec($curl);
if (curl_errno($curl)) {
  $error_message = curl_error($curl);
}
curl_close($curl);

# Handle errors.
if (isset($error_message)) {
  echo 'Error: ' . $error_message;
  return;
}

# Parse Shelly Plug's JSON payload and return metrics.
$results = json_decode($result);

?>
# HELP power Current real AC power being drawn, in Watts
# TYPE power gauge
power <?php print $results->power; ?>

# HELP is_valid Whether power metering self-checks OK
# TYPE is_valid gauge
is_valid <?php print $results->is_valid; ?>

# HELP total Total energy consumed by the attached electrical appliance in Watt-minute
# TYPE total gauge
total <?php print $results->total; ?>
