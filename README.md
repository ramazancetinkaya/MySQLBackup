# MySQLBackup
A class that takes database backup written in PHP.

## To use this class, you would first need to instantiate it with the appropriate parameters.

Here is an example of how you would use it to backup a MySQL database named "mydb" with the tables "users" and "orders" to a file named "backup.sql" in the "backups" directory:

```php
$backup = new MySQLBackup("localhost", "root", "password", "mydb", ["users", "orders"], "./backups/", "backup.sql");

if ($backup->backup()) {
    echo "Backup Successful";
} else {
    echo "Backup Failed";
}
```

You can also use this class to backup all tables in the specified database by passing an empty array as the $tables parameter.

```php
$backup = new MySQLBackup("localhost", "root", "password", "mydb", [], "./backups/", "backup.sql");

if ($backup->backup()) {
    echo "Backup Successful";
} else {
    echo "Backup Failed";
}
```

This class also includes exception handling, so if there is an error in the backup process, it will return false and you can use the catch block to handle the exception.

```php
try {
    $backup = new MySQLBackup("localhost", "root", "password", "mydb", [], "./backups/", "backup.sql");
    $backup->backup();
    echo "Backup Successful";
} catch (PDOException $e) {
    echo "Backup Failed: " . $e->getMessage();
}
```

### Author

**Ramazan Çetinkaya**

* [github/ramazancetinkaya](https://github.com/ramazancetinkaya)

### License

Copyright © 2023, [Ramazan Çetinkaya](https://github.com/ramazancetinkaya).
