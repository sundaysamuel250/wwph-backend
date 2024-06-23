<?php

namespace App\Services;

use App\Models\Clinic;
use Exception;

class ClinicServices {

    public function storeClinic(array $details) : Clinic
    {
        $clinic = new Clinic();
        $clinic->name = $details['name'];
        $clinic->about = $details['about'];
        $clinic->email = $details['email'];
        $clinic->tel_no = $details['tel_no'];
        $clinic->opening_hours = $details['opening_hours'];
        $clinic->contact_name = $details['contact_name'];
        $clinic->address = $details['address'];
        $clinic->country = $details['country'];
        if (isset($details['longitude'])) {
            $clinic->longitude = $details['longitude'];
            $clinic->latitude = $details['latitude'];
        }

        if (isset($details['display_pic'])) {
            $file = $details['display_pic'];
            $img_extension = $file->extension();
            if (in_array($img_extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                $img_name = $file->hashName();
                $clinic->display_pic = 'http://127.0.0.1:8000/display_pic/' . $img_name;
                $details['display_pic']->move(public_path('/display_pic'), $img_name);
            } else {
                return response()->json(['message' => 'Invalid filetype']);
            }
        }

       if (!$clinic->save()) {
            throw new Exception();
       }
       
       if (!$clinic->facilities()->attach($details['facilities'])) {
            throw new Exception();
       }
       
       if (!$clinic->days()->attach($details['days'])) {
            throw new Exception();
       }

       if (!$clinic->patientType()->attach($details['patient_type'])) {
            throw new Exception();
       }

        $clinic_gallery['clinic_id'] = $clinic->id;

        if(isset($details['gallery'])) {
            $file_upload = $details['gallery'];
            foreach($file_upload as $gal_img) {
                $file_extension = $gal_img->extension();
                if (in_array($file_extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                    $clinic_gallery['image'] = $gal_img->hashName();
                    $gal_img->move(public_path('/clinic_gallery'), $clinic_gallery['image']);
                    $clinic->clinicGallery()->create($clinic_gallery);
                } else {
                    return response()->json(['message' => 'Invalid filetype']);
                }
            }
        }

        return $clinic;
    }
}