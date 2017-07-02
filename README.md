## Scraping Magic Town MX
Web Scraping for Mexican Magic Towns

Create Object Scraping_pmagicos
```php
require("scraping.php");
$scr=new Scraping_pmagicos();
$data=$scr->execute(False,True);
$myfile = fopen("files_scraping.json", "w") or die("Unable to open file!");
fwrite($myfile, $data);
fclose($myfile);
```

<br /><br /><br /><br />








Structure of response (JSON):
```json
[
{
	"link":"",
	"name":"",
	"image":"",
	"info":"",
	"section":[
		{
			"title":"",
			"description":""
		}
	],
	"coordinates":
		{
			"latitude":"",
			"longitude":""
		},
	"distance":""
}
]
```
