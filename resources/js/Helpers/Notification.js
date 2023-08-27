
// import Noty from 'noty';
class Notification{

  success(msg){
    Toast.fire({
      icon: "success",
      title: msg
    });
  	// new Noty({
    // type: 'success',
    // layout: 'topRight',
    // text: 'Successfully Done!',
    // timeout: 1000,
    //    }).show();
  } 


  alert(){
  	new Noty({
    type: 'alert',
    layout: 'topRight',
    text: 'Are you Sure?',
    timeout: 1000,
       }).show();
  } 



  error(msg){
    Toast.fire({
        icon: "warning",
        title: msg
      });
  } 


 warning(){
  	new Noty({
    type: 'warning',
    layout: 'topRight',
    text: 'Opps Wrong ',
    timeout: 1000,
       }).show();
  } 



  image_validation(){
    new Noty({
    type: 'error',
    layout: 'topRight',
    text: 'Upload Image less then 1MB ',
    timeout: 1000,
       }).show();
  } 



    cart_success(){
    new Noty({
    type: 'success',
    layout: 'topRight',
    text: 'Successfully Add to Cart!',
    timeout: 1000,
       }).show();
  } 


   cart_delete(){
    new Noty({
    type: 'success',
    layout: 'topRight',
    text: 'Successfully Deleted!',
    timeout: 1000,
       }).show();
  } 



}

export default Notification = new Notification()