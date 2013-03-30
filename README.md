## MCHostPanel
A lightweight Minecraft control panel for hosting companies

MCHostPanel can manage an unlimited number of Minecraft control panels, including CraftBukkit, Tekkit, and any other custom build that uses a .jar file.

NOTE: Some features of this project including auto-updating and directory deleting/renaming are currently broken, and have been disabled.

### Requirements

- PHP 5
- Java 7

### Installation

- Upload all of the files to a web-accessible directory on your server.
- Edit data/config.php and set KT_LOCAL_IP to your server's public IP address
- Go to install.php in your browser and set up an administrator user.
- Delete install.php
- Add any Minecraft server .jar file to your home directory, and rename it "craftbukkit.jar"

### User setup

- Log in as an administrator user
- Go to Administration
- Use the "Add a New User" form to set up a new account, the home directory SHOULD NOT be web accessible
- Add any Minecraft server .jar file to the user's directory, and rename it "craftbukkit.jar"
- If desired, you can now start the user's server from the Administration page
