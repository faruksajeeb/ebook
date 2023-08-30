<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }



    static function roleHasPermissions($role,$permissions){
        $result = true;
      
        foreach($permissions as $permission){
           
            if(!$role->hasPermissionTo($permission->name)){
                $result = false;
            }
        }
        return $result; 
    }


    public static function insertUser($request){
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password =Hash::make($request->password);
        $user->save();
        if($request->roles){
            $user->assignRole($request->roles);
        }
        return $user->id; // return insert id
    }
    

    public static function menuByGroupName($groupName){
        return DB::table('permissions')->where('group_name',$groupName)->get();
    }


    public function scopeSearch($query,$term){
        $term = "%$term%";         
        $query->where(function($q) use($term){
            // $q->where('id','LIKE',$term);
            $q->where('name','LIKE',$term);
            $q->orWhere('email','LIKE',$term);
            // $q->orWhere('price','LIKE',$term);
            // $q->orWhereHas('categories',function($q) use($term){
            //     $q->where('category_name','LIKE',$term);
            // });
            // $q->orWhereHas('sub_categories',function($q) use($term){
            //     $q->where('sub_category_name','LIKE',$term);
            // });
        });
    }
}
