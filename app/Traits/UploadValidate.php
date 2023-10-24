<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Http\Request;

trait UploadValidate
{
    /**
     * filtering Limit and Offset
     * @return Boolean
     */
    public function validateUpload(Request $request)
    {
        // dd($request->post('key') == 'image');
        switch ($request->post('key')) {
            case 'image':
                $this->_image($request);
                return 'image';

            case 'images':
                $this->images($request);
                return 'images';

            case 'avatar':
                $this->_avatar($request);
                return 'avatar';

            case 'document':
                $this->_document($request);
                return 'document';

            case 'video':
                $this->_video($request);
                return 'video';

            case 'music':
                $this->_music($request);
                return 'music';

            default:
                # code...
                break;
        }
    }

    private function _avatar(Request $request)
    {
        $rules = [
            'avatar' => "bail|required|mimes:jpeg,png,webp|max:1024"
        ];

        $messages = [
            'avatar.required' => 'Photo tidak boleh kosong',
            'avatar.file' => 'Pastikan Photo anda sudah benar.',
            'avatar.mimes' => 'Ekstensi file yang didukung hanya, jpeg, png, webp',
            'avatar.max' => 'Photo tidak boleh lebih dari 1024 Kb'
        ];

        $this->validate($request, $rules, $messages);
    }

    private function _document(Request $request)
    {
        $rules = [
            'document' => "bail|required|mimes:jpeg,png,webp|max:512"
        ];

        $messages = [
            'document.required' => 'Photo tidak boleh kosong',
            'document.file' => 'Pastikan Photo anda sudah benar.',
            'document.mimes' => 'Ekstensi file yang didukung hanya, jpeg, png, webp',
            'document.max' => 'Photo tidak boleh lebih dari 512 Kb'
        ];

        $this->validate($request, $rules, $messages);
    }

    private function _image(Request $request)
    {
        $rules = [
            'value' => "bail|required|mimes:jpeg,jpg,png,webp|max:1024"
        ];

        $messages = [
            'value.required' => 'Photo tidak boleh kosong',
            'value.file' => 'Pastikan Photo anda sudah benar.',
            'value.mimes' => 'Ekstensi file yang didukung hanya, jpeg, png, webp',
            'value.max' => 'Photo tidak boleh lebih dari 1024 Kb'
        ];

        $this->validate($request, $rules, $messages);
    }

    private function _images(Request $request)
    {
        $rules = [
            'images.*' => "bail|required|mimes:jpeg,jpg,png,webp|max:1024"
        ];

        $messages = [
            'images.required' => 'Photo tidak boleh kosong',
            'images.file' => 'Pastikan Photo anda sudah benar.',
            'images.mimes' => 'Ekstensi file yang didukung hanya, jpeg, png, webp',
            'images.max' => 'Photo tidak boleh lebih dari 1024 Kb'
        ];

        $this->validate($request, $rules, $messages);
    }

    private function _video(Request $request)
    {
        $rules = [
            'value' => "bail|required|mimes:mp4,mov"
        ];

        $messages = [
            'value.required' => 'Video tidak boleh kosong',
            'value.file' => 'Pastikan Video anda sudah benar.',
            'value.mimes' => 'Ekstensi file yang didukung hanya, mp4 & mov',
            'value.max' => 'Video tidak boleh lebih dari 5MB'
        ];

        $this->validate($request, $rules, $messages);
    }

    private function _music(Request $request)
    {
        $rules = [
            'value' => "bail|required|mimes:mp3,wav"
        ];

        $messages = [
            'value.required' => 'Audio music tidak boleh kosong',
            'value.file' => 'Pastikan Audio music anda sudah benar.',
            'value.mimes' => 'Ekstensi file yang didukung hanya, mp3 & wav',
            'value.max' => 'Audio music tidak boleh lebih dari 5MB'
        ];

        $this->validate($request, $rules, $messages);
    }
}
