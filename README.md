# Pyl-One
Le site de gestion des notes et de partage de documents dédié aux étudiants UTBM, ouvert en écriture

This is the source code of the UTBM's students website allowing grades management and documents sharing.

This website is actually under construction, and will firstly be available as a beta test for the twentieth promotion of UTBM students.

# Localhost
In order to developp, test or simply use the website, here are some instructions to host it locally, especially with the right database structure :

**For Windows users (using WAMP) :**
1) Clone all files in your www directory
2) Then add a folder named "private" in the website root folder
3) In this folder, create a new file "config.ini", and copy-paste the following content (replace "[your_db_...]" with your own databse credentials) :
	*******************
	[database]</br>
	servername = localhost</br>
	username = [your_db_username]</br>
	password = [your_db_password]</br>
	dbname = PylOne
	*******************
4) Please note that a .htaccess is present in the website root to delete ".php" extension of website files. Thus, simply refer to those files without the .php extension, when writing a new page.

**For Linux users (using XAMPP) :**
1) Clone all files in your htdocs directory
2) Do steps 2-4 above

**Emulation of account activation by email**  
If you need to emulate the signup page with e-mail account activation, please follow the instructions below (for Ubuntu) :

1) Edit your local *php.ini* file (its location depends on your system and your local web server, for ubuntu with lampp it's /opt/lampp/etc/php.ini) :  
	* Search (Ctrl + F) for "sendmail_path", and replace the 3 lines following lines like that (pay attention in uncommenting the last one):
	*******************
	; For Unix only.  You may supply arguments as well (default: "sendmail -t -i").  
	; http://php.net/sendmail-path  
	sendmail_path = /usr/bin/msmtp -t
	*******************
	
2) If you want to use a gmail address as sending address, follow the steps below to activate **Less Secure Apps** :  
    1. Open your Google Admin console (admin.google.com)  
    2. Click Security > Basic settings.
    Under Less secure apps, select Go to settings for less secure apps.  
    3. In the subwindow, select the Enforce access to less secure apps for all users radio button.  
    (You can also use the Allow users to manage their access to less secure apps, but don't forget to turn on the less secure apps option in users settings then!)  
	4. Click the Save button.  

3) You need to install msmtp on Linux/Ubuntu server :  

	Gmail uses https:// (it's hyper text secure) so you need install ca-certificates

		~$ sudo apt-get install msmtp ca-certificates

	It will take few seconds to install msmtp package.

	Now you have to create configuration file(msmtprc) using , gedit editor.

		~$ sudo gedit /etc/msmtprc

	Now you have to copy and paste following code in gedit (file you created with above command)

		defaults
		tls on
		tls_starttls on
		tls_trust_file /etc/ssl/certs/ca-certificates.crt

		account default
		host smtp.gmail.com
		port 587
		auth on
		user MY_GMAIL_ID@gmail.com
		password MY_GMAIL_PASSSWORD
		from MY_GMAIL_ID@gmail.com
		logfile /var/log/msmtp.log

	Don't forget to replace MY_GMAIL_ID with your "gmail id" and MY_GMAIL_PASSSWORD with your "gmail password" in above lines of code.

	Now create msmtp.log as

		~$ sudo touch /var/log/msmtp.log

	You have to make this file readable by anyone with

		~$ sudo chmod 0644 /etc/msmtprc

	Now Enable sendmail log file as writable with

		~$ sudo chmod 0777 /var/log/msmtp.log

	Your configuration for gmail's SMTP is now ready. Now send one test email as

		~$ echo -e "Subject: Test Mail\r\n\r\nThis is my first test email." |msmtp --debug --from=default -t MY_GMAIL_ID@gmail.com

	Please check your Gmail inbox.

	Now if you want to send email with php from localhost please follow below instructions:-

	Open and edit php.ini file

		~$ sudo gedit /etc/php/7.0/apache2/php.ini

	You have to set sendmail_path in your php.ini file.

	Check your SMTP path with

		~$ which msmtp 

	and you will get /usr/bin/msmtp like that.

	Now save and exit from gedit. Now it's time to restart your apache

		~$ sudo lampp stop
		~$ sudo lampp start

	Now you are able to create an account and to verify it with your own gmail address.
