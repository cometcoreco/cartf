<?php

namespace Modules\MobileApp\Http\Services\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserServices
{
    public static function validateLoginRequest(?array $request){
        return Validator::make($request,[
            'username' => 'required|max:191',
            'password' => 'required',
        ]);
    }

    public static function validateRegisterRequest(?array $request){
        return Validator::make($request,[
            'full_name' => 'required|max:191',
            'email' => 'required|email|unique:users|max:191',
            'username' => 'required|unique:users|max:191',
            'country_code' => 'nullable|max:10',
            'mobile' => 'nullable|unique:users|max:191',
            'password' => 'required|min:6|max:191',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'nullable',
            'postal_code' => 'nullable',
            'terms_conditions' => 'required',
        ]);
    }

    public static function createNewUser(array $validatedData){
        return User::create([
            'name' => $validatedData['full_name'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'mobile' => ($validatedData['country_code'] ?? '') . $validatedData['mobile'],
            'password' => Hash::make($validatedData['password']),
            'country' => $validatedData['country_id'],
            'state' => $validatedData['state_id'],
            'city' => $validatedData['city_id'],
            'postal_code' => $validatedData['postal_code'],
        ]);
    }

    public static function validationErrorsResponse($validate): JsonResponse
    {
        return response()->json([
            'validation_errors' => $validate->messages()
        ])->setStatusCode(422);
    }

    public static function loginUserType(string $username): string
    {
        return filter_var($username,FILTER_VALIDATE_EMAIL) ? "email" : "username";
    }

    public static function isValideEmail($email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    public static function emailValidationResponse(): JsonResponse
    {
        return response()->json([
            'message' => __('invalid Email'),
        ])->setStatusCode(422);
    }
}
