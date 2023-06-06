## Contributor Note

**Note:** My contributions are made under the username MahdiChaabane(my institutional github account)

## Tuniconnect Web Application

Thank you for your interest in Tuniconnect! Follow the steps below to set up and run the web application on your local machine.

### Prerequisites
- PHP 7.2 or higher
- Composer (https://getcomposer.org)
- Node.js (https://nodejs.org)

### Backend Setup
1. Clone the backend repository:
git clone https://github.com/azizmass/socialNetwork.git

2. Navigate to the backend directory:
cd socialNetwork


3. Install the required dependencies:
composer install


4. Set up the database:
- Create a new MySQL database named social_app
- Run this 2 commands
  ```
  symfony console make:migrations
  symfony console doctrine:migrations:migrate
  ```
5.Generating Encryption Keys:
To generate the encryption keys required for secure token authentication, follow these additional steps:

A. Install OpenSSL from [https://slproweb.com/products/Win32OpenSSL.html]

B. Open the OpensSSL terminal and navigate to the Symfony project's root directory.

C. Run the following command to generate the encryption keypair:
 ```
symfony console lexik:jwt:generate-keypair --overwrite
```
6.Start mySQL server

7. Start the backend server:
symfony console serve

8. Start the Ratchet server in another terminal:
php bin/console app:ratchet 

### Frontend Setup
1. Clone the frontend repository:
git clone https://github.com/Medaminegdoura/socialNetwork-front.git

2. Navigate to the frontend directory:
cd socialNetwork-front

3. Install the required dependencies:
npm install

4. Start the frontend server:
npm start


Please note: If you encounter any CORS-related issues, you may need to install the "Allow CORS" Google Chrome extension. You can find and install it from [https://chrome.google.com/webstore/detail/allow-cors-access-control/lhobafahddgcelffkeicbaginigeejlf?hl=en&fbclid=IwAR2xRuJcu5k0NL720DO6T6XNOuHYOSCcW6fqd25mNIJLLw1Pi7K9hpJwdsI]. Make sure to enable the extension while working on the application and disable it when you're done.

### Accessing the Web Application
Once the backend and frontend servers are running, you can access the Tuniconnect web application by opening your browser and visiting `http://localhost:3000`.

That's it! You should now have the Tuniconnect web application up and running on your local machine.

If you encounter any issues during the setup process, please feel free to reach out to us for assistance.

 

### Accessing the Web Application
Once the backend and frontend servers are running, you can access the Tuniconnect web application by opening your browser and visiting `http://localhost:3000`.

That's it! You should now have the Tuniconnect web application up and running on your local machine.

