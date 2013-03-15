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
    'lat'		=> latitude,
    'lng'		=> longitude,
)
```
    
Optionally, you can specify the source API:
```php
$coords = GeoCoder::get_coords('Central Park, New York, NY, USA', 'google');
```
    
Source API's are prioritised like this: [Google](https://developers.google.com/maps/documentation/geocoding/), [MapQuest](http://developer.mapquest.com/web/products/open/geocoding-service), [GeoCodeFarm](http://www.geocodefarm.com/geocoding-dashboard.php?reg=1).

Have fun!
