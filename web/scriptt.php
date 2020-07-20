<pre>
<?php

$output = $return_var= null;
$command = __DIR__.'/../bin/console -v cache:clear -e=prod';
echo "Running: $command\n";
exec($command, $output, $return_var);

if (!empty($output) && is_array($output)) {
    echo "Output:\n";
    foreach ($output as $line) {
        echo $line."\n";
    }
} else {
    echo 'Cache could not be cleared: '.var_export($return_var, true);
}