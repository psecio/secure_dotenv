<?php

namespace Psecio\SecureDotenv;

class File
{
    public static function read($path) : array
    {
        $realpath = realpath($path);
        if ($realpath == false || !is_file($path)) {
            throw new \InvalidArgumentException('Invalid path: '.$path);
        }

        $results = parse_ini_file($realpath, true);
        return $results;
    }

    public static function write($data, $path = null)
    {
        $output = '';
        foreach ($data as $index => $data) {
            // See if it's a section
            if (is_array($data)) {
                $output .= '['.$index.']';
                foreach ($data as $i => $d) {
                    $output .= $i.'='.$d."\n";
                }
                $output .= "\n";
            } else {
                $output .= $index.'='.$data."\n";
            }
        }

        if ($path !== null) {
            return file_put_contents($path, $output);
        } else {
            echo $output;
        }
    }
}
