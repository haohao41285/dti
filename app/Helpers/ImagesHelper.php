<?php
namespace App\Helpers;

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
        $data = [
          "fileUpload" => fopen($tmpUpload, 'r'), 
          "name" => $name,
          "pathImage" => $path,
        ];

        $curl = curl_init("http://localhost:8000/upload/images/");

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data',
            'Content-Type: application/json',
            // 'Content-Length: ' . strlen($data_string)
          )
        );
dd($curl);

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;

      } catch (\Exception $e) {
        \Log::info($e);
        dd('falsdf');
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