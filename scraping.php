<?php
/*

  Created on: 2017
  Author: Mizael Martinez

*/
class Scraping_pmagicos
{
  private $url="http://www.gob.mx/sectur/articulos/pueblos-magicos-herencia-que-impulsan-turismo";
  private $content_link;
  private $content_magic_tw;
  private $data;
  public function execute($url=FALSE,$json=false)
  {
    if($url!==FALSE)
      $this->url=$url;
    $this->extract_link();
    $this->ctrl_extract_info();

    return ($json)?json_encode($this->data):$this->data;
  }
  private function extract_link()
  {
    $aux=array();
    $this->data=array();
    $this->content_link=file_get_contents($this->url);
    preg_match_all('/(http:\/\/www.sectur.gob.mx\/gobmx\/pueblos-magico)/',$this->content_link,
    $aux, PREG_OFFSET_CAPTURE);
    if(count($aux)>0)
    {
      foreach ($aux[0] as $key => $value)
      {
        $item=array();
        $item["link"]=$this->extract_link_item($value[1]);
        $item["name"]=$this->extract_name($item["link"]);
        $this->data[]=$item;
      }
    }
  }
  private function extract_link_item($start)
  {
    $end=strpos($this->content_link,'"',$start);
    return substr($this->content_link,$start,$end-$start);
  }
  private function extract_name($url)
  {
    $numbers=[0,1,2,3,4,5,6,7,8,9];
    $name="";
    $arr=explode("/",$url);
    if($arr[count($arr)-1]=="")
      $name=$arr[count($arr)-2];
    else
      $name=$arr[count($arr)-1];
    $name=str_replace("-"," ",$name);
    $name=str_replace($numbers," ",$name);
    return ucwords(trim($name));
  }

  private function ctrl_extract_info()
  {
    foreach($this->data as $key=>$value)
    {
      $this->extract_content_magic_tw($value["link"]);
      $this->data[$key]["image"]=$this->extract_image_magic_tw();
      $this->data[$key]["info"]=$this->extract_info_magic_tw();
      $this->data[$key]["section"]=$this->extract_sections_magic_tw();
      $this->data[$key]["coordinates"]=$this->extract_coordinates_magic_tw();
      $this->data[$key]["distance"]=0.0;
      sleep(1);
    }
  }
  private function extract_content_magic_tw($url)
  {
    $this->content_magic_tw="";
    $this->content_magic_tw=file_get_contents($url);
  }
  private function extract_info_magic_tw()
  {
    $start=strpos($this->content_magic_tw,'<div class="entry-content">')+27+8;
    $end=strpos($this->content_magic_tw,'<p>&nbsp;</p>',$start);
    return substr($this->content_magic_tw,$start,$end-$start);
  }
  private function extract_image_magic_tw()
  {
    $start=strpos($this->content_magic_tw,'http://www.sectur.gob.mx/gobmx/wp-content/uploads');
    $end=strpos($this->content_magic_tw,'" title=',$start);
    return substr($this->content_magic_tw,$start,$end-$start);
  }
  private function extract_sections_magic_tw()
  {
    $array=array();
    $item=array();
    $item["title"]="";
    $item["description"]="";
    $start=0;
    $end=0;
    $count=0;
    while(($start=strpos($this->content_magic_tw,'<p><strong>',$start))!=FALSE)
    {
      $start=$start+11;
      $end=strpos($this->content_magic_tw,'</strong></p>',$start);
      $item["title"]=substr($this->content_magic_tw,$start,$end-$start);
      $item["description"]=$this->extract_description_section($end+13);
      if($item["description"]!==FALSE)
        $array[]=$item;
      $start=$end;
      $count++;
    }
    return $array;
  }
  private function extract_description_section($start)
  {
    $end=strpos($this->content_magic_tw,'<p>&nbsp;</p>',$start);
    return substr($this->content_magic_tw,$start,$end-$start);
  }
  private function extract_coordinates_magic_tw()
  {
    $coordinates=array();
    $coordinates["latitude"]=0;
    $coordinates["longitude"]=0;
    $start=strpos($this->content_magic_tw,"new google.maps.LatLng(")+23;
    $end=strpos($this->content_magic_tw,")",$start);
    $aux=substr($this->content_magic_tw,$start,$end-$start);
    $aux=explode(",",$aux);
    $coordinates["latitude"]=$aux[0];
    $coordinates["longitude"]=$aux[1];
    return $coordinates;
  }

}
?>
