# Minimal
Simple PHP Mailer

### Installing

```
$ composer require bhrdn/minimail
```

### How it works
* Bypass spam filtering
```php
$mail = new \Minimail\Mailer([
	'homograph' => true
]);
```


### License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE) file for details
