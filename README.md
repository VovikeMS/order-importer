# Order importer application

This application uses for regular import data from third party services.
After config your app check logs in `storage\logs`  and database table `orders` after some time.

## Install notes

- Clone this repository
- Open console and go to root of your cloned repository
- Copy `.env.example` to `.env` and open
- Update variables according to your local and save file
	* `APP_ENV` - environment key `local`, `production`, `staging`
	* `APP_DEBUG` - true or false
	* `APP_URL` - full url to your application
	* `DB_HOST` - database host
	* `DB_PORT` - database port
	* `DB_DATABASE` - database name
	* `DB_USERNAME` - database user name
	* `DB_PASSWORD` - database user password
	* `XML_API_SOURCE` - xml import file url
	* `CSV_API_SOURCE` - csv import file url
- Run `composer install`
- Run `composer update`
- Run `php artisan key:generate`
- Run `php artisan migrate`
- Give write access to folder `bootstrap/cache` (`chmod -R 775 bootstrap/cache`)
- Give write access to folder `storage/*` (`chmod -R 775 storage`)
- [Add application scheduler](https://laravel.com/docs/5.2/scheduling#introduction) to your local cron:
	* `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`
- Choose your [method for listen queue jobs](https://laravel.com/docs/5.2/queues#running-the-queue-listener) in your system. You can just run `php artisan queue:work --daemon --sleep=3 --tries=3` for listen jobs (this command should be worked every time). 

## Adding new import format

For add new import format you should create new class in folder `App\Importer\Services`.
Add this code to your importer, where replace `Type` and `type` word to your new import type:
```php
<?php

namespace App\Importer\Services;

use App\Importer\Importable;
use App\Importer\Importer;

class Type extends Importer implements Importable
{
	protected $name = 'Type';
	protected $type = 'type';
	protected $mime_type = 'mime-type-for-your-type';

	/**
	 * Type constructor.
	 *
	 * @param string $resource
	 */
	public function __construct($resource)
	{
		parent::__construct($resource);
	}

	/**
	 * Validate input data
	 *
	 * @return boolean
	 */
	public function validate()
	{
		// $this->source_data - contain response from $resource

		return true;
	}

	/**
	 * Start process
	 *
	 * @return boolean
	 */
	public function import()
	{
		//

		return true;
	}
}
```
Method `validate()` should check `$this->source_data` variable for valid type format.
Method `import()` parse response data and import data to DB.
You can use local instance `$this->logger` of `Logger` class what give you access to log your parser events in file `storage/logs/type_importer.log`.

Then open file `App\Importer\Factory.php` and add new import format into factory.
After added new type `type` `Factory.php` should be like:
```php
<?php

namespace App\Importer;

use App\Importer\Services\Csv;
use App\Importer\Services\Xml;
use App\Importer\Services\Type;

abstract class Factory
{
	/**
	 * Registered importers type
	 *
	 * @var array
	 */
	private static $importerTypes = ['csv', 'xml', 'type'];

	/**
	 * Generate importer instance according type of importer
	 *
	 * @param string $type
	 * @param string $resource
	 * @return Csv|Xml|Type|false
	 * @throws \Exception
	 */
	public static function create($type, $resource)
	{

		if(!in_array($type, self::$importerTypes))
			throw new \Exception("Uncaught type of importer [$type]. Importer abort");

		switch($type){
			case 'csv': return new Csv($resource);
			case 'xml': return new Xml($resource);
			case 'type': return new Type($resource);
		}

		return false;
	}
}
```

Then you should run importer where you need by next code:
```php
$xml_importer = App\Importer\Factory::create('type', 'http://path-to-api-method.com');
if($xml_importer->validate())
	$xml_importer->import();
```

After you cah check log file in folder `storage\logs`.

## Error handling

If you see nothing in database `order` table please check `storage\logs\laravel.log` to see what happens.
If you see nothing in logs then check your cron and scheduler configuration.
For debug importer tasks just run in console `php artisan schedule:run` then `php artisan queue:work`.