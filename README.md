# MoodleBis
Le site de gestion des notes et de partage de documents dédié aux étudiants UTBM, ouvert en écriture

This is the source code of the UTBM's students website allowing grades management and documents sharing.

This website is actually under construction, and will firstly be available as a crash test for the twentieth promotion of UTBM students.

# Localhost
In order to developp, test or simply use the website, here are some instructions to host it locally, especially with the right database structure :

<u>For Windows users (using WAMP) :</u>
1) Clone all files in your www directory
2) Then add a folder named "private" in the website root folder
3) In this folder, create a new file "config.ini", and copy-paste the following content (replace "[your_db_...]" with your own databse credentials) :
	*******************
	[database]</br>
	servername = localhost</br>
	username = [your_db_username]</br>
	password = [your_db_password]</br>
	dbname = MoodleBis
	*******************
4) Please note that a .htaccess is present in the website root to delete ".php" extension of website files. Thus, simply refer to those files without the .php extension, when writing a new page.

<u>For Linux users (using XAMPP) :</u>
1) Clone all files in your htdocs directory
2) Do steps 2-4 above
