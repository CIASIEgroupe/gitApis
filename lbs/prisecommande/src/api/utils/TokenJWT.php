<?php
use Firebase\JWT\JWT;
namespace prisecommande\api\utils;
class TokenJWT{
	
	public static function new($data){
		$token = \Firebase\JWT\JWT::encode([
			'iss' => 'http://backend-lmaillard.pagekite.me/',
			'aud' => 'http://backend-lmaillard.pagekite.me/',
			'iat' => time(),
			'exp' => time()+3600, 
			'data' => $data 
		], getenv("secret")); 
		return $token; 
	} 

	public static function decode($jwt){
		try{ 
			return \Firebase\JWT\JWT::decode($jwt, getenv("secret"), array('HS256')); 
		} 
		catch(\Exception $e){ 
			return false; 
		}
	}

	public static function check($request){
		try{ 
			$authorization = $request->getHeader("Authorization");
			$tokenJWT = explode(" ", $authorization[0])[1];
			return TokenJWT::decode($tokenJWT);
		} 
		catch(\Exception $e){ 
			return false; 
		}
	}