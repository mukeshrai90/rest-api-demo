<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Image;
   
class ProfileController extends BaseController
{
     /**
     * Get Profile api
     *
     * @return \Illuminate\Http\Response
     */
    public function get_profile(Request $request)
    {
        $user = Auth::user(); 
		
		$success['name'] =  $user->name;
		$success['user_name'] =  $user->user_name;
		$success['email'] =  $user->email;
		$success['avatar'] =  $user->avatar;
		$success['registered_at'] =  $user->registered_at;
   
        return $this->sendResponse($success, 'User details.');
    }
	
	/**
     * Update Profile api
     *
     * @return \Illuminate\Http\Response
     */
    public function update_profile(Request $request)
    {
        $user = Auth::user(); 
		
		$validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'avatar' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		$input = $request->all();
		 
		$image = $request->file('avatar');
        $input['avatar'] = time().'.'.$image->extension();
     
        $destinationPath = public_path('/thumbnail');
        $img = Image::make($image->path());
        $img->resize(256, 256, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['avatar']);
   
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $input['avatar']);
		
		User::where('id', $user->id)->update($input);

        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
}