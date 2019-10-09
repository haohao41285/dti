<?php
namespace App\Helpers;
use GuzzleHttp\Client;

class ImagesHelper
{
    /**
     * upload image local
     */
	public static function uploadImage($checkImage, $requestImage, $nameImageDB){
		if($checkImage){ 
            $img = $requestImage;
            $img_name = time()."-".$img->getClientOriginalName();
            
            $img->move(env('PATH_UPLOAD_IMAGE'),$img_name);
            return $img_name;
        }
	}

    public static function uploadImage2($file, $current_month) {

        // $pathFile = config('app.url_file_write');
        $name = $file->getClientOriginalName();
        $name = str_replace(" ", "-", $name);
        $pathImage = 'images/comment/'.$current_month.'/';
        $filename = strtotime('now') . strtolower($name);
        // //dd(config('app.url_file_write'));
        // if (!file_exists($pathImage)) {
        //     mkdir($pathImage, 0777, true);
        // }
        $file->move('images/comment/'.$current_month, $filename);
        // $tmpUpload = "tmp-upload/".$filename;

        // self::sendRequestToApi($tmpUpload,$filename,$pathImage);
        // unlink("tmp-upload/".$filename);

        // die();
        return $pathImage. $filename;
    }

    //==========================================

    /**
     * send post request upload Image to a different server
     * @return string image name
     */
    private static function sendRequestToApi($tmpUpload,$name,$path){
      try {
        $client = new Client;
        $response = $client->request('POST', env('URL_FILE_WRITE'), 
          [                
                'multipart' => [
                      [
                          'name'     => 'name',
                          'contents' => $name,
                      ],                    
                      [
                          'name'     => 'fileUpload',
                          'contents' => fopen($tmpUpload, 'r'),
                      ],
                      [
                          'name'     => 'pathImage',
                          'contents' => $path,
                      ]
                  ],
                  'headers' => [
                      'Authorization' => 'Bearer '.env('UPLOAD_IMAGE_API_KEY'),
                  ],
          ]); 
        $body = (string)$response->getBody();
        // echo ($body);

      } catch (\Exception $e) {
        \Log::info($e);
        return "error";
      }
    }

    public static function uploadImageToAPI($file, $folder_upload) { 
        $name = $file->getClientOriginalName();
        $name = str_replace(" ", "-", $name);
        $pathImage = '/images/place/' . $folder_upload . '/';
        $filename = strtotime('now') .'-'. strtolower($name);
       
        $file->move("tmp-upload", $filename);
        $tmpUpload = "tmp-upload/".$filename;

        self::sendRequestToApi($tmpUpload,$filename,$pathImage);
        unlink("tmp-upload/".$filename);

        return $pathImage . $filename;
    }


    /**
     * Auto upload image to SummerNote 
     * @param  $content input
     * @return $content output
     */
    public static function uploadImageSummerNote($content){
        $dom = new \DomDocument();
        $dom->loadHtml('<?xml encoding="utf-8" ?>' .$content);   
        $images = $dom->getElementsByTagName('img');

        foreach($images as $k => $img){
            $data = $img->getAttribute('src');

            if($data){
                try {
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $image_name = "/tmp-upload/summer_note/" . time().$k.'.png';
                    $path = public_path() . $image_name;
                    
                    file_put_contents($path, $data);
                    $img->removeAttribute('src');
                    $img->setAttribute('src', $image_name);
                    
                } catch (\Exception $e) {
                  // \Log::info($e);
                  continue;
                }
                
            }            
        }
        $content = $dom->saveHTML();
        // \Log::info($content);
        return $content;
    }

    
}