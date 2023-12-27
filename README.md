<a name="readme-top"></a>

# Yii 2 Ticket System

<details>
<summary><h2>Table of Contents</h2></summary>

  - [Overview](#overview)
  - [Features](#features)
  - [Installation](#installation)
  - [Usage](#usage)
  - [Project objectives](#project-objectives)
  - [License](#license)
</details>

## Overview

The **Yii 2 Ticket System** is designed to facilitate the process of receiving and handling user requests from a website. Any user can submit data through a public API provided by the system, leaving a request with a specific message. These requests are then reviewed by designated individuals, who can update the request status to "Completed" by leaving comments. The system ensures that users receive responses via email. Additionally, responsible parties can filter requests by status and date, respond to users through email, and retrieve a list of requests for efficient management.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Features

- **API Endpoint:** users can submit requests through a public API endpoint.
- **Request Status Management:** responsible parties can update request statuses to "Completed" by leaving comments.
- **Email Notifications:** users receive email responses to their requests.
- **Filtering:** requests can be filtered by status (e.g., "Active" or "Resolved") and date range.
- **Pagination:** requests are paginated for efficient retrieval.
- **Error Handling:** comprehensive error handling provides feedback on validation and request-related issues.
- **Secure API Access:** the system securely interacts with the API to protect data privacy.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Installation

To install Yii 2 Ticket System, follow these steps:

### 1. 🐘 Environment Setup

To begin, ensure that you have PHP installed, specifically version `8.2.14`. If you don't have it installed, please visit the [official PHP website](https://www.php.net/downloads.php) to download and install the latest version.

You can check your PHP version using the following command, which should display `8.2.14`. Depending on your system, you may need to use aliases like `php`, `php8`, or `php8.2` instead of `php8.2`:

```bash
$ php8.2 --version
PHP 8.2.14 (cli) (built: Dec 21 2023 20:19:23) (NTS)
```

### 2. 📥 Repository cloning

Clone the repository using the command below:

```
git clone https://github.com/WarLikeLaux/yii2-ticket-system
```

Then, navigate to the project folder:

```
cd yii2-ticket-system
```

### 3. 🧩 Dependencies installation

To install the dependencies, use Composer:

```
composer install
```

If you prefer to download the latest stable version of Composer as a local file and run it with PHP, you can use the following commands:

```
wget https://getcomposer.org/download/latest-stable/composer.phar
php8.2 composer.phar install
```

### 4. 🗝️ Database config setup

First, create a database. Replace the port and database name if necessary.

```
createdb -h localhost -U root -p 5432 yii2_ticket_system
```

Next, copy the database configuration file using `cp config/db-local-example.php config/db-local.php`, and then set the username and password correctly in the `config/db-local.php` file. Additionally, update the port and database name if you are using values different from the default.

```
<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;port=5432;dbname=yii2_ticket_system',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
];

```

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Usage

To use the Yii 2 Ticket System, follow these steps:

**1. To serve a web app:**
```
php8.2 yii serve -p 8081
```

**2. To access the API documentation and perform test implementations:**
```
xdg-open http://localhost:8081/
```

Or open the link `http://localhost:8081/` in your browser.

Here, you can find information about available API methods and test them using the provided UI with example data.

**3. To access the UI for managing requests:**

```
xdg-open http://localhost:8081/requests-ui/
```

Or open the link `http://localhost:8081/requests-ui/` in your browser.

**4. Run unit tests:**

```
php8.2 vendor/bin/codecept run unit tests/unit/controllers/RequestsApiControllerTest
```

These instructions guide you through setting up and using the Yii 2 Ticket System for your web application.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Project objectives

This code was originally developed as a demonstration of my coding skills and problem-solving abilities. It serves as a practical example of my web development expertise. You can review this code to assess my capabilities and approach to solving web development challenges.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## License

This code is open-source and free for any modifications, distributions, and uses. Feel free to utilize it in any manner you see fit.

<p align="right">(<a href="#readme-top">back to top</a>)</p>