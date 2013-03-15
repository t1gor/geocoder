Free php GeoCoder class
========

Uses 3 API's: 
- [Google](https://developers.google.com/maps/documentation/geocoding/) (2500/day max) 
- [MapQuest](http://developer.mapquest.com/web/products/open/geocoding-service) (seems to be unlimited, but doesn't always find adresses)
- and [GeoCodeFarm](http://www.geocodefarm.com/geocoding-dashboard.php?reg=1) (2500/day, registration needed)


Usage example
--------------------
```php
$coords = GeoCoder::get_coords('Central Park, New York, NY, USA');
```
    
Where `$coords` is an array like this:

```php
array(
    'source'    => 'api source',
    'lat'		=> 41.6659568,
    'lng'		=> -80.808452,
)
```
    
Optionally, you can specify the source API:
```php
$coords = GeoCoder::get_coords('Central Park, New York, NY, USA', 'google');
```
    
Source API's are prioritised like this: [Google](https://developers.google.com/maps/documentation/geocoding/), [MapQuest](http://developer.mapquest.com/web/products/open/geocoding-service), [GeoCodeFarm](http://www.geocodefarm.com/geocoding-dashboard.php?reg=1).

Speeding up
--------------------

I found it much more efficient with some caching usage. I used - [PHP-SimpleCache](https://github.com/gilbitron/PHP-SimpleCache).
Here's an axample:

```php
...
// load cache class
require_once('SimpleCache.php');

// check if cache
if ($cache->is_cached($adress)) {
	$geo_data = json_decode($cache->get_cache($adress));
	$geo_data = array(
		'source'	=> $geo_data->source,
		'lat'		=> $geo_data->lat,
		'lng'		=> $geo_data->lng,
	);
}
else {
	// get coords
	$geo_data = GeoCoder::get_coords($adress);

	// cache the response (only if coded ok)
	if (is_array($geo_data)) {
		$cache->set_cache($adress, json_encode($geo_data));
	}
}
...

```


Have fun!
