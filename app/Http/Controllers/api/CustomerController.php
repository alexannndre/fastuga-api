<?php

namespace App\Http\Controllers\api;

use App\Helpers\OrderHelper;
use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CustomerPostRequest;
use App\Http\Requests\User\CustomerPutRequest;
use App\Http\Resources\MyUserResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allCostumers()
    {
        //To also return user information
        return UserResource::collection(User::where('type', 'C')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerPostRequest $request)
    {
        $newCustomer = $request->validated();

        if ($request->has('default_payment_reference')) {
            $resValPay = OrderHelper::validatePaymentInfo($newCustomer["default_payment_type"], $newCustomer["default_payment_reference"]);
            if ($resValPay['status'] !== true)
                return response(['message' => $resValPay['message']], 422);
        }

        $newUser = [
            "name" => $newCustomer["name"],
            "email" => $newCustomer["email"],
            "password" => $newCustomer["password"],
            "type" => 'C'
        ];

        //Unset all user fields from newCustomer
        foreach ($newUser as $key => $value)
            unset($newCustomer[$key]);

        $regUser = UserHelper::registerUser($request, $newUser);

        $newCustomer["user_id"] = $regUser->id;
        $newCustomer["points"] = 0;

        $regCustomer = Customer::create($newCustomer);

        $accessToken = UserHelper::createAccessToken($regUser);

        return response(["message" => "Customer user created", "user" => new MyUserResource($regCustomer->user), "access_token" => $accessToken]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function updateCustomer(CustomerPutRequest $request)
    {
        if ($request->user()->type != 'C')
            return response(['message' => 'You are not a customer! To update your account please use the ' . route('update-employee-profile') . ' route'], 403);

        $newCustomer = $request->validated();

        if ($request->has('default_payment_reference')) {
            $resValPay = OrderHelper::validatePaymentInfo($newCustomer["default_payment_type"], $newCustomer["default_payment_reference"]);
            if ($resValPay['status'] !== true)
                return response(['message' => $resValPay['message']], 422);
        }

        $newUser = [
            "name" => $newCustomer["name"]
        ];

        if ($request->has('remove_photo'))
            $newUser["remove_photo"] = $newCustomer["remove_photo"];

        //Unset all user fields from newCustomer
        foreach ($newUser as $key => $value)
            unset($newCustomer[$key]);

        UserHelper::updateUser($request, $newUser, $request->user());

        $request->user()->customer->update($newCustomer);

        return response(['message' => 'User updated', 'user' => new MyUserResource($request->user())]);
    }
}
