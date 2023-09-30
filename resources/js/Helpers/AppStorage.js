class AppStorage{

 storeToken(token){
 	localStorage.setItem('token',token);
 }

 storeUserId(userId){
	localStorage.setItem('userId',userId);
}
 storeUserName(userName){
	localStorage.setItem('userName',userName);
}
 storeUserEmail(userEmail){
	localStorage.setItem('userEmail',userEmail);
}
 storeUserRoles(userRoles){
	localStorage.setItem('userRoles',userRoles);
}

 store(token){
 	this.storeToken(token)
  }

  clear(){
  	localStorage.removeItem('token')
  	localStorage.removeItem('userId')
  	localStorage.removeItem('userName')
  	localStorage.removeItem('userEmail')
  	localStorage.removeItem('userRoles')
  }

  getToken(){
  	localStorage.getItem(token);
  }

//    getUser(){
//   	localStorage.getItem(user);
//   }



}

export default AppStorage = new AppStorage();