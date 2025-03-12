# Lab 3A - PHP Part 1

## Overview

This lab will give you the opportunity to add in some authentication using PHP and allow users to register and log in to your site. In lab 3B we will add the application logic. This lab builds off of the previous labs (as do all of the labs), so make sure you organize your code well so you will be able to read and understand it again later. A MySQL database will be used to store all of the user authentication information.

Our overall setup will be a LAMP stack: a **L**inux machine running an **A**pache server and a **M**ySQL database, serving **P**HP files.

To clarify, your app does not have to be actually functional as a task list by the end of lab 3A. You'll need to be able to log in and out, and register new users. You should be able to hide some content from anyone who isn't logged in, and show it to anyone who *is* logged in. In the next lab (Lab 3B), we'll transpile the JavaScript you wrote in the previous labs into PHP.

## Functionality

- Create a database to store user data
- Implement user authentication for your site
- Hash incoming passwords

## Concepts

- Simple Authentication (username and password)
- Containers
- Separation of Responsibility

## Technologies

- UML
- Docker
- PHP
- MySQL

## Resources

- [Official PHP Documentation](https://www.php.net/)
- [PHP MySQL Introduction](https://www.tutorialrepublic.com/php-tutorial/php-mysql-introduction.php)
- [PHP Sessions](https://www.w3schools.com/php/php_sessions.asp)
- [Prepared Statements](https://www.tutorialrepublic.com/php-tutorial/php-mysql-prepared-statements.php)

## Setup

#### For MAC Users with ARM processors
We have an alternative docker-compose file for those running M1 and M2 based Macs. Since it uses native ARM code it performs better on those computers though the other works due to Intel emulation on MacOS. To use the alternate, delete `docker-compose.yml` then rename `docker-compose-ARM.yml` to `docker-compose.yml`.

### Development Environment 

1. Clone this repo onto your development computer, and open it in VSCode

2. Copy the `.env.example` template as a file called `.env`. Edit the `.env` with your own MySQL username, password, and database

3. Bring up your docker containers

4. Your development server should be running on `http://localhost`, and the development phpMyAdmin should be running on `http://localhost:8080`

5. Set up your database:

    - Log in to **phpMyAdmin** ([`http://localhost:8080`](http://localhost:8080))
    - Make sure that the ``lab_3`` (or what ever it's called in ``.env``) database has been created
    - Create a new table called `'user'` in the `'lab_3'` database with 4 columns
    - Add the following fields:

        | Name        | Type      | Length/Values  | Default         | Index   | A_I |  ...  |
        | ----------- | --------- | -------------- | --------------- | ------- | --- | ----- |
        | `id`        | `INT`     |                | ...             | Primary |  ☒  |  ...  |
        | `username`  | `VARCHAR` | `100`          | ...             | Unique  |  ☐  |  ...  |
        | `password`  | `VARCHAR` | `255`          | ...             | ...     |  ☐  |  ...  |
        | `logged_in` | `BOOLEAN` |                | `As defined: 0` | ...     |  ☐  |  ...  |

        > Note: Selecting the Auto Increment checkbox (`A_I`) will result in a dialogue box asking if you want it to be your primary key. Just click <kbd>Go</kbd>.

    The top of the first image is what a database setup could look like, and the bottom is what the setup of our project is. Notice how:
    - Each database can hold multiple tables
    - Each table can hold multiple columns.

    <img src="./Database 1.jpg" alt="Database Layout" width="400px">

    ---
    Also, PHPMyAdmin, PHP, and MySQL Command Line Interface (CLI) are all different programs that use the same database.
    <img src="./Database 3.png" alt="Database 3" width="400px">

6. Navigate to [http://localhost/actions/health_check.php](http://localhost/actions/health_check.php) to see if your server connected properly to your database.

> When you run `docker-compose down --volumes`, it deletes this database entirely. If you run `docker-compose down` *without* the flag, it won't delete your database. If you do run it with the flag, you'll have to create the `user` table in the database again.

### Production Environment (AWS Live Server)

1. SSH into your AWS EC2 instance environment

2. You should already have **Apache** installed from Lab 1. If not, go back and do it.

3. Install the rest of the LAMP stack (**PHP**, **MariaDB**, and **phpMyAdmin**) using the following command:
    ```sh

    sudo apt update && sudo apt install -y php php-mbstring php-zip php-gd php-json php-curl mariadb-server phpmyadmin

    ```
    As you do this, phpMyAdmin will prompt you for a few things:
    
    1. Which database:<br>
        **WARNING: When the prompt appears, “apache2” is highlighted, but not selected. If you do not hit <kbd>SPACE</kbd> to select Apache, the installer will not move the necessary files during installation. Press <kbd>Space</kbd> to select apache2 (it should look like `[*] apache2`), then <kbd>Enter</kbd>.**
        > Please choose the web server that should be automatically configured to run phpMyAdmin.
        >
        > Web server to reconfigure automatically:
        >
        > [ ] apache2<br>
        > [ ] lighttpd
        >
        > &lt;Ok>
        
    1. Use dbconfig-common:
        > The phpmyadmin package must have a database installed and configured before it can be used. This can be optionally handled with dbconfig-common.
        >
        > If you are an advanced database administrator and know that you want to perform this configuration manually, or if your database has already been installed and configured, you should refuse this option. Details on what needs to be done should most likely be provided in /usr/share/doc/phpmyadmin.
        >
        > Otherwise, you should probably choose this option.
        >
        > Configure database for phpmyadmin with dbconfig-common?
        >
        > &lt;Yes> &lt;No>
        
        Press <kbd>Enter</kbd> to select `<yes>`.
    1. Password for phpMyAdmin:
        > Please provide a password for phpmyadmin to register with the database server. If left blank, a random password will be generated.
        >
        > MySQL application password for phpmyadmin:
        >
        > _____
        >
        > &lt;Ok> &lt;Cancel>
        
        Type in a password, and press <kbd>Enter</kbd>.
        
        > Password confirmation:
        >
        > _____
        >
        > &lt;Ok> &lt;Cancel>
    
        Type in the password again, and press <kbd>Enter</kbd>.

4. Set up your database:

    - Before you can log into **phpMyAdmin** you will need to create a root user with a password.

        - Run the command `sudo mariadb`. This should open a new terminal that looks like this:
        ```bash
        MariaDB [(none)]>
        ```
        - Run the query statement: `SET PASSWORD FOR 'root'@'localhost' = PASSWORD('<yourPassword>');` Making sure to replace `<yourPassword>` with a password that you will **NEVER FORGET!** (when entering `<yourPassword>` remove angle brackets but keep the quotes).
        - Then run `flush privileges;`
        - Run type `exit`
        - You should now be able to go to **phpMyAdmin** in your browser and login with user: `root` and the password you made
    - Log in to **phpMyAdmin** (`http://<aws-ip-address>/phpmyadmin`)
        > If a 404 not found error appears when you go to `http://<aws-ip-address>/phpmyadmin` there is a tip below that will help  
    - Create a new ```lab_3``` database and ```user``` table, exactly matching your development environment

5. Create a new MySQL User with limited privileges to be used in your code using the **phpmyadmin** page:
    - The new user should have the same username and password as what's in the `docker-compose.yml` file, under `MYSQL_USER` and `MYSQL_PASSWORD`
    - Make sure this user (and the root user) is password protected
    - Enable `SELECT`, `INSERT`, `UPDATE`, and `DELETE` in the "Data" section, and only the `CREATE` checkbox in the "Structure" section
    
    *MySQL users != Site users*: 
    - MySQL user accounts are used by database administrators to make changes to the database. Authentication is handled by the database program. These are incredibly powerful and can have devastating consequences for the entire website if compromised. These are the credentials used to log in to PHPMyAdmin.
    - Site users are used by end users to make changes to their account. Authentication for end users is handled by the application, not database itself. These accounts being compromised have consequences for the end users who use the accounts, like personal data being breached. These credentials are used to log in to the website.

6. Navigate to the `/var/www` folder and clone your repo here as well. Change your symbolic link to point to this new directory, and then `cd` into it.
   
7. In the your root folder (the same directory as your `.env.example`), create a file called `.env` and paste the credentials into this file as before, notice the servername changes:

    ```bash
    MYSQL_SERVERNAME=localhost
    MYSQL_USER=youruser
    MYSQL_PASSWORD=yourpassword
    MYSQL_DATABASE=yourdatabase
    ```
8.  Read and use the `apache2_env_setup.sh` file. 

    > Note: you didn't need to source the file in your development environment because docker-compose automatically uses any `.env` files present, and even passes them in as environment variables to our container. It's very important to source this file on the live server because Apache doesn't automatically load it.

9. Restart the Apache service:

    ```
    sudo service apache2 restart
    ```

You should now have a barebones site on both your dev and live servers!

## Instructions

### Step 1: Grab your old HTML

1. On your development environment, paste the HTML from your old project into `index.php`

    > PHP is HTML with bonus syntax that allows you to do scripting. Valid HTML is also valid PHP, but valid PHP is not valid HTML.

### Step 2: Authentication

1. In your project, there is a folder inside of the `src` folder called `views`. Inside of the `views` folder, there are 2 files:
    - `register.php`
        - This will contain a form to register a new user
        - The form needs to ask for a username and a password, and also a field to confirm the password
        - The `action` attribute of the `<form>` tag will be the relative path to the `register_action.php` page you created
    - `login.php`
        - This will contain a form to log in as an existing user
        - The form needs to ask for a username and a password
        - The `action` attribute of the `<form>` tag will be the relative path to the `login_action.php` page you created

2. In your project, there is a folder inside of the `src` folder called `actions`. Inside of the `actions` folder, there are 3 files:

    > Note: This `actions` folder will eventually contain your CRUD operations, each in a seperate file this time.

    - `register_action.php`
        - This will contain the code that adds a new user to the database:
            1. Check if the passwords match
                - If they don't:
                    - Redirect to the register page (`register.php`)
                    - Display a descriptive error on the register form
                - If they do:
                    - Continue to next step
            2. Check if the username exists in the database
                - If it does:
                    - Redirect to the register page (`register.php`)
                    - Display a descriptive error on the register form
                - If it doesn't:
                    - Insert a new user into the database (set the value of `logged_in` to `true`)
                    - Set session variables for the user (e.g. 'logged_in' = 'true', 'username' = 'joeking', and id as the id in the database)
                    - Redirect to the application (`index.php`)
        > Note: The passwords will need to be hashed before saving it to the database. To do that, use PHP's `password_hash()` function, and make sure to use the current default, bcrypt. DO NOT use `'sha1'`, `'sha2'`, or `'md5'`, as these are no longer cryptographically secure for passwords. If you would like to read more on best security practices for storing passwords, [this article on hashing security](https://crackstation.net/hashing-security.htm) is one of my favorites.
    - `login_action.php`
        - This will contain the code that's associated with logging in:
            1. Check if the username exists in the database
                - If it doesn't:
                    - Redirect to the login page (`login.php`)
                    - Display a descriptive error on the login form
                - If it does:
                    - Continue to next step
            2. Check if the password in the database matches the hashed password that the user provided
                - If it doesn't:
                    - Redirect to the login page (`login.php`)
                    - Display a descriptive error on the login form
                - If it does:
                    - Set session variables for the user (i.e. `'logged_in' = 'yes'` and `'username'='mike'`, as well as the user's `id`)
                    - Update the `logged_in` variable in the database
                    - Redirect to the application (`index.php`)
    - `logout_action.php`
        - This will contain the code that's associated with logging out:
            1. Update the `'logged_in'` variable in the database
            2. Destroy any session variables
            3. Redirect to the login page (`login.php`)
            4. Add a <kbd>Log Out</kbd> button on the `<nav>` bar in your `index.php` file that runs `logout_action.php` when clicked.

3. Add some logic to `index.php` that only shows your application if the user is logged in
    - If they aren't logged in, redirect them to the `login.php` page automatically. This is typically done by checking for session variables and redirecting to a login page if those variables are missing

To recap:

```register.php``` sends a POST request to ```register_action.php```.

```login.php``` sends a POST request to ```login_action.php```. (Think: why wouldn't this be a GET request?).

```register_action.php``` attempts to register a user using the information in the POST request sent by ```register.php```.

```login_action.php``` attempts to log in a user using the information in the POST request sent by ```login.php```.

```logout_action.php``` attempts to log out a user using the information provided in POST request sent by the logout button in ```index.php```.

The **VIEWS** pages are only used by end users while the **ACTION** pages are only used to do database operations and business logic.

# PHP Resources
[IT&C210 Github PHP walkthrough](https://byu-itc-210.github.io/walkthrough/PHP)

[W3Schools echo/print](https://www.w3schools.com/php/php_echo_print.asp)

[W3Schools Superglobal variables](https://www.w3schools.com/php/php_superglobals.asp)

[W3Schools form handling](https://www.w3schools.com/php/php_forms.asp)

[W3Schools MySQL integration](https://www.w3schools.com/php/php_mysql_connect.asp)

[W3Schools MySQL Prepared Statements](https://www.w3schools.com/php/php_mysql_prepared_statements.asp)

[VSCode PHP Extensions list](https://medium.com/@Omojunior11/my-most-used-vs-code-extensions-for-php-282d6f0ac315)

[Prepared SELECT statements in PHP](https://phpdelusions.net/mysqli_examples/prepared_select)

# Tips

## Objected Oriented vs Procedural in PHP
PHP has 2 ways to make queries on a database.

*Procedural*: A procedural function that uses the connection object as an argument.
```php
mysqli_function($conn, $arg1, $arg2);
```

*Object-Oriented*: Using public functions of the connection oject.
```php
$conn->function($arg1, $arg2);
```

Both of these forms are used, but we will be using the **OOP** format.


## Prepared Statements

You are required to use prepared statements, which are SQL statements that are more resistant to SQL injection, so a user can't enter the username ```Robert'); DROP TABLE user;--``` like [this](https://xkcd.com/327/) and lose the whole table. Look at [this](https://www.w3schools.com/php/php_mysql_prepared_statements.asp) W3Schools link and [this](https://www.tutorialrepublic.com/php-tutorial/php-mysql-prepared-statements.php) article to learn about how to use them in PHP and why they are useful.

Here's a sample prepared statements:
```sql
INSERT INTO table_1 (column_1, column_2, column_3) VALUES (?, ?, ?);
```

Then we would use some PHP code to insert data into those placeholder values.

## Refactoring

There are multiple ways to implement the logic in the code. Make sure that your UML diagram reflects your specific implementation.

## Where can I see PHP Errors?

A good practice is to make sure errors are reported while developing. You can accomplish this by adding the following line to the top of any PHP page where you need to see errors:

```php
error_reporting(-1);
```

This will print errors to the browser window as HTML.

Make sure to change it back before pushing to production:

```php
error_reporting(0);
```

## Reasons for MySQL Connection Failure

Here are a few common reasons for a MySQL Connection Failure:

### `Connection refused`

The database isn't set up yet. This should only happen in your development environment. It just means that MySQL server hasn't finished being set up yet. Usually it will go away after 5 minutes.

### `No such file or directory`

The `$mysql_servername` isn't correct. It should be `mariadb` on dev, and `localhost` on production. Use `echo` to see the value of the environment variable, and get it changed in your `.env` file.

### `Access denied for user 'developer'@'192.168.1.10' (using password: YES)`

The `$mysql_password` isn't correct. If you remember the password, change it in your `.env`. If you forgot your password, you may need to reset your user or your database.

## `or die();`

For each PHP statement that does anything with the database you should have an `or die()` next to it so if it errors, you will know it. You do this by adding `or die("Error message");` at the end of your PHP statements (before the `;`). You can also print the MySQL error in the die statement. Here's an example.

```php
$conn->select_db("lab_3") or die("Failed to connect to db: " . $conn->connect_error);
```

## Code Complexity

Many times you might need to check a condition in order to continue in your file. For example, in your `register_action.php`, it might look something like this:

```php
if (/* database connected */) {
    if (/* passwords match */) {
        if (/* username isn't taken */) {
            /* Do register */
        }
    }
}
```

Nested conditionals add to code complexity and they're ugly. Let's look at a better way, which is less complex, prettier, and is easier to find any mistakes:

```php
if (/* database NOT connected */) {
    die();
}
if (/* passwords DON'T match */) {
    die();
}
if (/* username IS taken */) {
    die();
}
/* Do register */
```

You will not be graded on this, but it is good practice to reduce code complexity, especially when your code has `else` blocks.

## phpMyAdmin 404 Not Found Error on Production Environment

For some reason, sometimes phpMyAdmin gets a 404 Not Found Error. Complete these steps and it should start to work!

- Open your enabled site using your favorite editor

    ```sh
    sudo nano /etc/apache2/sites-available/it210_lab.conf
    ```

- Then add the following line at the very bottom:

    ```
    Include /etc/phpmyadmin/apache.conf
    ```

- Then run the following commands:

    ```sh
    sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-available/phpmyadmin.conf

    sudo a2enconf phpmyadmin.conf

    sudo service apache2 reload
    ```

## Apache-php error when running `docker-compose up -d`

When you run `docker-compose up -d` you might get an apache-php error that says **"Message": "Unhandled exception: Filesharing has been cancelled"**. If you do get this error, there is a simple fix to it. You just need to add your project folder to docker's file sharing settings. To do this, click on the docker icon on the task bar to bring up a menu bar. On the menu bar, go to Settings > Resources > File Sharing. Add the path to your project folder by clicking on the blue plus button and navigating to your project folder. Click Apply & Restart, then run `docker-compose up -d` again. 

## How can I see my session variables?

Session variables cannot be seen in the developer console. In order to see what variables are stored in your current session you need to dump them to a webpage. This can be done by by adding the following code:

```php
<?php var_dump($_SESSION); ?>
```

## Error Log on Production Environment

If you get "Internal Server Error" check the apache log files. Simple way is to run `tail /var/log/apache2/error.log` which will give you the last few lines of the log (most recent).

## `header()` & `session_start()` functions and errors

These two functions MUST come before anything is written to the output stream. This means they come before anything is `echo`ed or written outside of the `<?php` or `?>` tag. This includes any white space (space, new line, etc.) outside of the `<?php` tag. Check the beginning of your scripts including any included scripts. The best way to think of how to use this is to put them at the top of the script as much as you can. You will get an error otherwise.

## `mysqli::real_escape_string()`

Although it is not required, it is good to know and understand sanitizing user inputs. Whenever an input from GET or POST or whatever is being used to go into a SQL statement should be sanitized (escaped). Use this function to do that. Example...

```php
$query = "SELECT * FROM user WHERE username = '". $mysqli->real_escape_string($_GET['name']) ."'";
```

## Hashed password format

The ```password_hash()``` function can use multiple hashing algorithms of varying security. According to [documentation](https://www.php.net/manual/en/function.password-hash.php) the default is currently the [bcrypt algorithm](https://en.wikipedia.org/wiki/Bcrypt), but will be updated as more secure algorithms are discovered. Because the specific algorithm might change, it is a good idea to not tie the password length in the database to the length of the current hashing algorithm.

Bcrypt hashes look like `$2y$10$GXRIH4gu37ZCFJ/Rc91.xu7dY5K8RVjZS30pOJxurOz3y71O5ncVa` and are much more cryptographically secure than earlier hash algorithms like `sha1` and `md5`.

The format of the result of bcrypt is `$algorithm$iterations$generatedSaltGeneratedHash`. An example of this is `$2y$10$GXRIH4gu37ZCFJ/Rc91.xu7dY5K8RVjZS30pOJxurOz3y71O5ncVa`. In our case, `2y` is the algorithm, which was run `10` times. The salt is 22 characters long, or in our case, the string `GXRIH4gu37ZCFJ/Rc91.xu`, and the resulting hash is the remaining 31 characters, `7dY5K8RVjZS30pOJxurOz3y71O5ncVa`. Keeping this format allows the ```password_verify()``` function can take just one string and a test password, hash it with the same salt, and verify they match.

# PHP – Part 1 Pass-off (Rubric)

- [ ] 5 Points - First commit is on or before Friday
- [ ] 8 Points - Application is deployed to a live cloud server
- [ ] 4 Points - Source code is pushed to GitHub
- [ ] 8 Points - Database is deployed on the cloud server
- [ ] 10 Points - Can register a new user
- [ ] 5 Points - No duplicate usernames allowed
- [ ] 5 Points - Passwords are hashed in the database
- [ ] 5 Points - User can log in and see protected PHP pages
- [ ] 5 Points - User can log out and is prevented from seeing protected PHP pages
- [ ] 5 Points - An appropriate error message is displayed when login or register fails
- [ ] 5 Points - PHP Code uses SQL Prepared Statements and PHP Bound Parameters.

# Extra Credit

> Note: TAs cannot help you with extra credit!

- [ ] 8 Points - Use PHP to escape user input before submitting to the database in order to prevent JavaScript XSS attacks

# Writeup Questions

- Describe how cookies are used to keep track of the state. (Where are they stored? How does the server distinguish one user from another? What sets the cookie?)
- Describe how prepared statements protect against sql injection, but not xss.
