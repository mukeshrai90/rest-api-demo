<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|in:1,2',
            'email' => 'required|email|unique:users|min:10|max:50',
            'user_name' => 'required|unique:users|min:4|max:20',
            'password' => 'required|min:3|max:8',
            'c_password' => 'required|same:password|min:3|max:8',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 200);       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['registered_at'] = date('Y-m-d H:i:s');
        $input['verification_code'] = mt_rand(100000, 999999);
         
		$user = User::create($input);
        
		$success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['user_name'] =  $user->user_name;
        $success['verification_code'] =  $input['registered_at'];
		
		//Email Code here
		//
   
        return $this->sendResponse($success, 'User register successfully.');
    }
	
	/**
     * Verify api
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|min:4|max:20',
			'verification_code' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		
		if(Auth::attempt(['user_name' => $request->user_name, 'password' => $request->password])){ 
            $user = Auth::user(); 
			
			if($user->verification_code == $request->verification_code){
				$data = array();
				$data['verified'] = 1;
				User::where('id', $user->id)->update($data);
							
				$success['token'] =  $user->createToken('MyApp')-> accessToken; 
				$success['name'] =  $user->name;
				
				return $this->sendResponse($success, 'User login successfully.');
				
			} else {
				 return $this->sendError('Validation Error.',  ['error'=>'Please enter a valid code'], 200);  
			}
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised'], 403);
        } 
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['user_name' => $request->user_name, 'password' => $request->password])){ 
            $user = Auth::user(); 
			
			if(!isset($user->verified) || $user->verified != 1){
				return $this->sendError('NotActive.', ['error'=>'Please activate account first']);
			}
			
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['user_name'] =  $user->user_name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}