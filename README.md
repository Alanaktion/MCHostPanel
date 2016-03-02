## MCHostPanel
A lightweight Minecraft control panel for hosting companies

MCHostPanel can manage an unlimited number of Minecraft control panels, including CraftBukkit, Tekkit, and any other custom build that uses a .jar file.

### Requirements

- PHP 5.3+ with GD library (GD only used for player faces)
- GNU Screen (installed by default on many platforms)
- Java 6/7 Headless (OpenJDK JRE works great)

### Installation

- Upload all of the files to a web-accessible directory on your server
- Copy data/config-sample.php to data/config.php
- Edit data/config.php and set `KT_LOCAL_IP` to your server's public IP address
- Go to install.php in your browser and set up an administrator user
- Add any Minecraft server .jar file to your home directory
- Ensure the web server user has write access to the main MCHP and `data/` directories.

### User setup

- Log in as an administrator user
- Go to Administration
- Use the "Add a New User" form to set up a new account, the home directory SHOULD NOT be web accessible
- Add any Minecraft server .jar file to the user's directory
- Ensure the web server user has write access to the directory
- If desired, you can now start the user's server from the Administration page
