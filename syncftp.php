<?php

$ftpHost = "bstuder.synology.me";
$ftpUser = "fdsdocumentationsyncftp";
$ftpPassword = "]'5Q..sadfas";
$remoteDir = "/";
$localDir = "./public_files";
$fileTypes = '.';

// Function to list remote files recursively
function listRemoteFiles($remoteDir, $remoteFileList, $fileTypes)
{
    global $conn;
    unset($list, $files, $folders);
    @ftp_chdir($conn, $remoteDir);
    ftp_raw($conn, 'OPTS UTF8 ON'); // Enable UTF-8 support
    $list = ftp_nlist($conn, $remoteDir);
    $files = [];
    $folders = [];
    foreach ($list as $entry) {
        if (@ftp_chdir($conn, $entry)) {
            $folders[] = $entry;
            ftp_chdir($conn, $remoteDir);
        } else {
            $files[] = $entry;
        }
    }
    foreach ($files as $file) {
        if (stristr($file, $fileTypes) !== false) {
            $finalFile = basename($file);
            $remoteFileList[] = $file;
        }
    }
    foreach ($folders as $folder) {
        $remoteFileList = listRemoteFiles($folder, $remoteFileList, $fileTypes);
    }
    return $remoteFileList;
}

// Function to list local files recursively
function listLocalFiles($directory, $localFileList, $fileTypes)
{
    global $localDir;
    if (strpos($directory, $localDir) === false) {
        $directory = $localDir . '/' . $directory;
    }
    if ($handle = opendir($directory)) {
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                if (is_dir($directory . '/' . $file)) {
                    $subFilesArr = listLocalFiles($directory . '/' . $file, $localFileList, $fileTypes);
                    $localFileList = array_merge($localFileList, $subFilesArr);
                } else {
                    if (stristr($file, $fileTypes)) {
                        $localFileList[] = trim(str_replace($localDir, '', $directory . '/' . $file));
                    }
                }
            }
        }
        closedir($handle);
    }
    return array_unique($localFileList);
}

// Function to delete empty folders recursively
function deleteEmptyFolders($directory)
{
    if (!is_dir($directory)) {
        return;
    }
    $files = scandir($directory);
    if (count($files) <= 2) {
        rmdir($directory);
        return;
    }
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $path = $directory . '/' . $file;
        if (is_dir($path)) {
            deleteEmptyFolders($path);
        }
    }
}

// Function to synchronize files
function fileSync($remoteFileList, $localFileList)
{
    global $conn, $remoteDir, $localDir;
    if (empty($localFileList)) {
        $toBeAdded = $remoteFileList;
    } else {
        $toBeAdded = array_diff($remoteFileList, $localFileList);
    }
    $toBeRemoved = array_diff($localFileList, $remoteFileList);
    deleteEmptyFolders($localDir);
    if (empty($toBeRemoved) && empty($toBeAdded)) {
        echo '<b style="color: green; font-size: 50px;">All files are up to date</b><br>';
    }
    foreach ($toBeRemoved as $removeFile) {
        unlink($localDir . $removeFile);
    }
    foreach ($toBeAdded as $addFile) {
        $folderPath = dirname($addFile);
        if (!is_dir($localDir . $folderPath)) {
            mkdir($localDir . $folderPath, 0777, true);
        }
        $fp = fopen($localDir . $addFile, 'w');
        $getFile = $remoteDir . $addFile;
        if (ftp_fget($conn, $fp, $getFile, FTP_BINARY) || ftp_fget($conn, $fp, iconv('ISO-8859-1', 'UTF-8', $getFile), FTP_BINARY)) {
            echo 'Successfully transferred ' . $addFile . '!<br>';
            fclose($fp);
        }else{
            echo '<span style="color:red">Error transferring ' . $addFile . '!<br></span>';
        }
    }
    return 1;
}

try {
    $conn = ftp_connect($ftpHost);
    $login = ftp_login($conn, $ftpUser, $ftpPassword);
    $mode = ftp_pasv($conn, true);
    if ((!$conn) || (!$login) || (!$mode)) {
        throw new Exception("FTP connection has failed !");
    }
    $remoteFileList = listRemoteFiles($remoteDir, [], $fileTypes);
    $localFileList = listLocalFiles($localDir, [], $fileTypes);
    $result = fileSync($remoteFileList, $localFileList);
    ftp_close($conn);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

?>
