## About Laravel

This document outlines the requirements and setup instructions for the API service that facilitates fund transfers between customer accounts. The project is developed using the Laravel framework.

## Requirements
* PHP (>= 8.1)
* Composer
* MySQL
* Account in https://apilayer.com/
  * API Layer key
  * Subscription to https://apilayer.com/marketplace/exchangerates_data-api

## Set-up
* `git clone https://github.com/tomsBi/transfer-app.git`
* `cd transfer-app`
* `composer install`
* `cp .env.example .env`
* `cp .env.testing.example .env.testing`
* In `.env` file configure DB connection, API Layer key.
* In `.env.testing` file configure another DB connection, which will work for tests, also add API Layer key.
* `php artisan migrate`
* `php artisan serve`
* Import `transfer-app.postman_collection.json` into postman

## HTTP API Functionality
1. POST `/api/register`
  * Use this endpoint to register and recieve token
2. POST `/api/accounts/create`
  * Use this endpoint to create account for user
3. GET `/api/accounts`
  * Return all accounts created by user
4. POST `/api/transactions`
  * Use this endpoint to transfer funds between accounts
5. GET `/api/transactions/:account_id?offset=0&limit=10`
  * Return all transactions by account ID
6. GET `/api/users`
  * Return all users

