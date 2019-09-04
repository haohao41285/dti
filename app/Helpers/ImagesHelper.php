<?php
namespace App\Helpers;

class ImagesHelper
{
	public static function uploadImage($checkImage, $requestImage, $nameImageDB){
		if($checkImage){ 
            $img = $requestImage;
            $img_name = time()."-".$img->getClientOriginalName();
            if(!empty($nameImageDB)){
            	if(file_exists(env('PATH_UPLOAD_IMAGE').'/'.$nameImageDB)){
               	 	unlink(env('PATH_UPLOAD_IMAGE').'/'.$nameImageDB);
            	}
            }
            $img->move(env('PATH_UPLOAD_IMAGE'),$img_name);
            return $img_name;
        }
	}
    /**
     * Auto upload image to SummerNote 
     * @param  $description is SummerNote content
     * @return $description 
     */
    public static function uploadImageSummerNote($description){
        $dom = new \DomDocument();
        $dom->loadHtml('<?xml encoding="utf-8" ?>' .$description);   
        $images = $dom->getElementsByTagName('img');

        foreach($images as $k => $img){
            $data = $img->getAttribute('src');
            if($data){
                try {
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $image_name= "/upload/summer_note/" . time().$k.'.png';
                    $path = public_path() . $image_name;
                    file_put_contents($path, $data);
                    $img->removeAttribute('src');
                    $img->setAttribute('src', $image_name);
                } catch (\Exception $e) {
                    continue;
                }
                
            }            
        }
        $description = $dom->saveHTML();
        return $description;
    }
}