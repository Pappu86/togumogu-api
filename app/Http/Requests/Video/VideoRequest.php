<?php

namespace App\Http\Requests\Video;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $translation = DB::table('video_translations')->where('video_id', '=', $this->id)->where('locale', '=', app()->getLocale())->first();

        return [
            'user_id' => 'filled',
            'title' => 'required|min:5',
            'slug' => 'required|alpha_dash|unique:video_translations,slug,' . $translation?->id,
            'datetime' => 'required',
            'excerpt' => 'required|min:10',
            'content' => 'required|min:10',
        ];
    }
}
