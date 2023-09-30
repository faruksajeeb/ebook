import Token from './Token'
import AppStorage from './AppStorage'


class User{

 responseAfterLogin(res){
 	const access_token = res.data.access_token
 	const userId = res.data.user_id
 	const userName = res.data.user_name
 	const userEmail = res.data.user_email
 	const userRoles = res.data.user_roles
 	if (Token.isValid(access_token)) {
 		AppStorage.store(access_token)
 		AppStorage.storeUserId(userId)
 		AppStorage.storeUserName(userName)
 		AppStorage.storeUserEmail(userEmail)
 		AppStorage.storeUserRoles(userRoles)
 	}
 }
 

  hasToken(){
  	const storeToken = localStorage.getItem('token');
  	if (storeToken) {
  		return Token.isValid(storeToken)? true : false
  	}
  	false
  }

  loggedIn(){
  	return this.hasToken()
  }

  userId(){
  	if (this.loggedIn()) {
  		return localStorage.getItem('userId');
  	}
  }
  userName(){
  	if (this.loggedIn()) {
  		return localStorage.getItem('userName');
  	}
  }
  userEmail(){
  	if (this.loggedIn()) {
  		return localStorage.getItem('userEmail');
  	}
  }
  userRoles(){
  	if (this.loggedIn()) {
  		return localStorage.getItem('userRoles');
  	}
  }


id(){
	if (this.loggedIn()) {
		const payload = Token.payload(localStorage.getItem('token'));
		return payload.sub
	}
	return false
	}

 
 
}

export default User = new User()