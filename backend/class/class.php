<?php


class DirectoryScanner
{
    public static function scanDirRecursive($dir, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, &$results, &$directories, $baseDir = "")
    {
        if (!is_dir($dir)) {
            return;
        }

        $handle = opendir($dir);
        if (!$handle) {
            return;
        }

        $allowedExtensions = $authorise === "*" ? [] : array_map('strtolower', (array)$authorise);
        $ignoreExtensions = array_map('strtolower', (array)$ignore);

        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = "$dir/$file";
            $relativePath = $baseDir ? "$baseDir/$file" : $file;

            if (is_dir($filePath)) {
                if ($returnDirPath) {
                    $directories[] = $relativePath;
                } else {
                    $directories[] = $file;
                }

                if ($recursive) {
                    self::scanDirRecursive($filePath, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories, $relativePath);
                }
            } else {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if ((!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) ||
                    (!empty($ignoreExtensions) && in_array($extension, $ignoreExtensions))
                ) {
                    continue;
                }

                switch ($returnNameType) {
                    case 1:
                        $results[] = $file;
                        break;
                    case 2:
                        $results[] = $filePath;
                        break;
                    default:
                        $results[] = pathinfo($file, PATHINFO_FILENAME);
                        break;
                }
            }
        }

        closedir($handle);
    }

    public static function ss2_dirScanner($dir, $returnNameType = 0, $returnDirPath = false, $authorise = "*", $ignore = [], $recursive = false)
    {
        $results = [];
        $directories = [];

        self::scanDirRecursive($dir, $returnNameType, $returnDirPath, $authorise, $ignore, $recursive, $results, $directories);

        return [
            'files' => $results,
            'directories' => $directories
        ];
    }
}
