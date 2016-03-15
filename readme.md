# S3 Portal

> Constructing...

## Requirement

`php >= 5.5.9`

## Usage
Copy `.env.example`

```
$ cp .env.example .env
```

Install dependencies:

```
$ composer install
```

Generate Application Key

```
$ php artisan key:generate
```

Generate JWT Key

```
$ php artisan jwt:generate
```

Enter this information in `.env`

```
AccessKey=acceAccessKeysskey
SecretKey=SecretKey
Region=defautl
ServerURL=S3_HTTP_URL
AdminEntryPoint=AdminEntryPoint
```
