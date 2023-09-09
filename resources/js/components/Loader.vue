<template>
    <div class="loader-overlay">
      <div class="loader"></div> 
      <!-- <span
                        class="spinner-border spinner-border-lg"
                        role="status"
                        aria-hidden="true"
                      ></span> -->
                      
    </div>
  </template>
  
  <style>
    .loader-overlay {
       /* 
      position: fixed;
      top: 0;
      left: 0;
      */
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.2);
      display: flex;
      justify-content: center;
      align-items: center;
    }
  
    .loader {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #974EC3;
      border-radius: 50%;
      width: 70px;
      height: 70px;
      animation: spin 2s linear infinite;
    }
  
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
  