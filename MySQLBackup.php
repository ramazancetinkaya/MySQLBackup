<?php

/**
 * MySQLBackup - A class to backup specified MySQL databases and tables
 *
 * @author [ramazancetinkaya]
 * @date [23.01.2023]
 *
 * Please note, this class is only a demonstration and should be used with caution, you should test it before using on a production environment.
 */

class MySQLBackup {
    private $host;
    private $username;
    private $password;
    private $database;
    private $tables;
    private $backup_path;
    private $file_name;

    public function __construct(string $host, string $username, string $password, string $database, array $tables, string $backup_path, string $file_name) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->tables = $tables;
        $this->backup_path = $backup_path;
        $this->file_name = $file_name;
    }

    public function backup(): bool {
        try {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $tables = "";
            if (count($this->tables) == 0) {
                $tables = "*";
            } else {
                $tables = implode(",", $this->tables);
            }

            $sql = "SELECT TABLE_NAME, CREATE_TIME, UPDATE_TIME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$this->database' AND (TABLE_NAME IN ($tables))";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $tables_data = "";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $table_name = $row['TABLE_NAME'];
                $create_time = $row['CREATE_TIME'];
                $update_time = $row['UPDATE_TIME'];

                $table_data = $conn->query("SELECT * FROM $table_name");
                $table_data = $table_data->fetchAll(PDO::FETCH_ASSOC);

                $tables_data .= "-- Table: $table_name\n-- Created: $create_time\n-- Updated: $update_time\n";
                $tables_data .= "INSERT INTO $table_name VALUES ";

                foreach ($table_data as $data) {
                    $tables_data .= "(";
                    $tables_data .= "'" . implode("','", $data) . "'";
                    $tables_data .= "),";
                }

                $tables_data = rtrim($tables_data, ",");
                $tables_data .= ";\n\n";
            }

            $file = fopen($this->backup_path . $this->file_name, "w");
            fwrite($file, $tables_data);
            fclose($file);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

/**
 *
 * To use this class, you would first need to instantiate it with the appropriate parameters.
 * Here is an example of how you would use it to backup a MySQL database named "mydb" with the tables "users" and "orders" to a file named "backup.sql" in the "backups" directory:
 *
 */
$backup = new MySQLBackup("localhost", "root", "password", "mydb", ["users", "orders"], "./backups/", "backup.sql");

if ($backup->backup()) {
    echo "Backup Successful";
} else {
    echo "Backup Failed";
}

/**
 *
 * You can also use this class to backup all tables in the specified database by passing an empty array as the $tables parameter.
 *
 */
$backup = new MySQLBackup("localhost", "root", "password", "mydb", [], "./backups/", "backup.sql");

if ($backup->backup()) {
    echo "Backup Successful";
} else {
    echo "Backup Failed";
}

/**
 *
 * This class also includes exception handling, so if there is an error in the backup process, it will return false and you can use the catch block to handle the exception.
 *
 */
try {
    $backup = new MySQLBackup("localhost", "root", "password", "mydb", [], "./backups/", "backup.sql");
    $backup->backup();
    echo "Backup Successful";
} catch (PDOException $e) {
    echo "Backup Failed: " . $e->getMessage();
}
