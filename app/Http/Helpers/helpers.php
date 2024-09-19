<?php
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!function_exists("randomToken")) {
    function randomToken($length = 6, $type = 'number')
    {
        $random = "123456789";
        if($type == "text"){
            $random = "ABCDEFHIJKLMNOPQRSTUVYZYX1234567890";
        }
        return substr(str_shuffle($random), 0, $length);
    }
}


if (!function_exists("errorResponse")) {
    function errorResponse($msg = "", $errors = "", $code = 422)
    {
        return response()->json([
            'code' => $code,
            'status' => 'error',
            'errors' => $errors,
            'message' => $msg
        ], $code);
    }
}

if (!function_exists("simplePagination")) {
    function simplePagination($data, $count, $page, $limit)
    {
        return [
            'data' => $data,
            'limit' => $limit,
            'currentPage' => $page,
            'totalRecords' => $count,
            'totalPages' => ceil($count/$limit),
        ];
    }
}


if (!function_exists("okResponse")) {
    function okResponse($msg = "", $response = [])
    {
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $response,
            'message' => $msg
        ], 200);
    }
}


if (!function_exists("uploadFile")) {
    function uploadFile(Request $request, $key = "")
    {
        try {
            //code...
            $upload = Cloudinary::uploadFile($request->file($key != "" ? $key: "file")->getRealPath());

        return [
            "url" => $upload->getSecurePath(),
            "size"  =>$upload->getSize(),
            "size_in_kb"  => $upload->getReadableSize(),
            "file_type"  =>$upload->getFileType(),
            "file_name"=>$upload->getFileName(),
            "file_id"  =>$upload->getPublicId(),
            "ext"  =>$upload->getExtension(),
            "width"  =>$upload->getWidth(),
            "height"  =>$upload->getHeight(),
            "uploaded_at"  => $upload->getTimeUploaded(),
        ];
        // Upload an Image File to Cloudinary with One line of Code
        } catch (\Throwable $th) {
            return false;
        }
    }
}

if (!function_exists("uploadFiles")) {
    function uploadFiles(Request $request, $key = 'files')
    {
        $response = [];
        foreach ($request->file($key) as $k => $file) {
            $upload = Cloudinary::upload($file->getRealPath());
            $d = [
                "url" => $upload->getSecurePath(),
                "size"  =>$upload->getSize(),
                "size_in_kb"  => $upload->getReadableSize(),
                "file_type"  =>$upload->getFileType(),
                "file_name"=>$upload->getFileName(),
                "file_id"  =>$upload->getPublicId(),
                "ext"  =>$upload->getExtension(),
                "width"  =>$upload->getWidth(),
                "height"  =>$upload->getHeight(),
                "uploaded_at"  => $upload->getTimeUploaded(),
            ];
            array_push($response, $d);
        }
        return $response;
        // Upload an Image File to Cloudinary with One line of Code
    }
}
if (!function_exists("uploadLocalFile")) {
    function uploadLocalFile($url)
    {
        try {
            //code...
            $upload = Cloudinary::uploadFile($url);
            return [
                "url" => $upload->getSecurePath(),
                "file_id"  =>$upload->getPublicId(),
            ];
      
        // Upload an Image File to Cloudinary with One line of Code
        } catch (\Throwable $th) {
            return false;
        }
    }
}

if (!function_exists("deleteCloudFile")) {
    function deleteCloudFile($publicId)
    {
        try {
            //code...
            $upload = Cloudinary::destroy($publicId);
            return $upload;
        // Upload an Image File to Cloudinary with One line of Code
        } catch (\Throwable $th) {
            return $th->getMessage();
            return false;
        }
    }
}

if (!function_exists("sendMail2")) {
    function sendMail2($email, $subject, $body){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions
 
        try {
            $mail->SMTPDebug = 0;                                      // Enable verbose debug output
            $mail->isSMTP();                                           // Set mailer to use SMTP
            $mail->Host       = env('MAIL_HOST');                  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                  // Enable SMTP authentication
            $mail->Username   = env('MAIL_USERNAME');              // SMTP username
            $mail->Password   = env('MAIL_PASSWORD');                                // SMTP password
            $mail->SMTPSecure = 'tls';                                 // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = env('MAIL_PORT');      

            $mail->setFrom(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"));
            $mail->addAddress($email);
            $mail->isHTML(true); 
 
            $mail->Subject = $subject;
            $mail->Body    = $body;
 
            if( !$mail->send() ) {
                return false;
            }
            
            else {
                return true;
            }
 
        } catch (Exception $e) {
            // var_dump($e->getMessage());
             return false;
        }
    }
}




