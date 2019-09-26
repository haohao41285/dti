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

    
}