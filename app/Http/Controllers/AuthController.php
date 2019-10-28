<?php

namespace App\Http\Controllers;


use App\ModelAuth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
// use App\Helpers\HttpRequestHelper as HttpRequest;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('login');
        if(!Session::get('login')){
            return redirect('login')->with('alert','Kamu harus login dulu');
        }
        else{
            
            $client = new Client();
            $response = $client->request('GET', 'http://127.0.0.1:8000/api/siswa',[
                'headers' => [
                    'Authorization' => 'Bearer '.Session::get('api_token'),
                    'Accept'     => 'application/json',
                ]
            ]);
    
            if ($response->getStatusCode() == 200) { // 200 OK
                $response_data = $response->getBody()->getContents();
                
                $data = json_decode($response_data,true);
            }
            return view('dash',['data'=>$data]);
        }
        
    }

    public function login(){
        return view('login');
    }

    public function logout(){
        Session::flush();
        return redirect('login')->with('alert','Kamu sudah logout');
    }
    
    public function cek_auth(Request $request)
    {
        $email = $request->email;
        $pass = $request->password;

        $email = $request->email;
        $password = $request->password;

        // $data = ModelAuth::where('email',$email)->first();
        // if($data){ //apakah email tersebut ada atau tidak
        //     if(Hash::check($password,$data->password)){
        //         Session::put('name',$data->name);
        //         Session::put('email',$data->email);
        //         Session::put('login',TRUE);
        //         Session::put('api_token',$data->api_token);
        //         return redirect('/');
        //     }
        //     else{
        //         return redirect('login')->with('alert','Password atau Email, Salah !');
        //     }
        // }
        // else{
        //     return redirect('login')->with('alert','Password atau Email, Salah!');
        // }
        $client = new Client();

        $response = $client->request('POST', 'http://127.0.0.1:8000/api/login',[
            'form_params' => [
                'email' => $email,
                'password' => $pass
            ]
        ]);

        // $response = $client->request('GET', 'http://127.0.0.1:8000/api/siswa');

        if ($response->getStatusCode() == 200) { // 200 OK
            $response_data = $response->getBody()->getContents();
            
            $data = json_decode($response_data,true);
            if($data["success"]["isvalid"]){
                Session::put('name',$data["success"]["data"]["name"]);
                Session::put('email',$data["success"]["data"]["email"]);
                Session::put('api_token',$data["success"]["data"]["api_token"]);
                Session::put('login',TRUE);
                return redirect('/');
                // echo $data["success"]["data"]["api_token"];
            }else{
                return redirect('login')->with('alert','Password atau Email, Salah !');
            }
        }else{
            return redirect('login')->with('alert','Password atau Email, Salah !');
        }

    }

    public function register(Request $request){
        return view('register');
    }

    public function registerPost(Request $request){
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|min:4|email|unique:users',
            'password' => 'required',
            'confirmation' => 'required|same:password',
        ]);

        $data =  new ModelUser();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->save();
        return redirect('login')->with('status','Kamu berhasil Register');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
        ]);

        $client = new Client();
        $response = $client->request('POST', 'http://127.0.0.1:8000/api/siswa/',[
            'headers' => [
                'Authorization' => 'Bearer '.Session::get('api_token'),
                'Accept'     => 'application/json',
            ],
            'form_params' => [
                'nama' => $request->nama,
                'alamat' => $request->alamat
            ]
        ]);
        
        if ($response->getStatusCode() == 200) { // 200 OK
            $response_data = $response->getBody()->getContents();
            
            $data = json_decode($response_data,true);
            
            return redirect('/')->with('status','Data berhasil disimpan');  
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = new Client();
        $response = $client->request('GET', 'http://127.0.0.1:8000/api/siswa/'.$id,[
            'headers' => [
                'Authorization' => 'Bearer '.Session::get('api_token'),
                'Accept'     => 'application/json',
                ]
        ]);
        
        if ($response->getStatusCode() == 200) { // 200 OK
            $response_data = $response->getBody()->getContents();
            
            $data = json_decode($response_data,true);
            
            return view('edit',['data'=>$data]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
        ]);

        $client = new Client();
        $response = $client->request('PUT', 'http://127.0.0.1:8000/api/siswa/'.$id,[
            'headers' => [
                'Authorization' => 'Bearer '.Session::get('api_token'),
                'Accept'     => 'application/json',
            ],
            'form_params' => [
                'nama' => $request->nama,
                'alamat' => $request->alamat
            ]
        ]);
        
        if ($response->getStatusCode() == 200) { // 200 OK
            $response_data = $response->getBody()->getContents();
            
            $data = json_decode($response_data,true);
            
            return redirect('/')->with('status','Data berhasil diubah');  
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = new Client();
        $response = $client->request('DELETE', 'http://127.0.0.1:8000/api/siswa/'.$id,[
            'headers' => [
                'Authorization' => 'Bearer '.Session::get('api_token'),
                'Accept'     => 'application/json',
            ]
        ]);
        
        if ($response->getStatusCode() == 200) { // 200 OK
            $response_data = $response->getBody()->getContents();
            
            $data = json_decode($response_data,true);
            
            return redirect('/')->with('status','Data berhasil dihapus');
        }
    }
}
