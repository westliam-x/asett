## Ayulla’s Software Engineering Tryout Task (ASETT)
-- This repository contains a basic PHP web application that displays the average prices of the top 10 cryptocurrencies for the day, yesterday, the --week, month, and year.

## Requirements
--PHP version 7.4 or higher
--MySQL
--Apache server
--Windown or Linux Operating system

## Getting Started
To run this application locally, you will need to have xampp installed

Once you have Xampp installed, you can clone this repository into the `htdocs` directory in xampp using the following steps:

## Installation
--Navigate to the directory c:/xampp/htdocs using the command line on your code editor
--Clone the repository: git clone https://github.com/westliam-x/asett.git into that directory
--Navigate to the project directory: cd asett.git
--Start your local server(xampp) and open the index.php file in your web browser.

## Usage

## Sign Up Form

--Enter your desired username, email, password and confirm your password.
--Click on the "Sign Up" button to submit your details.
--If the form is successfully submitted, you will be redirected to the login page.

## Login Form

--Enter your registered email and password.
--Click on the `Login` button to submit your details.
--If the form is successfully submitted, you will be redirected to the index page.

## Built With
--HTML
--CSS
--PHP
--Javascript

## Main page
--When you first open the application, it will display the top 10 cryptocurrencies by market cap, sorted in descending order. 
--You can click on any of the column headers to sort the table by that column. Clicking on the same column header again will reverse the sort order.

## Dependencies
--This application relies on the following dependencies:

--Bootstrap 5: for styling the application
--CoinGecko API: for retrieving the cryptocurrency price data  `https://www.coingecko.com/api/documentations/v3`

## Database
## This is a SQL dump file containing the schema and data for two tables in a database named asett.

## Tables
## coin_averages
--This table has three columns: id (an integer primary key), key (a varchar), and values (an integer). 
--The engine used for this table is InnoDB with a default character set of utf8mb4.

## users
--This table has four columns: user_id (an integer primary key), username (a varchar), email (a varchar), and password (a varchar). 
--The engine used for this table is InnoDB with a default character set of utf8mb4. This table also contains one row of data.

## Dump Information
The SQL dump was generated using phpMyAdmin version 5.2.0. The server version is MariaDB 10.4.25, and the PHP version is 8.1.10.

## Usage
This SQL dump file can be used to recreate the asett database and its two tables, coin_averages and users, in a MySQL or MariaDB server. To see this file navigate to the `asett.sql` ile


## Disclaimer
The data used in this web application is for educational purposes only. 
The information provided should not be used as investment advice.
Also note that UI was not the main focus of this project just the idea of the funtionality
Some programming lexicons were also purposely overlooked

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Also note that the API is on a free version so there are restrictions to it that sometimes may not lead to it working well
