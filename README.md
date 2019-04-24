# Documentation

* method - string (examples: create, login, get, ...)
* secret - api secret

## Users

Endpoint `https://www.example.de/index.php?user-api`

### create
Parameters
* username
* password
* email

### login
Parameters
* username
* password

### get
Parameters
* userID

### update
Parameters
* userID
* username
* wscApiId
* userOptionXX - XX represent the id (eg 01, 02, 03)

Either username, wscApiId or userOptionXX is required. 

### notification
Parameters
* userID
* title
* message
* url

This function is currently experimental.

## Groups

Endpoint `https://www.example.de/index.php?user-group-api`

### add
Parameters
* groupID

### remove
Parameters
* groupID
* userID - number or array

### get
Parameters
* groupID
* userID - number or array