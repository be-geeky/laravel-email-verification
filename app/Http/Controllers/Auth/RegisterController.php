<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;

class RegisterController extends Controller {
	/*
		    |--------------------------------------------------------------------------
		    | Register Controller
		    |--------------------------------------------------------------------------
		    |
		    | This controller handles the registration of new users as well as their
		    | validation and creation. By default this controller uses a trait to
		    | provide this functionality without requiring any additional code.
		    |
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('guest');
	}

	/**
	 * Show the application registration form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showRegistrationForm() {
		return view('custom.auth.register');
	}
	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data) {
		return Validator::make($data, [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			//'password' => 'required|string|min:6|confirmed',
		]);
	}

	/**
	 * Get a validator for an incoming registration password.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validatorPassword(array $data) {
		return Validator::make($data, [
			'password' => 'required|string|min:6|confirmed',
		]);
	}
	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(array $data) {
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			//'password' => bcrypt($data['password']),
			'password' => '',
			'email_token' => bin2hex(openssl_random_pseudo_bytes(30)),
		]);
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */

	public function register(Request $request) {

		$this->validator($request->all())->validate();
		event(new Registered($user = $this->create($request->all())));
		$email = new EmailVerification($user);
		Mail::to($user->email)->send($email);
		return view('auth.emails.verification');

	}

	/**
	 * Set Password after email verification.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */

	public function setPassword(Request $request) {

		$this->validatorPassword($request->all())->validate();
		$user = User::where('email_token', $request->email_token)->first();
		if (!$user) {
			return redirect('login')->with('flash-error', 'Invalid User!');
		}

		$user->password = bcrypt($request->password);
		if ($user->save()) {
			return view('custom.auth.passwordset', ['user' => $user]);
		}
	}

	/**
	 * Handle a email verification request for the application.
	 *
	 * @param $token
	 * @return \Illuminate\Http\Response
	 */

	public function verify($token) {
		if (!$token) {
			return redirect('login')->with('flash-error', 'Email Verification Token not provided!');
		}

		$user = User::where('email_token', $token)->first();
		if (!$user) {
			return redirect('login')->with('flash-error', 'Invalid Email Verification Token!');
		}

		$user->verified = 1;
		if ($user->save()) {
			return view('custom.auth.setpassword', ['user' => $user]);
		}
	}
}

