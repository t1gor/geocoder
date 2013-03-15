Free php GeoCoder class
========

Uses 3 API's: 
- Google (2500/day max) 
- MapQuest (seems to be unlimited, but doesn't always find adresses)
- and GeoCodeFarm (2500/day, registration needed)


Usage example
--------------------

    $coords = GeoCoder::get_coords($adress);
    
Where `$coords` is an array like this:

    array(
        'source'    => 'api source',
        'lat'		=> latitude,
        'lng'		=> longitude,
    )
    
Optionally, you can specify the source API:

    $coords = GeoCoder::get_coords($adress, 'google');
    
Source API's are prioritised like this: Google, MapQuest, GeoCodeFarm.

Have fun!
