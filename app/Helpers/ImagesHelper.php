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

    public static function uploadImage2($file, $current_month,$path_save) {

        $file_name = $file->getClientOriginalName();
        $original_name = pathinfo($file_name, PATHINFO_FILENAME);
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);

        $name = str_slug($original_name).".".$extension;
        $pathImage = $path_save.$current_month.'/';

        if($current_month != "term_service"){
          $filename = strtotime('now') . strtolower($name);
        }else{
          $filename = strtolower($name);
        }
        
         if (!file_exists($pathImage)) {
             mkdir($pathImage, 0777, true);
         }
        $file->move($path_save.$current_month, $filename);
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
        $pathImage = '/images/' . $folder_upload . '/';
        $filename = strtotime('now') .'-'. strtolower($name);

        $file->move("tmp-upload", $filename);
        $tmpUpload = "tmp-upload/".$filename;

        self::sendRequestToApi($tmpUpload,$filename,$pathImage);
        unlink("tmp-upload/".$filename);

        return $pathImage . $filename;
    }


    /**
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
                    $filename = time().$k.'.png';
                    $tmpUpload = "tmp-upload/" . $filename;
                    $pathTmpUpload = public_path() .'/'. $tmpUpload;
                    file_put_contents($pathTmpUpload, $data);

                    $pathImage = '/images/place/news/summernote/';
                    self::sendRequestToApi($tmpUpload,$filename,$pathImage);
                    unlink("tmp-upload/".$filename);

                    $img->removeAttribute('src');
                    $img->setAttribute('src', env('URL_FILE_VIEW').$pathImage.$filename);

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
    public static function uploadImageWebbuilder($file , $folder_upload , $place_ip_license)
    {
          $name = $file->getClientOriginalName();
          $name = str_replace(" ", "-", $name);
          $filename = strtotime('now') .'-'. strtolower($name);

          $pathImage = '/images/'.$place_ip_license.'/website/'.$folder_upload.'/';
          $file->move("tmp-upload", $filename);
          $tmpUpload = "tmp-upload/".$filename;

          self::sendRequestToApi($tmpUpload,$filename,$pathImage);
          unlink("tmp-upload/".$filename);

          return $pathImage.$filename;
    }
    public static function uploadImageDropZone($file , $folder_upload , $place_ip_license)
    {     
          return self::uploadImageDropZone_get_path($file , $folder_upload , $place_ip_license);
          
          // $place_ip_license = self::getLicense();

          // $pathFile   = config('app.url_file_write');
          // $name = preg_replace("/[^A-Za-z0-9\-]/",'_',$file->getClientOriginalName());
          // $pathImage = '/images/'.$place_ip_license.'/website/'.$folder_upload.'/';
          // // if (!file_exists($pathFile.$pathImage)) {
          // //     mkdir($pathFile.$pathImage,0777, true);
          // // }
          // // $file->move($pathFile.$pathImage,$name);
          // $file->move("tmp-upload", $name);
          // $tmpUpload = "tmp-upload/".$name;

          // self::sendRequestToApi($tmpUpload,$name,$pathImage);
          // unlink("tmp-upload/".$name);
          
          // return $pathImage.$name;
    }

    public static function uploadImageDropZone_get_path($file , $folder_upload , $place_ip_license)
    {     
          $pathFile   = config('app.url_file_write');
          $name = preg_replace("/[^A-Za-z0-9\-]\./",'_',$file->getClientOriginalName());
          $pathImage = '/images/'.$place_ip_license.'/website/'.$folder_upload.'/';

          if (!file_exists($pathFile.$pathImage)) {
              mkdir($pathFile.$pathImage,0775, true);
          }
        //   $file->move($pathFile.$pathImage,$name);
          $file->move("tmp-upload", $name);
          $tmpUpload = "tmp-upload/".$name;

          self::sendRequestToApi($tmpUpload,$name,$pathImage);
          unlink("tmp-upload/".$name);
          
          return $pathImage.$name;
    }
}

