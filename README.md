
# HOW TO USE

1. Update the following variables in the script with your FTP server details:
    - `$ftpHost`: The hostname or IP address of the FTP server.
    - `$ftpUser`: The username for the FTP server.
    - `$ftpPassword`: The password for the FTP server.
    - `$remoteDir`: The remote directory to synchronize.
    - `$localDir`: The local directory to synchronize.

2. Place this script in the root directory of your project.

3. Run the script by visiting a URL linked to it (for example: `www.yourwebsite.com/syncftp.php` ) using a PHP interpreter (e.g. `php file-sync.php`).

# CRON JOB (automatic synchronization)

If you want to run the script in the background, you can use a cron job to execute it at regular intervals. Usually, you can set up a cron job directly on your hosting provider's control panel or by using the `crontab` command on a Linux server.

Infos about CRON JOB: [https://www.ionos.com/digitalguide/server/configuration/cron-jobs-in-linux/](https://www.ionos.com/digitalguide/server/configuration/cron-jobs-in-linux/)

# Infos

Local = your server where this script is running

Remote = the FTP server (source) from where you want to sync files

# File Synchronization Script

This script is used to synchronize files between a remote FTP server and a local directory. It performs the following tasks:

1. Connects to the FTP server using the provided credentials.
2. Lists all the files in the remote directory recursively.
3. Lists all the files in the local directory recursively.
4. Compares the lists of remote and local files to determine which files need to be added or removed.
5. Deletes empty folders in the local directory.
6. Transfers the files from the remote directory to the local directory, if they are missing or outdated.
7. Displays the status of each file transfer.

## Usage

1. Update the following variables in the script with your FTP server details:
    - `$ftpHost`: The hostname or IP address of the FTP server.
    - `$ftpUser`: The username for the FTP server.
    - `$ftpPassword`: The password for the FTP server.
    - `$remoteDir`: The remote directory to synchronize.
    - `$localDir`: The local directory to synchronize.

2. Run the script using a PHP interpreter.

Note: Make sure you have the necessary permissions to read and write files on both the remote and local directories.

## Dependencies

This script requires a PHP interpreter and the FTP extension enabled.

## Error Handling

If any errors occur during the FTP connection or file transfer process, an error message will be displayed.

## USECASES
I've create this script to Syncronize a shared folder on a Synology NAS to a FTP folder on a hosting provider not using WebDav or any other Synology compatible protocol.