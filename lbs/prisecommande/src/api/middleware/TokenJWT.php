<?php
use Firebase\JWT\JWT;
namespace lbs\prisecommande\api\middleware;
class TokenJWT{
	public static function new($id){
		$token = \Firebase\JWT\JWT::encode([
			'iss' => 'http://api.prisecommande.local',
			'aud' => 'http://api.prisecommande.local',
			'iat' => time(), 
			'exp' => time()+3600,
			'id' => $id
		],
		getenv("secret"));
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
}