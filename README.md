# PHP MVC framework

## This is my **MVC** framework [FW] built using **PHP**.

This project will satisfy the following requirements.
- [ ] will be able to handle any request
	- [x] Parse URL, Body and Header.
	- [x] Identify the HTTP Method used to perform the request (GET, POST, DELETE, PUT).
	- [x] Gather all the parameters attached to the request by URL, Request Body or Request Header.
	- [ ] Apply constraints ( Authentication, Autherization, Once, Private .... ) before performing the request.
	- [x] Run request Handler Method.
	- [ ] Feed the user with the desired response formate
		- [ ] Html
		- [ ] Plain text 
		- [ ] XML 
		- [ ] JSON
- [ ] Database connectore
	- [ ] Ability to use multiple DB driver into the project.
		- [x] MySql
		- [ ] Sql Server
	- [x] Ability to retrieve data from database by building a query using php functions.
	- [x] Build simple ORM framework.
	
- [ ] Cache System
	- [ ] Ability to use multiple Cache System.
		- [ ] Memcached
		- [ ] Redis
- [ ] Session System
	- [ ] Ability to use multiple session handler driver.
		- [ ] Filebase
		- [ ] Database
- [ ] Configuration System
	- [ ] Ability to use muliple Configuration Parse System.
		- [x] INI
		- [x] Array
		- [x] JSON
		- [x] XML
		- [ ] plain text.
- [ ] Create an Event based system

